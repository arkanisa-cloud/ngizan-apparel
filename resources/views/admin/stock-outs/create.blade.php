@extends('layouts.admin')

@section('title', 'Catat Stok Keluar · NGIZAN APPAREL')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between border-b border-hairline-soft pb-5">
        <div>
            <h1 class="text-2xl font-medium tracking-tight text-ink">Catat Penyesuaian Stok Keluar</h1>
            <p class="text-xs text-mute mt-1">Kurangi kuantitas stok akibat barang cacat, sampel display, atau promosi.</p>
        </div>
        <a href="{{ route('admin.stock-outs.index') }}" class="px-4 py-2 bg-soft-cloud hover:bg-neutral-200 text-ink rounded-full text-xs font-medium transition">&larr; Kembali</a>
    </div>

    <form action="{{ route('admin.stock-outs.store') }}" method="POST" class="bg-white p-6 rounded-2xl border border-hairline-soft space-y-4 text-xs">
        @csrf

        <div>
            <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Pilih Jersey & Varian Ukuran *</label>
            <select name="product_variant_id" required class="w-full bg-soft-cloud border border-hairline p-2.5 rounded-xl text-xs text-ink focus:outline-none focus:border-ink cursor-pointer">
                <option value="">Pilih Varian Jersey</option>
                @foreach($products as $prod)
                    <optgroup label="{{ $prod->name }}">
                        @foreach($prod->variants as $variant)
                            <option value="{{ $variant->id }}">
                                {{ $prod->name }} — Size: {{ $variant->size }} ({{ $variant->type }}) [Stok Tersedia: {{ $variant->stock }} pcs]
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Jumlah Qty Keluar (Pcs) *</label>
                <input type="number" name="quantity" required min="1" value="1"
                       class="w-full bg-soft-cloud border border-hairline p-2.5 rounded-xl text-xs text-ink focus:outline-none focus:border-ink">
            </div>

            <div>
                <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Alasan Pengurangan *</label>
                <select name="reason" required class="w-full bg-soft-cloud border border-hairline p-2.5 rounded-xl text-xs text-ink focus:outline-none focus:border-ink cursor-pointer">
                    <option value="Damaged">Barang Rusak / Cacat Sablon (Damaged)</option>
                    <option value="Sample">Sampel Display Workshop (Sample)</option>
                    <option value="Promotion">Promosi Influencer / Give Away (Promotion)</option>
                    <option value="Loss">Selisih Opname / Hilang (Loss)</option>
                </select>
            </div>

            <div class="sm:col-span-2">
                <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Tanggal Keluar *</label>
                <input type="date" name="out_date" required value="{{ date('Y-m-d') }}"
                       class="w-full bg-soft-cloud border border-hairline p-2.5 rounded-xl text-xs text-ink focus:outline-none focus:border-ink">
            </div>
        </div>

        <div>
            <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Catatan Tambahan</label>
            <textarea name="notes" rows="2" placeholder="Cacat press logo pada dada kiri..."
                      class="w-full bg-soft-cloud border border-hairline p-2.5 rounded-xl text-xs text-ink focus:outline-none focus:border-ink"></textarea>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-hairline-soft">
            <a href="{{ route('admin.stock-outs.index') }}" class="px-5 py-2.5 bg-soft-cloud hover:bg-neutral-200 rounded-full text-xs font-medium text-ink transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-ink hover:opacity-90 text-white rounded-full text-xs font-medium transition cursor-pointer">
                Simpan & Kurangi Stok
            </button>
        </div>
    </form>
</div>
@endsection
