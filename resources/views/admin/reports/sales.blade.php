@extends('layouts.admin')

@section('title', 'Laporan Penjualan & Finansial · NGIZAN APPAREL')

@section('content')
<div class="space-y-6">
    
    {{-- Header & Export Button --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-hairline-soft pb-5">
        <div>
            <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-mute block mb-1">Laporan & Keuangan</span>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-ink">Laporan Penjualan & Finansial</h1>
            <p class="text-xs text-mute mt-1">Rekapitulasi omset penjualan, volume pesanan berhasil, total produk terjual, dan rata-rata transaksi.</p>
        </div>
        <a href="{{ route('admin.reports.export.sales', request()->all()) }}" class="px-5 py-2.5 bg-ink hover:opacity-90 text-white rounded-full text-xs font-medium transition flex items-center gap-1.5 self-start sm:self-auto cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>Export Laporan CSV</span>
        </a>
    </div>

    {{-- Filter Date Range Form --}}
    <form method="GET" action="{{ route('admin.reports.sales') }}" class="bg-white p-4 sm:p-5 rounded-2xl border border-hairline-soft flex flex-wrap gap-3 items-end text-xs">
        <div class="w-full sm:w-auto">
            <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[10px]">Tanggal Mulai</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="w-full sm:w-auto bg-soft-cloud border border-hairline px-3.5 py-2 rounded-full text-xs text-ink focus:outline-none focus:border-ink">
        </div>
        <div class="w-full sm:w-auto">
            <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[10px]">Tanggal Selesai</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="w-full sm:w-auto bg-soft-cloud border border-hairline px-3.5 py-2 rounded-full text-xs text-ink focus:outline-none focus:border-ink">
        </div>
        <div class="w-full sm:w-auto">
            <label class="block font-medium text-mute mb-1 uppercase tracking-wider text-[10px]">Status Pesanan</label>
            <div class="relative" x-data="{ 
                statusOpen: false, 
                selected: '{{ $status ?? '' }}',
                options: [
                    { id: '', label: 'Semua Status' },
                    @foreach(\App\Enums\OrderStatus::cases() as $st)
                        { id: '{{ $st->value }}', label: '{{ $st->label() }}' },
                    @endforeach
                ],
                get currentLabel() {
                    const found = this.options.find(o => o.id === this.selected);
                    return found ? found.label : 'Semua Status';
                }
            }">
                <input type="hidden" name="status" :value="selected">

                <button type="button" 
                    @click="statusOpen = !statusOpen" 
                    @keydown.escape="statusOpen = false"
                    class="w-full sm:w-auto inline-flex items-center justify-between gap-3 bg-soft-cloud hover:bg-neutral-200 border border-hairline text-ink text-xs font-semibold rounded-full px-4 py-2 transition focus:outline-none cursor-pointer shadow-2xs select-none">
                    <span x-text="currentLabel"></span>
                    <svg class="w-3.5 h-3.5 text-mute transition-transform duration-200"
                        :class="statusOpen ? 'rotate-180 text-ink' : ''" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="statusOpen" @click.away="statusOpen = false" x-cloak
                    x-transition:enter="transition ease-out duration-150 transform"
                    x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100 transform"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                    class="absolute left-0 mt-2 w-56 bg-white border border-hairline-soft rounded-2xl py-1.5 shadow-xl z-40 text-xs overflow-hidden backdrop-blur-xl">
                    <template x-for="item in options" :key="item.id">
                        <button type="button" 
                            @click="selected = item.id; statusOpen = false"
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
            Filter Data
        </button>
    </form>

    {{-- Financial Summary Breakdown Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Card 1: Total Omset --}}
        <div class="bg-soft-cloud p-5 rounded-2xl border border-hairline-soft space-y-1">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-mute">Total Omset Penjualan</span>
            <div class="text-2xl font-bold text-ink tabular-nums">
                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-mute">Dari {{ number_format($totalOrdersCount) }} pesanan lunas</p>
        </div>

        {{-- Card 2: Total Pesanan Berhasil --}}
        <div class="bg-soft-cloud p-5 rounded-2xl border border-hairline-soft space-y-1">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-mute">Pesanan Berhasil</span>
            <div class="text-2xl font-bold text-ink tabular-nums">
                {{ number_format($totalOrdersCount) }} <span class="text-base font-medium text-mute">Order</span>
            </div>
            <p class="text-[11px] text-mute">Status lunas & selesai diproses</p>
        </div>

        {{-- Card 3: Total Produk Terjual --}}
        <div class="bg-soft-cloud p-5 rounded-2xl border border-hairline-soft space-y-1">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-mute">Total Produk Terjual</span>
            <div class="text-2xl font-bold text-emerald-700 tabular-nums">
                {{ number_format($totalItemsSold) }} <span class="text-base font-medium text-emerald-600/70">Pcs</span>
            </div>
            <p class="text-[11px] text-mute">Akumulasi volume barang terjual</p>
        </div>

        {{-- Card 4: Average Order Value (AOV) --}}
        <div class="bg-soft-cloud p-5 rounded-2xl border border-hairline-soft space-y-1">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-mute">Rata-rata Order (AOV)</span>
            <div class="text-2xl font-bold text-ink tabular-nums">
                Rp {{ number_format($averageOrderValue, 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-mute">Rata-rata nilai belanja per order</p>
        </div>
    </div>

    {{-- Top 5 Jersey Best Sellers --}}
    @if($topJerseys->isNotEmpty())
        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-hairline-soft space-y-4">
            <h2 class="font-medium text-sm text-ink">Top 5 Jersey Terlaris (Periode Ini)</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
                @foreach($topJerseys as $idx => $tj)
                    <div class="p-3.5 bg-soft-cloud rounded-xl border border-hairline-soft space-y-1">
                        <span class="text-[10px] font-medium text-mute block uppercase tracking-wider">#{{ $idx + 1 }} TERLARIS</span>
                        <strong class="font-medium text-ink block line-clamp-1">{{ $tj->product_name }}</strong>
                        <div class="mt-2 flex justify-between items-center text-[11px] pt-1 border-t border-hairline-soft">
                            <span class="font-medium text-emerald-700">{{ $tj->total_sold }} pcs</span>
                            <span class="text-mute">Rp {{ number_format($tj->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Transactions Table --}}
    <div class="bg-white rounded-2xl border border-hairline-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-ink">
                <thead class="bg-soft-cloud uppercase font-medium text-mute border-b border-hairline-soft">
                    <tr>
                        <th class="p-4">No. Order</th>
                        <th class="p-4">Tanggal</th>
                        <th class="p-4">Pelanggan</th>
                        <th class="p-4">Subtotal</th>
                        <th class="p-4">Ongkir</th>
                        <th class="p-4">Grand Total</th>
                        <th class="p-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline-soft">
                    @forelse($orders as $ord)
                        <tr class="hover:bg-soft-cloud/50 transition">
                            <td class="p-4 font-mono font-medium text-ink">
                                <a href="{{ route('admin.orders.show', $ord->id) }}" class="underline hover:text-mute">
                                    #{{ $ord->order_number }}
                                </a>
                            </td>
                            <td class="p-4 text-mute">
                                {{ $ord->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="p-4 font-medium text-ink">
                                {{ $ord->customer_name }}
                            </td>
                            <td class="p-4 tabular-nums">
                                {{ $ord->formatted_subtotal_amount }}
                            </td>
                            <td class="p-4 tabular-nums text-mute">
                                {{ $ord->formatted_shipping_cost }}
                            </td>
                            <td class="p-4 font-medium text-ink tabular-nums">
                                {{ $ord->formatted_grand_total }}
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-medium {{ $ord->status->badgeClass() }}">
                                    {{ $ord->status->label() }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-mute">Tidak ada data transaksi pada rentang tanggal ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
