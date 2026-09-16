@extends('layouts.admin')

@section('title', 'Manajemen Pesanan Masuk · NGIZAN APPAREL')

@section('content')
<div class="space-y-6">
    
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold font-display text-slate-900">Manajemen Pesanan Masuk</h1>
            <p class="text-xs text-slate-500 mt-0.5">Kelola antrian pesanan, status produksi nameset, booking kurir Biteship, dan cetak label thermal.</p>
        </div>
    </div>

    {{-- Filter & Search Bar --}}
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row gap-3 justify-between items-center text-xs">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
            <div class="relative w-full sm:w-64">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. Order atau Pelanggan..." 
                       class="w-full bg-slate-50 border border-slate-200 pl-8 p-2 rounded-lg text-xs focus:ring-1 focus:ring-slate-900">
                <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <select name="status" onchange="this.form.submit()" class="bg-slate-50 border border-slate-200 p-2 rounded-lg text-xs focus:ring-1 focus:ring-slate-900">
                <option value="">Semua Status</option>
                @foreach(\App\Enums\OrderStatus::cases() as $st)
                    <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>{{ $st->label() }}</option>
                @endforeach
            </select>

            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.orders.index') }}" class="text-rose-600 font-bold hover:underline">Reset</a>
            @endif
        </form>
    </div>

    {{-- Orders Table --}}
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <table class="w-full text-xs text-left text-slate-600">
            <thead class="bg-slate-50 uppercase font-bold text-slate-700 border-b border-slate-200">
                <tr>
                    <th class="p-3.5">No. Order & Waktu</th>
                    <th class="p-3.5">Pelanggan</th>
                    <th class="p-3.5">Item Jersey (Nameset)</th>
                    <th class="p-3.5">Layanan Kurir</th>
                    <th class="p-3.5">Grand Total</th>
                    <th class="p-3.5">Status Pesanan</th>
                    <th class="p-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($orders as $order)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="p-3.5 font-mono font-bold text-slate-900">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="text-cyan-700 hover:underline">
                                #{{ $order->order_number }}
                            </a>
                            <div class="text-[10px] text-slate-400 font-sans">{{ $order->created_at->format('d/m/Y H:i') }} WIB</div>
                        </td>
                        <td class="p-3.5">
                            <span class="font-semibold text-slate-900 block">{{ $order->customer_name }}</span>
                            <span class="text-[10px] text-slate-400">{{ $order->customer_phone }}</span>
                        </td>
                        <td class="p-3.5">
                            @foreach($order->items as $item)
                                <div class="font-semibold text-slate-800">
                                    {{ $item->product_name }} ({{ $item->size }}) &times; {{ $item->quantity }}
                                    @if($item->custom_name || $item->custom_number)
                                        <span class="text-[9.5px] font-bold text-cyan-700 bg-cyan-50 px-1 py-0.2 rounded font-jersey">
                                            #{{ $item->custom_name }} {{ $item->custom_number }}
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </td>
                        <td class="p-3.5">
                            <span class="font-semibold uppercase text-slate-800">{{ $order->courier_service_name ?? ($order->courier_code . ' Reguler') }}</span>
                            @if($order->tracking_number)
                                <div class="text-[10px] text-cyan-700 font-mono font-bold">Resi: {{ $order->tracking_number }}</div>
                            @endif
                        </td>
                        <td class="p-3.5 font-display font-black text-slate-900 tabular-nums">
                            {{ $order->formatted_grand_total }}
                        </td>
                        <td class="p-3.5">
                            <span class="px-2.5 py-0.5 rounded text-[10px] font-bold {{ $order->status->badgeClass() }}">
                                {{ $order->status->label() }}
                            </span>
                        </td>
                        <td class="p-3.5 text-right space-x-1 whitespace-nowrap">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded text-xs font-bold transition inline-block">
                                Kelola
                            </a>
                            <a href="{{ route('admin.orders.print.label', $order->id) }}" target="_blank" class="px-2 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-xs transition inline-block" title="Cetak Label Pengiriman">
                                🖨️
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-slate-400">Tidak ada pesanan ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t border-slate-100">
            {{ $orders->links() }}
        </div>
    </div>

</div>
@endsection
