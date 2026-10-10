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
    <div class="pt-4 pb-32 sm:pt-6 sm:pb-36 lg:pt-10 lg:pb-16 bg-canvas min-h-screen" x-data="cartPageManager()" x-cloak>
        <div class="wrap">
            {{-- Page Header --}}
            <div
                class="flex items-center justify-between border-b border-hairline-soft pb-4 sm:pb-5 mb-4 sm:mb-8 gap-3">
                <div>
                    <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.2em] text-mute block mb-0.5 sm:mb-1">
                        Detail Pesanan Anda
                    </span>
                    <h1
                        class="text-xl sm:text-3xl font-extrabold tracking-tight text-ink uppercase flex items-center gap-2 sm:gap-3">
                        <span>Tas Belanja</span>
                    </h1>
                </div>

                @if ($cart && $cart->items->isNotEmpty())
                    <div class="flex items-center gap-2 shrink-0">
                        <form action="{{ route('customer.cart.clear') }}" method="POST"
                            onsubmit="return confirm('Apakah Anda yakin ingin mengosongkan semua produk di tas belanja?');">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-4 sm:py-2 bg-soft-cloud hover:bg-rose-50 text-mute hover:text-sale border border-hairline-soft hover:border-sale/30 text-xs font-semibold rounded-full transition cursor-pointer select-none active:scale-95">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                <span class="hidden sm:inline">Kosongkan Tas</span>
                                <span class="sm:hidden text-[11px]">Kosongkan</span>
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            {{-- Empty State --}}
            <template x-if="items.length === 0">
                <div
                    class="text-center py-16 sm:py-20 bg-white border border-hairline-soft rounded-3xl max-w-lg mx-auto my-6 sm:my-8 p-6 sm:p-12 space-y-4 shadow-2xs">
                    <div class="w-16 h-16 bg-soft-cloud rounded-full flex items-center justify-center mx-auto text-ink">
                        <svg class="w-8 h-8 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <h2 class="text-lg sm:text-xl font-bold uppercase tracking-tight text-ink">Tas Belanja Anda Masih Kosong</h2>
                    <p class="text-xs text-mute leading-relaxed max-w-xs mx-auto">
                        Jelajahi koleksi jersey klub, tim nasional, dan rilisan arsip retro autentik kami.
                    </p>
                    <div class="pt-2">
                        <a href="{{ route('shop.index') }}"
                            class="bg-ink hover:bg-black text-white py-3 px-7 text-xs font-bold uppercase tracking-widest inline-flex rounded-full shadow-md transition select-none active:scale-95">
                            <span>Mulai Belanja &rarr;</span>
                        </a>
                    </div>
                </div>
            </template>

            {{-- Cart Content (2 Columns Layout) --}}
            <template x-if="items.length > 0">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 lg:gap-10 items-start">

                    {{-- 1. Left Column: Item List & Toolbar (7 Cols) --}}
                    <div class="lg:col-span-7 space-y-3 sm:space-y-4 min-w-0">

                        {{-- Checkbox Toolbar (Select All & Quick Actions) --}}
                        <div
                            class="bg-white border border-hairline-soft rounded-2xl p-3 sm:p-4 flex items-center justify-between gap-2 shadow-2xs select-none">
                            <label class="flex items-center gap-2 cursor-pointer select-none py-0.5">
                                <input type="checkbox" :checked="allSelected" @change="toggleSelectAll()"
                                    class="w-4 h-4 rounded border-neutral-300 text-ink focus:ring-ink transition cursor-pointer">
                                <span class="text-xs font-bold uppercase tracking-wider text-ink">
                                    Pilih Semua (<span x-text="items.length"></span>)
                                </span>
                            </label>

                            <div class="flex items-center gap-1.5">
                                <button type="button" @click="resetSelection()"
                                    class="px-2.5 py-1 sm:px-3 sm:py-1.5 bg-soft-cloud hover:bg-neutral-200 text-ink text-[11px] font-semibold rounded-full transition cursor-pointer active:scale-95">
                                    ✕ Batal
                                </button>
                                <button type="button" @click="selectAll()"
                                    class="px-2.5 py-1 sm:px-3 sm:py-1.5 bg-soft-cloud hover:bg-neutral-200 text-ink text-[11px] font-semibold rounded-full transition cursor-pointer active:scale-95">
                                    ✓ Semua
                                </button>
                            </div>
                        </div>

                        {{-- Item Rows --}}
                        <div class="space-y-3">
                            <template x-for="(item, index) in items" :key="item.id">
                                <div class="bg-white p-3 sm:p-5 border border-hairline-soft rounded-2xl sm:rounded-3xl flex flex-col sm:flex-row gap-3 sm:gap-5 items-stretch sm:items-center justify-between shadow-2xs transition hover:border-hairline"
                                    :class="{ 'opacity-60 bg-neutral-50/50': !item.selected }">

                                    {{-- Primary Item Block: Checkbox, Thumbnail, and Responsive Content Area --}}
                                    <div class="flex items-start gap-2.5 sm:gap-4 flex-1 min-w-0">
                                        {{-- Product Checkbox --}}
                                        <div class="pt-1 sm:pt-2 shrink-0">
                                            <label class="flex items-center justify-center p-0.5 cursor-pointer">
                                                <input type="checkbox" x-model="item.selected"
                                                    :aria-label="'Pilih ' + item.name"
                                                    class="w-4 h-4 sm:w-5 sm:h-5 rounded border-neutral-300 text-ink focus:ring-ink transition cursor-pointer">
                                            </label>
                                        </div>

                                        {{-- Thumbnail --}}
                                        <a :href="'/product/' + item.slug"
                                            class="w-20 h-24 sm:w-24 sm:h-28 shrink-0 rounded-xl sm:rounded-2xl overflow-hidden bg-soft-cloud border border-hairline-soft block group relative select-none">
                                            <img :src="item.image" :alt="item.name"
                                                class="w-full h-full object-cover object-center group-hover:scale-105 transition duration-300 block"
                                                loading="lazy">
                                        </a>

                                        {{-- Details Block --}}
                                        <div class="flex-1 min-w-0 flex flex-col justify-between self-stretch py-0.5">
                                            <div>
                                                {{-- Title & Mobile Delete Button --}}
                                                <div class="flex items-start justify-between gap-1.5">
                                                    <h3 class="font-bold text-xs sm:text-sm text-ink leading-snug line-clamp-2">
                                                        <a :href="'/product/' + item.slug" class="hover:text-neutral-600 transition"
                                                            x-text="item.name"></a>
                                                    </h3>

                                                    {{-- Mobile Delete Button (sm:hidden) --}}
                                                    <form :action="item.deleteUrl" method="POST" class="sm:hidden shrink-0 -mt-1 -mr-1"
                                                        data-confirm-title="Hapus dari Tas Belanja?"
                                                        data-confirm-text="Apakah Anda yakin ingin mengeluarkan jersey ini dari tas belanja?"
                                                        data-confirm-btn="Ya, Hapus">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="w-7 h-7 rounded-full text-mute hover:text-sale hover:bg-rose-50 flex items-center justify-center transition cursor-pointer"
                                                            title="Hapus Item" aria-label="Hapus Item">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>

                                                {{-- Badges (Size & Stock) --}}
                                                <div class="flex items-center gap-1.5 sm:gap-2 mt-1 sm:mt-1.5 flex-wrap">
                                                    <span
                                                        class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 bg-soft-cloud border border-hairline-soft rounded-full text-ink"
                                                        x-text="'Size ' + item.size">
                                                    </span>
                                                    <span class="text-[10px] text-mute font-medium">
                                                        Stok: <strong class="text-ink font-bold" x-text="item.stock"></strong>
                                                    </span>
                                                    <template x-if="item.customFee > 0">
                                                        <span class="text-[10px] font-bold text-amber-900 bg-amber-50 border border-amber-200/60 px-1.5 py-0.5 rounded-md">
                                                            + Custom
                                                        </span>
                                                    </template>
                                                </div>
                                            </div>

                                            {{-- Mobile In-Card Bottom Controls: Price on Left, Stepper on Right (sm:hidden) --}}
                                            <div class="flex sm:hidden items-end justify-between gap-2 mt-2 pt-1 border-t border-hairline-soft/60">
                                                {{-- Mobile Price / Subtotal --}}
                                                <div class="min-w-0">
                                                    <div class="font-extrabold text-xs text-ink tabular-nums tracking-tight"
                                                        x-text="formatRupiah((item.unitPrice + item.customFee) * item.quantity)">
                                                    </div>
                                                    <template x-if="item.quantity > 1">
                                                        <div class="text-[10px] text-mute tabular-nums leading-none mt-0.5"
                                                            x-text="'@ ' + formatRupiah(item.unitPrice + item.customFee)">
                                                        </div>
                                                    </template>
                                                    <template x-if="isPremium && item.rawPrice > item.unitPrice">
                                                        <div class="text-[10px] text-mute line-through tabular-nums leading-none mt-0.5"
                                                            x-text="formatRupiah(item.rawPrice)">
                                                        </div>
                                                    </template>
                                                </div>

                                                {{-- Mobile Stepper --}}
                                                <div class="flex items-center bg-soft-cloud border border-hairline rounded-full p-0.5 shrink-0">
                                                    <button type="button" @click="updateQty(item, -1)"
                                                        :disabled="item.quantity <= 1"
                                                        class="w-6 h-6 rounded-full bg-white hover:bg-neutral-200 text-ink flex items-center justify-center font-bold text-xs disabled:opacity-40 disabled:hover:bg-white transition cursor-pointer shadow-2xs active:scale-90"
                                                        aria-label="Kurangi kuantitas">
                                                        &minus;
                                                    </button>

                                                    <input type="number" :value="item.quantity" @change="setQty(item, $event)"
                                                        min="1" :max="item.stock"
                                                        class="w-7 text-center bg-transparent border-none text-xs font-bold text-ink p-0 focus:ring-0 focus:outline-none tabular-nums"
                                                        aria-label="Jumlah pesanan">

                                                    <button type="button" @click="updateQty(item, 1)"
                                                        :disabled="item.quantity >= item.stock"
                                                        class="w-6 h-6 rounded-full bg-white hover:bg-neutral-200 text-ink flex items-center justify-center font-bold text-xs disabled:opacity-40 disabled:hover:bg-white transition cursor-pointer shadow-2xs active:scale-90"
                                                        aria-label="Tambah kuantitas">
                                                        &#43;
                                                    </button>
                                                </div>
                                            </div>

                                            {{-- Desktop Unit Price (Hidden on Mobile) --}}
                                            <div class="hidden sm:flex items-center gap-2 text-xs text-mute mt-1.5 flex-wrap">
                                                <span class="font-bold tabular-nums text-ink"
                                                    x-text="formatRupiah(item.unitPrice + item.customFee)"></span>
                                                <template x-if="isPremium && item.rawPrice > item.unitPrice">
                                                    <span class="text-[11px] text-mute line-through tabular-nums"
                                                        x-text="formatRupiah(item.rawPrice)"></span>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Desktop Right Controls: Stepper, Subtotal & Delete Action (Hidden on Mobile) --}}
                                    <div class="hidden sm:flex sm:flex-col sm:items-end gap-3 shrink-0">
                                        {{-- Item Subtotal --}}
                                        <div class="font-extrabold text-base text-ink tabular-nums tracking-tight"
                                            x-text="formatRupiah((item.unitPrice + item.customFee) * item.quantity)">
                                        </div>

                                        <div class="flex items-center gap-2">
                                            {{-- Quantity Pill Controls --}}
                                            <div class="flex items-center bg-soft-cloud border border-hairline rounded-full p-0.5">
                                                <button type="button" @click="updateQty(item, -1)"
                                                    :disabled="item.quantity <= 1"
                                                    class="w-7 h-7 rounded-full bg-white hover:bg-neutral-200 text-ink flex items-center justify-center font-bold text-xs disabled:opacity-40 disabled:hover:bg-white transition cursor-pointer shadow-2xs active:scale-95"
                                                    aria-label="Kurangi kuantitas">
                                                    &minus;
                                                </button>

                                                <input type="number" :value="item.quantity" @change="setQty(item, $event)"
                                                    min="1" :max="item.stock"
                                                    class="w-10 text-center bg-transparent border-none text-xs font-bold text-ink p-0 focus:ring-0 focus:outline-none tabular-nums"
                                                    aria-label="Jumlah pesanan">

                                                <button type="button" @click="updateQty(item, 1)"
                                                    :disabled="item.quantity >= item.stock"
                                                    class="w-7 h-7 rounded-full bg-white hover:bg-neutral-200 text-ink flex items-center justify-center font-bold text-xs disabled:opacity-40 disabled:hover:bg-white transition cursor-pointer shadow-2xs active:scale-95"
                                                    aria-label="Tambah kuantitas">
                                                    &#43;
                                                </button>
                                            </div>

                                            {{-- Delete Button --}}
                                            <form :action="item.deleteUrl" method="POST"
                                                data-confirm-title="Hapus dari Tas Belanja?"
                                                data-confirm-text="Apakah Anda yakin ingin mengeluarkan jersey ini dari tas belanja?"
                                                data-confirm-btn="Ya, Hapus">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="w-8 h-8 rounded-full bg-soft-cloud hover:bg-rose-50 text-mute hover:text-sale flex items-center justify-center border border-hairline-soft transition cursor-pointer active:scale-95"
                                                    title="Hapus Item">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
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

                    {{-- 2. Right Column: Realtime Order Summary Card (5 Cols) --}}
                    <div
                        class="lg:col-span-5 bg-white p-4 sm:p-7 rounded-2xl sm:rounded-3xl border border-hairline-soft space-y-4 sm:space-y-6 lg:sticky lg:top-24 shadow-2xs">

                        {{-- Header --}}
                        <div class="border-b border-hairline-soft pb-3 sm:pb-4 flex items-center justify-between">
                            <h2 class="font-extrabold text-xs sm:text-sm uppercase tracking-wider text-ink">
                                Ringkasan Pesanan
                            </h2>
                            <span class="text-xs font-bold text-mute tabular-nums"
                                x-text="selectedCount + ' Item'"></span>
                        </div>

                        {{-- Selected Products Thumbnail Strip (Desktop Only) --}}
                        <div
                            class="hidden lg:block space-y-3 max-h-60 overflow-y-auto pr-1 border-b border-hairline-soft pb-4 divide-y divide-hairline-soft">
                            {{-- Empty Selection Alert --}}
                            <template x-if="selectedItems.length === 0">
                                <div
                                    class="py-5 text-center text-xs text-mute space-y-1 bg-soft-cloud/60 rounded-2xl p-4 border border-hairline-soft">
                                    <p class="font-bold text-ink">Belum Ada Jersey Dipilih</p>
                                    <p class="text-[11px] text-neutral-400">Centang kotak produk di sebelah kiri untuk melihat ringkasan item & melanjutkan checkout.</p>
                                </div>
                            </template>

                            {{-- Selected Items List --}}
                            <template x-for="item in selectedItems" :key="'summary-' + item.id">
                                <div class="flex items-center gap-3 pt-3 first:pt-0">
                                    {{-- Thumbnail --}}
                                    <div
                                        class="w-14 h-16 rounded-xl overflow-hidden bg-soft-cloud border border-hairline-soft shrink-0">
                                        <img :src="item.image" :alt="item.name"
                                            class="w-full h-full object-cover object-center">
                                    </div>

                                    {{-- Product Meta --}}
                                    <div class="flex-1 min-w-0 space-y-0.5">
                                        <h4 class="font-bold text-xs sm:text-sm text-ink line-clamp-1 leading-snug"
                                            x-text="item.name"></h4>
                                        <div class="flex items-center gap-2 text-[11px] text-mute flex-wrap">
                                            <span
                                                class="px-2 py-0.5 bg-soft-cloud border border-hairline-soft rounded-md font-bold text-ink text-[10px]"
                                                x-text="'Size ' + item.size"></span>
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
                        <div class="space-y-2.5 sm:space-y-3 text-xs">
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
                                <span>Ongkos Kirim J&T</span>
                                <span
                                    class="text-[11px] text-emerald-700 font-semibold bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/50">
                                    Gratis Ongkir (Rp 0)
                                </span>
                            </div>
                        </div>

                        {{-- Grand Total --}}
                        <div class="border-t border-hairline-soft pt-3 sm:pt-4 flex justify-between items-baseline">
                            <div>
                                <span class="text-[10px] uppercase font-bold tracking-widest text-mute block">Total
                                    Pembayaran</span>
                                <span class="text-[10px] sm:text-[11px] text-mute font-normal">Gratis Ongkir J&T Express</span>
                            </div>
                            <div class="text-xl sm:text-2xl font-extrabold text-ink tabular-nums tracking-tight"
                                x-text="formatRupiah(grandTotal)">
                            </div>
                        </div>

                        {{-- Main Checkout CTA Form (Visible in Summary Card on All Viewports) --}}
                        <div class="pt-1">
                            <form action="{{ route('customer.checkout.index') }}" method="GET"
                                @submit="if (selectedItems.length === 0) { $event.preventDefault(); return false; }">
                                <input type="hidden" name="selected_items" :value="selectedIdsString">

                                <button type="submit" :disabled="selectedItems.length === 0"
                                    :class="selectedItems.length === 0 ?
                                        'bg-neutral-200 text-neutral-400 border border-neutral-200 cursor-not-allowed shadow-none pointer-events-none' :
                                        'bg-ink hover:bg-black text-white shadow-md cursor-pointer active:scale-[0.99]'"
                                    class="w-full py-3.5 sm:py-4 text-center rounded-full text-xs font-bold uppercase tracking-widest flex items-center justify-center gap-2 transition duration-200 select-none">
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
                        <div class="space-y-2.5 pt-2 border-t border-hairline-soft text-xs text-mute">
                            <div class="flex items-start gap-2">
                                <span class="text-sm shrink-0">⏱️</span>
                                <div>
                                    <p class="font-bold text-ink text-[11px]">Batas Waktu Pembayaran 2 Jam</p>
                                    <p class="text-[10px] leading-relaxed text-neutral-400">Stok jersey otomatis diamankan
                                        (*Anti-Overselling Guard*). Jika lewat 2 jam, pesanan kedaluwarsa dan stok
                                        dikembalikan ke etalase.</p>
                                </div>
                            </div>

                            <div
                                class="flex items-center justify-center gap-1.5 text-neutral-500 font-medium text-[11px] pt-1">
                                <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                <span>Transaksi Terenkripsi Midtrans Snap</span>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ===== MOBILE FLOATING CHECKOUT BAR (Muncul di layar < 1024px dengan Safe-Area Inset) ===== --}}
                <div class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-xl border-t border-hairline-soft px-3.5 py-2.5 sm:px-4 sm:py-3 shadow-[0_-4px_25px_rgba(0,0,0,0.08)] flex items-center justify-between gap-2 select-none"
                    style="padding-bottom: max(0.625rem, env(safe-area-inset-bottom, 0.625rem));">
                    <div class="flex items-center gap-2 shrink-0">
                        <label class="flex items-center gap-1.5 cursor-pointer select-none">
                            <input type="checkbox" :checked="allSelected" @change="toggleSelectAll()"
                                class="w-4 h-4 rounded border-neutral-300 text-ink focus:ring-ink transition cursor-pointer"
                                id="mobile-select-all">
                            <span class="text-xs font-bold text-ink">
                                Semua
                            </span>
                        </label>
                    </div>

                    <div class="flex items-center gap-2.5 ml-auto min-w-0">
                        <div class="text-right truncate">
                            <span class="text-[9px] uppercase font-bold tracking-wider text-mute block leading-none">Total</span>
                            <span class="text-xs sm:text-sm font-extrabold text-ink tabular-nums block mt-0.5 truncate max-w-[125px]"
                                x-text="formatRupiah(grandTotal)"></span>
                        </div>

                        <form action="{{ route('customer.checkout.index') }}" method="GET"
                            @submit="if (selectedItems.length === 0) { $event.preventDefault(); return false; }">
                            <input type="hidden" name="selected_items" :value="selectedIdsString">
                            <button type="submit" :disabled="selectedItems.length === 0"
                                :class="selectedItems.length === 0 ? 'bg-neutral-200 text-neutral-400 cursor-not-allowed' : 'bg-ink hover:bg-black text-white active:scale-95 cursor-pointer shadow-md'"
                                class="px-4 py-2 sm:px-5 sm:py-2.5 rounded-full text-xs font-bold uppercase tracking-wider transition flex items-center gap-1 shrink-0 select-none">
                                <span>Checkout</span>
                                <span x-show="selectedCount > 0" x-text="'(' + selectedCount + ')'"></span>
                            </button>
                        </form>
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
