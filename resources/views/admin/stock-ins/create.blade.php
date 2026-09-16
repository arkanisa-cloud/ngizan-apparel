@extends('layouts.admin')

@section('title', 'Catat Stok Masuk · NGIZAN APPAREL')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between border-b border-hairline-soft pb-5">
        <div>
            <h1 class="text-2xl font-medium tracking-tight text-ink">Catat Restock Jersey Masuk</h1>
            <p class="text-xs text-mute mt-1">Tambah kuantitas fisik stok jersey dari pengadaan supplier.</p>
        </div>
        <a href="{{ route('admin.stock-ins.index') }}" class="px-4 py-2 bg-soft-cloud hover:bg-neutral-200 text-ink rounded-full text-xs font-medium transition">&larr; Kembali</a>
    </div>

    <form action="{{ route('admin.stock-ins.store') }}" method="POST" class="bg-white p-6 rounded-2xl border border-hairline-soft space-y-4 text-xs">
        @csrf

        <div>
            <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Pilih Supplier Pengadaan *</label>
            <select name="supplier_id" required class="w-full bg-soft-cloud border border-hairline p-2.5 rounded-xl text-xs text-ink focus:outline-none focus:border-ink cursor-pointer">
                <option value="">Pilih Supplier</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}">{{ $supplier->name }} ({{ $supplier->phone }})</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Pilih Jersey & Varian Ukuran *</label>
            <select name="product_variant_id" required class="w-full bg-soft-cloud border border-hairline p-2.5 rounded-xl text-xs text-ink focus:outline-none focus:border-ink cursor-pointer">
                <option value="">Pilih Varian Jersey</option>
                @foreach($products as $prod)
                    <optgroup label="{{ $prod->name }}">
                        @foreach($prod->variants as $variant)
                            <option value="{{ $variant->id }}">
                                {{ $prod->name }} — Size: {{ $variant->size }} ({{ $variant->type }}) [Stok Saat Ini: {{ $variant->stock }} pcs]
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Jumlah Qty Masuk (Pcs) *</label>
                <input type="number" name="quantity" required min="1" value="10"
                       class="w-full bg-soft-cloud border border-hairline p-2.5 rounded-xl text-xs text-ink focus:outline-none focus:border-ink">
            </div>

            <div>
                <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Harga Beli per Pcs (Rp) *</label>
                <input type="number" name="purchase_price" required min="0" step="1000" value="150000"
                       class="w-full bg-soft-cloud border border-hairline p-2.5 rounded-xl text-xs text-ink focus:outline-none focus:border-ink">
            </div>

            <div>
                <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">No. Invoice / Surat Jalan</label>
                <input type="text" name="invoice_number" placeholder="Contoh: INV-SUP-2024-001"
                       class="w-full bg-soft-cloud border border-hairline p-2.5 rounded-xl text-xs text-ink focus:outline-none focus:border-ink">
            </div>

            <div>
                <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Tanggal Diterima *</label>
                <input type="date" name="received_date" required value="{{ date('Y-m-d') }}"
                       class="w-full bg-soft-cloud border border-hairline p-2.5 rounded-xl text-xs text-ink focus:outline-none focus:border-ink">
            </div>
        </div>

        <div>
            <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Catatan Tambahan</label>
            <textarea name="notes" rows="2" placeholder="Kondisi kain mulus, jahitan rapi batch 1..."
                      class="w-full bg-soft-cloud border border-hairline p-2.5 rounded-xl text-xs text-ink focus:outline-none focus:border-ink"></textarea>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-hairline-soft">
            <a href="{{ route('admin.stock-ins.index') }}" class="px-5 py-2.5 bg-soft-cloud hover:bg-neutral-200 rounded-full text-xs font-medium text-ink transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-ink hover:opacity-90 text-white rounded-full text-xs font-medium transition cursor-pointer">
                Simpan & Tambah Stok
            </button>
        </div>
    </form>
</div>
@endsection
