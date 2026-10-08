@extends('layouts.admin')

@section('title', 'Manajemen Pesanan Masuk · NGIZAN APPAREL')

@section('content')
<div class="space-y-6">
    
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-hairline-soft pb-5">
        <div>
            <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-mute block mb-1">Transaksi & Logistik</span>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-ink">Manajemen Pesanan</h1>
            <p class="text-xs text-mute mt-1">Kelola pesanan masuk, kustomisasi sablon, resi kurir, dan cetak label.</p>
        </div>
    </div>

    {{-- Filter & Search Bar --}}
    <div class="bg-white p-4 rounded-2xl border border-hairline-soft flex flex-col sm:flex-row gap-3 justify-between items-center text-xs">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
            <div class="relative w-full sm:w-64">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No. Order atau Pelanggan..." 
                       class="w-full bg-soft-cloud border border-hairline pl-8 pr-4 py-2 rounded-full text-xs text-ink focus:border-ink focus:ring-0">
                <svg class="w-4 h-4 text-mute absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <select name="status" onchange="this.form.submit()" class="bg-soft-cloud border border-hairline px-4 py-2 rounded-full text-xs text-ink focus:border-ink focus:ring-0">
                <option value="">Semua Status</option>
                @foreach(\App\Enums\OrderStatus::cases() as $st)
                    <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>{{ $st->label() }}</option>
                @endforeach
            </select>

            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.orders.index') }}" class="text-sale font-medium hover:underline">Reset</a>
            @endif
        </form>
    </div>

    {{-- Orders Table --}}
    <div class="bg-white rounded-2xl border border-hairline-soft overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left text-ink">
            <thead class="bg-soft-cloud font-medium text-mute border-b border-hairline-soft">
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
            <tbody class="divide-y divide-hairline-soft">
                @forelse($orders as $order)
                    <tr class="hover:bg-soft-cloud transition">
                        <td class="p-3.5 font-mono font-medium text-ink">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="hover:underline">
                                #{{ $order->order_number }}
                            </a>
                            <div class="text-[10px] text-mute font-sans">{{ $order->created_at->format('d/m/Y H:i') }} WIB</div>
                        </td>
                        <td class="p-3.5">
                            <span class="font-medium text-ink block">{{ $order->customer_name }}</span>
                            <span class="text-[10px] text-mute">{{ $order->customer_phone }}</span>
                        </td>
                        <td class="p-3.5">
                            @foreach($order->items as $item)
                                <div class="font-medium text-ink">
                                    {{ $item->product_name }} ({{ $item->size }}) &times; {{ $item->quantity }}
                                    @if($item->custom_name || $item->custom_number)
                                        <span class="text-[10px] font-medium text-ink bg-soft-cloud px-1.5 py-0.5 rounded font-jersey">
                                            #{{ $item->custom_name }} {{ $item->custom_number }}
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </td>
                        <td class="p-3.5">
                            <span class="font-medium uppercase text-ink">{{ $order->courier_service_name ?? ($order->courier_code . ' Reguler') }}</span>
                            @if($order->tracking_number)
                                <div class="text-[10px] text-mute font-mono">Resi: {{ $order->tracking_number }}</div>
                            @endif
                        </td>
                        <td class="p-3.5 font-medium text-ink tabular-nums">
                            {{ $order->formatted_grand_total }}
                        </td>
                        <td class="p-3.5">
                            <span class="px-3 py-0.5 rounded-full text-[10px] font-medium {{ $order->status->badgeClass() }}">
                                {{ $order->status->label() }}
                            </span>
                        </td>
                        <td class="p-3.5 text-right space-x-1.5 whitespace-nowrap">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="px-3.5 py-1.5 bg-ink hover:opacity-90 text-white rounded-full text-xs font-medium transition inline-block">
                                Kelola
                            </a>
                            <a href="{{ route('admin.orders.print.label', $order->id) }}" target="_blank" class="p-2 bg-soft-cloud hover:bg-neutral-200 text-ink rounded-full transition inline-flex items-center justify-center align-middle" title="Cetak Label Pengiriman">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-mute">Tidak ada pesanan ditemukan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        <div class="p-4 border-t border-hairline-soft">
            {{ $orders->links() }}
        </div>
    </div>

</div>
@endsection
