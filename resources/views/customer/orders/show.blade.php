@extends('layouts.customer')

@section('title', 'Detail Pesanan #' . $order->order_number . ' · NGIZAN APPAREL')
@section('meta_description', 'Detail pesanan, status pembayaran Midtrans, dan pelacakan langsung pengiriman J&T Express di Ngizan Apparel.')

@section('content')

@php
    $isPending = $order->status === \App\Enums\OrderStatus::PENDING_PAYMENT;
    $snapToken = $order->payment?->snap_token;
    $expiresAtTimestamp = $order->expires_at ? $order->expires_at->timestamp * 1000 : null;
    $addr = $order->shipping_address_snapshot ?? [];
    
    // Generate Google Maps URL
    $gmapsUrl = null;
    if (!empty($addr['latitude']) && !empty($addr['longitude'])) {
        $gmapsUrl = 'https://www.google.com/maps?q=' . $addr['latitude'] . ',' . $addr['longitude'];
    } elseif (!empty($addr['full_address'])) {
        $queryParts = array_filter([
            $addr['full_address'] ?? '',
            $addr['district_name'] ?? '',
            $addr['city_name'] ?? '',
            $addr['province_name'] ?? '',
            $addr['postal_code'] ?? '',
        ]);
        $gmapsUrl = 'https://www.google.com/maps/search/?api=1&query=' . urlencode(implode(', ', $queryParts));
    }
@endphp

