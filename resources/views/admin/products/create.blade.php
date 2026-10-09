@extends('layouts.admin')

@section('title', 'Tambah Kit Jersey Baru · NGIZAN APPAREL')

@section('content')
<div class="max-w-4xl space-y-6" x-data="{
    allowNameset: true,
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
                    <input type="text" name="name" required value="{{ old('name') }}" placeholder="Contoh: Real Madrid 2024/2025 Home Authentic Kit"
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
                        selected: '{{ old('size_chart_id', $sizeCharts->firstWhere('is_default', true)?->id ?? '') }}',
                        sizeCharts: @js($sizeCharts),
                        get currentLabel() {
                            if (!this.selected) return 'Gunakan Default Sistem';
                            const found = this.sizeCharts.find(sc => sc.id == this.selected);
                            return found ? found.name : 'Gunakan Default Sistem';
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
                                <span>Gunakan Default Sistem</span>
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
        <div class="bg-white p-6 rounded-2xl border border-hairline-soft space-y-4 text-xs">
            <h2 class="font-medium text-sm text-ink border-b border-hairline-soft pb-3">2. Foto Dual POV Jersey (Auto WebP 82%)</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Foto Tampak Depan (Front POV)</label>
                    <input type="file" name="thumbnail_front" accept="image/*" class="w-full text-xs text-mute file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-medium file:bg-soft-cloud file:text-ink hover:file:bg-neutral-200 cursor-pointer">
                    <p class="text-[11px] text-mute mt-1">Digunakan untuk tampilan default etalase toko (JPG, PNG, WEBP Maks. 10MB, auto resize & WebP).</p>
                </div>

                <div>
                    <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Foto Tampak Belakang (Back POV / Studio Mockup)</label>
                    <input type="file" name="thumbnail_back" accept="image/*" class="w-full text-xs text-mute file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-medium file:bg-soft-cloud file:text-ink hover:file:bg-neutral-200 cursor-pointer">
                    <p class="text-[11px] text-mute mt-1">Sangat penting untuk live overlay teks Sablon Nameset 2D Studio (Maks. 10MB, auto resize & WebP).</p>
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
                    <h2 class="font-medium text-sm text-ink">4. Matriks Varian Ukuran & Stok Awal</h2>
                    <p class="text-mute text-[11px]">Tentukan kombinasi ukuran, tipe jersey, dan stok fisik di gudang.</p>
                </div>
                <button type="button" @click="addVariant()" class="px-4 py-2 bg-soft-cloud hover:bg-neutral-200 text-ink rounded-full text-xs font-medium transition cursor-pointer">
                    + Tambah Baris Varian
                </button>
            </div>

            <div class="space-y-3">
                <template x-for="(v, index) in variants" :key="index">
                    <div class="flex items-center gap-3 p-3.5 bg-soft-cloud border border-hairline-soft rounded-xl">
                        <div class="w-28 relative" x-data="{ open: false }">
                            <label class="block text-[10px] font-medium text-mute uppercase mb-1">Ukuran</label>
                            <input type="hidden" :name="'variants[' + index + '][size]'" :value="v.size">
                            <button type="button" 
                                    @click="open = !open" 
                                    @click.outside="open = false" 
                                    class="w-full flex items-center justify-between gap-1.5 px-3 py-2 bg-white border border-hairline hover:border-ink rounded-xl text-xs font-semibold text-ink transition">
                                <span x-text="v.size"></span>
                                <svg class="w-3.5 h-3.5 text-mute transition-transform duration-200" :class="{ 'rotate-180 text-ink': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                                 class="absolute left-0 mt-1 w-full bg-white border border-hairline-soft rounded-xl py-1 shadow-xl z-50 text-xs overflow-hidden"
                                 style="display: none;">
                                <template x-for="s in ['S', 'M', 'L', 'XL', 'XXL', '3XL']" :key="s">
                                    <button type="button" 
                                            @click="v.size = s; open = false" 
                                            class="w-full text-left px-3 py-1.5 flex items-center justify-between hover:bg-soft-cloud transition cursor-pointer"
                                            :class="v.size === s ? 'bg-soft-cloud text-ink font-bold' : 'text-mute'">
                                        <span x-text="s"></span>
                                        <span x-show="v.size === s" class="text-ink font-bold">✓</span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <div class="w-36 relative" x-data="{ open: false }">
                            <label class="block text-[10px] font-medium text-mute uppercase mb-1">Tipe Rilis</label>
                            <input type="hidden" :name="'variants[' + index + '][type]'" :value="v.type">
                            <button type="button" 
                                    @click="open = !open" 
                                    @click.outside="open = false" 
                                    class="w-full flex items-center justify-between gap-1.5 px-3 py-2 bg-white border border-hairline hover:border-ink rounded-xl text-xs font-semibold text-ink transition">
                                <span x-text="v.type" class="truncate"></span>
                                <svg class="w-3.5 h-3.5 text-mute transition-transform duration-200 shrink-0" :class="{ 'rotate-180 text-ink': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave="transition ease-in duration-100"
                                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                 x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                                 class="absolute left-0 mt-1 w-full bg-white border border-hairline-soft rounded-xl py-1 shadow-xl z-50 text-xs overflow-hidden"
                                 style="display: none;">
                                <template x-for="t in ['Fans Issue', 'Player Issue', 'Retro']" :key="t">
                                    <button type="button" 
                                            @click="v.type = t; open = false" 
                                            class="w-full text-left px-3 py-1.5 flex items-center justify-between hover:bg-soft-cloud transition cursor-pointer"
                                            :class="v.type === t ? 'bg-soft-cloud text-ink font-bold' : 'text-mute'">
                                        <span x-text="t"></span>
                                        <span x-show="v.type === t" class="text-ink font-bold">✓</span>
                                    </button>
                                </template>
                            </div>
                        </div>

                        <div class="w-28">
                            <label class="block text-[10px] font-medium text-mute uppercase">Stok Awal</label>
                            <input type="number" :name="'variants[' + index + '][stock]'" x-model="v.stock" min="0" required
                                   class="w-full bg-white border border-hairline p-2 rounded-lg text-xs text-ink focus:outline-none focus:border-ink">
                        </div>

                        <div class="w-36">
                            <label class="block text-[10px] font-medium text-mute uppercase">Penyesuaian Harga</label>
                            <input type="number" :name="'variants[' + index + '][price_adj]'" x-model="v.price_adj" min="0" step="1000"
                                   class="w-full bg-white border border-hairline p-2 rounded-lg text-xs text-ink focus:outline-none focus:border-ink" placeholder="+Rp 0">
                        </div>

                        <div class="pt-4">
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
