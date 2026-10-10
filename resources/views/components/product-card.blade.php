@props(['product'])

@php
    $frontImage = $product->thumbnail_front ? asset('storage/' . $product->thumbnail_front) : 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=700&q=80';
    $backImage  = $product->thumbnail_back  ? asset('storage/' . $product->thumbnail_back)  : null;
    
    // Rating Bintang
    $avgRating = $product->average_rating > 0 ? $product->average_rating : 5.0;
    $reviewsCount = $product->reviews_count;

    // Membership Check
    $isPremium = auth()->check() && auth()->user()->isPremiumActive();
    $finalPrice = $product->getFinalPrice(auth()->user());
@endphp

<a href="{{ route('shop.show', $product->slug) }}" 
   class="product-card group block bg-transparent transition-all duration-300 select-none">
    
    {{-- 1. Image Frame dengan Background Soft Cloud & Dual POV Hover (Aspect Square) --}}
    <div class="image-frame bg-soft-cloud aspect-square overflow-hidden relative rounded-xl sm:rounded-none">
        <img src="{{ $frontImage }}" 
             alt="{{ $product->name }}" 
             class="img-front object-cover w-full h-full transition-all duration-500 ease-out group-hover:scale-105 {{ $backImage ? 'group-hover:opacity-0' : '' }}" 
             loading="lazy">
             
        @if($backImage)
            <img src="{{ $backImage }}" 
                 alt="{{ $product->name }} Tampak Belakang" 
                 class="img-back absolute inset-0 object-cover w-full h-full opacity-0 transition-all duration-500 ease-out group-hover:scale-105 group-hover:opacity-100" 
                 loading="lazy">
        @endif

        {{-- Badge Diskon Member jika Aktif --}}
        @if($isPremium)
            <div class="absolute top-2 left-2 z-10">
                <span class="bg-amber-400 text-ink text-[10px] font-bold px-2 py-0.5 rounded-full shadow-2xs uppercase tracking-wider">
                    5% OFF
                </span>
            </div>
        @endif

        {{-- Floating Action Icon on Hover (Desktop) --}}
        <div class="hidden sm:block absolute bottom-3 right-3 z-10 opacity-0 translate-y-2 scale-90 group-hover:opacity-100 group-hover:translate-y-0 group-hover:scale-100 transition-all duration-300 ease-out">
            <span class="w-9 h-9 bg-ink text-white rounded-full flex items-center justify-center shadow-md hover:bg-black transition-transform active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
            </span>
        </div>
    </div>

    {{-- 2. Detail Produk (Kategori, Nama, Harga, Bintang) --}}
    <div class="pt-2.5 sm:pt-3 space-y-1">
        {{-- Kategori Subtitle --}}
        <p class="text-[10px] sm:text-[11px] font-medium text-mute uppercase tracking-wider truncate">
            {{ $product->category->name ?? 'Official Kit' }}
        </p>

        {{-- Nama Produk --}}
        <h3 class="font-semibold text-xs sm:text-sm text-ink line-clamp-1 group-hover:text-black transition duration-200">
            {{ $product->name }}
        </h3>

        {{-- Responsive Price & Rating Block --}}
        <div class="flex flex-col xs:flex-row xs:items-baseline xs:justify-between gap-1 pt-0.5">
            {{-- Harga Produk --}}
            <div class="flex items-baseline gap-1.5 flex-wrap">
                @if($isPremium)
                    <p class="font-bold text-xs sm:text-sm text-ink tabular-nums tracking-tight">
                        Rp {{ number_format($finalPrice, 0, ',', '.') }}
                    </p>
                    <span class="text-[10px] sm:text-[11px] text-mute line-through tabular-nums">
                        {{ $product->formatted_price }}
                    </span>
                @else
                    <p class="font-bold text-xs sm:text-sm text-ink tabular-nums tracking-tight">
                        {{ $product->formatted_price }}
                    </p>
                @endif
            </div>

            {{-- Rating Bintang --}}
            <div class="flex items-center gap-1 text-[11px] sm:text-xs shrink-0 text-mute">
                <span class="text-amber-500 text-xs">★</span>
                <span class="text-ink font-semibold tabular-nums">{{ number_format($avgRating, 1) }}</span>
                @if($reviewsCount > 0)
                    <span class="text-[10px] sm:text-[11px] tabular-nums">({{ $reviewsCount }})</span>
                @endif
            </div>
        </div>
    </div>
</a>
