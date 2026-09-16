@extends('layouts.admin')

@section('title', 'Edit Produk: ' . $product->name . ' · NGIZAN APPAREL')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    {{-- Breadcrumb & Title --}}
    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-xl font-bold font-display text-slate-900">Edit Kit Jersey</h1>
            <p class="text-xs text-slate-500 mt-0.5">Perbarui informasi harga, status aktif, foto POV, dan konfigurasi sablon/patch.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900">&larr; Kembali</a>
    </div>

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- 1. INFORMASI UTAMA --}}
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4 text-xs">
            <h2 class="font-bold text-sm text-slate-900 border-b border-slate-100 pb-3">1. Informasi Utama Produk</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1 uppercase tracking-wider">Nama Kit / Jersey *</label>
                    <input type="text" name="name" required value="{{ old('name', $product->name) }}"
                           class="w-full bg-slate-50 border border-slate-200 p-2.5 rounded-lg text-xs focus:ring-1 focus:ring-slate-900">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1 uppercase tracking-wider">Kategori Edisi *</label>
                    <select name="category_id" required class="w-full bg-slate-50 border border-slate-200 p-2.5 rounded-lg text-xs focus:ring-1 focus:ring-slate-900">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1 uppercase tracking-wider">Harga Dasar (Rp) *</label>
                    <input type="number" name="base_price" required value="{{ old('base_price', (int)$product->base_price) }}" min="0" step="1000"
                           class="w-full bg-slate-50 border border-slate-200 p-2.5 rounded-lg text-xs focus:ring-1 focus:ring-slate-900">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1 uppercase tracking-wider">Estimasi Berat (Gram) *</label>
                    <input type="number" name="weight_grams" required value="{{ old('weight_grams', $product->weight_grams) }}" min="50"
                           class="w-full bg-slate-50 border border-slate-200 p-2.5 rounded-lg text-xs focus:ring-1 focus:ring-slate-900">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1 uppercase tracking-wider">Status Publikasi</label>
                    <label class="flex items-center gap-2 font-bold text-slate-900 cursor-pointer pt-2">
                        <input type="checkbox" name="is_active" value="1" {{ $product->is_active ? 'checked' : '' }} class="rounded text-cyan-600 focus:ring-cyan-500">
                        <span>Tampilkan di Toko (Aktif)</span>
                    </label>
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1 uppercase tracking-wider">Deskripsi Material & Detail Produk</label>
                    <textarea name="description" rows="3"
                              class="w-full bg-slate-50 border border-slate-200 p-2.5 rounded-lg text-xs focus:ring-1 focus:ring-slate-900">{{ old('description', $product->description) }}</textarea>
                </div>
            </div>
        </div>

        {{-- 2. DUAL POV IMAGE UPLOAD --}}
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4 text-xs">
            <h2 class="font-bold text-sm text-slate-900 border-b border-slate-100 pb-3">2. Foto Dual POV Jersey</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block font-bold text-slate-700 mb-1 uppercase tracking-wider">Foto Tampak Depan (Front POV)</label>
                    @if($product->thumbnail_front)
                        <img src="{{ asset('storage/' . $product->thumbnail_front) }}" alt="Front POV" class="w-24 h-28 object-cover rounded border border-slate-200 mb-2">
                    @endif
                    <input type="file" name="thumbnail_front" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1 uppercase tracking-wider">Foto Tampak Belakang (Back POV / Studio)</label>
                    @if($product->thumbnail_back)
                        <img src="{{ asset('storage/' . $product->thumbnail_back) }}" alt="Back POV" class="w-24 h-28 object-cover rounded border border-slate-200 mb-2">
                    @endif
                    <input type="file" name="thumbnail_back" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700">
                </div>
            </div>
        </div>

        {{-- 3. FITUR KUSTOMISASI & PATCH --}}
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4 text-xs">
            <h2 class="font-bold text-sm text-slate-900 border-b border-slate-100 pb-3">3. Konfigurasi Sablon Nameset & Patch</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="space-y-3 p-4 bg-slate-50 rounded-lg border border-slate-200">
                    <label class="flex items-center gap-2 font-bold text-slate-900 cursor-pointer">
                        <input type="checkbox" name="allow_custom_nameset" value="1" {{ $product->allow_custom_nameset ? 'checked' : '' }} class="rounded text-cyan-600 focus:ring-cyan-500">
                        <span>Aktifkan Kustom Sablon Nameset</span>
                    </label>
                    <div>
                        <label class="block text-slate-600 font-semibold mb-1 text-[11px]">Biaya Tambahan Sablon (Rp)</label>
                        <input type="number" name="custom_nameset_price" value="{{ old('custom_nameset_price', (int)$product->custom_nameset_price) }}" step="1000"
                               class="w-full bg-white border border-slate-200 p-2 rounded text-xs">
                    </div>
                </div>

                <div class="space-y-3 p-4 bg-slate-50 rounded-lg border border-slate-200">
                    <label class="flex items-center gap-2 font-bold text-slate-900 cursor-pointer">
                        <input type="checkbox" name="allow_patch" value="1" {{ $product->allow_patch ? 'checked' : '' }} class="rounded text-cyan-600 focus:ring-cyan-500">
                        <span>Aktifkan Pilihan Patch Turnamen</span>
                    </label>
                    <div>
                        <label class="block text-slate-600 font-semibold mb-1 text-[11px]">Biaya Tambahan Patch (Rp)</label>
                        <input type="number" name="patch_price" value="{{ old('patch_price', (int)$product->patch_price) }}" step="1000"
                               class="w-full bg-white border border-slate-200 p-2 rounded text-xs">
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. DAFTAR VARIAN & STOK (READONLY) --}}
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4 text-xs">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <div>
                    <h2 class="font-bold text-sm text-slate-900">4. Varian Ukuran & Stok Saat Ini</h2>
                    <p class="text-slate-400 text-[11px]">Untuk menambah atau mengurangi stok, gunakan modul Stok Masuk / Stok Keluar.</p>
                </div>
                <a href="{{ route('admin.stock-ins.create') }}" class="text-xs font-bold text-cyan-600 hover:underline">+ Restock Masuk</a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                @foreach($product->variants as $variant)
                    <div class="p-3 bg-slate-50 border border-slate-200 rounded-lg text-center space-y-1">
                        <span class="font-bold text-xs text-slate-900 block">{{ $variant->size }} ({{ $variant->type }})</span>
                        <span class="font-mono text-[10px] text-slate-400 block">{{ $variant->sku }}</span>
                        <div class="font-display font-black text-base {{ $variant->stock <= 3 ? 'text-rose-600' : 'text-slate-900' }}">
                            {{ $variant->stock }} pcs
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('admin.products.index') }}" class="px-5 py-2.5 border border-slate-300 rounded-lg text-xs font-bold text-slate-700 hover:bg-slate-100 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg text-xs font-bold transition shadow-md">
                Simpan Perubahan
            </button>
        </div>

    </form>
</div>
@endsection
