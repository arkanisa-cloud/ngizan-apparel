@extends('layouts.admin')

@section('title', 'Tambah Template Panduan Ukuran')
@section('header_title', 'Tambah Template Ukuran')

@section('content')
<div class="max-w-4xl space-y-6" x-data="sizeChartEditor()">

    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-hairline-soft pb-5">
        <div>
            <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-mute block mb-1">Master Data</span>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-ink">Buat Template Panduan Ukuran</h1>
            <p class="text-xs text-mute mt-1">Lengkapi spesifikasi ukuran dan matriks dimensi produk.</p>
        </div>
        <a href="{{ route('admin.size-charts.index') }}" 
           class="px-5 py-2.5 bg-soft-cloud hover:bg-neutral-200 text-ink rounded-full text-xs font-medium transition flex items-center gap-1.5 self-start sm:self-auto">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali</span>
        </a>
    </div>

    {{-- Form Container --}}
    <form action="{{ route('admin.size-charts.store') }}" method="POST" class="space-y-6">
        @csrf

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
                    <input type="text" name="name" required placeholder="Contoh: Trackpants Dewasa (Pria / Unisex)"
                           value="{{ old('name') }}"
                           class="w-full bg-soft-cloud border border-hairline-soft px-4 py-3 rounded-xl text-xs text-ink focus:border-ink focus:bg-white focus:ring-0 transition font-medium">
                    @error('name') <p class="text-sale text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block font-bold text-ink uppercase text-[11px] tracking-wider mb-1.5">
                        Kategori Pakaian *
                    </label>
                    <select name="category_type" required
                            class="w-full bg-soft-cloud border border-hairline-soft px-4 py-3 rounded-xl text-xs text-ink focus:border-ink focus:bg-white focus:ring-0 transition font-medium">
                        <option value="tops">Atasan / Jersey (Tops)</option>
                        <option value="bottoms">Bawahan / Celana (Bottoms)</option>
                        <option value="outerwear">Jaket / Luaran (Outerwear)</option>
                        <option value="other">Lainnya / Setelan</option>
                    </select>
                </div>
            </div>

            <div class="text-xs">
                <label class="block font-bold text-ink uppercase text-[11px] tracking-wider mb-1.5">
                    Catatan / Petunjuk Pengukuran (Opsional)
                </label>
                <textarea name="description" rows="2" placeholder="Contoh: Diukur dalam posisi pakaian terbentang rata di atas meja. Toleransi ± 1-2 cm."
                          class="w-full bg-soft-cloud border border-hairline-soft p-4 rounded-xl text-xs text-ink focus:border-ink focus:bg-white focus:ring-0 transition font-medium">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <label class="flex items-center gap-2 cursor-pointer select-none text-xs font-semibold text-ink">
                    <input type="checkbox" name="is_default" value="1" {{ old('is_default') ? 'checked' : '' }}
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
                                           placeholder="Size (misal: S)" required
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
                *Tips: Anda dapat mengubah nama header kolom atau menambah baris sesuai spesifikasi jenis pakaian (baju, celana, atau jaket).
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
                Simpan Template
            </button>
        </div>

    </form>
</div>

<script>
    function sizeChartEditor() {
        return {
            columns: ['Ukuran', 'Lebar Dada (cm)', 'Panjang Baju (cm)', 'Rekomendasi TB / BB'],
            rows: [
                { size: 'S', col1: '48 cm', col2: '68 cm', col3: '160-168 cm / 50-60 kg' },
                { size: 'M', col1: '50 cm', col2: '70 cm', col3: '168-175 cm / 60-70 kg' },
                { size: 'L', col1: '52 cm', col2: '72 cm', col3: '172-180 cm / 70-80 kg' },
                { size: 'XL', col1: '54 cm', col2: '74 cm', col3: '178-185 cm / 80-90 kg' },
            ],
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
