@extends('layouts.customer')

@section('title', 'NGIZAN APPAREL · Bespoke Football Kits & Archive Store')
@section('meta_description', 'Ngizan Apparel - Toko jersey sepak bola autentik, edisi retro arsip, dan live studio kustomisasi nameset nama & nomor punggung.')

@section('content')

    {{-- ===== 1. HERO SECTION: NIKE CAMPAIGN EDITORIAL ===== --}}
    <section id="hero" class="relative w-full overflow-hidden bg-soft-cloud border-b border-hairline-soft">
        <div class="relative min-h-[75vh] md:min-h-[82vh] flex items-end">
            {{-- Campaign Photography Background --}}
            <div class="absolute inset-0 z-0">
                <img src="https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&w=2000&q=85" 
                     alt="Ngizan Football Kit Campaign" 
                     class="w-full h-full object-cover object-center filter brightness-90">
                <div class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/30 to-transparent"></div>
            </div>

            {{-- Campaign Headline & CTAs (Lower-Left Placement per DESIGN.md) --}}
            <div class="wrap relative z-10 w-full pb-14 md:pb-20 pt-32">
                <div class="max-w-3xl space-y-4">
                    <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur-xs text-white text-[11px] font-medium tracking-wider uppercase rounded-full">
                        EDISI MUSIM 2024/2025 · AUTHENTIC KITS
                    </span>
                    <h1 class="display-campaign text-white text-[clamp(44px,9vw,92px)] leading-[0.9] tracking-tight">
                        WIN ON AIR.<br>BESPOKE KITS.
                    </h1>
                    <p class="text-white/85 text-sm sm:text-base font-medium max-w-xl leading-relaxed">
                        Arsip jersey sepak bola vintage terkurasi, edisi match-issue klub eropa, dan studio kustomisasi sablon polyflex berstandar resmi.
                    </p>
                    <div class="flex items-center gap-3 pt-2 flex-wrap">
                        <a href="#katalog" class="btn-outline-image">
                            Jelajahi Koleksi Jersey
                        </a>
                        <a href="#custom-studio" class="inline-flex items-center justify-center gap-2 bg-black/60 hover:bg-black/80 backdrop-blur-xs text-white px-6 py-3 rounded-full text-[15px] font-medium transition">
                            Studio Kustomisasi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== 2. CATEGORY TILES (FLAT CLEAN ON WHITE CANVAS) ===== --}}
    <section class="py-12 bg-canvas border-b border-hairline-soft">
        <div class="wrap">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">

                <a href="{{ route('shop.index', ['category' => 'klub-eropa']) }}"
                    class="group relative overflow-hidden bg-soft-cloud aspect-[4/3] flex flex-col justify-end p-4 sm:p-5 transition hover:bg-neutral-200">
                    <img src="https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&w=500&q=80"
                        alt="Klub Eropa"
                        class="absolute inset-0 w-full h-full object-cover mix-blend-multiply opacity-75 group-hover:scale-105 transition duration-500">
                    <div class="relative z-10">
                        <h3 class="font-medium text-sm sm:text-base text-ink uppercase tracking-tight">Klub Eropa</h3>
                        <p class="text-xs text-mute mt-0.5">EPL, La Liga, Serie A</p>
                    </div>
                </a>

                <a href="{{ route('shop.index', ['category' => 'tim-nasional']) }}"
                    class="group relative overflow-hidden bg-soft-cloud aspect-[4/3] flex flex-col justify-end p-4 sm:p-5 transition hover:bg-neutral-200">
                    <img src="https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=500&q=80"
                        alt="Tim Nasional"
                        class="absolute inset-0 w-full h-full object-cover mix-blend-multiply opacity-75 group-hover:scale-105 transition duration-500">
                    <div class="relative z-10">
                        <h3 class="font-medium text-sm sm:text-base text-ink uppercase tracking-tight">Tim Nasional</h3>
                        <p class="text-xs text-mute mt-0.5">Garuda & World Cup</p>
                    </div>
                </a>

                <a href="{{ route('shop.index', ['category' => 'retro-classics']) }}"
                    class="group relative overflow-hidden bg-soft-cloud aspect-[4/3] flex flex-col justify-end p-4 sm:p-5 transition hover:bg-neutral-200">
                    <img src="https://images.unsplash.com/photo-1518091043644-c1d4457512c6?auto=format&fit=crop&w=500&q=80"
                        alt="Retro Classics"
                        class="absolute inset-0 w-full h-full object-cover mix-blend-multiply opacity-75 group-hover:scale-105 transition duration-500">
                    <div class="relative z-10">
                        <h3 class="font-medium text-sm sm:text-base text-ink uppercase tracking-tight">Retro Archive</h3>
                        <p class="text-xs text-mute mt-0.5">Arsip Edisi 90s & 2000s</p>
                    </div>
                </a>

                <a href="#custom-studio"
                    class="group relative overflow-hidden bg-soft-cloud aspect-[4/3] flex flex-col justify-end p-4 sm:p-5 transition hover:bg-neutral-200">
                    <img src="https://images.unsplash.com/photo-1544698310-74ea9d1c8258?auto=format&fit=crop&w=500&q=80"
                        alt="Custom Nameset"
                        class="absolute inset-0 w-full h-full object-cover mix-blend-multiply opacity-75 group-hover:scale-105 transition duration-500">
                    <div class="relative z-10">
                        <h3 class="font-medium text-sm sm:text-base text-ink uppercase tracking-tight">Custom Nameset</h3>
                        <p class="text-xs text-mute mt-0.5">Nama & Nomor Font Resmi</p>
                    </div>
                </a>

            </div>
        </div>
    </section>

    {{-- ===== 3. FEATURED CATALOG WITH DUAL POV HOVER ===== --}}
    <section id="katalog" class="py-16 bg-canvas">
        <div class="wrap">
            <div class="flex flex-col sm:flex-row sm:items-baseline justify-between mb-8 pb-4 border-b border-hairline-soft gap-4">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-medium tracking-tight text-ink">Katalog Paling Dicari</h2>
                    <p class="text-xs text-mute mt-1">Arahkan kursor pada kartu produk untuk melihat tampak belakang (Back POV)</p>
                </div>
                <div>
                    <a href="{{ route('shop.index') }}"
                        class="text-xs font-medium text-ink hover:text-mute underline transition inline-flex items-center gap-1">
                        <span>Lihat Semua Produk ({{ $featuredProducts->count() }})</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            {{-- Grid Product Cards (Nike flat, square 1:1, soft-cloud studio background) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
                @forelse($featuredProducts as $product)
                    <x-product-card :product="$product" />
                @empty
                    <div class="col-span-4 text-center py-16 text-mute">
                        <p>Belum ada produk yang ditampilkan.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ===== 4. NIKE MEMBER PRIVILEGE EDITORIAL BANNER ===== --}}
    <section class="py-12 bg-canvas">
        <div class="wrap">
            <div class="relative overflow-hidden bg-ink text-white rounded-2xl p-8 sm:p-12 flex flex-col md:flex-row items-start md:items-center justify-between gap-8">
                <div class="max-w-xl space-y-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/10 rounded-full text-xs font-medium">
                        <span class="text-premium-gold">★</span>
                        <span>NGIZAN MEMBERSHIP</span>
                    </div>
                    <h3 class="display-campaign text-3xl sm:text-4xl leading-tight">
                        NIKMATI DISKON 5% & GRATIS ONGKIR SEUMUR HIDUP
                    </h3>
                    <p class="text-white/80 text-xs sm:text-sm leading-relaxed">
                        Bergabunglah dengan program Ngizan Premium untuk mendapatkan diskon langsung tanpa batas dan kemitraan kurir resmi J&T Express ke seluruh wilayah Indonesia.
                    </p>
                </div>
                <div class="shrink-0" x-data>
                    <button type="button" @click="$dispatch('open-premium-modal')" class="btn-outline-image px-8 py-4 text-sm font-medium">
                        Pelajari Keanggotaan &rarr;
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- ===== 5. LIVE INTERACTIVE CUSTOM NAMESET 2D STUDIO ===== --}}
    @php
        $studioProduct = $customStudioProduct ?? $featuredProducts->first();
        $studioBackImg = $studioProduct?->thumbnail_back
            ? asset('storage/' . $studioProduct->thumbnail_back)
            : 'https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&w=800&q=80';
        $studioBasePrice = $studioProduct ? (int) $studioProduct->base_price : 299000;
        $studioNamesetFee = $studioProduct ? (int) $studioProduct->custom_nameset_price : 50000;
        $studioPatchFee = $studioProduct ? (int) $studioProduct->patch_price : 35000;
    @endphp

    <section id="custom-studio" class="py-16 sm:py-20 bg-soft-cloud border-t border-hairline-soft"
        x-data="{
            productName: '{{ $studioProduct?->name ?? 'Real Madrid 2024/25 Home' }}',
            basePrice: {{ $studioBasePrice }},
            namesetFee: {{ $studioNamesetFee }},
            patchFee: {{ $studioPatchFee }},
            customName: 'BRUNO',
            customNumber: '8',
            selectedSize: 'L',
            selectedPatch: '',
            get hasNameset() {
                return this.customName.trim() !== '' || this.customNumber.trim() !== '';
            },
            get grandTotal() {
                let total = this.basePrice;
                if (this.hasNameset) total += this.namesetFee;
                if (this.selectedPatch) total += this.patchFee;
                return total;
            }
        }">
        <div class="wrap">
            <div class="max-w-2xl mb-10">
                <span class="text-xs font-medium uppercase tracking-widest text-mute block mb-1">Live Nameset Studio</span>
                <h2 class="text-2xl sm:text-3xl font-medium tracking-tight text-ink">
                    Pasang Nama & Nomor Punggung
                </h2>
                <p class="text-xs text-mute mt-1.5 leading-relaxed">
                    Teks sablon polyflex dirender langsung secara real-time di atas visual punggung jersey saat Anda mengetik nama dan nomor punggung.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                {{-- Left: Mockup Canvas Preview on soft-cloud studio background --}}
                <div class="lg:col-span-6 bg-white p-4 sm:p-6 border border-hairline-soft">
                    <div class="nameset-stage border border-hairline-soft">
                        <img src="{{ $studioBackImg }}" alt="Jersey Back Mockup Studio"
                            class="select-none pointer-events-none">

                        {{-- 2D Live Typography Layer --}}
                        <div class="nameset-layer">
                            <div class="nameset-text-name uppercase" x-text="customName || 'NAMA ANDA'"></div>
                            <div class="nameset-text-number" x-text="customNumber || '00'"></div>
                        </div>

                        {{-- Dynamic Patch Badge Display --}}
                        <template x-if="selectedPatch">
                            <div class="absolute bottom-4 right-4 bg-ink text-white px-3 py-1 rounded-full text-[11px] font-medium uppercase shadow-md">
                                <span x-text="'★ ' + selectedPatch"></span>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Right: Interactive Controls Form on White Card --}}
                <div class="lg:col-span-6 bg-white p-6 sm:p-8 border border-hairline-soft space-y-6">
                    <div>
                        <h3 class="font-medium text-lg text-ink" x-text="productName"></h3>
                        <p class="text-xs text-mute">Sablon Polyflex PU Premium & Heat Press 160°C</p>
                    </div>

                    <div class="space-y-4 text-xs">
                        {{-- Input Nama Punggung --}}
                        <div>
                            <label class="block font-medium text-ink mb-1.5">
                                Nama Punggung (Maks. 12 Karakter)
                                <span class="text-mute font-normal">(+Rp {{ number_format($studioNamesetFee, 0, ',', '.') }})</span>
                            </label>
                            <input type="text" x-model="customName" maxlength="12" placeholder="CONTOH: RONALDO"
                                class="w-full bg-soft-cloud border border-hairline px-4 py-2.5 rounded-full text-sm text-ink uppercase font-jersey tracking-widest focus:outline-none focus:border-ink">
                        </div>

                        {{-- Input Nomor Punggung --}}
                        <div>
                            <label class="block font-medium text-ink mb-1.5">
                                Nomor Punggung (0 - 99)
                            </label>
                            <input type="text" x-model="customNumber" maxlength="2" placeholder="7"
                                class="w-full bg-soft-cloud border border-hairline px-4 py-2.5 rounded-full text-lg text-ink font-jersey tracking-widest focus:outline-none focus:border-ink">
                        </div>

                        {{-- Pilih Ukuran --}}
                        <div>
                            <label class="block font-medium text-ink mb-2">Pilih Ukuran Jersey</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach (['S', 'M', 'L', 'XL', 'XXL'] as $sz)
                                    <button type="button" @click="selectedSize = '{{ $sz }}'"
                                        :class="selectedSize === '{{ $sz }}' ?
                                            'bg-ink text-white border-ink' :
                                            'bg-white text-ink border-hairline hover:border-ink'"
                                        class="w-12 py-2 text-center font-medium rounded-full border text-xs transition">
                                        {{ $sz }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Pilihan Patch Lengan --}}
                        <div>
                            <label class="block font-medium text-ink mb-1.5">
                                Pilihan Patch Turnamen
                                <span class="text-mute font-normal">(+Rp {{ number_format($studioPatchFee, 0, ',', '.') }})</span>
                            </label>
                            <select x-model="selectedPatch"
                                class="w-full bg-soft-cloud border border-hairline px-4 py-2.5 rounded-full text-xs text-ink focus:outline-none focus:border-ink">
                                <option value="">Tanpa Patch (+Rp 0)</option>
                                <option value="UCL Starball + Foundation">UEFA Champions League Starball + Foundation</option>
                                <option value="Premier League Official Gold">Premier League Official Sleeve Badge</option>
                                <option value="FIFA World Cup Qualifiers">FIFA World Cup Qualifiers Badge</option>
                            </select>
                        </div>
                    </div>

                    {{-- Price Bar & Action --}}
                    <div class="border-t border-hairline-soft pt-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <div class="text-[11px] text-mute uppercase font-medium">Total Estimasi Harga:</div>
                            <div class="text-2xl font-medium text-ink mt-0.5"
                                x-text="'Rp ' + grandTotal.toLocaleString('id-ID')"></div>
                        </div>

                        @if ($studioProduct)
                            <a :href="'{{ route('shop.show', $studioProduct->slug) }}' +
                            '?custom_name=' + encodeURIComponent(customName) + '&custom_number=' +
                            encodeURIComponent(customNumber) + '&size=' + selectedSize + '&patch=' +
                            encodeURIComponent(selectedPatch)"
                                class="btn-primary px-8 py-3.5 text-xs font-medium uppercase tracking-wider text-center">
                                <span>Beli Spesifikasi Ini &rarr;</span>
                            </a>
                        @else
                            <a href="{{ route('shop.index') }}"
                                class="btn-primary px-8 py-3.5 text-xs font-medium uppercase tracking-wider text-center">
                                <span>Beli di Katalog &rarr;</span>
                            </a>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection
