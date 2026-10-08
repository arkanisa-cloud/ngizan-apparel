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
   class="product-card group block p-2 sm:p-2.5 bg-white border border-gray-100 hover:border-gray-300 rounded-xl sm:rounded-2xl shadow-[0_1px_4px_rgba(0,0,0,0.02)] hover:shadow-[0_8px_24px_rgba(0,0,0,0.06)] transition-all duration-300 select-none">
    
    {{-- 1. Image Frame dengan Background Soft Cloud & Dual POV Hover --}}
    <div class="image-frame bg-soft-cloud aspect-square rounded-lg sm:rounded-xl overflow-hidden relative">
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
                <span class="bg-premium-gold text-ink text-[10px] font-bold px-2 py-0.5 rounded-full shadow-xs uppercase tracking-wide">
                    5% OFF
                </span>
            </div>
        @endif

        {{-- Floating Add / Detail Action Button on Hover --}}
        <div class="absolute bottom-2.5 right-2.5 z-10 opacity-0 translate-y-2 scale-90 group-hover:opacity-100 group-hover:translate-y-0 group-hover:scale-100 transition-all duration-300 ease-out">
            <span class="w-8 h-8 sm:w-9 sm:h-9 bg-ink text-white rounded-full flex items-center justify-center shadow-md hover:bg-black transition-transform active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
            </span>
        </div>
    </div>

    {{-- 2. Detail Produk (Kategori, Nama, Harga, Bintang) --}}
    <div class="pt-3 px-1 pb-1 space-y-1">
        {{-- Kategori Subtitle --}}
        <p class="text-[11px] font-medium text-mute uppercase tracking-wider truncate">
            {{ $product->category->name ?? 'Official Jersey' }}
        </p>

        {{-- Nama Produk --}}
        <h3 class="font-medium text-[13px] sm:text-[15px] text-ink line-clamp-1 group-hover:text-black transition duration-200">
            {{ $product->name }}
        </h3>

        {{-- Row: Harga & Rating Bintang --}}
        <div class="flex items-center justify-between gap-2 pt-1">
            {{-- Harga Produk --}}
            <div class="flex items-baseline gap-1.5 flex-wrap">
                @if($isPremium)
                    <p class="font-bold text-[14px] sm:text-[15px] text-ink tabular-nums tracking-tight">
                        Rp {{ number_format($finalPrice, 0, ',', '.') }}
                    </p>
                    <span class="text-[11px] text-mute line-through tabular-nums">
                        {{ $product->formatted_price }}
                    </span>
                @else
                    <p class="font-bold text-[14px] sm:text-[15px] text-ink tabular-nums tracking-tight">
                        {{ $product->formatted_price }}
                    </p>
                @endif
            </div>

            {{-- Rating Bintang --}}
            <div class="flex items-center gap-1 text-xs shrink-0">
                <span class="text-amber-500 text-xs">★</span>
                <span class="text-ink font-semibold text-xs tabular-nums">{{ number_format($avgRating, 1) }}</span>
                @if($reviewsCount > 0)
                    <span class="text-mute text-[11px] tabular-nums">({{ $reviewsCount }})</span>
                @endif
            </div>
        </div>
    </div>
</a>
