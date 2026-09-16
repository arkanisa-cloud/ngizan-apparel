<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Backoffice · NGIZAN APPAREL')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700;800;900&family=Bebas+Neue&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Toastr.js CSS & JS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full font-sans antialiased text-slate-900 bg-slate-100" x-data="{ sidebarOpen: false }">
    <div class="flex h-full">
        
        {{-- ===== SIDEBAR (Desktop) ===== --}}
        <aside class="hidden lg:flex lg:w-64 lg:flex-col lg:fixed lg:inset-y-0 z-40 shadow-xl">
            <div class="flex flex-col flex-grow bg-[#101010] text-[#EFEDE8] overflow-y-auto border-r border-neutral-800">
                
                {{-- Brand Header --}}
                <div class="flex items-center gap-3 px-6 py-5 border-b border-neutral-800">
                    <div class="flex items-center justify-center w-8 h-8 rounded bg-cyan-600 font-display font-black text-white text-base">
                        N
                    </div>
                    <div>
                        <span class="font-display font-black text-sm tracking-widest text-white uppercase block">NGIZAN</span>
                        <span class="text-[9.5px] uppercase font-bold text-neutral-400 tracking-wider">Backoffice Admin</span>
                    </div>
                </div>

                {{-- Navigation Links --}}
                <nav class="flex-1 px-4 py-5 space-y-1.5 text-xs font-semibold">
                    
                    {{-- Dashboard --}}
                    <a href="{{ route('admin.dashboard') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('admin.dashboard') ? 'bg-cyan-600 text-white font-bold' : 'text-neutral-300 hover:bg-neutral-900 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        <span>Dashboard</span>
                    </a>

                    {{-- Section: Master Data --}}
                    <div class="pt-4 pb-1">
                        <p class="px-3 text-[10px] font-bold uppercase tracking-widest text-neutral-500">Master Data</p>
                    </div>
                    <a href="{{ route('admin.products.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('admin.products.*') ? 'bg-cyan-600 text-white font-bold' : 'text-neutral-300 hover:bg-neutral-900 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <span>Katalog Jersey</span>
                    </a>
                    <a href="{{ route('admin.categories.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('admin.categories.*') ? 'bg-cyan-600 text-white font-bold' : 'text-neutral-300 hover:bg-neutral-900 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z"/></svg>
                        <span>Kategori Edisi</span>
                    </a>

                    {{-- Section: Logistik & Pesanan --}}
                    <div class="pt-4 pb-1">
                        <p class="px-3 text-[10px] font-bold uppercase tracking-widest text-neutral-500">Transaksi & Logistik</p>
                    </div>
                    <a href="{{ route('admin.orders.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('admin.orders.*') ? 'bg-cyan-600 text-white font-bold' : 'text-neutral-300 hover:bg-neutral-900 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        <span>Pesanan Masuk</span>
                    </a>

                    {{-- Section: Gudang & Inventori --}}
                    <div class="pt-4 pb-1">
                        <p class="px-3 text-[10px] font-bold uppercase tracking-widest text-neutral-500">Gudang & Inventori</p>
                    </div>
                    <a href="{{ route('admin.stock-ins.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('admin.stock-ins.*') ? 'bg-cyan-600 text-white font-bold' : 'text-neutral-300 hover:bg-neutral-900 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        <span>Restock Masuk</span>
                    </a>
                    <a href="{{ route('admin.stock-outs.index') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('admin.stock-outs.*') ? 'bg-cyan-600 text-white font-bold' : 'text-neutral-300 hover:bg-neutral-900 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span>Penyesuaian Keluar</span>
                    </a>

                    {{-- Section: Laporan Finansial --}}
                    <div class="pt-4 pb-1">
                        <p class="px-3 text-[10px] font-bold uppercase tracking-widest text-neutral-500">Laporan & Keuangan</p>
                    </div>
                    <a href="{{ route('admin.reports.sales') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('admin.reports.sales') ? 'bg-cyan-600 text-white font-bold' : 'text-neutral-300 hover:bg-neutral-900 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Laporan Penjualan</span>
                    </a>
                    <a href="{{ route('admin.reports.stock') }}"
                       class="flex items-center gap-3 px-3 py-2.5 rounded transition {{ request()->routeIs('admin.reports.stock') ? 'bg-cyan-600 text-white font-bold' : 'text-neutral-300 hover:bg-neutral-900 hover:text-white' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        <span>Audit Stok Gudang</span>
                    </a>
                </nav>

                {{-- Storefront Quick Link --}}
                <div class="p-4 border-t border-neutral-800">
                    <a href="{{ route('home') }}" target="_blank" class="flex items-center justify-center gap-2 p-2 bg-neutral-900 hover:bg-neutral-800 text-neutral-300 rounded text-xs transition">
                        <span>Lihat Toko Online</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </div>

            </div>
        </aside>

        {{-- ===== MAIN CONTENT AREA ===== --}}
        <div class="flex flex-col flex-1 lg:pl-64">
            
            {{-- Top Navigation Header --}}
            <header class="sticky top-0 z-30 flex items-center justify-between h-16 px-4 bg-white border-b border-slate-200 shadow-sm sm:px-6 lg:px-8">
                <button @click="sidebarOpen = true" class="lg:hidden text-slate-500 hover:text-slate-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <div class="hidden lg:block">
                    <h2 class="text-xs font-bold text-slate-500 uppercase tracking-widest">{{ now()->translatedFormat('l, d F Y') }}</h2>
                </div>

                {{-- Admin Profile & Logout --}}
                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <span class="text-xs font-bold text-slate-900 block">{{ Auth::user()->name }}</span>
                        <span class="text-[10px] text-cyan-600 uppercase font-semibold">Administrator</span>
                    </div>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 bg-slate-100 hover:bg-rose-50 hover:text-rose-600 rounded text-xs font-bold text-slate-700 transition flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            <span>Keluar</span>
                        </button>
                    </form>
                </div>
            </header>

            {{-- Main Body --}}
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>

            <footer class="border-t border-slate-200 bg-white px-6 py-4 text-center text-xs text-slate-400">
                &copy; {{ date('Y') }} NGIZAN APPAREL. Backoffice Management System.
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

            @if(session('success')) toastr.success("{{ session('success') }}"); @endif
            @if(session('error')) toastr.error("{{ session('error') }}"); @endif
            @if(session('info')) toastr.info("{{ session('info') }}"); @endif
            @if(session('warning')) toastr.warning("{{ session('warning') }}"); @endif
        });
    </script>
    @stack('scripts')
</body>
</html>
