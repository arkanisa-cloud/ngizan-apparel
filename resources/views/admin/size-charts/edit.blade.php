@extends('layouts.admin')

@section('title', 'Edit Template Panduan Ukuran')
@section('header_title', 'Edit Template Ukuran')

@section('content')
<div class="max-w-4xl space-y-6" x-data="sizeChartEditor()">

    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-hairline-soft pb-5">
        <div>
            <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-mute block mb-1">Master Data</span>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-ink">Edit Template: {{ $sizeChart->name }}</h1>
            <p class="text-xs text-mute mt-1">Perbarui matriks dimensi ukuran panduan.</p>
        </div>
        <a href="{{ route('admin.size-charts.index') }}" 
           class="px-5 py-2.5 bg-soft-cloud hover:bg-neutral-200 text-ink rounded-full text-xs font-medium transition flex items-center gap-1.5 self-start sm:self-auto">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali</span>
        </a>
    </div>

    {{-- Form Container --}}
    <form action="{{ route('admin.size-charts.update', $sizeChart->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- 1. Informasi Dasar --}}
        <div class="bg-white p-6 sm:p-7 rounded-2xl border border-hairline-soft space-y-5 shadow-xs">
            <h2 class="font-extrabold text-sm uppercase tracking-wider text-ink border-b border-hairline-soft pb-3">
                1. Informasi Dasar Template
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 text-xs">
                <div>
                    <label class="block font-bold text-ink uppercase text-[11px] tracking-wider mb-1.5">
                        Nama Template *
                    </label>
                    <input type="text" name="name" required placeholder="Trackpants Dewasa (Pria / Unisex)"
                           value="{{ old('name', $sizeChart->name) }}"
                           class="w-full bg-soft-cloud border border-hairline-soft px-4 py-3 rounded-xl text-xs text-ink focus:border-ink focus:bg-white focus:ring-0 transition font-medium">
                    @error('name') <p class="text-sale text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block font-bold text-ink uppercase text-[11px] tracking-wider mb-1.5">
                        Kategori Pakaian <span class="text-sale">*</span>
                    </label>
                    <div class="relative" x-data="{ 
                        open: false, 
                        search: '{{ old('category_type', $sizeChart->category_type) }}',
                        selected: '{{ old('category_type', $sizeChart->category_type) }}',
                        options: [
                            @foreach($categories as $category)
                                { id: '{{ $category->name }}', label: '{{ $category->name }}' },
                            @endforeach
                        ],
                        get filteredOptions() {
                            if (!this.search || this.search.trim() === '') {
                                return this.options;
                            }
                            return this.options.filter(o => o.label.toLowerCase().includes(this.search.toLowerCase()));
                        },
                        selectOption(item) {
                            this.selected = item.id;
                            this.search = item.label;
                            this.open = false;
                        },
                        handleInput() {
                            this.selected = this.search;
                            this.open = true;
                        }
                    }">
                        <input type="hidden" name="category_type" :value="selected" required>

                        {{-- Combobox Input Trigger --}}
                        <div class="relative flex items-center">
                            <input type="text" 
                                   x-model="search"
                                   @focus="open = true"
                                   @input="handleInput()"
                                   @keydown.escape="open = false"
                                   @click.outside="open = false"
                                   placeholder="Pilih atau cari kategori..."
                                   class="w-full bg-soft-cloud focus:bg-white hover:bg-neutral-200/70 border border-hairline-soft focus:border-ink text-ink text-xs font-semibold rounded-2xl pl-4 pr-16 py-3 transition focus:outline-none shadow-2xs">

                            <div class="absolute right-3 flex items-center gap-1.5">
                                <template x-if="search && search.length > 0">
                                    <button type="button" 
                                            @click.stop="search = ''; selected = ''; open = true" 
                                            class="p-1 text-mute hover:text-ink transition cursor-pointer"
                                            title="Hapus pencarian">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </template>
                                <button type="button" 
                                        @click.stop="open = !open" 
                                        class="p-1 text-mute hover:text-ink transition cursor-pointer">
                                    <svg class="w-4 h-4 transition-transform duration-200"
                                         :class="open ? 'rotate-180 text-ink' : ''" 
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- Combobox Results Dropdown --}}
                        <div x-show="open" 
                            @click.away="open = false" 
                            x-cloak
                            x-transition:enter="transition ease-out duration-150 transform"
                            x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100 transform"
                            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                            x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                            class="absolute left-0 right-0 mt-2 bg-white border border-hairline-soft rounded-2xl py-1.5 shadow-xl z-40 text-xs overflow-hidden backdrop-blur-xl max-h-60 overflow-y-auto">
                            
                            <div class="px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-wider text-mute border-b border-hairline-soft flex items-center justify-between">
                                <span>Kategori Database</span>
                                <span x-text="filteredOptions.length + ' opsi'"></span>
                            </div>

                            <template x-for="item in filteredOptions" :key="item.id">
                                <button type="button" 
                                    @click="selectOption(item)"
                                    class="w-full flex items-center justify-between px-4 py-2.5 transition text-left cursor-pointer"
                                    :class="selected.toLowerCase() === item.id.toLowerCase() ? 'bg-soft-cloud text-ink font-bold' : 'text-neutral-600 hover:text-ink hover:bg-soft-cloud/70 font-medium'">
                                    <div class="flex items-center gap-2">
                                        <span class="w-1.5 h-1.5 rounded-full" :class="selected.toLowerCase() === item.id.toLowerCase() ? 'bg-ink' : 'bg-transparent'"></span>
                                        <span x-text="item.label"></span>
                                    </div>
                                    <span x-show="selected.toLowerCase() === item.id.toLowerCase()" class="text-ink font-bold">✓</span>
                                </button>
                            </template>

                            {{-- Option to use typed text if not strictly in database --}}
                            <template x-if="search && search.trim() !== '' && !options.some(o => o.label.toLowerCase() === search.trim().toLowerCase())">
                                <button type="button" 
                                    @click="selected = search.trim(); open = false"
                                    class="w-full flex items-center justify-between px-4 py-2.5 transition text-left cursor-pointer bg-amber-50/50 hover:bg-amber-50 text-ink border-t border-hairline-soft font-semibold">
                                    <span class="truncate">Gunakan input: <strong class="text-ink" x-text="'\'' + search.trim() + '\''"></strong></span>
                                    <span class="text-[10px] bg-amber-200/80 px-2 py-0.5 rounded-full text-amber-900 font-bold">Kustom +</span>
                                </button>
                            </template>

                            <template x-if="filteredOptions.length === 0 && (!search || search.trim() === '')">
                                <div class="px-4 py-3 text-center text-mute text-[11px]">
                                    Belum ada kategori terdaftar di database.
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-xs">
                <label class="block font-bold text-ink uppercase text-[11px] tracking-wider mb-1.5">
                    Catatan / Petunjuk Pengukuran (Opsional)
                </label>
                <textarea name="description" rows="2" placeholder="Diukur dalam posisi pakaian terbentang rata di atas meja. Toleransi ± 1-2 cm."
                          class="w-full bg-soft-cloud border border-hairline-soft p-4 rounded-xl text-xs text-ink focus:border-ink focus:bg-white focus:ring-0 transition font-medium">{{ old('description', $sizeChart->description) }}</textarea>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <label class="flex items-center gap-2 cursor-pointer select-none text-xs font-semibold text-ink">
                    <input type="checkbox" name="is_default" value="1" {{ old('is_default', $sizeChart->is_default) ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-neutral-300 text-ink focus:ring-ink transition">
                    <span>Jadikan template utama (default)</span>
                </label>
            </div>
        </div>

        {{-- 2. Konfigurasi Kolom & Baris Ukuran Interaktif --}}
        <div class="bg-white p-6 sm:p-7 rounded-2xl border border-hairline-soft space-y-5 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-hairline-soft pb-3 gap-2">
                <h2 class="font-extrabold text-sm uppercase tracking-wider text-ink">
                    2. Matriks Tabel Ukuran
                </h2>

                <div class="flex items-center gap-2">
                    <button type="button" @click="addColumn()"
                            class="px-3 py-1.5 bg-soft-cloud hover:bg-neutral-200 text-ink text-xs font-bold rounded-full transition inline-flex items-center gap-1 cursor-pointer">
                        <span>+ Tambah Kolom</span>
                    </button>
                    <button type="button" @click="addRow()"
                            class="px-3 py-1.5 bg-ink hover:bg-black text-white text-xs font-bold rounded-full transition inline-flex items-center gap-1 cursor-pointer">
                        <span>+ Tambah Baris Size</span>
                    </button>
                </div>
            </div>

            {{-- Table Grid Preview --}}
            <div class="overflow-x-auto border border-hairline-soft rounded-xl">
                <table class="w-full text-xs text-left">
                    <thead class="bg-soft-cloud border-b border-hairline-soft">
                        <tr>
                            <template x-for="(col, colIdx) in columns" :key="colIdx">
                                <th class="p-3">
                                    <div class="flex items-center gap-1.5">
                                        <input type="text" :name="'columns[' + colIdx + ']'" x-model="columns[colIdx]"
                                               placeholder="Nama Kolom" required
                                               class="w-full bg-white border border-hairline-soft px-2.5 py-1.5 rounded-lg text-xs font-bold text-ink focus:border-ink focus:ring-0">
                                        <button type="button" x-show="columns.length > 2 && colIdx > 0" @click="removeColumn(colIdx)"
                                                class="text-mute hover:text-sale p-1 text-sm font-bold" title="Hapus Kolom">&times;</button>
                                    </div>
                                </th>
                            </template>
                            <th class="p-3 w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline-soft">
                        <template x-for="(row, rowIdx) in rows" :key="rowIdx">
                            <tr class="hover:bg-soft-cloud/40 transition">
                                <td class="p-3">
                                    <input type="text" :name="'rows[' + rowIdx + '][size]'" x-model="row.size"
                                           placeholder="Ukuran (S, M, L...)" required
                                           class="w-full bg-soft-cloud border border-hairline-soft px-2.5 py-1.5 rounded-lg text-xs font-bold text-ink focus:bg-white focus:border-ink">
                                </td>
                                <template x-for="(col, colIdx) in columns.slice(1)" :key="colIdx">
                                    <td class="p-3">
                                        <input type="text" :name="'rows[' + rowIdx + '][col' + (colIdx + 1) + ']'"
                                               x-model="row['col' + (colIdx + 1)]" placeholder="Isi ukuran"
                                               class="w-full bg-soft-cloud border border-hairline-soft px-2.5 py-1.5 rounded-lg text-xs text-ink focus:bg-white focus:border-ink font-medium">
                                    </td>
                                </template>
                                <td class="p-3 text-center">
                                    <button type="button" x-show="rows.length > 1" @click="removeRow(rowIdx)"
                                            class="text-mute hover:text-sale p-1 text-sm font-bold" title="Hapus Baris">&times;</button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <p class="text-[11px] text-mute">
                *Tips: Perubahan pada template ini akan langsung berlaku pada seluruh produk yang menggunakan template panduan ukuran ini.
            </p>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('admin.size-charts.index') }}" 
               class="px-5 py-2.5 bg-soft-cloud hover:bg-neutral-200 text-ink text-xs font-semibold rounded-full transition">
                Batal
            </a>
            <button type="submit" 
                    class="px-6 py-2.5 bg-ink hover:bg-black text-white text-xs font-bold uppercase tracking-wider rounded-full transition shadow-md cursor-pointer">
                Simpan Perubahan
            </button>
        </div>

    </form>
</div>

<script>
    function sizeChartEditor() {
        return {
            columns: @json($sizeChart->columns ?? ['Ukuran', 'Lebar Dada (cm)', 'Panjang Baju (cm)']),
            rows: @json($sizeChart->rows ?? []),
            addColumn() {
                const nextNum = this.columns.length;
                this.columns.push('Kolom ' + nextNum);
                this.rows.forEach(r => {
                    r['col' + (nextNum - 1)] = '';
                });
            },
            removeColumn(index) {
                if (this.columns.length <= 2) return;
                this.columns.splice(index, 1);
            },
            addRow() {
                const newRow = { size: '' };
                for (let i = 1; i < this.columns.length; i++) {
                    newRow['col' + i] = '';
                }
                this.rows.push(newRow);
            },
            removeRow(index) {
                if (this.rows.length <= 1) return;
                this.rows.splice(index, 1);
            }
        };
    }
</script>
@endsection
