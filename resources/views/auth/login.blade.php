<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="space-y-6">
        <div class="text-center">
            <h2 class="text-xl font-medium text-ink">Masuk ke Akun Anda</h2>
            <p class="text-xs text-mute mt-1">Akses riwayat pesanan dan keanggotaan Ngizan Premium.</p>
        </div>

        {{-- Google OAuth 1-Click Login --}}
        <a href="{{ route('auth.google') }}" class="btn-secondary w-full py-3 rounded-full text-xs font-medium flex items-center justify-center gap-2">
            <svg class="w-4 h-4" viewBox="0 0 24 24">
                <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
            </svg>
            <span>Masuk dengan Google</span>
        </a>

        <div class="relative flex items-center justify-center">
            <div class="border-t border-hairline-soft w-full"></div>
            <span class="bg-white px-3 text-[11px] text-mute uppercase tracking-wider relative">atau</span>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-4 text-xs">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@email.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="current-password"
                                placeholder="••••••••" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me & Forgot Password -->
            <div class="flex items-center justify-between text-xs pt-1">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" type="checkbox" class="rounded border-hairline text-ink shadow-none focus:ring-0" name="remember">
                    <span class="ms-2 text-mute">{{ __('Ingat saya') }}</span>
                </label>

                @if (Route::has('password.request'))
                    <a class="text-mute hover:text-ink underline" href="{{ route('password.request') }}">
                        {{ __('Lupa password?') }}
                    </a>
                @endif
            </div>

            <div class="pt-2 space-y-3">
                <x-primary-button class="w-full py-3.5">
                    {{ __('Masuk') }}
                </x-primary-button>

                <p class="text-center text-xs text-mute">
                    Belum punya akun? 
                    <a href="{{ route('register') }}" class="text-ink font-medium underline hover:text-mute">Daftar sekarang</a>
                </p>
            </div>
        </form>
    </div>
</x-guest-layout>
