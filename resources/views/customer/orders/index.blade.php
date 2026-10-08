@extends('layouts.customer')

@section('title', 'Pesanan Saya · NGIZAN APPAREL')
@section('meta_description', 'Lacak status pesanan jersey, status pembayaran Midtrans, dan nomor resi pengiriman J&T Express Anda di Ngizan Apparel.')

@section('content')
    <div class="py-10 sm:py-16 bg-canvas" x-data="{
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
            <div class="flex flex-col md:flex-row md:items-end justify-between border-b border-hairline-soft pb-6 sm:pb-8 mb-8 sm:mb-10 gap-4">
                <div class="space-y-1.5">
                    <span class="text-[11px] font-bold tracking-[0.2em] uppercase text-mute block">
                        Riwayat Transaksi & Pelacakan
                    </span>
                    <h1 class="font-display text-4xl sm:text-6xl tracking-tight text-ink uppercase leading-none">
                        PESANAN SAYA
                    </h1>
                    <p class="text-xs sm:text-sm text-mute pt-1">
                        Kelola status pesanan, selesaikan pembayaran Midtrans, dan pantau pengiriman J&T Express real-time.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('shop.index') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-soft-cloud hover:bg-neutral-200 border border-hairline-soft text-ink text-xs font-bold uppercase tracking-[0.12em] rounded-full shadow-2xs hover:shadow-xs transition active:scale-95 cursor-pointer">
                        <span>Katalog Jersey</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            @if ($orders->isEmpty())
                {{-- Empty State --}}
                <div class="bg-soft-cloud rounded-3xl border border-hairline-soft p-10 sm:p-16 text-center max-w-xl mx-auto my-8 space-y-5">
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
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-ink hover:bg-neutral-800 text-white text-xs font-bold uppercase tracking-[0.12em] rounded-full shadow-2xs hover:shadow-xs transition active:scale-95 cursor-pointer">
                            <span>Mulai Belanja Jersey</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>
            @else
                {{-- Filter Tabs Pills --}}
                <div class="flex items-center gap-2 overflow-x-auto pb-4 mb-6 scrollbar-none text-xs font-bold uppercase tracking-[0.12em]">
                    <button type="button" @click="filter = 'all'"
                        :class="filter === 'all' ? 'bg-ink text-white shadow-2xs' : 'bg-soft-cloud text-ink hover:bg-neutral-200 border border-hairline-soft'"
                        class="px-4 py-2 rounded-full transition active:scale-95 whitespace-nowrap cursor-pointer">
                        Semua ({{ $orders->total() }})
                    </button>
                    <button type="button" @click="filter = 'pending'"
                        :class="filter === 'pending' ? 'bg-ink text-white shadow-2xs' : 'bg-soft-cloud text-ink hover:bg-neutral-200 border border-hairline-soft'"
                        class="px-4 py-2 rounded-full transition active:scale-95 whitespace-nowrap cursor-pointer">
                        Menunggu Pembayaran
                    </button>
                    <button type="button" @click="filter = 'process'"
                        :class="filter === 'process' ? 'bg-ink text-white shadow-2xs' : 'bg-soft-cloud text-ink hover:bg-neutral-200 border border-hairline-soft'"
                        class="px-4 py-2 rounded-full transition active:scale-95 whitespace-nowrap cursor-pointer">
                        Dikemas / Diproses
                    </button>
                    <button type="button" @click="filter = 'shipped'"
                        :class="filter === 'shipped' ? 'bg-ink text-white shadow-2xs' : 'bg-soft-cloud text-ink hover:bg-neutral-200 border border-hairline-soft'"
                        class="px-4 py-2 rounded-full transition active:scale-95 whitespace-nowrap cursor-pointer">
                        Sedang Dikirim
                    </button>
                    <button type="button" @click="filter = 'completed'"
                        :class="filter === 'completed' ? 'bg-ink text-white shadow-2xs' : 'bg-soft-cloud text-ink hover:bg-neutral-200 border border-hairline-soft'"
                        class="px-4 py-2 rounded-full transition active:scale-95 whitespace-nowrap cursor-pointer">
                        Selesai
                    </button>
                    <button type="button" @click="filter = 'cancelled'"
                        :class="filter === 'cancelled' ? 'bg-ink text-white shadow-2xs' : 'bg-soft-cloud text-ink hover:bg-neutral-200 border border-hairline-soft'"
                        class="px-4 py-2 rounded-full transition active:scale-95 whitespace-nowrap cursor-pointer">
                        Dibatalkan / Kadaluarsa
                    </button>
                </div>

                {{-- Orders List --}}
                <div class="space-y-6">
                    @foreach ($orders as $order)
                        @php
                            $statusValue = $order->status->value;
                            $isPending = $order->status === \App\Enums\OrderStatus::PENDING_PAYMENT;
                        @endphp
                        <div x-show="matches('{{ $statusValue }}')" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="bg-white rounded-3xl border border-hairline-soft p-6 sm:p-8 space-y-6 hover:border-ink/50 transition duration-200 shadow-xs">

                            {{-- Card Header: Order Number, Date, Status Badge, and Grand Total --}}
                            <div class="flex flex-col lg:flex-row lg:items-center justify-between pb-5 border-b border-hairline-soft gap-4">
                                <div class="flex items-center gap-3 flex-wrap">
                                    <span class="font-mono font-bold text-sm sm:text-base text-ink tracking-tight">
                                        #{{ $order->order_number }}
                                    </span>
                                    <span class="text-xs text-mute font-medium">
                                        &bull; {{ $order->created_at->format('d M Y, H:i') }} WIB
                                    </span>
                                    <span
                                        class="text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-full {{ $order->status->badgeClass() }} inline-flex items-center gap-1.5">
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

                                <div class="flex items-center justify-between lg:justify-end gap-4">
                                    <div class="text-left lg:text-right">
                                        <span class="text-[10px] font-bold uppercase tracking-wider text-mute block">
                                            Total Pembayaran
                                        </span>
                                        <span class="font-bold text-base sm:text-lg text-ink tabular-nums">
                                            {{ $order->formatted_grand_total }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Card Body: Items Snapshot --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                                @foreach ($order->items as $item)
                                    <div class="flex items-center gap-3.5 bg-soft-cloud/60 p-3.5 rounded-2xl border border-hairline-soft/80">
                                        <div class="w-16 h-20 bg-white rounded-xl overflow-hidden flex-shrink-0 border border-hairline-soft">
                                            @php
                                                $img = $item->product?->thumbnail_front
                                                    ? asset('storage/' . $item->product->thumbnail_front)
                                                    : 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=200&q=80';
                                            @endphp
                                            <img src="{{ $img }}" alt="{{ $item->product_name }}"
                                                class="w-full h-full object-cover">
                                        </div>
                                        <div class="space-y-1 min-w-0 flex-1">
                                            <h4 class="font-bold text-xs sm:text-sm text-ink truncate">
                                                {{ $item->product_name }}</h4>
                                            <div class="flex items-center gap-1.5 flex-wrap text-[11px] text-mute">
                                                <span
                                                    class="font-semibold text-ink bg-white px-2 py-0.5 rounded-md border border-hairline-soft">{{ $item->size }}</span>
                                                <span>{{ $item->type }}</span>
                                                <span>&times; {{ $item->quantity }} pcs</span>
                                            </div>
                                            @if ($item->custom_name || $item->custom_number)
                                                <div class="pt-0.5">
                                                    <span
                                                        class="text-[10px] font-bold text-ink bg-white px-2 py-0.5 rounded-full border border-hairline font-jersey uppercase tracking-wider inline-block">
                                                        #{{ $item->custom_name }} {{ $item->custom_number }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Card Footer: Courier info, Tracking snippet & CTA --}}
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between pt-4 border-t border-hairline-soft text-xs gap-4">
                                <div class="flex items-center gap-2 flex-wrap text-mute text-xs">
                                    <span class="bg-soft-cloud px-3 py-1 rounded-full border border-hairline-soft text-ink font-semibold inline-flex items-center gap-1.5">
                                        <span class="uppercase">{{ $order->courier_service_name ?? 'J&T Express (Gratis Ongkir)' }}</span>
                                    </span>
                                    @if ($order->tracking_number)
                                        <span class="font-mono text-ink bg-soft-cloud px-2.5 py-1 rounded-full border border-hairline-soft font-semibold">
                                            Resi: {{ $order->tracking_number }}
                                        </span>
                                    @endif
                                </div>

                                <div class="flex items-center gap-3 w-full sm:w-auto">
                                    @if ($isPending)
                                        <a href="{{ route('customer.orders.show', $order->id) }}"
                                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-ink hover:bg-neutral-800 text-white text-xs font-bold uppercase tracking-[0.12em] rounded-full shadow-2xs hover:shadow-xs transition active:scale-95 cursor-pointer w-full sm:w-auto">
                                            <span>Bayar Sekarang</span>
                                            <span>&rarr;</span>
                                        </a>
                                    @else
                                        <a href="{{ route('customer.orders.show', $order->id) }}"
                                            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-soft-cloud hover:bg-neutral-200 border border-hairline-soft text-ink text-xs font-bold uppercase tracking-[0.12em] rounded-full shadow-2xs hover:shadow-xs transition active:scale-95 cursor-pointer w-full sm:w-auto">
                                            <span>Detail & Lacak</span>
                                            <span>&rarr;</span>
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
