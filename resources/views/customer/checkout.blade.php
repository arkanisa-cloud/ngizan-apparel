@extends('layouts.customer')

@section('title', 'Checkout & Pengiriman · NGIZAN APPAREL')
@section('meta_description',
    'Lengkapi alamat pengiriman dengan pin point peta akurat, pilih kurir Biteship, dan bayar
    aman dengan Midtrans Snap.')

    @push('styles')
        <style>
            #map {
                height: 220px;
                width: 100%;
                border-radius: 16px;
                z-index: 10;
            }

            @media (min-width: 640px) {
                #map {
                    height: 280px;
                    border-radius: 20px;
                }
            }

            .leaflet-container {
                font-family: inherit;
            }
        </style>
    @endpush

@section('content')
    <div class="pt-4 pb-28 sm:pt-6 sm:pb-32 lg:pt-8 lg:pb-16 bg-canvas min-h-screen" x-data="checkoutApp()" x-cloak>
        <div class="wrap">

            {{-- ===== 1. EDITORIAL HEADER ===== --}}
            <div class="flex items-center justify-between border-b border-hairline-soft pb-4 sm:pb-5 mb-5 sm:mb-8 gap-3">
                <div>
                    <span
                        class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.2em] text-mute block mb-0.5 sm:mb-1">
                        Detail Pengiriman · Langkah Terakhir
                    </span>
                    <h1
                        class="text-xl sm:text-3xl md:text-4xl font-extrabold tracking-tight text-ink uppercase flex items-center gap-2 sm:gap-3">
                        <span>Checkout & Pengiriman</span>
                    </h1>
                </div>

                <div class="shrink-0">
                    <a href="{{ route('customer.cart.index') }}"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 sm:px-4 sm:py-2 bg-soft-cloud hover:bg-neutral-200 text-ink border border-hairline-soft text-xs font-semibold rounded-full transition active:scale-95 select-none">
                        &larr; <span class="hidden sm:inline">Kembali ke Tas Belanja</span><span
                            class="sm:hidden text-[11px]">Tas Belanja</span>
                    </a>
                </div>
            </div>

            <form @submit.prevent="submitOrder" class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-10 items-start">

                {{-- ===== 2. FORM DATA PENGIRIMAN & PINPOINT MAPS (7 COLS) ===== --}}
                <div class="lg:col-span-7 space-y-5 sm:space-y-6 min-w-0">

                    {{-- MOBILE ONLY: EXPANDABLE ORDER SUMMARY ACCORDION (Gaya Shopify / Nike) --}}
                    @php
                        $isPremium = auth()->check() && auth()->user()->isPremiumActive();
                        $rawSubtotal = 0;
                        foreach ($cart->items as $it) {
                            $itRaw = $it->variant ? (float) $it->variant->final_price : (float) $it->unit_price;
                            $rawSubtotal += ($itRaw + (float) $it->custom_fee) * $it->quantity;
                        }
                        $discountAmount = $isPremium ? max(0, $rawSubtotal - $cart->total_price) : 0;
                    @endphp
                    <div
                        class="lg:hidden bg-white border border-hairline-soft rounded-2xl overflow-hidden shadow-2xs select-none">
                        {{-- Accordion Header Bar --}}
                        <button type="button" @click="mobileSummaryOpen = !mobileSummaryOpen"
                            class="w-full px-4 py-3 bg-white hover:bg-neutral-50 flex items-center justify-between transition cursor-pointer text-xs">
                            <div class="flex items-center gap-2 font-bold text-ink">
                                <svg class="w-4 h-4 text-mute shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                <span>Ringkasan Pesanan ({{ $cart->items->sum('quantity') }} Item)</span>
                                <svg class="w-3.5 h-3.5 text-mute transition-transform duration-200"
                                    :class="mobileSummaryOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            <div class="font-extrabold text-ink tabular-nums text-right"
                                x-text="'Rp ' + grandTotal.toLocaleString('id-ID')">
                                {{ $cart->formatted_total_price }}
                            </div>
                        </button>

                        {{-- Collapsible Content Panel --}}
                        <div x-show="mobileSummaryOpen" x-cloak
                            class="p-4 bg-soft-cloud/40 border-t border-hairline-soft space-y-3.5">
                            {{-- Item List --}}
                            <div class="space-y-3 divide-y divide-hairline-soft max-h-56 overflow-y-auto pr-1">
                                @foreach ($cart->items as $item)
                                    @php
                                        $mItemImg = $item->product?->thumbnail_front
                                            ? asset('storage/' . $item->product->thumbnail_front)
                                            : 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=300&q=80';
                                    @endphp
                                    <div class="flex items-center gap-3 pt-3 first:pt-0">
                                        <div
                                            class="w-12 h-14 rounded-xl overflow-hidden bg-soft-cloud border border-hairline-soft shrink-0">
                                            <img src="{{ $mItemImg }}" alt="{{ $item->product->name }}"
                                                class="w-full h-full object-cover">
                                        </div>
                                        <div class="flex-1 min-w-0 space-y-0.5">
                                            <h4 class="font-bold text-xs text-ink truncate">{{ $item->product->name }}</h4>
                                            <div class="flex items-center gap-2 text-[10px] text-mute">
                                                <span
                                                    class="px-1.5 py-0.2 bg-white rounded border border-hairline-soft font-bold text-ink">Size
                                                    {{ $item->variant->size ?? 'M' }}</span>
                                                <span>{{ $item->quantity }} pcs</span>
                                            </div>
                                        </div>
                                        <div class="font-extrabold text-xs text-ink tabular-nums shrink-0">
                                            Rp {{ number_format($item->total_price, 0, ',', '.') }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Mini Breakdown --}}
                            <div class="space-y-2 pt-2 border-t border-hairline-soft text-xs">
                                <div class="flex justify-between items-center text-mute">
                                    <span>Subtotal</span>
                                    <span class="font-bold text-ink tabular-nums">Rp
                                        {{ number_format($rawSubtotal, 0, ',', '.') }}</span>
                                </div>
                                @if ($isPremium && $discountAmount > 0)
                                    <div
                                        class="flex justify-between items-center text-amber-950 bg-amber-50 p-2 rounded-xl border border-amber-200/60 text-[11px]">
                                        <span class="font-bold">⭐ Diskon Member VIP (5%)</span>
                                        <span class="font-bold text-sale tabular-nums">- Rp
                                            {{ number_format($discountAmount, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between items-center text-mute">
                                    <span>Ongkir J&T Express</span>
                                    <span
                                        class="text-[10px] text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/50">GRATIS
                                        (Rp 0)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- CARD 01: INFORMASI PENERIMA --}}
                    <div
                        class="bg-white p-4 sm:p-7 rounded-2xl sm:rounded-3xl border border-hairline-soft shadow-2xs space-y-4 sm:space-y-5">
                        <div class="flex items-center justify-between border-b border-hairline-soft pb-3.5">
                            <div class="flex items-center gap-2.5">
                                <span
                                    class="w-6 h-6 rounded-full bg-ink text-white text-[11px] font-bold flex items-center justify-center shrink-0">
                                    1
                                </span>
                                <h2 class="font-extrabold text-xs sm:text-sm text-ink uppercase tracking-wider">
                                    Informasi Penerima
                                </h2>
                            </div>

                            {{-- Saved Address Indicator --}}
                            @if ($addresses->isNotEmpty())
                                <span class="text-[11px] text-mute font-medium hidden sm:inline-block">
                                    {{ $addresses->count() }} Alamat Tersimpan
                                </span>
                            @endif
                        </div>

                        {{-- Quick Address Switcher Pills if user has saved addresses --}}
                        @if ($addresses->isNotEmpty())
                            <div class="space-y-2">
                                <label class="block text-[11px] uppercase font-bold tracking-wider text-mute">
                                    Pilih dari Alamat Tersimpan:
                                </label>
                                <div class="flex flex-wrap gap-2">
                                    @foreach ($addresses as $addr)
                                        <button type="button" @click="selectSavedAddress({{ json_encode($addr) }})"
                                            :class="form.full_address === @json($addr->full_address) ?
                                                'bg-ink text-white border-ink shadow-xs' :
                                                'bg-soft-cloud text-ink border-hairline hover:bg-neutral-200'"
                                            class="px-3 py-1.5 rounded-full border text-xs font-semibold transition cursor-pointer flex items-center gap-1.5 active:scale-95">
                                            <span>{{ $addr->label ?? 'Alamat' }}</span>
                                            @if ($addr->is_primary)
                                                <span
                                                    class="text-[9px] px-1.5 py-0.2 bg-emerald-500 text-white rounded-full font-bold">Utama</span>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 text-xs">
                            <div>
                                <label class="block font-bold text-ink mb-1.5 uppercase text-[11px] tracking-wider">
                                    Nama Lengkap Penerima *
                                </label>
                                <input type="text" x-model="form.recipient_name" required
                                    placeholder="Nama lengkap penerima"
                                    class="w-full bg-soft-cloud border border-hairline-soft px-4 py-2.5 sm:py-3 rounded-full text-xs text-ink focus:border-ink focus:bg-white focus:ring-0 transition placeholder:text-neutral-400 font-medium">
                            </div>
                            <div>
                                <label class="block font-bold text-ink mb-1.5 uppercase text-[11px] tracking-wider">
                                    Nomor WhatsApp Aktif *
                                </label>
                                <input type="tel" x-model="form.phone_number" required placeholder="081234567890"
                                    class="w-full bg-soft-cloud border border-hairline-soft px-4 py-2.5 sm:py-3 rounded-full text-xs text-ink focus:border-ink focus:bg-white focus:ring-0 transition placeholder:text-neutral-400 font-medium">
                            </div>
                        </div>
                    </div>

                    {{-- CARD 02: LOKASI & ALAMAT PENGIRIMAN + GOOGLE MAPS LINK --}}
                    <div
                        class="bg-white p-4 sm:p-7 rounded-2xl sm:rounded-3xl border border-hairline-soft shadow-2xs space-y-4 sm:space-y-5">
                        <div
                            class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-hairline-soft pb-3.5 gap-2.5 sm:gap-3">
                            <div class="flex items-center gap-2.5">
                                <span
                                    class="w-6 h-6 rounded-full bg-ink text-white text-[11px] font-bold flex items-center justify-center shrink-0">
                                    2
                                </span>
                                <h2 class="font-extrabold text-xs sm:text-sm text-ink uppercase tracking-wider">
                                    Lokasi & Alamat Pengiriman
                                </h2>
                            </div>

                            {{-- GPS Locator Button --}}
                            <button type="button" @click="getCurrentLocation()" :disabled="isGeocoding"
                                class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 sm:py-1.5 bg-ink hover:bg-neutral-800 text-white text-xs font-bold rounded-full transition shadow-xs cursor-pointer disabled:opacity-50 active:scale-95 self-start sm:self-auto">
                                <template x-if="!isGeocoding">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </template>
                                <template x-if="isGeocoding">
                                    <span
                                        class="inline-block w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin shrink-0"></span>
                                </template>
                                <span x-text="isGeocoding ? 'Mendeteksi Alamat...' : 'Gunakan Lokasi Saat Ini'"></span>
                            </button>
                        </div>

                        {{-- Autocomplete Area Biteship & Manual Helper --}}
                        <div class="relative text-xs space-y-1.5">
                            <label class="block font-bold text-ink uppercase text-[11px] tracking-wider">
                                Cari Kecamatan / Kota / Kelurahan *
                            </label>
                            <div class="relative">
                                <input type="text" x-model="areaSearchQuery"
                                    @input.debounce.350ms="searchBiteshipAreas()"
                                    placeholder="Ketik kecamatan, kelurahan, atau kode pos..."
                                    class="w-full bg-soft-cloud border border-hairline-soft pl-10 pr-4 py-2.5 sm:py-3 rounded-full text-xs text-ink focus:border-ink focus:bg-white focus:ring-0 transition placeholder:text-neutral-400 font-medium">
                                <div class="absolute left-3.5 top-3 sm:top-3.5 text-neutral-400 pointer-events-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>

                            <p class="text-[11px] text-mute flex items-center gap-1.5">
                                <span>💡</span>
                                <span>Ketik area atau <strong>geser pin di peta di bawah</strong> untuk pengisian
                                    otomatis.</span>
                            </p>

                            {{-- Dropdown Hasil Area Autocomplete --}}
                            <div x-show="areaResults.length > 0" x-cloak @click.away="areaResults = []"
                                class="absolute left-0 right-0 top-full mt-1.5 bg-white border border-hairline-soft rounded-2xl shadow-xl max-h-56 overflow-y-auto z-40 divide-y divide-hairline-soft">
                                <template x-for="area in areaResults" :key="area.id">
                                    <div @click="selectArea(area)"
                                        class="p-3 sm:p-3.5 hover:bg-soft-cloud cursor-pointer transition text-xs flex items-center justify-between">
                                        <div>
                                            <div class="font-bold text-ink" x-text="area.name"></div>
                                            <div class="text-[11px] text-mute"
                                                x-text="(area.district_name || '') + ', ' + (area.city_name || '') + ' - ' + (area.postal_code || '')">
                                            </div>
                                        </div>
                                        <span
                                            class="text-[10px] px-2 py-0.5 bg-soft-cloud border border-hairline-soft rounded-full text-mute font-medium shrink-0 ml-2">Pilih
                                            &rarr;</span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Area Terpilih Badge --}}
                        <template x-if="form.biteship_area_id">
                            <div
                                class="p-3 sm:p-3.5 bg-soft-cloud border border-hairline-soft rounded-2xl text-xs flex flex-col sm:flex-row justify-between sm:items-center gap-2">
                                <div class="space-y-0.5 min-w-0">
                                    <span class="text-[10px] uppercase font-bold tracking-wider text-mute block">Area
                                        Terpilih:</span>
                                    <p class="font-bold text-ink truncate"
                                        x-text="form.district_name + ', ' + form.city_name + ' (' + form.postal_code + ')'">
                                    </p>
                                </div>
                                <span
                                    class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200/50 rounded-full text-[11px] font-bold self-start sm:self-auto shrink-0">
                                    ✓ Terverifikasi J&T Express
                                </span>
                            </div>
                        </template>

                        {{-- Leaflet.js Interactive Map --}}
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <label class="block font-bold text-ink uppercase text-[11px] tracking-wider">
                                    Pin Point Lokasi Rumah (Geser Pin untuk Deteksi Alamat)
                                </label>
                                <span class="text-[10px] sm:text-[11px] text-mute hidden sm:inline">OpenStreetMap</span>
                            </div>

                            <div class="border border-hairline-soft rounded-2xl overflow-hidden relative shadow-inner">
                                <div id="map"></div>
                            </div>

                            {{-- Coordinate & Google Maps Button Bar --}}
                            <div
                                class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 p-2.5 sm:p-3 bg-soft-cloud rounded-2xl border border-hairline-soft text-xs">
                                <div class="flex items-center gap-2 text-mute truncate">
                                    <span class="w-2 h-2 rounded-full shrink-0"
                                        :class="isGeocoding ? 'bg-amber-500 animate-spin' : 'bg-emerald-500 animate-pulse'"></span>
                                    <span class="truncate">Koordinat: <strong class="text-ink font-bold tabular-nums"
                                            x-text="form.latitude.toFixed(5) + ', ' + form.longitude.toFixed(5)"></strong></span>
                                </div>

                                {{-- Google Maps Button --}}
                                <a :href="googleMapsUrl" target="_blank" rel="noopener noreferrer"
                                    class="inline-flex items-center gap-1.5 px-3 py-1 bg-white hover:bg-neutral-100 text-ink text-xs font-bold rounded-full border border-hairline shadow-2xs transition group self-start sm:self-auto cursor-pointer active:scale-95 shrink-0"
                                    title="Buka titik koordinat di Google Maps">
                                    <svg class="w-3.5 h-3.5 text-sale group-hover:scale-110 transition shrink-0"
                                        fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                                    </svg>
                                    <span>Buka Maps</span>
                                </a>
                            </div>

                            {{-- Dynamic Geocoding Status Card --}}
                            <div x-show="isGeocoding" x-cloak
                                class="p-3 bg-neutral-100 border border-hairline-soft rounded-2xl text-xs flex items-center gap-2.5 text-ink animate-pulse">
                                <span
                                    class="w-3.5 h-3.5 border-2 border-ink border-t-transparent rounded-full animate-spin shrink-0"></span>
                                <span class="font-medium">Mendeteksi nama jalan dan wilayah dari titik peta...</span>
                            </div>

                            <template x-if="detectedLocationText && !isGeocoding">
                                <div
                                    class="p-3 sm:p-3.5 bg-emerald-50/90 border border-emerald-200/80 rounded-2xl text-xs space-y-2 transition">
                                    <div class="flex items-center justify-between gap-2">
                                        <span
                                            class="text-[10px] uppercase font-bold tracking-wider text-emerald-800 flex items-center gap-1.5 truncate">
                                            <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span class="truncate">Alamat Terdeteksi Otomatis</span>
                                        </span>
                                        <button type="button" @click="applyAutoAddress()"
                                            class="px-2.5 py-1 bg-ink hover:bg-neutral-800 text-white rounded-full text-[10px] font-bold uppercase tracking-wider transition cursor-pointer shrink-0 active:scale-95">
                                            Terapkan ke Form
                                        </button>
                                    </div>
                                    <p class="font-bold text-ink leading-snug" x-text="detectedLocationText"></p>
                                    <div class="text-[11px] text-neutral-600 flex flex-wrap gap-x-3 gap-y-1">
                                        <span>Kecamatan: <strong class="text-ink"
                                                x-text="form.district_name || '-'"></strong></span>
                                        <span>Kota: <strong class="text-ink"
                                                x-text="form.city_name || '-'"></strong></span>
                                        <span>Kode Pos: <strong class="text-ink"
                                                x-text="form.postal_code || '-'"></strong></span>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Alamat Lengkap & Patokan Rumah --}}
                        <div class="space-y-3.5 sm:space-y-4 text-xs">
                            {{-- Penamaan / Label Alamat --}}
                            <div>
                                <label class="block font-bold text-ink mb-1.5 uppercase text-[11px] tracking-wider">
                                    Nama / Label Alamat *
                                </label>
                                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2 mb-2">
                                    <template x-for="preset in ['Rumah', 'Kantor', 'Kost', 'Apartemen']"
                                        :key="preset">
                                        <button type="button" @click="form.label = preset"
                                            :class="form.label === preset ? 'bg-ink text-white border-ink shadow-2xs' :
                                                'bg-soft-cloud text-ink border-hairline hover:bg-neutral-200'"
                                            class="px-3 py-1 rounded-full border text-xs font-semibold transition cursor-pointer active:scale-95">
                                            <span x-text="preset"></span>
                                        </button>
                                    </template>
                                </div>
                                <input type="text" x-model="form.label" required
                                    placeholder="Label alamat (Rumah, Kantor, Kosan...)"
                                    class="w-full bg-soft-cloud border border-hairline-soft px-4 py-2.5 rounded-full text-xs text-ink focus:border-ink focus:bg-white focus:ring-0 transition placeholder:text-neutral-400 font-medium">
                            </div>

                            <div>
                                <label class="block font-bold text-ink mb-1.5 uppercase text-[11px] tracking-wider">
                                    Alamat Lengkap (Nama Jalan, No. Rumah, RT/RW, Blok) *
                                </label>
                                <textarea x-model="form.full_address" required rows="2" placeholder="Jl. Kemang Raya No. 12B, RT 02/RW 04..."
                                    class="w-full bg-soft-cloud border border-hairline-soft p-3 sm:p-3.5 rounded-2xl text-xs text-ink focus:border-ink focus:bg-white focus:ring-0 transition placeholder:text-neutral-400 font-medium"></textarea>
                            </div>

                            <div>
                                <label class="block font-bold text-ink mb-1.5 uppercase text-[11px] tracking-wider">
                                    Patokan Rumah / Cat / Pagar (Opsional)
                                </label>
                                <input type="text" x-model="form.benchmark_notes"
                                    placeholder="Patokan alamat (pagar hitam, samping minimarket...)"
                                    class="w-full bg-soft-cloud border border-hairline-soft px-4 py-2.5 sm:py-3 rounded-full text-xs text-ink focus:border-ink focus:bg-white focus:ring-0 transition placeholder:text-neutral-400 font-medium">
                            </div>

                            {{-- Alamat Otomatis Tersimpan --}}
                            <div class="flex items-center gap-2 pt-1 text-xs text-neutral-500 font-medium">
                                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span>Alamat pengiriman otomatis tersimpan ke buku alamat Anda.</span>
                            </div>
                        </div>
                    </div>

                    {{-- CARD 03: KEMITRAAN EKSPEDISI J&T EXPRESS (GRATIS ONGKIR) --}}
                    <div
                        class="bg-white p-4 sm:p-7 rounded-2xl sm:rounded-3xl border border-hairline-soft shadow-2xs space-y-4 sm:space-y-5">
                        <div
                            class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-hairline-soft pb-3.5 gap-2">
                            <div class="flex items-center gap-2.5">
                                <span
                                    class="w-6 h-6 rounded-full bg-ink text-white text-[11px] font-bold flex items-center justify-center shrink-0">
                                    3
                                </span>
                                <h2 class="font-extrabold text-xs sm:text-sm text-ink uppercase tracking-wider">
                                    Kurir Pengiriman: J&T Express
                                </h2>
                            </div>
                            <span
                                class="px-2.5 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200/50 rounded-full text-[10px] font-bold uppercase tracking-wider self-start sm:self-auto shrink-0">
                                Kemitraan Resmi (Gratis Ongkir)
                            </span>
                        </div>

                        <p class="text-xs text-mute leading-relaxed">
                            Seluruh pesanan dikirimkan via kemitraan resmi <strong>J&T Express</strong> dengan fasilitas
                            <strong>Gratis Ongkir Flat Rp 0</strong> ke seluruh Indonesia disertai pelacakan nomor resi
                            otomatis.
                        </p>

                        {{-- Loading State --}}
                        <div x-show="isLoadingRates" class="py-6 text-center text-xs text-mute space-y-2.5">
                            <div
                                class="inline-block w-6 h-6 border-2 border-ink border-t-transparent rounded-full animate-spin">
                            </div>
                            <p class="font-semibold text-ink">Memeriksa jangkauan layanan J&T Express...</p>
                        </div>

                        {{-- Courier List Options (J&T Express Exclusive) --}}
                        <div x-show="!isLoadingRates && shippingOptions.length > 0"
                            class="space-y-2.5 max-h-72 overflow-y-auto pr-1">
                            <template x-for="(rate, idx) in shippingOptions" :key="idx">
                                <label
                                    class="flex flex-col sm:flex-row sm:items-center justify-between p-3.5 sm:p-4 border rounded-2xl cursor-pointer transition text-xs border-ink bg-soft-cloud/70 ring-1 ring-ink shadow-xs gap-3">
                                    <div class="flex items-start sm:items-center gap-3 min-w-0">
                                        <input type="radio" name="selected_courier" checked
                                            @change="selectCourier(rate)"
                                            class="w-4 h-4 text-ink focus:ring-ink shrink-0 mt-0.5 sm:mt-0">
                                        <div class="min-w-0 space-y-0.5">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span class="font-bold text-ink uppercase tracking-wide text-xs sm:text-sm"
                                                    x-text="rate.courier_name + ' · ' + rate.service_name"></span>
                                                <span
                                                    class="px-2 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200/50 rounded-full text-[10px] font-bold tracking-wider uppercase">
                                                    Gratis Ongkir
                                                </span>
                                            </div>
                                            <div class="text-[11px] text-mute font-medium leading-relaxed"
                                                x-text="'Estimasi Tiba: ' + (rate.etd || '1-2 Hari') + (rate.description ? ' · ' + rate.description : ' · Reguler Kilat J&T Express')">
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="text-left sm:text-right border-t sm:border-t-0 border-hairline-soft/60 pt-2 sm:pt-0 shrink-0 flex sm:block items-baseline justify-between">
                                        <span class="font-extrabold text-sm text-emerald-700 tracking-tight">GRATIS</span>
                                        <span class="block text-[10px] text-emerald-600 font-bold">Rp 0</span>
                                    </div>
                                </label>
                            </template>
                        </div>
                    </div>

                </div>

                {{-- ===== 3. ORDER SUMMARY CARD & MIDTRANS SNAP CTA (5 COLS) ===== --}}
                <div
                    class="lg:col-span-5 bg-white p-4 sm:p-7 rounded-2xl sm:rounded-3xl border border-hairline-soft space-y-5 sm:space-y-6 lg:sticky lg:top-24 shadow-2xs">

                    {{-- Header --}}
                    <div class="border-b border-hairline-soft pb-3.5 sm:pb-4 flex items-center justify-between">
                        <h2 class="font-extrabold text-xs sm:text-sm uppercase tracking-wider text-ink">
                            Ringkasan Pesanan
                        </h2>
                        <span class="text-xs font-bold text-mute tabular-nums">
                            {{ $cart->items->sum('quantity') }} Item
                        </span>
                    </div>

                    {{-- Product List with Visual Thumbnails --}}
                    <div
                        class="space-y-3 max-h-60 overflow-y-auto pr-1 border-b border-hairline-soft pb-4 divide-y divide-hairline-soft">
                        @foreach ($cart->items as $item)
                            @php
                                $itemImg = $item->product?->thumbnail_front
                                    ? asset('storage/' . $item->product->thumbnail_front)
                                    : 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=300&q=80';
                            @endphp
                            <div class="flex items-center gap-3 pt-3 first:pt-0">
                                {{-- Thumbnail --}}
                                <div
                                    class="w-14 h-16 sm:w-16 sm:h-20 rounded-xl sm:rounded-2xl overflow-hidden bg-soft-cloud border border-hairline-soft shrink-0">
                                    <img src="{{ $itemImg }}" alt="{{ $item->product->name }}"
                                        class="w-full h-full object-cover object-center">
                                </div>

                                {{-- Product Meta --}}
                                <div class="flex-1 min-w-0 space-y-0.5">
                                    <h4 class="font-bold text-xs sm:text-sm text-ink line-clamp-1 leading-snug">
                                        {{ $item->product->name }}
                                    </h4>
                                    <div class="flex items-center gap-2 text-[11px] text-mute flex-wrap">
                                        <span
                                            class="px-2 py-0.5 bg-soft-cloud border border-hairline-soft rounded-md font-bold text-ink text-[10px]">
                                            Size {{ $item->variant->size ?? 'M' }}
                                        </span>
                                        <span class="text-neutral-300">·</span>
                                        <span class="font-semibold text-ink">{{ $item->quantity }} pcs</span>
                                    </div>
                                </div>

                                {{-- Price --}}
                                <div class="font-extrabold text-xs sm:text-sm text-ink tabular-nums shrink-0 text-right">
                                    <div>Rp {{ number_format($item->total_price, 0, ',', '.') }}</div>
                                    @if ($isPremium && $item->variant && (float) $item->variant->final_price > (float) $item->unit_price)
                                        <div class="text-[10px] text-mute line-through tabular-nums font-normal">
                                            Rp
                                            {{ number_format(((float) $item->variant->final_price + (float) $item->custom_fee) * $item->quantity, 0, ',', '.') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Price Breakdown --}}
                    <div class="space-y-2.5 sm:space-y-3 text-xs">
                        <div class="flex justify-between items-center text-mute">
                            <span>Subtotal Jersey</span>
                            <span class="font-bold text-ink tabular-nums">Rp
                                {{ number_format($rawSubtotal, 0, ',', '.') }}</span>
                        </div>

                        @if ($isPremium && $discountAmount > 0)
                            <div
                                class="flex justify-between items-center p-2.5 bg-amber-50 border border-amber-200/60 rounded-xl text-amber-950">
                                <span class="font-bold flex items-center gap-1 text-[11px]">
                                    <span>⭐</span> Diskon Member VIP (5%)
                                </span>
                                <span class="font-bold text-sale text-xs tabular-nums">
                                    - Rp {{ number_format($discountAmount, 0, ',', '.') }}
                                </span>
                            </div>
                        @endif

                        <div class="flex justify-between items-center text-mute">
                            <span>Ongkos Kirim</span>
                            <template x-if="form.courier_service_code && form.shipping_cost <= 0">
                                <span
                                    class="text-[11px] text-emerald-700 font-bold bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200/50">
                                    GRATIS (Rp 0)
                                </span>
                            </template>
                            <template x-if="form.courier_service_code && form.shipping_cost > 0">
                                <span class="font-bold text-ink tabular-nums"
                                    x-text="'Rp ' + form.shipping_cost.toLocaleString('id-ID')"></span>
                            </template>
                            <template x-if="!form.courier_service_code">
                                <span
                                    class="text-[11px] text-mute font-semibold bg-soft-cloud px-2.5 py-0.5 rounded-full border border-hairline-soft">
                                    Belum Dipilih
                                </span>
                            </template>
                        </div>

                        <template x-if="form.courier_service_name">
                            <div class="flex justify-between items-center text-[11px] text-mute">
                                <span>Layanan</span>
                                <span class="font-medium text-ink" x-text="form.courier_service_name"></span>
                            </div>
                        </template>
                    </div>

                    {{-- Grand Total --}}
                    <div class="border-t border-hairline-soft pt-3 sm:pt-4 flex justify-between items-baseline">
                        <div>
                            <span class="text-[10px] uppercase font-bold tracking-widest text-mute block">Total
                                Pembayaran</span>
                            <span class="text-[10px] sm:text-[11px] text-mute font-normal">Sudah termasuk ongkir</span>
                        </div>
                        <div class="text-xl sm:text-2xl font-extrabold text-ink tabular-nums tracking-tight"
                            x-text="'Rp ' + grandTotal.toLocaleString('id-ID')">
                            {{ $cart->formatted_total_price }}
                        </div>
                    </div>

                    {{-- Catatan Pesanan --}}
                    <div class="space-y-1.5">
                        <label class="block font-bold text-ink text-[11px] uppercase tracking-wider">Catatan Khusus
                            Pesanan</label>
                        <input type="text" x-model="form.notes" placeholder="Catatan khusus untuk kurir (opsional)"
                            class="w-full bg-soft-cloud border border-hairline-soft px-4 py-2.5 rounded-full text-xs text-ink focus:border-ink focus:bg-white focus:ring-0 transition placeholder:text-neutral-400">
                    </div>

                    {{-- Tombol Bayar Sekarang --}}
                    <div>
                        <button type="submit" :disabled="isSubmitting"
                            :class="isSubmitting ?
                                'bg-neutral-200 text-neutral-400 border border-neutral-200 cursor-not-allowed shadow-none pointer-events-none' :
                                'bg-ink hover:bg-black text-white active:scale-[0.99] shadow-md cursor-pointer'"
                            class="w-full py-3.5 sm:py-4 text-center rounded-full text-xs font-bold uppercase tracking-[0.15em] flex items-center justify-center gap-2 transition duration-200 select-none">
                            <span x-show="!isSubmitting" class="flex items-center gap-2">
                                <span>Bayar Sekarang</span>
                                <span>&rarr;</span>
                            </span>
                            <span x-show="isSubmitting" x-cloak class="flex items-center gap-2">
                                <span
                                    class="inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                                <span>Memproses Pesanan...</span>
                            </span>
                        </button>
                    </div>

                    {{-- Destination Quick Review & Google Maps Verification link --}}
                    <div class="p-3 bg-soft-cloud border border-hairline-soft rounded-2xl text-[11px] text-mute space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-ink uppercase text-[10px] tracking-wider">Tujuan Pengiriman:</span>
                            <a :href="googleMapsUrl" target="_blank" rel="noopener noreferrer"
                                class="text-ink font-bold hover:underline inline-flex items-center gap-1">
                                <span>Buka Google Maps</span>
                                <span>&nearr;</span>
                            </a>
                        </div>
                        <p class="text-neutral-600 line-clamp-2"
                            x-text="form.full_address || 'Belum mengisi alamat lengkap'"></p>
                    </div>

                    {{-- Trust Indicators & Anti-Overselling Guard --}}
                    <div class="space-y-2.5 pt-2 border-t border-hairline-soft text-xs text-mute">
                        <div class="flex items-start gap-2.5">
                            <span class="text-sm shrink-0">⏱️</span>
                            <div>
                                <p class="font-bold text-ink text-[11px]">Batas Waktu Pembayaran 2 Jam</p>
                                <p class="text-[10px] leading-relaxed text-neutral-400">Stok jersey otomatis diamankan
                                    (*Anti-Overselling Guard*). Jika lewat 2 jam, pesanan kedaluwarsa dan stok dikembalikan
                                    ke etalase.</p>
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

                {{-- ===== MOBILE FLOATING PAY BAR (Muncul di layar < 1024px dengan Safe-Area Inset) ===== --}}
                <div class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-xl border-t border-hairline-soft px-3.5 py-2.5 sm:px-4 sm:py-3 shadow-[0_-4px_25px_rgba(0,0,0,0.08)] flex items-center justify-between gap-3 select-none"
                    style="padding-bottom: max(0.625rem, env(safe-area-inset-bottom, 0.625rem));">
                    <div class="min-w-0">
                        <span
                            class="text-[9px] uppercase font-bold tracking-wider text-mute block leading-none">Total</span>
                        <span
                            class="text-xs sm:text-sm font-extrabold text-ink tabular-nums block mt-0.5 truncate max-w-[130px]"
                            x-text="'Rp ' + grandTotal.toLocaleString('id-ID')"></span>
                    </div>

                    <button type="submit" :disabled="isSubmitting"
                        :class="isSubmitting ? 'bg-neutral-200 text-neutral-400 cursor-not-allowed shadow-none' :
                            'bg-ink hover:bg-black text-white active:scale-95 cursor-pointer shadow-md'"
                        class="px-5 py-2.5 rounded-full text-xs font-bold uppercase tracking-wider transition flex items-center gap-1.5 shrink-0 select-none">
                        <span x-show="!isSubmitting">Bayar Sekarang</span>
                        <span x-show="isSubmitting" x-cloak class="flex items-center gap-1.5">
                            <span
                                class="inline-block w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                            <span>Memproses...</span>
                        </span>
                        <span x-show="!isSubmitting">&rarr;</span>
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Midtrans Snap JS (Sandbox / Production) -->
    <script
        src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>

    <script>
        function checkoutApp() {
            return {
                cartSubtotal: {{ (float) $cart->total_price }},
                isSubmitting: false,
                isLoadingRates: false,
                isGeocoding: false,
                mobileSummaryOpen: false,
                detectedLocationText: '',
                areaSearchQuery: '',
                areaResults: [],
                shippingOptions: [{
                    courier_code: 'jnt',
                    courier_name: 'J&T Express',
                    service_code: 'ez',
                    service_name: 'EZ (Reguler Kilat)',
                    price: 0,
                    badge: 'Kemitraan Resmi (Gratis Ongkir)',
                    etd: '1-2 Hari',
                    description: 'Gratis Ongkir Kemitraan Resmi J&T Express x Ngizan Apparel'
                }],
                map: null,
                marker: null,

                form: {
                    recipient_name: '{{ Auth::user()->name }}',
                    phone_number: '{{ $primaryAddress?->phone_number ?? (Auth::user()->phone ?? '') }}',
                    label: '{{ $primaryAddress?->label ?? 'Rumah' }}',
                    full_address: '{{ $primaryAddress?->full_address ?? '' }}',
                    biteship_area_id: '{{ $primaryAddress?->biteship_area_id ?? 'IDNP6IDNC148IDND840IDZ12730' }}',
                    province_name: '{{ $primaryAddress?->province_name ?? 'DKI Jakarta' }}',
                    city_name: '{{ $primaryAddress?->city_name ?? 'Jakarta Selatan' }}',
                    district_name: '{{ $primaryAddress?->district_name ?? 'Tebet' }}',
                    postal_code: '{{ $primaryAddress?->postal_code ?? '12810' }}',
                    latitude: {{ $primaryAddress?->latitude ?? -6.229728 }},
                    longitude: {{ $primaryAddress?->longitude ?? 106.855556 }},
                    benchmark_notes: '{{ $primaryAddress?->benchmark_notes ?? '' }}',
                    save_address: true,
                    courier_code: 'jnt',
                    courier_service_code: 'ez',
                    courier_service_name: 'J&T Express · EZ (Reguler Kilat)',
                    shipping_cost: 0,
                    notes: ''
                },

                get grandTotal() {
                    return this.cartSubtotal + (parseFloat(this.form.shipping_cost) || 0);
                },

                get googleMapsUrl() {
                    if (this.form.latitude && this.form.longitude) {
                        return 'https://www.google.com/maps/search/?api=1&query=' + this.form.latitude + ',' + this.form
                            .longitude;
                    }
                    if (this.form.full_address) {
                        const parts = [
                            this.form.full_address,
                            this.form.district_name,
                            this.form.city_name,
                            this.form.province_name,
                            this.form.postal_code
                        ].filter(Boolean).join(', ');
                        return 'https://www.google.com/maps/search/?api=1&query=' + encodeURIComponent(parts);
                    }
                    return 'https://maps.google.com';
                },

                init() {
                    if (this.form.district_name || this.form.city_name) {
                        this.areaSearchQuery = [this.form.district_name, this.form.city_name].filter(Boolean).join(', ') + (
                            this.form.postal_code ? ' (' + this.form.postal_code + ')' : '');
                    }
                    this.initMap();
                    this.fetchShippingRates();
                },

                initMap() {
                    this.$nextTick(() => {
                        const mapEl = document.getElementById('map');
                        if (!mapEl) return;

                        if (typeof L === 'undefined') {
                            console.error('Leaflet library is not available.');
                            return;
                        }

                        // Fix default icon assets in Leaflet
                        try {
                            delete L.Icon.Default.prototype._getIconUrl;
                            L.Icon.Default.mergeOptions({
                                iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
                                iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
                                shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                            });
                        } catch (e) {
                            console.warn('Leaflet icon config notice:', e);
                        }

                        this.map = L.map('map').setView([this.form.latitude, this.form.longitude], 15);
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; OpenStreetMap contributors'
                        }).addTo(this.map);

                        this.marker = L.marker([this.form.latitude, this.form.longitude], {
                            draggable: true
                        }).addTo(this.map);

                        this.marker.on('dragend', (e) => {
                            const pos = e.target.getLatLng();
                            this.form.latitude = pos.lat;
                            this.form.longitude = pos.lng;
                            this.reverseGeocodeLocation(pos.lat, pos.lng);
                        });

                        setTimeout(() => {
                            if (this.map) this.map.invalidateSize();
                        }, 250);

                        window.addEventListener('resize', () => {
                            if (this.map) this.map.invalidateSize();
                        }, {
                            passive: true
                        });
                    });
                },

                applyAutoAddress() {
                    if (this.detectedLocationText) {
                        this.form.full_address = this.detectedLocationText;
                        if (window.toastr) {
                            toastr.info('Alamat dari peta berhasil disalin ke form Alamat Lengkap.');
                        }
                    }
                },

                async reverseGeocodeLocation(lat, lng) {
                    this.isGeocoding = true;
                    try {
                        const res = await fetch(
                            `/api/shipping/reverse-geocode?latitude=${encodeURIComponent(lat)}&longitude=${encodeURIComponent(lng)}`
                        );
                        const data = await res.json();
                        if (data.success && data.geo) {
                            const geo = data.geo;
                            this.detectedLocationText = geo.street_address || geo.road || geo.display_name || '';

                            // LANGSUNG ISI ALAMAT LENGKAP SECARA OTOMATIS
                            if (this.detectedLocationText) {
                                this.form.full_address = this.detectedLocationText;
                            }

                            if (geo.state) this.form.province_name = geo.state;
                            if (geo.city) this.form.city_name = geo.city;
                            if (geo.district) this.form.district_name = geo.district;
                            if (geo.postcode) this.form.postal_code = geo.postcode.toString();

                            // Jika ada area Biteship yang cocok
                            if (data.matched_area) {
                                this.form.biteship_area_id = data.matched_area.id;
                                this.form.district_name = data.matched_area.administrative_division_level_3_name || data
                                    .matched_area.name;
                                this.form.city_name = data.matched_area.administrative_division_level_2_name || this
                                    .form.city_name;
                                this.form.province_name = data.matched_area.administrative_division_level_1_name || this
                                    .form.province_name;
                                if (data.matched_area.postal_code) {
                                    this.form.postal_code = data.matched_area.postal_code.toString();
                                }
                                // LANGSUNG ISI KOLOM PENCARIAN KECAMATAN SECARA OTOMATIS
                                this.areaSearchQuery = [this.form.district_name, this.form.city_name].filter(Boolean)
                                    .join(', ') + ' (' + this.form.postal_code + ')';
                            } else if (geo.district || geo.city) {
                                this.areaSearchQuery = [geo.district, geo.city].filter(Boolean).join(', ') + (geo
                                    .postcode ? ' (' + geo.postcode + ')' : '');
                            }

                            if (window.toastr) {
                                toastr.success('Alamat & Kecamatan berhasil diisi otomatis dari lokasi GPS!');
                            }

                            this.fetchShippingRates();
                        }
                    } catch (e) {
                        console.error('Reverse geocode error:', e);
                    } finally {
                        this.isGeocoding = false;
                    }
                },

                selectSavedAddress(addr) {
                    if (!addr) return;
                    this.form.recipient_name = addr.recipient_name || this.form.recipient_name;
                    this.form.phone_number = addr.phone_number || this.form.phone_number;
                    this.form.label = addr.label || this.form.label;
                    this.form.full_address = addr.full_address || '';
                    this.form.benchmark_notes = addr.benchmark_notes || '';
                    this.form.biteship_area_id = addr.biteship_area_id || this.form.biteship_area_id;
                    this.form.province_name = addr.province_name || '';
                    this.form.city_name = addr.city_name || '';
                    this.form.district_name = addr.district_name || '';
                    this.form.postal_code = addr.postal_code || '';
                    this.areaSearchQuery = [this.form.district_name, this.form.city_name].filter(Boolean).join(', ') + (this
                        .form.postal_code ? ' (' + this.form.postal_code + ')' : '');
                    if (addr.latitude && addr.longitude) {
                        this.form.latitude = parseFloat(addr.latitude);
                        this.form.longitude = parseFloat(addr.longitude);
                        if (this.map && this.marker) {
                            this.map.setView([this.form.latitude, this.form.longitude], 15);
                            this.marker.setLatLng([this.form.latitude, this.form.longitude]);
                        }
                    }
                    if (window.toastr) {
                        toastr.info('Alamat ' + (addr.label || 'pilihan') + ' telah dimuat.');
                    }
                    this.fetchShippingRates();
                },

                getCurrentLocation() {
                    if (navigator.geolocation) {
                        this.isGeocoding = true;
                        navigator.geolocation.getCurrentPosition((pos) => {
                            this.form.latitude = pos.coords.latitude;
                            this.form.longitude = pos.coords.longitude;
                            if (this.map && this.marker) {
                                this.map.setView([pos.coords.latitude, pos.coords.longitude], 16);
                                this.marker.setLatLng([pos.coords.latitude, pos.coords.longitude]);
                            }
                            if (window.toastr) {
                                toastr.info('Mendeteksi detail alamat dari GPS...');
                            }
                            this.reverseGeocodeLocation(pos.coords.latitude, pos.coords.longitude);
                        }, (err) => {
                            this.isGeocoding = false;
                            if (window.toastr) {
                                toastr.warning('Izin akses lokasi GPS ditolak atau tidak tersedia.');
                            }
                        });
                    }
                },

                async searchBiteshipAreas() {
                    if (this.areaSearchQuery.length < 3) {
                        this.areaResults = [];
                        return;
                    }
                    try {
                        const res = await fetch(
                            `/api/shipping/areas?query=${encodeURIComponent(this.areaSearchQuery)}`);
                        const data = await res.json();
                        this.areaResults = data.areas || [];
                    } catch (e) {
                        console.error(e);
                    }
                },

                selectArea(area) {
                    this.form.biteship_area_id = area.id;
                    this.form.province_name = area.province_name || '';
                    this.form.city_name = area.city_name || '';
                    this.form.district_name = area.district_name || area.name;
                    this.form.postal_code = area.postal_code || '';
                    this.areaSearchQuery = area.name || ([this.form.district_name, this.form.city_name].filter(Boolean)
                        .join(', ') + ' (' + this.form.postal_code + ')');
                    if (area.latitude && area.longitude) {
                        this.form.latitude = parseFloat(area.latitude);
                        this.form.longitude = parseFloat(area.longitude);
                        if (this.map && this.marker) {
                            this.map.setView([area.latitude, area.longitude], 14);
                            this.marker.setLatLng([area.latitude, area.longitude]);
                        }
                    }
                    this.areaResults = [];
                    this.fetchShippingRates();
                },

                async fetchShippingRates() {
                    this.isLoadingRates = true;
                    try {
                        const tokenEl = document.querySelector('meta[name="csrf-token"]');
                        const token = tokenEl ? tokenEl.getAttribute('content') : '';

                        const res = await fetch('/api/shipping/rates', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token
                            },
                            body: JSON.stringify({
                                destination_area_id: this.form.biteship_area_id,
                                destination_latitude: this.form.latitude,
                                destination_longitude: this.form.longitude
                            })
                        });
                        const data = await res.json();
                        this.shippingOptions = data.pricing || [];
                        if (this.shippingOptions.length > 0) {
                            const current = this.shippingOptions.find(o => o.service_code === this.form
                                .courier_service_code && o.courier_code === this.form.courier_code);
                            if (!current) {
                                this.selectCourier(this.shippingOptions[0]);
                            }
                        }
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.isLoadingRates = false;
                    }
                },

                selectCourier(rate) {
                    this.form.courier_code = rate.courier_code;
                    this.form.courier_service_code = rate.service_code;
                    this.form.courier_service_name = rate.courier_name + ' · ' + rate.service_name;
                    this.form.shipping_cost = rate.price;
                },

                async submitOrder() {
                    // Validasi Nomor WhatsApp Indonesia (08... / 628... / +628...)
                    const phoneVal = (this.form.phone_number || '').trim();
                    const phoneRegex = /^(\+62|62|0)8[1-9][0-9]{7,11}$/;
                    if (!phoneRegex.test(phoneVal)) {
                        if (window.toastr) {
                            toastr.error(
                                'Nomor WhatsApp harus berupa nomor seluler Indonesia yang valid (contoh: 081234567890).'
                            );
                        }
                        return;
                    }

                    // Otomatis fallback ke kurir resmi J&T jika belum terpilih
                    if (!this.form.courier_service_code) {
                        this.form.courier_code = 'jnt';
                        this.form.courier_service_code = 'ez';
                        this.form.courier_service_name = 'J&T Express · EZ (Reguler Kilat)';
                        this.form.shipping_cost = 0;
                    }

                    this.isSubmitting = true;
                    try {
                        const tokenEl = document.querySelector('meta[name="csrf-token"]');
                        const token = tokenEl ? tokenEl.getAttribute('content') : '';

                        const res = await fetch('{{ route('customer.checkout.store') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token
                            },
                            body: JSON.stringify(this.form)
                        });

                        const data = await res.json();

                        if (!data.success) {
                            let errorMsg = data.message || 'Gagal memproses pesanan.';
                            if (data.errors) {
                                const firstKey = Object.keys(data.errors)[0];
                                if (data.errors[firstKey] && data.errors[firstKey][0]) {
                                    errorMsg = data.errors[firstKey][0];
                                }
                            }
                            if (window.toastr) {
                                toastr.error(errorMsg);
                            }
                            this.isSubmitting = false;
                            return;
                        }

                        if (window.toastr) {
                            toastr.success(data.message);
                        }

                        // Trigger Midtrans Snap Popup
                        if (data.snap_token && typeof window.snap !== 'undefined') {
                            window.snap.pay(data.snap_token, {
                                onSuccess: function(result) {
                                    window.location.href = `/customer/orders/${data.order_id}`;
                                },
                                onPending: function(result) {
                                    window.location.href = `/customer/orders/${data.order_id}`;
                                },
                                onError: function(result) {
                                    window.location.href = `/customer/orders/${data.order_id}`;
                                },
                                onClose: function() {
                                    window.location.href = `/customer/orders/${data.order_id}`;
                                }
                            });
                        } else if (data.redirect_url) {
                            window.location.href = data.redirect_url;
                        } else {
                            window.location.href = `/customer/orders/${data.order_id}`;
                        }

                    } catch (e) {
                        if (window.toastr) {
                            toastr.error('Terjadi kesalahan jaringan atau koneksi server.');
                        }
                        this.isSubmitting = false;
                    }
                }
            };
        }
    </script>
@endpush
