<x-guest-layout>
    <div class="space-y-4 text-xs">
        <div class="text-center">
            <h2 class="text-xl font-medium text-ink">Verifikasi Email Anda</h2>
            <p class="text-xs text-mute mt-1 leading-relaxed">
                {{ __('Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi alamat email Anda melalui tautan yang baru saja kami kirimkan ke email Anda.') }}
            </p>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="p-3 bg-soft-cloud border border-hairline rounded-xl text-xs text-ink">
                {{ __('Tautan verifikasi baru telah dikirimkan ke alamat email yang Anda berikan saat pendaftaran.') }}
            </div>
        @endif

        <div class="pt-2 space-y-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <x-primary-button class="w-full py-3.5">
                    {{ __('Kirim Ulang Email Verifikasi') }}
                </x-primary-button>
            </form>

            <form method="POST" action="{{ route('logout') }}" class="text-center">
                @csrf
                <button type="submit" class="text-xs text-mute hover:text-ink underline">
                    {{ __('Keluar (Log Out)') }}
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
