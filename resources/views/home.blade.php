@extends('layouts.customer')

@section('title', 'NGIZAN APPAREL · Bespoke Football Kits & Archive Store')
@section('meta_description',
    'Ngizan Apparel - Toko jersey sepak bola autentik, edisi retro arsip, dan live studio
    kustomisasi nameset nama & nomor punggung.')

@section('content')

    {{-- ===== 1. HERO SECTION: MODEL CUTOUT IN TYPOGRAPHY WORDMARK ===== --}}
    <section id="hero"
        class="relative min-h-[calc(88vh-76px)] flex flex-col justify-center overflow-hidden border-b border-black/10 py-10">
        <div class="wrap relative w-full">

            {{-- Top-Left Sub-label --}}
            <div class="absolute left-6 top-2 z-10 hidden md:block">
                <p class="lbl text-ink-muted">EDISI MUSIM 2024/2025</p>
                <div class="w-12 h-[1.5px] bg-ink mt-2"></div>
            </div>

            {{-- Bottom-Right Details --}}
            <div class="absolute right-6 bottom-4 z-10 text-right hidden md:block">
                <p class="lbl text-ink-muted">STANDAR PRESTASI & ELEGANSI</p>
                <p class="text-xs text-ink-muted mt-1">Sablon DTF High-Density & Patch Turnamen Resmi</p>
            </div>

            {{-- Stage: Big Wordmark Behind Cutout Model --}}
            <div class="relative w-full grid place-items-center min-h-[min(55vh,540px)]">
                {{-- Big Typography Behind Model --}}
                <div class="absolute inset-0 grid place-items-center pointer-events-none select-none">
                    <b
                        class="font-display font-black text-[clamp(75px,20vw,290px)] tracking-[-0.03em] leading-[0.8] whitespace-nowrap text-ink opacity-95">
                        NGIZAN
                    </b>
                </div>

                {{-- Athlete Model Cutout --}}
                <div class="relative z-10 h-[min(58vh,560px)] flex items-end">
                    <img src="{{ asset('build/assets/images/model.png') }}" alt="Ngizan Football Athlete Model"
                        class="h-full w-auto object-contain drop-shadow-[0_26px_46px_rgba(16,16,16,0.25)] rounded-t-xl" />
                </div>
            </div>

            {{-- Call To Action Buttons --}}
            <div class="flex items-center justify-center gap-4 mt-6 flex-wrap z-20 relative">
                <a href="#katalog" class="btn-curtain">
                    <span>Jelajahi Koleksi Jersey</span>
                </a>
                <a href="#custom-studio" class="btn-line">
                    <span>Studio Kustomisasi</span>
                </a>
            </div>

        </div>
    </section>

    {{-- ===== 2. CATEGORY STRIP (DARK BLOCK) ===== --}}
    <section class="bg-neutral-950 text-[#EFEDE8] py-12 border-b border-neutral-800">
        <div class="wrap">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                <a href="{{ route('shop.index', ['category' => 'klub-eropa']) }}"
                    class="group flex items-center gap-4 p-2 rounded hover:bg-neutral-900 transition">
                    <div class="w-14 h-16 bg-neutral-900 overflow-hidden rounded flex-shrink-0">
                        <img src="https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&w=200&q=80"
                            alt="Klub Eropa"
                            class="w-full h-full object-cover grayscale group-hover:grayscale-0 group-hover:scale-110 transition duration-500">
                    </div>
                    <div>
                        <h3 class="font-bold uppercase tracking-wider text-xs text-white">Klub Eropa</h3>
                        <p class="text-[11px] text-neutral-400 mt-0.5">EPL, La Liga, Serie A</p>
                    </div>
                </a>

                <a href="{{ route('shop.index', ['category' => 'tim-nasional']) }}"
                    class="group flex items-center gap-4 p-2 rounded hover:bg-neutral-900 transition">
                    <div class="w-14 h-16 bg-neutral-900 overflow-hidden rounded flex-shrink-0">
                        <img src="https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=200&q=80"
                            alt="Tim Nasional"
                            class="w-full h-full object-cover grayscale group-hover:grayscale-0 group-hover:scale-110 transition duration-500">
                    </div>
                    <div>
                        <h3 class="font-bold uppercase tracking-wider text-xs text-white">Tim Nasional</h3>
                        <p class="text-[11px] text-neutral-400 mt-0.5">Garuda & World Cups</p>
                    </div>
                </a>

                <a href="{{ route('shop.index', ['category' => 'retro-classics']) }}"
                    class="group flex items-center gap-4 p-2 rounded hover:bg-neutral-900 transition">
                    <div class="w-14 h-16 bg-neutral-900 overflow-hidden rounded flex-shrink-0">
                        <img src="https://images.unsplash.com/photo-1518091043644-c1d4457512c6?auto=format&fit=crop&w=200&q=80"
                            alt="Retro Classics"
                            class="w-full h-full object-cover grayscale group-hover:grayscale-0 group-hover:scale-110 transition duration-500">
                    </div>
                    <div>
                        <h3 class="font-bold uppercase tracking-wider text-xs text-white">Retro Classics</h3>
                        <p class="text-[11px] text-neutral-400 mt-0.5">Arsip Edisi 90s & 2000s</p>
                    </div>
                </a>

                <a href="#custom-studio" class="group flex items-center gap-4 p-2 rounded hover:bg-neutral-900 transition">
                    <div class="w-14 h-16 bg-neutral-900 overflow-hidden rounded flex-shrink-0">
                        <img src="https://images.unsplash.com/photo-1544698310-74ea9d1c8258?auto=format&fit=crop&w=200&q=80"
                            alt="Custom Studio"
                            class="w-full h-full object-cover grayscale group-hover:grayscale-0 group-hover:scale-110 transition duration-500">
                    </div>
                    <div>
                        <h3 class="font-bold uppercase tracking-wider text-xs text-lime-400">Custom Nameset</h3>
                        <p class="text-[11px] text-neutral-400 mt-0.5">Nama & Nomor Font Resmi</p>
                    </div>
                </a>

            </div>
        </div>
    </section>

    {{-- ===== 3. FEATURED CATALOG WITH DUAL POV HOVER ===== --}}
    <section id="katalog" class="py-16">
        <div class="wrap">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10 gap-4">
                <div>
                    <h2 class="font-display font-black text-2xl md:text-3xl text-ink mt-1 uppercase tracking-tight">Katalog Paling Dicari</h2>
                </div>
                <div class="flex items-center gap-4">
                    <p class="text-xs text-ink-muted hidden md:block">Hover jersey untuk melihat tampak belakang (Back POV)
                    </p>
                    <a href="{{ route('shop.index') }}"
                        class="text-xs font-bold uppercase tracking-wider text-cyan-600 hover:text-cyan-700 transition">
                        Lihat Semua ({{ $featuredProducts->count() }}) &rarr;
                    </a>
                </div>
            </div>

            {{-- Grid Product Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($featuredProducts as $product)
                    <x-product-card :product="$product" />
                @empty
                    <div class="col-span-4 text-center py-12 text-ink-muted">
                        <p>Belum ada produk yang ditampilkan.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- ===== 4. LIVE INTERACTIVE CUSTOM NAMESET 2D STUDIO ===== --}}
    @php
        $studioProduct = $customStudioProduct ?? $featuredProducts->first();
        $studioBackImg = $studioProduct?->thumbnail_back
            ? asset('storage/' . $studioProduct->thumbnail_back)
            : 'https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&w=800&q=80';
        $studioBasePrice = $studioProduct ? (int) $studioProduct->base_price : 299000;
        $studioNamesetFee = $studioProduct ? (int) $studioProduct->custom_nameset_price : 50000;
        $studioPatchFee = $studioProduct ? (int) $studioProduct->patch_price : 35000;
    @endphp

    <section id="custom-studio" class="py-20 bg-neutral-900 text-[#F4F1EA] border-t border-neutral-800"
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
            <div class="max-w-2xl mb-12">
                <span class="lbl text-cyan-400">STUDIO KUSTOMISASI LIVE</span>
                <h2 class="font-display font-black text-3xl md:text-4xl text-white mt-1 uppercase tracking-tight">
                    Pasang Nama & Nomor Punggung
                </h2>
                <p class="text-xs text-neutral-400 mt-2">
                    Teks sablon langsung dirender secara live di atas punggung jersey secara real-time saat Anda mengetik
                    nama & nomor punggung.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">

                {{-- Left: Mockup Canvas Preview (6 cols) --}}
                <div class="lg:col-span-6">
                    <div class="nameset-stage border border-neutral-800">
                        <img src="{{ $studioBackImg }}" alt="Jersey Back Mockup Studio"
                            class="select-none pointer-events-none">

                        {{-- 2D Live Typography Layer --}}
                        <div class="nameset-layer">
                            <div class="nameset-text-name uppercase" x-text="customName || 'NAMA ANDA'"></div>
                            <div class="nameset-text-number" x-text="customNumber || '00'"></div>
                        </div>

                        {{-- Dynamic Patch Badge Display --}}
                        <template x-if="selectedPatch">
                            <div
                                class="absolute bottom-5 right-5 bg-black/85 border border-neutral-700 px-3 py-1.5 rounded text-[10px] font-bold text-white uppercase backdrop-blur shadow-lg">
                                <span x-text="'★ ' + selectedPatch"></span>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Right: Interactive Controls Form (6 cols) --}}
                <div class="lg:col-span-6 bg-neutral-950 p-8 rounded border border-neutral-800 space-y-6">
                    <div>
                        <h3 class="font-bold text-lg text-white" x-text="productName"></h3>
                        <p class="text-xs text-neutral-400">Sablon Polyflex PU Premium & Heat Press 160°C</p>
                    </div>

                    <div class="space-y-4 text-xs">
                        {{-- Input Nama Punggung --}}
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-neutral-300 mb-1">
                                Nama Punggung (Maks. 12 Karakter)
                                <span class="text-cyan-400 font-semibold">(+Rp
                                    {{ number_format($studioNamesetFee, 0, ',', '.') }})</span>
                            </label>
                            <input type="text" x-model="customName" maxlength="12" placeholder="CONTOH: RONALDO"
                                class="w-full bg-neutral-900 border border-neutral-700 p-3 rounded text-sm text-white uppercase font-jersey tracking-widest focus:outline-none focus:border-cyan-500">
                        </div>

                        {{-- Input Nomor Punggung --}}
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-neutral-300 mb-1">
                                Nomor Punggung (0 - 99)
                            </label>
                            <input type="text" x-model="customNumber" maxlength="2" placeholder="7"
                                class="w-full bg-neutral-900 border border-neutral-700 p-3 rounded text-xl text-white font-jersey tracking-widest focus:outline-none focus:border-cyan-500">
                        </div>

                        {{-- Pilih Ukuran --}}
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-neutral-300 mb-1.5">Pilih Ukuran
                                Jersey</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach (['S', 'M', 'L', 'XL', 'XXL'] as $sz)
                                    <button type="button" @click="selectedSize = '{{ $sz }}'"
                                        :class="selectedSize === '{{ $sz }}' ?
                                            'bg-cyan-500 text-neutral-950 font-bold border-cyan-400 shadow-md' :
                                            'bg-neutral-900 text-neutral-300 border border-neutral-800 hover:border-neutral-600'"
                                        class="w-12 py-2 text-center font-semibold rounded text-xs transition">
                                        {{ $sz }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Pilihan Patch Lengan --}}
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-neutral-300 mb-1">
                                Pilihan Patch Turnamen
                                <span class="text-cyan-400 font-semibold">(+Rp
                                    {{ number_format($studioPatchFee, 0, ',', '.') }})</span>
                            </label>
                            <select x-model="selectedPatch"
                                class="w-full bg-neutral-900 border border-neutral-700 p-2.5 rounded text-xs text-white uppercase focus:outline-none focus:border-cyan-500">
                                <option value="">Tanpa Patch (+Rp 0)</option>
                                <option value="UCL Starball + Foundation">UEFA Champions League Starball + Foundation
                                </option>
                                <option value="Premier League Official Gold">Premier League Official Sleeve Badge</option>
                                <option value="FIFA World Cup Qualifiers">FIFA World Cup Qualifiers Badge</option>
                            </select>
                        </div>
                    </div>

                    {{-- Price Bar & Action --}}
                    <div
                        class="border-t border-neutral-800 pt-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <div class="text-[11px] text-neutral-400 uppercase tracking-wider">Total Harga Spesifikasi:
                            </div>
                            <div class="font-display font-extrabold text-2xl text-cyan-400 mt-0.5"
                                x-text="'Rp ' + grandTotal.toLocaleString('id-ID')"></div>
                        </div>

                        @if ($studioProduct)
                            <a :href="'{{ route('shop.show', $studioProduct->slug) }}' +
                            '?custom_name=' + encodeURIComponent(customName) + ' &
                                custom_number =
                                ' + encodeURIComponent(customNumber) + ' &
                                size = ' + selectedSize + ' & patch = ' +
                            encodeURIComponent(selectedPatch)"
                                class="btn-curtain bg-cyan-500 hover:bg-cyan-400 text-neutral-950 font-bold px-6 py-3.5 border-none text-center">
                                <span>Beli Spesifikasi Ini &rarr;</span>
                            </a>
                        @else
                            <a href="{{ route('shop.index') }}"
                                class="btn-curtain bg-cyan-500 hover:bg-cyan-400 text-neutral-950 font-bold px-6 py-3.5 border-none">
                                <span>Beli di Katalog &rarr;</span>
                            </a>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </section>

@endsection
