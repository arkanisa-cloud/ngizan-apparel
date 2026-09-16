@extends('layouts.customer')

@section('title', 'Pesanan Saya · NGIZAN APPAREL')
@section('meta_description', 'Lacak status pesanan jersey, status pembayaran Midtrans, dan nomor resi pengiriman Biteship Anda.')

@section('content')
<div class="py-8 bg-canvas">
    <div class="wrap">
        
        {{-- Header & Title --}}
        <div class="border-b border-hairline-soft pb-5 mb-8 flex justify-between items-baseline">
            <div>
                <span class="text-xs font-medium uppercase tracking-widest text-mute block mb-1">Riwayat Transaksi</span>
                <h1 class="text-2xl sm:text-3xl font-medium tracking-tight text-ink">
                    Pesanan Saya
                </h1>
            </div>
            <a href="{{ route('shop.index') }}" class="text-xs font-medium text-ink hover:text-mute underline">
                Katalog Jersey &rarr;
            </a>
        </div>

        @if($orders->isEmpty())
            <div class="text-center py-20 bg-soft-cloud border border-hairline-soft max-w-lg mx-auto my-8 p-8 space-y-4">
                <h2 class="text-xl font-medium text-ink">Belum Ada Riwayat Pesanan</h2>
                <p class="text-xs text-mute leading-relaxed">
                    Anda belum pernah melakukan pemesanan. Mulai belanja jersey autentik sekarang!
                </p>
                <a href="{{ route('shop.index') }}" class="btn-primary py-3 px-8 text-xs inline-flex mt-2 rounded-full">
                    <span>Mulai Belanja &rarr;</span>
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach($orders as $order)
                    <div class="bg-white p-6 border border-hairline-soft space-y-4 hover:border-ink transition">
                        
                        {{-- Header Pesanan --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-hairline-soft gap-2">
                            <div class="flex items-center gap-3 flex-wrap">
                                <span class="font-medium text-sm text-ink tracking-wider">
                                    #{{ $order->order_number }}
                                </span>
                                <span class="text-xs text-mute">
                                    · {{ $order->created_at->format('d M Y, H:i') }} WIB
                                </span>
                                <span class="text-[10px] uppercase font-medium px-3 py-0.5 rounded-full {{ $order->status->badgeClass() }}">
                                    {{ $order->status->label() }}
                                </span>
                            </div>

                            <div class="font-medium text-base text-ink tabular-nums">
                                {{ $order->formatted_grand_total }}
                            </div>
                        </div>

                        {{-- Item List Snapshot --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($order->items as $item)
                                <div class="flex items-center gap-3 text-xs">
                                    <div class="w-14 h-16 bg-soft-cloud overflow-hidden flex-shrink-0">
                                        @php
                                            $img = $item->product?->thumbnail_front ? asset('storage/' . $item->product->thumbnail_front) : 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=150&q=80';
                                        @endphp
                                        <img src="{{ $img }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                    </div>
                                    <div class="space-y-0.5">
                                        <h4 class="font-medium text-ink line-clamp-1">{{ $item->product_name }}</h4>
                                        <p class="text-[11px] text-mute">
                                            Ukuran: <strong class="text-ink">{{ $item->size }}</strong> ({{ $item->type }}) &times; {{ $item->quantity }} pcs
                                        </p>
                                        @if($item->custom_name || $item->custom_number)
                                            <span class="text-[10px] font-medium text-ink bg-soft-cloud px-2 py-0.5 rounded-full font-jersey">
                                                #{{ $item->custom_name }} {{ $item->custom_number }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Footer & Action --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between pt-4 border-t border-hairline-soft text-xs gap-3">
                            <div class="text-mute text-[11px]">
                                Kurir: <strong class="text-ink uppercase">{{ $order->courier_service_name ?? 'Reguler' }}</strong>
                                @if($order->tracking_number)
                                    · No. Resi: <strong class="text-ink font-mono">{{ $order->tracking_number }}</strong>
                                @endif
                            </div>

                            <a href="{{ route('customer.orders.show', $order->id) }}" class="btn-secondary py-2 px-5 text-xs text-center inline-block rounded-full">
                                Lihat Detail & Lacak &rarr;
                            </a>
                        </div>

                    </div>
                @endforeach

                <div class="pt-4 border-t border-hairline-soft">
                    {{ $orders->links() }}
                </div>
            </div>
        @endif

    </div>
</div>
@endsection
