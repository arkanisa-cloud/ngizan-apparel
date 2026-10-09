@extends('layouts.admin')

@section('title', 'Akun & Sesi Pengguna · NGIZAN APPAREL')

@section('content')
<div class="space-y-6" x-data="{
    detailModalOpen: false,
    selectedUser: null,
    roleModalOpen: false,
    roleUser: null,
    openDetail(user) {
        this.selectedUser = user;
        this.detailModalOpen = true;
    },
    openRoleModal(user) {
        this.roleUser = user;
        this.roleModalOpen = true;
    }
}">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-hairline-soft pb-5">
        <div>
            <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-mute block mb-1">Pengguna & Akses</span>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-ink">Akun & Sesi Pengguna</h1>
            <p class="text-xs text-mute mt-1">Monitoring seluruh akun pengguna terdaftar, status sesi login real-time, keanggotaan VIP Premium, dan aktivitas belanja.</p>
        </div>
        <div class="flex items-center gap-2 self-start sm:self-auto">
            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>{{ $stats['online_users'] }} Pengguna Online</span>
            </span>
        </div>
    </div>

    {{-- 1. Summary Stat Cards (4 Cards Grid) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        {{-- Card 1: Total Pengguna --}}
        <div class="bg-white p-5 rounded-2xl border border-hairline-soft shadow-xs space-y-3">
            <div class="flex items-center justify-between text-mute">
                <span class="text-[11px] font-bold uppercase tracking-wider">Total Akun Terdaftar</span>
                <div class="w-8 h-8 rounded-full bg-soft-cloud flex items-center justify-center text-ink">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <div class="space-y-1">
                <div class="text-3xl font-bold tracking-tight text-ink tabular-nums">{{ number_format($stats['total_users']) }}</div>
                <div class="flex items-center gap-1.5 text-xs text-mute flex-wrap">
                    <span class="font-semibold text-ink">{{ $stats['total_customers'] }} Customer</span>
                    <span>&bull;</span>
                    <span class="font-semibold text-ink">{{ $stats['total_admins'] }} Admin</span>
                </div>
            </div>
        </div>

        {{-- Card 2: Pengguna Sedang Online --}}
        <div class="bg-white p-5 rounded-2xl border border-hairline-soft shadow-xs space-y-3">
            <div class="flex items-center justify-between text-mute">
                <span class="text-[11px] font-bold uppercase tracking-wider">Sesi Login Aktif</span>
                <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.829a5 5 0 010-7.07m7.072 0a5 5 0 010 7.07M13 12a1 1 0 11-2 0 1 1 0 012 0z" />
                    </svg>
                </div>
            </div>
            <div class="space-y-1">
                <div class="text-3xl font-bold tracking-tight text-ink tabular-nums flex items-center gap-2">
                    <span>{{ number_format($stats['online_users']) }}</span>
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                </div>
                <div class="text-xs text-emerald-700 font-medium">Aktif dalam 15 menit terakhir</div>
            </div>
        </div>

        {{-- Card 3: Member Ngizan Premium --}}
        <div class="bg-white p-5 rounded-2xl border border-hairline-soft shadow-xs space-y-3">
            <div class="flex items-center justify-between text-mute">
                <span class="text-[11px] font-bold uppercase tracking-wider">Ngizan Premium (VIP)</span>
                <div class="w-8 h-8 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                </div>
            </div>
            <div class="space-y-1">
                <div class="text-3xl font-bold tracking-tight text-amber-950 tabular-nums">{{ number_format($stats['premium_users']) }}</div>
                <div class="text-xs text-amber-800 font-medium">Pelanggan diskon 5% aktif</div>
            </div>
        </div>

        {{-- Card 4: Metode Login (Google vs Email) --}}
        <div class="bg-white p-5 rounded-2xl border border-hairline-soft shadow-xs space-y-3">
            <div class="flex items-center justify-between text-mute">
                <span class="text-[11px] font-bold uppercase tracking-wider">Metode Autentikasi</span>
                <div class="w-8 h-8 rounded-full bg-soft-cloud flex items-center justify-center text-ink">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
            </div>
            <div class="space-y-1">
                <div class="text-3xl font-bold tracking-tight text-ink tabular-nums">{{ number_format($stats['google_users']) }} <span class="text-xs font-semibold text-mute font-normal">Google</span></div>
                <div class="text-xs text-mute">{{ $stats['email_users'] }} akun via Email & Password</div>
            </div>
        </div>

    </div>

    {{-- 2. Filter & Search Toolbar --}}
    <div class="bg-white p-4 rounded-2xl border border-hairline-soft flex flex-col lg:flex-row gap-3 justify-between items-stretch lg:items-center text-xs">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap items-center gap-3 w-full">
            
            {{-- Search Input --}}
            <div class="relative flex-1 min-w-[220px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau nomor WA..." 
                       class="w-full bg-soft-cloud border border-hairline pl-9 pr-4 py-2 rounded-full text-xs text-ink placeholder:text-neutral-400 focus:outline-none focus:border-ink">
                <svg class="w-4 h-4 text-mute absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            {{-- Dropdown Filter Peran (Role) --}}
            @php
                $currentRole = request('role');
                $roleLabels = [
                    'admin'    => 'Administrator',
                    'customer' => 'Customer',
                ];
                $roleLabel = $roleLabels[$currentRole ?? ''] ?? 'Semua Peran';
            @endphp
            <div class="relative" x-data="{ roleOpen: false }">
                <button type="button" @click="roleOpen = !roleOpen"
                    class="inline-flex items-center gap-2 bg-soft-cloud hover:bg-neutral-200 border border-hairline text-ink text-xs font-semibold rounded-full px-4 py-2 transition focus:outline-none cursor-pointer select-none">
                    <span class="text-mute font-normal">Peran:</span>
                    <span class="font-bold">{{ $roleLabel }}</span>
                    <svg class="w-3.5 h-3.5 text-mute transition-transform duration-200" :class="roleOpen ? 'rotate-180 text-ink' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="roleOpen" @click.away="roleOpen = false" x-cloak
                    x-transition:enter="transition ease-out duration-150 transform"
                    x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100 transform"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                    class="absolute left-0 mt-2 w-48 bg-white border border-hairline-soft rounded-2xl py-1.5 shadow-xl z-40 text-xs overflow-hidden backdrop-blur-xl">
                    <div class="px-3.5 py-1.5 border-b border-hairline-soft text-[10px] font-bold uppercase tracking-wider text-mute">
                        Filter Peran Akun
                    </div>
                    <div class="py-1">
                        <a href="{{ route('admin.users.index', array_merge(request()->except('page', 'role'), [])) }}"
                            class="flex items-center justify-between px-3.5 py-2 transition {{ !$currentRole ? 'bg-soft-cloud text-ink font-bold' : 'text-neutral-600 hover:text-ink hover:bg-soft-cloud/70 font-medium' }}">
                            <span>Semua Peran</span>
                            @if(!$currentRole)
                                <svg class="w-4 h-4 text-ink shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                            @endif
                        </a>
                        @foreach($roleLabels as $key => $lbl)
                            @php $isRoleSelected = ($currentRole === $key); @endphp
                            <a href="{{ route('admin.users.index', array_merge(request()->except('page', 'role'), ['role' => $key])) }}"
                                class="flex items-center justify-between px-3.5 py-2 transition {{ $isRoleSelected ? 'bg-soft-cloud text-ink font-bold' : 'text-neutral-600 hover:text-ink hover:bg-soft-cloud/70 font-medium' }}">
                                <span>{{ $lbl }}</span>
                                @if($isRoleSelected)
                                    <svg class="w-4 h-4 text-ink shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Dropdown Filter Status --}}
            @php
                $currentStatus = request('status');
                $statusLabels = [
                    'online'  => 'Sedang Online',
                    'premium' => 'Member VIP (5%)',
                    'regular' => 'Member Reguler',
                ];
                $statusLabel = $statusLabels[$currentStatus ?? ''] ?? 'Semua Status';
            @endphp
            <div class="relative" x-data="{ statusOpen: false }">
                <button type="button" @click="statusOpen = !statusOpen"
                    class="inline-flex items-center gap-2 bg-soft-cloud hover:bg-neutral-200 border border-hairline text-ink text-xs font-semibold rounded-full px-4 py-2 transition focus:outline-none cursor-pointer select-none">
                    <span class="text-mute font-normal">Status:</span>
                    <span class="font-bold">{{ $statusLabel }}</span>
                    <svg class="w-3.5 h-3.5 text-mute transition-transform duration-200" :class="statusOpen ? 'rotate-180 text-ink' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="statusOpen" @click.away="statusOpen = false" x-cloak
                    x-transition:enter="transition ease-out duration-150 transform"
                    x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100 transform"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                    class="absolute left-0 mt-2 w-52 bg-white border border-hairline-soft rounded-2xl py-1.5 shadow-xl z-40 text-xs overflow-hidden backdrop-blur-xl">
                    <div class="px-3.5 py-1.5 border-b border-hairline-soft text-[10px] font-bold uppercase tracking-wider text-mute">
                        Filter Status Keanggotaan & Sesi
                    </div>
                    <div class="py-1">
                        <a href="{{ route('admin.users.index', array_merge(request()->except('page', 'status'), [])) }}"
                            class="flex items-center justify-between px-3.5 py-2 transition {{ !$currentStatus ? 'bg-soft-cloud text-ink font-bold' : 'text-neutral-600 hover:text-ink hover:bg-soft-cloud/70 font-medium' }}">
                            <span>Semua Status</span>
                            @if(!$currentStatus)
                                <svg class="w-4 h-4 text-ink shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                            @endif
                        </a>
                        @foreach($statusLabels as $key => $lbl)
                            @php $isStSelected = ($currentStatus === $key); @endphp
                            <a href="{{ route('admin.users.index', array_merge(request()->except('page', 'status'), ['status' => $key])) }}"
                                class="flex items-center justify-between px-3.5 py-2 transition {{ $isStSelected ? 'bg-soft-cloud text-ink font-bold' : 'text-neutral-600 hover:text-ink hover:bg-soft-cloud/70 font-medium' }}">
                                <span>{{ $lbl }}</span>
                                @if($isStSelected)
                                    <svg class="w-4 h-4 text-ink shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Dropdown Filter Provider Login --}}
            @php
                $currentProvider = request('provider');
                $providerLabels = [
                    'google' => 'Google OAuth',
                    'email'  => 'Email & Password',
                ];
                $providerLabel = $providerLabels[$currentProvider ?? ''] ?? 'Semua Login';
            @endphp
            <div class="relative" x-data="{ provOpen: false }">
                <button type="button" @click="provOpen = !provOpen"
                    class="inline-flex items-center gap-2 bg-soft-cloud hover:bg-neutral-200 border border-hairline text-ink text-xs font-semibold rounded-full px-4 py-2 transition focus:outline-none cursor-pointer select-none">
                    <span class="text-mute font-normal">Login:</span>
                    <span class="font-bold">{{ $providerLabel }}</span>
                    <svg class="w-3.5 h-3.5 text-mute transition-transform duration-200" :class="provOpen ? 'rotate-180 text-ink' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="provOpen" @click.away="provOpen = false" x-cloak
                    x-transition:enter="transition ease-out duration-150 transform"
                    x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100 transform"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                    class="absolute left-0 mt-2 w-52 bg-white border border-hairline-soft rounded-2xl py-1.5 shadow-xl z-40 text-xs overflow-hidden backdrop-blur-xl">
                    <div class="px-3.5 py-1.5 border-b border-hairline-soft text-[10px] font-bold uppercase tracking-wider text-mute">
                        Filter Metode Login
                    </div>
                    <div class="py-1">
                        <a href="{{ route('admin.users.index', array_merge(request()->except('page', 'provider'), [])) }}"
                            class="flex items-center justify-between px-3.5 py-2 transition {{ !$currentProvider ? 'bg-soft-cloud text-ink font-bold' : 'text-neutral-600 hover:text-ink hover:bg-soft-cloud/70 font-medium' }}">
                            <span>Semua Metode</span>
                            @if(!$currentProvider)
                                <svg class="w-4 h-4 text-ink shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                            @endif
                        </a>
                        @foreach($providerLabels as $key => $lbl)
                            @php $isProvSelected = ($currentProvider === $key); @endphp
                            <a href="{{ route('admin.users.index', array_merge(request()->except('page', 'provider'), ['provider' => $key])) }}"
                                class="flex items-center justify-between px-3.5 py-2 transition {{ $isProvSelected ? 'bg-soft-cloud text-ink font-bold' : 'text-neutral-600 hover:text-ink hover:bg-soft-cloud/70 font-medium' }}">
                                <span>{{ $lbl }}</span>
                                @if($isProvSelected)
                                    <svg class="w-4 h-4 text-ink shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Reset Filter Button --}}
            @if(request()->hasAny(['search', 'role', 'status', 'provider', 'sort']))
                <a href="{{ route('admin.users.index') }}" class="px-3.5 py-2 text-xs font-semibold text-mute hover:text-ink underline transition">
                    Reset Filter
                </a>
            @endif
        </form>
    </div>

    {{-- 3. Users Data Table --}}
    <div class="bg-white rounded-2xl border border-hairline-soft overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-ink">
                <thead class="bg-soft-cloud uppercase font-semibold text-mute border-b border-hairline-soft tracking-wider text-[10px]">
                    <tr>
                        <th class="p-4">Pengguna</th>
                        <th class="p-4">Status Sesi & Login</th>
                        <th class="p-4">Peran</th>
                        <th class="p-4">Keanggotaan</th>
                        <th class="p-4">Aktivitas Belanja</th>
                        <th class="p-4">Terdaftar</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline-soft">
                    @forelse($users as $u)
                        <tr class="hover:bg-soft-cloud/50 transition">
                            
                            {{-- 1. Pengguna --}}
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-soft-cloud border border-hairline-soft flex items-center justify-center font-bold text-sm text-ink shrink-0 overflow-hidden">
                                        @if($u->avatar)
                                            <img src="{{ $u->avatar }}" alt="{{ $u->name }}" class="w-full h-full object-cover">
                                        @else
                                            <span>{{ strtoupper(substr($u->name, 0, 2)) }}</span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="font-bold text-ink truncate block text-sm">{{ $u->name }}</span>
                                            @if($u->id === auth()->id())
                                                <span class="px-2 py-0.5 bg-neutral-900 text-white rounded-full text-[9px] font-bold uppercase tracking-wider">Anda</span>
                                            @endif
                                        </div>
                                        <span class="text-[11px] text-mute font-mono block truncate">{{ $u->email }}</span>
                                        @if($u->phone)
                                            <span class="text-[11px] text-neutral-400 font-mono block">{{ $u->phone }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- 2. Status Sesi & Login --}}
                            <td class="p-4 space-y-1">
                                <div>
                                    @if($u->is_online)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            <span>Online Sekarang</span>
                                        </span>
                                    @elseif($u->last_activity_time)
                                        <span class="text-[11px] text-mute font-medium flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-neutral-300"></span>
                                            <span>Aktif {{ $u->last_activity_time->diffForHumans() }}</span>
                                        </span>
                                    @else
                                        <span class="text-[11px] text-neutral-400">Belum ada catatan login</span>
                                    @endif
                                </div>
                                <div class="text-[10px] text-neutral-400 font-mono truncate max-w-[200px]" title="{{ $u->device_info }}">
                                    {{ $u->device_info }}
                                </div>
                            </td>

                            {{-- 3. Peran --}}
                            <td class="p-4">
                                @if($u->isAdmin())
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-ink text-white rounded-full text-[10px] font-bold uppercase tracking-wider">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                        <span>Administrator</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-soft-cloud text-ink border border-hairline-soft rounded-full text-[10px] font-semibold uppercase tracking-wider">
                                        <span>Customer</span>
                                    </span>
                                @endif
                            </td>

                            {{-- 4. Status Membership --}}
                            <td class="p-4">
                                @if($u->isPremiumActive())
                                    <div class="space-y-0.5">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-amber-50 border border-amber-200 text-amber-900 rounded-full text-[10px] font-bold uppercase tracking-wider">
                                            <span>⭐ VIP 5% OFF</span>
                                        </span>
                                        @if($u->premium_until)
                                            <span class="text-[10px] text-mute block">s.d. {{ $u->premium_until->format('d M Y') }}</span>
                                        @else
                                            <span class="text-[10px] text-mute block">Seumur Hidup</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-[11px] text-mute font-medium bg-soft-cloud px-2.5 py-0.5 rounded-full border border-hairline-soft">
                                        Reguler
                                    </span>
                                @endif
                            </td>

                            {{-- 5. Aktivitas Belanja --}}
                            <td class="p-4 space-y-0.5">
                                <div class="font-bold text-ink tabular-nums text-sm">
                                    Rp {{ number_format($u->total_spend ?? 0, 0, ',', '.') }}
                                </div>
                                <div class="text-[11px] text-mute">
                                    {{ $u->orders_count }} Pesanan &bull; {{ $u->reviews_count }} Ulasan
                                </div>
                            </td>

                            {{-- 6. Terdaftar --}}
                            <td class="p-4">
                                <span class="font-medium text-ink block">{{ $u->created_at->translatedFormat('d M Y') }}</span>
                                <span class="text-[10px] text-mute font-mono">{{ $u->created_at->format('H:i') }} WIB</span>
                            </td>

                            {{-- 7. Aksi --}}
                            <td class="p-4 text-right space-x-1 whitespace-nowrap">
                                
                                {{-- Tombol Detail --}}
                                <button type="button" @click="openDetail({{ json_encode([
                                    'id' => $u->id,
                                    'name' => $u->name,
                                    'email' => $u->email,
                                    'phone' => $u->phone ?? '-',
                                    'role' => $u->role,
                                    'is_premium' => $u->isPremiumActive(),
                                    'premium_until' => $u->premium_until ? $u->premium_until->format('d M Y') : null,
                                    'is_online' => $u->is_online,
                                    'last_activity' => $u->last_activity_time ? $u->last_activity_time->diffForHumans() : 'Belum tercatat',
                                    'device' => $u->device_info,
                                    'google_auth' => $u->google_id ? true : false,
                                    'orders_count' => $u->orders_count,
                                    'total_spend' => number_format($u->total_spend ?? 0, 0, ',', '.'),
                                    'reviews_count' => $u->reviews_count,
                                    'addresses_count' => $u->shipping_addresses_count,
                                    'registered_at' => $u->created_at->translatedFormat('d F Y, H:i') . ' WIB',
                                ]) }})"
                                    class="p-2 bg-soft-cloud hover:bg-neutral-200 text-ink rounded-full transition inline-flex items-center justify-center cursor-pointer"
                                    title="Lihat Detail Profil">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>

                                {{-- Tombol Ubah Peran --}}
                                @if($u->id !== auth()->id())
                                    <button type="button" @click="openRoleModal({{ json_encode(['id' => $u->id, 'name' => $u->name, 'role' => $u->role]) }})"
                                        class="p-2 bg-soft-cloud hover:bg-neutral-200 text-ink rounded-full transition inline-flex items-center justify-center cursor-pointer"
                                        title="Ubah Hak Akses / Peran">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                        </svg>
                                    </button>

                                    {{-- Tombol Hapus --}}
                                    <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $u->name }}? Data pesanan dan alamat yang terkait akan ikut terdampak.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-2 bg-soft-cloud hover:bg-rose-50 text-sale rounded-full transition inline-flex items-center justify-center cursor-pointer"
                                            title="Hapus Akun Pengguna">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                @endif

                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-12 text-center text-mute space-y-2">
                                <div class="w-12 h-12 rounded-full bg-soft-cloud mx-auto flex items-center justify-center text-neutral-400">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                <p class="font-bold text-ink">Tidak Ada Pengguna Ditemukan</p>
                                <p class="text-xs">Coba sesuaikan kata kunci pencarian atau filter peran/status yang Anda pilih.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="p-4 border-t border-hairline-soft bg-white">
            {{ $users->links() }}
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- MODAL 1: Detail Lengkap Pengguna & Sesi --}}
    {{-- ======================================================== --}}
    <div x-show="detailModalOpen" @click.away="detailModalOpen = false" x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4 backdrop-blur-xs">
        
        <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 space-y-6 shadow-2xl border border-hairline-soft text-left animate-in fade-in zoom-in-95 duration-200 max-h-[90vh] overflow-y-auto">
            
            {{-- Modal Header --}}
            <div class="flex items-center justify-between border-b border-hairline-soft pb-4">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-soft-cloud border border-hairline-soft flex items-center justify-center font-bold text-base text-ink shrink-0">
                        <span x-text="selectedUser ? selectedUser.name.substring(0, 2).toUpperCase() : 'US'"></span>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg text-ink" x-text="selectedUser ? selectedUser.name : ''"></h3>
                        <p class="text-xs text-mute font-mono" x-text="selectedUser ? selectedUser.email : ''"></p>
                    </div>
                </div>
                <button type="button" @click="detailModalOpen = false" class="w-8 h-8 rounded-full bg-soft-cloud hover:bg-neutral-200 text-ink flex items-center justify-center transition cursor-pointer text-lg font-bold">
                    &times;
                </button>
            </div>

            {{-- Modal Content Grid --}}
            <div class="space-y-4 text-xs">
                
                {{-- Status Pills --}}
                <div class="flex flex-wrap items-center gap-2">
                    <template x-if="selectedUser && selectedUser.is_online">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Sedang Online</span>
                        </span>
                    </template>
                    <template x-if="selectedUser && !selectedUser.is_online">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-soft-cloud text-mute border border-hairline-soft">
                            <span>Terakhir aktif:</span>
                            <span class="font-bold text-ink" x-text="selectedUser ? selectedUser.last_activity : ''"></span>
                        </span>
                    </template>

                    <template x-if="selectedUser && selectedUser.role === 'admin'">
                        <span class="px-3 py-1 bg-ink text-white rounded-full text-xs font-bold uppercase tracking-wider">
                            Administrator
                        </span>
                    </template>
                    <template x-if="selectedUser && selectedUser.role === 'customer'">
                        <span class="px-3 py-1 bg-soft-cloud text-ink border border-hairline-soft rounded-full text-xs font-semibold uppercase tracking-wider">
                            Customer
                        </span>
                    </template>

                    <template x-if="selectedUser && selectedUser.is_premium">
                        <span class="px-3 py-1 bg-amber-50 text-amber-950 border border-amber-200 rounded-full text-xs font-bold uppercase tracking-wider">
                            ⭐ Member VIP (5% OFF)
                        </span>
                    </template>

                    <template x-if="selectedUser && selectedUser.google_auth">
                        <span class="px-3 py-1 bg-sky-50 text-sky-950 border border-sky-200 rounded-full text-xs font-semibold">
                            Google OAuth
                        </span>
                    </template>
                </div>

                {{-- Metrics Box --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-soft-cloud p-4 rounded-2xl border border-hairline-soft text-center">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-mute block mb-0.5">Total Pesanan</span>
                        <span class="font-bold text-base text-ink" x-text="selectedUser ? selectedUser.orders_count : 0"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-mute block mb-0.5">Total Belanja</span>
                        <span class="font-bold text-base text-ink" x-text="'Rp ' + (selectedUser ? selectedUser.total_spend : 0)"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-mute block mb-0.5">Ulasan Produk</span>
                        <span class="font-bold text-base text-ink" x-text="selectedUser ? selectedUser.reviews_count : 0"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-mute block mb-0.5">Buku Alamat</span>
                        <span class="font-bold text-base text-ink" x-text="selectedUser ? selectedUser.addresses_count : 0"></span>
                    </div>
                </div>

                {{-- Detail List --}}
                <div class="space-y-3 bg-white p-4 rounded-2xl border border-hairline-soft">
                    <div class="flex justify-between items-center py-1 border-b border-hairline-soft">
                        <span class="text-mute font-medium">Nomor WhatsApp / HP</span>
                        <div class="flex items-center gap-2">
                            <span class="font-mono font-bold text-ink" x-text="selectedUser ? selectedUser.phone : '-'"></span>
                            <template x-if="selectedUser && selectedUser.phone && selectedUser.phone !== '-'">
                                <a :href="'https://wa.me/' + selectedUser.phone.replace(/[^0-9]/g, '')" target="_blank"
                                   class="px-2 py-0.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md text-[10px] font-bold transition">
                                    Chat WA
                                </a>
                            </template>
                        </div>
                    </div>

                    <div class="flex justify-between items-center py-1 border-b border-hairline-soft">
                        <span class="text-mute font-medium">Perangkat & Browser</span>
                        <span class="font-medium text-ink text-right" x-text="selectedUser ? selectedUser.device : '-'"></span>
                    </div>

                    <div class="flex justify-between items-center py-1 border-b border-hairline-soft">
                        <span class="text-mute font-medium">Masa Berlaku VIP</span>
                        <span class="font-semibold text-ink" x-text="selectedUser && selectedUser.premium_until ? 'Aktif s.d. ' + selectedUser.premium_until : (selectedUser && selectedUser.is_premium ? 'Aktif Seumur Hidup' : 'Tidak Aktif')"></span>
                    </div>

                    <div class="flex justify-between items-center py-1">
                        <span class="text-mute font-medium">Waktu Registrasi Akun</span>
                        <span class="font-medium text-ink" x-text="selectedUser ? selectedUser.registered_at : '-'"></span>
                    </div>
                </div>

            </div>

            {{-- Modal Footer --}}
            <div class="pt-2 flex justify-end">
                <button type="button" @click="detailModalOpen = false"
                    class="px-5 py-2.5 bg-soft-cloud hover:bg-neutral-200 text-ink rounded-full text-xs font-bold transition cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- ======================================================== --}}
    {{-- MODAL 2: Ubah Hak Akses / Peran Akun --}}
    {{-- ======================================================== --}}
    <div x-show="roleModalOpen" @click.away="roleModalOpen = false" x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4 backdrop-blur-xs">
        
        <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 space-y-5 shadow-2xl border border-hairline-soft text-left animate-in fade-in zoom-in-95 duration-200">
            <div class="flex items-center justify-between border-b border-hairline-soft pb-4">
                <h3 class="font-bold text-base text-ink">Ubah Hak Akses Pengguna</h3>
                <button type="button" @click="roleModalOpen = false" class="text-mute hover:text-ink text-2xl font-bold transition">&times;</button>
            </div>

            <form :action="'{{ url('/admin/users') }}/' + (roleUser ? roleUser.id : '') + '/role'" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PATCH')

                <div>
                    <p class="text-mute mb-2">Pilih peran hak akses baru untuk pengguna <strong class="text-ink" x-text="roleUser ? roleUser.name : ''"></strong>:</p>
                    <div class="space-y-2">
                        <label class="flex items-center gap-3 p-3 bg-soft-cloud rounded-xl border border-hairline-soft cursor-pointer hover:bg-neutral-200/60 transition">
                            <input type="radio" name="role" value="customer" :checked="roleUser && roleUser.role === 'customer'" class="text-ink focus:ring-ink">
                            <div>
                                <span class="font-bold text-ink block">Customer (Pelanggan)</span>
                                <span class="text-[11px] text-mute">Hanya memiliki akses belanja, melihat riwayat pesanan, dan menulis ulasan.</span>
                            </div>
                        </label>

                        <label class="flex items-center gap-3 p-3 bg-soft-cloud rounded-xl border border-hairline-soft cursor-pointer hover:bg-neutral-200/60 transition">
                            <input type="radio" name="role" value="admin" :checked="roleUser && roleUser.role === 'admin'" class="text-ink focus:ring-ink">
                            <div>
                                <span class="font-bold text-ink block">Administrator (Pengelola)</span>
                                <span class="text-[11px] text-mute">Memiliki akses penuh ke seluruh menu backoffice, stok, pesanan, dan laporan.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-hairline-soft">
                    <button type="button" @click="roleModalOpen = false"
                        class="px-4 py-2 bg-soft-cloud hover:bg-neutral-200 text-ink rounded-full font-bold transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2 bg-ink hover:opacity-90 text-white rounded-full font-bold transition cursor-pointer">
                        Simpan Peran
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