<div class="py-10 sm:py-16 bg-canvas" x-data="{
    expiresAt: {{ $expiresAtTimestamp ?? 'null' }},
    timeLeft: '',
    timerInterval: null,
    init() {
        if (this.expiresAt) {
            this.updateCountdown();
            this.timerInterval = setInterval(() => this.updateCountdown(), 1000);
        }
    },
    updateCountdown() {
        const now = new Date().getTime();
        const dist = this.expiresAt - now;
        if (dist <= 0) {
            this.timeLeft = 'Kedaluwarsa';
            if (this.timerInterval) clearInterval(this.timerInterval);
            return;
        }
        const hours = Math.floor((dist % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((dist % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((dist % (1000 * 60)) / 1000);
        this.timeLeft = `${hours}j ${minutes}m ${seconds}d`;
    },
    payNow() {
        @if($snapToken)
            if (typeof window.snap !== 'undefined') {
                window.snap.pay('{{ $snapToken }}', {
                    onSuccess: (result) => {
                        this.syncAndReload('Pembayaran berhasil! Mengupdate status pesanan...');
                    },
                    onPending: (result) => {
                        this.syncAndReload('Menunggu penyelesaian pembayaran...');
                    },
                    onError: (result) => {
                        toastr.error('Pembayaran gagal atau dibatalkan.');
                        window.location.reload();
                    },
                    onClose: () => {
                        this.syncAndReload();
                    }
                });
            } else {
                toastr.error('Midtrans Snap SDK tidak tersedia.');
            }
        @else
            toastr.error('Token pembayaran tidak ditemukan.');
        @endif
    },
    async syncAndReload(msg = null) {
        if (msg && typeof toastr !== 'undefined') toastr.info(msg);
        try {
            await fetch('{{ route('customer.orders.sync-payment', $order->id) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });
        } catch (e) {
            console.error('Sync payment error:', e);
        }
        window.location.reload();
    }
}">
    <div class="wrap">
        
        {{-- Breadcrumb Navigation --}}
        <nav class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-mute mb-8">
            <a href="{{ route('home') }}" class="hover:text-ink transition">Beranda</a>
            <span class="text-stone">/</span>
            <a href="{{ route('customer.orders.index') }}" class="hover:text-ink transition">Pesanan Saya</a>
            <span class="text-stone">/</span>
            <span class="text-ink font-mono font-bold">#{{ $order->order_number }}</span>
        </nav>

        {{-- Hero Status Banner --}}
        <div class="rounded-3xl border p-6 sm:p-8 mb-8 sm:mb-10 transition {{ $isPending ? 'bg-ink text-white border-ink shadow-lg' : 'bg-white border-hairline-soft text-ink shadow-xs' }}">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="flex items-center gap-3 flex-wrap">
                        <span class="font-display text-2xl sm:text-4xl tracking-tight uppercase leading-none {{ $isPending ? 'text-white' : 'text-ink' }}">
                            PESANAN #{{ $order->order_number }}
                        </span>
                        <span class="text-[10px] font-bold uppercase tracking-wider px-3.5 py-1 rounded-full {{ $isPending ? 'bg-amber-400 text-ink font-extrabold' : $order->status->badgeClass() }} inline-flex items-center gap-1.5">
                            @if($isPending)
                                <span class="w-1.5 h-1.5 rounded-full bg-ink animate-pulse"></span>
                            @endif
                            {{ $order->status->label() }}
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm {{ $isPending ? 'text-neutral-300' : 'text-mute' }}">
                        Dibuat pada {{ $order->created_at->format('d F Y, H:i') }} WIB &bull; Metode: <strong class="{{ $isPending ? 'text-white' : 'text-ink' }}">{{ strtoupper($order->payment?->payment_type ?? 'Midtrans Snap') }}</strong>
                    </p>
                </div>

                {{-- Action / Countdown Button --}}
                @if($isPending)
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 pt-2 lg:pt-0 border-t lg:border-t-0 border-white/15">
                        <div class="text-left sm:text-right" x-show="timeLeft">
                            <span class="text-[10px] text-neutral-300 font-bold uppercase tracking-widest block">Sisa Waktu Pembayaran</span>
                            <span class="font-mono font-extrabold text-lg sm:text-xl text-amber-300 tabular-nums" x-text="timeLeft"></span>
                        </div>

                        <button type="button" 
                                @click="payNow()" 
                                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-white hover:bg-neutral-100 text-ink text-xs font-bold uppercase tracking-[0.12em] rounded-full shadow-2xs hover:shadow-xs transition active:scale-95 cursor-pointer w-full sm:w-auto">
                            <span>Bayar Sekarang</span>
                            <span>&rarr;</span>
                        </button>
                    </div>
                @else
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold uppercase tracking-wider bg-soft-cloud px-4 py-2 rounded-full border border-hairline-soft text-ink inline-flex items-center gap-2">
                            <span>✓</span>
                            <span>{{ $order->status->isCompleted() ? 'Pesanan Selesai' : 'Pesanan Terkonfirmasi' }}</span>
                        </span>
                    </div>
                @endif
            </div>
        </div>

        {{-- 2-Column Content Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- 1. LEFT COLUMN: Order Items & J&T Tracking (8 Cols) --}}
            <div class="lg:col-span-8 space-y-8">
                
                {{-- Daftar Jersey Pesanan --}}
                <div class="bg-white rounded-3xl border border-hairline-soft p-6 sm:p-8 space-y-6 shadow-xs">
                    <div class="flex items-center justify-between border-b border-hairline-soft pb-4">
                        <h2 class="font-display text-xl sm:text-2xl text-ink uppercase tracking-wide">
                            Daftar Jersey Pesanan ({{ $order->items->count() }} Item)
                        </h2>
                        <span class="text-xs font-bold uppercase tracking-wider text-mute">
                            {{ $order->items->sum('quantity') }} Total Pcs
                        </span>
                    </div>

                    <div class="divide-y divide-hairline-soft">
                        @foreach($order->items as $item)
                            <div class="py-6 first:pt-0 last:pb-0 space-y-4">
                                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-20 h-24 sm:w-24 sm:h-28 bg-soft-cloud rounded-2xl overflow-hidden border border-hairline-soft flex-shrink-0">
                                            @php
                                                $img = $item->product?->thumbnail_front ? asset('storage/' . $item->product->thumbnail_front) : 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=250&q=80';
                                            @endphp
                                            <img src="{{ $img }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                        </div>
                                        <div class="space-y-1.5 min-w-0">
                                            <h3 class="font-bold text-sm sm:text-base text-ink leading-snug">
                                                {{ $item->product_name }}
                                            </h3>
                                            <div class="flex items-center gap-2 flex-wrap text-xs text-mute">
                                                <span class="font-bold text-ink bg-soft-cloud px-2.5 py-0.5 rounded-full border border-hairline-soft">
                                                    Ukuran {{ $item->size }}
                                                </span>
                                                <span>{{ $item->type }}</span>
                                                <span>&bull;</span>
                                                <span>{{ $item->quantity }} pcs</span>
                                            </div>
                                            <div class="flex flex-wrap gap-1.5 pt-1">
                                                @if($item->custom_name || $item->custom_number)
                                                    <span class="text-[11px] font-bold bg-ink text-white px-3 py-1 rounded-full font-jersey tracking-wider uppercase inline-flex items-center gap-1 shadow-2xs">
                                                        <span>SABLON:</span>
                                                        <span>{{ $item->custom_name ?? '-' }} #{{ $item->custom_number ?? '0' }}</span>
                                                    </span>
                                                @endif
                                                @if($item->selected_patch)
                                                    <span class="text-[10px] font-bold bg-soft-cloud text-ink border border-hairline-soft px-3 py-1 rounded-full uppercase inline-flex items-center gap-1">
                                                        <span>★</span>
                                                        <span>{{ $item->selected_patch }}</span>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-left sm:text-right w-full sm:w-auto pt-2 sm:pt-0 border-t sm:border-t-0 border-hairline-soft">
                                        <div class="font-bold text-base sm:text-lg text-ink tabular-nums">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </div>
                                        <span class="text-[11px] text-mute font-medium">
                                            Rp {{ number_format($item->unit_price + $item->custom_fee, 0, ',', '.') }} / pcs
                                        </span>
                                    </div>
                                </div>

                                {{-- Verified Buyer Review Section (When Order is Completed) --}}
                                @if($order->status->isCompleted() && $item->product_id)
                                    @php
                                        $userReview = \App\Models\Review::where('order_id', $order->id)
                                            ->where('product_id', $item->product_id)
                                            ->where('user_id', auth()->id())
                                            ->first();
                                    @endphp

                                    @if($userReview)
                                        <div class="bg-soft-cloud border border-hairline-soft rounded-2xl p-4 text-ink flex flex-col sm:flex-row sm:items-center justify-between gap-3 mt-2">
                                            <div class="space-y-1">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-bold text-xs uppercase tracking-wider">Ulasan Anda:</span>
                                                    <div class="flex text-premium-gold text-sm tracking-tighter">
                                                        @for($s = 1; $s <= 5; $s++)
                                                            <span>{{ $s <= $userReview->rating ? '★' : '☆' }}</span>
                                                        @endfor
                                                    </div>
                                                    <span class="text-[11px] text-mute font-mono font-bold">({{ $userReview->rating }}/5)</span>
                                                </div>
                                                <p class="text-mute text-xs italic">"{{ $userReview->comment }}"</p>
                                            </div>
                                            <span class="text-[10px] bg-white border border-hairline-soft text-ink font-bold px-3 py-1 rounded-full uppercase tracking-wider self-start sm:self-center whitespace-nowrap shadow-2xs">
                                                ✓ Verified Buyer
                                            </span>
                                        </div>
                                    @else
                                        <div class="pt-2" x-data="{ openReviewModal: false }">
                                            <button type="button" @click="openReviewModal = true" 
                                                class="inline-flex items-center gap-2 px-4 py-2 bg-soft-cloud hover:bg-neutral-200 border border-hairline-soft text-ink text-xs font-bold uppercase tracking-[0.12em] rounded-full shadow-2xs hover:shadow-xs transition active:scale-95 cursor-pointer">
                                                <span>⭐ Tulis Ulasan & Rating</span>
                                            </button>

                                            {{-- Modal Ulasan --}}
                                            <div x-show="openReviewModal" @click.away="openReviewModal = false" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4 backdrop-blur-xs">
                                                <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 space-y-5 shadow-2xl border border-hairline-soft text-left animate-in fade-in zoom-in-95 duration-200">
                                                    <div class="flex justify-between items-center border-b border-hairline-soft pb-4">
                                                        <h3 class="font-display text-xl text-ink uppercase tracking-wide">Ulas {{ $item->product_name }}</h3>
                                                        <button type="button" @click="openReviewModal = false" class="text-mute hover:text-ink text-2xl font-bold transition">&times;</button>
                                                    </div>
                                                    <p class="text-xs text-mute leading-relaxed">
                                                        Bagikan pengalaman kepuasan Anda mengenai kualitas kain, jahitan jersey, serta sablon nama & nomor punggung resmi NGIZAN Apparel.
                                                    </p>
                                                    <form action="{{ route('customer.reviews.store') }}" method="POST" class="space-y-4" x-data="{ currentRating: 5 }">
                                                        @csrf
                                                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                        <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                                        <input type="hidden" name="rating" :value="currentRating">

                                                        <div>
                                                            <label class="block text-xs font-bold uppercase tracking-wider text-ink mb-2">Kepuasan Produk</label>
                                                            <div class="flex items-center gap-1.5 bg-soft-cloud p-3 rounded-2xl border border-hairline-soft">
                                                                <template x-for="star in [1, 2, 3, 4, 5]">
                                                                    <button type="button" @click="currentRating = star" class="text-2xl transition transform hover:scale-125 focus:outline-none" :class="star <= currentRating ? 'text-amber-500' : 'text-neutral-300'">
                                                                        ★
                                                                    </button>
                                                                </template>
                                                                <span class="text-xs font-bold text-ink ml-3 font-mono" x-text="currentRating + ' / 5 Bintang'"></span>
                                                            </div>
                                                        </div>

                                                        <div>
                                                            <label class="block text-xs font-bold uppercase tracking-wider text-ink mb-2">Komentar & Testimoni</label>
                                                            <textarea name="comment" rows="3" required minlength="5" placeholder="Kualitas jersey autentik sangat memuaskan, bahan adem, sablon presisi, pengiriman J&T sangat cepat!" class="w-full text-xs p-3.5 bg-soft-cloud border border-hairline-soft rounded-2xl focus:border-ink focus:bg-white focus:outline-none transition"></textarea>
                                                        </div>

                                                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-hairline-soft">
                                                            <button type="button" @click="openReviewModal = false" class="px-5 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider text-mute hover:text-ink transition active:scale-95">Batal</button>
                                                            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-ink hover:bg-neutral-800 text-white text-xs font-bold uppercase tracking-[0.12em] rounded-full shadow-2xs hover:shadow-xs transition active:scale-95 cursor-pointer">Kirim Ulasan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Pelacakan Ekspedisi J&T Express (Binderbyte Live Tracking) --}}
                @if($order->tracking_number)
                    <div class="bg-white rounded-3xl border border-hairline-soft p-6 sm:p-8 space-y-6 shadow-xs" x-data>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-hairline-soft pb-4 gap-3">
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <h2 class="font-display text-xl sm:text-2xl text-ink uppercase tracking-wide">
                                    🚚 Pelacakan Ekspedisi J&T Express
                                </h2>
                                <span class="text-[10px] font-bold bg-soft-cloud border border-hairline-soft text-ink px-3 py-1 rounded-full uppercase tracking-wider">
                                    Kemitraan Resmi
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-mono font-bold text-ink bg-soft-cloud px-3 py-1.5 rounded-full border border-hairline-soft">
                                    {{ $order->tracking_number }}
                                </span>
                                <button type="button" 
                                        @click="navigator.clipboard.writeText('{{ $order->tracking_number }}'); toastr.success('Nomor resi J&T Express berhasil disalin!');" 
                                        class="px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-[0.12em] bg-ink hover:bg-neutral-800 text-white rounded-full transition shadow-2xs active:scale-95 cursor-pointer">
                                    Salin Resi
                                </button>
                            </div>
                        </div>

                        <div class="space-y-6 text-xs">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 bg-soft-cloud p-4 rounded-2xl border border-hairline-soft">
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-mute block mb-0.5">Layanan Kurir</span>
                                    <span class="font-bold text-ink uppercase">{{ $order->courier_service_name ?? 'J&T Express EZ (Reguler Kilat)' }}</span>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-mute block mb-0.5">Status Pengiriman Terkini</span>
                                    <span class="font-bold text-ink uppercase inline-flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        {{ $trackingInfo['status'] ?? ($order->status->isCompleted() ? 'DELIVERED (Terkirim)' : 'ON_DELIVERY (Dalam Pengiriman)') }}
                                    </span>
                                </div>
                            </div>

                            @if(!empty($trackingInfo['history']))
                                <div class="border-l-2 border-ink pl-5 space-y-5 ml-3 my-2">
                                    @foreach($trackingInfo['history'] as $history)
                                        <div class="relative">
                                            <div class="absolute -left-[27px] top-1 w-3 h-3 rounded-full bg-ink ring-4 ring-white"></div>
                                            <p class="font-bold text-xs text-ink">{{ $history['note'] ?? $history['message'] }}</p>
                                            <p class="text-[11px] text-mute font-mono mt-0.5">{{ $history['updated_at'] ?? '' }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif(!empty($trackingInfo['error']))
                                <div class="text-xs text-amber-950 p-4 bg-amber-50/80 rounded-2xl border border-amber-200/70 leading-relaxed space-y-2">
                                    <div class="flex items-start gap-2.5">
                                        <span class="text-sm">🚚</span>
                                        <div>
                                            <p class="font-bold text-amber-950 text-xs">Paket Dalam Pengiriman Kemitraan J&T Express</p>
                                            <p class="text-[11px] text-amber-800/90 mt-0.5">{{ $trackingInfo['error'] }}</p>
                                        </div>
                                    </div>
                                    <div class="pt-1 pl-6">
                                        <a href="https://www.jet.co.id/track" target="_blank" rel="noopener noreferrer"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-100 hover:bg-amber-200 text-amber-950 rounded-full font-bold text-[10px] uppercase tracking-wider transition">
                                            <span>🌐 Buka Portal Pelacakan Resmi J&T Express</span>
                                            <span>&rarr;</span>
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="text-xs text-mute p-4 bg-soft-cloud rounded-2xl border border-hairline-soft leading-relaxed flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <span>Paket jersey telah diserahkan ke gerai J&T Express. Riwayat pemindaian barcode resi diperbarui secara berkala oleh kurir.</span>
                                    <a href="https://www.jet.co.id/track" target="_blank" rel="noopener noreferrer"
                                       class="inline-flex items-center gap-1 text-[11px] font-bold text-ink hover:underline shrink-0">
                                        <span>Lacak di J&T</span> &rarr;
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- 2. RIGHT COLUMN: Shipping Address, Pricing & Support (4 Cols) --}}
            <div class="lg:col-span-4 space-y-6">
                
                {{-- Alamat Pengiriman Snapshot --}}
                <div class="bg-white rounded-3xl border border-hairline-soft p-6 sm:p-7 space-y-4 shadow-xs text-xs">
                    <div class="flex items-center justify-between border-b border-hairline-soft pb-3">
                        <h2 class="font-display text-lg sm:text-xl text-ink uppercase tracking-wide">
                            Alamat Pengiriman
                        </h2>
                        @if(!empty($addr['label']))
                            <span class="text-[10px] font-bold uppercase tracking-wider bg-soft-cloud px-2.5 py-0.5 rounded-full border border-hairline-soft text-ink">
                                {{ $addr['label'] }}
                            </span>
                        @endif
                    </div>

                    <div class="space-y-1">
                        <div class="font-bold text-sm text-ink">{{ $addr['recipient_name'] ?? $order->customer_name }}</div>
                        <div class="text-mute font-mono">{{ $addr['phone_number'] ?? $order->customer_phone }}</div>
                    </div>

                    <div class="space-y-1 leading-relaxed text-ink">
                        <p class="font-medium">{{ $addr['full_address'] ?? '-' }}</p>
                        <p class="text-mute text-[11px]">
                            {{ $addr['district_name'] ?? '' }}{{ !empty($addr['district_name']) ? ', ' : '' }}{{ $addr['city_name'] ?? '' }}{{ !empty($addr['province_name']) ? ', ' . $addr['province_name'] : '' }} - {{ $addr['postal_code'] ?? '' }}
                        </p>
                    </div>

                    @if(!empty($addr['benchmark_notes']))
                        <div class="p-3 bg-soft-cloud rounded-2xl text-[11px] text-mute border border-hairline-soft">
                            <strong class="text-ink font-semibold">Patokan:</strong> {{ $addr['benchmark_notes'] }}
                        </div>
                    @endif

                    @if($gmapsUrl)
                        <div class="pt-2">
                            <a href="{{ $gmapsUrl }}" target="_blank" rel="noopener noreferrer" 
                               class="inline-flex items-center justify-center gap-2 w-full px-5 py-2.5 bg-soft-cloud hover:bg-neutral-200 border border-hairline-soft text-ink text-xs font-bold uppercase tracking-[0.12em] rounded-full shadow-2xs hover:shadow-xs transition active:scale-95 cursor-pointer">
                                <svg class="w-3.5 h-3.5 text-sale shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                                </svg>
                                <span>Lihat di Maps</span>
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Rincian Pembayaran --}}
                <div class="bg-soft-cloud rounded-3xl border border-hairline-soft p-6 sm:p-7 space-y-4 shadow-xs text-xs">
                    <h2 class="font-display text-lg sm:text-xl text-ink uppercase tracking-wide border-b border-hairline-soft pb-3">
                        Rincian Pembayaran
                    </h2>

                    <div class="space-y-2.5 text-mute">
                        <div class="flex justify-between items-center">
                            <span>Subtotal Jersey</span>
                            <span class="font-bold text-ink tabular-nums">Rp {{ number_format($order->subtotal_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>Ongkos Kirim J&T Express</span>
                            <span class="font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200 text-[11px]">Gratis Ongkir (Rp 0)</span>
                        </div>
                        @if($order->shipping_cost > 0)
                            <div class="flex justify-between items-center">
                                <span>Biaya Pengiriman Tambahan</span>
                                <span class="font-bold text-ink tabular-nums">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="border-t border-hairline-soft pt-4 flex justify-between items-baseline">
                        <div>
                            <span class="font-bold text-xs uppercase tracking-wider text-ink block">Total Tagihan</span>
                            <span class="text-[10px] text-mute">Sudah termasuk PPN & kemasan box</span>
                        </div>
                        <span class="font-bold text-xl sm:text-2xl text-ink tabular-nums">{{ $order->formatted_grand_total }}</span>
                    </div>
                </div>

                {{-- Support & Guarantee Card --}}
                <div class="bg-white rounded-3xl border border-hairline-soft p-6 space-y-3 text-xs shadow-xs">
                    <h3 class="font-bold text-xs uppercase tracking-wider text-ink flex items-center gap-1.5">
                        <span>🛡️</span>
                        <span>Jaminan Kualitas Ngizan</span>
                    </h3>
                    <p class="text-mute text-[11px] leading-relaxed">
                        Setiap jersey melalui quality control ketat sebelum pengiriman. Butuh bantuan pesanan? Tim CS kami siap melayani Anda.
                    </p>
                    <div class="pt-1">
                        <a href="https://wa.me/6281234567890?text=Halo%20Admin%20Ngizan%20Apparel,%20saya%20ingin%20menanyakan%20pesanan%20nomor%20{{ $order->order_number }}" target="_blank" rel="noopener noreferrer" class="text-[11px] font-bold text-ink hover:underline inline-flex items-center gap-1">
                            <span>Hubungi Bantuan CS via WhatsApp</span>
                            <span>&rarr;</span>
                        </a>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

@push('scripts')
    <!-- Midtrans Snap JS -->
    <script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" 
            data-client-key="{{ config('services.midtrans.client_key') }}"></script>
@endpush

@endsection
