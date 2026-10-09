@extends('layouts.admin')

@section('title', $product->name . ' · Detail Produk Admin')

@section('content')
<div class="space-y-6">
    
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-hairline-soft pb-5">
        <div>
            <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-mute block mb-1">Master Data</span>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-ink">{{ $product->name }}</h1>
                <span class="px-3 py-0.5 rounded-full text-[11px] font-medium {{ $product->is_active ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-soft-cloud text-mute border border-hairline' }}">
                    {{ $product->is_active ? 'Aktif' : 'Draft' }}
                </span>
            </div>
            <p class="text-xs text-mute mt-1">SKU: <strong class="font-mono text-ink">{{ $product->sku }}</strong> &bull; Kategori: {{ $product->category->name }}</p>
        </div>
        <div class="flex items-center gap-2.5 self-start sm:self-auto">
            <a href="{{ route('admin.products.edit', $product->id) }}" class="px-5 py-2.5 bg-ink hover:opacity-90 text-white rounded-full text-xs font-medium transition inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                <span>Edit Kit Jersey</span>
            </a>
            <a href="{{ route('admin.products.index') }}" class="px-5 py-2.5 bg-soft-cloud hover:bg-neutral-200 text-ink rounded-full text-xs font-medium transition inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        {{-- Photos POV (5 Cols) --}}
        <div class="lg:col-span-5 bg-white p-6 rounded-2xl border border-hairline-soft space-y-4">
            <h2 class="font-medium text-sm text-ink border-b border-hairline-soft pb-3">Galeri Dual POV WebP</h2>
            
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <span class="text-[10px] uppercase font-medium text-mute block mb-1">Tampak Depan</span>
                    @php
                        $frontImg = $product->thumbnail_front ? asset('storage/' . $product->thumbnail_front) : 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=300&q=80';
                    @endphp
                    <img src="{{ $frontImg }}" alt="Front POV" class="w-full aspect-[3/4] object-cover rounded-xl border border-hairline-soft bg-soft-cloud">
                </div>
                <div>
                    <span class="text-[10px] uppercase font-medium text-mute block mb-1">Tampak Belakang</span>
                    @php
                        $backImg = $product->thumbnail_back ? asset('storage/' . $product->thumbnail_back) : 'https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&w=300&q=80';
                    @endphp
                    <img src="{{ $backImg }}" alt="Back POV" class="w-full aspect-[3/4] object-cover rounded-xl border border-hairline-soft bg-soft-cloud">
                </div>
            </div>

            <div class="text-xs text-mute space-y-2 pt-2 border-t border-hairline-soft">
                <div class="flex justify-between">
                    <span>Harga Dasar:</span>
                    <strong class="text-ink font-medium text-sm">{{ $product->formatted_price }}</strong>
                </div>
                <div class="flex justify-between">
                    <span>Berat Paket:</span>
                    <strong class="text-ink">{{ $product->weight_grams }} gram</strong>
                </div>
                <div class="flex justify-between">
                    <span>Opsi Sablon:</span>
                    <strong class="text-ink">{{ $product->allow_custom_nameset ? 'Aktif (+Rp ' . number_format($product->custom_nameset_price, 0, ',', '.') . ')' : 'Tidak Aktif' }}</strong>
                </div>
            </div>
        </div>

        {{-- Varian Matrix & Stock History (7 Cols) --}}
        <div class="lg:col-span-7 space-y-6">
            
            {{-- Varian Matrix --}}
            <div class="bg-white p-6 rounded-2xl border border-hairline-soft space-y-4">
                <h2 class="font-medium text-sm text-ink border-b border-hairline-soft pb-3">Matriks Varian Ukuran & Stok</h2>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left text-ink">
                        <thead class="bg-soft-cloud uppercase font-medium text-mute border-b border-hairline-soft">
                            <tr>
                                <th class="p-3">Ukuran</th>
                                <th class="p-3">SKU Varian</th>
                                <th class="p-3">Harga Akhir</th>
                                <th class="p-3 text-right">Stok Fisik</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-hairline-soft">
                            @foreach($product->variants as $variant)
                                <tr>
                                    <td class="p-3 font-medium text-ink">{{ $variant->size }}</td>
                                    <td class="p-3 font-mono text-[10px] text-mute">{{ $variant->sku }}</td>
                                    <td class="p-3 font-medium text-ink">{{ $variant->formatted_final_price }}</td>
                                    <td class="p-3 text-right font-medium text-sm {{ $variant->stock <= 3 ? 'text-sale' : 'text-ink' }}">
                                        {{ $variant->stock }} pcs
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Audit Trail Riwayat Stok --}}
            <div class="bg-white p-6 rounded-2xl border border-hairline-soft space-y-4">
                <h2 class="font-medium text-sm text-ink border-b border-hairline-soft pb-3">Audit Trail Mutasi Stok Terbaru</h2>

                <div class="space-y-2 text-xs">
                    @php
                        $histories = \App\Models\StockHistory::whereIn('product_variant_id', $product->variants->pluck('id'))
                            ->with('variant')
                            ->latest()
                            ->take(6)
                            ->get();
                    @endphp

                    @forelse($histories as $h)
                        <div class="flex items-center justify-between p-3 bg-soft-cloud rounded-xl border border-hairline-soft">
                            <div>
                                <span class="font-medium text-ink">
                                    {{ $h->variant?->size }} ({{ $h->variant?->type }}): 
                                    <span class="{{ $h->quantity_change > 0 ? 'text-emerald-700' : 'text-sale' }}">
                                        {{ $h->quantity_change > 0 ? '+' . $h->quantity_change : $h->quantity_change }} pcs
                                    </span>
                                </span>
                                <span class="text-[10px] text-mute block">{{ $h->notes ?? $h->reference_type->value }} · {{ $h->created_at->format('d M Y H:i') }}</span>
                            </div>
                            <span class="text-xs font-medium text-ink font-mono">Sisa: {{ $h->stock_after }}</span>
                        </div>
                    @empty
                        <p class="text-mute py-4 text-center">Belum ada catatan mutasi stok.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
</div>
@endsection
