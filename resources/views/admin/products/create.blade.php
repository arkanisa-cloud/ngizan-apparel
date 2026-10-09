@extends('layouts.admin')

@section('title', 'Tambah Kit Jersey Baru · NGIZAN APPAREL')

@section('content')
<div class="max-w-4xl space-y-6" x-data="{
    allowNameset: true,
    variants: [
        { size: '', stock: 0, price_adj: 0 }
    ],
    addVariant() {
        this.variants.push({ size: '', stock: 0, price_adj: 0 });
    },
    removeVariant(index) {
        if (this.variants.length > 1) {
            this.variants.splice(index, 1);
        }
    }
}">
    
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-hairline-soft pb-5">
        <div>
            <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-mute block mb-1">Master Data</span>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-ink">Tambah Kit Jersey Baru</h1>
            <p class="text-xs text-mute mt-1">Lengkapi spesifikasi jersey, unggah foto tampak depan/belakang, dan tentukan stok awal varian.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="px-5 py-2.5 bg-soft-cloud hover:bg-neutral-200 text-ink rounded-full text-xs font-medium transition self-start sm:self-auto flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali</span>
        </a>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- 1. INFORMASI UTAMA --}}
        <div class="bg-white p-6 rounded-2xl border border-hairline-soft space-y-4 text-xs">
            <h2 class="font-medium text-sm text-ink border-b border-hairline-soft pb-3">1. Informasi Utama Produk</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Nama Kit / Jersey *</label>
                    <input type="text" name="name" required value="{{ old('name') }}" placeholder="Real Madrid 2024/2025 Home Authentic Kit"
                           class="w-full bg-soft-cloud border border-hairline p-2.5 rounded-xl text-xs text-ink focus:outline-none focus:border-ink">
                </div>

                <div>
                    <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Kategori Edisi *</label>
                    <div class="relative" x-data="{ 
                        open: false, 
                        selected: '{{ old('category_id', '') }}',
                        categories: @js($categories),
                        get currentLabel() {
                            const found = this.categories.find(c => c.id == this.selected);
                            return found ? found.name : 'Pilih Kategori';
                        }
                    }">
                        <input type="hidden" name="category_id" :value="selected" required>

                        <button type="button" 
                            @click="open = !open" 
                            @keydown.escape="open = false"
                            class="w-full flex items-center justify-between bg-soft-cloud hover:bg-neutral-200 border border-hairline text-ink text-xs font-semibold rounded-2xl px-4 py-2.5 transition focus:outline-none focus:border-ink shadow-2xs cursor-pointer">
                            <span x-text="currentLabel"></span>
                            <svg class="w-4 h-4 text-mute transition-transform duration-200"
                                :class="open ? 'rotate-180 text-ink' : ''" 
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" 
                            @click.away="open = false" 
                            x-cloak
                            x-transition:enter="transition ease-out duration-150 transform"
                            x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100 transform"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                            class="absolute left-0 right-0 mt-2 bg-white border border-hairline-soft rounded-2xl py-1.5 shadow-xl z-40 text-xs overflow-hidden max-h-60 overflow-y-auto backdrop-blur-xl">
                            
                            <template x-for="cat in categories" :key="cat.id">
                                <button type="button" 
                                    @click="selected = cat.id; open = false"
                                    class="w-full flex items-center justify-between px-4 py-2.5 transition text-left cursor-pointer"
                                    :class="selected == cat.id ? 'bg-soft-cloud text-ink font-bold' : 'text-neutral-600 hover:text-ink hover:bg-soft-cloud/70 font-medium'">
                                    <span x-text="cat.name"></span>
                                    <span x-show="selected == cat.id" class="text-ink font-bold">✓</span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Template Panduan Ukuran (Size Chart)</label>
                    <div class="relative" x-data="{ 
                        open: false, 
                        selected: '{{ old('size_chart_id', '') }}',
                        sizeCharts: @js($sizeCharts),
                        get currentLabel() {
                            if (!this.selected) return 'Tanpa Panduan Ukuran (Tidak Ditampilkan)';
                            const found = this.sizeCharts.find(sc => sc.id == this.selected);
                            return found ? found.name : 'Tanpa Panduan Ukuran (Tidak Ditampilkan)';
                        }
                    }">
                        <input type="hidden" name="size_chart_id" :value="selected">

                        <button type="button" 
                            @click="open = !open" 
                            @keydown.escape="open = false"
                            class="w-full flex items-center justify-between bg-soft-cloud hover:bg-neutral-200 border border-hairline text-ink text-xs font-semibold rounded-2xl px-4 py-2.5 transition focus:outline-none focus:border-ink shadow-2xs cursor-pointer">
                            <span x-text="currentLabel"></span>
                            <svg class="w-4 h-4 text-mute transition-transform duration-200"
                                :class="open ? 'rotate-180 text-ink' : ''" 
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" 
                            @click.away="open = false" 
                            x-cloak
                            x-transition:enter="transition ease-out duration-150 transform"
                            x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100 transform"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                            class="absolute left-0 right-0 mt-2 bg-white border border-hairline-soft rounded-2xl py-1.5 shadow-xl z-40 text-xs overflow-hidden max-h-60 overflow-y-auto backdrop-blur-xl">
                            
                            <button type="button" 
                                @click="selected = ''; open = false"
                                class="w-full flex items-center justify-between px-4 py-2.5 transition text-left cursor-pointer border-b border-hairline-soft/60"
                                :class="!selected ? 'bg-soft-cloud text-ink font-bold' : 'text-neutral-600 hover:text-ink hover:bg-soft-cloud/70 font-medium'">
                                <span>Tanpa Panduan Ukuran (Tidak Ditampilkan)</span>
                                <span x-show="!selected" class="text-ink font-bold">✓</span>
                            </button>

                            <template x-for="sc in sizeCharts" :key="sc.id">
                                <button type="button" 
                                    @click="selected = sc.id; open = false"
                                    class="w-full flex items-center justify-between px-4 py-2.5 transition text-left cursor-pointer"
                                    :class="selected == sc.id ? 'bg-soft-cloud text-ink font-bold' : 'text-neutral-600 hover:text-ink hover:bg-soft-cloud/70 font-medium'">
                                    <span x-text="sc.name"></span>
                                    <span x-show="selected == sc.id" class="text-ink font-bold">✓</span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Harga Dasar (Rp) *</label>
                    <input type="number" name="base_price" required value="{{ old('base_price', 299000) }}" min="0" step="1000"
                           class="w-full bg-soft-cloud border border-hairline p-2.5 rounded-xl text-xs text-ink focus:outline-none focus:border-ink">
                </div>

                <div>
                    <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Estimasi Berat (Gram) *</label>
                    <input type="number" name="weight_grams" required value="{{ old('weight_grams', 250) }}" min="50"
                           class="w-full bg-soft-cloud border border-hairline p-2.5 rounded-xl text-xs text-ink focus:outline-none focus:border-ink">
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Deskripsi Material & Detail Produk</label>
                    <textarea name="description" rows="3" placeholder="Material berpori mikro DRI-FIT, bordir emblem HD, heat press 160°C..."
                              class="w-full bg-soft-cloud border border-hairline p-2.5 rounded-xl text-xs text-ink focus:outline-none focus:border-ink">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        {{-- 2. DUAL POV IMAGE UPLOAD (WEBP AUTO-COMPRESSION) --}}
        <div class="bg-white p-6 rounded-2xl border border-hairline-soft space-y-4 text-xs" x-data="{ frontPreview: null, backPreview: null }">
            <h2 class="font-medium text-sm text-ink border-b border-hairline-soft pb-3">2. Foto Dual POV Jersey (Auto WebP 82%)</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Foto Tampak Depan (Front POV)</label>
                    
                    {{-- Preview Container --}}
                    <div class="mb-3" x-show="frontPreview" x-cloak>
                        <div class="relative w-32 h-40 rounded-xl overflow-hidden border border-hairline bg-soft-cloud">
                            <img :src="frontPreview" alt="Preview Tampak Depan" class="w-full h-full object-cover">
                        </div>
                    </div>

                    <input type="file" name="thumbnail_front" id="thumbnail_front" accept="image/*"
                           @change="const file = $event.target.files[0]; if(file) { const reader = new FileReader(); reader.onload = (e) => { frontPreview = e.target.result; }; reader.readAsDataURL(file); }"
                           class="w-full bg-soft-cloud border border-hairline p-2.5 rounded-xl text-xs text-ink file:mr-4 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-medium file:bg-ink file:text-white hover:file:opacity-80 cursor-pointer">
                    <p class="text-[11px] text-mute mt-1">Digunakan untuk tampilan default etalase toko (JPG, PNG, WEBP Maks. 10MB, auto resize & WebP).</p>
                    @error('thumbnail_front') <p class="text-sale text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Foto Tampak Belakang (Back POV / Studio Mockup)</label>
                    
                    {{-- Preview Container --}}
                    <div class="mb-3" x-show="backPreview" x-cloak>
                        <div class="relative w-32 h-40 rounded-xl overflow-hidden border border-hairline bg-soft-cloud">
                            <img :src="backPreview" alt="Preview Tampak Belakang" class="w-full h-full object-cover">
                        </div>
                    </div>

                    <input type="file" name="thumbnail_back" id="thumbnail_back" accept="image/*"
                           @change="const file = $event.target.files[0]; if(file) { const reader = new FileReader(); reader.onload = (e) => { backPreview = e.target.result; }; reader.readAsDataURL(file); }"
                           class="w-full bg-soft-cloud border border-hairline p-2.5 rounded-xl text-xs text-ink file:mr-4 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-medium file:bg-ink file:text-white hover:file:opacity-80 cursor-pointer">
                    <p class="text-[11px] text-mute mt-1">Sangat penting untuk live overlay teks Sablon Nameset 2D Studio (Maks. 10MB, auto resize & WebP).</p>
                    @error('thumbnail_back') <p class="text-sale text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- 3. FITUR KUSTOMISASI SABLON --}}
        <div class="bg-white p-6 rounded-2xl border border-hairline-soft space-y-4 text-xs">
            <h2 class="font-medium text-sm text-ink border-b border-hairline-soft pb-3">3. Konfigurasi Kustom Sablon Nameset</h2>

            <div class="max-w-md">
                <div class="space-y-3 p-4 bg-soft-cloud rounded-xl border border-hairline-soft">
                    <label class="flex items-center gap-2 font-medium text-ink cursor-pointer">
                        <input type="checkbox" name="allow_custom_nameset" value="1" x-model="allowNameset" class="rounded text-ink focus:ring-ink">
                        <span>Aktifkan Kustom Sablon Nameset</span>
                    </label>
                    <div x-show="allowNameset">
                        <label class="block text-mute font-medium mb-1 text-[11px]">Biaya Tambahan Sablon (Rp)</label>
                        <input type="number" name="custom_nameset_price" value="{{ old('custom_nameset_price', 50000) }}" step="1000"
                               class="w-full bg-white border border-hairline p-2 rounded-lg text-xs text-ink focus:outline-none focus:border-ink">
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. MATRIKS VARIAN UKURAN & STOK AWAL --}}
        <div class="bg-white p-6 rounded-2xl border border-hairline-soft space-y-4 text-xs">
            <div class="flex justify-between items-center border-b border-hairline-soft pb-3">
                <div>
                    <h2 class="font-medium text-sm text-ink">4. Varian Ukuran & Stok Awal</h2>
                    <p class="text-mute text-[11px]">Tambahkan ukuran produk bebas (misal: S, M, L, XL, XXL, 28, 30, All Size, dll).</p>
                </div>
                <button type="button" @click="addVariant()" class="px-4 py-2 bg-soft-cloud hover:bg-neutral-200 text-ink rounded-full text-xs font-medium transition cursor-pointer">
                    + Tambah Ukuran / Varian
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(v, index) in variants" :key="index">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 p-3.5 bg-soft-cloud border border-hairline-soft rounded-xl">
                        <div class="flex-1 min-w-[150px]">
                            <label class="block text-[10px] font-medium text-mute uppercase mb-1">Ukuran *</label>
                            <input type="text" :name="'variants[' + index + '][size]'" x-model="v.size" placeholder="S, M, L, XL, 28, 30, All Size..." required
                                   class="w-full bg-white border border-hairline p-2 rounded-lg text-xs text-ink font-semibold focus:outline-none focus:border-ink">
                        </div>

                        <div class="w-full sm:w-36">
                            <label class="block text-[10px] font-medium text-mute uppercase mb-1">Stok Awal *</label>
                            <input type="number" :name="'variants[' + index + '][stock]'" x-model="v.stock" min="0" required
                                   class="w-full bg-white border border-hairline p-2 rounded-lg text-xs text-ink focus:outline-none focus:border-ink">
                        </div>

                        <div class="w-full sm:w-44">
                            <label class="block text-[10px] font-medium text-mute uppercase mb-1">Penyesuaian Harga</label>
                            <input type="number" :name="'variants[' + index + '][price_adj]'" x-model="v.price_adj" min="0" step="1000"
                                   class="w-full bg-white border border-hairline p-2 rounded-lg text-xs text-ink focus:outline-none focus:border-ink" placeholder="+Rp 0">
                        </div>

                        <div class="self-end sm:self-auto sm:pt-4">
                            <button type="button" @click="removeVariant(index)" class="p-2 text-mute hover:text-sale rounded-full transition cursor-pointer" title="Hapus Baris">
                                ✕
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('admin.products.index') }}" class="px-5 py-2.5 bg-soft-cloud hover:bg-neutral-200 rounded-full text-xs font-medium text-ink transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-ink hover:opacity-90 text-white rounded-full text-xs font-medium transition cursor-pointer">
                Simpan & Rilis ke Katalog
            </button>
        </div>

    </form>
</div>
@endsection
