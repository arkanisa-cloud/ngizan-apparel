@extends('layouts.customer')

@section('title', 'Katalog Lengkap Jersey · NGIZAN APPAREL')
@section('meta_description',
    'Jelajahi koleksi lengkap jersey sepak bola autentik, player issue, edisi retro, dan tim
    nasional dengan opsi kustomisasi sablon nama resmi.')

@php
    $activeCategory = request('category')
        ? $categories->first(fn($c) => $c->slug == request('category') || (string)$c->id == (string)request('category'))
        : null;

    $currentSort = request('sort', 'latest');
    $sortLabels = [
        'latest' => 'Rilis Terbaru',
        'price_low' => 'Harga: Terendah',
        'price_high' => 'Harga: Tertinggi',
        'name' => 'Nama: A - Z',
    ];
    $currentSortLabel = $sortLabels[$currentSort] ?? 'Rilis Terbaru';

    $activeFiltersCount = collect([request('category'), request('size'), request('type'), request('search')])->filter()->count();
@endphp

@section('content')
    <div class="bg-canvas min-h-screen" 
         x-data="{
             showSidebar: true,
             mobileFilterOpen: false
         }"
         x-init="$watch('mobileFilterOpen', value => document.body.classList.toggle('overflow-hidden', value))">

        {{-- Main Container --}}
        <div class="wrap pt-4 pb-16 sm:pt-8 sm:pb-24">

            {{-- ===== 1. EDITORIAL HEADER & TITLE ===== --}}
            <div class="border-b border-hairline-soft pb-4 sm:pb-6 mb-4 sm:mb-6">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3">
                    <div>
                        <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.2em] text-mute block mb-1">
                            Official Archive & Releases
                        </span>
                        <div class="flex items-center gap-3 flex-wrap">
                            <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight text-ink uppercase">
                                Katalog Ngizan
                            </h1>
                            <span class="text-[11px] sm:text-xs font-semibold px-2.5 py-0.5 rounded-full bg-soft-cloud border border-hairline text-mute">
                                {{ $products->total() }} Produk
                            </span>
                        </div>
                    </div>

                    {{-- Breadcrumb / Info Subtitle (Desktop) --}}
                    <div class="text-xs text-mute hidden sm:block">
                        <span>Koleksi jersey, apparel, dan arsip olahraga terkurasi</span>
                    </div>
                </div>

                {{-- Mobile Quick Horizontal Category Rail (1-Tap Switching) --}}
                <div class="lg:hidden mt-3.5 pt-3 border-t border-hairline-soft/60">
                    <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-1 -mx-2 px-2 select-none">
                        {{-- Semua Kategori --}}
                        <a href="{{ route('shop.index', request()->except('category', 'page')) }}"
                           class="shrink-0 text-xs font-semibold px-3.5 py-1.5 rounded-full transition-all duration-200 {{ !request('category') ? 'bg-ink text-white' : 'bg-soft-cloud text-ink border border-hairline hover:bg-neutral-200' }}">
                            Semua
                        </a>
                        @foreach ($categories as $cat)
                            <a href="{{ route('shop.index', array_merge(request()->except('page'), ['category' => $cat->slug])) }}"
                               class="shrink-0 text-xs font-semibold px-3.5 py-1.5 rounded-full transition-all duration-200 {{ request('category') === $cat->slug ? 'bg-ink text-white' : 'bg-soft-cloud text-ink border border-hairline hover:bg-neutral-200' }}">
                                {{ $cat->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ===== 2. ACTION TOOLBAR (Search, Sidebar Toggle, Sort) ===== --}}
            <div class="pb-3 mb-5 border-b border-hairline-soft/40 space-y-2.5 sm:space-y-0">
                
                {{-- Flex Container yang Adaptif Antara Mobile dan Desktop --}}
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 sm:gap-3">

                    {{-- Baris Kontrol Filter & Sort untuk Mobile (< 640px) / Kiri untuk Desktop --}}
                    <div class="flex items-center justify-between sm:justify-start gap-2 w-full sm:w-auto">
                        
                        {{-- Mobile Filter Drawer Trigger --}}
                        <button type="button" @click="mobileFilterOpen = true"
                            class="lg:hidden inline-flex items-center gap-2 px-3.5 py-2 bg-soft-cloud hover:bg-neutral-200 text-ink text-xs font-bold rounded-full border border-hairline transition shadow-2xs cursor-pointer select-none">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            <span>Filter</span>
                            @if ($activeFiltersCount > 0)
                                <span class="w-4 h-4 rounded-full bg-ink text-white text-[10px] flex items-center justify-center font-bold">
                                    {{ $activeFiltersCount }}
                                </span>
                            @endif
                        </button>

                        {{-- Desktop Toggle Sidebar Button --}}
                        <button type="button" @click="showSidebar = !showSidebar"
                            class="hidden lg:inline-flex items-center gap-2 px-4 py-2 bg-soft-cloud hover:bg-neutral-200 text-ink text-xs font-semibold rounded-full border border-hairline transition cursor-pointer select-none">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h7" />
                            </svg>
                            <span x-text="showSidebar ? 'Sembunyikan Filter' : 'Tampilkan Filter'">Sembunyikan Filter</span>
                        </button>

                        {{-- Mobile Sort Dropdown (Hanya Tampil di Mobile, Sebelah Filter) --}}
                        <div class="sm:hidden relative" x-data="{ sortOpenMobile: false }">
                            <button type="button" @click="sortOpenMobile = !sortOpenMobile"
                                class="inline-flex items-center gap-1.5 bg-soft-cloud hover:bg-neutral-200 border border-hairline text-ink text-xs font-semibold rounded-full px-3.5 py-2 transition focus:outline-none cursor-pointer shadow-2xs select-none">
                                <span class="font-bold">{{ $currentSortLabel }}</span>
                                <svg class="w-3 h-3 text-mute transition-transform duration-200"
                                    :class="sortOpenMobile ? 'rotate-180 text-ink' : ''" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            {{-- Mobile Sort Floating Menu --}}
                            <div x-show="sortOpenMobile" @click.away="sortOpenMobile = false" x-cloak
                                x-transition:enter="transition ease-out duration-150 transform"
                                x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100 transform"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                                class="absolute right-0 mt-2 w-52 bg-white/95 border border-hairline-soft rounded-2xl py-1.5 shadow-xl z-40 text-xs overflow-hidden backdrop-blur-xl">
                                <div class="px-3.5 py-1.5 border-b border-hairline-soft text-[10px] font-bold uppercase tracking-wider text-mute">
                                    Urutkan Berdasarkan
                                </div>
                                <div class="py-1">
                                    @foreach ($sortLabels as $val => $label)
                                        @php $isSelected = ($currentSort === $val); @endphp
                                        <a href="{{ route('shop.index', array_merge(request()->except('page', 'sort'), $val !== 'latest' ? ['sort' => $val] : [])) }}"
                                            class="flex items-center justify-between px-3.5 py-2.5 transition {{ $isSelected ? 'bg-soft-cloud text-ink font-bold' : 'text-neutral-600 hover:text-ink hover:bg-soft-cloud/70 font-medium' }}">
                                            <span>{{ $label }}</span>
                                            @if ($isSelected)
                                                <svg class="w-4 h-4 text-ink shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                </svg>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Form Pencarian Cepat & Dropdown Sort Desktop --}}
                    <div class="flex items-center gap-2.5 w-full sm:w-auto">

                        {{-- Quick Search Input (Lebar Penuh di Mobile, Standar di Desktop) --}}
                        <form method="GET" action="{{ route('shop.index') }}" class="relative w-full sm:w-auto flex-1 sm:flex-initial">
                            @if (request('category'))
                                <input type="hidden" name="category" value="{{ request('category') }}">
                            @endif
                            @if (request('size'))
                                <input type="hidden" name="size" value="{{ request('size') }}">
                            @endif
                            @if (request('type'))
                                <input type="hidden" name="type" value="{{ request('type') }}">
                            @endif
                            @if (request('sort'))
                                <input type="hidden" name="sort" value="{{ request('sort') }}">
                            @endif

                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari jersey, klub, negara..."
                                class="w-full sm:w-56 bg-soft-cloud border border-hairline px-3.5 py-2 pl-8 pr-7 rounded-full text-xs text-ink placeholder:text-stone focus:ring-1 focus:ring-ink focus:border-ink transition-all">
                            
                            <svg class="w-3.5 h-3.5 text-mute absolute left-2.5 top-2.5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            
                            @if (request('search'))
                                <a href="{{ route('shop.index', request()->except('search', 'page')) }}"
                                    class="absolute right-2.5 top-2 text-xs text-stone hover:text-ink font-bold">&times;</a>
                            @endif
                        </form>

                        {{-- Desktop Sort Dropdown (Custom Nike Luxury Pill) --}}
                        <div class="hidden sm:block relative" x-data="{ sortOpen: false }">
                            <button type="button" @click="sortOpen = !sortOpen"
                                class="inline-flex items-center gap-2 bg-soft-cloud hover:bg-neutral-200 border border-hairline text-ink text-xs font-semibold rounded-full px-4 py-2 transition focus:outline-none cursor-pointer shadow-2xs select-none">
                                <span class="text-mute font-normal">Urutkan:</span>
                                <span class="font-bold">{{ $currentSortLabel }}</span>
                                <svg class="w-3.5 h-3.5 text-mute transition-transform duration-200"
                                    :class="sortOpen ? 'rotate-180 text-ink' : ''" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            {{-- Floating Menu --}}
                            <div x-show="sortOpen" @click.away="sortOpen = false" x-cloak
                                x-transition:enter="transition ease-out duration-150 transform"
                                x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100 transform"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                                class="absolute right-0 mt-2 w-56 bg-white/95 border border-hairline-soft rounded-2xl py-1.5 shadow-xl z-40 text-xs overflow-hidden backdrop-blur-xl">

                                <div class="px-3.5 py-1.5 border-b border-hairline-soft text-[10px] font-bold uppercase tracking-wider text-mute">
                                    Urutkan Berdasarkan
                                </div>

                                <div class="py-1">
                                    @foreach ($sortLabels as $val => $label)
                                        @php $isSelected = ($currentSort === $val); @endphp
                                        <a href="{{ route('shop.index', array_merge(request()->except('page', 'sort'), $val !== 'latest' ? ['sort' => $val] : [])) }}"
                                            class="flex items-center justify-between px-3.5 py-2.5 transition {{ $isSelected ? 'bg-soft-cloud text-ink font-bold' : 'text-neutral-600 hover:text-ink hover:bg-soft-cloud/70 font-medium' }}">
                                            <span>{{ $label }}</span>
                                            @if ($isSelected)
                                                <svg class="w-4 h-4 text-ink shrink-0" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                </svg>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

            {{-- ===== 3. MAIN CATALOG GRID & SIDEBAR ===== --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-start">

                {{-- DESKTOP FILTER SIDEBAR (Col-3) --}}
                <aside x-show="showSidebar" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0"
                    class="hidden lg:block lg:col-span-3 space-y-6 select-none sticky top-24">

                    {{-- Kategori Filter Section --}}
                    <div class="bg-white p-5 rounded-2xl border border-hairline-soft space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="font-bold text-xs uppercase tracking-widest text-ink">Kategori</h3>
                            @if (request('category'))
                                <a href="{{ route('shop.index', request()->except('category', 'page')) }}"
                                    class="text-[11px] text-sale hover:underline font-medium">Reset</a>
                            @endif
                        </div>
                        <ul class="space-y-1 text-xs">
                            <li>
                                <a href="{{ route('shop.index', request()->except('category', 'page')) }}"
                                    class="flex items-center px-3 py-2 rounded-xl transition {{ !request('category') ? 'bg-ink text-white font-semibold' : 'text-mute hover:text-ink hover:bg-soft-cloud' }}">
                                    <span>Semua Kategori</span>
                                </a>
                            </li>
                            @foreach ($categories as $cat)
                                <li>
                                    <a href="{{ route('shop.index', array_merge(request()->except('page'), ['category' => $cat->slug])) }}"
                                        class="flex items-center px-3 py-2 rounded-xl transition {{ request('category') === $cat->slug ? 'bg-ink text-white font-semibold' : 'text-mute hover:text-ink hover:bg-soft-cloud' }}">
                                        <span>{{ $cat->name }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Ukuran Tersedia (Size Filter) --}}
                    <div class="bg-white p-5 rounded-2xl border border-hairline-soft space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="font-bold text-xs uppercase tracking-widest text-ink">Ukuran (Size)</h3>
                            @if (request('size'))
                                <a href="{{ route('shop.index', request()->except('size', 'page')) }}"
                                    class="text-[11px] text-sale hover:underline font-medium">Reset</a>
                            @endif
                        </div>
                        <div class="grid grid-cols-3 gap-1.5">
                            @foreach (['S', 'M', 'L', 'XL', 'XXL', '3XL'] as $s)
                                @php $isActiveSize = request('size') === $s; @endphp
                                <a href="{{ route('shop.index', array_merge(request()->except('page'), ['size' => $isActiveSize ? null : $s])) }}"
                                    class="h-9 rounded-xl border text-xs font-bold flex items-center justify-center transition {{ $isActiveSize ? 'bg-ink text-white border-ink shadow-xs' : 'bg-soft-cloud border-hairline text-ink hover:border-ink' }}">
                                    {{ $s }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Ngizan Advantage Banner Card --}}
                    <div class="bg-soft-cloud p-4 rounded-2xl border border-hairline space-y-2">
                        <div class="flex items-center gap-2 text-xs font-bold text-ink">
                            <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                            <span>Keuntungan Ngizan Apparel</span>
                        </div>
                        <ul class="text-[11px] text-mute space-y-1.5">
                            <li class="flex items-center gap-1.5">
                                <span class="text-emerald-600 font-bold">✓</span>
                                <span>Gratis Ongkir Rp 0 via J&T Express</span>
                            </li>
                            <li class="flex items-center gap-1.5">
                                <span class="text-amber-500 font-bold">✓</span>
                                <span>Member Premium Diskon 5% Otomatis</span>
                            </li>
                        </ul>
                    </div>

                    {{-- Global Reset Filter CTA --}}
                    @if ($activeFiltersCount > 0)
                        <div class="pt-1 text-center">
                            <a href="{{ route('shop.index') }}"
                                class="inline-flex items-center gap-1.5 text-xs font-bold text-sale hover:text-sale-deep py-2 px-4 rounded-full border border-sale/30 hover:border-sale transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                <span>Hapus Semua Filter</span>
                            </a>
                        </div>
                    @endif

                </aside>

                {{-- PRODUCT GRID CONTAINER (Col-9 atau Col-12 jika sidebar disembunyikan) --}}
                <main :class="showSidebar ? 'lg:col-span-9' : 'lg:col-span-12'" class="lg:col-span-9 space-y-5">

                    {{-- Active Filter Tags Row --}}
                    @if ($activeFiltersCount > 0)
                        <div class="flex flex-wrap items-center gap-2 bg-soft-cloud p-3 rounded-2xl border border-hairline-soft text-xs">
                            <span class="text-mute font-bold uppercase tracking-wider text-[10px]">Filter Aktif:</span>

                            @if (request('category'))
                                <span class="inline-flex items-center gap-1.5 bg-white border border-hairline px-3 py-1 rounded-full text-ink font-semibold shadow-2xs">
                                    <span>Kategori: <strong>{{ $activeCategory?->name ?? request('category') }}</strong></span>
                                    <a href="{{ route('shop.index', request()->except('category', 'page')) }}"
                                        class="text-stone hover:text-sale font-bold ml-1">&times;</a>
                                </span>
                            @endif

                            @if (request('size'))
                                <span class="inline-flex items-center gap-1.5 bg-white border border-hairline px-3 py-1 rounded-full text-ink font-semibold shadow-2xs">
                                    <span>Ukuran: <strong>{{ request('size') }}</strong></span>
                                    <a href="{{ route('shop.index', request()->except('size', 'page')) }}"
                                        class="text-stone hover:text-sale font-bold ml-1">&times;</a>
                                </span>
                            @endif

                            @if (request('type'))
                                <span class="inline-flex items-center gap-1.5 bg-white border border-hairline px-3 py-1 rounded-full text-ink font-semibold shadow-2xs">
                                    <span>Tipe: <strong>{{ request('type') }}</strong></span>
                                    <a href="{{ route('shop.index', request()->except('type', 'page')) }}"
                                        class="text-stone hover:text-sale font-bold ml-1">&times;</a>
                                </span>
                            @endif

                            @if (request('search'))
                                <span class="inline-flex items-center gap-1.5 bg-white border border-hairline px-3 py-1 rounded-full text-ink font-semibold shadow-2xs">
                                    <span>Cari: "<strong>{{ request('search') }}</strong>"</span>
                                    <a href="{{ route('shop.index', request()->except('search', 'page')) }}"
                                        class="text-stone hover:text-sale font-bold ml-1">&times;</a>
                                </span>
                            @endif

                            <a href="{{ route('shop.index') }}"
                                class="text-[11px] font-bold text-sale hover:underline ml-auto pl-2">
                                Hapus Semua
                            </a>
                        </div>
                    @endif

                    {{-- Product Cards Grid (Responsif 2 Kolom Mobile, 3 Kolom Tablet/Desktop, 4 Kolom saat Sidebar Tersembunyi) --}}
                    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 gap-3 sm:gap-4 md:gap-6"
                        :class="{ 'xl:grid-cols-4': !showSidebar }">
                        @forelse($products as $product)
                            <x-product-card :product="$product" />
                        @empty
                            <div class="col-span-full text-center py-16 sm:py-20 bg-soft-cloud rounded-2xl border border-hairline space-y-4 px-4">
                                <div class="w-14 h-14 sm:w-16 sm:h-16 bg-white rounded-full flex items-center justify-center mx-auto shadow-2xs text-mute">
                                    <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                </div>
                                <div class="space-y-1">
                                    <h3 class="font-bold text-sm sm:text-base text-ink uppercase tracking-wide">
                                        Tidak Ada {{ $activeCategory?->name ?? 'Produk' }} Ditemukan
                                    </h3>
                                    <p class="text-xs text-mute max-w-sm mx-auto">
                                        Kombinasi filter atau kata kunci pencarian tidak cocok dengan koleksi saat ini. Coba
                                        reset filter untuk melihat koleksi lainnya.
                                    </p>
                                </div>
                                <div class="pt-2">
                                    <a href="{{ route('shop.index') }}"
                                        class="inline-block bg-ink hover:bg-black text-white px-6 py-2.5 text-xs font-bold uppercase tracking-widest rounded-full transition shadow-2xs">
                                        Lihat Semua Produk
                                    </a>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    {{-- Clean Pagination Links --}}
                    @if ($products->hasPages())
                        <div class="pt-8 border-t border-hairline-soft flex justify-center">
                            {{ $products->links() }}
                        </div>
                    @endif

                </main>

            </div>

        </div>

        {{-- ===== 4. MOBILE OFF-CANVAS FILTER DRAWER ===== --}}
        <div x-show="mobileFilterOpen" x-cloak class="fixed inset-0 z-50 overflow-hidden lg:hidden"
            aria-labelledby="slide-over-title" role="dialog" aria-modal="true">

            {{-- Background Backdrop Blur --}}
            <div x-show="mobileFilterOpen" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" @click="mobileFilterOpen = false"
                class="fixed inset-0 bg-black/60 backdrop-blur-xs transition-opacity"></div>

            <div class="fixed inset-y-0 right-0 max-w-full flex pl-8 sm:pl-10">
                <div x-show="mobileFilterOpen" x-transition:enter="transform transition ease-in-out duration-300"
                    x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-300"
                    x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                    class="w-[85vw] max-w-sm bg-white shadow-2xl flex flex-col justify-between">

                    {{-- Drawer Header --}}
                    <div class="p-4 sm:p-5 border-b border-hairline-soft flex items-center justify-between">
                        <div>
                            <h2 class="font-bold text-sm sm:text-base uppercase tracking-tight text-ink" id="slide-over-title">
                                Filter Produk
                            </h2>
                            <p class="text-[11px] text-mute">Sesuaikan pencarian jersey impian Anda</p>
                        </div>
                        <button type="button" @click="mobileFilterOpen = false"
                            class="w-8 h-8 rounded-full bg-soft-cloud flex items-center justify-center text-ink hover:bg-neutral-200 transition cursor-pointer">
                            <span class="sr-only">Tutup filter</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Drawer Content (Scrollable) --}}
                    <div class="p-4 sm:p-5 overflow-y-auto space-y-6 flex-1">

                        {{-- Kategori --}}
                        <div>
                            <div class="flex items-center justify-between mb-2.5">
                                <h3 class="font-bold text-xs uppercase tracking-widest text-ink">Kategori</h3>
                                @if (request('category'))
                                    <a href="{{ route('shop.index', request()->except('category', 'page')) }}"
                                        class="text-[11px] text-sale font-medium hover:underline">Reset</a>
                                @endif
                            </div>
                            <div class="space-y-1 text-xs">
                                <a href="{{ route('shop.index', request()->except('category', 'page')) }}"
                                    class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ !request('category') ? 'bg-ink text-white font-semibold' : 'bg-soft-cloud text-ink font-medium' }}">
                                    <span>Semua Kategori</span>
                                    @if (!request('category'))
                                        <span class="text-white text-xs">✓</span>
                                    @endif
                                </a>
                                @foreach ($categories as $cat)
                                    <a href="{{ route('shop.index', array_merge(request()->except('page'), ['category' => $cat->slug])) }}"
                                        class="flex items-center justify-between px-3.5 py-2.5 rounded-xl transition {{ request('category') === $cat->slug ? 'bg-ink text-white font-semibold' : 'bg-soft-cloud text-ink font-medium' }}">
                                        <span>{{ $cat->name }}</span>
                                        @if (request('category') === $cat->slug)
                                            <span class="text-white text-xs">✓</span>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        {{-- Ukuran --}}
                        <div class="border-t border-hairline-soft pt-5">
                            <div class="flex items-center justify-between mb-2.5">
                                <h3 class="font-bold text-xs uppercase tracking-widest text-ink">Ukuran (Size)</h3>
                                @if (request('size'))
                                    <a href="{{ route('shop.index', request()->except('size', 'page')) }}"
                                        class="text-[11px] text-sale font-medium hover:underline">Reset</a>
                                @endif
                            </div>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach (['S', 'M', 'L', 'XL', 'XXL', '3XL'] as $s)
                                    @php $isActiveSize = request('size') === $s; @endphp
                                    <a href="{{ route('shop.index', array_merge(request()->except('page'), ['size' => $isActiveSize ? null : $s])) }}"
                                        class="h-10 rounded-xl border text-xs font-bold flex items-center justify-center transition {{ $isActiveSize ? 'bg-ink text-white border-ink shadow-xs' : 'bg-soft-cloud border-hairline text-ink hover:border-ink' }}">
                                        {{ $s }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                    </div>

                    {{-- Drawer Footer Fixed CTA --}}
                    <div class="p-4 sm:p-5 border-t border-hairline-soft bg-white space-y-2">
                        <button type="button" @click="mobileFilterOpen = false"
                            class="w-full bg-ink hover:bg-black text-white py-3 text-xs font-bold uppercase tracking-widest rounded-full transition shadow-md cursor-pointer">
                            Terapkan Filter
                        </button>
                        @if ($activeFiltersCount > 0)
                            <a href="{{ route('shop.index') }}"
                                class="block text-center text-xs font-bold text-sale hover:underline py-1">
                                Reset Semua Filter
                            </a>
                        @endif
                    </div>

                </div>
            </div>
        </div>

    </div>
@endsection
