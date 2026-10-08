@extends('layouts.admin')

@section('title', 'Kelola Gambar Hero & Banner · NGIZAN APPAREL')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-hairline-soft pb-5">
        <div>
            <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-mute block mb-1">Master Data</span>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-ink">Gambar Hero & Banner</h1>
            <p class="text-xs text-mute mt-1">Ganti gambar visual Hero Section dan Banner Promo etalase beranda.</p>
        </div>
        <a href="{{ route('home') }}" target="_blank" class="px-5 py-2.5 bg-soft-cloud hover:bg-neutral-200 text-ink rounded-full text-xs font-medium transition flex items-center gap-1.5 self-start sm:self-auto">
            <span>Lihat Live Beranda</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
        </a>
    </div>

    {{-- Grid 2 Banner: Hero Section & Promo Banner --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        {{-- 1. Hero Section --}}
        <div class="bg-white rounded-2xl border border-hairline-soft p-6 sm:p-7 space-y-5" x-data="{ imagePreview: null }">
            <div class="flex items-center justify-between border-b border-hairline-soft pb-3">
                <div>
                    <h2 class="font-bold text-base text-ink uppercase tracking-wide">1. Hero Section Utama</h2>
                    <p class="text-[11px] text-mute">Gambar full-bleed (125vh) di bagian paling atas beranda.</p>
                </div>
                <span class="px-3 py-1 bg-ink text-white text-[10px] font-bold uppercase rounded-full tracking-wider">
                    Hero Section
                </span>
            </div>

            {{-- Image Preview Area --}}
            <div class="space-y-2">
                <span class="text-[11px] text-mute font-medium block">Tampilan Gambar Saat Ini:</span>
                <div class="relative w-full aspect-video bg-neutral-900 rounded-xl overflow-hidden border border-hairline shadow-inner">
                    <img x-show="!imagePreview" src="{{ $heroBanner->image_url }}" alt="Hero Section" class="w-full h-full object-cover">
                    
                    <template x-if="imagePreview">
                        <img :src="imagePreview" alt="Preview Baru" class="w-full h-full object-cover">
                    </template>

                    <div class="absolute bottom-3 left-3 right-3 pointer-events-none text-white text-xs font-bold drop-shadow">
                        <span x-show="!imagePreview" class="bg-black/60 px-3 py-1 rounded-full text-[10px] uppercase tracking-wider">Gambar Aktif</span>
                        <span x-show="imagePreview" class="bg-amber-600 px-3 py-1 rounded-full text-[10px] uppercase tracking-wider text-white">Preview Gambar Baru</span>
                    </div>
                </div>
            </div>

            {{-- Form Upload --}}
            <form action="{{ route('admin.banners.update', $heroBanner) }}" method="POST" enctype="multipart/form-data" class="space-y-4 pt-2">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-medium text-mute uppercase tracking-wider text-[11px] mb-1.5">
                        Pilih Gambar Baru (JPG, PNG, WEBP)
                    </label>
                    <input type="file" name="image" accept="image/*" required
                           @change="const file = $event.target.files[0]; if(file) { const reader = new FileReader(); reader.onload = (e) => { imagePreview = e.target.result; }; reader.readAsDataURL(file); }"
                           class="w-full bg-soft-cloud border border-hairline p-2.5 rounded-xl text-xs text-ink file:mr-4 file:py-1.5 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-ink file:text-white hover:file:opacity-80 cursor-pointer">
                    <p class="text-[11px] text-mute mt-1">Rekomendasi resolusi tajam: 1920x1080 atau lebih (Maks. 5MB, otomatis dikonversi ke WebP).</p>
                    @error('image') <p class="text-sale text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end pt-3 border-t border-hairline-soft">
                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-ink hover:opacity-90 text-white rounded-full text-xs font-bold uppercase tracking-wider transition inline-flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Simpan Gambar Hero
                    </button>
                </div>
            </form>
        </div>

        {{-- 2. Promo Banner --}}
        <div class="bg-white rounded-2xl border border-hairline-soft p-6 sm:p-7 space-y-5" x-data="{ imagePreview: null }">
            <div class="flex items-center justify-between border-b border-hairline-soft pb-3">
                <div>
                    <h2 class="font-bold text-base text-ink uppercase tracking-wide">2. Banner Promo Tengah</h2>
                    <p class="text-[11px] text-mute">Banner horizontal lebar di tengah halaman beranda.</p>
                </div>
                <span class="px-3 py-1 bg-ink text-white text-[10px] font-bold uppercase rounded-full tracking-wider">
                    Promo Banner
                </span>
            </div>

            {{-- Image Preview Area --}}
            <div class="space-y-2">
                <span class="text-[11px] text-mute font-medium block">Tampilan Gambar Saat Ini:</span>
                <div class="relative w-full aspect-[21/7] bg-neutral-900 rounded-xl overflow-hidden border border-hairline shadow-inner flex items-center justify-center">
                    @if($promoBanner->image_url)
                        <img x-show="!imagePreview" src="{{ $promoBanner->image_url }}" alt="Promo Banner" class="w-full h-full object-cover">
                    @else
                        <div x-show="!imagePreview" class="text-center p-4">
                            <p class="text-neutral-400 text-xs">Belum ada gambar</p>
                            <p class="text-neutral-500 text-[10px] mt-0.5">Banner promo disembunyikan otomatis di beranda</p>
                        </div>
                    @endif
                    
                    <template x-if="imagePreview">
                        <img :src="imagePreview" alt="Preview Baru" class="w-full h-full object-cover">
                    </template>

                    <div class="absolute bottom-3 left-3 right-3 pointer-events-none text-white text-xs font-bold drop-shadow">
                        @if($promoBanner->image_url)
                            <span x-show="!imagePreview" class="bg-black/60 px-3 py-1 rounded-full text-[10px] uppercase tracking-wider">Gambar Aktif</span>
                        @endif
                        <span x-show="imagePreview" class="bg-amber-600 px-3 py-1 rounded-full text-[10px] uppercase tracking-wider text-white">Preview Gambar Baru</span>
                    </div>
                </div>
            </div>

            {{-- Form Upload --}}
            <form action="{{ route('admin.banners.update', $promoBanner) }}" method="POST" enctype="multipart/form-data" class="space-y-4 pt-2">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-medium text-mute uppercase tracking-wider text-[11px] mb-1.5">
                        Pilih Gambar Baru (JPG, PNG, WEBP)
                    </label>
                    <input type="file" name="image" accept="image/*" required
                           @change="const file = $event.target.files[0]; if(file) { const reader = new FileReader(); reader.onload = (e) => { imagePreview = e.target.result; }; reader.readAsDataURL(file); }"
                           class="w-full bg-soft-cloud border border-hairline p-2.5 rounded-xl text-xs text-ink file:mr-4 file:py-1.5 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-ink file:text-white hover:file:opacity-80 cursor-pointer">
                    <p class="text-[11px] text-mute mt-1">Rekomendasi format landscape memanjang: 1920x500 atau 2400x600 (Maks. 5MB, otomatis dikonversi ke WebP).</p>
                    @error('image') <p class="text-sale text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end pt-3 border-t border-hairline-soft">
                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-ink hover:opacity-90 text-white rounded-full text-xs font-bold uppercase tracking-wider transition inline-flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        Simpan Gambar Banner
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
