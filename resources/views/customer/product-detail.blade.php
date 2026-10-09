@extends('layouts.customer')

@section('title', $product->name . ' · NGIZAN APPAREL')
@section('meta_description',
    'Beli ' .
    $product->name .
    ' edisi resmi autentik dengan material berkualitas di Ngizan
    Apparel.')

@section('content')

    @php
        $frontImg = $product->thumbnail_front
            ? asset('storage/' . $product->thumbnail_front)
            : 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=800&q=80';
        $backImg = $product->thumbnail_back
            ? asset('storage/' . $product->thumbnail_back)
            : 'https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&w=800&q=80';

        // Gallery images list
        $galleryList = [];
        $galleryList[] = ['type' => 'front', 'url' => $frontImg, 'label' => 'Tampak Depan'];
        $galleryList[] = ['type' => 'back', 'url' => $backImg, 'label' => 'Tampak Belakang'];

        if (!empty($product->gallery_images) && is_array($product->gallery_images)) {
            foreach ($product->gallery_images as $idx => $gImg) {
                $galleryList[] = [
                    'type' => 'gallery',
                    'url' => asset('storage/' . $gImg),
                    'label' => 'Detail ' . ($idx + 1),
                ];
            }
        }

        $initialVariant = $product->variants->where('stock', '>', 0)->first() ?? $product->variants->first();
        $defaultSize = request('size') ?? ($initialVariant?->size ?? 'M');

        $isPremium = auth()->check() && auth()->user()->isPremiumActive();
        $effectiveBasePrice = (int) $product->getFinalPrice(auth()->user());

        // Real dynamic reviews calculations
        $reviews = $product->reviews;
        $reviewsCount = $reviews->count();
        $avgRating = $reviewsCount > 0 ? round((float) $reviews->avg('rating'), 1) : 0.0;

        // Star counts distribution (5, 4, 3, 2, 1)
        $starDistribution = [
            5 => $reviews->where('rating', 5)->count(),
            4 => $reviews->where('rating', 4)->count(),
            3 => $reviews->where('rating', 3)->count(),
            2 => $reviews->where('rating', 2)->count(),
            1 => $reviews->where('rating', 1)->count(),
        ];
    @endphp

    <div class="bg-canvas min-h-screen py-6 sm:py-10" x-data="{
        currentSlide: 0,
        totalSlides: {{ count($galleryList) }},
        touchStartX: 0,
        touchEndX: 0,
        sizeModal: false,
        selectedVariantId: {{ $initialVariant?->id ?? 0 }},
        selectedSize: '{{ $defaultSize }}',
        selectedType: '{{ $initialVariant?->type ?? 'Fans Issue' }}',
        selectedStock: {{ (int) ($initialVariant?->stock ?? 0) }},
        isPremium: {{ $isPremium ? 'true' : 'false' }},
        basePrice: {{ (int) $product->base_price }},
        priceAdj: {{ (int) ($initialVariant?->price_adjustment ?? 0) }},
        qty: 1,
    
        nextSlide() {
            this.currentSlide = (this.currentSlide + 1) % this.totalSlides;
        },
        prevSlide() {
            this.currentSlide = (this.currentSlide - 1 + this.totalSlides) % this.totalSlides;
        },
        goToSlide(index) {
            this.currentSlide = index;
        },
        handleTouchStart(e) {
            this.touchStartX = e.changedTouches[0].screenX;
        },
        handleTouchEnd(e) {
            this.touchEndX = e.changedTouches[0].screenX;
            if (this.touchStartX - this.touchEndX > 50) {
                this.nextSlide();
            } else if (this.touchEndX - this.touchStartX > 50) {
                this.prevSlide();
            }
        },
        get originalUnitPrice() {
            return this.basePrice + this.priceAdj;
        },
        get unitPrice() {
            const raw = this.originalUnitPrice;
            return this.isPremium ? Math.round(raw * 0.95) : raw;
        },
        get grandTotal() {
            return this.unitPrice * this.qty;
        },
        selectVariant(id, size, type, adj, stock) {
            this.selectedVariantId = id;
            this.selectedSize = size;
            this.selectedType = type;
            this.priceAdj = adj;
            this.selectedStock = stock;
            if (this.qty > stock) {
                this.qty = Math.max(1, stock);
            }
        }
    }">
        <div class="wrap">

            {{-- Breadcrumb Trail --}}
            <nav
                class="flex items-center gap-2 text-xs text-mute mb-6 sm:mb-8 overflow-x-auto whitespace-nowrap pb-1 hide-scroll">
                <a href="{{ route('home') }}" class="hover:text-ink transition">Beranda</a>
                <span>/</span>
                <a href="{{ route('shop.index') }}" class="hover:text-ink transition">Katalog</a>
                <span>/</span>
                <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}"
                    class="hover:text-ink transition">{{ $product->category->name }}</a>
                <span>/</span>
                <span class="text-ink font-semibold truncate">{{ $product->name }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-14 items-start">

                {{-- ===== 1. LEFT: SWIPEABLE GALLERY (7 COLS) ===== --}}
                <div class="lg:col-span-7 space-y-4 select-none">

                    {{-- View Toggle Tabs (Pills) --}}
                    <div class="flex items-center gap-2 overflow-x-auto pb-1 hide-scroll">
                        @if (count($galleryList) > 2)
                            @foreach (array_slice($galleryList, 2) as $idx => $g)
                                <button type="button" @click="goToSlide({{ $idx + 2 }})"
                                    :class="currentSlide === {{ $idx + 2 }} ? 'bg-ink text-white shadow-xs' :
                                        'bg-soft-cloud text-ink hover:bg-neutral-200'"
                                    class="px-4 py-1.5 rounded-full text-xs font-semibold transition shrink-0">
                                    {{ $g['label'] }}
                                </button>
                            @endforeach
                        @endif
                    </div>

                    {{-- Interactive Swipeable Gallery Frame on Soft Cloud --}}
                    <div class="relative bg-soft-cloud rounded-2xl sm:rounded-3xl overflow-hidden aspect-square border border-hairline-soft shadow-xs group"
                        @touchstart="handleTouchStart($event)" @touchend="handleTouchEnd($event)">

                        {{-- SLIDE 0: Front View --}}
                        <div x-show="currentSlide === 0" x-transition:enter="transition ease-out duration-300 transform"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            class="w-full h-full flex items-center justify-center">
                            <img src="{{ $frontImg }}" alt="{{ $product->name }} Tampak Depan"
                                class="w-full h-full object-cover select-none">
                        </div>

                        {{-- SLIDE 1: Back View --}}
                        <div x-show="currentSlide === 1" x-cloak
                            x-transition:enter="transition ease-out duration-300 transform"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            class="relative w-full h-full flex items-center justify-center">
                            <img src="{{ $backImg }}" alt="{{ $product->name }} Tampak Belakang"
                                class="w-full h-full object-cover select-none">
                        </div>

                        {{-- Extra Gallery Slides --}}
                        @if (count($galleryList) > 2)
                            @foreach (array_slice($galleryList, 2) as $idx => $g)
                                <div x-show="currentSlide === {{ $idx + 2 }}" x-cloak
                                    x-transition:enter="transition ease-out duration-300 transform"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    class="w-full h-full flex items-center justify-center">
                                    <img src="{{ $g['url'] }}" alt="{{ $product->name }} {{ $g['label'] }}"
                                        class="w-full h-full object-cover select-none">
                                </div>
                            @endforeach
                        @endif

                        {{-- Floating Navigation Chevrons (< and >) --}}
                        <button type="button" @click="prevSlide()"
                            class="absolute left-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/90 hover:bg-white text-ink border border-hairline flex items-center justify-center shadow-md opacity-80 group-hover:opacity-100 transition-all hover:scale-105 active:scale-95"
                            aria-label="Foto Sebelumnya">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>

                        <button type="button" @click="nextSlide()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/90 hover:bg-white text-ink border border-hairline flex items-center justify-center shadow-md opacity-80 group-hover:opacity-100 transition-all hover:scale-105 active:scale-95"
                            aria-label="Foto Selanjutnya">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        {{-- Swipe Hint & Counter Badge --}}
                        <div
                            class="absolute bottom-3 left-1/2 -translate-x-1/2 bg-black/70 backdrop-blur-md text-white text-[11px] font-semibold px-3.5 py-1 rounded-full flex items-center gap-2 pointer-events-none">
                            <span x-text="(currentSlide + 1) + ' / ' + totalSlides"></span>
                            <span class="opacity-60">· Geser foto</span>
                        </div>
                    </div>

                    {{-- Horizontal Thumbnail Selector Strip --}}
                    <div class="grid grid-cols-4 sm:grid-cols-5 gap-2.5 pt-1">
                        @foreach ($galleryList as $idx => $item)
                            <button type="button" @click="goToSlide({{ $idx }})"
                                :class="currentSlide === {{ $idx }} ? 'ring-2 ring-ink border-ink' :
                                    'border-hairline-soft opacity-70 hover:opacity-100'"
                                class="aspect-square rounded-xl bg-soft-cloud overflow-hidden border transition-all relative">
                                <img src="{{ $item['url'] }}" alt="{{ $item['label'] }}"
                                    class="w-full h-full object-cover">
                            </button>
                        @endforeach
                    </div>

                    <p class="text-[11px] text-mute text-center pt-1">
                        *Foto resolusi tinggi autentik studio. Geser atau klik tombol panah untuk melihat sudut jersey
                        lainnya.
                    </p>
                </div>

                {{-- ===== 2. RIGHT: PRODUCT DETAILS & VARIANT SELECTION (5 COLS) ===== --}}
                <div class="lg:col-span-5 space-y-6">

                    {{-- Header: Category, Title & Real Dynamic Ratings --}}
                    <div class="border-b border-hairline-soft pb-5">
                        <div class="flex items-center justify-between gap-2 mb-1.5">
                            <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-mute">
                                {{ $product->category->name ?? 'Official Archive' }}
                            </span>

                            {{-- Real Dynamic Star Rating Badge --}}
                            <div class="flex items-center gap-1.5 text-xs">
                                @if ($reviewsCount > 0)
                                    <a href="#reviews-section"
                                        class="flex items-center gap-1 bg-soft-cloud border border-hairline px-2.5 py-1 rounded-full hover:border-ink transition">
                                        <span class="text-amber-500 font-bold">★</span>
                                        <span
                                            class="text-ink font-bold tabular-nums">{{ number_format($avgRating, 1) }}</span>
                                        <span
                                            class="text-mute font-medium text-[11px] tabular-nums">({{ $reviewsCount }})</span>
                                    </a>
                                @else
                                    <span
                                        class="text-mute text-xs bg-soft-cloud px-2.5 py-0.5 rounded-full border border-hairline-soft">
                                        Belum ada ulasan
                                    </span>
                                @endif
                            </div>
                        </div>

                        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-ink uppercase">
                            {{ $product->name }}
                        </h1>

                        {{-- Price Display --}}
                        <div class="mt-3 flex items-baseline gap-3 flex-wrap">
                            <div class="text-2xl sm:text-3xl font-extrabold text-ink tabular-nums tracking-tight"
                                x-text="'Rp ' + unitPrice.toLocaleString('id-ID')">
                                Rp {{ number_format($effectiveBasePrice, 0, ',', '.') }}
                            </div>
                            @if ($isPremium)
                                <span class="text-xs sm:text-sm text-mute line-through tabular-nums"
                                    x-text="'Rp ' + originalUnitPrice.toLocaleString('id-ID')">
                                    {{ $product->formatted_price }}
                                </span>
                                <span
                                    class="px-2.5 py-0.5 bg-premium-gold text-ink text-[11px] font-bold rounded-full uppercase shadow-xs">
                                    ★ Member 5% OFF
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Deskripsi Produk --}}
                    <div class="text-xs sm:text-sm text-mute leading-relaxed">
                        {{ $product->description ?? 'Jersey edisi resmi dengan material berpori mikro yang nyaman dipakai untuk olahraga maupun kasual fashion street style.' }}
                    </div>

                    {{-- Form Add to Cart --}}
                    <form action="{{ route('customer.cart.store') }}" method="POST" class="space-y-6">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="product_variant_id" :value="selectedVariantId">

                        {{-- 1. Pilihan Ukuran & Tipe (Simpel & Kecil) --}}
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <label class="block font-bold text-xs uppercase tracking-wider text-ink">
                                    Ukuran & Tipe: <span class="text-mute font-normal normal-case"
                                        x-text="selectedSize + ' (' + selectedType + ')'"></span>
                                </label>
                                <button type="button" @click="sizeModal = true"
                                    class="text-xs font-semibold text-ink underline hover:text-mute">
                                    Panduan Ukuran
                                </button>
                            </div>

                            {{-- Compact & Clean Variant Badges --}}
                            <div class="flex flex-wrap gap-2">
                                @foreach ($product->variants as $variant)
                                    <button type="button"
                                        @click="selectVariant({{ $variant->id }}, '{{ $variant->size }}', '{{ $variant->type }}', {{ (int) $variant->price_adjustment }}, {{ (int) $variant->stock }})"
                                        @if ($variant->stock <= 0) disabled @endif
                                        :class="selectedVariantId == {{ $variant->id }} ?
                                            'bg-ink text-white border-ink shadow-xs' :
                                            '{{ $variant->stock <= 0 ? 'opacity-40 bg-soft-cloud text-stone line-through border-hairline-soft cursor-not-allowed' : 'bg-soft-cloud text-ink border-hairline hover:border-ink hover:bg-neutral-200' }}'"
                                        class="px-3.5 py-2 rounded-xl border text-xs font-semibold transition inline-flex items-center gap-1.5 select-none">
                                        <span class="font-bold">{{ $variant->size }}</span>
                                        <span class="text-[10px] opacity-75 font-normal uppercase">·
                                            {{ $variant->type }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- 2. Kuantitas (Qty) dengan Validasi Max Stok & Keterangan Sisa Stok Disamping Counter --}}
                        <div class="flex items-center justify-between border-t border-hairline-soft pt-4 gap-4">
                            <div>
                                <span
                                    class="text-xs font-bold uppercase tracking-wider text-ink block mb-1.5">Kuantitas</span>

                                <div class="flex items-center gap-3">
                                    {{-- Counter Controls --}}
                                    <div
                                        class="flex items-center border border-hairline rounded-full overflow-hidden bg-white">
                                        <button type="button" @click="if(qty > 1) qty--" :disabled="qty <= 1"
                                            :class="qty <= 1 ? 'opacity-40 cursor-not-allowed' : 'hover:bg-neutral-200'"
                                            class="px-3.5 py-1.5 bg-soft-cloud text-ink font-bold transition select-none"
                                            aria-label="Kurangi Kuantitas">-</button>
                                        <input type="number" name="quantity" x-model.number="qty" min="1"
                                            :max="selectedStock"
                                            class="w-10 text-center p-1 border-none text-xs font-bold text-ink bg-transparent select-none focus:ring-0"
                                            readonly>
                                        <button type="button" @click="if(qty < selectedStock) qty++"
                                            :disabled="qty >= selectedStock || selectedStock <= 0"
                                            :class="(qty >= selectedStock || selectedStock <= 0) ?
                                            'opacity-40 cursor-not-allowed' : 'hover:bg-neutral-200'"
                                            class="px-3.5 py-1.5 bg-soft-cloud text-ink font-bold transition select-none"
                                            aria-label="Tambah Kuantitas">+</button>
                                    </div>

                                    {{-- Keterangan Sisa Stok di Samping Counter --}}
                                    <div class="text-xs">
                                        <template x-if="selectedStock > 0">
                                            <span
                                                :class="selectedStock <= 3 ? 'text-sale font-semibold' : 'text-mute font-medium'">
                                                Sisa: <strong class="text-ink font-bold tabular-nums"
                                                    x-text="selectedStock"></strong> pcs
                                                <template x-if="selectedStock <= 3">
                                                    <span class="text-[10px] text-sale font-bold block">(Segera
                                                        habis!)</span>
                                                </template>
                                            </span>
                                        </template>
                                        <template x-if="selectedStock <= 0">
                                            <span class="text-sale font-bold text-xs bg-sale/10 px-2.5 py-1 rounded-full">
                                                Stok Habis
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            {{-- Total Harga Subtotal --}}
                            <div class="text-right">
                                <span class="text-xs text-mute font-medium">Total Harga</span>
                                <div class="text-2xl font-extrabold text-ink tabular-nums mt-0.5"
                                    x-text="'Rp ' + grandTotal.toLocaleString('id-ID')"></div>
                            </div>
                        </div>

                        {{-- Submit Button (Nike Primary CTA Pill) --}}
                        <button type="submit" :disabled="selectedStock <= 0"
                            :class="selectedStock <= 0 ? 'opacity-50 cursor-not-allowed bg-neutral-400' :
                                'bg-ink hover:bg-black'"
                            class="w-full text-white py-4 text-xs sm:text-sm font-bold uppercase tracking-widest rounded-full transition shadow-md flex items-center justify-center gap-2">
                            <span x-text="selectedStock > 0 ? 'Masukkan ke Tas Belanja' : 'Stok Jersey Habis'"></span>
                            <span x-show="selectedStock > 0">&rarr;</span>
                        </button>

                    </form>

                </div>

            </div>

            {{-- ===== 3. DYNAMIC REVIEWS & RATINGS SECTION ===== --}}
            <section id="reviews-section" class="mt-16 sm:mt-24 pt-10 border-t border-hairline-soft">

                {{-- Section Header --}}
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
                    <div>
                        <span
                            class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.2em] text-neutral-400 block mb-1">
                            Verified Buyer Feedbacks
                        </span>
                        <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-ink uppercase">
                            Ulasan & Penilaian Pembeli
                        </h2>
                        <p class="text-xs text-mute mt-1">
                            Semua ulasan asli dan terverifikasi dari pembeli yang telah menerima jersey ini.
                        </p>
                    </div>

                    {{-- Rating Summary Box --}}
                    @if ($reviewsCount > 0)
                        <div class="flex items-center gap-4 bg-soft-cloud p-4 rounded-2xl border border-hairline">
                            <div class="text-center">
                                <div class="text-3xl font-extrabold text-ink tabular-nums">
                                    {{ number_format($avgRating, 1) }}
                                </div>
                                <div class="text-amber-500 text-xs">
                                    @for ($i = 1; $i <= 5; $i++)
                                        {{ $i <= round($avgRating) ? '★' : '☆' }}
                                    @endfor
                                </div>
                                <span class="text-[10px] text-mute font-medium">{{ $reviewsCount }} Ulasan</span>
                            </div>

                            {{-- Star Breakdown Progress Bars --}}
                            <div class="space-y-1 text-[11px] border-l border-hairline-soft pl-4 min-w-[150px]">
                                @foreach ([5, 4, 3, 2, 1] as $star)
                                    @php
                                        $count = $starDistribution[$star];
                                        $pct = $reviewsCount > 0 ? round(($count / $reviewsCount) * 100) : 0;
                                    @endphp
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-mute font-mono w-4">{{ $star }}★</span>
                                        <div class="flex-1 h-1.5 bg-neutral-200 rounded-full overflow-hidden">
                                            <div class="h-full bg-ink rounded-full" style="width: {{ $pct }}%">
                                            </div>
                                        </div>
                                        <span
                                            class="text-[10px] text-mute font-mono w-4 text-right">{{ $count }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Reviews List / Empty State --}}
                @if ($reviews->isEmpty())
                    <div class="bg-soft-cloud border border-hairline rounded-2xl p-10 text-center space-y-2">
                        <div
                            class="w-12 h-12 bg-white rounded-full flex items-center justify-center mx-auto text-amber-500 shadow-xs">
                            ★
                        </div>
                        <h3 class="font-bold text-sm text-ink uppercase tracking-wide">Belum Ada Ulasan untuk Jersey Ini
                        </h3>
                        <p class="text-xs text-mute max-w-sm mx-auto">
                            Jadilah yang pertama memesan jersey ini dan berikan ulasan bintang Anda setelah paket tiba!
                        </p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($reviews as $review)
                            <div
                                class="bg-white border border-hairline rounded-2xl p-5 space-y-3 shadow-xs hover:border-neutral-400 transition">
                                <div class="flex justify-between items-start gap-2">
                                    <div>
                                        <div class="font-bold text-xs text-ink flex items-center gap-2">
                                            <span>{{ Str::mask($review->user->name ?? 'Pelanggan Ngizan', '*', 3, 4) }}</span>
                                            <span
                                                class="text-[10px] px-2 py-0.5 bg-soft-cloud text-ink border border-hairline rounded-full font-bold">
                                                ✓ Verified Buyer
                                            </span>
                                        </div>
                                        <div class="text-[10px] text-mute mt-0.5">
                                            {{ $review->created_at ? $review->created_at->format('d M Y') : 'Baru saja' }}
                                        </div>
                                    </div>

                                    {{-- Star Rating --}}
                                    <div class="text-amber-500 text-xs tracking-wider">
                                        @for ($s = 1; $s <= 5; $s++)
                                            {{ $s <= $review->rating ? '★' : '☆' }}
                                        @endfor
                                    </div>
                                </div>

                                <p class="text-xs text-mute leading-relaxed italic">
                                    "{{ $review->comment }}"
                                </p>
                            </div>
                        @endforeach
                    </div>
                @endif

            </section>

            {{-- ===== 4. RELATED PRODUCTS ===== --}}
            @if ($relatedProducts->count() > 0)
                <section class="mt-20 sm:mt-24 pt-10 border-t border-hairline-soft">
                    <div class="flex items-center justify-between mb-6 sm:mb-8">
                        <div>
                            <span
                                class="text-[10px] sm:text-[11px] font-bold uppercase tracking-[0.2em] text-neutral-400 block mb-1">
                                More From {{ $product->category->name }}
                            </span>
                            <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight text-ink uppercase">
                                Koleksi Terkait
                            </h2>
                        </div>
                        <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}"
                            class="text-xs font-bold text-ink underline hover:text-mute uppercase tracking-wider">
                            Lihat Semua &rarr;
                        </a>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 md:gap-6">
                        @foreach ($relatedProducts as $related)
                            <x-product-card :product="$related" />
                        @endforeach
                    </div>
                </section>
            @endif

        </div>

        {{-- ===== DYNAMIC SIZE CHART MODAL ===== --}}
        @php
            $activeSizeChart =
                $product->sizeChart ??
                (\App\Models\SizeChart::where('is_default', true)->first() ?? \App\Models\SizeChart::first());
        @endphp

        <div x-show="sizeModal" x-cloak
            class="fixed inset-0 z-50 bg-black/70 backdrop-blur-xs flex items-center justify-center p-4 sm:p-6 overflow-y-auto">
            <div @click.away="sizeModal = false"
                class="bg-white border border-hairline w-full max-w-2xl rounded-3xl p-6 sm:p-8 space-y-5 shadow-2xl my-auto">

                {{-- Header --}}
                <div class="flex justify-between items-start border-b border-hairline-soft pb-4 gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span
                                class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-soft-cloud text-ink border border-hairline-soft">
                                {{ match ($activeSizeChart?->category_type) {
                                    'tops' => 'Atasan / Jersey',
                                    'bottoms' => 'Bawahan / Celana',
                                    'outerwear' => 'Jaket / Luaran',
                                    default => ($activeSizeChart?->category_type ?? 'Fitting Guide'),
                                } }}
                            </span>
                            <span class="text-[10px] text-mute uppercase font-bold tracking-widest">· Standar
                                Autentik</span>
                        </div>
                        <h3 class="font-extrabold text-lg sm:text-xl text-ink uppercase tracking-tight">
                            {{ $activeSizeChart?->name ?? 'Panduan Ukuran (Size Chart)' }}
                        </h3>
                    </div>
                    <button @click="sizeModal = false"
                        class="w-8 h-8 rounded-full bg-soft-cloud flex items-center justify-center text-ink hover:bg-neutral-200 text-lg transition font-bold shrink-0 cursor-pointer">&times;</button>
                </div>

                {{-- Deskripsi Petunjuk Ukur --}}
                <div
                    class="p-3.5 bg-soft-cloud rounded-2xl border border-hairline-soft text-xs text-mute flex items-start gap-2.5">
                    <span class="text-base shrink-0">📏</span>
                    <p class="leading-relaxed">
                        {{ $activeSizeChart?->description ?? 'Ukur pakaian dalam posisi terbentang rata di permukaan datar tanpa ditarik. Toleransi jahitan & material elastis: ± 1-2 cm.' }}
                    </p>
                </div>

                {{-- Dynamic Table --}}
                @if ($activeSizeChart && !empty($activeSizeChart->columns) && !empty($activeSizeChart->rows))
                    <div class="overflow-x-auto border border-hairline-soft rounded-2xl">
                        <table class="w-full text-xs text-left">
                            <thead
                                class="bg-soft-cloud border-b border-hairline-soft font-bold text-ink uppercase text-[10px] tracking-wider">
                                <tr>
                                    @foreach ($activeSizeChart->columns as $col)
                                        <th class="p-3 sm:p-3.5 whitespace-nowrap">{{ $col }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-hairline-soft">
                                @foreach ($activeSizeChart->rows as $r)
                                    <tr class="hover:bg-soft-cloud/40 transition">
                                        <td class="p-3 sm:p-3.5 font-bold text-ink whitespace-nowrap">
                                            <span
                                                class="px-2 py-0.5 bg-soft-cloud border border-hairline-soft rounded-md inline-block">
                                                {{ $r['size'] ?? '-' }}
                                            </span>
                                        </td>
                                        @for ($i = 1; $i < count($activeSizeChart->columns); $i++)
                                            <td class="p-3 sm:p-3.5 text-mute font-medium whitespace-nowrap">
                                                {{ $r['col' . $i] ?? '-' }}
                                            </td>
                                        @endfor
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-xs text-mute text-center py-6">Data tabel ukuran sedang disiapkan.</p>
                @endif

                {{-- Footer Tips & Close Button --}}
                <div
                    class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2 border-t border-hairline-soft">
                    <span class="text-[11px] text-mute flex items-center gap-1.5">
                        <span class="text-emerald-600 font-bold">✓</span>
                        <span>Jika ragu di antara dua ukuran, disarankan memilih <strong>1 size lebih besar</strong>.</span>
                    </span>

                    <button type="button" @click="sizeModal = false"
                        class="bg-ink text-white py-2.5 px-6 text-xs font-bold uppercase tracking-wider rounded-full hover:bg-black transition self-end sm:self-auto cursor-pointer shadow-xs">
                        Mengerti
                    </button>
                </div>

            </div>
        </div>

    </div>

@endsection
