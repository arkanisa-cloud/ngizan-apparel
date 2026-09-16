@extends('layouts.admin')

@section('title', 'Katalog Produk & Jersey · NGIZAN APPAREL')

@section('content')
<div class="space-y-6">
    
    {{-- Header & Add Button --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold font-display text-slate-900">Katalog Kit & Jersey</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola seluruh produk jersey, foto tampak depan/belakang, dan matriks stok varian.</p>
        </div>
        <a href="{{ route('admin.products.create') }}" class="px-4 py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>+ Tambah Kit Jersey</span>
        </a>
    </div>

    {{-- Filter & Search Bar --}}
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row gap-3 justify-between items-center text-xs">
        <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
            <div class="relative w-full sm:w-64">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama kit atau SKU..." 
                       class="w-full bg-slate-50 border border-slate-200 pl-8 p-2 rounded-lg text-xs focus:ring-1 focus:ring-slate-900">
                <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <select name="category" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 p-2 rounded-lg text-xs focus:ring-1 focus:ring-slate-900">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>

            @if(request()->hasAny(['search', 'category']))
                <a href="{{ route('admin.products.index') }}" class="text-rose-600 font-bold hover:underline">Reset</a>
            @endif
        </form>
    </div>

    {{-- Table Grid --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-xs text-left text-slate-600">
            <thead class="bg-slate-50 uppercase font-bold text-slate-700 border-b border-slate-200">
                <tr>
                    <th class="p-3.5">Jersey & Preview</th>
                    <th class="p-3.5">SKU & Kategori</th>
                    <th class="p-3.5">Harga Dasar</th>
                    <th class="p-3.5">Total Stok</th>
                    <th class="p-3.5">Opsi Kustom</th>
                    <th class="p-3.5">Status</th>
                    <th class="p-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($products as $product)
                    @php
                        $frontImg = $product->thumbnail_front ? asset('storage/' . $product->thumbnail_front) : 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=150&q=80';
                        $backImg  = $product->thumbnail_back  ? asset('storage/' . $product->thumbnail_back)  : 'https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&w=150&q=80';
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="p-3.5 flex items-center gap-3">
                            <div class="flex gap-1">
                                <img src="{{ $frontImg }}" alt="Front" class="w-10 h-12 rounded object-cover border border-slate-200" title="Front POV">
                                <img src="{{ $backImg }}" alt="Back" class="w-10 h-12 rounded object-cover border border-slate-200" title="Back POV">
                            </div>
                            <div>
                                <span class="font-bold text-slate-900 block line-clamp-1">{{ $product->name }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $product->weight_grams }} gram</span>
                            </div>
                        </td>
                        <td class="p-3.5">
                            <span class="font-mono text-slate-900 font-bold block">{{ $product->sku }}</span>
                            <span class="text-[10.5px] text-slate-500">{{ $product->category->name }}</span>
                        </td>
                        <td class="p-3.5 font-bold text-slate-900 tabular-nums">
                            {{ $product->formatted_price }}
                        </td>
                        <td class="p-3.5">
                            <span class="font-display font-black text-sm {{ $product->total_stock <= 5 ? 'text-rose-600' : 'text-slate-900' }}">
                                {{ $product->total_stock }} pcs
                            </span>
                            <div class="text-[9.5px] text-slate-400">
                                {{ $product->variants->count() }} Varian Ukuran
                            </div>
                        </td>
                        <td class="p-3.5 space-y-0.5">
                            @if($product->allow_custom_nameset)
                                <span class="inline-block text-[9.5px] bg-neutral-900 text-white px-1.5 py-0.2 rounded font-bold font-jersey">
                                    +SABLON Rp {{ number_format($product->custom_nameset_price, 0, ',', '.') }}
                                </span>
                            @endif
                            @if($product->allow_patch)
                                <span class="inline-block text-[9.5px] bg-cyan-100 text-cyan-800 px-1.5 py-0.2 rounded font-bold">
                                    +PATCH Rp {{ number_format($product->patch_price, 0, ',', '.') }}
                                </span>
                            @endif
                        </td>
                        <td class="p-3.5">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $product->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                {{ $product->is_active ? 'Aktif' : 'Draft' }}
                            </span>
                        </td>
                        <td class="p-3.5 text-right space-x-1 whitespace-nowrap">
                            <a href="{{ route('admin.products.show', $product->id) }}" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded transition inline-block" title="Detail">
                                👁️
                            </a>
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded transition inline-block" title="Edit">
                                ✏️
                            </a>
                            <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus produk ini dari katalog?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded transition" title="Hapus">
                                    🗑️
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-slate-400">Belum ada produk jersey yang ditambahkan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t border-slate-100">
            {{ $products->links() }}
        </div>
    </div>

</div>
@endsection
