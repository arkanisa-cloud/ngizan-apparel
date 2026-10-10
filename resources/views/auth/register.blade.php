<x-guest-layout>
    <div class="space-y-6">
        <div class="text-center">
            <h2 class="text-xl font-medium text-ink">Buat Akun Baru</h2>
            <p class="text-xs text-mute mt-1">Daftar instan untuk menikmati diskon dan kustomisasi jersey.</p>
        </div>

        {{-- Google OAuth 1-Click Fast Register --}}
        <a href="{{ route('auth.google') }}" class="btn-secondary w-full py-3 rounded-full text-xs font-medium flex items-center justify-center gap-2">
            <svg class="w-4 h-4" viewBox="0 0 24 24">
                <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
            </svg>
            <span>Daftar Cepat dengan Google</span>
        </a>

        <div class="relative flex items-center justify-center">
            <div class="border-t border-hairline-soft w-full"></div>
            <span class="bg-white px-3 text-[11px] text-mute uppercase tracking-wider relative">atau isi formulir</span>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4 text-xs">
            @csrf

            {{-- Honeypot Anti-Bot Field (Invisible to human eyes, filled by bots) --}}
            <div style="display:none !important; visibility:hidden !important; position:absolute !important; left:-9999px !important;" aria-hidden="true">
                <input type="text" name="extra_security_token" id="extra_security_token" tabindex="-1" autocomplete="off" value="">
            </div>

            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Nama Lengkap')" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Nama Anda" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email (Gmail, Yahoo, Outlook, dsb)')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="nama@gmail.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Phone Number -->
            <div>
                <x-input-label for="phone" :value="__('Nomor HP (WhatsApp)')" />
                <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')" required autocomplete="tel" placeholder="Contoh: 081234567890" />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" :value="__('Password')" />
                <x-text-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="new-password"
                                placeholder="Minimal 8 karakter" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div>
                <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password"
                                placeholder="Ulangi password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="pt-2 space-y-3">
                <x-primary-button class="w-full py-3.5">
                    {{ __('Daftar Sekarang') }}
                </x-primary-button>

                <p class="text-center text-xs text-mute">
                    Sudah memiliki akun? 
                    <a href="{{ route('login') }}" class="text-ink font-medium underline hover:text-mute">Masuk ke akun</a>
                </p>
            </div>
        </form>
    </div>
</x-guest-layout>
