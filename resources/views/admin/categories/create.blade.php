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
                    <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Status Publikasi <span class="text-sale">*</span></label>
                    <div class="relative" x-data="{ 
                        open: false, 
                        selected: '{{ old('status', 'active') }}',
                        options: [
                            { id: 'active', label: 'Aktif (Tampil di Beranda & Katalog)' },
                            { id: 'inactive', label: 'Tidak Aktif' }
                        ],
                        get currentLabel() {
                            const found = this.options.find(o => o.id === this.selected);
                            return found ? found.label : 'Pilih Status';
                        }
                    }">
                        <input type="hidden" name="status" :value="selected" required>

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
                            class="absolute left-0 right-0 mt-2 bg-white border border-hairline-soft rounded-2xl py-1.5 shadow-xl z-40 text-xs overflow-hidden backdrop-blur-xl">
                            
                            <template x-for="item in options" :key="item.id">
                                <button type="button" 
                                    @click="selected = item.id; open = false"
                                    class="w-full flex items-center justify-between px-4 py-2.5 transition text-left cursor-pointer"
                                    :class="selected === item.id ? 'bg-soft-cloud text-ink font-bold' : 'text-neutral-600 hover:text-ink hover:bg-soft-cloud/70 font-medium'">
                                    <span x-text="item.label"></span>
                                    <span x-show="selected === item.id" class="text-ink font-bold">✓</span>
                                </button>
                            </template>
                        </div>
                    </div>
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
