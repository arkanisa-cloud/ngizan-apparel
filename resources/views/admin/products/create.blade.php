@extends('layouts.admin')

@section('title', 'Tambah Kit Jersey Baru · NGIZAN APPAREL')

@section('content')
<div class="max-w-4xl mx-auto space-y-6" x-data="{
    allowNameset: true,
    allowPatch: true,
    variants: [
        { size: 'S', type: 'Fans Issue', stock: 10, price_adj: 0 },
        { size: 'M', type: 'Fans Issue', stock: 15, price_adj: 0 },
        { size: 'L', type: 'Fans Issue', stock: 20, price_adj: 0 },
        { size: 'XL', type: 'Fans Issue', stock: 10, price_adj: 0 },
        { size: 'XXL', type: 'Fans Issue', stock: 5, price_adj: 0 },
        { size: 'L', type: 'Player Issue', stock: 8, price_adj: 50000 }
    ],
    addVariant() {
        this.variants.push({ size: 'M', type: 'Fans Issue', stock: 5, price_adj: 0 });
    },
    removeVariant(index) {
        if (this.variants.length > 1) {
            this.variants.splice(index, 1);
        }
    }
}">
    
    {{-- Breadcrumb & Title --}}
    <div class="flex items-center justify-between border-b border-slate-200 pb-4">
        <div>
            <h1 class="text-xl font-bold font-display text-slate-900">Tambah Kit Jersey Baru</h1>
            <p class="text-xs text-slate-500 mt-0.5">Lengkapi spesifikasi jersey, unggah foto WebP tampak depan/belakang, dan tentukan stok awal varian.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900">&larr; Kembali</a>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- 1. INFORMASI UTAMA --}}
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4 text-xs">
            <h2 class="font-bold text-sm text-slate-900 border-b border-slate-100 pb-3">1. Informasi Utama Produk</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1 uppercase tracking-wider">Nama Kit / Jersey *</label>
                    <input type="text" name="name" required value="{{ old('name') }}" placeholder="Contoh: Real Madrid 2024/2025 Home Authentic Kit"
                           class="w-full bg-slate-50 border border-slate-200 p-2.5 rounded-lg text-xs focus:ring-1 focus:ring-slate-900">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1 uppercase tracking-wider">Kategori Edisi *</label>
                    <select name="category_id" required class="w-full bg-slate-50 border border-slate-200 p-2.5 rounded-lg text-xs focus:ring-1 focus:ring-slate-900">
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1 uppercase tracking-wider">Harga Dasar (Rp) *</label>
                    <input type="number" name="base_price" required value="{{ old('base_price', 299000) }}" min="0" step="1000"
                           class="w-full bg-slate-50 border border-slate-200 p-2.5 rounded-lg text-xs focus:ring-1 focus:ring-slate-900">
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1 uppercase tracking-wider">Estimasi Berat (Gram) *</label>
                    <input type="number" name="weight_grams" required value="{{ old('weight_grams', 250) }}" min="50"
                           class="w-full bg-slate-50 border border-slate-200 p-2.5 rounded-lg text-xs focus:ring-1 focus:ring-slate-900">
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-bold text-slate-700 mb-1 uppercase tracking-wider">Deskripsi Material & Detail Produk</label>
                    <textarea name="description" rows="3" placeholder="Material berpori mikro DRI-FIT, bordir emblem HD, heat press 160°C..."
                              class="w-full bg-slate-50 border border-slate-200 p-2.5 rounded-lg text-xs focus:ring-1 focus:ring-slate-900">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        {{-- 2. DUAL POV IMAGE UPLOAD (WEBP AUTO-COMPRESSION) --}}
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4 text-xs">
            <h2 class="font-bold text-sm text-slate-900 border-b border-slate-100 pb-3">2. Foto Dual POV Jersey (Auto WebP 82%)</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block font-bold text-slate-700 mb-1 uppercase tracking-wider">Foto Tampak Depan (Front POV)</label>
                    <input type="file" name="thumbnail_front" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                    <p class="text-[10px] text-slate-400 mt-1">Digunakan untuk tampilan default etalase toko.</p>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1 uppercase tracking-wider">Foto Tampak Belakang (Back POV / Studio Mockup)</label>
                    <input type="file" name="thumbnail_back" accept="image/*" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                    <p class="text-[10px] text-slate-400 mt-1">Sangat penting untuk live overlay teks Sablon Nameset 2D Studio.</p>
                </div>
            </div>
        </div>

        {{-- 3. FITUR KUSTOMISASI & PATCH --}}
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4 text-xs">
            <h2 class="font-bold text-sm text-slate-900 border-b border-slate-100 pb-3">3. Konfigurasi Sablon Nameset & Patch</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="space-y-3 p-4 bg-slate-50 rounded-lg border border-slate-200">
                    <label class="flex items-center gap-2 font-bold text-slate-900 cursor-pointer">
                        <input type="checkbox" name="allow_custom_nameset" value="1" x-model="allowNameset" class="rounded text-cyan-600 focus:ring-cyan-500">
                        <span>Aktifkan Kustom Sablon Nameset</span>
                    </label>
                    <div x-show="allowNameset">
                        <label class="block text-slate-600 font-semibold mb-1 text-[11px]">Biaya Tambahan Sablon (Rp)</label>
                        <input type="number" name="custom_nameset_price" value="{{ old('custom_nameset_price', 50000) }}" step="1000"
                               class="w-full bg-white border border-slate-200 p-2 rounded text-xs">
                    </div>
                </div>

                <div class="space-y-3 p-4 bg-slate-50 rounded-lg border border-slate-200">
                    <label class="flex items-center gap-2 font-bold text-slate-900 cursor-pointer">
                        <input type="checkbox" name="allow_patch" value="1" x-model="allowPatch" class="rounded text-cyan-600 focus:ring-cyan-500">
                        <span>Aktifkan Pilihan Patch Turnamen</span>
                    </label>
                    <div x-show="allowPatch">
                        <label class="block text-slate-600 font-semibold mb-1 text-[11px]">Biaya Tambahan Patch (Rp)</label>
                        <input type="number" name="patch_price" value="{{ old('patch_price', 35000) }}" step="1000"
                               class="w-full bg-white border border-slate-200 p-2 rounded text-xs">
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. MATRIKS VARIAN UKURAN & STOK AWAL --}}
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4 text-xs">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <div>
                    <h2 class="font-bold text-sm text-slate-900">4. Matriks Varian Ukuran & Stok Awal</h2>
                    <p class="text-slate-400 text-[11px]">Tentukan kombinasi ukuran, tipe jersey, dan stok fisik di gudang.</p>
                </div>
                <button type="button" @click="addVariant()" class="px-3 py-1.5 bg-slate-900 text-white rounded text-xs font-bold hover:bg-slate-800 transition">
                    + Tambah Baris Varian
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(v, index) in variants" :key="index">
                    <div class="flex items-center gap-3 p-3 bg-slate-50 border border-slate-200 rounded-lg">
                        <div class="w-28">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase">Ukuran</label>
                            <select :name="'variants[' + index + '][size]'" x-model="v.size" class="w-full bg-white border border-slate-200 p-1.5 rounded text-xs">
                                <option value="S">S</option>
                                <option value="M">M</option>
                                <option value="L">L</option>
                                <option value="XL">XL</option>
                                <option value="XXL">XXL</option>
                                <option value="3XL">3XL</option>
                            </select>
                        </div>

                        <div class="w-36">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase">Tipe Rilis</label>
                            <select :name="'variants[' + index + '][type]'" x-model="v.type" class="w-full bg-white border border-slate-200 p-1.5 rounded text-xs">
                                <option value="Fans Issue">Fans Issue</option>
                                <option value="Player Issue">Player Issue</option>
                                <option value="Retro">Retro</option>
                            </select>
                        </div>

                        <div class="w-28">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase">Stok Awal</label>
                            <input type="number" :name="'variants[' + index + '][stock]'" x-model="v.stock" min="0" required
                                   class="w-full bg-white border border-slate-200 p-1.5 rounded text-xs">
                        </div>

                        <div class="w-36">
                            <label class="block text-[10px] font-bold text-slate-500 uppercase">Penyesuaian Harga</label>
                            <input type="number" :name="'variants[' + index + '][price_adj]'" x-model="v.price_adj" min="0" step="1000"
                                   class="w-full bg-white border border-slate-200 p-1.5 rounded text-xs" placeholder="+Rp 0">
                        </div>

                        <div class="pt-4">
                            <button type="button" @click="removeVariant(index)" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded" title="Hapus Baris">
                                ✕
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('admin.products.index') }}" class="px-5 py-2.5 border border-slate-300 rounded-lg text-xs font-bold text-slate-700 hover:bg-slate-100 transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg text-xs font-bold transition shadow-md">
                Simpan & Rilis ke Katalog
            </button>
        </div>

    </form>
</div>
@endsection
