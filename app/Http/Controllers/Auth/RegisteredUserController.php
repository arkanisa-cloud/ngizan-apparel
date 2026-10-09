<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Honeypot Anti-Bot Trap: Jika input tersembunyi terisi, batalkan diam-diam
        if ($request->filled('extra_security_token')) {
            return redirect()->route('login')->with('success', 'Pendaftaran akun Anda sedang diproses.');
        }

        // 2. IP Rate Limiting: Maksimal 5 pendaftaran per 10 menit per IP
        $ipKey = 'register-rate:' . $request->ip();
        if (RateLimiter::tooManyAttempts($ipKey, 5)) {
            $seconds = RateLimiter::availableIn($ipKey);
            throw ValidationException::withMessages([
                'email' => 'Terlalu banyak permintaan pendaftaran dari jaringan Anda. Silakan coba lagi dalam ' . ceil($seconds / 60) . ' menit atau gunakan tombol "Daftar Cepat dengan Google".',
            ]);
        }

        // 3. Whitelist Provider Email Resmi Terpercaya (Opsi B: Gmail, Yahoo, Outlook, iCloud)
        $emailInput = strtolower(trim((string) $request->email));
        $emailParts = explode('@', $emailInput);
        $domain = end($emailParts);

        $trustedDomains = [
            'gmail.com',
            'googlemail.com',
            'yahoo.com',
            'yahoo.co.id',
            'ymail.com',
            'outlook.com',
            'hotmail.com',
            'live.com',
            'icloud.com',
        ];

        if (! in_array($domain, $trustedDomains, true)) {
            throw ValidationException::withMessages([
                'email' => "Pendaftaran manual hanya menerima email dari penyedia resmi terpercaya (@gmail.com, @yahoo.com, @outlook.com, @icloud.com). Domain '{$domain}' tidak diizinkan. Silakan gunakan Gmail atau tombol 'Daftar Cepat dengan Google'.",
            ]);
        }

        // 4. Validasi Struktur Username Khusus Akun Gmail Asli
        if (in_array($domain, ['gmail.com', 'googlemail.com'], true)) {
            $username = $emailParts[0] ?? '';
            // Aturan resmi Google: 6-30 karakter, alfanumerik, tidak boleh dot di awal/akhir atau berurutan
            if (strlen($username) < 6 || strlen($username) > 30 || !preg_match('/^[a-z0-9]+(\.[a-z0-9]+)*$/i', $username)) {
                throw ValidationException::withMessages([
                    'email' => 'Format alamat Gmail tidak valid menurut standar Google (username 6-30 karakter alfanumerik).',
                ]);
            }
        }

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email:rfc,dns', 'max:255', 'unique:'.User::class],
            'phone'    => ['required', 'string', 'regex:/^(\+62|62|0)8[1-9][0-9]{7,11}$/', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'name.required'      => 'Nama lengkap wajib diisi.',
            'email.required'     => 'Alamat email wajib diisi.',
            'email.email'        => 'Format email tidak valid atau domain email tidak aktif.',
            'email.unique'       => 'Email ini sudah terdaftar. Silakan masuk atau gunakan email lain.',
            'phone.required'     => 'Nomor HP wajib diisi.',
            'phone.regex'        => 'Nomor HP harus berupa nomor seluler Indonesia yang valid (contoh: 081234567890 atau 6281234567890).',
            'phone.unique'       => 'Nomor HP ini sudah terdaftar.',
            'password.required'  => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        RateLimiter::hit($ipKey, 600);

        $cleanPhone = preg_replace('/[^0-9]/', '', (string) $request->phone);
        if (str_starts_with($cleanPhone, '62')) {
            $cleanPhone = '0' . substr($cleanPhone, 2);
        }

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $cleanPhone,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
