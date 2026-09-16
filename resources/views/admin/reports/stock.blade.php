@extends('layouts.admin')

@section('title', 'Laporan & Audit Stok Gudang · NGIZAN APPAREL')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-hairline-soft pb-5">
        <div>
            <h1 class="text-2xl font-medium tracking-tight text-ink">Laporan & Audit Stok Gudang</h1>
            <p class="text-xs text-mute mt-1">Audit inventori fisik varian ukuran, sisa stok gudang, dan indikator restock.</p>
        </div>
    </div>

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('admin.reports.stock') }}" class="bg-white p-4 sm:p-5 rounded-2xl border border-hairline-soft flex flex-wrap gap-3 items-end text-xs">
        <div class="w-full sm:w-auto">
            <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[10px]">Filter Kategori</label>
            <select name="category_id" class="w-full sm:w-auto bg-soft-cloud border border-hairline px-4 py-2 rounded-full text-xs text-ink font-medium focus:outline-none focus:border-ink cursor-pointer">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="w-full sm:w-auto">
            <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[10px]">Kondisi Stok</label>
            <select name="filter" class="w-full sm:w-auto bg-soft-cloud border border-hairline px-4 py-2 rounded-full text-xs text-ink font-medium focus:outline-none focus:border-ink cursor-pointer">
                <option value="">Semua Kondisi</option>
                <option value="low" {{ request('filter') === 'low' ? 'selected' : '' }}>Stok Menipis (&le; 3 pcs)</option>
                <option value="out" {{ request('filter') === 'out' ? 'selected' : '' }}>Stok Habis (0 pcs)</option>
            </select>
        </div>

        <button type="submit" class="w-full sm:w-auto px-5 py-2 bg-ink hover:opacity-90 text-white rounded-full text-xs font-medium transition cursor-pointer">
            Filter
        </button>

        @if(request()->hasAny(['category_id', 'filter']))
            <a href="{{ route('admin.reports.stock') }}" class="text-xs font-medium text-mute hover:text-ink underline self-center">Reset</a>
        @endif
    </form>

    {{-- Variant Stock Table --}}
    <div class="bg-white rounded-2xl border border-hairline-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-ink">
                <thead class="bg-soft-cloud uppercase font-medium text-mute border-b border-hairline-soft">
                    <tr>
                        <th class="p-4">Nama Jersey</th>
                        <th class="p-4">Kategori</th>
                        <th class="p-4">Ukuran & Tipe</th>
                        <th class="p-4">SKU Varian</th>
                        <th class="p-4">Harga Jual</th>
                        <th class="p-4 text-right">Sisa Stok Fisik</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline-soft">
                    @forelse($variants as $v)
                        <tr class="hover:bg-soft-cloud/50 transition">
                            <td class="p-4 font-medium text-ink">
                                {{ $v->product?->name }}
                            </td>
                            <td class="p-4 text-mute">
                                {{ $v->product?->category?->name }}
                            </td>
                            <td class="p-4">
                                <span class="font-medium text-ink">{{ $v->size }}</span>
                                <span class="text-[10px] text-mute uppercase font-medium">({{ $v->type }})</span>
                            </td>
                            <td class="p-4 font-mono text-mute">
                                {{ $v->sku }}
                            </td>
                            <td class="p-4 font-medium text-ink tabular-nums">
                                {{ $v->formatted_final_price }}
                            </td>
                            <td class="p-4 text-right font-medium text-sm {{ $v->stock <= 3 ? 'text-sale' : 'text-ink' }}">
                                {{ $v->stock }} pcs
                                @if($v->stock <= 3 && $v->stock > 0)
                                    <span class="text-[10px] text-amber-600 block font-medium">Menipis</span>
                                @elseif($v->stock == 0)
                                    <span class="text-[10px] text-sale block font-medium">Habis</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-mute">Tidak ada varian stok yang cocok dengan filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-hairline-soft">
            {{ $variants->links() }}
        </div>
    </div>
</div>
@endsection
