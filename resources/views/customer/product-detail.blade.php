@extends('layouts.customer')

@section('title', $product->name . ' · NGIZAN APPAREL')
@section('meta_description', 'Beli ' . $product->name . ' dengan kustomisasi sablon nama & nomor punggung resmi dan patch turnamen di Ngizan Apparel.')

@section('content')

@php
    $frontImg = $product->thumbnail_front ? asset('storage/' . $product->thumbnail_front) : 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=800&q=80';
    $backImg  = $product->thumbnail_back  ? asset('storage/' . $product->thumbnail_back)  : 'https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&w=800&q=80';
    
    $initialVariant = $product->variants->where('stock', '>', 0)->first() ?? $product->variants->first();
    $defaultSize = request('size') ?? ($initialVariant?->size ?? 'M');
    $defaultCustomName = request('custom_name') ?? '';
    $defaultCustomNumber = request('custom_number') ?? '';
    $defaultPatch = request('patch') ?? '';

    $isPremium = auth()->check() && auth()->user()->isPremiumActive();
    $effectiveBasePrice = (int)$product->getFinalPrice(auth()->user());
    $reviewsCount = $product->reviews_count;
    $avgRating = $product->average_rating;
@endphp

<div class="py-8 bg-canvas" x-data="{
    activeTab: 'front', // 'front' or 'back'
    sizeModal: false,
    selectedVariantId: {{ $initialVariant?->id ?? 0 }},
    selectedSize: '{{ $defaultSize }}',
    selectedType: '{{ $initialVariant?->type ?? 'Fans Issue' }}',
    basePrice: {{ $effectiveBasePrice }},
    priceAdj: {{ (int)($initialVariant?->price_adjustment ?? 0) }},
    customName: '{{ $defaultCustomName }}',
    customNumber: '{{ $defaultCustomNumber }}',
    namesetPrice: {{ (int)$product->custom_nameset_price }},
    allowNameset: {{ $product->allow_custom_nameset ? 'true' : 'false' }},
    selectedPatch: '{{ $defaultPatch }}',
    patchPrice: {{ (int)$product->patch_price }},
    allowPatch: {{ $product->allow_patch ? 'true' : 'false' }},
    qty: 1,
    
    get hasNameset() {
        return this.allowNameset && (this.customName.trim() !== '' || this.customNumber.trim() !== '');
    },
    get customFee() {
        let fee = 0;
        if (this.hasNameset) fee += this.namesetPrice;
        if (this.allowPatch && this.selectedPatch) fee += this.patchPrice;
        return fee;
    },
    get unitPrice() {
        return this.basePrice + this.priceAdj;
    },
    get grandTotal() {
        return (this.unitPrice + this.customFee) * this.qty;
    },
    selectVariant(id, size, type, adj) {
        this.selectedVariantId = id;
        this.selectedSize = size;
        this.selectedType = type;
        this.priceAdj = adj;
    }
}">
    <div class="wrap">
        
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-xs text-mute mb-8">
            <a href="{{ route('home') }}" class="hover:text-ink">Beranda</a>
            <span>/</span>
            <a href="{{ route('shop.index') }}" class="hover:text-ink">Katalog</a>
            <span>/</span>
            <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}" class="hover:text-ink">{{ $product->category->name }}</a>
            <span>/</span>
            <span class="text-ink font-medium">{{ $product->name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-start">
            
            {{-- ===== 1. LEFT: DUAL POV HIGH-RES IMAGE & LIVE STUDIO PREVIEW (7 COLS) ===== --}}
            <div class="lg:col-span-7 space-y-4">
                {{-- View Toggle Tabs (Pills) --}}
                <div class="flex items-center gap-2">
                    <button type="button" @click="activeTab = 'front'"
                            :class="activeTab === 'front' ? 'bg-ink text-white' : 'bg-soft-cloud text-ink hover:bg-neutral-200'"
                            class="px-4 py-1.5 rounded-full text-xs font-medium transition">
                        Tampak Depan
                    </button>
                    <button type="button" @click="activeTab = 'back'"
                            :class="activeTab === 'back' ? 'bg-ink text-white' : 'bg-soft-cloud text-ink hover:bg-neutral-200'"
                            class="px-4 py-1.5 rounded-full text-xs font-medium transition flex items-center gap-1.5">
                        <span>Live Nameset Back POV</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-premium-gold animate-pulse"></span>
                    </button>
                </div>

                {{-- Image Display Stage on soft-cloud --}}
                <div class="relative bg-soft-cloud overflow-hidden aspect-square flex items-center justify-center border border-hairline-soft">
                    
                    {{-- Front View --}}
                    <div x-show="activeTab === 'front'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="w-full h-full">
                        <img src="{{ $frontImg }}" alt="{{ $product->name }} Tampak Depan" class="w-full h-full object-cover">
                    </div>

                    {{-- Back View with Live 2D Typography Layer --}}
                    <div x-show="activeTab === 'back'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="relative w-full h-full bg-soft-cloud">
                        <img src="{{ $backImg }}" alt="{{ $product->name }} Tampak Belakang" class="w-full h-full object-cover">
                        
                        <div class="nameset-layer">
                            <div class="nameset-text-name uppercase" x-text="customName || 'NAMA ANDA'"></div>
                            <div class="nameset-text-number" x-text="customNumber || '00'"></div>
                        </div>

                        {{-- Patch Indicator Badge --}}
                        <template x-if="selectedPatch">
                            <div class="absolute bottom-4 right-4 bg-ink text-white px-3 py-1 rounded-full text-[11px] font-medium uppercase shadow-md">
                                <span x-text="'★ ' + selectedPatch"></span>
                            </div>
                        </template>
                    </div>

                </div>

                <p class="text-xs text-mute text-center">
                    *Tampilan sablon pada preview adalah representasi proporsional dari hasil akhir heat press 160°C.
                </p>
            </div>

            {{-- ===== 2. RIGHT: PRODUCT DETAILS & SPECIFICATION FORM (5 COLS) ===== --}}
            <div class="lg:col-span-5 space-y-6">
                
                {{-- Title & Category & Rating --}}
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-medium uppercase tracking-widest text-mute">{{ $product->category->name }}</span>
                        <div class="flex items-center gap-1.5 text-xs">
                            @if($reviewsCount > 0)
                                <a href="#reviews-section" class="flex items-center gap-1 hover:underline">
                                    <span class="text-premium-gold font-medium">★ {{ number_format($avgRating, 1) }}</span>
                                    <span class="text-mute">({{ $reviewsCount }} ulasan)</span>
                                </a>
                            @else
                                <span class="text-mute text-xs">Belum ada ulasan</span>
                            @endif
                        </div>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-medium tracking-tight text-ink mt-1">
                        {{ $product->name }}
                    </h1>

                    <div class="mt-2 flex items-baseline gap-3">
                        <div class="text-2xl font-medium text-ink tabular-nums" x-text="'Rp ' + (unitPrice + customFee).toLocaleString('id-ID')">
                            Rp {{ number_format($effectiveBasePrice, 0, ',', '.') }}
                        </div>
                        @if($isPremium)
                            <span class="text-xs text-mute line-through tabular-nums">
                                {{ $product->formatted_price }}
                            </span>
                            <span class="px-2.5 py-0.5 bg-premium-gold text-ink text-[10px] font-medium rounded-full uppercase">
                                ★ Member 5% OFF
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div class="text-xs text-mute leading-relaxed border-t border-b border-hairline-soft py-4">
                    {{ $product->description ?? 'Jersey edisi resmi dengan material berpori mikro yang nyaman dipakai untuk olahraga maupun kasual fashion street style.' }}
                </div>

                {{-- Form Add to Cart --}}
                <form action="{{ route('customer.cart.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="product_variant_id" :value="selectedVariantId">
                    <input type="hidden" name="custom_name" :value="customName">
                    <input type="hidden" name="custom_number" :value="customNumber">
                    <input type="hidden" name="selected_patch" :value="selectedPatch">

                    {{-- 1. Matriks Pilihan Ukuran & Tipe --}}
                    <div>
                        <div class="flex justify-between items-center mb-2.5">
                            <label class="block font-medium text-xs text-ink">
                                Ukuran & Tipe: <span class="text-mute" x-text="selectedSize + ' (' + selectedType + ')'"></span>
                            </label>
                            <button type="button" @click="sizeModal = true" class="text-xs font-medium text-ink underline">
                                Panduan Ukuran
                            </button>
                        </div>

                        <div class="grid grid-cols-3 gap-2">
                            @foreach($product->variants as $variant)
                                <button type="button"
                                        @click="selectVariant({{ $variant->id }}, '{{ $variant->size }}', '{{ $variant->type }}', {{ (int)$variant->price_adjustment }})"
                                        @if($variant->stock <= 0) disabled @endif
                                        :class="selectedVariantId == {{ $variant->id }} ? 'bg-ink text-white border-ink' : '{{ $variant->stock <= 0 ? 'opacity-40 bg-soft-cloud text-stone line-through border-hairline-soft cursor-not-allowed' : 'bg-white text-ink border-hairline hover:border-ink' }}'"
                                        class="p-2.5 rounded-xl border text-center transition flex flex-col items-center justify-center">
                                    <span class="text-xs font-medium">{{ $variant->size }}</span>
                                    <span class="text-[10px] uppercase opacity-80">{{ $variant->type }}</span>
                                    <span class="text-[9px] mt-0.5 {{ $variant->stock <= 3 && $variant->stock > 0 ? 'text-sale font-medium' : 'text-mute' }}">
                                        @if($variant->stock > 0)
                                            Sisa {{ $variant->stock }}
                                        @else
                                            Habis
                                        @endif
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- 2. Kustomisasi Sablon Nama & Nomor Punggung --}}
                    @if($product->allow_custom_nameset)
                        <div class="bg-soft-cloud p-4 rounded-xl border border-hairline-soft space-y-3">
                            <div class="flex justify-between items-center">
                                <label class="font-medium text-xs text-ink flex items-center gap-1.5">
                                    <span>Kustom Sablon Nameset</span>
                                    <span class="text-xs text-mute">(+Rp {{ number_format($product->custom_nameset_price, 0, ',', '.') }})</span>
                                </label>
                                <span class="text-[10px] text-mute">Font Bebas Neue Resmi</span>
                            </div>

                            <div class="grid grid-cols-3 gap-3">
                                <div class="col-span-2">
                                    <input type="text" x-model="customName" maxlength="12" placeholder="NAMA (MAKS 12)" 
                                           @input="if(activeTab !== 'back') activeTab = 'back'"
                                           class="w-full bg-white border border-hairline px-3 py-2 rounded-full text-xs uppercase font-jersey tracking-widest focus:outline-none focus:border-ink">
                                </div>
                                <div>
                                    <input type="text" x-model="customNumber" maxlength="2" placeholder="NO" 
                                           @input="if(activeTab !== 'back') activeTab = 'back'"
                                           class="w-full bg-white border border-hairline px-3 py-2 rounded-full text-xs font-jersey tracking-widest text-center focus:outline-none focus:border-ink">
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- 3. Pilihan Patch Turnamen --}}
                    @if($product->allow_patch && !empty($product->available_patches))
                        <div>
                            <label class="block font-medium text-xs text-ink mb-1.5">
                                Patch Turnamen <span class="text-mute font-normal">(+Rp {{ number_format($product->patch_price, 0, ',', '.') }})</span>
                            </label>
                            <select x-model="selectedPatch" class="w-full bg-soft-cloud border border-hairline px-4 py-2 rounded-full text-xs text-ink focus:outline-none focus:border-ink">
                                <option value="">Tanpa Patch (+Rp 0)</option>
                                @foreach($product->available_patches as $patch)
                                    <option value="{{ $patch }}">{{ $patch }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- 4. Kuantitas (Qty) & Grand Total --}}
                    <div class="flex items-center justify-between border-t border-hairline-soft pt-4">
                        <div>
                            <span class="text-xs text-mute block font-medium">Kuantitas</span>
                            <div class="flex items-center border border-hairline rounded-full mt-1 overflow-hidden">
                                <button type="button" @click="if(qty > 1) qty--" class="px-3 py-1 bg-soft-cloud hover:bg-neutral-200 text-ink font-medium">-</button>
                                <input type="number" name="quantity" x-model="qty" min="1" max="10" class="w-10 text-center p-1 border-none text-xs font-medium text-ink bg-transparent" readonly>
                                <button type="button" @click="qty++" class="px-3 py-1 bg-soft-cloud hover:bg-neutral-200 text-ink font-medium">+</button>
                            </div>
                        </div>

                        <div class="text-right">
                            <span class="text-xs text-mute font-medium">Subtotal Item</span>
                            <div class="text-2xl font-medium text-ink mt-0.5 tabular-nums" x-text="'Rp ' + grandTotal.toLocaleString('id-ID')"></div>
                        </div>
                    </div>

                    {{-- Submit Button (Nike Pill) --}}
                    <button type="submit" class="btn-primary w-full py-4 text-sm font-medium rounded-full text-center">
                        Masukkan ke Tas Belanja &rarr;
                    </button>
                </form>

            </div>

        </div>

        {{-- ===== REVIEWS & RATINGS SECTION ===== --}}
        <div id="reviews-section" class="mt-16 pt-10 border-t border-hairline-soft">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                <div>
                    <h2 class="text-xl font-medium tracking-tight text-ink flex items-center gap-2">
                        <span>Ulasan & Penilaian Pembeli</span>
                        <span class="text-sm font-normal text-mute">({{ $reviewsCount }})</span>
                    </h2>
                    <p class="text-xs text-mute mt-0.5">Seluruh ulasan berasal dari Verified Buyer yang telah menyelesaikan pesanan.</p>
                </div>
                @if($reviewsCount > 0)
                    <div class="flex items-center gap-2 bg-soft-cloud border border-hairline px-4 py-2 rounded-full">
                        <span class="text-xl font-medium text-premium-gold">★ {{ number_format($avgRating, 1) }}</span>
                        <span class="text-xs text-mute">dari 5.0 bintang</span>
                    </div>
                @endif
            </div>

            @if($product->reviews->isEmpty())
                <div class="bg-soft-cloud border border-hairline-soft rounded-2xl p-8 text-center text-xs text-mute">
                    <p class="text-sm font-medium text-ink mb-1">Belum ada ulasan untuk jersey ini.</p>
                    <p>Jadilah yang pertama memesan dan memberikan ulasan setelah jersey tiba di tangan Anda!</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($product->reviews()->with('user')->latest()->get() as $review)
                        <div class="bg-white border border-hairline-soft rounded-2xl p-5 space-y-2">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="font-medium text-xs text-ink flex items-center gap-2">
                                        <span>{{ Str::mask($review->user->name ?? 'Pelanggan', '*', 3, 4) }}</span>
                                        <span class="text-[10px] px-2 py-0.5 bg-soft-cloud text-ink border border-hairline rounded-full font-medium">
                                            ✓ Verified Buyer
                                        </span>
                                    </div>
                                    <div class="text-[10px] text-mute mt-0.5">
                                        {{ $review->created_at->format('d M Y') }}
                                    </div>
                                </div>
                                <div class="text-premium-gold font-medium text-sm">
                                    {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                                </div>
                            </div>
                            <p class="text-xs text-mute leading-relaxed">
                                "{{ $review->comment }}"
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ===== 3. RELATED PRODUCTS ===== --}}
        @if($relatedProducts->count() > 0)
            <div class="mt-20 pt-12 border-t border-hairline-soft">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="text-xl font-medium tracking-tight text-ink">
                        Koleksi Jersey Terkait
                    </h2>
                    <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}" class="text-xs font-medium text-ink underline hover:text-mute">
                        Lihat Kategori Ini &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
                    @foreach($relatedProducts as $related)
                        <x-product-card :product="$related" />
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    {{-- ===== SIZE CHART MODAL ===== --}}
    <div x-show="sizeModal" x-cloak class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div @click.away="sizeModal = false" class="bg-white border border-hairline w-full max-w-xl rounded-2xl p-6 space-y-4 shadow-2xl">
            <div class="flex justify-between items-center border-b border-hairline-soft pb-3">
                <h3 class="font-medium text-base text-ink">Panduan Ukuran (Size Chart)</h3>
                <button @click="sizeModal = false" class="text-mute hover:text-ink text-xl font-bold">&times;</button>
            </div>

            <p class="text-xs text-mute">Toleransi ukuran jahitan & material elastis: &plusmn; 1-2 cm.</p>

            <table class="w-full text-xs text-left border border-hairline-soft">
                <thead class="bg-soft-cloud border-b border-hairline-soft font-medium text-ink">
                    <tr>
                        <th class="p-2.5">Ukuran</th>
                        <th class="p-2.5">Lebar Dada (cm)</th>
                        <th class="p-2.5">Panjang Baju (cm)</th>
                        <th class="p-2.5">Rekomendasi TB / BB</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-hairline-soft">
                    <tr><td class="p-2.5 font-medium">S</td><td class="p-2.5 text-mute">48 cm</td><td class="p-2.5 text-mute">68 cm</td><td class="p-2.5 text-mute">160-168 cm / 50-60 kg</td></tr>
                    <tr><td class="p-2.5 font-medium">M</td><td class="p-2.5 text-mute">50 cm</td><td class="p-2.5 text-mute">70 cm</td><td class="p-2.5 text-mute">168-175 cm / 60-70 kg</td></tr>
                    <tr><td class="p-2.5 font-medium">L</td><td class="p-2.5 text-mute">52 cm</td><td class="p-2.5 text-mute">72 cm</td><td class="p-2.5 text-mute">172-180 cm / 70-80 kg</td></tr>
                    <tr><td class="p-2.5 font-medium">XL</td><td class="p-2.5 text-mute">54 cm</td><td class="p-2.5 text-mute">74 cm</td><td class="p-2.5 text-mute">178-185 cm / 80-90 kg</td></tr>
                    <tr><td class="p-2.5 font-medium">XXL</td><td class="p-2.5 text-mute">56 cm</td><td class="p-2.5 text-mute">76 cm</td><td class="p-2.5 text-mute">180-190 cm / 90-100 kg</td></tr>
                    <tr><td class="p-2.5 font-medium">3XL</td><td class="p-2.5 text-mute">58 cm</td><td class="p-2.5 text-mute">78 cm</td><td class="p-2.5 text-mute">> 185 cm / > 100 kg</td></tr>
                </tbody>
            </table>

            <div class="text-right pt-2">
                <button type="button" @click="sizeModal = false" class="btn-secondary py-2 px-5 text-xs rounded-full">Tutup</button>
            </div>
        </div>
    </div>

</div>

@endsection
