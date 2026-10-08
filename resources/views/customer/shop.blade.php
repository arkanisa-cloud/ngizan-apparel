@extends('layouts.customer')

@section('title', 'Katalog Lengkap Jersey · NGIZAN APPAREL')
@section('meta_description',
    'Jelajahi koleksi lengkap jersey sepak bola autentik, player issue, edisi retro, dan tim
    nasional dengan opsi kustomisasi sablon nama resmi.')

@php
    $activeCategory = request('category')
        ? $categories->first(fn($c) => $c->slug == request('category') || (string)$c->id == (string)request('category'))
        : null;
@endphp

@section('content')
    <div class="bg-canvas min-h-screen" x-data="{
        showSidebar: true,
        mobileFilterOpen: false
    }">

        {{-- Main Container --}}
        <div class="wrap pt-6 pb-16 sm:pt-10 sm:pb-24">

            {{-- ===== 1. EDITORIAL HEADER & TITLE ===== --}}
            <div class="border-b border-hairline-soft pb-6 sm:pb-8 mb-6 sm:mb-8">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <div>
                        <span
                            class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.2em] text-neutral-400 block mb-1.5">
                            Official Archive & Releases
                        </span>
                        <div class="flex items-center gap-3">
                            <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight text-ink uppercase">
                                Katalog Ngizan
                            </h1>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== 2. ACTION TOOLBAR (Search, Sidebar Toggle, Sort) ===== --}}
            <div class="flex flex-wrap items-center justify-between gap-3 pb-4 mb-6">

                {{-- Kiri: Sidebar Toggle (Desktop) & Filter Trigger (Mobile) --}}
                <div class="flex items-center gap-2">
                    {{-- Mobile Filter Button --}}
                    <button type="button" @click="mobileFilterOpen = true"
                        class="lg:hidden inline-flex items-center gap-2 px-4 py-2 bg-soft-cloud hover:bg-neutral-200 text-ink text-xs font-bold rounded-full border border-hairline transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <span>Filter</span>
                    </button>

                    {{-- Desktop Toggle Sidebar Button --}}
                    <button type="button" @click="showSidebar = !showSidebar"
                        class="hidden lg:inline-flex items-center gap-2 px-4 py-2 bg-soft-cloud hover:bg-neutral-200 text-ink text-xs font-semibold rounded-full border border-hairline transition">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                        <span x-text="showSidebar ? 'Sembunyikan Filter' : 'Tampilkan Filter'">Sembunyikan Filter</span>
                    </button>
                </div>

                {{-- Kanan: Form Pencarian & Sort Dropdown --}}
                <div class="flex items-center gap-2.5 ml-auto">

                    {{-- Quick Search Input --}}
                    <form method="GET" action="{{ route('shop.index') }}" class="relative">
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
                            placeholder="Cari klub, negara, nama..."
                            class="w-36 sm:w-56 bg-soft-cloud border border-hairline px-3.5 py-1.5 pl-8 rounded-full text-xs text-ink placeholder:text-stone focus:ring-1 focus:ring-ink focus:border-ink transition-all">
                        <svg class="w-3.5 h-3.5 text-mute absolute left-2.5 top-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        @if (request('search'))
                            <a href="{{ route('shop.index', request()->except('search', 'page')) }}"
                                class="absolute right-2.5 top-1.5 text-xs text-stone hover:text-ink font-bold">&times;</a>
                        @endif
                    </form>

                    {{-- Sort Dropdown --}}
                    <form method="GET" action="{{ route('shop.index') }}" class="flex items-center">
                        @if (request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        @if (request('size'))
                            <input type="hidden" name="size" value="{{ request('size') }}">
                        @endif
                        @if (request('type'))
                            <input type="hidden" name="type" value="{{ request('type') }}">
                        @endif
                        @if (request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif

                        <div class="relative">
                            <select name="sort" id="sort" onchange="this.form.submit()"
                                class="appearance-none bg-soft-cloud border border-hairline text-ink text-xs font-semibold rounded-full pl-4 pr-8 py-1.5 focus:ring-1 focus:ring-ink focus:border-ink cursor-pointer">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Rilis Terbaru
                                </option>
                                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga:
                                    Rendah ke Tinggi</option>
                                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga:
                                    Tinggi ke Rendah</option>
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nama A - Z
                                </option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-ink">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ===== 3. MAIN CATALOG GRID & SIDEBAR ===== --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                {{-- DESKTOP FILTER SIDEBAR (Col-3) --}}
                <aside x-show="showSidebar" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0"
                    class="hidden lg:block lg:col-span-3 space-y-6 select-none">

                    {{-- Kategori Filter Section --}}
                    <div class="bg-white p-5 rounded-2xl border border-hairline-soft space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="font-bold text-xs uppercase tracking-widest text-ink">Kategori</h3>
                            @if (request('category'))
                                <a href="{{ route('shop.index', request()->except('category', 'page')) }}"
                                    class="text-[11px] text-sale hover:underline font-medium">Reset</a>
                            @endif
                        </div>
                        <ul class="space-y-1.5 text-xs">
                            <li>
                                <a href="{{ route('shop.index', request()->except('category', 'page')) }}"
                                    class="flex items-center px-2.5 py-1.5 rounded-lg transition {{ !request('category') ? 'bg-ink text-white font-semibold' : 'text-mute hover:text-ink hover:bg-soft-cloud' }}">
                                    <span>Semua Kategori</span>
                                </a>
                            </li>
                            @foreach ($categories as $cat)
                                <li>
                                    <a href="{{ route('shop.index', array_merge(request()->except('page'), ['category' => $cat->slug])) }}"
                                        class="flex items-center px-2.5 py-1.5 rounded-lg transition {{ request('category') === $cat->slug ? 'bg-ink text-white font-semibold' : 'text-mute hover:text-ink hover:bg-soft-cloud' }}">
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
                            <span class="w-2 h-2 rounded-full bg-premium-gold"></span>
                            <span>Keuntungan Ngizan Apparel</span>
                        </div>
                        <ul class="text-[11px] text-mute space-y-1">
                            <li class="flex items-center gap-1.5">
                                <span class="text-success font-bold">✓</span>
                                <span>Gratis Ongkir Rp 0 via J&T Express</span>
                            </li>
                            <li class="flex items-center gap-1.5">
                                <span class="text-premium-gold font-bold">✓</span>
                                <span>Member Premium Diskon 5% Otomatis</span>
                            </li>
                        </ul>
                    </div>

                    {{-- Global Reset Filter CTA --}}
                    @if (request()->hasAny(['category', 'search', 'size', 'type', 'sort']))
                        <div class="pt-2 text-center">
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
                <main :class="showSidebar ? 'lg:col-span-9' : 'lg:col-span-12'" class="lg:col-span-9 space-y-6">

                    {{-- Active Filter Tags Row --}}
                    @if (request()->hasAny(['category', 'search', 'size', 'type']))
                        <div
                            class="flex flex-wrap items-center gap-2 bg-soft-cloud p-3 rounded-xl border border-hairline-soft text-xs">
                            <span class="text-mute font-bold uppercase tracking-wider text-[10px]">Filter Aktif:</span>

                            @if (request('category'))
                                <span
                                    class="inline-flex items-center gap-1.5 bg-white border border-hairline px-3 py-1 rounded-full text-ink font-semibold shadow-xs">
                                    <span>Kategori: <strong>{{ $activeCategory?->name ?? request('category') }}</strong></span>
                                    <a href="{{ route('shop.index', request()->except('category', 'page')) }}"
                                        class="text-stone hover:text-sale font-bold ml-1">&times;</a>
                                </span>
                            @endif

                            @if (request('size'))
                                <span
                                    class="inline-flex items-center gap-1.5 bg-white border border-hairline px-3 py-1 rounded-full text-ink font-semibold shadow-xs">
                                    <span>Ukuran: <strong>{{ request('size') }}</strong></span>
                                    <a href="{{ route('shop.index', request()->except('size', 'page')) }}"
                                        class="text-stone hover:text-sale font-bold ml-1">&times;</a>
                                </span>
                            @endif

                            @if (request('type'))
                                <span
                                    class="inline-flex items-center gap-1.5 bg-white border border-hairline px-3 py-1 rounded-full text-ink font-semibold shadow-xs">
                                    <span>Tipe: <strong>{{ request('type') }}</strong></span>
                                    <a href="{{ route('shop.index', request()->except('type', 'page')) }}"
                                        class="text-stone hover:text-sale font-bold ml-1">&times;</a>
                                </span>
                            @endif

                            @if (request('search'))
                                <span
                                    class="inline-flex items-center gap-1.5 bg-white border border-hairline px-3 py-1 rounded-full text-ink font-semibold shadow-xs">
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

                    {{-- Product Cards Grid --}}
                    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 gap-3 sm:gap-4 md:gap-6"
                        :class="{ 'xl:grid-cols-4': !showSidebar }">
                        @forelse($products as $product)
                            <x-product-card :product="$product" />
                        @empty
                            <div
                                class="col-span-full text-center py-20 bg-soft-cloud rounded-2xl border border-hairline space-y-4 px-4">
                                <div
                                    class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto shadow-xs text-mute">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                    </svg>
                                </div>
                                <div class="space-y-1">
                                    <h3 class="font-bold text-base text-ink uppercase tracking-wide">Tidak Ada {{ $activeCategory?->name ?? 'Produk' }}
                                        Ditemukan</h3>
                                    <p class="text-xs text-mute max-w-sm mx-auto">
                                        Kombinasi filter atau kata kunci pencarian tidak cocok dengan koleksi saat ini. Coba
                                        reset filter untuk melihat koleksi lainnya.
                                    </p>
                                </div>
                                <div class="pt-2">
                                    <a href="{{ route('shop.index') }}"
                                        class="inline-block bg-ink hover:bg-black text-white px-6 py-2.5 text-xs font-bold uppercase tracking-widest rounded-full transition shadow-xs">
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
                class="fixed inset-0 bg-black/50 backdrop-blur-xs transition-opacity"></div>

            <div class="fixed inset-y-0 right-0 max-w-full flex pl-10">
                <div x-show="mobileFilterOpen" x-transition:enter="transform transition ease-in-out duration-300"
                    x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-300"
                    x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
                    class="w-screen max-w-md bg-white shadow-2xl flex flex-col justify-between">

                    {{-- Drawer Header --}}
                    <div class="p-5 border-b border-hairline-soft flex items-center justify-between">
                        <div>
                            <h2 class="font-bold text-base uppercase tracking-tight text-ink" id="slide-over-title">Filter
                                Produk</h2>
                            <p class="text-[11px] text-mute">Sesuaikan pencarian jersey impian Anda</p>
                        </div>
                        <button type="button" @click="mobileFilterOpen = false"
                            class="w-8 h-8 rounded-full bg-soft-cloud flex items-center justify-center text-ink hover:bg-neutral-200">
                            <span class="sr-only">Close panel</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Drawer Content (Scrollable) --}}
                    <div class="p-5 overflow-y-auto space-y-6 flex-1">

                        {{-- Kategori --}}
                        <div>
                            <h3 class="font-bold text-xs uppercase tracking-widest text-ink mb-3">Kategori</h3>
                            <div class="space-y-1.5 text-xs">
                                <a href="{{ route('shop.index', request()->except('category', 'page')) }}"
                                    class="flex items-center px-3 py-2 rounded-xl transition {{ !request('category') ? 'bg-ink text-white font-semibold' : 'bg-soft-cloud text-ink' }}">
                                    <span>Semua Kategori</span>
                                </a>
                                @foreach ($categories as $cat)
                                    <a href="{{ route('shop.index', array_merge(request()->except('page'), ['category' => $cat->slug])) }}"
                                        class="flex items-center px-3 py-2 rounded-xl transition {{ request('category') === $cat->slug ? 'bg-ink text-white font-semibold' : 'bg-soft-cloud text-ink' }}">
                                        <span>{{ $cat->name }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        {{-- Ukuran --}}
                        <div class="border-t border-hairline-soft pt-5">
                            <h3 class="font-bold text-xs uppercase tracking-widest text-ink mb-3">Ukuran (Size)</h3>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach (['S', 'M', 'L', 'XL', 'XXL', '3XL'] as $s)
                                    @php $isActiveSize = request('size') === $s; @endphp
                                    <a href="{{ route('shop.index', array_merge(request()->except('page'), ['size' => $isActiveSize ? null : $s])) }}"
                                        class="h-10 rounded-xl border text-xs font-bold flex items-center justify-center transition {{ $isActiveSize ? 'bg-ink text-white border-ink' : 'bg-soft-cloud border-hairline text-ink' }}">
                                        {{ $s }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                    </div>

                    {{-- Drawer Footer Fixed CTA --}}
                    <div class="p-5 border-t border-hairline-soft bg-white space-y-2">
                        <button type="button" @click="mobileFilterOpen = false"
                            class="w-full bg-ink hover:bg-black text-white py-3 text-xs font-bold uppercase tracking-widest rounded-full transition shadow-md">
                            Terapkan Filter
                        </button>
                        @if (request()->hasAny(['category', 'search', 'size', 'type']))
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
