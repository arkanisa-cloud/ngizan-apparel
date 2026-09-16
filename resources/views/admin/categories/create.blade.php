@extends('layouts.admin')

@section('title', 'Tambah Kategori · NGIZAN APPAREL')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between border-b border-hairline-soft pb-5">
        <div>
            <h1 class="text-2xl font-medium tracking-tight text-ink">Tambah Kategori</h1>
            <p class="text-xs text-mute mt-1">Buat kelompok kategori baru untuk koleksi jersey.</p>
        </div>
        <a href="{{ route('admin.categories.index') }}" class="px-4 py-2 bg-soft-cloud hover:bg-neutral-200 text-ink rounded-full text-xs font-medium transition flex items-center gap-1.5">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali</span>
        </a>
    </div>

    <div class="bg-white p-6 rounded-2xl border border-hairline-soft">
        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            <div class="space-y-5 text-xs">
                <div>
                    <label for="name" class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Nama Kategori <span class="text-sale">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full bg-soft-cloud border border-hairline p-2.5 rounded-xl text-xs text-ink focus:outline-none focus:border-ink" placeholder="Contoh: Tim Nasional, Liga Inggris, Retro Classics" required>
                    @error('name') <p class="text-sale text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="description" class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Deskripsi</label>
                    <textarea name="description" id="description" rows="3" class="w-full bg-soft-cloud border border-hairline p-2.5 rounded-xl text-xs text-ink focus:outline-none focus:border-ink" placeholder="Deskripsi kategori (opsional)">{{ old('description') }}</textarea>
                    @error('description') <p class="text-sale text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="status" class="block font-medium text-mute mb-1 uppercase tracking-wider text-[11px]">Status <span class="text-sale">*</span></label>
                    <select name="status" id="status" class="w-full bg-soft-cloud border border-hairline p-2.5 rounded-xl text-xs text-ink focus:outline-none focus:border-ink cursor-pointer" required>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                    @error('status') <p class="text-sale text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-center justify-end gap-3 pt-5 border-t border-hairline-soft">
                    <a href="{{ route('admin.categories.index') }}" class="px-5 py-2.5 bg-soft-cloud hover:bg-neutral-200 rounded-full text-xs font-medium text-ink transition">Batal</a>
                    <button type="submit" class="px-6 py-2.5 bg-ink hover:opacity-90 text-white rounded-full text-xs font-medium transition inline-flex items-center gap-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Simpan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
