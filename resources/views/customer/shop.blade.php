@extends('layouts.customer')

@section('title', 'Katalog Lengkap Jersey · NGIZAN APPAREL')
@section('meta_description', 'Jelajahi koleksi lengkap jersey sepak bola autentik, edisi retro, dan tim nasional dengan opsi kustomisasi sablon nama resmi.')

@section('content')
<div class="py-8 bg-canvas" x-data="{ mobileFilter: false }">
    <div class="wrap">
        
        {{-- Sub-Nav Strip & Header --}}
        <div class="flex flex-col sm:flex-row sm:items-baseline justify-between border-b border-hairline-soft pb-5 mb-8 gap-4">
            <div>
                <span class="text-xs font-medium uppercase tracking-widest text-mute block mb-1">Arsip & Koleksi Resmi</span>
                <h1 class="text-2xl sm:text-3xl font-medium tracking-tight text-ink">
                    Katalog Jersey
                </h1>
            </div>

            {{-- Controls: Filter Toggle (Mobile) & Sort Dropdown --}}
            <div class="flex items-center gap-3">
                <button type="button" @click="mobileFilter = !mobileFilter" class="lg:hidden filter-chip text-xs">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    <span>Filter</span>
                </button>

                <form method="GET" action="{{ route('shop.index') }}" class="flex items-center gap-2 text-xs">
                    @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                    @if(request('size')) <input type="hidden" name="size" value="{{ request('size') }}"> @endif
                    @if(request('type')) <input type="hidden" name="type" value="{{ request('type') }}"> @endif
                    @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif

                    <label for="sort" class="text-mute font-medium hidden sm:inline">Urutkan:</label>
                    <select name="sort" id="sort" onchange="this.form.submit()" class="bg-soft-cloud border border-hairline text-ink text-xs rounded-full px-4 py-1.5 focus:ring-1 focus:ring-ink focus:border-ink">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Rilis Terbaru</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga: Rendah ke Tinggi</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga: Tinggi ke Rendah</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nama A - Z</option>
                    </select>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- ===== 1. FILTER SIDEBAR (3 COLS) — NIKE EDITORIAL RAIL ===== --}}
            <aside :class="mobileFilter ? 'block fixed inset-0 z-50 bg-white p-6 overflow-y-auto' : 'hidden lg:block'"
                   class="lg:col-span-3 space-y-6">
                
                <div class="flex items-center justify-between lg:hidden pb-4 border-b border-hairline-soft">
                    <h2 class="font-medium text-lg text-ink">Filter Produk</h2>
                    <button type="button" @click="mobileFilter = false" class="text-2xl font-bold text-ink">&times;</button>
                </div>

                {{-- Search Box --}}
                <div>
                    <h3 class="font-medium text-sm text-ink mb-2">Pencarian</h3>
                    <form method="GET" action="{{ route('shop.index') }}">
                        @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari klub / tim..." 
                                   class="w-full bg-soft-cloud border border-hairline px-4 py-2 pl-9 rounded-full text-xs text-ink focus:ring-1 focus:ring-ink focus:border-ink">
                            <svg class="w-3.5 h-3.5 text-mute absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </form>
                </div>

                {{-- Kategori --}}
                <div class="border-t border-hairline-soft pt-5">
                    <h3 class="font-medium text-sm text-ink mb-3">Kategori</h3>
                    <ul class="space-y-2 text-xs">
                        <li>
                            <a href="{{ route('shop.index', request()->except('category', 'page')) }}" 
                               class="flex justify-between items-center py-0.5 {{ !request('category') ? 'font-medium text-ink underline' : 'text-mute hover:text-ink' }}">
                                <span>Semua Kategori</span>
                            </a>
                        </li>
                        @foreach($categories as $cat)
                            <li>
                                <a href="{{ route('shop.index', array_merge(request()->except('page'), ['category' => $cat->slug])) }}" 
                                   class="flex justify-between items-center py-0.5 {{ request('category') === $cat->slug ? 'font-medium text-ink underline' : 'text-mute hover:text-ink' }}">
                                    <span>{{ $cat->name }}</span>
                                    <span class="text-[11px] text-mute font-mono">({{ $cat->products_count }})</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Tipe Jersey --}}
                <div class="border-t border-hairline-soft pt-5">
                    <h3 class="font-medium text-sm text-ink mb-3">Tipe Edisi</h3>
                    <div class="space-y-2 text-xs">
                        @foreach(['Player Issue', 'Fans Issue', 'Retro'] as $t)
                            <a href="{{ route('shop.index', array_merge(request()->except('page'), ['type' => request('type') === $t ? null : $t])) }}"
                               class="flex items-center gap-2.5 py-0.5 {{ request('type') === $t ? 'font-medium text-ink' : 'text-mute hover:text-ink' }}">
                                <span class="w-4 h-4 rounded-full border flex items-center justify-center text-[10px] {{ request('type') === $t ? 'bg-ink border-ink text-white' : 'border-hairline' }}">
                                    @if(request('type') === $t) &#10003; @endif
                                </span>
                                <span>{{ $t }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Filter Ukuran (Size) --}}
                <div class="border-t border-hairline-soft pt-5">
                    <h3 class="font-medium text-sm text-ink mb-3">Ukuran Tersedia</h3>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach(['S', 'M', 'L', 'XL', 'XXL', '3XL'] as $s)
                            <a href="{{ route('shop.index', array_merge(request()->except('page'), ['size' => request('size') === $s ? null : $s])) }}"
                               class="w-10 h-9 rounded-full border text-xs font-medium flex items-center justify-center transition {{ request('size') === $s ? 'bg-ink text-white border-ink' : 'border-hairline text-ink hover:border-ink' }}">
                                {{ $s }}
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Reset Filter Button --}}
                @if(request()->hasAny(['category', 'search', 'size', 'type', 'sort']))
                    <div class="border-t border-hairline-soft pt-4">
                        <a href="{{ route('shop.index') }}" class="text-xs font-medium text-sale hover:underline block">
                            ✕ Hapus Semua Filter
                        </a>
                    </div>
                @endif

                <div class="lg:hidden pt-4">
                    <button type="button" @click="mobileFilter = false" class="btn-primary w-full text-center py-3 text-xs font-medium rounded-full">
                        Terapkan Filter
                    </button>
                </div>

            </aside>

            {{-- ===== 2. PRODUCT GRID & PAGINATION (9 COLS) ===== --}}
            <div class="lg:col-span-9 space-y-8">
                
                {{-- Active Filters Chips --}}
                @if(request()->hasAny(['category', 'search', 'size', 'type']))
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <span class="text-mute font-medium">Filter Aktif:</span>
                        
                        @if(request('category'))
                            <span class="inline-flex items-center gap-1.5 bg-soft-cloud border border-hairline px-3 py-1 rounded-full text-ink font-medium">
                                Kategori: {{ request('category') }}
                                <a href="{{ route('shop.index', request()->except('category', 'page')) }}" class="text-mute hover:text-ink font-bold">&times;</a>
                            </span>
                        @endif

                        @if(request('size'))
                            <span class="inline-flex items-center gap-1.5 bg-soft-cloud border border-hairline px-3 py-1 rounded-full text-ink font-medium">
                                Ukuran: {{ request('size') }}
                                <a href="{{ route('shop.index', request()->except('size', 'page')) }}" class="text-mute hover:text-ink font-bold">&times;</a>
                            </span>
                        @endif

                        @if(request('type'))
                            <span class="inline-flex items-center gap-1.5 bg-soft-cloud border border-hairline px-3 py-1 rounded-full text-ink font-medium">
                                Tipe: {{ request('type') }}
                                <a href="{{ route('shop.index', request()->except('type', 'page')) }}" class="text-mute hover:text-ink font-bold">&times;</a>
                            </span>
                        @endif

                        @if(request('search'))
                            <span class="inline-flex items-center gap-1.5 bg-soft-cloud border border-hairline px-3 py-1 rounded-full text-ink font-medium">
                                Kata Kunci: "{{ request('search') }}"
                                <a href="{{ route('shop.index', request()->except('search', 'page')) }}" class="text-mute hover:text-ink font-bold">&times;</a>
                            </span>
                        @endif
                    </div>
                @endif

                {{-- Product Cards Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                    @forelse($products as $product)
                        <x-product-card :product="$product" />
                    @empty
                        <div class="col-span-3 text-center py-20 bg-soft-cloud border border-hairline-soft space-y-3">
                            <h3 class="font-medium text-base text-ink">Tidak Ada Produk yang Ditemukan</h3>
                            <p class="text-xs text-mute max-w-sm mx-auto">
                                Coba ubah kata kunci pencarian atau hapus filter untuk melihat koleksi jersey lainnya.
                            </p>
                            <a href="{{ route('shop.index') }}" class="btn-primary py-2.5 px-6 text-xs inline-block mt-2 rounded-full">
                                Reset Filter
                            </a>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination Links --}}
                <div class="pt-6 border-t border-hairline-soft">
                    {{ $products->links() }}
                </div>

            </div>

        </div>
    </div>
</div>
@endsection
