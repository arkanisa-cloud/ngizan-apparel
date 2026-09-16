@extends('layouts.admin')

@section('title', 'Laporan Penjualan & Finansial · NGIZAN APPAREL')

@section('content')
<div class="space-y-6">
    
    {{-- Header & Export Button --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-hairline-soft pb-5">
        <div>
            <h1 class="text-2xl font-medium tracking-tight text-ink">Laporan Penjualan & Finansial</h1>
            <p class="text-xs text-mute mt-1">Rekapitulasi omset jersey, biaya kustom sablon nameset, patch turnamen, dan ongkos kirim.</p>
        </div>
        <a href="{{ route('admin.reports.export.sales', request()->all()) }}" class="px-5 py-2.5 bg-ink hover:opacity-90 text-white rounded-full text-xs font-medium transition flex items-center gap-1.5 self-start sm:self-auto cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>📥 Export Laporan CSV</span>
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
            <select name="status" class="w-full sm:w-auto bg-soft-cloud border border-hairline px-4 py-2 rounded-full text-xs text-ink font-medium focus:outline-none focus:border-ink cursor-pointer">
                <option value="">Semua Status</option>
                @foreach(\App\Enums\OrderStatus::cases() as $st)
                    <option value="{{ $st->value }}" {{ $status === $st->value ? 'selected' : '' }}>{{ $st->label() }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="w-full sm:w-auto px-5 py-2 bg-ink hover:opacity-90 text-white rounded-full text-xs font-medium transition cursor-pointer">
            Filter Data
        </button>
    </form>

    {{-- Financial Summary Breakdown Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-soft-cloud p-5 rounded-2xl border border-hairline-soft space-y-1">
            <span class="text-[11px] font-medium uppercase tracking-wider text-mute">Total Omset Bersih</span>
            <div class="text-2xl font-medium text-ink tabular-nums">
                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-mute">Dari {{ $paidOrders->count() }} pesanan lunas</p>
        </div>

        <div class="bg-soft-cloud p-5 rounded-2xl border border-hairline-soft space-y-1">
            <span class="text-[11px] font-medium uppercase tracking-wider text-mute">Penjualan Jersey Murni</span>
            <div class="text-2xl font-medium text-ink tabular-nums">
                Rp {{ number_format($totalSubtotal - $totalCustomFee, 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-mute">Harga dasar kit</p>
        </div>

        <div class="bg-soft-cloud p-5 rounded-2xl border border-hairline-soft space-y-1">
            <span class="text-[11px] font-medium uppercase tracking-wider text-mute">Omset Sablon & Patch</span>
            <div class="text-2xl font-medium text-ink tabular-nums">
                Rp {{ number_format($totalCustomFee, 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-mute">Fee sablon nameset + patch</p>
        </div>

        <div class="bg-soft-cloud p-5 rounded-2xl border border-hairline-soft space-y-1">
            <span class="text-[11px] font-medium uppercase tracking-wider text-mute">Total Ongkir Biteship</span>
            <div class="text-2xl font-medium text-ink tabular-nums">
                Rp {{ number_format($totalShipping, 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-mute">Diteruskan ke kurir logistik</p>
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
