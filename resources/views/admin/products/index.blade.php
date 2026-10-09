@extends('layouts.admin')

@section('title', 'Katalog Produk & Jersey · NGIZAN APPAREL')

@section('content')
    <div class="space-y-6">

        {{-- Header & Add Button --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-hairline-soft pb-5">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-mute block mb-1">Master Data</span>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-ink">Katalog Kit & Jersey</h1>
                <p class="text-xs text-mute mt-1">Kelola seluruh produk jersey, foto tampak depan/belakang, dan matriks stok varian.</p>
            </div>
            <a href="{{ route('admin.products.create') }}"
                class="px-5 py-2.5 bg-ink hover:opacity-90 text-white rounded-full text-xs font-medium transition flex items-center gap-1.5 self-start sm:self-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Tambah Kit Jersey</span>
            </a>
        </div>

        {{-- Filter & Search Bar --}}
        <div
            class="bg-white p-4 rounded-2xl border border-hairline-soft flex flex-col sm:flex-row gap-3 justify-between items-center text-xs">
            <form method="GET" action="{{ route('admin.products.index') }}"
                class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                <div class="relative w-full sm:w-64">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Cari nama kit atau SKU..."
                        class="w-full bg-soft-cloud border border-hairline pl-9 pr-4 py-2 rounded-full text-xs text-ink focus:outline-none focus:border-ink">
                    <svg class="w-4 h-4 text-mute absolute left-3 top-2.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                @php
                    $selectedCategory = $categories->firstWhere('id', request('category'));
                    $categoryLabel = $selectedCategory ? $selectedCategory->name : 'Semua Kategori';
                @endphp

                <div class="relative" x-data="{ catOpen: false }">
                    <button type="button" @click="catOpen = !catOpen"
                        class="inline-flex items-center gap-2 bg-soft-cloud hover:bg-neutral-200 border border-hairline text-ink text-xs font-semibold rounded-full px-4 py-2 transition focus:outline-none cursor-pointer shadow-2xs select-none">
                        <span class="text-mute font-normal">Kategori:</span>
                        <span class="font-bold">{{ $categoryLabel }}</span>
                        <svg class="w-3.5 h-3.5 text-mute transition-transform duration-200"
                            :class="catOpen ? 'rotate-180 text-ink' : ''" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="catOpen" @click.away="catOpen = false" x-cloak
                        x-transition:enter="transition ease-out duration-150 transform"
                        x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100 transform"
                        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                        x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                        class="absolute left-0 mt-2 w-56 bg-white border border-hairline-soft rounded-2xl py-1.5 shadow-xl z-40 text-xs overflow-hidden backdrop-blur-xl max-h-64 overflow-y-auto">
                        <div class="px-3.5 py-1.5 border-b border-hairline-soft text-[10px] font-bold uppercase tracking-wider text-mute">
                            Pilih Kategori
                        </div>
                        <div class="py-1">
                            <a href="{{ route('admin.products.index', array_merge(request()->except('page', 'category'), [])) }}"
                                class="flex items-center justify-between px-3.5 py-2 transition {{ !request('category') ? 'bg-soft-cloud text-ink font-bold' : 'text-neutral-600 hover:text-ink hover:bg-soft-cloud/70 font-medium' }}">
                                <span>Semua Kategori</span>
                                @if(!request('category'))
                                    <svg class="w-4 h-4 text-ink shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                @endif
                            </a>
                            @foreach ($categories as $cat)
                                @php $isCatSelected = request('category') == $cat->id; @endphp
                                <a href="{{ route('admin.products.index', array_merge(request()->except('page', 'category'), ['category' => $cat->id])) }}"
                                    class="flex items-center justify-between px-3.5 py-2 transition {{ $isCatSelected ? 'bg-soft-cloud text-ink font-bold' : 'text-neutral-600 hover:text-ink hover:bg-soft-cloud/70 font-medium' }}">
                                    <span>{{ $cat->name }}</span>
                                    @if ($isCatSelected)
                                        <svg class="w-4 h-4 text-ink shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                        </svg>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                @if (request()->hasAny(['search', 'category']))
                    <a href="{{ route('admin.products.index') }}"
                        class="text-xs font-medium text-mute hover:text-ink underline">Reset</a>
                @endif
            </form>
        </div>

        {{-- Table Grid --}}
        <div class="bg-white rounded-2xl border border-hairline-soft overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left text-ink">
                <thead class="bg-soft-cloud uppercase font-medium text-mute border-b border-hairline-soft">
                    <tr>
                        <th class="p-4">Jersey & Preview</th>
                        <th class="p-4">SKU & Kategori</th>
                        <th class="p-4">Harga Dasar</th>
                        <th class="p-4">Total Stok</th>
                        <th class="p-4">Opsi Kustom</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline-soft">
                    @forelse($products as $product)
                        @php
                            $frontImg = $product->thumbnail_front
                                ? asset('storage/' . $product->thumbnail_front)
                                : 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=150&q=80';
                            $backImg = $product->thumbnail_back
                                ? asset('storage/' . $product->thumbnail_back)
                                : 'https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&w=150&q=80';
                        @endphp
                        <tr class="hover:bg-soft-cloud/50 transition">
                            <td class="p-4 flex items-center gap-3">
                                <div class="flex gap-1.5">
                                    <img src="{{ $frontImg }}" alt="Front"
                                        class="w-10 h-12 rounded-lg object-cover border border-hairline-soft"
                                        title="Front POV">
                                    <img src="{{ $backImg }}" alt="Back"
                                        class="w-10 h-12 rounded-lg object-cover border border-hairline-soft"
                                        title="Back POV">
                                </div>
                                <div>
                                    <span class="font-medium text-ink block line-clamp-1">{{ $product->name }}</span>
                                    <span class="text-[11px] text-mute font-mono">{{ $product->weight_grams }} gram</span>
                                </div>
                            </td>
                            <td class="p-4">
                                <span class="font-mono text-ink font-medium block">{{ $product->sku }}</span>
                                <span class="text-[11px] text-mute">{{ $product->category->name }}</span>
                            </td>
                            <td class="p-4 font-medium text-ink tabular-nums">
                                {{ $product->formatted_price }}
                            </td>
                            <td class="p-4">
                                <span
                                    class="font-medium text-sm {{ $product->total_stock <= 5 ? 'text-sale' : 'text-ink' }}">
                                    {{ $product->total_stock }} pcs
                                </span>
                                <div class="text-[11px] text-mute">
                                    {{ $product->variants->count() }} Varian Ukuran
                                </div>
                            </td>
                            <td class="p-4 space-y-1">
                                @if ($product->allow_custom_nameset)
                                    <span
                                        class="inline-block text-[10px] bg-ink text-white px-2 py-0.5 rounded-full font-medium font-jersey">
                                        +SABLON Rp {{ number_format($product->custom_nameset_price, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-[11px] text-mute">-</span>
                                @endif
                            </td>
                            <td class="p-4">
                                <span
                                    class="px-2.5 py-1 rounded-full text-[11px] font-medium {{ $product->is_active ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-soft-cloud text-mute border border-hairline' }}">
                                    {{ $product->is_active ? 'Aktif' : 'Draft' }}
                                </span>
                            </td>
                            <td class="p-4 text-right space-x-1 whitespace-nowrap">
                                <a href="{{ route('admin.products.show', $product->id) }}"
                                    class="p-2 bg-soft-cloud hover:bg-neutral-200 text-ink rounded-full transition inline-flex items-center justify-center"
                                    title="Lihat Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('admin.products.edit', $product->id) }}"
                                    class="p-2 bg-soft-cloud hover:bg-neutral-200 text-ink rounded-full transition inline-flex items-center justify-center"
                                    title="Edit Produk">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST"
                                    class="inline"
                                    data-confirm-title="Hapus Produk Jersey?"
                                    data-confirm-text="Apakah Anda yakin ingin menghapus '{{ $product->name }}' beserta variannya?"
                                    data-confirm-btn="Ya, Hapus Produk">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="p-2 bg-soft-cloud hover:bg-rose-50 text-sale rounded-full transition inline-flex items-center justify-center cursor-pointer"
                                        title="Hapus Produk">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-mute">Belum ada produk jersey yang ditambahkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>

            <div class="p-4 border-t border-hairline-soft">
                {{ $products->links() }}
            </div>
        </div>

    </div>
@endsection
