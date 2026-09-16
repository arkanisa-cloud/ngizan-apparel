@extends('layouts.admin')
@section('title', 'Edit Kategori')
@section('content')
<div class="page-header">
    <h1 class="page-title">Edit Kategori: {{ $category->name }}</h1>
    <a href="{{ route('admin.categories.index') }}" class="btn-outline">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali
    </a>
</div>

<div class="card max-w-2xl">
    <div class="card-body">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-5">
                <div>
                    <label for="name" class="form-label">Nama Kategori <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $category->name) }}" class="form-input-custom" required>
                    @error('name') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="description" class="form-label">Deskripsi</label>
                    <textarea name="description" id="description" rows="3" class="form-input-custom">{{ old('description', $category->description) }}</textarea>
                    @error('description') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="status" class="form-label">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="status" class="form-input-custom" required>
                        <option value="active" {{ old('status', $category->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status', $category->status) == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                    @error('status') <p class="form-error">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                    <a href="{{ route('admin.categories.index') }}" class="btn-secondary">Batal</a>
                    <button type="submit" class="btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Update
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
