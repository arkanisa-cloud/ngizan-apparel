@extends('layouts.admin')

@section('title', 'Laporan Penjualan & Finansial · NGIZAN APPAREL')

@section('content')
<div class="space-y-6">
    
    {{-- Header & Export Button --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold font-display text-slate-900">Laporan Penjualan & Finansial</h1>
            <p class="text-xs text-slate-500 mt-0.5">Rekapitulasi omset jersey, biaya kustom sablon nameset, patch turnamen, dan ongkos kirim.</p>
        </div>
        <a href="{{ route('admin.reports.export.sales', request()->all()) }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            <span>📥 Export Laporan CSV</span>
        </a>
    </div>

    {{-- Filter Date Range Form --}}
    <form method="GET" action="{{ route('admin.reports.sales') }}" class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-wrap gap-3 items-end text-xs">
        <div>
            <label class="block font-bold text-slate-700 mb-1 uppercase tracking-wider text-[10px]">Tanggal Mulai</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="bg-slate-50 border border-slate-200 p-2 rounded-lg text-xs focus:ring-1 focus:ring-slate-900">
        </div>
        <div>
            <label class="block font-bold text-slate-700 mb-1 uppercase tracking-wider text-[10px]">Tanggal Selesai</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="bg-slate-50 border border-slate-200 p-2 rounded-lg text-xs focus:ring-1 focus:ring-slate-900">
        </div>
        <div>
            <label class="block font-bold text-slate-700 mb-1 uppercase tracking-wider text-[10px]">Status Pesanan</label>
            <select name="status" class="bg-slate-50 border border-slate-200 p-2 rounded-lg text-xs focus:ring-1 focus:ring-slate-900">
                <option value="">Semua Status</option>
                @foreach(\App\Enums\OrderStatus::cases() as $st)
                    <option value="{{ $st->value }}" {{ $status === $st->value ? 'selected' : '' }}>{{ $st->label() }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg font-bold transition">
            Filter Data
        </button>
    </form>

    {{-- Financial Summary Breakdown Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm space-y-1">
            <span class="text-[10.5px] font-bold uppercase tracking-wider text-slate-500">Total Omset Bersih</span>
            <div class="font-display font-black text-2xl text-slate-900">
                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
            </div>
            <p class="text-[10px] text-slate-400">Dari {{ $paidOrders->count() }} pesanan lunas</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm space-y-1">
            <span class="text-[10.5px] font-bold uppercase tracking-wider text-slate-500">Penjualan Jersey Murni</span>
            <div class="font-display font-black text-2xl text-slate-900">
                Rp {{ number_format($totalSubtotal - $totalCustomFee, 0, ',', '.') }}
            </div>
            <p class="text-[10px] text-slate-400">Harga dasar kit</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm space-y-1">
            <span class="text-[10.5px] font-bold uppercase tracking-wider text-cyan-600">Omset Kustom Sablon & Patch</span>
            <div class="font-display font-black text-2xl text-cyan-700">
                Rp {{ number_format($totalCustomFee, 0, ',', '.') }}
            </div>
            <p class="text-[10px] text-slate-400">Fee sablon nameset + patch turnamen</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm space-y-1">
            <span class="text-[10.5px] font-bold uppercase tracking-wider text-slate-500">Total Ongkir Biteship</span>
            <div class="font-display font-black text-2xl text-slate-900">
                Rp {{ number_format($totalShipping, 0, ',', '.') }}
            </div>
            <p class="text-[10px] text-slate-400">Diteruskan ke kurir logistik</p>
        </div>
    </div>

    {{-- Top 5 Jersey Best Sellers --}}
    @if($topJerseys->isNotEmpty())
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm space-y-3">
            <h2 class="font-bold text-sm text-slate-900">Top 5 Jersey Terlaris (Periode Ini)</h2>
            <div class="grid grid-cols-1 sm:grid-cols-5 gap-3 text-xs">
                @foreach($topJerseys as $idx => $tj)
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                        <span class="text-[10px] font-bold text-cyan-700 block uppercase">#{{ $idx + 1 }} TERLARIS</span>
                        <strong class="font-bold text-slate-900 block line-clamp-1 mt-0.5">{{ $tj->product_name }}</strong>
                        <div class="mt-2 flex justify-between items-center text-[11px]">
                            <span class="font-bold text-emerald-600">{{ $tj->total_sold }} pcs</span>
                            <span class="font-mono text-slate-500">Rp {{ number_format($tj->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Transactions Table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-xs text-left text-slate-600">
            <thead class="bg-slate-50 uppercase font-bold text-slate-700 border-b border-slate-200">
                <tr>
                    <th class="p-3.5">No. Order</th>
                    <th class="p-3.5">Tanggal</th>
                    <th class="p-3.5">Pelanggan</th>
                    <th class="p-3.5">Subtotal</th>
                    <th class="p-3.5">Ongkir</th>
                    <th class="p-3.5">Grand Total</th>
                    <th class="p-3.5">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($orders as $ord)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="p-3.5 font-mono font-bold text-slate-900">
                            <a href="{{ route('admin.orders.show', $ord->id) }}" class="text-cyan-700 hover:underline">
                                #{{ $ord->order_number }}
                            </a>
                        </td>
                        <td class="p-3.5 text-slate-500">
                            {{ $ord->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="p-3.5 font-semibold text-slate-900">
                            {{ $ord->customer_name }}
                        </td>
                        <td class="p-3.5 tabular-nums">
                            {{ $ord->formatted_subtotal_amount }}
                        </td>
                        <td class="p-3.5 tabular-nums text-slate-500">
                            {{ $ord->formatted_shipping_cost }}
                        </td>
                        <td class="p-3.5 font-bold text-slate-900 tabular-nums">
                            {{ $ord->formatted_grand_total }}
                        </td>
                        <td class="p-3.5">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $ord->status->badgeClass() }}">
                                {{ $ord->status->label() }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-slate-400">Tidak ada data transaksi pada rentang tanggal ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
