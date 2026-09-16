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

<div class="py-10" x-data="{
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
        <nav class="flex items-center gap-2 text-xs text-ink-muted mb-8 uppercase tracking-wider">
            <a href="{{ route('home') }}" class="hover:text-ink">Beranda</a>
            <span>/</span>
            <a href="{{ route('shop.index') }}" class="hover:text-ink">Katalog</a>
            <span>/</span>
            <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}" class="hover:text-ink">{{ $product->category->name }}</a>
            <span>/</span>
            <span class="text-ink font-bold">{{ $product->name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">
            
            {{-- ===== 1. LEFT: DUAL POV HIGH-RES IMAGE & LIVE STUDIO PREVIEW (7 COLS) ===== --}}
            <div class="lg:col-span-7 space-y-4">
                {{-- View Toggle Tabs --}}
                <div class="flex items-center gap-3">
                    <button type="button" @click="activeTab = 'front'"
                            :class="activeTab === 'front' ? 'bg-ink text-canvas font-bold' : 'bg-canvas-card text-ink hover:bg-neutral-300 font-semibold'"
                            class="px-4 py-2 rounded text-xs uppercase tracking-wider transition">
                        📸 Tampak Depan
                    </button>
                    <button type="button" @click="activeTab = 'back'"
                            :class="activeTab === 'back' ? 'bg-cyan-600 text-white font-bold' : 'bg-canvas-card text-ink hover:bg-neutral-300 font-semibold'"
                            class="px-4 py-2 rounded text-xs uppercase tracking-wider transition flex items-center gap-1.5">
                        <span>⚡ Live Nameset Back POV</span>
                        <span class="w-2 h-2 rounded-full bg-lime-400 animate-pulse"></span>
                    </button>
                </div>

                {{-- Image Display Stage --}}
                <div class="relative bg-canvas-card rounded border border-black/10 overflow-hidden aspect-[4/5] flex items-center justify-center">
                    
                    {{-- Front View --}}
                    <div x-show="activeTab === 'front'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="w-full h-full">
                        <img src="{{ $frontImg }}" alt="{{ $product->name }} Tampak Depan" class="w-full h-full object-cover">
                    </div>

                    {{-- Back View with Live 2D Typography Layer --}}
                    <div x-show="activeTab === 'back'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="relative w-full h-full bg-[#181818]">
                        <img src="{{ $backImg }}" alt="{{ $product->name }} Tampak Belakang" class="w-full h-full object-cover opacity-90">
                        
                        <div class="nameset-layer">
                            <div class="nameset-text-name uppercase" x-text="customName || 'NAMA ANDA'"></div>
                            <div class="nameset-text-number" x-text="customNumber || '00'"></div>
                        </div>

                        {{-- Patch Indicator Badge --}}
                        <template x-if="selectedPatch">
                            <div class="absolute bottom-6 right-6 bg-black/85 border border-neutral-700 px-3 py-1.5 rounded text-[11px] font-bold text-white uppercase backdrop-blur shadow-xl">
                                <span x-text="'★ ' + selectedPatch"></span>
                            </div>
                        </template>
                    </div>

                </div>

                <p class="text-xs text-ink-muted italic text-center">
                    *Tampilan sablon pada preview adalah representasi visual proporsional dari hasil akhir heat press 160°C.
                </p>
            </div>

            {{-- ===== 2. RIGHT: PRODUCT DETAILS & SPECIFICATION FORM (5 COLS) ===== --}}
            <div class="lg:col-span-5 bg-white p-8 rounded-lg border border-black/10 shadow-sm space-y-6">
                
                {{-- Title & Category & Rating --}}
                <div>
                    <div class="flex items-center justify-between">
                        <span class="lbl text-cyan-600">{{ $product->category->name }}</span>
                        <div class="flex items-center gap-1.5 text-xs">
                            @if($reviewsCount > 0)
                                <a href="#reviews-section" class="flex items-center gap-1 hover:underline">
                                    <span class="text-amber-500 font-bold">★ {{ number_format($avgRating, 1) }}</span>
                                    <span class="text-ink-muted">({{ $reviewsCount }} ulasan)</span>
                                </a>
                            @else
                                <span class="text-neutral-400 text-xs">Belum ada ulasan</span>
                            @endif
                        </div>
                    </div>

                    <h1 class="font-display font-black text-2xl md:text-3xl text-ink uppercase tracking-tight mt-1">
                        {{ $product->name }}
                    </h1>

                    <div class="mt-2 flex items-baseline gap-3">
                        <div class="font-display font-extrabold text-2xl text-ink" x-text="'Rp ' + (unitPrice + customFee).toLocaleString('id-ID')">
                            Rp {{ number_format($effectiveBasePrice, 0, ',', '.') }}
                        </div>
                        @if($isPremium)
                            <span class="text-xs text-neutral-400 line-through">
                                {{ $product->formatted_price }}
                            </span>
                            <span class="px-2 py-0.5 bg-amber-100 border border-amber-300 text-amber-900 text-[10px] font-bold rounded">
                                ⭐ Member 5% OFF
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Deskripsi --}}
                <div class="text-xs text-ink-muted leading-relaxed border-t border-b border-black/10 py-4">
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
                        <div class="flex justify-between items-center mb-2">
                            <label class="block font-bold text-xs uppercase tracking-wider text-ink">
                                Ukuran & Tipe Rilis: <span class="text-cyan-600" x-text="selectedSize + ' (' + selectedType + ')'"></span>
                            </label>
                            <button type="button" @click="sizeModal = true" class="text-xs font-semibold text-ink-muted hover:text-ink underline">
                                📏 Size Chart
                            </button>
                        </div>

                        <div class="grid grid-cols-3 gap-2">
                            @foreach($product->variants as $variant)
                                <button type="button"
                                        @click="selectVariant({{ $variant->id }}, '{{ $variant->size }}', '{{ $variant->type }}', {{ (int)$variant->price_adjustment }})"
                                        @if($variant->stock <= 0) disabled @endif
                                        :class="selectedVariantId == {{ $variant->id }} ? 'bg-ink text-canvas font-bold border-ink shadow-md' : '{{ $variant->stock <= 0 ? 'opacity-40 bg-neutral-100 text-neutral-400 line-through border-black/5 cursor-not-allowed' : 'bg-canvas text-ink border-black/15 hover:border-black' }}'"
                                        class="p-2.5 rounded border text-center transition flex flex-col items-center justify-center">
                                    <span class="text-xs font-bold">{{ $variant->size }}</span>
                                    <span class="text-[9.5px] uppercase opacity-80">{{ $variant->type }}</span>
                                    <span class="text-[8.5px] font-semibold mt-0.5 {{ $variant->stock <= 3 && $variant->stock > 0 ? 'text-amber-500 font-bold' : '' }}">
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
                        <div class="bg-neutral-50 p-4 rounded border border-black/10 space-y-3">
                            <div class="flex justify-between items-center">
                                <label class="font-bold text-xs uppercase tracking-wider text-ink flex items-center gap-1.5">
                                    <span>⚡ Kustom Sablon Nameset</span>
                                    <span class="text-[10px] text-cyan-600">(+Rp {{ number_format($product->custom_nameset_price, 0, ',', '.') }})</span>
                                </label>
                                <span class="text-[10px] text-ink-muted">Font Bebas Neue Resmi</span>
                            </div>

                            <div class="grid grid-cols-3 gap-3">
                                <div class="col-span-2">
                                    <input type="text" x-model="customName" maxlength="12" placeholder="NAMA (MAKS 12)" 
                                           @input="if(activeTab !== 'back') activeTab = 'back'"
                                           class="w-full bg-white border border-black/20 p-2.5 rounded text-xs uppercase font-jersey tracking-widest focus:ring-1 focus:ring-ink">
                                </div>
                                <div>
                                    <input type="text" x-model="customNumber" maxlength="2" placeholder="NO (0-99)" 
                                           @input="if(activeTab !== 'back') activeTab = 'back'"
                                           class="w-full bg-white border border-black/20 p-2.5 rounded text-xs font-jersey tracking-widest text-center focus:ring-1 focus:ring-ink">
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- 3. Pilihan Patch Turnamen --}}
                    @if($product->allow_patch && !empty($product->available_patches))
                        <div>
                            <label class="block font-bold text-xs uppercase tracking-wider text-ink mb-1.5">
                                Patch Turnamen <span class="text-cyan-600">(+Rp {{ number_format($product->patch_price, 0, ',', '.') }})</span>
                            </label>
                            <select x-model="selectedPatch" class="w-full bg-white border border-black/20 p-2.5 rounded text-xs uppercase focus:ring-1 focus:ring-ink">
                                <option value="">Tanpa Patch (+Rp 0)</option>
                                @foreach($product->available_patches as $patch)
                                    <option value="{{ $patch }}">{{ $patch }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- 4. Kuantitas (Qty) & Grand Total --}}
                    <div class="flex items-center justify-between border-t border-black/10 pt-4">
                        <div>
                            <span class="text-xs text-ink-muted block uppercase tracking-wider">Kuantitas</span>
                            <div class="flex items-center border border-black/20 rounded mt-1 overflow-hidden">
                                <button type="button" @click="if(qty > 1) qty--" class="px-3 py-1 bg-canvas hover:bg-neutral-300 font-bold">-</button>
                                <input type="number" name="quantity" x-model="qty" min="1" max="10" class="w-12 text-center p-1 border-none text-xs font-bold text-ink" readonly>
                                <button type="button" @click="qty++" class="px-3 py-1 bg-canvas hover:bg-neutral-300 font-bold">+</button>
                            </div>
                        </div>

                        <div class="text-right">
                            <span class="text-xs text-ink-muted uppercase tracking-wider">Subtotal Item</span>
                            <div class="font-display font-black text-2xl text-ink mt-0.5" x-text="'Rp ' + grandTotal.toLocaleString('id-ID')"></div>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <button type="submit" class="btn-curtain w-full text-center py-4">
                        <span>🛍️ Masukkan ke Tas Belanja</span>
                    </button>
                </form>

            </div>

        </div>

        {{-- ===== REVIEWS & RATINGS SECTION ===== --}}
        <div id="reviews-section" class="mt-16 pt-10 border-t border-black/10">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                <div>
                    <h2 class="font-display font-bold text-xl uppercase tracking-tight text-ink flex items-center gap-2">
                        <span>⭐ Ulasan & Penilaian Pembeli</span>
                        <span class="text-sm font-semibold text-ink-muted">({{ $reviewsCount }})</span>
                    </h2>
                    <p class="text-xs text-ink-muted mt-1">Seluruh ulasan berasal dari Verified Buyer yang telah menyelesaikan pesanan.</p>
                </div>
                @if($reviewsCount > 0)
                    <div class="flex items-center gap-2 bg-amber-50 border border-amber-200 px-4 py-2 rounded-lg">
                        <span class="text-2xl font-black text-amber-600">★ {{ number_format($avgRating, 1) }}</span>
                        <span class="text-xs text-amber-900 font-semibold">dari 5.0 bintang</span>
                    </div>
                @endif
            </div>

            @if($product->reviews->isEmpty())
                <div class="bg-neutral-50 border border-black/5 rounded-lg p-8 text-center text-xs text-ink-muted">
                    <p class="text-base font-semibold text-ink mb-1">Belum ada ulasan untuk jersey ini.</p>
                    <p>Jadilah yang pertama memesan dan memberikan ulasan setelah jersey tiba di tangan Anda!</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($product->reviews()->with('user')->latest()->get() as $review)
                        <div class="bg-white border border-black/10 rounded-lg p-5 shadow-sm space-y-2">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="font-bold text-xs text-ink flex items-center gap-2">
                                        <span>{{ Str::mask($review->user->name ?? 'Pelanggan', '*', 3, 4) }}</span>
                                        <span class="text-[10px] px-1.5 py-0.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded font-semibold">
                                            ✓ Verified Buyer
                                        </span>
                                    </div>
                                    <div class="text-[10px] text-neutral-400 mt-0.5">
                                        {{ $review->created_at->format('d M Y') }}
                                    </div>
                                </div>
                                <div class="text-amber-500 font-bold text-sm">
                                    {{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}
                                </div>
                            </div>
                            <p class="text-xs text-ink-muted leading-relaxed">
                                "{{ $review->comment }}"
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ===== 3. RELATED PRODUCTS ===== --}}
        @if($relatedProducts->count() > 0)
            <div class="mt-20 pt-12 border-t border-black/10">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="font-display font-bold text-xl uppercase tracking-tight text-ink">
                        Koleksi Jersey Terkait
                    </h2>
                    <a href="{{ route('shop.index', ['category' => $product->category->slug]) }}" class="text-xs font-bold uppercase text-cyan-600 hover:underline">
                        Lihat Kategori Ini &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $related)
                        <x-product-card :product="$related" />
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    {{-- ===== SIZE CHART MODAL ===== --}}
    <div x-show="sizeModal" x-cloak class="fixed inset-0 z-50 bg-black/75 backdrop-blur-sm flex items-center justify-center p-4">
        <div @click.away="sizeModal = false" class="bg-white border border-black/10 w-full max-w-xl rounded-lg p-6 space-y-4 shadow-2xl">
            <div class="flex justify-between items-center border-b border-black/10 pb-3">
                <h3 class="font-display font-bold text-lg text-ink uppercase tracking-tight">Panduan Ukuran (Size Chart)</h3>
                <button @click="sizeModal = false" class="text-ink-muted hover:text-ink text-xl font-bold">&times;</button>
            </div>

            <p class="text-xs text-ink-muted">Toleransi ukuran jahitan & material elastis: &plusmn; 1-2 cm.</p>

            <table class="w-full text-xs text-left border border-black/10">
                <thead class="bg-canvas border-b border-black/10 uppercase font-bold text-ink">
                    <tr>
                        <th class="p-2.5">Ukuran</th>
                        <th class="p-2.5">Lebar Dada (cm)</th>
                        <th class="p-2.5">Panjang Baju (cm)</th>
                        <th class="p-2.5">Rekomendasi TB / BB</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-black/10">
                    <tr><td class="p-2.5 font-bold">S</td><td class="p-2.5">48 cm</td><td class="p-2.5">68 cm</td><td class="p-2.5">160-168 cm / 50-60 kg</td></tr>
                    <tr><td class="p-2.5 font-bold">M</td><td class="p-2.5">50 cm</td><td class="p-2.5">70 cm</td><td class="p-2.5">168-175 cm / 60-70 kg</td></tr>
                    <tr><td class="p-2.5 font-bold">L</td><td class="p-2.5">52 cm</td><td class="p-2.5">72 cm</td><td class="p-2.5">172-180 cm / 70-80 kg</td></tr>
                    <tr><td class="p-2.5 font-bold">XL</td><td class="p-2.5">54 cm</td><td class="p-2.5">74 cm</td><td class="p-2.5">178-185 cm / 80-90 kg</td></tr>
                    <tr><td class="p-2.5 font-bold">XXL</td><td class="p-2.5">56 cm</td><td class="p-2.5">76 cm</td><td class="p-2.5">180-190 cm / 90-100 kg</td></tr>
                    <tr><td class="p-2.5 font-bold">3XL</td><td class="p-2.5">58 cm</td><td class="p-2.5">78 cm</td><td class="p-2.5">> 185 cm / > 100 kg</td></tr>
                </tbody>
            </table>

            <div class="text-right pt-2">
                <button type="button" @click="sizeModal = false" class="btn-line py-2 px-4 text-xs">Tutup</button>
            </div>
        </div>
    </div>

</div>

@endsection
