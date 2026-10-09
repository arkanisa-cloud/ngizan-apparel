@extends('layouts.customer')

@section('title', 'Profil Saya · NGIZAN APPAREL')
@section('meta_description', 'Kelola informasi profil, alamat tersimpan, dan riwayat belanja Anda di Ngizan Apparel.')

@section('content')
<div class="py-8 sm:py-12 bg-canvas">
    <div class="wrap">
        {{-- Page Header --}}
        <div class="border-b border-hairline-soft pb-5 mb-8">
            <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-mute block mb-1">
                Pengaturan Akun
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-ink uppercase">
                Profil Saya
            </h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            {{-- Left Column: Identity Card, Navigation & Membership --}}
            <div class="space-y-6">
                {{-- Profile Identity Card --}}
                <div class="bg-white border border-hairline-soft rounded-3xl p-6 sm:p-7 text-center space-y-4 shadow-xs">
                    <div class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-ink text-white font-display text-2xl tracking-wider shadow-md">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-ink">{{ auth()->user()->name }}</h2>
                        <p class="text-xs text-mute mt-0.5">{{ auth()->user()->email }}</p>
                    </div>
                    <div>
                        @if (auth()->user()->isPremiumActive())
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 border border-amber-200/80 text-amber-900 rounded-full text-[11px] font-bold">
                                ⭐ Member Premium VIP
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 bg-soft-cloud border border-hairline-soft text-ink rounded-full text-[10px] font-bold uppercase tracking-wider">
                                Member Reguler
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Quick Navlinks --}}
                <div class="bg-white border border-hairline-soft rounded-3xl overflow-hidden divide-y divide-hairline-soft text-xs shadow-xs">
                    <a href="{{ route('customer.addresses.index') }}"
                        class="flex items-center justify-between px-5 py-4 text-ink hover:bg-soft-cloud transition font-medium group">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-mute group-hover:text-ink transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Alamat Pengiriman</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-soft-cloud text-[11px] text-mute font-bold tabular-nums">
                            {{ auth()->user()->shippingAddresses->count() }}
                        </span>
                    </a>

                    <a href="{{ route('customer.orders.index') }}"
                        class="flex items-center justify-between px-5 py-4 text-ink hover:bg-soft-cloud transition font-medium group">
                        <div class="flex items-center gap-3">
                            <svg class="w-4 h-4 text-mute group-hover:text-ink transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <span>Pesanan Saya</span>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-soft-cloud text-[11px] text-mute font-bold tabular-nums">
                            {{ auth()->user()->orders->count() }}
                        </span>
                    </a>
                </div>

                {{-- Ngizan Premium Membership Card --}}
                @if (auth()->user()->isPremiumActive())
                    <div class="bg-neutral-950 text-white border border-amber-500/30 rounded-3xl p-6 relative overflow-hidden shadow-xl space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/20 border border-amber-500/40 text-amber-300 text-[10px] font-bold tracking-wider uppercase">
                                ⭐ NGIZAN PREMIUM
                            </span>
                            <span class="text-[10px] bg-white/10 px-2.5 py-0.5 rounded-full text-neutral-300 font-mono">
                                5% OFF AKTIF
                            </span>
                        </div>

                        <div>
                            <h3 class="text-sm font-bold text-white uppercase tracking-wide">Keanggotaan VIP Aktif</h3>
                            <p class="text-xs text-neutral-400 mt-1 leading-relaxed">
                                Masa aktif berlaku hingga:
                                <span class="text-amber-300 font-bold block sm:inline mt-0.5 sm:mt-0">
                                    {{ auth()->user()->premium_until?->translatedFormat('d F Y') }}
                                </span>
                            </p>
                        </div>

                        <div class="pt-3 border-t border-white/10 text-[11px] text-neutral-400 flex items-center justify-between">
                            <span>Diskon 5% otomatis di setiap pesanan</span>
                            <span class="text-amber-400 font-bold uppercase tracking-widest text-[10px]">VIP CLUB</span>
                        </div>
                    </div>
                @else
                    <div class="bg-neutral-950 text-white border border-neutral-800 rounded-3xl p-6 relative overflow-hidden shadow-xl space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-amber-300 text-[10px] font-bold tracking-wider uppercase">
                                ⭐ NGIZAN PREMIUM
                            </span>
                            <span class="text-[10px] text-neutral-400 font-mono">Rp 100.000 / thn</span>
                        </div>

                        <div>
                            <h3 class="text-sm font-bold uppercase tracking-wider text-white">Privilege Khusus Member</h3>
                            <p class="text-xs text-neutral-300 leading-relaxed mt-1">
                                Nikmati diskon otomatis <strong class="text-amber-400">5% di setiap pembelian</strong> tanpa syarat minimum transaksi pada seluruh produk katalog.
                            </p>
                        </div>

                        <button type="button" @click="$dispatch('open-premium-modal')"
                            class="w-full py-3 px-4 bg-amber-500 hover:bg-amber-400 text-neutral-950 font-bold text-xs rounded-full shadow-lg transition duration-200 cursor-pointer flex items-center justify-center gap-2 uppercase tracking-wider">
                            <span>⭐</span>
                            <span>Gabung Membership</span>
                        </button>
                    </div>
                @endif
            </div>

            {{-- Right Column: Profile Edit Form & Stats --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Account Details & Update Form --}}
                <div class="bg-white border border-hairline-soft rounded-3xl p-6 sm:p-8 space-y-6 shadow-xs">
                    <div class="flex items-center justify-between border-b border-hairline-soft pb-4">
                        <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-ink">
                            Informasi Akun
                        </h2>
                        @if (session('status') === 'profile-updated')
                            <span class="text-[11px] font-bold text-success flex items-center gap-1">
                                ✓ Profil berhasil diperbarui
                            </span>
                        @endif
                    </div>

                    <form action="{{ route('profile.update') }}" method="POST" class="space-y-5 text-xs">
                        @csrf
                        @method('patch')

                        {{-- Nama Lengkap --}}
                        <div>
                            <label for="name" class="block font-bold text-ink uppercase tracking-wider text-[11px] mb-2">
                                Nama Lengkap
                            </label>
                            <input type="text" id="name" name="name"
                                value="{{ old('name', auth()->user()->name) }}" required
                                class="w-full bg-soft-cloud focus:bg-white border border-hairline-soft focus:border-ink rounded-full px-4 py-3 text-xs text-ink transition outline-none @error('name') border-sale @enderror"
                                placeholder="Masukkan nama lengkap Anda">
                            @error('name')
                                <p class="text-sale text-[11px] font-medium mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email Address --}}
                        <div>
                            <label for="email" class="block font-bold text-ink uppercase tracking-wider text-[11px] mb-2">
                                Alamat Email
                            </label>
                            <input type="email" id="email" name="email"
                                value="{{ old('email', auth()->user()->email) }}" required
                                class="w-full bg-soft-cloud focus:bg-white border border-hairline-soft focus:border-ink rounded-full px-4 py-3 text-xs text-ink transition outline-none @error('email') border-sale @enderror"
                                placeholder="nama@email.com">
                            @error('email')
                                <p class="text-sale text-[11px] font-medium mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Static Account Metadata --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                            <div class="bg-soft-cloud/70 border border-hairline-soft rounded-2xl p-4">
                                <span class="text-[10px] text-mute uppercase font-bold tracking-wider block mb-1">
                                    Role Pengguna
                                </span>
                                <span class="text-xs font-bold text-ink capitalize">
                                    {{ auth()->user()->role }}
                                </span>
                            </div>

                            <div class="bg-soft-cloud/70 border border-hairline-soft rounded-2xl p-4">
                                <span class="text-[10px] text-mute uppercase font-bold tracking-wider block mb-1">
                                    Bergabung Sejak
                                </span>
                                <span class="text-xs font-bold text-ink">
                                    {{ auth()->user()->created_at->translatedFormat('d F Y') }}
                                </span>
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="pt-2">
                            <button type="submit"
                                class="btn-primary py-3.5 px-8 rounded-full text-xs font-bold uppercase tracking-[0.15em] shadow-md hover:bg-neutral-800 transition cursor-pointer">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>

                {{-- User Stats --}}
                <div class="bg-white border border-hairline-soft rounded-3xl p-6 sm:p-8 shadow-xs">
                    <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-ink border-b border-hairline-soft pb-4 mb-6">
                        Statistik Transaksi
                    </h2>
                    <div class="grid grid-cols-3 gap-4 text-center divide-x divide-hairline-soft">
                        <div class="px-2">
                            <div class="font-display font-medium text-3xl sm:text-4xl text-ink tabular-nums">
                                {{ auth()->user()->orders->count() }}
                            </div>
                            <p class="text-[10px] sm:text-[11px] text-mute uppercase font-bold tracking-wider mt-1">
                                Total Pesanan
                            </p>
                        </div>
                        <div class="px-2">
                            <div class="font-display font-medium text-3xl sm:text-4xl text-ink tabular-nums">
                                {{ auth()->user()->orders()->where('status', 'completed')->count() }}
                            </div>
                            <p class="text-[10px] sm:text-[11px] text-mute uppercase font-bold tracking-wider mt-1">
                                Selesai
                            </p>
                        </div>
                        <div class="px-2">
                            <div class="font-display font-medium text-3xl sm:text-4xl text-ink tabular-nums">
                                {{ auth()->user()->shippingAddresses->count() }}
                            </div>
                            <p class="text-[10px] sm:text-[11px] text-mute uppercase font-bold tracking-wider mt-1">
                                Alamat Tersimpan
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
