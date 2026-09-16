<x-guest-layout>
    <div class="space-y-6">
        <div class="text-center">
            <h2 class="text-xl font-medium text-ink">Buat Akun Baru</h2>
            <p class="text-xs text-mute mt-1">Daftar untuk menikmati diskon dan kustomisasi jersey.</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4 text-xs">
            @csrf

            <!-- Name -->
            <div>
                <x-input-label for="name" :value="__('Nama Lengkap')" />
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Nama Anda" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="nama@email.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
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
