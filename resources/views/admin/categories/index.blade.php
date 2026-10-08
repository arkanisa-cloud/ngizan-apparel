@extends('layouts.admin')

@section('title', 'Kategori Produk · NGIZAN APPAREL')

@section('content')
<div class="space-y-6">
    {{-- Header & Add Button --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-hairline-soft pb-5">
        <div>
            <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-mute block mb-1">Master Data</span>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-ink">Kategori Produk</h1>
            <p class="text-xs text-mute mt-1">Kelola kategori edisi kit jersey dan visual banner untuk katalog dan etalase toko.</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="px-5 py-2.5 bg-ink hover:opacity-90 text-white rounded-full text-xs font-medium transition flex items-center gap-1.5 self-start sm:self-auto cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Kategori</span>
        </a>
    </div>

    {{-- Table Grid --}}
    <div class="bg-white rounded-2xl border border-hairline-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-ink">
                <thead class="bg-soft-cloud uppercase font-medium text-mute border-b border-hairline-soft">
                    <tr>
                        <th class="p-4 w-12 text-center">No</th>
                        <th class="p-4 w-20">Banner</th>
                        <th class="p-4">Nama Kategori</th>
                        <th class="p-4">Slug</th>
                        <th class="p-4">Deskripsi</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Jumlah Produk</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline-soft">
                    @forelse($categories as $index => $category)
                        <tr class="hover:bg-soft-cloud/50 transition">
                            <td class="p-4 text-center text-mute">{{ $index + 1 }}</td>
                            <td class="p-4">
                                <div class="w-14 h-10 rounded-lg overflow-hidden border border-hairline-soft bg-soft-cloud">
                                    <img src="{{ $category->image_url }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
                                </div>
                            </td>
                            <td class="p-4 font-medium text-ink">
                                <span class="text-sm font-semibold">{{ $category->name }}</span>
                            </td>
                            <td class="p-4 text-mute font-mono text-[11px]">{{ $category->slug }}</td>
                            <td class="p-4 text-mute max-w-xs truncate">{{ $category->description ?? '-' }}</td>
                            <td class="p-4">
                                <span class="px-3 py-1 rounded-full text-[11px] font-medium {{ $category->is_active ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-soft-cloud text-mute border border-hairline' }}">
                                    {{ $category->status_label }}
                                </span>
                            </td>
                            <td class="p-4 font-medium">{{ $category->products_count ?? $category->products->count() }} Produk</td>
                            <td class="p-4 text-right">
                                <div class="flex items-center justify-end gap-2 whitespace-nowrap">
                                    <a href="{{ route('admin.categories.edit', $category) }}" class="px-3 py-1.5 bg-soft-cloud hover:bg-neutral-200 text-ink rounded-full text-xs font-medium transition inline-flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 bg-soft-cloud hover:bg-red-50 text-sale rounded-full text-xs font-medium transition inline-flex items-center gap-1 cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-mute">Belum ada kategori yang ditambahkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
