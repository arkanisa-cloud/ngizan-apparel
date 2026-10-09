@extends('layouts.admin')

@section('title', 'Catat Stok Masuk · NGIZAN APPAREL')

@section('content')
<div class="max-w-4xl space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-hairline-soft pb-5">
        <div>
            <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-mute block mb-1">Gudang & Inventori</span>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-ink">Catat Restock Jersey Masuk</h1>
            <p class="text-xs text-mute mt-1">Tambah kuantitas fisik stok jersey masuk ke gudang.</p>
        </div>
        <a href="{{ route('admin.stock-ins.index') }}" class="px-5 py-2.5 bg-soft-cloud hover:bg-neutral-200 text-ink rounded-full text-xs font-medium transition self-start sm:self-auto flex items-center gap-1.5 shadow-2xs">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali</span>
        </a>
    </div>

    @php
        $variantOptions = [];
        foreach($products as $prod) {
            foreach($prod->variants as $variant) {
                $typeSuffix = ($variant->type && !in_array(strtolower($variant->type), ['standard', 'default', ''])) ? ' (' . $variant->type . ')' : '';
                $variantOptions[] = [
                    'id'    => (string)$variant->id,
                    'name'  => $prod->name,
                    'size'  => $variant->size,
                    'type'  => $variant->type,
                    'sku'   => $variant->sku,
                    'stock' => $variant->stock,
                    'label' => $prod->name . ' — ' . $variant->size . $typeSuffix . ' [Stok: ' . $variant->stock . ' pcs]'
                ];
            }
        }
    @endphp

    <form action="{{ route('admin.stock-ins.store') }}" method="POST" class="bg-white p-6 rounded-2xl border border-hairline-soft space-y-5 text-xs shadow-xs">
        @csrf

        <div>
            <label class="block font-bold text-ink uppercase text-[11px] tracking-wider mb-1.5">
                Pilih Jersey & Varian Ukuran <span class="text-sale">*</span>
            </label>
            <div class="relative" x-data="{ 
                open: false, 
                search: '',
                selected: '{{ old('product_variant_id', '') }}',
                options: @js($variantOptions),
                init() {
                    if (this.selected) {
                        const found = this.options.find(o => o.id == this.selected);
                        if (found) this.search = found.name + ' (' + found.size + ' - ' + found.type + ')';
                    }
                },
                get filteredOptions() {
                    if (!this.search || this.search.trim() === '') {
                        return this.options;
                    }
                    const q = this.search.toLowerCase();
                    return this.options.filter(o => 
                        o.label.toLowerCase().includes(q) || 
                        (o.sku && o.sku.toLowerCase().includes(q))
                    );
                },
                selectOption(item) {
                    this.selected = item.id;
                    this.search = item.name + ' (' + item.size + ' - ' + item.type + ')';
                    this.open = false;
                }
            }">
                <input type="hidden" name="product_variant_id" :value="selected" required>

                {{-- Combobox Trigger Input --}}
                <div class="relative flex items-center">
                    <input type="text" 
                           x-model="search"
                           @focus="open = true"
                           @keydown.escape="open = false"
                           @click.outside="open = false"
                           placeholder="Ketik untuk mencari nama jersey, ukuran, atau SKU..."
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

                {{-- Combobox Dropdown Results --}}
                <div x-show="open" 
                    @click.away="open = false" 
                    x-cloak
                    x-transition:enter="transition ease-out duration-150 transform"
                    x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100 transform"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                    class="absolute left-0 right-0 mt-2 bg-white border border-hairline-soft rounded-2xl py-1.5 shadow-xl z-40 text-xs overflow-hidden backdrop-blur-xl max-h-64 overflow-y-auto">
                    
                    <div class="px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-wider text-mute border-b border-hairline-soft flex items-center justify-between">
                        <span>Daftar Varian Produk</span>
                        <span x-text="filteredOptions.length + ' varian'"></span>
                    </div>

                    <template x-for="item in filteredOptions" :key="item.id">
                        <button type="button" 
                            @click="selectOption(item)"
                            class="w-full flex items-center justify-between px-4 py-2.5 transition text-left cursor-pointer border-b border-hairline-soft/50 last:border-b-0"
                            :class="selected == item.id ? 'bg-soft-cloud text-ink font-bold' : 'text-neutral-700 hover:text-ink hover:bg-soft-cloud/70 font-medium'">
                            <div class="space-y-0.5">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-ink" x-text="item.name"></span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-white border border-hairline-soft text-ink">
                                        <span x-text="item.size"></span> (<span x-text="item.type"></span>)
                                    </span>
                                </div>
                                <div class="flex items-center gap-2 text-[11px] text-mute font-mono">
                                    <span x-text="'SKU: ' + item.sku"></span>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 shrink-0 ml-3">
                                <div class="text-right">
                                    <span class="text-[10px] text-mute block uppercase tracking-wider">Stok Saat Ini</span>
                                    <span class="font-bold" :class="item.stock <= 3 ? 'text-sale' : 'text-ink'" x-text="item.stock + ' pcs'"></span>
                                </div>
                                <span x-show="selected == item.id" class="text-ink font-bold text-sm">✓</span>
                            </div>
                        </button>
                    </template>

                    <template x-if="filteredOptions.length === 0">
                        <div class="px-4 py-4 text-center text-mute text-xs">
                            Tidak ditemukan varian yang sesuai dengan pencarian.
                        </div>
                    </template>
                </div>
            </div>
            @error('product_variant_id') <p class="text-sale text-[11px] mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold text-ink mb-1.5 uppercase tracking-wider text-[11px]">
                    Jumlah Qty Masuk (Pcs) <span class="text-sale">*</span>
                </label>
                <input type="number" name="quantity" required min="1" value="{{ old('quantity', 10) }}"
                       class="w-full bg-soft-cloud focus:bg-white border border-hairline focus:border-ink p-3 rounded-xl text-xs text-ink focus:outline-none transition">
                @error('quantity') <p class="text-sale text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-semibold text-ink mb-1.5 uppercase tracking-wider text-[11px]">
                    Harga Beli per Pcs (Rp) <span class="text-sale">*</span>
                </label>
                <input type="number" name="purchase_price" required min="0" step="1000" value="{{ old('purchase_price', 150000) }}"
                       class="w-full bg-soft-cloud focus:bg-white border border-hairline focus:border-ink p-3 rounded-xl text-xs text-ink focus:outline-none transition">
                @error('purchase_price') <p class="text-sale text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-semibold text-ink mb-1.5 uppercase tracking-wider text-[11px]">No. Invoice / Surat Jalan</label>
                <input type="text" name="invoice_number" value="{{ old('invoice_number') }}" placeholder="INV-RESTOCK-2026-001"
                       class="w-full bg-soft-cloud focus:bg-white border border-hairline focus:border-ink p-3 rounded-xl text-xs text-ink focus:outline-none transition">
                @error('invoice_number') <p class="text-sale text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block font-semibold text-ink mb-1.5 uppercase tracking-wider text-[11px]">
                    Tanggal Diterima <span class="text-sale">*</span>
                </label>
                <input type="date" name="received_date" required value="{{ old('received_date', date('Y-m-d')) }}"
                       class="w-full bg-soft-cloud focus:bg-white border border-hairline focus:border-ink p-3 rounded-xl text-xs text-ink focus:outline-none transition">
                @error('received_date') <p class="text-sale text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block font-semibold text-ink mb-1.5 uppercase tracking-wider text-[11px]">Catatan Tambahan (Opsional)</label>
            <textarea name="notes" rows="2" placeholder="Kondisi kain mulus, packaging lengkap..."
                      class="w-full bg-soft-cloud focus:bg-white border border-hairline focus:border-ink p-3 rounded-xl text-xs text-ink focus:outline-none transition">{{ old('notes') }}</textarea>
            @error('notes') <p class="text-sale text-[11px] mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-hairline-soft">
            <a href="{{ route('admin.stock-ins.index') }}" class="px-5 py-2.5 bg-soft-cloud hover:bg-neutral-200 rounded-full text-xs font-semibold text-ink transition shadow-2xs">
                Batal
            </a>
            <button type="submit" class="px-6 py-2.5 bg-ink hover:opacity-90 text-white rounded-full text-xs font-semibold uppercase tracking-wider transition cursor-pointer shadow-xs">
                Simpan & Tambah Stok
            </button>
        </div>
    </form>
</div>
@endsection
