<x-guest-layout>
    <div class="space-y-4 text-xs">
        <div class="text-center">
            <h2 class="text-xl font-medium text-ink">Lupa Password</h2>
            <p class="text-xs text-mute mt-1 leading-relaxed">
                {{ __('Masukkan alamat email yang terdaftar. Kami akan mengirimkan tautan untuk mereset password akun Anda.') }}
            </p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

            <!-- Email Address -->
            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus placeholder="nama@email.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="pt-2 space-y-3">
                <x-primary-button class="w-full py-3.5">
                    {{ __('Kirim Link Reset Password') }}
                </x-primary-button>

                <p class="text-center text-xs text-mute">
                    <a href="{{ route('login') }}" class="text-ink font-medium underline hover:text-mute">&larr; Kembali ke halaman Masuk</a>
                </p>
            </div>
        </form>
    </div>
</x-guest-layout>
