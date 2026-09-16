@extends('layouts.admin')

@section('title', 'Katalog Produk & Jersey · NGIZAN APPAREL')

@section('content')
<div class="space-y-6">
    
    {{-- Header & Add Button --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-hairline-soft pb-5">
        <div>
            <h1 class="text-2xl font-medium tracking-tight text-ink">Katalog Kit & Jersey</h1>
            <p class="text-xs text-mute mt-1">Kelola seluruh produk jersey, foto tampak depan/belakang, dan matriks stok varian.</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="px-5 py-2.5 bg-ink hover:opacity-90 text-white rounded-full text-xs font-medium transition flex items-center gap-1.5 self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>+ Tambah Kit Jersey</span>
        </a>
    </div>

    {{-- Filter & Search Bar --}}
    <div class="bg-white p-4 rounded-2xl border border-hairline-soft flex flex-col sm:flex-row gap-3 justify-between items-center text-xs">
        <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
            <div class="relative w-full sm:w-64">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kit atau SKU..." 
                       class="w-full bg-soft-cloud border border-hairline pl-9 pr-4 py-2 rounded-full text-xs text-ink focus:outline-none focus:border-ink">
                <svg class="w-4 h-4 text-mute absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <select name="category" onchange="this.form.submit()" class="bg-soft-cloud border border-hairline px-4 py-2 rounded-full text-xs text-ink font-medium focus:outline-none focus:border-ink cursor-pointer">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>

            @if(request()->hasAny(['search', 'category']))
                <a href="{{ route('admin.products.index') }}" class="text-xs font-medium text-mute hover:text-ink underline">Reset</a>
            @endif
        </form>
    </div>

    {{-- Table Grid --}}
    <div class="bg-white rounded-2xl border border-hairline-soft overflow-hidden">
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
                        $frontImg = $product->thumbnail_front ? asset('storage/' . $product->thumbnail_front) : 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=150&q=80';
                        $backImg  = $product->thumbnail_back  ? asset('storage/' . $product->thumbnail_back)  : 'https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&w=150&q=80';
                    @endphp
                    <tr class="hover:bg-soft-cloud/50 transition">
                        <td class="p-4 flex items-center gap-3">
                            <div class="flex gap-1.5">
                                <img src="{{ $frontImg }}" alt="Front" class="w-10 h-12 rounded-lg object-cover border border-hairline-soft" title="Front POV">
                                <img src="{{ $backImg }}" alt="Back" class="w-10 h-12 rounded-lg object-cover border border-hairline-soft" title="Back POV">
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
                            <span class="font-medium text-sm {{ $product->total_stock <= 5 ? 'text-sale' : 'text-ink' }}">
                                {{ $product->total_stock }} pcs
                            </span>
                            <div class="text-[11px] text-mute">
                                {{ $product->variants->count() }} Varian Ukuran
                            </div>
                        </td>
                        <td class="p-4 space-y-1">
                            @if($product->allow_custom_nameset)
                                <span class="inline-block text-[10px] bg-ink text-white px-2 py-0.5 rounded-full font-medium font-jersey">
                                    +SABLON Rp {{ number_format($product->custom_nameset_price, 0, ',', '.') }}
                                </span>
                            @endif
                            @if($product->allow_patch)
                                <span class="inline-block text-[10px] bg-soft-cloud border border-hairline text-ink px-2 py-0.5 rounded-full font-medium">
                                    +PATCH Rp {{ number_format($product->patch_price, 0, ',', '.') }}
                                </span>
                            @endif
                        </td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-medium {{ $product->is_active ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-soft-cloud text-mute border border-hairline' }}">
                                {{ $product->is_active ? 'Aktif' : 'Draft' }}
                            </span>
                        </td>
                        <td class="p-4 text-right space-x-1.5 whitespace-nowrap">
                            <a href="{{ route('admin.products.show', $product->id) }}" class="px-2.5 py-1.5 bg-soft-cloud hover:bg-neutral-200 text-ink rounded-full text-xs font-medium transition inline-block" title="Detail">
                                👁️ Detail
                            </a>
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="px-2.5 py-1.5 bg-soft-cloud hover:bg-neutral-200 text-ink rounded-full text-xs font-medium transition inline-block" title="Edit">
                                ✏️ Edit
                            </a>
                            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus produk ini dari katalog?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2.5 py-1.5 bg-soft-cloud hover:bg-red-50 text-sale rounded-full text-xs font-medium transition cursor-pointer" title="Hapus">
                                    🗑️
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-mute">Belum ada produk jersey yang ditambahkan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t border-hairline-soft">
            {{ $products->links() }}
        </div>
    </div>

</div>
@endsection
