@extends('layouts.admin')

@section('title', 'Edit Gambar ' . $banner->name . ' · NGIZAN APPAREL')

@section('content')
<div class="max-w-4xl space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-hairline-soft pb-5">
        <div>
            <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-mute block mb-1">Master Data</span>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-ink">Edit Gambar {{ $banner->name }}</h1>
            <p class="text-xs text-mute mt-1">Upload dan perbarui gambar untuk banner ini di halaman beranda.</p>
        </div>
        <a href="{{ route('admin.banners.index') }}" class="px-5 py-2.5 bg-soft-cloud hover:bg-neutral-200 text-ink rounded-full text-xs font-medium transition flex items-center gap-1.5 self-start sm:self-auto">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali</span>
        </a>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-hairline-soft" x-data="{ imagePreview: null }">
        <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-6 text-xs">
                
                {{-- Preview Area --}}
                <div class="space-y-2">
                    <span class="text-[11px] text-mute font-medium block">Tampilan Gambar:</span>
                    <div class="relative w-full aspect-video bg-neutral-900 rounded-xl overflow-hidden border border-hairline shadow-inner">
                        <img x-show="!imagePreview" src="{{ $banner->image_url }}" alt="{{ $banner->name }}" class="w-full h-full object-cover">
                        
                        <template x-if="imagePreview">
                            <img :src="imagePreview" alt="Preview Baru" class="w-full h-full object-cover">
                        </template>

                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent pointer-events-none"></div>
                        <div class="absolute bottom-3 left-3 right-3 pointer-events-none text-white text-xs font-bold drop-shadow">
                            <span x-show="!imagePreview">Gambar Aktif di Beranda</span>
                            <span x-show="imagePreview" class="text-amber-400">Preview Gambar Baru (Belum Disimpan)</span>
                        </div>
                    </div>
                </div>

                {{-- Upload Input --}}
                <div>
                    <label class="block font-medium text-mute uppercase tracking-wider text-[11px] mb-1.5">
                        Pilih File Gambar Baru <span class="text-sale">*</span>
                    </label>
                    <input type="file" name="image" accept="image/*" required
                           @change="const file = $event.target.files[0]; if(file) { const reader = new FileReader(); reader.onload = (e) => { imagePreview = e.target.result; }; reader.readAsDataURL(file); }"
                           class="w-full bg-soft-cloud border border-hairline p-2.5 rounded-xl text-xs text-ink file:mr-4 file:py-1.5 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-ink file:text-white hover:file:opacity-80 cursor-pointer">
                    <p class="text-[11px] text-mute mt-1">Format: JPG, PNG, WEBP, SVG (Maks. 10MB).</p>
                    @error('image') <p class="text-sale text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-hairline-soft">
                    <a href="{{ route('admin.banners.index') }}" class="px-5 py-2.5 bg-soft-cloud hover:bg-neutral-200 rounded-full text-xs font-medium text-ink transition">Batal</a>
                    <button type="submit" class="px-6 py-2.5 bg-ink hover:opacity-90 text-white rounded-full text-xs font-bold uppercase tracking-wider transition inline-flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Gambar
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
@endsection
