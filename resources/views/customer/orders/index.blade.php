@extends('layouts.customer')

@section('title', 'Pesanan Saya · NGIZAN APPAREL')
@section('meta_description', 'Lacak status pesanan jersey, status pembayaran Midtrans, dan nomor resi pengiriman J&T Express Anda di Ngizan Apparel.')

@section('content')
    <div class="py-8 sm:py-14 bg-canvas min-h-screen" x-data="{
        filter: 'all',
        matches(status) {
            if (this.filter === 'all') return true;
            if (this.filter === 'pending' && status === 'pending_payment') return true;
            if (this.filter === 'process' && (status === 'paid' || status === 'in_production')) return true;
            if (this.filter === 'shipped' && status === 'shipped') return true;
            if (this.filter === 'completed' && status === 'completed') return true;
            if (this.filter === 'cancelled' && (status === 'cancelled' || status === 'expired')) return true;
            return false;
        }
    }">
        <div class="wrap">

            {{-- Header Section --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between border-b border-hairline-soft pb-5 sm:pb-8 mb-6 sm:mb-8 gap-4">
                <div class="space-y-1.5">
                    <span class="text-[11px] font-bold tracking-[0.2em] uppercase text-mute block">
                        Riwayat Transaksi & Pelacakan
                    </span>
                    <h1 class="font-display text-3xl sm:text-5xl lg:text-6xl tracking-tight text-ink uppercase leading-none">
                        PESANAN SAYA
                    </h1>
                    <p class="text-xs sm:text-sm text-mute pt-0.5 max-w-xl">
                        Kelola status pesanan, selesaikan pembayaran Midtrans, dan pantau pengiriman resmi J&T Express secara langsung.
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('shop.index') }}"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-soft-cloud hover:bg-neutral-200 border border-hairline-soft text-ink text-xs font-bold uppercase tracking-[0.12em] rounded-full shadow-2xs hover:shadow-xs transition active:scale-95 cursor-pointer">
                        <span>Katalog Jersey</span>
                        <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>
            </div>

            @if ($orders->isEmpty())
                {{-- Empty State --}}
                <div class="bg-soft-cloud rounded-3xl border border-hairline-soft p-8 sm:p-14 text-center max-w-lg mx-auto my-10 space-y-5">
                    <div class="w-16 h-16 rounded-full bg-white border border-hairline-soft flex items-center justify-center mx-auto text-2xl shadow-xs">
                        📦
                    </div>
                    <div class="space-y-2">
                        <h2 class="font-display text-2xl sm:text-3xl text-ink uppercase tracking-wide">
                            Belum Ada Riwayat Pesanan
                        </h2>
                        <p class="text-xs sm:text-sm text-mute leading-relaxed max-w-md mx-auto">
                            Anda belum memiliki pesanan aktif maupun riwayat transaksi. Jelajahi katalog jersey autentik dan
                            buat kit kustom impian Anda sekarang!
                        </p>
                    </div>
                    <div class="pt-2">
                        <a href="{{ route('shop.index') }}"
                            class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-ink hover:bg-neutral-800 text-white text-xs font-bold uppercase tracking-[0.12em] rounded-full shadow-2xs hover:shadow-xs transition active:scale-95 cursor-pointer">
                            <span>Mulai Belanja Jersey</span>
                            <span aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                </div>
            @else
                {{-- Filter Tabs Pills (Edge-to-edge scrollable rail on mobile) --}}
                <div class="-mx-4 px-4 sm:mx-0 sm:px-0 overflow-x-auto scrollbar-none pb-2 mb-6 sm:mb-8">
                    <div class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.12em] min-w-full sm:min-w-0">
                        <button type="button" @click="filter = 'all'"
                            :class="filter === 'all' ? 'bg-ink text-white shadow-2xs border-ink' : 'bg-soft-cloud text-ink hover:bg-neutral-200 border-hairline-soft'"
                            class="min-h-[40px] px-4 py-2 border rounded-full transition active:scale-95 whitespace-nowrap cursor-pointer">
                            Semua ({{ $orders->total() }})
                        </button>
                        <button type="button" @click="filter = 'pending'"
                            :class="filter === 'pending' ? 'bg-ink text-white shadow-2xs border-ink' : 'bg-soft-cloud text-ink hover:bg-neutral-200 border-hairline-soft'"
                            class="min-h-[40px] px-4 py-2 border rounded-full transition active:scale-95 whitespace-nowrap cursor-pointer">
                            Menunggu Pembayaran
                        </button>
                        <button type="button" @click="filter = 'process'"
                            :class="filter === 'process' ? 'bg-ink text-white shadow-2xs border-ink' : 'bg-soft-cloud text-ink hover:bg-neutral-200 border-hairline-soft'"
                            class="min-h-[40px] px-4 py-2 border rounded-full transition active:scale-95 whitespace-nowrap cursor-pointer">
                            Dikemas / Diproses
                        </button>
                        <button type="button" @click="filter = 'shipped'"
                            :class="filter === 'shipped' ? 'bg-ink text-white shadow-2xs border-ink' : 'bg-soft-cloud text-ink hover:bg-neutral-200 border-hairline-soft'"
                            class="min-h-[40px] px-4 py-2 border rounded-full transition active:scale-95 whitespace-nowrap cursor-pointer">
                            Sedang Dikirim
                        </button>
                        <button type="button" @click="filter = 'completed'"
                            :class="filter === 'completed' ? 'bg-ink text-white shadow-2xs border-ink' : 'bg-soft-cloud text-ink hover:bg-neutral-200 border-hairline-soft'"
                            class="min-h-[40px] px-4 py-2 border rounded-full transition active:scale-95 whitespace-nowrap cursor-pointer">
                            Selesai
                        </button>
                        <button type="button" @click="filter = 'cancelled'"
                            :class="filter === 'cancelled' ? 'bg-ink text-white shadow-2xs border-ink' : 'bg-soft-cloud text-ink hover:bg-neutral-200 border-hairline-soft'"
                            class="min-h-[40px] px-4 py-2 border rounded-full transition active:scale-95 whitespace-nowrap cursor-pointer">
                            Dibatalkan / Kedaluwarsa
                        </button>
                    </div>
                </div>

                {{-- Orders List --}}
                <div class="space-y-5 sm:space-y-6">
                    @foreach ($orders as $order)
                        @php
                            $statusValue = $order->status->value;
                            $isPending = $order->status === \App\Enums\OrderStatus::PENDING_PAYMENT;
                        @endphp
                        <div x-show="matches('{{ $statusValue }}')" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="bg-white rounded-2xl sm:rounded-3xl border border-hairline-soft p-4 sm:p-6 lg:p-7 space-y-5 hover:border-ink/40 transition duration-200 shadow-2xs">

                            {{-- Card Header: Responsive Order Header --}}
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-hairline-soft gap-3 sm:gap-4">
                                <div class="flex items-center justify-between sm:justify-start gap-2.5 sm:gap-3 flex-wrap">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-bold text-sm sm:text-base text-ink tracking-tight">
                                            #{{ $order->order_number }}
                                        </span>
                                        <span class="hidden sm:inline text-xs text-mute font-medium">
                                            &bull; {{ $order->created_at->format('d M Y, H:i') }} WIB
                                        </span>
                                    </div>
                                    <span
                                        class="text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-full {{ $order->status->badgeClass() }} inline-flex items-center gap-1.5">
                                        @if ($isPending)
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                        @elseif($order->status->isCompleted())
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        @elseif($order->status->isShipped())
                                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></span>
                                        @endif
                                        {{ $order->status->label() }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between sm:justify-end gap-3 pt-2 sm:pt-0 border-t sm:border-t-0 border-hairline-soft/60">
                                    <span class="sm:hidden text-[11px] text-mute font-medium">
                                        {{ $order->created_at->format('d M Y, H:i') }} WIB
                                    </span>
                                    <div class="text-right">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-mute block leading-tight">
                                            Total Tagihan
                                        </span>
                                        <span class="font-bold text-base sm:text-lg text-ink tabular-nums">
                                            {{ $order->formatted_grand_total }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Card Body: Items Snapshot --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                                @foreach ($order->items as $item)
                                    <div class="flex items-center gap-3 bg-soft-cloud/70 p-3 rounded-2xl border border-hairline-soft/80">
                                        <div class="w-14 h-18 sm:w-16 sm:h-20 bg-white rounded-xl overflow-hidden shrink-0 border border-hairline-soft">
                                            @php
                                                $img = $item->product?->thumbnail_front
                                                    ? asset('storage/' . $item->product->thumbnail_front)
                                                    : 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=200&q=80';
                                            @endphp
                                            <img src="{{ $img }}" alt="{{ $item->product_name }}"
                                                class="w-full h-full object-cover">
                                        </div>
                                        <div class="space-y-1 min-w-0 flex-1">
                                            <h4 class="font-bold text-xs sm:text-sm text-ink truncate" title="{{ $item->product_name }}">
                                                {{ $item->product_name }}
                                            </h4>
                                            <div class="flex items-center gap-1.5 flex-wrap text-[11px] text-mute">
                                                <span
                                                    class="font-semibold text-ink bg-white px-2 py-0.5 rounded-md border border-hairline-soft text-[10px]">
                                                    {{ $item->size }}
                                                </span>
                                                @if($item->type && !in_array(strtolower($item->type), ['standard', 'default', 'fans issue', 'player issue']))
                                                    <span class="truncate max-w-[100px]">{{ $item->type }}</span>
                                                @endif
                                                <span>&times; {{ $item->quantity }} pcs</span>
                                            </div>
                                            @if ($item->custom_name || $item->custom_number)
                                                <div class="pt-0.5">
                                                    <span
                                                        class="text-[10px] font-bold text-ink bg-white px-2 py-0.5 rounded-md border border-hairline-soft font-jersey uppercase tracking-wider inline-block">
                                                        #{{ $item->custom_name }} {{ $item->custom_number }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Card Footer: Courier info, Tracking snippet & CTA --}}
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between pt-4 border-t border-hairline-soft text-xs gap-3.5">
                                <div class="flex items-center gap-2 flex-wrap text-mute text-xs">
                                    <span class="bg-soft-cloud px-3 py-1 rounded-full border border-hairline-soft text-ink font-semibold inline-flex items-center gap-1.5 text-[11px]">
                                        <span class="uppercase">{{ $order->courier_service_name ?? 'J&T Express (Gratis Ongkir)' }}</span>
                                    </span>
                                    @if ($order->tracking_number)
                                        <span class="font-mono text-ink bg-soft-cloud px-2.5 py-1 rounded-full border border-hairline-soft font-semibold text-[11px]">
                                            Resi: {{ $order->tracking_number }}
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-3 w-full sm:w-auto">
                                    @if ($isPending)
                                        <a href="{{ route('customer.orders.show', $order->id) }}"
                                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 min-h-[44px] bg-ink hover:bg-neutral-800 text-white text-xs font-bold uppercase tracking-[0.12em] rounded-full shadow-2xs hover:shadow-xs transition active:scale-95 cursor-pointer w-full sm:w-auto">
                                            <span>Bayar Sekarang</span>
                                            <span aria-hidden="true">&rarr;</span>
                                        </a>
                                    @else
                                        <a href="{{ route('customer.orders.show', $order->id) }}"
                                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 min-h-[44px] bg-soft-cloud hover:bg-neutral-200 border border-hairline-soft text-ink text-xs font-bold uppercase tracking-[0.12em] rounded-full shadow-2xs hover:shadow-xs transition active:scale-95 cursor-pointer w-full sm:w-auto">
                                            <span>Detail & Lacak</span>
                                            <span aria-hidden="true">&rarr;</span>
                                        </a>
                                    @endif
                                </div>
                            </div>

                        </div>
                    @endforeach

                    {{-- Pagination --}}
                    <div class="pt-6 border-t border-hairline-soft flex justify-center">
                        {{ $orders->links() }}
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection
