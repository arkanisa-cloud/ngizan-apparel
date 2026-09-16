@extends('layouts.admin')

@section('title', 'Kelola Pesanan #' . $order->order_number . ' · NGIZAN APPAREL')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    
    {{-- Header & Quick Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-hairline-soft pb-5 gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-medium tracking-tight text-ink">Pesanan #{{ $order->order_number }}</h1>
                <span class="px-3 py-0.5 rounded-full text-[11px] font-medium {{ $order->status->badgeClass() }}">
                    {{ $order->status->label() }}
                </span>
            </div>
            <p class="text-xs text-mute mt-1">Dibuat pada {{ $order->created_at->format('d F Y, H:i') }} WIB</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('admin.orders.print.label', $order->id) }}" target="_blank" class="px-4 py-2 bg-ink hover:opacity-90 text-white rounded-full text-xs font-medium transition flex items-center gap-1.5">
                <span>🖨️ Cetak Label Resi</span>
            </a>
            <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 bg-soft-cloud hover:bg-neutral-200 text-ink rounded-full text-xs font-medium transition">
                &larr; Kembali
            </a>
        </div>
    </div>

    {{-- Status Control & J&T Express Resi Panel --}}
    <div class="bg-white p-6 rounded-2xl border border-hairline-soft space-y-5">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            {{-- Manual Status Update --}}
            <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" class="flex flex-wrap items-center gap-2 text-xs">
                @csrf
                @method('PUT')
                <label class="font-medium text-mute uppercase tracking-wider text-[10px]">Ubah Status:</label>
                <select name="status" class="bg-soft-cloud border border-hairline px-3 py-2 rounded-full text-xs font-medium text-ink focus:outline-none focus:border-ink">
                    <option value="in_production" {{ $order->status->value === 'in_production' ? 'selected' : '' }}>Masuk Antrian Produksi</option>
                    <option value="shipped" {{ $order->status->value === 'shipped' ? 'selected' : '' }}>Telah Dikirim (Shipped)</option>
                    <option value="completed" {{ $order->status->value === 'completed' ? 'selected' : '' }}>Selesai (Completed)</option>
                    <option value="cancelled" {{ $order->status->value === 'cancelled' ? 'selected' : '' }}>Batalkan Pesanan</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-soft-cloud hover:bg-neutral-200 text-ink rounded-full text-xs font-medium transition cursor-pointer">
                    Update Status
                </button>
            </form>

            {{-- Resi J&T Express Status Badge --}}
            @if($order->tracking_number)
                <div class="flex items-center gap-2 text-xs">
                    <span class="text-xs text-ink bg-soft-cloud px-3.5 py-1.5 rounded-full border border-hairline font-medium flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Resi J&T: <span class="font-mono font-bold">{{ $order->tracking_number }}</span>
                    </span>
                </div>
            @endif
        </div>

        {{-- J&T Express Tracking Input Form --}}
        <div class="p-5 bg-soft-cloud rounded-2xl border border-hairline-soft flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="space-y-0.5">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-medium text-sale uppercase tracking-wider">🚚 J&T Express (Gratis Ongkir)</span>
                    @if($order->tracking_number)
                        <span class="text-[10px] bg-emerald-100 text-emerald-800 font-medium px-2 py-0.5 rounded-full">Otomatis Terlacak</span>
                    @endif
                </div>
                <p class="text-xs text-mute">
                    Masukkan nomor resi J&T. Pesanan otomatis berstatus <strong>Telah Dikirim (Shipped)</strong> dan notifikasi WhatsApp langsung terkirim ke pelanggan.
                </p>
            </div>

            <form action="{{ route('admin.orders.tracking', $order->id) }}" method="POST" class="flex items-center gap-2 w-full md:w-auto">
                @csrf
                <div class="relative flex-1 md:w-64">
                    <input type="text" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}" 
                           placeholder="Contoh: JNT9827361829" required
                           class="w-full bg-white border border-hairline px-3.5 py-2 text-xs rounded-full font-mono text-ink focus:outline-none focus:border-ink uppercase">
                </div>
                <button type="submit" class="px-4 py-2 bg-ink hover:opacity-90 text-white rounded-full text-xs font-medium transition flex items-center gap-1.5 whitespace-nowrap cursor-pointer">
                    <span>⚡ Simpan & Kirim</span>
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        {{-- 1. DAFTAR ITEM JERSEY & KUSTOMISASI (8 COLS) --}}
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-hairline-soft space-y-4">
                <h2 class="font-medium text-sm text-ink border-b border-hairline-soft pb-3">Spesifikasi Item Jersey & Nameset</h2>

                <div class="divide-y divide-hairline-soft">
                    @foreach($order->items as $item)
                        <div class="py-4 first:pt-0 last:pb-0 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 text-xs">
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-20 bg-soft-cloud rounded-xl overflow-hidden flex-shrink-0 border border-hairline-soft">
                                    @php
                                        $img = $item->product?->thumbnail_front ? asset('storage/' . $item->product->thumbnail_front) : 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=200&q=80';
                                    @endphp
                                    <img src="{{ $img }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                </div>
                                <div class="space-y-1">
                                    <h3 class="font-medium text-ink text-sm uppercase">{{ $item->product_name }}</h3>
                                    <p class="text-mute">
                                        Ukuran: <strong class="text-ink">{{ $item->size }}</strong> ({{ $item->type }}) &times; {{ $item->quantity }} pcs
                                    </p>
                                    
                                    {{-- Customization Details --}}
                                    <div class="flex flex-wrap gap-1.5 pt-1">
                                        @if($item->custom_name || $item->custom_number)
                                            <span class="px-2.5 py-0.5 bg-ink text-white rounded-full text-[10px] font-medium font-jersey tracking-widest uppercase">
                                                ⚡ SABLON: {{ $item->custom_name ?? '-' }} #{{ $item->custom_number ?? '0' }}
                                            </span>
                                        @endif
                                        @if($item->selected_patch)
                                            <span class="px-2.5 py-0.5 bg-soft-cloud border border-hairline text-ink rounded-full text-[10px] font-medium uppercase">
                                                ★ {{ $item->selected_patch }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="text-right w-full sm:w-auto">
                                <div class="font-medium text-sm text-ink tabular-nums">
                                    Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                </div>
                                <span class="text-[10px] text-mute">
                                    (Rp {{ number_format($item->unit_price + $item->custom_fee, 0, ',', '.') }} / pcs)
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Biteship Live Tracking Timeline --}}
            @if($trackingInfo)
                <div class="bg-white p-6 rounded-2xl border border-hairline-soft space-y-4 text-xs">
                    <h2 class="font-medium text-sm text-ink border-b border-hairline-soft pb-3 flex justify-between items-center">
                        <span>🚚 Pelacakan Biteship Real-Time</span>
                        <span class="font-mono text-ink font-medium">{{ $order->tracking_number }}</span>
                    </h2>

                    @if(!empty($trackingInfo['history']))
                        <div class="border-l-2 border-ink pl-4 space-y-3 ml-2">
                            @foreach($trackingInfo['history'] as $h)
                                <div class="relative">
                                    <div class="absolute -left-[21px] top-1 w-2.5 h-2.5 rounded-full bg-ink ring-4 ring-white"></div>
                                    <p class="font-medium text-ink">{{ $h['note'] ?? $h['message'] }}</p>
                                    <p class="text-[10px] text-mute">{{ $h['updated_at'] ?? '' }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </div>

        {{-- 2. ALAMAT PENGIRIMAN & FINANSIAL (4 COLS) --}}
        <div class="lg:col-span-4 space-y-6">
            
            {{-- Alamat Penerima Snapshot --}}
            <div class="bg-white p-6 rounded-2xl border border-hairline-soft space-y-3 text-xs">
                <h2 class="font-medium text-sm text-ink border-b border-hairline-soft pb-3">📍 Alamat Pengiriman</h2>
                @php
                    $addr = $order->shipping_address_snapshot ?? [];
                @endphp
                <div>
                    <strong class="text-ink block font-medium text-sm">{{ $addr['recipient_name'] ?? $order->customer_name }}</strong>
                    <p class="text-mute">{{ $addr['phone_number'] ?? $order->customer_phone }}</p>
                </div>
                <p class="text-ink leading-relaxed">
                    {{ $addr['full_address'] ?? '-' }}
                </p>
                <p class="text-[11px] text-mute">
                    {{ $addr['district_name'] ?? '' }}, {{ $addr['city_name'] ?? '' }} ({{ $addr['postal_code'] ?? '' }})
                </p>
                @if(!empty($addr['benchmark_notes']))
                    <div class="p-3 bg-soft-cloud border border-hairline rounded-xl text-[11px] text-ink">
                        <strong>Patokan:</strong> {{ $addr['benchmark_notes'] }}
                    </div>
                @endif
                @if(!empty($addr['latitude']) && !empty($addr['longitude']))
                    <div class="text-[10.5px] text-mute">
                        GPS: <a href="https://maps.google.com/?q={{ $addr['latitude'] }},{{ $addr['longitude'] }}" target="_blank" class="text-ink underline font-mono">{{ $addr['latitude'] }}, {{ $addr['longitude'] }}</a>
                    </div>
                @endif
            </div>

            {{-- Rincian Biaya & Pembayaran Midtrans --}}
            <div class="bg-white p-6 rounded-2xl border border-hairline-soft space-y-3 text-xs">
                <h2 class="font-medium text-sm text-ink border-b border-hairline-soft pb-3">💳 Rincian Finansial</h2>
                
                <div class="flex justify-between text-mute">
                    <span>Subtotal Jersey:</span>
                    <strong class="text-ink">{{ $order->formatted_subtotal_amount }}</strong>
                </div>
                <div class="flex justify-between text-mute">
                    <span>Ongkir ({{ $order->courier_code }}):</span>
                    <strong class="text-ink">{{ $order->formatted_shipping_cost }}</strong>
                </div>
                <div class="border-t border-hairline-soft pt-3 flex justify-between items-center text-sm">
                    <span class="font-medium text-ink">Total Transaksi:</span>
                    <span class="font-medium text-lg text-ink">{{ $order->formatted_grand_total }}</span>
                </div>

                @if($order->payment)
                    <div class="p-3 bg-soft-cloud rounded-xl space-y-1 text-[11px] text-mute mt-2">
                        <div class="flex justify-between">
                            <span>Status Gateway:</span>
                            <span class="font-medium uppercase text-emerald-700">{{ $order->payment->transaction_status }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Metode:</span>
                            <span class="font-medium uppercase text-ink">{{ $order->payment->payment_type ?? 'Midtrans' }}</span>
                        </div>
                        @if($order->payment->transaction_id)
                            <div class="flex justify-between">
                                <span>ID Transaksi:</span>
                                <span class="font-mono text-[10px] text-ink">{{ substr($order->payment->transaction_id, 0, 16) }}...</span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

        </div>

    </div>
</div>
@endsection
