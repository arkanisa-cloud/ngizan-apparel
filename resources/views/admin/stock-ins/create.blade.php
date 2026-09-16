@extends('layouts.admin')

@section('title', 'Catat Stok Masuk · NGIZAN APPAREL')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-xl font-bold font-display text-slate-900">Catat Restock Jersey Masuk</h1>
            <p class="text-xs text-slate-500 mt-0.5">Tambah kuantitas fisik stok jersey dari pengadaan supplier.</p>
        </div>
        <a href="{{ route('admin.stock-ins.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900">&larr; Kembali</a>
    </div>

    <form action="{{ route('admin.stock-ins.store') }}" method="POST" class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4 text-xs">
        @csrf

        <div>
            <label class="block font-bold text-slate-700 mb-1 uppercase tracking-wider">Pilih Supplier Pengadaan *</label>
            <select name="supplier_id" required class="w-full bg-slate-50 border border-slate-200 p-2.5 rounded-lg text-xs focus:ring-1 focus:ring-slate-900">
                <option value="">Pilih Supplier</option>
                @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}">{{ $supplier->name }} ({{ $supplier->phone }})</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block font-bold text-slate-700 mb-1 uppercase tracking-wider">Pilih Jersey & Varian Ukuran *</label>
            <select name="product_variant_id" required class="w-full bg-slate-50 border border-slate-200 p-2.5 rounded-lg text-xs focus:ring-1 focus:ring-slate-900">
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
                <label class="block font-bold text-slate-700 mb-1 uppercase tracking-wider">Jumlah Qty Masuk (Pcs) *</label>
                <input type="number" name="quantity" required min="1" value="10"
                       class="w-full bg-slate-50 border border-slate-200 p-2.5 rounded-lg text-xs focus:ring-1 focus:ring-slate-900">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1 uppercase tracking-wider">Harga Beli per Pcs (Rp) *</label>
                <input type="number" name="purchase_price" required min="0" step="1000" value="150000"
                       class="w-full bg-slate-50 border border-slate-200 p-2.5 rounded-lg text-xs focus:ring-1 focus:ring-slate-900">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1 uppercase tracking-wider">No. Invoice / Surat Jalan</label>
                <input type="text" name="invoice_number" placeholder="Contoh: INV-SUP-2024-001"
                       class="w-full bg-slate-50 border border-slate-200 p-2.5 rounded-lg text-xs focus:ring-1 focus:ring-slate-900">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1 uppercase tracking-wider">Tanggal Diterima *</label>
                <input type="date" name="received_date" required value="{{ date('Y-m-d') }}"
                       class="w-full bg-slate-50 border border-slate-200 p-2.5 rounded-lg text-xs focus:ring-1 focus:ring-slate-900">
            </div>
        </div>

        <div>
            <label class="block font-bold text-slate-700 mb-1 uppercase tracking-wider">Catatan Tambahan</label>
            <textarea name="notes" rows="2" placeholder="Kondisi kain mulus, jahitan rapi batch 1..."
                      class="w-full bg-slate-50 border border-slate-200 p-2.5 rounded-lg text-xs focus:ring-1 focus:ring-slate-900"></textarea>
        </div>

        <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
            <a href="{{ route('admin.stock-ins.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-xs font-bold text-slate-700 hover:bg-slate-100 transition">
                Batal
            </a>
            <button type="submit" class="px-5 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg text-xs font-bold transition shadow-sm">
                Simpan & Tambah Stok
            </button>
        </div>
    </form>
</div>
@endsection
