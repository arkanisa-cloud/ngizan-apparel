@extends('layouts.customer')

@section('title', 'Pesanan Saya · NGIZAN APPAREL')
@section('meta_description', 'Lacak status pesanan jersey, status pembayaran Midtrans, dan nomor resi pengiriman Biteship Anda.')

@section('content')
<div class="py-10">
    <div class="wrap">
        
        {{-- Breadcrumb & Title --}}
        <div class="border-b border-black/10 pb-6 mb-8 flex justify-between items-end">
            <div>
                <span class="lbl text-ink-muted">RIWAYAT TRANSAKSI</span>
                <h1 class="font-display font-black text-2xl md:text-3xl text-ink uppercase tracking-tight mt-1">
                    Pesanan Saya
                </h1>
            </div>
            <a href="{{ route('shop.index') }}" class="btn-line py-2 px-4 text-xs">
                Katalog Jersey &rarr;
            </a>
        </div>

        @if($orders->isEmpty())
            <div class="text-center py-20 bg-white rounded border border-black/10 space-y-4 max-w-lg mx-auto my-8 p-8">
                <div class="text-5xl">📦</div>
                <h2 class="font-display font-black text-xl text-ink uppercase tracking-tight">Belum Ada Riwayat Pesanan</h2>
                <p class="text-xs text-ink-muted leading-relaxed">
                    Anda belum pernah melakukan pemesanan. Mulai belanja jersey autentik sekarang!
                </p>
                <a href="{{ route('shop.index') }}" class="btn-curtain py-3.5 px-8 text-xs inline-flex mt-2">
                    <span>Mulai Belanja &rarr;</span>
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($orders as $order)
                    <div class="bg-white p-6 rounded border border-black/10 space-y-4 shadow-sm hover:border-black/30 transition">
                        
                        {{-- Header Pesanan --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-black/10 gap-2">
                            <div class="flex items-center gap-3 flex-wrap">
                                <span class="font-display font-black text-sm text-ink uppercase tracking-wider">
                                    #{{ $order->order_number }}
                                </span>
                                <span class="text-xs text-ink-muted">
                                    · {{ $order->created_at->format('d M Y, H:i') }} WIB
                                </span>
                                <span class="text-[10px] uppercase font-bold px-2.5 py-0.5 rounded {{ $order->status->badgeClass() }}">
                                    {{ $order->status->label() }}
                                </span>
                            </div>

                            <div class="font-display font-black text-base text-ink tabular-nums">
                                {{ $order->formatted_grand_total }}
                            </div>
                        </div>

                        {{-- Item List Snapshot --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($order->items as $item)
                                <div class="flex items-center gap-3 text-xs">
                                    <div class="w-12 h-14 bg-canvas rounded overflow-hidden flex-shrink-0 border border-black/5">
                                        @php
                                            $img = $item->product?->thumbnail_front ? asset('storage/' . $item->product->thumbnail_front) : 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=150&q=80';
                                        @endphp
                                        <img src="{{ $img }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="space-y-0.5">
                                        <h4 class="font-bold text-ink uppercase line-clamp-1">{{ $item->product_name }}</h4>
                                        <p class="text-[11px] text-ink-muted">
                                            Ukuran: <strong>{{ $item->size }}</strong> ({{ $item->type }}) &times; {{ $item->quantity }} pcs
                                        </p>
                                        @if($item->custom_name || $item->custom_number)
                                            <span class="text-[9.5px] font-bold text-cyan-700 bg-cyan-50 px-1.5 py-0.2 rounded font-jersey">
                                                #{{ $item->custom_name }} {{ $item->custom_number }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Footer & Action --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between pt-3 border-t border-black/10 text-xs gap-3">
                            <div class="text-ink-muted text-[11px]">
                                Kurir: <strong class="text-ink uppercase">{{ $order->courier_service_name ?? 'Reguler' }}</strong>
                                @if($order->tracking_number)
                                    · No. Resi: <strong class="text-cyan-700 font-mono">{{ $order->tracking_number }}</strong>
                                @endif
                            </div>

                            <a href="{{ route('customer.orders.show', $order->id) }}" class="btn-line py-2 px-4 text-xs text-center inline-block">
                                Lihat Detail & Lacak Status &rarr;
                            </a>
                        </div>

                    </div>
                @endforeach

                <div class="pt-4">
                    {{ $orders->links() }}
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
