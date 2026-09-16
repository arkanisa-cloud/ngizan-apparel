<x-guest-layout>
    <div class="space-y-4 text-xs">
        <div class="text-center">
            <h2 class="text-xl font-medium text-ink">Konfirmasi Password</h2>
            <p class="text-xs text-mute mt-1 leading-relaxed">
                {{ __('Halaman ini memerlukan konfirmasi keamanan. Harap masukkan password Anda sebelum melanjutkan.') }}
            </p>
        </div>

        <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
            @csrf

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

            <div class="pt-2">
                <x-primary-button class="w-full py-3.5">
                    {{ __('Konfirmasi') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>
