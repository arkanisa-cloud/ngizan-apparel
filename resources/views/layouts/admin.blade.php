<!DOCTYPE html>
<html lang="id" class="h-full bg-canvas overflow-x-hidden">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Backoffice · NGIZAN APPAREL')</title>

    {{-- Preconnect & Google Fonts CDN --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    @stack('styles')
</head>

<body class="h-full font-sans antialiased text-ink bg-canvas overflow-x-hidden" x-data="{ sidebarOpen: false }">
    <div class="flex h-full min-h-screen w-full max-w-full overflow-x-hidden">

        {{-- ===== MOBILE SIDEBAR DRAWER ===== --}}
        <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-50 lg:hidden flex" role="dialog" aria-modal="true">
            {{-- Backdrop --}}
            <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/60 backdrop-blur-sm"
                @click="sidebarOpen = false"></div>

            {{-- Off-canvas Menu --}}
            <div x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300 transform"
                x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="transition ease-in-out duration-300 transform"
                x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
                class="relative flex-1 flex flex-col max-w-xs w-full bg-[#111111] text-white border-r border-neutral-800 z-10 overflow-y-auto">

                {{-- Brand Header & Close --}}
                <div class="flex items-center justify-between px-6 py-5 border-b border-neutral-800">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex items-center justify-center w-8 h-8 rounded-full bg-white text-ink font-display text-lg font-bold">
                            N
                        </div>
                        <div>
                            <span
                                class="font-display font-medium text-lg tracking-wider text-white uppercase block leading-none">NGIZAN</span>
                            <span
                                class="text-[9px] uppercase font-bold text-neutral-400 tracking-[0.2em] mt-1 block">Backoffice</span>
                        </div>
                    </div>
                    <button type="button" @click="sidebarOpen = false"
                        class="p-2 text-neutral-400 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Navigation Links Mobile --}}
                <nav class="flex-1 px-4 py-5 space-y-1 text-xs">
                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-full transition {{ request()->routeIs('admin.dashboard') ? 'bg-white text-ink font-bold shadow-xs' : 'text-neutral-400 hover:text-white hover:bg-neutral-800' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    <div class="pt-4 pb-1">
                        <p class="px-3.5 text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400">Master Data
                        </p>
                    </div>
                    <a href="{{ route('admin.products.index') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-full transition {{ request()->routeIs('admin.products.*') ? 'bg-white text-ink font-bold shadow-xs' : 'text-neutral-400 hover:text-white hover:bg-neutral-800' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <span>Katalog Produk</span>
                    </a>
                    <a href="{{ route('admin.size-charts.index') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-full transition {{ request()->routeIs('admin.size-charts.*') ? 'bg-white text-ink font-bold shadow-xs' : 'text-neutral-400 hover:text-white hover:bg-neutral-800' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        <span>Panduan Ukuran</span>
                    </a>
                    <a href="{{ route('admin.categories.index') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-full transition {{ request()->routeIs('admin.categories.*') ? 'bg-white text-ink font-bold shadow-xs' : 'text-neutral-400 hover:text-white hover:bg-neutral-800' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        <span>Kategori Edisi</span>
                    </a>
                    <a href="{{ route('admin.banners.index') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-full transition {{ request()->routeIs('admin.banners.*') ? 'bg-white text-ink font-bold shadow-xs' : 'text-neutral-400 hover:text-white hover:bg-neutral-800' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Banner & Hero</span>
                    </a>

                    <div class="pt-4 pb-1">
                        <p class="px-3.5 text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400">Transaksi &
                            Logistik</p>
                    </div>
                    <a href="{{ route('admin.orders.index') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-full transition {{ request()->routeIs('admin.orders.*') ? 'bg-white text-ink font-bold shadow-xs' : 'text-neutral-400 hover:text-white hover:bg-neutral-800' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        <span>Pesanan Masuk</span>
                    </a>

                    <div class="pt-4 pb-1">
                        <p class="px-3.5 text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400">Gudang &
                            Inventori</p>
                    </div>
                    <a href="{{ route('admin.stock-ins.index') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-full transition {{ request()->routeIs('admin.stock-ins.*') ? 'bg-white text-ink font-bold shadow-xs' : 'text-neutral-400 hover:text-white hover:bg-neutral-800' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        <span>Restock Masuk</span>
                    </a>
                    <a href="{{ route('admin.stock-outs.index') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-full transition {{ request()->routeIs('admin.stock-outs.*') ? 'bg-white text-ink font-bold shadow-xs' : 'text-neutral-400 hover:text-white hover:bg-neutral-800' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Penyesuaian Keluar</span>
                    </a>

                    <div class="pt-4 pb-1">
                        <p class="px-3.5 text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400">Laporan &
                            Keuangan</p>
                    </div>
                    <a href="{{ route('admin.reports.sales') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-full transition {{ request()->routeIs('admin.reports.sales') ? 'bg-white text-ink font-bold shadow-xs' : 'text-neutral-400 hover:text-white hover:bg-neutral-800' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Laporan Penjualan</span>
                    </a>
                    <a href="{{ route('admin.reports.stock') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-full transition {{ request()->routeIs('admin.reports.stock') ? 'bg-white text-ink font-bold shadow-xs' : 'text-neutral-400 hover:text-white hover:bg-neutral-800' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span>Audit Stok Gudang</span>
                    </a>

                    <div class="pt-4 pb-1">
                        <p class="px-3.5 text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400">Pengaturan
                        </p>
                    </div>
                    <a href="{{ route('admin.profile.edit') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-full transition {{ request()->routeIs('admin.profile.*') ? 'bg-white text-ink font-bold shadow-xs' : 'text-neutral-400 hover:text-white hover:bg-neutral-800' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Profil & Keamanan</span>
                    </a>
                </nav>

                <div class="p-4 border-t border-neutral-800">
                    <a href="{{ route('home') }}" target="_blank"
                        class="flex items-center justify-center gap-2 p-2.5 bg-neutral-900 hover:bg-neutral-800 text-neutral-300 hover:text-white rounded-full text-xs font-semibold uppercase tracking-wider transition border border-neutral-800">
                        <span>Lihat Toko Online</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- ===== SIDEBAR (Desktop) — DEEP INK HIGH CONTRAST ===== --}}
        <aside class="hidden lg:flex lg:w-64 lg:flex-col lg:fixed lg:inset-y-0 z-40">
            <div class="flex flex-col flex-grow bg-[#111111] text-white overflow-y-auto border-r border-neutral-800">

                {{-- Brand Header --}}
                <div class="flex items-center gap-3 px-6 py-5 border-b border-neutral-800">
                    <div
                        class="flex items-center justify-center w-8 h-8 rounded-full bg-white text-ink font-display text-lg font-bold">
                        N
                    </div>
                    <div>
                        <span
                            class="font-display font-medium text-lg tracking-wider text-white uppercase block leading-none">NGIZAN</span>
                        <span
                            class="text-[9px] uppercase font-bold text-neutral-400 tracking-[0.2em] mt-1 block">Backoffice</span>
                    </div>
                </div>

                {{-- Navigation Links --}}
                <nav class="flex-1 px-4 py-5 space-y-1 text-xs">

                    {{-- Dashboard --}}
                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-full transition {{ request()->routeIs('admin.dashboard') ? 'bg-white text-ink font-bold shadow-xs' : 'text-neutral-400 hover:text-white hover:bg-neutral-800' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>Dashboard</span>
                    </a>

                    {{-- Section: Master Data --}}
                    <div class="pt-4 pb-1">
                        <p class="px-3.5 text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400">Master Data
                        </p>
                    </div>
                    <a href="{{ route('admin.products.index') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-full transition {{ request()->routeIs('admin.products.*') ? 'bg-white text-ink font-bold shadow-xs' : 'text-neutral-400 hover:text-white hover:bg-neutral-800' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <span>Katalog Produk</span>
                    </a>
                    <a href="{{ route('admin.size-charts.index') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-full transition {{ request()->routeIs('admin.size-charts.*') ? 'bg-white text-ink font-bold shadow-xs' : 'text-neutral-400 hover:text-white hover:bg-neutral-800' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        <span>Panduan Ukuran</span>
                    </a>
                    <a href="{{ route('admin.categories.index') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-full transition {{ request()->routeIs('admin.categories.*') ? 'bg-white text-ink font-bold shadow-xs' : 'text-neutral-400 hover:text-white hover:bg-neutral-800' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        <span>Kategori Edisi</span>
                    </a>
                    <a href="{{ route('admin.banners.index') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-full transition {{ request()->routeIs('admin.banners.*') ? 'bg-white text-ink font-bold shadow-xs' : 'text-neutral-400 hover:text-white hover:bg-neutral-800' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Banner & Hero</span>
                    </a>

                    {{-- Section: Logistik & Pesanan --}}
                    <div class="pt-4 pb-1">
                        <p class="px-3.5 text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400">Transaksi &
                            Logistik</p>
                    </div>
                    <a href="{{ route('admin.orders.index') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-full transition {{ request()->routeIs('admin.orders.*') ? 'bg-white text-ink font-bold shadow-xs' : 'text-neutral-400 hover:text-white hover:bg-neutral-800' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        <span>Pesanan Masuk</span>
                    </a>

                    {{-- Section: Gudang & Inventori --}}
                    <div class="pt-4 pb-1">
                        <p class="px-3.5 text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400">Gudang &
                            Inventori</p>
                    </div>
                    <a href="{{ route('admin.stock-ins.index') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-full transition {{ request()->routeIs('admin.stock-ins.*') ? 'bg-white text-ink font-bold shadow-xs' : 'text-neutral-400 hover:text-white hover:bg-neutral-800' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                        <span>Restock Masuk</span>
                    </a>
                    <a href="{{ route('admin.stock-outs.index') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-full transition {{ request()->routeIs('admin.stock-outs.*') ? 'bg-white text-ink font-bold shadow-xs' : 'text-neutral-400 hover:text-white hover:bg-neutral-800' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Penyesuaian Keluar</span>
                    </a>

                    {{-- Section: Laporan Finansial --}}
                    <div class="pt-4 pb-1">
                        <p class="px-3.5 text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400">Laporan &
                            Keuangan</p>
                    </div>
                    <a href="{{ route('admin.reports.sales') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-full transition {{ request()->routeIs('admin.reports.sales') ? 'bg-white text-ink font-bold shadow-xs' : 'text-neutral-400 hover:text-white hover:bg-neutral-800' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Laporan Penjualan</span>
                    </a>
                    <a href="{{ route('admin.reports.stock') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-full transition {{ request()->routeIs('admin.reports.stock') ? 'bg-white text-ink font-bold shadow-xs' : 'text-neutral-400 hover:text-white hover:bg-neutral-800' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        <span>Audit Stok Gudang</span>
                    </a>

                    <div class="pt-4 pb-1">
                        <p class="px-3.5 text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400">Pengaturan
                        </p>
                    </div>
                    <a href="{{ route('admin.profile.edit') }}"
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-full transition {{ request()->routeIs('admin.profile.*') ? 'bg-white text-ink font-bold shadow-xs' : 'text-neutral-400 hover:text-white hover:bg-neutral-800' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Profil & Keamanan</span>
                    </a>
                </nav>

                {{-- Storefront Quick Link --}}
                <div class="p-4 border-t border-neutral-800">
                    <a href="{{ route('home') }}" target="_blank"
                        class="flex items-center justify-center gap-2 p-2.5 bg-neutral-900 hover:bg-neutral-800 text-neutral-300 hover:text-white rounded-full text-xs font-semibold uppercase tracking-wider transition border border-neutral-800">
                        <span>Lihat Toko Online</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                </div>

            </div>
        </aside>

        {{-- ===== MAIN CONTENT AREA ===== --}}
        <div class="flex flex-col flex-1 lg:pl-64 min-w-0 w-full max-w-full overflow-x-hidden">

            {{-- Top Navigation Header --}}
            <header
                class="sticky top-0 z-30 flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8 bg-white/95 backdrop-blur-md border-b border-hairline-soft shrink-0 w-full">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true"
                        class="lg:hidden p-1.5 -ml-1.5 text-ink hover:text-mute rounded-lg transition"
                        aria-label="Buka Menu">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 shrink-0"></span>
                        <span
                            class="text-xs font-bold text-ink uppercase tracking-wider hidden sm:inline-block">Backoffice
                            System</span>
                        <span class="text-neutral-300 hidden sm:inline-block font-light">&bull;</span>
                        <span class="text-xs text-mute font-medium">{{ now()->translatedFormat('l, d F Y') }}</span>
                    </div>
                </div>

                {{-- Admin Profile Dropdown (Variant C - UI Kit Specification) --}}
                <div class="relative" x-data="{ open: false }">
                    <button type="button" @click="open = !open" @click.outside="open = false"
                        class="flex items-center gap-2.5 p-1 sm:px-3 sm:py-1.5 rounded-full hover:bg-soft-cloud border border-transparent hover:border-hairline-soft transition group cursor-pointer focus:outline-none"
                        :class="{ 'bg-soft-cloud border-hairline-soft': open }">
                        <div
                            class="w-8 h-8 rounded-full bg-ink text-white font-bold text-xs flex items-center justify-center uppercase shadow-xs group-hover:scale-105 transition-transform">
                            {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                        </div>
                        <div class="text-left hidden sm:flex flex-col justify-center">
                            <span class="text-xs font-bold text-ink leading-tight">{{ Auth::user()->name }}</span>
                            <span class="text-[10px] text-mute uppercase font-semibold tracking-wider">Super
                                Admin</span>
                        </div>
                        <svg class="w-3.5 h-3.5 text-mute transition-transform duration-200"
                            :class="{ 'rotate-180 text-ink': open }" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    {{-- Dropdown Card --}}
                    <div x-show="open" x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                        class="absolute right-0 mt-2 w-64 bg-white border border-hairline-soft rounded-2xl py-2 shadow-xl z-50 text-xs overflow-hidden backdrop-blur-xl"
                        style="display: none;">

                        {{-- Profile Header Summary --}}
                        <div class="px-4 py-3 border-b border-hairline-soft bg-soft-cloud/50">
                            <div class="flex items-center gap-2.5 mb-1.5">
                                <div
                                    class="w-7 h-7 rounded-full bg-ink text-white font-bold text-[11px] flex items-center justify-center uppercase shrink-0">
                                    {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="font-bold text-ink truncate text-xs">{{ Auth::user()->name }}</p>
                                    <p class="text-[11px] text-mute truncate">{{ Auth::user()->email }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Menu Links --}}
                        <div class="py-1">
                            <a href="{{ route('admin.dashboard') }}"
                                class="flex items-center gap-2.5 px-4 py-2 hover:bg-soft-cloud text-ink transition {{ request()->routeIs('admin.dashboard') ? 'bg-soft-cloud font-bold' : 'text-neutral-700' }}">
                                <svg class="w-4 h-4 text-mute" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                <span>Dashboard Admin</span>
                            </a>

                            <a href="{{ route('admin.profile.edit') }}"
                                class="flex items-center gap-2.5 px-4 py-2 hover:bg-soft-cloud text-ink transition {{ request()->routeIs('admin.profile.*') ? 'bg-soft-cloud font-bold' : 'text-neutral-700' }}">
                                <svg class="w-4 h-4 text-mute" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <div class="flex-1 flex items-center justify-between">
                                    <span>Profil & Keamanan</span>
                                    @if (request()->routeIs('admin.profile.*'))
                                        <span class="text-ink font-bold">✓</span>
                                    @endif
                                </div>
                            </a>
                        </div>

                        <div class="border-t border-hairline-soft my-1"></div>

                        {{-- Logout Action --}}
                        <div class="px-1 py-1">
                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit"
                                    class="w-full flex items-center gap-2.5 px-3.5 py-2 rounded-xl text-left text-sale hover:bg-red-50 font-semibold transition cursor-pointer">
                                    <svg class="w-4 h-4 text-sale" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    <span>Keluar dari Sistem</span>
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </header>

            {{-- Main Body --}}
            <main class="flex-1 p-4 sm:p-6 lg:p-8 bg-canvas min-w-0 w-full max-w-full">
                @yield('content')
            </main>

            <footer
                class="border-t border-hairline-soft bg-white px-4 sm:px-6 lg:px-8 py-4 text-center text-xs text-mute font-medium shrink-0 w-full">
                &copy; {{ date('Y') }} NGIZAN APPAREL.
            </footer>

        </div>
    </div>

    {{-- Toastr.js Notifications --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "4000"
            };

            @if (session('success'))
                toastr.success("{{ session('success') }}");
            @endif
            @if (session('error'))
                toastr.error("{{ session('error') }}");
            @endif
            @if (session('info'))
                toastr.info("{{ session('info') }}");
            @endif
            @if (session('warning'))
                toastr.warning("{{ session('warning') }}");
            @endif
        });
    </script>
    @stack('scripts')
</body>

</html>
