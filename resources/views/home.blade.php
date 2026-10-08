@extends('layouts.customer')

@section('title', 'NGIZAN APPAREL · Official Football Kits & Archive Store')
@section('meta_description',
    'Ngizan Apparel - Toko jersey sepak bola autentik, edisi player issue, tim nasional, dan
    arsip retro terkurasi.')

@section('content')

    {{-- ===== 1. HERO HEADER (FULLSCREEN MINIMALIST EDITORIAL) ===== --}}
    @php
        $heroImage =
            $heroBanner?->image_url ??
            'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&w=2000&q=85';
    @endphp

    <header id="hero-section"
        class="relative w-full h-[65vh] sm:h-[85vh] lg:h-[125vh] min-h-[440px] sm:min-h-[550px] bg-neutral-950 overflow-hidden">
        {{-- Full Screen Pure Hero Image --}}
        <img src="{{ $heroImage }}" alt="Ngizan Apparel Hero Campaign"
            class="w-full h-full object-cover object-center select-none pointer-events-none">
    </header>

    {{-- ===== 2. TOP CATEGORIES (EXPANDED LARGE EDITORIAL CARDS SESUAI REFERENSI GAMBAR) ===== --}}
    <section class="wrap pt-10 pb-5 sm:pt-16 sm:pb-8 relative" x-data="{
        canScrollLeft: false,
        canScrollRight: true,
        updateScrollState() {
            const el = this.$refs.categoryScroll;
            this.canScrollLeft = el.scrollLeft > 10;
            this.canScrollRight = el.scrollLeft < (el.scrollWidth - el.clientWidth - 10);
        },
        scroll(direction) {
            const container = this.$refs.categoryScroll;
            const scrollAmount = container.clientWidth * 0.8;
            container.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
            setTimeout(() => this.updateScrollState(), 350);
        }
    }" x-init="setTimeout(() => updateScrollState(), 200)">

        {{-- Section Header --}}
        <div class="flex items-end justify-between mb-4 sm:mb-6 pb-2.5 sm:pb-3 border-b border-gray-200">
            <div>
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.2em] text-neutral-400 block mb-1">
                    Official Archive
                </span>
                <h2 class="text-lg sm:text-2xl md:text-3xl font-extrabold tracking-tight text-ink uppercase">
                    Kategori Pilihan
                </h2>
            </div>

            {{-- Top Navigation Controls (Mobile/Desktop Header) --}}
            <div class="flex items-center gap-1.5 sm:gap-2">
                <button type="button" @click="scroll(-1)"
                    class="w-8 h-8 sm:w-9 sm:h-9 rounded-full border border-hairline hover:border-ink flex items-center justify-center text-ink transition hover:bg-soft-cloud focus:outline-none"
                    aria-label="Geser Kategori ke Kiri">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button type="button" @click="scroll(1)"
                    class="w-8 h-8 sm:w-9 sm:h-9 rounded-full border border-hairline hover:border-ink flex items-center justify-center text-ink transition hover:bg-soft-cloud focus:outline-none"
                    aria-label="Geser Kategori ke Kanan">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Card Rail Container with Floating Slider Buttons --}}
        <div class="relative group">
            {{-- Floating Prev Arrow --}}
            <button type="button" @click="scroll(-1)" x-show="canScrollLeft" x-transition
                class="hidden md:flex absolute -left-3 top-1/2 -translate-y-1/2 z-20 w-11 h-11 bg-white text-ink rounded-full shadow-xl border border-gray-100 items-center justify-center hover:scale-105 active:scale-95 transition focus:outline-none cursor-pointer"
                aria-label="Previous Categories">
                <svg class="w-5 h-5 text-neutral-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
            </button>

            {{-- Large 1-Row Horizontally Scrollable Category Cards --}}
            <div x-ref="categoryScroll" @scroll.debounce.50ms="updateScrollState()"
                class="flex gap-3 sm:gap-4 overflow-x-auto pb-1 hide-scroll scroll-smooth snap-x snap-mandatory">
                @forelse($categories as $cat)
                    <a href="{{ route('shop.index', ['category' => $cat->slug]) }}"
                        class="relative w-[72vw] sm:w-[48vw] md:w-[calc(33.333%-11px)] min-w-[240px] sm:min-w-[320px] md:min-w-[360px] h-[340px] sm:h-[440px] md:h-[500px] lg:h-[540px] bg-neutral-900 rounded-2xl overflow-hidden group cursor-pointer shrink-0 snap-start block select-none">

                        {{-- Category Image with Smooth Zoom on Hover --}}
                        <img src="{{ $cat->image_url }}" alt="{{ $cat->name }}"
                            class="w-full h-full object-cover object-center transition-transform duration-700 ease-out group-hover:scale-105">

                        {{-- Subtle Bottom Shadow Overlay for Readability --}}
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-70 group-hover:opacity-85 transition-opacity">
                        </div>

                        {{-- Rounded Dark Pill Badge (Sesuai Screenshot Referensi) --}}
                        <div class="absolute bottom-4 left-4 sm:bottom-5 sm:left-5 z-10">
                            <span
                                class="bg-neutral-900/90 hover:bg-black text-white text-xs sm:text-sm font-semibold px-3.5 sm:px-5 py-2 sm:py-2.5 rounded-xl sm:rounded-2xl backdrop-blur-md shadow-lg inline-block tracking-normal transition">
                                {{ $cat->name }}
                            </span>
                        </div>
                    </a>
                @empty
                    <div class="w-full text-center py-12 text-mute text-sm bg-soft-cloud rounded-xl">
                        Belum ada kategori yang ditambahkan.
                    </div>
                @endforelse
            </div>

            {{-- Floating Next Arrow --}}
            <button type="button" @click="scroll(1)" x-show="canScrollRight" x-transition
                class="hidden md:flex absolute -right-3 top-1/2 -translate-y-1/2 z-20 w-11 h-11 bg-white text-ink rounded-full shadow-xl border border-gray-100 items-center justify-center hover:scale-105 active:scale-95 transition focus:outline-none cursor-pointer"
                aria-label="Next Categories">
                <svg class="w-5 h-5 text-neutral-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </section>

    {{-- ===== 3. NEW ARRIVALS (CONSISTENT INTER-SECTION RHYTHM) ===== --}}
    <section class="wrap pt-5 pb-5 sm:pt-8 sm:pb-8" x-data="{
        scroll(direction) {
            const container = this.$refs.newArrivalsScroll;
            const scrollAmount = container.clientWidth * 0.75;
            container.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
        }
    }">

        {{-- Section Header with Interactive Prev/Next Navigation Controls --}}
        <div class="flex items-end justify-between mb-4 sm:mb-6 pb-2.5 sm:pb-3 border-b border-gray-200">
            <div>
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.2em] text-neutral-400 block mb-1">
                    Fresh Releases
                </span>
                <h2 class="text-lg sm:text-2xl md:text-3xl font-extrabold tracking-tight text-ink uppercase">
                    Jersey Ngizan
                </h2>
            </div>

            <div class="flex items-center gap-1.5 sm:gap-2">
                <button type="button" @click="scroll(-1)"
                    class="w-8 h-8 sm:w-9 sm:h-9 rounded-full border border-hairline hover:border-ink flex items-center justify-center text-ink transition hover:bg-soft-cloud focus:outline-none"
                    aria-label="Geser ke Kiri">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button type="button" @click="scroll(1)"
                    class="w-8 h-8 sm:w-9 sm:h-9 rounded-full border border-hairline hover:border-ink flex items-center justify-center text-ink transition hover:bg-soft-cloud focus:outline-none"
                    aria-label="Geser ke Kanan">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Dynamic Horizontally Scrollable Rail (Responsive Card Widths) --}}
        <div x-ref="newArrivalsScroll"
            class="flex gap-3 sm:gap-4 overflow-x-auto pb-1 hide-scroll scroll-smooth snap-x snap-mandatory">
            @forelse($featuredProducts as $product)
                <div class="w-[170px] sm:w-[220px] md:w-[260px] lg:w-[280px] shrink-0 snap-start">
                    <x-product-card :product="$product" />
                </div>
            @empty
                <div class="w-full text-center py-12 text-mute text-sm">
                    Belum ada produk rilis terbaru.
                </div>
            @endforelse
        </div>

        {{-- View All CTA Button --}}
        <div class="mt-5 sm:mt-6 flex justify-center">
            <a href="{{ route('shop.index') }}"
                class="bg-black text-white px-6 sm:px-8 py-2 sm:py-2.5 text-xs sm:text-sm font-bold tracking-widest uppercase hover:bg-neutral-800 transition-colors rounded-full shadow-sm">
                View All
            </a>
        </div>
    </section>

    {{-- ===== 4. WIDE PROMO BANNER (RESPONSIVE FULL WIDTH) ===== --}}
    @if ($promoBanner && $promoBanner->image_url)
        <section class="wrap pt-5 pb-5 sm:pt-8 sm:pb-8 overflow-hidden">
            <a href="{{ route('shop.index') }}"
                class="block relative w-full aspect-[16/9] sm:aspect-[1200/350] bg-neutral-900 rounded-xl sm:rounded-2xl overflow-hidden cursor-pointer group shadow-sm">
                <img src="{{ $promoBanner->image_url }}" alt="Promo Banner"
                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
            </a>
        </section>
    @endif

    {{-- ===== 5. KOLEKSI TIMNAS (CONSISTENT INTER-SECTION RHYTHM) ===== --}}
    @php
        $timnasList = $featuredProducts->filter(function ($p) {
            $name = strtolower($p->name);
            $cat = strtolower($p->category->slug ?? '');
            return str_contains($name, 'timnas') || str_contains($name, 'garuda') || str_contains($cat, 'tim-nasional');
        });

        // Fallback jika belum ada produk timnas khusus di DB, tampilkan subset produk
        $displayTimnas = $timnasList->isNotEmpty() ? $timnasList : $featuredProducts->take(6);
    @endphp

    <section class="wrap pt-5 pb-5 sm:pt-8 sm:pb-8" x-data="{
        scroll(direction) {
            const container = this.$refs.timnasScroll;
            const scrollAmount = container.clientWidth * 0.75;
            container.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
        }
    }">

        {{-- Section Header with Interactive Prev/Next Navigation Controls --}}
        <div class="flex items-end justify-between mb-4 sm:mb-6 pb-2.5 sm:pb-3 border-b border-gray-200">
            <div>
                <span class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.2em] text-neutral-400 block mb-1">
                    Produk Selain Jersey
                </span>
                <h2 class="text-lg sm:text-2xl md:text-3xl font-extrabold tracking-tight text-ink uppercase">
                    Katalog Ngizan
                </h2>
            </div>

            <div class="flex items-center gap-1.5 sm:gap-2">
                <button type="button" @click="scroll(-1)"
                    class="w-8 h-8 sm:w-9 sm:h-9 rounded-full border border-hairline hover:border-ink flex items-center justify-center text-ink transition hover:bg-soft-cloud focus:outline-none"
                    aria-label="Geser ke Kiri">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button type="button" @click="scroll(1)"
                    class="w-8 h-8 sm:w-9 sm:h-9 rounded-full border border-hairline hover:border-ink flex items-center justify-center text-ink transition hover:bg-soft-cloud focus:outline-none"
                    aria-label="Geser ke Kanan">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Dynamic Horizontally Scrollable Rail --}}
        <div x-ref="timnasScroll"
            class="flex gap-3 sm:gap-4 overflow-x-auto pb-1 hide-scroll scroll-smooth snap-x snap-mandatory">
            @forelse($displayTimnas as $product)
                <div class="w-[170px] sm:w-[220px] md:w-[260px] lg:w-[280px] shrink-0 snap-start">
                    <x-product-card :product="$product" />
                </div>
            @empty
                <div class="w-full text-center py-12 text-mute text-sm">
                    Belum ada produk
                </div>
            @endforelse
        </div>

        {{-- View All CTA Button --}}
        <div class="mt-5 sm:mt-6 flex justify-center">
            <a href="{{ route('shop.index', ['category' => 'tim-nasional']) }}"
                class="bg-black text-white px-6 sm:px-8 py-2 sm:py-2.5 text-xs sm:text-sm font-bold tracking-widest uppercase hover:bg-neutral-800 transition-colors rounded-full shadow-sm">
                View All
            </a>
        </div>
    </section>

@endsection
