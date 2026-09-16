@extends('layouts.admin')

@section('title', 'Laporan & Audit Stok Gudang · NGIZAN APPAREL')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold font-display text-slate-900">Laporan & Audit Stok Gudang</h1>
            <p class="text-xs text-slate-500 mt-0.5">Audit inventori fisik varian ukuran, sisa stok gudang, dan indikator restock.</p>
        </div>
    </div>

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('admin.reports.stock') }}" class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-wrap gap-3 items-end text-xs">
        <div>
            <label class="block font-bold text-slate-700 mb-1 uppercase tracking-wider text-[10px]">Filter Kategori</label>
            <select name="category_id" class="bg-slate-50 border border-slate-200 p-2 rounded-lg text-xs focus:ring-1 focus:ring-slate-900">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block font-bold text-slate-700 mb-1 uppercase tracking-wider text-[10px]">Kondisi Stok</label>
            <select name="filter" class="bg-slate-50 border border-slate-200 p-2 rounded-lg text-xs focus:ring-1 focus:ring-slate-900">
                <option value="">Semua Kondisi</option>
                <option value="low" {{ request('filter') === 'low' ? 'selected' : '' }}>Stok Menipis (&le; 3 pcs)</option>
                <option value="out" {{ request('filter') === 'out' ? 'selected' : '' }}>Stok Habis (0 pcs)</option>
            </select>
        </div>

        <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg font-bold transition">
            Filter
        </button>

        @if(request()->hasAny(['category_id', 'filter']))
            <a href="{{ route('admin.reports.stock') }}" class="text-rose-600 font-bold hover:underline self-center">Reset</a>
        @endif
    </form>

    {{-- Variant Stock Table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-xs text-left text-slate-600">
            <thead class="bg-slate-50 uppercase font-bold text-slate-700 border-b border-slate-200">
                <tr>
                    <th class="p-3.5">Nama Jersey</th>
                    <th class="p-3.5">Kategori</th>
                    <th class="p-3.5">Ukuran & Tipe</th>
                    <th class="p-3.5">SKU Varian</th>
                    <th class="p-3.5">Harga Jual</th>
                    <th class="p-3.5 text-right">Sisa Stok Fisik</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($variants as $v)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="p-3.5 font-bold text-slate-900">
                            {{ $v->product?->name }}
                        </td>
                        <td class="p-3.5 text-slate-500">
                            {{ $v->product?->category?->name }}
                        </td>
                        <td class="p-3.5">
                            <span class="font-bold text-slate-800">{{ $v->size }}</span>
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">({{ $v->type }})</span>
                        </td>
                        <td class="p-3.5 font-mono text-slate-500">
                            {{ $v->sku }}
                        </td>
                        <td class="p-3.5 font-semibold text-slate-900 tabular-nums">
                            {{ $v->formatted_final_price }}
                        </td>
                        <td class="p-3.5 text-right font-display font-black text-sm {{ $v->stock <= 3 ? 'text-rose-600' : 'text-slate-900' }}">
                            {{ $v->stock }} pcs
                            @if($v->stock <= 3 && $v->stock > 0)
                                <span class="text-[9.5px] font-sans font-bold text-amber-600 block">Menipis</span>
                            @elseif($v->stock == 0)
                                <span class="text-[9.5px] font-sans font-bold text-rose-600 block">Habis</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-400">Tidak ada varian stok yang cocok dengan filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t border-slate-100">
            {{ $variants->links() }}
        </div>
    </div>
</div>
@endsection
