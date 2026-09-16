@extends('layouts.customer')

@section('title', 'Profil Saya · NGIZAN APPAREL')
@section('meta_description', 'Kelola informasi profil, alamat tersimpan, dan riwayat belanja Anda di Ngizan Apparel.')

@section('content')
<div class="py-8 bg-canvas">
    <div class="wrap">
        <div class="border-b border-hairline-soft pb-5 mb-8">
            <span class="text-xs font-medium uppercase tracking-widest text-mute block mb-1">Pengaturan Akun</span>
            <h1 class="text-2xl sm:text-3xl font-medium tracking-tight text-ink">
                Profil Saya
            </h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            <!-- Left Column: Profile Card & Quick Links -->
            <div class="space-y-6">
                <!-- Profile Card -->
                <div class="bg-soft-cloud border border-hairline-soft rounded-2xl p-6 text-center space-y-4">
                    <div class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-white text-ink border border-hairline text-2xl font-medium">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <div>
                        <h3 class="text-base font-medium text-ink">{{ auth()->user()->name }}</h3>
                        <p class="text-xs text-mute mt-0.5">{{ auth()->user()->email }}</p>
                    </div>
                    <span class="inline-block px-3 py-1 bg-white border border-hairline rounded-full text-xs font-medium text-ink capitalize">
                        {{ auth()->user()->role }}
                    </span>
                </div>

                <!-- Quick Links -->
                <div class="bg-white border border-hairline-soft rounded-2xl overflow-hidden divide-y divide-hairline-soft text-xs">
                    <a href="{{ route('customer.addresses.index') }}" class="flex items-center gap-3 px-5 py-3.5 text-ink hover:bg-soft-cloud transition font-medium">
                        <svg class="w-4 h-4 text-mute" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span>Alamat Pengiriman</span>
                    </a>
                    <a href="{{ route('customer.orders.index') }}" class="flex items-center gap-3 px-5 py-3.5 text-ink hover:bg-soft-cloud transition font-medium">
                        <svg class="w-4 h-4 text-mute" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        <span>Pesanan Saya</span>
                    </a>
                </div>
            </div>

            <!-- Right Column: Account Info Form & Stats -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Account Info -->
                <div class="bg-white border border-hairline-soft rounded-2xl p-6 space-y-4">
                    <h3 class="text-sm font-medium uppercase tracking-wider text-ink border-b border-hairline-soft pb-3">Informasi Akun</h3>
                    <form action="#" method="POST" class="space-y-4 text-xs">
                        <div>
                            <label for="name" class="block font-medium text-ink mb-1.5">Nama Lengkap</label>
                            <input type="text"
                                   class="w-full bg-soft-cloud border border-hairline rounded-full px-4 py-2.5 text-xs text-ink cursor-not-allowed"
                                   id="name"
                                   name="name"
                                   value="{{ auth()->user()->name }}"
                                   disabled>
                            <p class="text-[11px] text-mute mt-1">Fitur edit profil mandiri akan segera hadir.</p>
                        </div>

                        <div>
                            <label for="email" class="block font-medium text-ink mb-1.5">Alamat Email</label>
                            <input type="email"
                                   class="w-full bg-soft-cloud border border-hairline rounded-full px-4 py-2.5 text-xs text-ink cursor-not-allowed"
                                   id="email"
                                   name="email"
                                   value="{{ auth()->user()->email }}"
                                   disabled>
                        </div>

                        <div>
                            <label for="role" class="block font-medium text-ink mb-1.5">Role Akun</label>
                            <input type="text"
                                   class="w-full bg-soft-cloud border border-hairline rounded-full px-4 py-2.5 text-xs text-ink cursor-not-allowed"
                                   id="role"
                                   value="{{ ucfirst(auth()->user()->role) }}"
                                   disabled>
                        </div>

                        <div>
                            <label for="joined" class="block font-medium text-ink mb-1.5">Bergabung Sejak</label>
                            <input type="text"
                                   class="w-full bg-soft-cloud border border-hairline rounded-full px-4 py-2.5 text-xs text-ink cursor-not-allowed"
                                   id="joined"
                                   value="{{ auth()->user()->created_at->translatedFormat('d F Y') }}"
                                   disabled>
                        </div>
                    </form>
                </div>

                <!-- User Stats -->
                <div class="bg-soft-cloud border border-hairline-soft rounded-2xl p-6">
                    <h3 class="text-sm font-medium uppercase tracking-wider text-ink border-b border-hairline-soft pb-3">Statistik Transaksi</h3>
                    <div class="grid grid-cols-3 gap-4 text-center divide-x divide-hairline-soft pt-4">
                        <div>
                            <h4 class="text-2xl font-medium text-ink tabular-nums">{{ auth()->user()->orders->count() }}</h4>
                            <p class="text-[10px] text-mute uppercase font-medium mt-1">Total Pesanan</p>
                        </div>
                        <div>
                            <h4 class="text-2xl font-medium text-ink tabular-nums">{{ auth()->user()->orders()->where('status', 'completed')->count() }}</h4>
                            <p class="text-[10px] text-mute uppercase font-medium mt-1">Selesai</p>
                        </div>
                        <div>
                            <h4 class="text-2xl font-medium text-ink tabular-nums">{{ auth()->user()->shippingAddresses->count() }}</h4>
                            <p class="text-[10px] text-mute uppercase font-medium mt-1">Alamat</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
