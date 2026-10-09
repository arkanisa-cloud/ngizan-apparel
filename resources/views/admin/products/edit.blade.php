@extends('layouts.admin')

@section('title', 'Edit Produk: ' . $product->name . ' · NGIZAN APPAREL')

@section('content')
<div class="max-w-4xl space-y-6">
    
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-hairline-soft pb-5">
        <div>
            <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-mute block mb-1">Master Data</span>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-ink">Edit Kit Jersey</h1>
            <p class="text-xs text-mute mt-1">Perbarui informasi harga, status aktif, foto POV, dan konfigurasi sablon/patch.</p>
        </div>
        <a href="{{ route('admin.products.index') }}" class="px-5 py-2.5 bg-soft-cloud hover:bg-neutral-200 text-ink rounded-full text-xs font-medium transition self-start sm:self-auto flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali</span>
        </a>
    </div>

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- 1. INFORMASI UTAMA --}}
        <div class="bg-white p-6 rounded-2xl border border-hairline-soft space-y-4 text-xs">
            <h2 class="font-medium text-sm text-ink border-b border-hairline-soft pb-3">1. Informasi Utama Produk</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Nama Kit / Jersey *</label>
                    <input type="text" name="name" required value="{{ old('name', $product->name) }}"
                           class="w-full bg-soft-cloud border border-hairline p-2.5 rounded-xl text-xs text-ink focus:outline-none focus:border-ink">
                </div>

                <div>
                    <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Kategori Edisi *</label>
                    <div class="relative" x-data="{ 
                        open: false, 
                        selected: '{{ old('category_id', $product->category_id) }}',
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
                        selected: '{{ old('size_chart_id', $product->size_chart_id ?? '') }}',
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
                    <input type="number" name="base_price" required value="{{ old('base_price', (int)$product->base_price) }}" min="0" step="1000"
                           class="w-full bg-soft-cloud border border-hairline p-2.5 rounded-xl text-xs text-ink focus:outline-none focus:border-ink">
                </div>

                <div>
                    <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Estimasi Berat (Gram) *</label>
                    <input type="number" name="weight_grams" required value="{{ old('weight_grams', $product->weight_grams) }}" min="50"
                           class="w-full bg-soft-cloud border border-hairline p-2.5 rounded-xl text-xs text-ink focus:outline-none focus:border-ink">
                </div>

                <div>
                    <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Status Publikasi</label>
                    <label class="flex items-center gap-2 font-medium text-ink cursor-pointer pt-2">
                        <input type="checkbox" name="is_active" value="1" {{ $product->is_active ? 'checked' : '' }} class="rounded text-ink focus:ring-ink">
                        <span>Tampilkan di Toko (Aktif)</span>
                    </label>
                </div>

                <div class="sm:col-span-2">
                    <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Deskripsi Material & Detail Produk</label>
                    <textarea name="description" rows="3"
                              class="w-full bg-soft-cloud border border-hairline p-2.5 rounded-xl text-xs text-ink focus:outline-none focus:border-ink">{{ old('description', $product->description) }}</textarea>
                </div>
            </div>
        </div>

        {{-- 2. DUAL POV IMAGE UPLOAD --}}
        <div class="bg-white p-6 rounded-2xl border border-hairline-soft space-y-4 text-xs">
            <h2 class="font-medium text-sm text-ink border-b border-hairline-soft pb-3">2. Foto Dual POV Jersey</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Foto Tampak Depan (Front POV)</label>
                    @if($product->thumbnail_front)
                        <img src="{{ asset('storage/' . $product->thumbnail_front) }}" alt="Front POV" class="w-24 h-28 object-cover rounded-xl border border-hairline-soft mb-2">
                    @endif
                    <input type="file" name="thumbnail_front" accept="image/*" class="w-full text-xs text-mute file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-medium file:bg-soft-cloud file:text-ink hover:file:bg-neutral-200 cursor-pointer">
                    <p class="text-[11px] text-mute mt-1">Kosongkan jika tidak diganti (JPG, PNG, WEBP Maks. 10MB, auto resize & WebP).</p>
                </div>

                <div>
                    <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Foto Tampak Belakang (Back POV / Studio)</label>
                    @if($product->thumbnail_back)
                        <img src="{{ asset('storage/' . $product->thumbnail_back) }}" alt="Back POV" class="w-24 h-28 object-cover rounded-xl border border-hairline-soft mb-2">
                    @endif
                    <input type="file" name="thumbnail_back" accept="image/*" class="w-full text-xs text-mute file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-medium file:bg-soft-cloud file:text-ink hover:file:bg-neutral-200 cursor-pointer">
                    <p class="text-[11px] text-mute mt-1">Kosongkan jika tidak diganti (Maks. 10MB, auto resize & WebP).</p>
                </div>
            </div>
        </div>

        {{-- 3. FITUR KUSTOMISASI SABLON --}}
        <div class="bg-white p-6 rounded-2xl border border-hairline-soft space-y-4 text-xs">
            <h2 class="font-medium text-sm text-ink border-b border-hairline-soft pb-3">3. Konfigurasi Kustom Sablon Nameset</h2>

            <div class="max-w-md">
                <div class="space-y-3 p-4 bg-soft-cloud rounded-xl border border-hairline-soft">
                    <label class="flex items-center gap-2 font-medium text-ink cursor-pointer">
                        <input type="checkbox" name="allow_custom_nameset" value="1" {{ $product->allow_custom_nameset ? 'checked' : '' }} class="rounded text-ink focus:ring-ink">
                        <span>Aktifkan Kustom Sablon Nameset</span>
                    </label>
                    <div>
                        <label class="block text-mute font-medium mb-1 text-[11px]">Biaya Tambahan Sablon (Rp)</label>
                        <input type="number" name="custom_nameset_price" value="{{ old('custom_nameset_price', (int)$product->custom_nameset_price) }}" step="1000"
                               class="w-full bg-white border border-hairline p-2 rounded-lg text-xs text-ink focus:outline-none focus:border-ink">
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. DAFTAR VARIAN & STOK (READONLY) --}}
        <div class="bg-white p-6 rounded-2xl border border-hairline-soft space-y-4 text-xs">
            <div class="flex justify-between items-center border-b border-hairline-soft pb-3">
                <div>
                    <h2 class="font-medium text-sm text-ink">4. Varian Ukuran & Stok Saat Ini</h2>
                    <p class="text-mute text-[11px]">Untuk menambah atau mengurangi stok, gunakan modul Stok Masuk / Stok Keluar.</p>
                </div>
                <a href="{{ route('admin.stock-ins.create') }}" class="text-xs font-medium text-ink underline">+ Restock Masuk</a>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                @foreach($product->variants as $variant)
                    <div class="p-3 bg-soft-cloud border border-hairline-soft rounded-xl text-center space-y-1">
                        <span class="font-medium text-xs text-ink block">{{ $variant->size }} ({{ $variant->type }})</span>
                        <span class="font-mono text-[10px] text-mute block">{{ $variant->sku }}</span>
                        <div class="font-medium text-base {{ $variant->stock <= 3 ? 'text-sale' : 'text-ink' }}">
                            {{ $variant->stock }} pcs
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('admin.products.index') }}" class="px-5 py-2.5 bg-soft-cloud hover:bg-neutral-200 rounded-full text-xs font-medium text-ink transition">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-ink hover:opacity-90 text-white rounded-full text-xs font-medium transition cursor-pointer">
                Simpan Perubahan
            </button>
        </div>

    </form>
</div>
@endsection
