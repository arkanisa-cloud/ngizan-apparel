@extends('layouts.customer')

@section('title', 'Profil Saya - Toko Online')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
        <svg class="w-7 h-7 text-slate-650" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
        Profil Saya
    </h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Left Column: Profile Card & Quick Links -->
    <div class="space-y-6">
        <!-- Profile Card -->
        <div class="card text-center py-8">
            <div class="card-body">
                <div class="mb-4">
                    <div class="inline-flex h-24 w-24 items-center justify-center rounded-full bg-brand-100 text-brand-650 text-3xl font-bold">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                </div>
                <h3 class="text-lg font-bold text-slate-800">{{ auth()->user()->name }}</h3>
                <p class="text-sm text-slate-500 mb-4">{{ auth()->user()->email }}</p>
                <span class="badge badge-info capitalize">{{ auth()->user()->role }}</span>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="card p-0">
            <div class="divide-y divide-slate-100 text-sm">
                <a href="{{ route('customer.addresses.index') }}" class="flex items-center gap-3 px-6 py-4 text-slate-700 hover:bg-slate-50 transition-colors font-medium">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Alamat Pengiriman
                </a>
                <a href="{{ route('customer.orders.index') }}" class="flex items-center gap-3 px-6 py-4 text-slate-700 hover:bg-slate-50 transition-colors font-medium">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    Pesanan Saya
                </a>
                <div class="flex items-center gap-3 px-6 py-4 text-slate-400 cursor-not-allowed bg-slate-50/50 font-medium">
                    <svg class="w-5 h-5 text-slate-350" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Pengaturan (Coming Soon)
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Account Info Form & Stats -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Account Info -->
        <div class="card">
            <div class="card-header border-b border-slate-200">
                <h3 class="text-base font-semibold text-slate-800">Informasi Akun</h3>
            </div>
            <div class="card-body">
                <form action="#" method="POST" class="space-y-4">
                    <div>
                        <label for="name" class="form-label text-xs">Nama Lengkap</label>
                        <input type="text"
                               class="form-input-custom bg-slate-50 cursor-not-allowed text-slate-500"
                               id="name"
                               name="name"
                               value="{{ auth()->user()->name }}"
                               disabled>
                        <p class="text-[11px] text-slate-400 mt-1">Fitur edit profil akan segera hadir!</p>
                    </div>

                    <div>
                        <label for="email" class="form-label text-xs">Alamat Email</label>
                        <input type="email"
                               class="form-input-custom bg-slate-50 cursor-not-allowed text-slate-500"
                               id="email"
                               name="email"
                               value="{{ auth()->user()->email }}"
                               disabled>
                    </div>

                    <div>
                        <label for="role" class="form-label text-xs">Role</label>
                        <input type="text"
                               class="form-input-custom bg-slate-50 cursor-not-allowed text-slate-500"
                               id="role"
                               value="{{ ucfirst(auth()->user()->role) }}"
                               disabled>
                    </div>

                    <div>
                        <label for="joined" class="form-label text-xs">Bergabung Sejak</label>
                        <input type="text"
                               class="form-input-custom bg-slate-50 cursor-not-allowed text-slate-500"
                               id="joined"
                               value="{{ auth()->user()->created_at->translatedFormat('d F Y') }}"
                               disabled>
                    </div>

                    <button type="button" class="btn-primary cursor-not-allowed opacity-50 text-sm" disabled>
                        Simpan Perubahan (Coming Soon)
                    </button>
                </form>
            </div>
        </div>

        <!-- User Stats -->
        <div class="card">
            <div class="card-header border-b border-slate-200">
                <h3 class="text-base font-semibold text-slate-805">Statistik Saya</h3>
            </div>
            <div class="card-body py-6">
                <div class="grid grid-cols-3 gap-4 text-center divide-x divide-slate-100">
                    <div>
                        <h4 class="text-2xl font-bold text-brand-600">{{ auth()->user()->orders->count() }}</h4>
                        <p class="text-xs text-slate-500 mt-1 uppercase tracking-wider font-semibold">Total Pesanan</p>
                    </div>
                    <div>
                        <h4 class="text-2xl font-bold text-emerald-600">{{ auth()->user()->orders()->where('status', 'completed')->count() }}</h4>
                        <p class="text-xs text-slate-500 mt-1 uppercase tracking-wider font-semibold">Selesai</p>
                    </div>
                    <div>
                        <h4 class="text-2xl font-bold text-sky-600">{{ auth()->user()->shippingAddresses->count() }}</h4>
                        <p class="text-xs text-slate-500 mt-1 uppercase tracking-wider font-semibold">Alamat</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
