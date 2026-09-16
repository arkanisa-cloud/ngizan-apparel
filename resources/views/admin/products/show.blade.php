@extends('layouts.admin')

@section('title', $product->name . ' · Detail Produk Admin')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    
    {{-- Header --}}
    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-bold font-display text-slate-900">{{ $product->name }}</h1>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $product->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                    {{ $product->is_active ? 'Aktif' : 'Draft' }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-0.5">SKU: <strong class="font-mono">{{ $product->sku }}</strong> · Kategori: {{ $product->category->name }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.products.edit', $product->id) }}" class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-bold transition">
                ✏️ Edit Produk
            </a>
            <a href="{{ route('admin.products.index') }}" class="px-3.5 py-2 border border-slate-300 hover:bg-slate-100 text-slate-700 rounded-lg text-xs font-bold transition">
                &larr; Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        {{-- Photos POV (5 Cols) --}}
        <div class="lg:col-span-5 bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
            <h2 class="font-bold text-sm text-slate-900 border-b border-slate-100 pb-3">Galeri Dual POV WebP</h2>
            
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Tampak Depan</span>
                    @php
                        $frontImg = $product->thumbnail_front ? asset('storage/' . $product->thumbnail_front) : 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=300&q=80';
                    @endphp
                    <img src="{{ $frontImg }}" alt="Front POV" class="w-full aspect-[3/4] object-cover rounded-lg border border-slate-200">
                </div>
                <div>
                    <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Tampak Belakang</span>
                    @php
                        $backImg = $product->thumbnail_back ? asset('storage/' . $product->thumbnail_back) : 'https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&w=300&q=80';
                    @endphp
                    <img src="{{ $backImg }}" alt="Back POV" class="w-full aspect-[3/4] object-cover rounded-lg border border-slate-200">
                </div>
            </div>

            <div class="text-xs text-slate-500 space-y-1.5 pt-2 border-t border-slate-100">
                <div class="flex justify-between">
                    <span>Harga Dasar:</span>
                    <strong class="text-slate-900 font-display text-sm">{{ $product->formatted_price }}</strong>
                </div>
                <div class="flex justify-between">
                    <span>Berat Paket:</span>
                    <strong class="text-slate-900">{{ $product->weight_grams }} gram</strong>
                </div>
                <div class="flex justify-between">
                    <span>Opsi Sablon:</span>
                    <strong class="text-cyan-700">{{ $product->allow_custom_nameset ? 'Aktif (+Rp ' . number_format($product->custom_nameset_price, 0, ',', '.') . ')' : 'Tidak Aktif' }}</strong>
                </div>
                <div class="flex justify-between">
                    <span>Opsi Patch:</span>
                    <strong class="text-cyan-700">{{ $product->allow_patch ? 'Aktif (+Rp ' . number_format($product->patch_price, 0, ',', '.') . ')' : 'Tidak Aktif' }}</strong>
                </div>
            </div>
        </div>

        {{-- Varian Matrix & Stock History (7 Cols) --}}
        <div class="lg:col-span-7 space-y-6">
            
            {{-- Varian Matrix --}}
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                <h2 class="font-bold text-sm text-slate-900 border-b border-slate-100 pb-3">Matriks Varian Ukuran & Stok</h2>
                
                <table class="w-full text-xs text-left text-slate-600">
                    <thead class="bg-slate-50 uppercase font-bold text-slate-700">
                        <tr>
                            <th class="p-2.5">Ukuran</th>
                            <th class="p-2.5">Tipe</th>
                            <th class="p-2.5">SKU Varian</th>
                            <th class="p-2.5">Harga Akhir</th>
                            <th class="p-2.5 text-right">Stok Fisik</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($product->variants as $variant)
                            <tr>
                                <td class="p-2.5 font-bold text-slate-900">{{ $variant->size }}</td>
                                <td class="p-2.5 uppercase text-[10.5px] font-semibold text-slate-700">{{ $variant->type }}</td>
                                <td class="p-2.5 font-mono text-[10px] text-slate-400">{{ $variant->sku }}</td>
                                <td class="p-2.5 font-semibold text-slate-800">{{ $variant->formatted_final_price }}</td>
                                <td class="p-2.5 text-right font-display font-black text-sm {{ $variant->stock <= 3 ? 'text-rose-600' : 'text-slate-900' }}">
                                    {{ $variant->stock }} pcs
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Audit Trail Riwayat Stok --}}
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                <h2 class="font-bold text-sm text-slate-900 border-b border-slate-100 pb-3">Audit Trail Mutasi Stok Terbaru</h2>

                <div class="space-y-2.5 text-xs">
                    @php
                        $histories = \App\Models\StockHistory::whereIn('product_variant_id', $product->variants->pluck('id'))
                            ->with('variant')
                            ->latest()
                            ->take(6)
                            ->get();
                    @endphp

                    @forelse($histories as $h)
                        <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-lg">
                            <div>
                                <span class="font-bold text-slate-900">
                                    {{ $h->variant?->size }} ({{ $h->variant?->type }}): 
                                    <span class="{{ $h->quantity_change > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $h->quantity_change > 0 ? '+' . $h->quantity_change : $h->quantity_change }} pcs
                                    </span>
                                </span>
                                <span class="text-[10px] text-slate-400 block">{{ $h->notes ?? $h->reference_type->value }} · {{ $h->created_at->format('d M Y H:i') }}</span>
                            </div>
                            <span class="text-xs font-bold text-slate-700 font-mono">Sisa: {{ $h->stock_after }}</span>
                        </div>
                    @empty
                        <p class="text-slate-400 py-4 text-center">Belum ada catatan mutasi stok.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
