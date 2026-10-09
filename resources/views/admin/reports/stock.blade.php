@extends('layouts.admin')

@section('title', 'Laporan & Audit Stok Gudang · NGIZAN APPAREL')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-hairline-soft pb-5">
        <div>
            <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-mute block mb-1">Laporan & Keuangan</span>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-ink">Laporan & Audit Stok Gudang</h1>
            <p class="text-xs text-mute mt-1">Audit inventori fisik varian ukuran, sisa stok gudang, dan indikator restock.</p>
        </div>
    </div>

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('admin.reports.stock') }}" class="bg-white p-4 sm:p-5 rounded-2xl border border-hairline-soft flex flex-wrap gap-3 items-end text-xs">
        <div class="w-full sm:w-auto">
            <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[10px]">Filter Kategori</label>
            <div class="relative" x-data="{ 
                catOpen: false, 
                selected: '{{ request('category_id', '') }}',
                options: [
                    { id: '', label: 'Semua Kategori' },
                    @foreach($categories as $cat)
                        { id: '{{ $cat->id }}', label: '{{ $cat->name }}' },
                    @endforeach
                ],
                get currentLabel() {
                    const found = this.options.find(o => o.id == this.selected);
                    return found ? found.label : 'Semua Kategori';
                }
            }">
                <input type="hidden" name="category_id" :value="selected">

                <button type="button" 
                    @click="catOpen = !catOpen" 
                    @keydown.escape="catOpen = false"
                    class="w-full sm:w-auto inline-flex items-center justify-between gap-3 bg-soft-cloud hover:bg-neutral-200 border border-hairline text-ink text-xs font-semibold rounded-full px-4 py-2 transition focus:outline-none cursor-pointer shadow-2xs select-none">
                    <span x-text="currentLabel"></span>
                    <svg class="w-3.5 h-3.5 text-mute transition-transform duration-200"
                        :class="catOpen ? 'rotate-180 text-ink' : ''" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="catOpen" @click.away="catOpen = false" x-cloak
                    x-transition:enter="transition ease-out duration-150 transform"
                    x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100 transform"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                    class="absolute left-0 mt-2 w-56 bg-white border border-hairline-soft rounded-2xl py-1.5 shadow-xl z-40 text-xs overflow-hidden max-h-60 overflow-y-auto backdrop-blur-xl">
                    <template x-for="item in options" :key="item.id">
                        <button type="button" 
                            @click="selected = item.id; catOpen = false"
                            class="w-full flex items-center justify-between px-3.5 py-2 transition text-left cursor-pointer"
                            :class="selected == item.id ? 'bg-soft-cloud text-ink font-bold' : 'text-neutral-600 hover:text-ink hover:bg-soft-cloud/70 font-medium'">
                            <span x-text="item.label"></span>
                            <span x-show="selected == item.id" class="text-ink font-bold">✓</span>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <div class="w-full sm:w-auto">
            <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[10px]">Kondisi Stok</label>
            <div class="relative" x-data="{ 
                filterOpen: false, 
                selected: '{{ request('filter', '') }}',
                options: [
                    { id: '', label: 'Semua Kondisi' },
                    { id: 'low', label: 'Stok Menipis (≤ 3 pcs)' },
                    { id: 'out', label: 'Stok Habis (0 pcs)' }
                ],
                get currentLabel() {
                    const found = this.options.find(o => o.id === this.selected);
                    return found ? found.label : 'Semua Kondisi';
                }
            }">
                <input type="hidden" name="filter" :value="selected">

                <button type="button" 
                    @click="filterOpen = !filterOpen" 
                    @keydown.escape="filterOpen = false"
                    class="w-full sm:w-auto inline-flex items-center justify-between gap-3 bg-soft-cloud hover:bg-neutral-200 border border-hairline text-ink text-xs font-semibold rounded-full px-4 py-2 transition focus:outline-none cursor-pointer shadow-2xs select-none">
                    <span x-text="currentLabel"></span>
                    <svg class="w-3.5 h-3.5 text-mute transition-transform duration-200"
                        :class="filterOpen ? 'rotate-180 text-ink' : ''" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="filterOpen" @click.away="filterOpen = false" x-cloak
                    x-transition:enter="transition ease-out duration-150 transform"
                    x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100 transform"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                    class="absolute left-0 mt-2 w-56 bg-white border border-hairline-soft rounded-2xl py-1.5 shadow-xl z-40 text-xs overflow-hidden backdrop-blur-xl">
                    <template x-for="item in options" :key="item.id">
                        <button type="button" 
                            @click="selected = item.id; filterOpen = false"
                            class="w-full flex items-center justify-between px-3.5 py-2 transition text-left cursor-pointer"
                            :class="selected === item.id ? 'bg-soft-cloud text-ink font-bold' : 'text-neutral-600 hover:text-ink hover:bg-soft-cloud/70 font-medium'">
                            <span x-text="item.label"></span>
                            <span x-show="selected === item.id" class="text-ink font-bold">✓</span>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <button type="submit" class="w-full sm:w-auto px-5 py-2 bg-ink hover:opacity-90 text-white rounded-full text-xs font-medium transition cursor-pointer">
            Filter
        </button>

        @if(request()->hasAny(['category_id', 'filter']))
            <a href="{{ route('admin.reports.stock') }}" class="text-xs font-medium text-mute hover:text-ink underline self-center">Reset</a>
        @endif
    </form>

    {{-- Variant Stock Table --}}
    <div class="bg-white rounded-2xl border border-hairline-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-ink">
                <thead class="bg-soft-cloud uppercase font-medium text-mute border-b border-hairline-soft">
                    <tr>
                        <th class="p-4">Nama Jersey</th>
                        <th class="p-4">Kategori</th>
                        <th class="p-4">Ukuran & Tipe</th>
                        <th class="p-4">SKU Varian</th>
                        <th class="p-4">Harga Jual</th>
                        <th class="p-4 text-right">Sisa Stok Fisik</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline-soft">
                    @forelse($variants as $v)
                        <tr class="hover:bg-soft-cloud/50 transition">
                            <td class="p-4 font-medium text-ink">
                                {{ $v->product?->name }}
                            </td>
                            <td class="p-4 text-mute">
                                {{ $v->product?->category?->name }}
                            </td>
                            <td class="p-4">
                                <span class="font-medium text-ink">{{ $v->size }}</span>
                                <span class="text-[10px] text-mute uppercase font-medium">({{ $v->type }})</span>
                            </td>
                            <td class="p-4 font-mono text-mute">
                                {{ $v->sku }}
                            </td>
                            <td class="p-4 font-medium text-ink tabular-nums">
                                {{ $v->formatted_final_price }}
                            </td>
                            <td class="p-4 text-right font-medium text-sm {{ $v->stock <= 3 ? 'text-sale' : 'text-ink' }}">
                                {{ $v->stock }} pcs
                                @if($v->stock <= 3 && $v->stock > 0)
                                    <span class="text-[10px] text-amber-600 block font-medium">Menipis</span>
                                @elseif($v->stock == 0)
                                    <span class="text-[10px] text-sale block font-medium">Habis</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-mute">Tidak ada varian stok yang cocok dengan filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-hairline-soft">
            {{ $variants->links() }}
        </div>
    </div>
</div>
@endsection
