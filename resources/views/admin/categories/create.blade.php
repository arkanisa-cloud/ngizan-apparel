@extends('layouts.admin')

@section('title', 'Tambah Kategori · NGIZAN APPAREL')

@section('content')
<div class="max-w-4xl space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-hairline-soft pb-5">
        <div>
            <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-mute block mb-1">Master Data</span>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-ink">Tambah Kategori</h1>
            <p class="text-xs text-mute mt-1">Buat kelompok kategori baru lengkap dengan banner/gambar untuk etalase.</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="px-5 py-2.5 bg-soft-cloud hover:bg-neutral-200 text-ink rounded-full text-xs font-medium transition flex items-center gap-1.5 self-start sm:self-auto">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali</span>
        </a>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-hairline-soft" x-data="{ imagePreview: null }">
        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-5 text-xs">
                {{-- Nama Kategori --}}
                <div>
                    <label for="name" class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Nama Kategori <span class="text-sale">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full bg-soft-cloud border border-hairline p-2.5 rounded-xl text-xs text-ink focus:outline-none focus:border-ink" placeholder="Contoh: Tim Nasional, Klub Eropa, Retro Classics" required>
                    @error('name') <p class="text-sale text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Upload Gambar Kategori --}}
                <div>
                    <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Gambar / Banner Kategori</label>
                    
                    {{-- Preview Container --}}
                    <div class="mb-3" x-show="imagePreview" x-cloak>
                        <div class="relative w-40 h-28 rounded-xl overflow-hidden border border-hairline bg-soft-cloud">
                            <img :src="imagePreview" alt="Preview Gambar Kategori" class="w-full h-full object-cover">
                        </div>
                    </div>

                    <input type="file" name="image" id="image" accept="image/*"
                           @change="const file = $event.target.files[0]; if(file) { const reader = new FileReader(); reader.onload = (e) => { imagePreview = e.target.result; }; reader.readAsDataURL(file); }"
                           class="w-full bg-soft-cloud border border-hairline p-2.5 rounded-xl text-xs text-ink file:mr-4 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-medium file:bg-ink file:text-white hover:file:opacity-80 cursor-pointer">
                    <p class="text-[11px] text-mute mt-1">Format: JPG, PNG, WEBP (Maks. 10MB, otomatis di-resize & dikonversi ke WebP). Rasio ideal 4:3 atau 16:9.</p>
                    @error('image') <p class="text-sale text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Deskripsi --}}
                <div>
                    <label for="description" class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Deskripsi</label>
                    <textarea name="description" id="description" rows="3" class="w-full bg-soft-cloud border border-hairline p-2.5 rounded-xl text-xs text-ink focus:outline-none focus:border-ink" placeholder="Deskripsi kategori (opsional)">{{ old('description') }}</textarea>
                    @error('description') <p class="text-sale text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Status Publikasi <span class="text-sale">*</span></label>
                    <select name="status" id="status" class="w-full bg-soft-cloud border border-hairline p-2.5 rounded-xl text-xs text-ink focus:outline-none focus:border-ink cursor-pointer" required>
                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Aktif (Tampil di Beranda & Katalog)</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                    @error('status') <p class="text-sale text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-5 border-t border-hairline-soft">
                    <a href="{{ route('admin.categories.index') }}" class="px-5 py-2.5 bg-soft-cloud hover:bg-neutral-200 rounded-full text-xs font-medium text-ink transition">Batal</a>
                    <button type="submit" class="px-6 py-2.5 bg-ink hover:opacity-90 text-white rounded-full text-xs font-medium transition inline-flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan Kategori
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
