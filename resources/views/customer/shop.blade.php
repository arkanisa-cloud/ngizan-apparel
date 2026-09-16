@extends('layouts.customer')

@section('title', 'Katalog Lengkap Jersey · NGIZAN APPAREL')
@section('meta_description', 'Jelajahi koleksi lengkap jersey sepak bola autentik, edisi retro, dan tim nasional dengan opsi kustomisasi sablon nama resmi.')

@section('content')
<div class="py-10" x-data="{ mobileFilter: false }">
    <div class="wrap">
        
        {{-- Header & Breadcrumb --}}
        <div class="flex flex-col sm:flex-row sm:items-end justify-between border-b border-black/10 pb-6 mb-8 gap-4">
            <div>
                <span class="lbl text-ink-muted">ARSIP & KOLEKSI RESMI</span>
                <h1 class="font-display font-black text-2xl md:text-3xl text-ink uppercase tracking-tight mt-1">
                    Katalog Jersey
                </h1>
            </div>

            {{-- Sort Dropdown & Mobile Filter Trigger --}}
            <div class="flex items-center gap-3">
                <button type="button" @click="mobileFilter = !mobileFilter" class="lg:hidden btn-line py-2.5 px-4 text-xs flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    <span>Filter</span>
                </button>

                <form method="GET" action="{{ route('shop.index') }}" class="flex items-center gap-2 text-xs">
                    @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                    @if(request('size')) <input type="hidden" name="size" value="{{ request('size') }}"> @endif
                    @if(request('type')) <input type="hidden" name="type" value="{{ request('type') }}"> @endif
                    @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif

                    <label for="sort" class="text-ink-muted hidden sm:inline uppercase font-bold text-[10.5px]">Urutkan:</label>
                    <select name="sort" id="sort" onchange="this.form.submit()" class="bg-white border border-black/20 text-ink text-xs rounded p-2 focus:ring-1 focus:ring-ink">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Rilis Terbaru</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga: Rendah ke Tinggi</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga: Tinggi ke Rendah</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nama A - Z</option>
                    </select>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- ===== 1. FILTER SIDEBAR (3 COLS) ===== --}}
            <aside class="hidden lg:block lg:col-span-3 bg-white p-6 rounded border border-black/10 space-y-6">
                
                {{-- Search Box --}}
                <div>
                    <h3 class="lbl text-ink mb-2">Pencarian</h3>
                    <form method="GET" action="{{ route('shop.index') }}">
                        @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama klub / negara..." 
                                   class="w-full bg-canvas border border-black/20 p-2.5 pl-8 rounded text-xs text-ink focus:ring-1 focus:ring-ink">
                            <svg class="w-4 h-4 text-ink-muted absolute left-2.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </form>
                </div>

                {{-- Kategori --}}
                <div class="border-t border-black/10 pt-5">
                    <h3 class="lbl text-ink mb-3">Kategori</h3>
                    <ul class="space-y-1.5 text-xs">
                        <li>
                            <a href="{{ route('shop.index', request()->except('category', 'page')) }}" 
                               class="flex justify-between items-center py-1 {{ !request('category') ? 'font-bold text-cyan-600' : 'text-ink-muted hover:text-ink' }}">
                                <span>Semua Kategori</span>
                            </a>
                        </li>
                        @foreach($categories as $cat)
                            <li>
                                <a href="{{ route('shop.index', array_merge(request()->except('page'), ['category' => $cat->slug])) }}" 
                                   class="flex justify-between items-center py-1 {{ request('category') === $cat->slug ? 'font-bold text-cyan-600' : 'text-ink-muted hover:text-ink' }}">
                                    <span>{{ $cat->name }}</span>
                                    <span class="text-[10px] text-ink-muted bg-canvas px-1.5 py-0.5 rounded">{{ $cat->products_count }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Tipe Jersey --}}
                <div class="border-t border-black/10 pt-5">
                    <h3 class="lbl text-ink mb-3">Tipe Edisi</h3>
                    <div class="space-y-1.5 text-xs">
                        @foreach(['Player Issue', 'Fans Issue', 'Retro'] as $t)
                            <a href="{{ route('shop.index', array_merge(request()->except('page'), ['type' => request('type') === $t ? null : $t])) }}"
                               class="flex items-center gap-2 py-1 {{ request('type') === $t ? 'font-bold text-cyan-600' : 'text-ink-muted hover:text-ink' }}">
                                <span class="w-3.5 h-3.5 border rounded flex items-center justify-center {{ request('type') === $t ? 'bg-cyan-600 border-cyan-600 text-white' : 'border-black/20' }}">
                                    @if(request('type') === $t) &#10003; @endif
                                </span>
                                <span>{{ $t }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Filter Ukuran (Size) --}}
                <div class="border-t border-black/10 pt-5">
                    <h3 class="lbl text-ink mb-3">Ukuran Tersedia</h3>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach(['S', 'M', 'L', 'XL', 'XXL', '3XL'] as $s)
                            <a href="{{ route('shop.index', array_merge(request()->except('page'), ['size' => request('size') === $s ? null : $s])) }}"
                               class="w-9 h-8 rounded border text-xs font-semibold flex items-center justify-center transition {{ request('size') === $s ? 'bg-ink text-canvas border-ink font-bold' : 'border-black/20 text-ink hover:border-black' }}">
                                {{ $s }}
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Reset Filter Button --}}
                @if(request()->hasAny(['category', 'search', 'size', 'type', 'sort']))
                    <div class="border-t border-black/10 pt-4">
                        <a href="{{ route('shop.index') }}" class="text-xs font-bold text-rose-600 hover:underline block text-center">
                            ✕ Hapus Semua Filter
                        </a>
                    </div>
                @endif

            </aside>

            {{-- ===== 2. PRODUCT GRID & PAGINATION (9 COLS) ===== --}}
            <div class="lg:col-span-9 space-y-8">
                
                {{-- Active Filters Chips --}}
                @if(request()->hasAny(['category', 'search', 'size', 'type']))
                    <div class="flex flex-wrap items-center gap-2 bg-white p-3.5 rounded border border-black/10 text-xs">
                        <span class="text-ink-muted uppercase font-bold text-[10px]">Filter Aktif:</span>
                        
                        @if(request('category'))
                            <span class="inline-flex items-center gap-1.5 bg-canvas px-2.5 py-1 rounded font-semibold text-ink">
                                Kategori: {{ request('category') }}
                                <a href="{{ route('shop.index', request()->except('category', 'page')) }}" class="text-ink-muted hover:text-ink font-bold">&times;</a>
                            </span>
                        @endif

                        @if(request('size'))
                            <span class="inline-flex items-center gap-1.5 bg-canvas px-2.5 py-1 rounded font-semibold text-ink">
                                Ukuran: {{ request('size') }}
                                <a href="{{ route('shop.index', request()->except('size', 'page')) }}" class="text-ink-muted hover:text-ink font-bold">&times;</a>
                            </span>
                        @endif

                        @if(request('type'))
                            <span class="inline-flex items-center gap-1.5 bg-canvas px-2.5 py-1 rounded font-semibold text-ink">
                                Tipe: {{ request('type') }}
                                <a href="{{ route('shop.index', request()->except('type', 'page')) }}" class="text-ink-muted hover:text-ink font-bold">&times;</a>
                            </span>
                        @endif

                        @if(request('search'))
                            <span class="inline-flex items-center gap-1.5 bg-canvas px-2.5 py-1 rounded font-semibold text-ink">
                                Kata Kunci: "{{ request('search') }}"
                                <a href="{{ route('shop.index', request()->except('search', 'page')) }}" class="text-ink-muted hover:text-ink font-bold">&times;</a>
                            </span>
                        @endif
                    </div>
                @endif

                {{-- Product Cards Grid --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($products as $product)
                        <x-product-card :product="$product" />
                    @empty
                        <div class="col-span-3 text-center py-20 bg-white rounded border border-black/10 space-y-3">
                            <div class="text-3xl">👕</div>
                            <h3 class="font-bold text-base text-ink">Tidak Ada Produk yang Ditemukan</h3>
                            <p class="text-xs text-ink-muted max-w-sm mx-auto">
                                Coba ubah kata kunci pencarian atau bersihkan filter untuk melihat koleksi jersey lainnya.
                            </p>
                            <a href="{{ route('shop.index') }}" class="btn-line py-2 px-5 text-xs inline-block mt-2">
                                Reset Filter
                            </a>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination Links --}}
                <div class="pt-4">
                    {{ $products->links() }}
                </div>

            </div>

        </div>
    </div>
</div>
@endsection
