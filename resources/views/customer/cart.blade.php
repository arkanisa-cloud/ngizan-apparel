@extends('layouts.customer')

@section('title', 'Tas Belanja · NGIZAN APPAREL')
@section('meta_description',
    'Periksa item jersey pesanan Anda, pilih produk yang ingin di-checkout, dan nikmati diskon
    member.')

    @php
        $cartItemsData = [];
        if ($cart && $cart->items->isNotEmpty()) {
            foreach ($cart->items as $item) {
                $rawPrice = $item->variant ? (float) $item->variant->final_price : (float) $item->unit_price;
                $cartItemsData[] = [
                    'id' => $item->id,
                    'productId' => $item->product_id,
                    'name' => $item->product->name,
                    'slug' => $item->product->slug,
                    'image' => $item->product->thumbnail_front
                        ? asset('storage/' . $item->product->thumbnail_front)
                        : 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=400&q=80',
                    'type' => $item->variant->type ?? 'Authentic',
                    'size' => $item->variant->size ?? 'M',
                    'stock' => (int) ($item->variant->stock ?? 10),
                    'rawPrice' => $rawPrice,
                    'unitPrice' => (float) $item->unit_price,
                    'customFee' => (float) $item->custom_fee,
                    'quantity' => (int) $item->quantity,
                    'weightGrams' => (int) ($item->product->weight_grams ?: 250),
                    'selected' => true,
                    'updateUrl' => route('customer.cart.update', $item->id),
                    'deleteUrl' => route('customer.cart.destroy', $item->id),
                ];
            }
        }
        $isPremiumUser = auth()->check() && auth()->user()->isPremiumActive();
    @endphp

@section('content')
    <div class="py-8 sm:py-12 bg-canvas" x-data="cartPageManager()" x-cloak>
        <div class="wrap">
            {{-- Page Header --}}
            <div
                class="flex flex-col sm:flex-row sm:items-end justify-between border-b border-hairline-soft pb-5 mb-6 sm:mb-8 gap-4">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-mute block mb-1">
                        Detail Pesanan Anda
                    </span>
                    <h1
                        class="text-2xl sm:text-3xl font-extrabold tracking-tight text-ink uppercase flex items-center gap-3">
                        <span>Tas Belanja</span>
                    </h1>
                </div>

                @if ($cart && $cart->items->isNotEmpty())
                    <div class="flex items-center gap-3 self-start sm:self-auto">
                        <form action="{{ route('customer.cart.clear') }}" method="POST"
                            onsubmit="return confirm('Apakah Anda yakin ingin mengosongkan semua produk di tas belanja?');">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-soft-cloud hover:bg-rose-50 text-mute hover:text-sale border border-hairline-soft hover:border-sale/30 text-xs font-semibold rounded-full transition cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <span>Kosongkan Tas</span>
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            {{-- Empty State --}}
            <template x-if="items.length === 0">
                <div
                    class="text-center py-16 sm:py-20 bg-white border border-hairline-soft rounded-3xl max-w-lg mx-auto my-8 p-6 sm:p-12 space-y-4 shadow-xs">
                    <div class="w-16 h-16 bg-soft-cloud rounded-full flex items-center justify-center mx-auto text-ink">
                        <svg class="w-8 h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold uppercase tracking-tight text-ink">Tas Belanja Anda Masih Kosong</h2>
                    <p class="text-xs text-mute leading-relaxed max-w-xs mx-auto">
                        Jelajahi koleksi jersey klub, tim nasional, dan rilisan arsip retro autentik kami.
                    </p>
                    <div class="pt-2">
                        <a href="{{ route('shop.index') }}"
                            class="btn-primary py-3.5 px-8 text-xs font-bold uppercase tracking-[0.15em] inline-flex rounded-full shadow-md hover:bg-neutral-800 transition">
                            <span>Mulai Belanja &rarr;</span>
                        </a>
                    </div>
                </div>
            </template>

            {{-- Cart Content (2 Columns Layout) --}}
            <template x-if="items.length > 0">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">

                    {{-- 1. Left Column: Item List & Toolbar (7 Cols) --}}
                    <div class="lg:col-span-7 space-y-4 min-w-0">

                        {{-- Checkbox Toolbar (Select All & Reset) --}}
                        <div
                            class="bg-white border border-hairline-soft rounded-2xl p-3.5 sm:p-4 flex flex-wrap items-center justify-between gap-3 shadow-xs">
                            <div class="flex items-center gap-3">
                                <label class="flex items-center gap-2.5 cursor-pointer select-none">
                                    <input type="checkbox" :checked="allSelected" @change="toggleSelectAll()"
                                        class="w-4 h-4 rounded border-neutral-300 text-ink focus:ring-ink transition cursor-pointer">
                                    <span class="text-xs font-bold uppercase tracking-wider text-ink">
                                        Pilih Semua (<span x-text="items.length"></span>)
                                    </span>
                                </label>
                            </div>

                            <div class="flex items-center gap-2">
                                <button type="button" @click="resetSelection()"
                                    class="px-3 py-1.5 bg-soft-cloud hover:bg-neutral-200 text-ink text-[11px] font-bold rounded-full transition cursor-pointer">
                                    ✕ Reset Pilihan
                                </button>
                                <button type="button" @click="selectAll()"
                                    class="px-3 py-1.5 bg-soft-cloud hover:bg-neutral-200 text-ink text-[11px] font-bold rounded-full transition cursor-pointer">
                                    ✓ Pilih Semua
                                </button>
                            </div>
                        </div>

                        {{-- Item Rows --}}
                        <div class="space-y-3.5">
                            <template x-for="(item, index) in items" :key="item.id">
                                <div class="bg-white p-4 sm:p-5 border border-hairline-soft rounded-3xl flex flex-col sm:flex-row gap-4 sm:gap-6 items-start sm:items-center justify-between shadow-xs transition hover:border-hairline"
                                    :class="{ 'opacity-60 bg-neutral-50/50': !item.selected }">

                                    {{-- Checkbox, Image & Product Info --}}
                                    <div class="flex items-start gap-3 sm:gap-4 w-full sm:flex-1 min-w-0">
                                        {{-- Product Checkbox --}}
                                        <div class="pt-2 sm:pt-3 shrink-0">
                                            <input type="checkbox" x-model="item.selected"
                                                :aria-label="'Pilih ' + item.name"
                                                class="w-5 h-5 rounded border-neutral-300 text-ink focus:ring-ink transition cursor-pointer">
                                        </div>

                                        {{-- Thumbnail (Fixed size, rounded, subtle border, smooth hover scale) --}}
                                        <a :href="'/product/' + item.slug"
                                            class="w-20 h-24 sm:w-24 sm:h-28 shrink-0 rounded-2xl overflow-hidden bg-soft-cloud border border-hairline-soft block group relative">
                                            <img :src="item.image" :alt="item.name"
                                                class="w-full h-full object-cover object-center group-hover:scale-105 transition duration-300 block"
                                                loading="lazy">
                                        </a>

                                        {{-- Details --}}
                                        <div class="space-y-1.5 flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span
                                                    class="text-[10px] uppercase font-bold tracking-wider px-2.5 py-0.5 bg-soft-cloud border border-hairline-soft rounded-full text-mute"
                                                    x-text="item.type">
                                                </span>
                                                <span class="text-[10px] text-mute font-medium">
                                                    Sisa stok: <strong class="text-ink font-bold"
                                                        x-text="item.stock"></strong>
                                                </span>
                                            </div>

                                            <h3 class="font-bold text-xs sm:text-sm text-ink leading-snug line-clamp-2">
                                                <a :href="'/product/' + item.slug" class="hover:text-neutral-600 transition"
                                                    x-text="item.name"></a>
                                            </h3>

                                            <div class="flex items-center gap-2 text-xs text-mute flex-wrap">
                                                <span class="inline-flex items-center gap-1 font-semibold text-ink">
                                                    Ukuran: <span
                                                        class="px-2 py-0.5 bg-soft-cloud border border-hairline-soft rounded-md text-[11px]"
                                                        x-text="item.size"></span>
                                                </span>
                                                <span class="text-neutral-300">·</span>
                                                <span class="font-bold tabular-nums text-ink"
                                                    x-text="formatRupiah(item.unitPrice)"></span>
                                                <template x-if="isPremium && item.rawPrice > item.unitPrice">
                                                    <span class="text-[11px] text-mute line-through tabular-nums"
                                                        x-text="formatRupiah(item.rawPrice)"></span>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Quantity Counter, Subtotal & Delete Action --}}
                                    <div
                                        class="flex items-center justify-between w-full sm:w-auto sm:flex-col sm:items-end gap-3 pt-3 sm:pt-0 border-t sm:border-t-0 border-hairline-soft shrink-0">
                                        {{-- Item Subtotal --}}
                                        <div
                                            class="font-extrabold text-sm sm:text-base text-ink tabular-nums tracking-tight">
                                            <span
                                                x-text="formatRupiah((item.unitPrice + item.customFee) * item.quantity)"></span>
                                        </div>

                                        <div class="flex items-center gap-2.5">
                                            {{-- Quantity Pill Controls --}}
                                            <div
                                                class="flex items-center bg-soft-cloud border border-hairline rounded-full p-0.5">
                                                <button type="button" @click="updateQty(item, -1)"
                                                    :disabled="item.quantity <= 1"
                                                    class="w-7 h-7 rounded-full bg-white hover:bg-neutral-200 text-ink flex items-center justify-center font-bold text-xs disabled:opacity-40 disabled:hover:bg-white transition cursor-pointer shadow-2xs"
                                                    aria-label="Kurangi kuantitas">
                                                    &minus;
                                                </button>

                                                <input type="number" :value="item.quantity" @change="setQty(item, $event)"
                                                    min="1" :max="item.stock"
                                                    class="w-9 sm:w-10 text-center bg-transparent border-none text-xs font-bold text-ink p-0 focus:ring-0 focus:outline-none tabular-nums"
                                                    aria-label="Jumlah pesanan">

                                                <button type="button" @click="updateQty(item, 1)"
                                                    :disabled="item.quantity >= item.stock"
                                                    class="w-7 h-7 rounded-full bg-white hover:bg-neutral-200 text-ink flex items-center justify-center font-bold text-xs disabled:opacity-40 disabled:hover:bg-white transition cursor-pointer shadow-2xs"
                                                    aria-label="Tambah kuantitas">
                                                    &#43;
                                                </button>
                                            </div>

                                            {{-- Delete Button --}}
                                            <form :action="item.deleteUrl" method="POST"
                                                onsubmit="return confirm('Hapus jersey ini dari tas belanja?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="w-8 h-8 rounded-full bg-soft-cloud hover:bg-rose-50 text-mute hover:text-sale flex items-center justify-center border border-hairline-soft transition cursor-pointer"
                                                    title="Hapus Item">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- 2. Right Column: Realtime Specific Order Summary Card (5 Cols) --}}
                    <div
                        class="lg:col-span-5 bg-white p-6 sm:p-7 rounded-3xl border border-hairline-soft space-y-6 lg:sticky lg:top-24 shadow-xs">

                        {{-- Header --}}
                        <div class="border-b border-hairline-soft pb-4 flex items-center justify-between">
                            <h2 class="font-extrabold text-sm uppercase tracking-wider text-ink">
                                Ringkasan Pesanan
                            </h2>
                            <span class="text-xs font-bold text-mute tabular-nums"
                                x-text="selectedCount + ' Item'"></span>
                        </div>

                        {{-- Specific Selected Products List with Visual Thumbnails (Like Checkout) --}}
                        <div
                            class="space-y-3.5 max-h-60 overflow-y-auto pr-1 border-b border-hairline-soft pb-4 divide-y divide-hairline-soft">
                            {{-- Empty Selection Alert --}}
                            <template x-if="selectedItems.length === 0">
                                <div
                                    class="py-5 text-center text-xs text-mute space-y-1 bg-soft-cloud/60 rounded-2xl p-4 border border-hairline-soft">
                                    <p class="font-bold text-ink">Belum Ada Jersey Dipilih</p>
                                    <p class="text-[11px] text-neutral-400">Centang kotak produk di sebelah kiri untuk
                                        melihat ringkasan item & melanjutkan checkout.</p>
                                </div>
                            </template>

                            {{-- Selected Items List --}}
                            <template x-for="item in selectedItems" :key="'summary-' + item.id">
                                <div class="flex items-center gap-3.5 pt-3.5 first:pt-0">
                                    {{-- Thumbnail --}}
                                    <div
                                        class="w-14 h-18 sm:w-16 sm:h-20 rounded-2xl overflow-hidden bg-soft-cloud border border-hairline-soft shrink-0">
                                        <img :src="item.image" :alt="item.name"
                                            class="w-full h-full object-cover object-center">
                                    </div>

                                    {{-- Product Meta --}}
                                    <div class="flex-1 min-w-0 space-y-1">
                                        <h4 class="font-bold text-xs sm:text-sm text-ink line-clamp-1 leading-snug"
                                            x-text="item.name"></h4>
                                        <div class="flex items-center gap-2 text-[11px] text-mute flex-wrap">
                                            <span
                                                class="px-2 py-0.5 bg-soft-cloud border border-hairline-soft rounded-md font-bold text-ink text-[10px]"
                                                x-text="item.size"></span>
                                            <span class="text-neutral-300">·</span>
                                            <span x-text="item.type"></span>
                                            <span class="text-neutral-300">·</span>
                                            <span class="font-semibold text-ink" x-text="item.quantity + ' pcs'"></span>
                                        </div>
                                    </div>

                                    {{-- Price --}}
                                    <div class="font-extrabold text-xs sm:text-sm text-ink tabular-nums shrink-0"
                                        x-text="formatRupiah((item.unitPrice + item.customFee) * item.quantity)">
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Price Breakdown --}}
                        <div class="space-y-3 text-xs">
                            <div class="flex justify-between items-center text-mute">
                                <span>Total Item Dipilih</span>
                                <span class="font-bold text-ink tabular-nums" x-text="selectedCount + ' pcs'"></span>
                            </div>

                            <div class="flex justify-between items-center text-mute">
                                <span>Estimasi Berat</span>
                                <span class="font-bold text-ink tabular-nums"
                                    x-text="selectedWeightGrams + ' gram'"></span>
                            </div>

                            <div class="flex justify-between items-center text-mute">
                                <span>Subtotal Jersey</span>
                                <span class="font-bold text-ink tabular-nums" x-text="formatRupiah(subtotal)"></span>
                            </div>

                            {{-- Member Premium Discount Row --}}
                            <template x-if="isPremium">
                                <div
                                    class="flex justify-between items-center p-2.5 bg-amber-50 border border-amber-200/60 rounded-xl text-amber-950">
                                    <span class="font-bold flex items-center gap-1 text-[11px]">
                                        <span>⭐</span> Diskon Member VIP (5%)
                                    </span>
                                    <span class="font-bold text-sale text-xs tabular-nums"
                                        x-text="'- ' + formatRupiah(discountAmount)"></span>
                                </div>
                            </template>

                            <div class="flex justify-between items-center text-mute">
                                <span>Ongkos Kirim</span>
                                <span
                                    class="text-[11px] text-emerald-700 font-semibold bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/50">
                                    Dihitung di Checkout
                                </span>
                            </div>
                        </div>

                        {{-- Grand Total --}}
                        <div class="border-t border-hairline-soft pt-4 flex justify-between items-baseline">
                            <div>
                                <span class="text-[10px] uppercase font-bold tracking-widest text-mute block">Total
                                    Pembayaran</span>
                                <span class="text-[11px] text-mute font-normal">Belum termasuk ongkir</span>
                            </div>
                            <div class="text-2xl font-extrabold text-ink tabular-nums tracking-tight"
                                x-text="formatRupiah(grandTotal)">
                            </div>
                        </div>

                        {{-- Checkout CTA Form --}}
                        <div>
                            <form action="{{ route('customer.checkout.index') }}" method="GET"
                                @submit="if (selectedItems.length === 0) { $event.preventDefault(); return false; }">
                                <input type="hidden" name="selected_items" :value="selectedIdsString">

                                <button type="submit" :disabled="selectedItems.length === 0"
                                    :class="selectedItems.length === 0 ?
                                        'bg-neutral-200 text-neutral-400 border border-neutral-200 cursor-not-allowed shadow-none pointer-events-none' :
                                        'btn-primary shadow-lg hover:bg-neutral-800 cursor-pointer'"
                                    class="w-full py-4 text-center rounded-full text-xs font-bold tracking-[0.15em] flex items-center justify-center gap-2 transition duration-200">
                                    <span x-show="selectedItems.length > 0" class="flex items-center gap-2">
                                        <span>Lanjut ke Pengiriman</span>
                                        <span>&rarr;</span>
                                    </span>
                                    <span x-show="selectedItems.length === 0">
                                        Pilih Produk Terlebih Dahulu
                                    </span>
                                </button>
                            </form>
                        </div>

                        {{-- Trust Indicators & Anti-Overselling Guard --}}
                        <div class="space-y-3 pt-2 border-t border-hairline-soft text-xs text-mute">
                            <div class="flex items-start gap-2.5">
                                <span class="text-sm">⏱️</span>
                                <div>
                                    <p class="font-bold text-ink text-[11px]">Batas Waktu Pembayaran 2 Jam</p>
                                    <p class="text-[10px] leading-relaxed text-neutral-400">Stok jersey otomatis diamankan
                                        (*Anti-Overselling Guard*). Jika lewat 2 jam, pesanan kedaluwarsa dan stok
                                        dikembalikan ke etalase.</p>
                                </div>
                            </div>

                            <div
                                class="flex items-center justify-center gap-1.5 text-neutral-500 font-medium text-[11px] pt-1">
                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <span>Transaksi Terenkripsi Midtrans Snap</span>
                            </div>
                            <p class="text-[10px] text-neutral-400 text-center">Pengiriman terintegrasi kurir resmi J&T
                                Express & Biteship ke seluruh pelosok Nusantara.</p>
                        </div>
                    </div>

                </div>
            </template>
        </div>
    </div>

    <script>
        function cartPageManager() {
            return {
                isPremium: @json($isPremiumUser),
                items: @json($cartItemsData),
                debounceTimer: null,
                formatRupiah: function(num) {
                    return 'Rp ' + Math.max(0, Math.round(num)).toLocaleString('id-ID');
                },
                get selectedItems() {
                    return this.items.filter(function(i) {
                        return i.selected;
                    });
                },
                get selectedCount() {
                    return this.selectedItems.reduce(function(acc, i) {
                        return acc + i.quantity;
                    }, 0);
                },
                get selectedWeightGrams() {
                    return this.selectedItems.reduce(function(acc, i) {
                        return acc + (i.weightGrams * i.quantity);
                    }, 0);
                },
                get subtotal() {
                    return this.selectedItems.reduce(function(acc, i) {
                        var p = i.rawPrice ? i.rawPrice : i.unitPrice;
                        return acc + ((p + i.customFee) * i.quantity);
                    }, 0);
                },
                get discountAmount() {
                    if (!this.isPremium) return 0;
                    var actualPaid = this.selectedItems.reduce(function(acc, i) {
                        return acc + ((i.unitPrice + i.customFee) * i.quantity);
                    }, 0);
                    return Math.max(0, this.subtotal - actualPaid);
                },
                get grandTotal() {
                    return Math.max(0, this.subtotal - this.discountAmount);
                },
                get allSelected() {
                    return this.items.length > 0 && this.items.every(function(i) {
                        return i.selected;
                    });
                },
                get selectedIdsString() {
                    return this.selectedItems.map(function(i) {
                        return i.id;
                    }).join(',');
                },
                toggleSelectAll: function() {
                    var nextState = !this.allSelected;
                    this.items.forEach(function(i) {
                        i.selected = nextState;
                    });
                },
                resetSelection: function() {
                    this.items.forEach(function(i) {
                        i.selected = false;
                    });
                },
                selectAll: function() {
                    this.items.forEach(function(i) {
                        i.selected = true;
                    });
                },
                updateQty: function(item, delta) {
                    var newQty = item.quantity + delta;
                    if (newQty < 1) return;
                    if (newQty > item.stock) {
                        if (window.toastr) {
                            toastr.warning('Maksimal pembelian untuk varian ini adalah ' + item.stock + ' pcs.');
                        }
                        return;
                    }
                    item.quantity = newQty;
                    this.syncQtyToServer(item);
                },
                setQty: function(item, event) {
                    var val = parseInt(event.target.value) || 1;
                    if (val < 1) val = 1;
                    if (val > item.stock) {
                        val = item.stock;
                        if (window.toastr) {
                            toastr.warning('Maksimal stok tersedia adalah ' + item.stock + ' pcs.');
                        }
                    }
                    item.quantity = val;
                    event.target.value = val;
                    this.syncQtyToServer(item);
                },
                syncQtyToServer: function(item) {
                    clearTimeout(this.debounceTimer);
                    this.debounceTimer = setTimeout(function() {
                        var tokenEl = document.querySelector('meta[name="csrf-token"]');
                        var token = tokenEl ? tokenEl.getAttribute('content') : '';
                        fetch(item.updateUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'X-HTTP-Method-Override': 'PUT',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                quantity: item.quantity
                            })
                        }).then(function(res) {
                            return res.json();
                        }).then(function(data) {
                            if (data && !data.success && window.toastr) {
                                toastr.error(data.message || 'Gagal memperbarui kuantitas.');
                            }
                        }).catch(function(err) {
                            console.error(err);
                        });
                    }, 350);
                }
            };
        }
    </script>
@endsection
