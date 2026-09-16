<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

/**
 * Controller GoogleAuthController
 * Mengelola alur login dan registrasi instan 1-klik pelanggan menggunakan Google OAuth 2.0
 */
class GoogleAuthController extends Controller
{
    /**
     * Redirect pengguna ke halaman otorisasi Google
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Tangani callback data pengguna setelah otorisasi dari Google
     */
    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // 1. Cari user berdasarkan google_id atau email
            $user = User::where('google_id', $googleUser->getId())
                ->orWhere('email', $googleUser->getEmail())
                ->first();

            if ($user) {
                // Perbarui data google_id dan avatar jika belum terpasang
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar'    => $user->avatar ?: $googleUser->getAvatar(),
                ]);
            } else {
                // 2. Buat akun customer baru secara otomatis
                $user = User::create([
                    'name'              => $googleUser->getName() ?? $googleUser->getNickname() ?? 'Pelanggan Ngizan',
                    'email'             => $googleUser->getEmail(),
                    'google_id'         => $googleUser->getId(),
                    'avatar'            => $googleUser->getAvatar(),
                    'role'              => 'customer',
                    'email_verified_at' => now(),
                    'password'          => null, // Login tanpa password via OAuth
                ]);
            }

            // 3. Pastikan customer memiliki keranjang belanja aktif
            Cart::firstOrCreate(['user_id' => $user->id]);

            // 4. Autentikasi user
            Auth::login($user, true);

            return redirect()->intended(route('home'))
                ->with('success', "Selamat datang kembali, {$user->name}!");
        } catch (Exception $e) {
            Log::error('Google OAuth Login Error: ' . $e->getMessage());

            return redirect()->route('login')
                ->with('error', 'Gagal melakukan login dengan Google. Silakan coba lagi.');
        }
    }
}
