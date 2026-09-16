@extends('layouts.admin')

@section('title', 'Catat Stok Keluar · NGIZAN APPAREL')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-xl font-bold font-display text-slate-900">Catat Penyesuaian Stok Keluar</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kurangi kuantitas stok akibat barang cacat, sampel display, atau promosi.</p>
        </div>
        <a href="{{ route('admin.stock-outs.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900">&larr; Kembali</a>
    </div>

    <form action="{{ route('admin.stock-outs.store') }}" method="POST" class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4 text-xs">
        @csrf

        <div>
            <label class="block font-bold text-slate-700 mb-1 uppercase tracking-wider">Pilih Jersey & Varian Ukuran *</label>
            <select name="product_variant_id" required class="w-full bg-slate-50 border border-slate-200 p-2.5 rounded-lg text-xs focus:ring-1 focus:ring-slate-900">
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
                <label class="block font-bold text-slate-700 mb-1 uppercase tracking-wider">Jumlah Qty Keluar (Pcs) *</label>
                <input type="number" name="quantity" required min="1" value="1"
                       class="w-full bg-slate-50 border border-slate-200 p-2.5 rounded-lg text-xs focus:ring-1 focus:ring-slate-900">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1 uppercase tracking-wider">Alasan Pengurangan *</label>
                <select name="reason" required class="w-full bg-slate-50 border border-slate-200 p-2.5 rounded-lg text-xs focus:ring-1 focus:ring-slate-900">
                    <option value="Damaged">Barang Rusak / Cacat Sablon (Damaged)</option>
                    <option value="Sample">Sampel Display Workshop (Sample)</option>
                    <option value="Promotion">Promosi Influencer / Give Away (Promotion)</option>
                    <option value="Loss">Selisih Opname / Hilang (Loss)</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1 uppercase tracking-wider">Tanggal Keluar *</label>
                <input type="date" name="out_date" required value="{{ date('Y-m-d') }}"
                       class="w-full bg-slate-50 border border-slate-200 p-2.5 rounded-lg text-xs focus:ring-1 focus:ring-slate-900">
            </div>
        </div>

        <div>
            <label class="block font-bold text-slate-700 mb-1 uppercase tracking-wider">Catatan Tambahan</label>
            <textarea name="notes" rows="2" placeholder="Cacat press logo pada dada kiri..."
                      class="w-full bg-slate-50 border border-slate-200 p-2.5 rounded-lg text-xs focus:ring-1 focus:ring-slate-900"></textarea>
        </div>

        <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
            <a href="{{ route('admin.stock-outs.index') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-xs font-bold text-slate-700 hover:bg-slate-100 transition">
                Batal
            </a>
            <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-bold transition shadow-sm">
                Simpan & Kurangi Stok
            </button>
        </div>
    </form>
</div>
@endsection
