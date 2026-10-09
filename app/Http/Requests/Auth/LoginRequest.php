<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

use App\Models\User;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email'    => ['required', 'string', 'email:rfc,dns'],
            'phone'    => ['required', 'string', 'regex:/^(\+62|62|0)8[1-9][0-9]{7,11}$/'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.required'    => 'Alamat email wajib diisi.',
            'email.email'       => 'Format email tidak valid atau domain email tidak aktif.',
            'phone.required'    => 'Nomor HP wajib diisi.',
            'phone.regex'       => 'Nomor HP harus berupa nomor seluler Indonesia yang valid (contoh: 081234567890 atau 6281234567890).',
            'password.required' => 'Password wajib diisi.',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $user = User::where('email', $this->input('email'))->first();

        // 1. Cek apakah email terdaftar di database
        if (! $user) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => 'Email belum terdaftar di Ngizan Apparel. Silakan daftar akun terlebih dahulu.',
            ]);
        }

        // 2. Cek apakah user mendaftar melalui Google OAuth (tanpa password manual)
        if (is_null($user->password) && $user->google_id) {
            throw ValidationException::withMessages([
                'email' => 'Akun ini terdaftar melalui Google. Silakan masuk menggunakan tombol "Masuk dengan Google".',
            ]);
        }

        // 3. Cek kesesuaian Nomor HP (Normalisasi awalan 62 / +62 / 0)
        $inputPhone = preg_replace('/[^0-9]/', '', (string) $this->input('phone'));
        if (str_starts_with($inputPhone, '62')) {
            $inputPhone = '0' . substr($inputPhone, 2);
        }

        if (! empty($user->phone)) {
            $userPhone = preg_replace('/[^0-9]/', '', (string) $user->phone);
            if (str_starts_with($userPhone, '62')) {
                $userPhone = '0' . substr($userPhone, 2);
            }

            if ($inputPhone !== $userPhone) {
                RateLimiter::hit($this->throttleKey());

                throw ValidationException::withMessages([
                    'phone' => 'Nomor HP tidak cocok dengan data akun yang terdaftar.',
                ]);
            }
        } else {
            // Jika akun lama belum memiliki no HP, simpan nomor yang diinput
            $user->update(['phone' => $inputPhone]);
        }

        // 4. Cek apakah password cocok
        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'password' => 'Password yang Anda masukkan salah.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
