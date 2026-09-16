@extends('layouts.customer')

@section('title', 'Pesanan #' . $order->order_number . ' · NGIZAN APPAREL')
@section('meta_description', 'Detail pesanan, status pembayaran, dan pelacakan pengiriman jersey di Ngizan Apparel.')

@section('content')

@php
    $isPending = $order->status === \App\Enums\OrderStatus::PENDING_PAYMENT;
    $snapToken = $order->payment?->snap_token;
    $expiresAtTimestamp = $order->expires_at ? $order->expires_at->timestamp * 1000 : null;
@endphp

<div class="py-10" x-data="{
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
                    onSuccess: () => window.location.reload(),
                    onPending: () => window.location.reload(),
                    onError: () => window.location.reload(),
                    onClose: () => window.location.reload()
                });
            } else {
                toastr.error('Midtrans Snap tidak tersedia.');
            }
        @else
            toastr.error('Token pembayaran tidak ditemukan.');
        @endif
    }
}">
    <div class="wrap">
        
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-xs text-ink-muted mb-6 uppercase tracking-wider">
            <a href="{{ route('home') }}" class="hover:text-ink">Beranda</a>
            <span>/</span>
            <a href="{{ route('customer.orders.index') }}" class="hover:text-ink">Pesanan Saya</a>
            <span>/</span>
            <span class="text-ink font-bold">#{{ $order->order_number }}</span>
        </nav>

        {{-- Status Hero Alert --}}
        <div class="p-6 rounded-lg border mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 {{ $order->status->isCompleted() ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : ($order->status->isCancelled() ? 'bg-rose-50 border-rose-200 text-rose-900' : 'bg-white border-black/10 text-ink') }}">
            <div class="space-y-1">
                <div class="flex items-center gap-3">
                    <span class="font-display font-black text-xl uppercase tracking-tight">
                        Pesanan #{{ $order->order_number }}
                    </span>
                    <span class="text-xs uppercase font-bold px-3 py-1 rounded {{ $order->status->badgeClass() }}">
                        {{ $order->status->label() }}
                    </span>
                </div>
                <p class="text-xs text-ink-muted">
                    Dibuat pada {{ $order->created_at->format('d F Y, H:i') }} WIB · Metode Pembayaran: <strong>{{ strtoupper($order->payment?->payment_type ?? 'Midtrans Snap') }}</strong>
                </p>
            </div>

            {{-- Pending Countdown & Action --}}
            @if($isPending)
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <div class="text-left sm:text-right" x-show="timeLeft">
                        <span class="text-[10px] text-ink-muted uppercase font-bold tracking-wider block">Sisa Waktu Bayar</span>
                        <span class="font-display font-black text-lg text-rose-600 tabular-nums" x-text="timeLeft"></span>
                    </div>

                    <button type="button" @click="payNow()" class="btn-curtain py-3.5 px-6 text-xs">
                        <span>⚡ Bayar Sekarang (Midtrans) &rarr;</span>
                    </button>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- 1. RINCIAN ITEM PESANAN (8 COLS) --}}
            <div class="lg:col-span-8 space-y-6">
                <div class="bg-white p-6 rounded border border-black/10 space-y-4 shadow-sm">
                    <h2 class="font-display font-bold text-sm text-ink uppercase tracking-wider border-b border-black/10 pb-3">
                        Daftar Jersey Pesanan ({{ $order->items->count() }} Item)
                    </h2>

                    <div class="divide-y divide-black/10">
                        @foreach($order->items as $item)
                            <div class="py-4 first:pt-0 last:pb-0 flex flex-col gap-3">
                                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-16 h-20 bg-canvas rounded overflow-hidden flex-shrink-0 border border-black/5">
                                            @php
                                                $img = $item->product?->thumbnail_front ? asset('storage/' . $item->product->thumbnail_front) : 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=200&q=80';
                                            @endphp
                                            <img src="{{ $img }}" alt="{{ $item->product_name }}" class="w-full h-full object-cover">
                                        </div>
                                        <div class="space-y-1">
                                            <h3 class="font-bold text-sm text-ink uppercase tracking-tight">{{ $item->product_name }}</h3>
                                            <p class="text-xs text-ink-muted">
                                                Ukuran: <strong>{{ $item->size }}</strong> ({{ $item->type }}) &times; {{ $item->quantity }} pcs
                                            </p>
                                            <div class="flex flex-wrap gap-1.5 pt-0.5">
                                                @if($item->custom_name || $item->custom_number)
                                                    <span class="text-[10px] font-bold bg-neutral-900 text-white px-2 py-0.5 rounded font-jersey tracking-widest uppercase">
                                                        ⚡ SABLON: {{ $item->custom_name ?? '-' }} #{{ $item->custom_number ?? '0' }}
                                                    </span>
                                                @endif
                                                @if($item->selected_patch)
                                                    <span class="text-[10px] font-bold bg-cyan-100 text-cyan-800 px-2 py-0.5 rounded uppercase">
                                                        ★ {{ $item->selected_patch }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="text-right w-full sm:w-auto">
                                        <div class="font-display font-bold text-sm text-ink tabular-nums">
                                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                        </div>
                                        <span class="text-[10px] text-ink-muted">
                                            (Rp {{ number_format($item->unit_price + $item->custom_fee, 0, ',', '.') }} / pcs)
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
                                        <div class="text-xs bg-amber-50/70 border border-amber-200/80 rounded p-3 text-amber-900 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                            <div class="space-y-1">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="font-bold text-[11px]">Ulasan Anda:</span>
                                                    <div class="flex text-amber-500 text-sm">
                                                        @for($s = 1; $s <= 5; $s++)
                                                            <span>{{ $s <= $userReview->rating ? '★' : '☆' }}</span>
                                                        @endfor
                                                    </div>
                                                    <span class="text-[10px] font-bold text-amber-800">({{ $userReview->rating }}/5)</span>
                                                </div>
                                                <p class="text-ink-muted text-xs italic">"{{ $userReview->comment }}"</p>
                                            </div>
                                            <span class="text-[10px] bg-emerald-100 text-emerald-800 font-bold px-2 py-0.5 rounded self-start sm:self-center whitespace-nowrap">
                                                ✓ Verified Buyer
                                            </span>
                                        </div>
                                    @else
                                        <div class="flex items-center justify-between pt-1" x-data="{ openReviewModal: false }">
                                            <button type="button" @click="openReviewModal = true" class="inline-flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded transition shadow-sm">
                                                <span>⭐ Tulis Ulasan & Beri Rating</span>
                                            </button>

                                            {{-- Modal Ulasan --}}
                                            <div x-show="openReviewModal" @click.away="openReviewModal = false" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-xs">
                                                <div class="bg-white rounded-lg max-w-md w-full p-6 space-y-4 shadow-2xl border border-black/10 text-left">
                                                    <div class="flex justify-between items-center border-b border-black/10 pb-3">
                                                        <h3 class="font-display font-bold text-sm text-ink uppercase tracking-wide">Ulas {{ $item->product_name }}</h3>
                                                        <button type="button" @click="openReviewModal = false" class="text-ink-muted hover:text-ink text-xl font-bold">&times;</button>
                                                    </div>
                                                    <p class="text-xs text-ink-muted">Bagikan kepuasan Anda terhadap kualitas jersey dan sablon nama NGIZAN Apparel.</p>
                                                    <form action="{{ route('customer.reviews.store') }}" method="POST" class="space-y-4" x-data="{ currentRating: 5 }">
                                                        @csrf
                                                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                        <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                                        <input type="hidden" name="rating" :value="currentRating">

                                                        <div>
                                                            <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-1">Kepuasan Produk</label>
                                                            <div class="flex items-center gap-1">
                                                                <template x-for="star in [1, 2, 3, 4, 5]">
                                                                    <button type="button" @click="currentRating = star" class="text-2xl transition transform hover:scale-110" :class="star <= currentRating ? 'text-amber-400' : 'text-neutral-300'">
                                                                        ★
                                                                    </button>
                                                                </template>
                                                                <span class="text-xs font-bold text-ink-muted ml-2" x-text="currentRating + ' / 5 Bintang'"></span>
                                                            </div>
                                                        </div>

                                                        <div>
                                                            <label class="block text-xs font-bold text-ink uppercase tracking-wider mb-1">Komentar Ulasan</label>
                                                            <textarea name="comment" rows="3" required minlength="5" placeholder="Bahan adem, sablon nameset presisi, pengiriman J&T super cepat!" class="w-full text-xs p-3 border border-black/20 rounded focus:border-black focus:outline-none"></textarea>
                                                        </div>

                                                        <div class="flex justify-end gap-2 pt-2 border-t border-black/10">
                                                            <button type="button" @click="openReviewModal = false" class="px-4 py-2 border border-black/20 rounded text-xs font-bold hover:bg-neutral-100 transition">Batal</button>
                                                            <button type="submit" class="px-4 py-2 bg-black text-white rounded text-xs font-bold hover:bg-neutral-800 transition">Kirim Ulasan</button>
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

                {{-- Live Tracking Timeline (J&T Express via Biteship) --}}
                @if($order->tracking_number)
                    <div class="bg-white p-6 rounded border border-black/10 space-y-4 shadow-sm" x-data>
                        <h2 class="font-display font-bold text-sm text-ink uppercase tracking-wider border-b border-black/10 pb-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <span class="flex items-center gap-2">
                                <span>🚚 Pelacakan Ekspedisi J&T Express</span>
                                <span class="text-[10px] bg-red-100 text-red-700 font-bold px-2 py-0.5 rounded uppercase">Gratis Ongkir</span>
                            </span>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-mono font-bold text-cyan-700">{{ $order->tracking_number }}</span>
                                <button type="button" @click="navigator.clipboard.writeText('{{ $order->tracking_number }}'); toastr.success('Nomor resi J&T berhasil disalin!');" class="px-2 py-0.5 text-[10px] bg-neutral-100 hover:bg-neutral-200 text-ink rounded font-bold transition">
                                    Salin
                                </button>
                            </div>
                        </h2>

                        <div class="space-y-4 text-xs">
                            <div class="flex flex-wrap justify-between gap-2 text-ink-muted bg-canvas p-3 rounded">
                                <span>Kurir: <strong class="text-ink uppercase">{{ $order->courier_service_name ?? 'J&T Express (Gratis Ongkir)' }}</strong></span>
                                <span>Status Terkini: <strong class="text-emerald-600 uppercase">{{ $trackingInfo['status'] ?? ($order->status->isCompleted() ? 'DELIVERED' : 'ON_DELIVERY') }}</strong></span>
                            </div>

                            @if(!empty($trackingInfo['history']))
                                <div class="border-l-2 border-cyan-500 pl-4 space-y-4 ml-2">
                                    @foreach($trackingInfo['history'] as $history)
                                        <div class="relative">
                                            <div class="absolute -left-[21px] top-1 w-2.5 h-2.5 rounded-full bg-cyan-500 ring-4 ring-white"></div>
                                            <p class="font-bold text-ink">{{ $history['note'] ?? $history['message'] }}</p>
                                            <p class="text-[10px] text-ink-muted">{{ $history['updated_at'] ?? '' }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-xs text-ink-muted p-3 bg-neutral-50 rounded border border-neutral-200">
                                    Paket telah diserahkan ke J&T Express. Riwayat perjalanan paket diperbarui secara berkala.
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- 2. ALAMAT PENGIRIMAN & SUMMARY (4 COLS) --}}
            <div class="lg:col-span-4 space-y-6">
                
                {{-- Alamat Snapshot --}}
                <div class="bg-white p-6 rounded border border-black/10 space-y-3 shadow-sm text-xs">
                    <h2 class="font-display font-bold text-sm text-ink uppercase tracking-wider border-b border-black/10 pb-3">
                        📍 Alamat Pengiriman
                    </h2>
                    @php
                        $addr = $order->shipping_address_snapshot ?? [];
                    @endphp
                    <div>
                        <strong class="text-ink">{{ $addr['recipient_name'] ?? $order->customer_name }}</strong>
                        <p class="text-ink-muted">{{ $addr['phone_number'] ?? $order->customer_phone }}</p>
                    </div>
                    <p class="text-ink leading-relaxed">
                        {{ $addr['full_address'] ?? '-' }}
                    </p>
                    <p class="text-[11px] text-ink-muted">
                        {{ $addr['district_name'] ?? '' }}, {{ $addr['city_name'] ?? '' }} - {{ $addr['postal_code'] ?? '' }}
                    </p>
                    @if(!empty($addr['benchmark_notes']))
                        <div class="p-2 bg-canvas rounded text-[11px] text-ink-muted">
                            <strong>Patokan:</strong> {{ $addr['benchmark_notes'] }}
                        </div>
                    @endif
                </div>

                {{-- Rincian Biaya --}}
                <div class="bg-white p-6 rounded border border-black/10 space-y-3 shadow-sm text-xs">
                    <h2 class="font-display font-bold text-sm text-ink uppercase tracking-wider border-b border-black/10 pb-3">
                        Rincian Pembayaran
                    </h2>
                    <div class="flex justify-between text-ink-muted">
                        <span>Subtotal Jersey</span>
                        <span class="font-bold text-ink">{{ $order->formatted_subtotal_amount }}</span>
                    </div>
                    <div class="flex justify-between text-ink-muted">
                        <span>Ongkos Kirim ({{ $order->courier_code }})</span>
                        <span class="font-bold text-ink">{{ $order->formatted_shipping_cost }}</span>
                    </div>
                    <div class="border-t border-black/10 pt-3 flex justify-between items-center text-sm">
                        <span class="font-bold uppercase text-ink">Total Akhir</span>
                        <span class="font-display font-black text-xl text-cyan-600">{{ $order->formatted_grand_total }}</span>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

@push('scripts')
    <!-- Midtrans Snap JS (Sandbox) -->
    <script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" 
            data-client-key="{{ config('services.midtrans.client_key') }}"></script>
@endpush

@endsection
