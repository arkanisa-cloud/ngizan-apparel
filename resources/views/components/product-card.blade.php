@props(['product'])

@php
    $frontImage = $product->thumbnail_front ? asset('storage/' . $product->thumbnail_front) : 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=700&q=80';
    $backImage  = $product->thumbnail_back  ? asset('storage/' . $product->thumbnail_back)  : 'https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&w=700&q=80';
    
    $firstVariant = $product->variants->first();
    $badgeType = $firstVariant ? strtoupper($firstVariant->type) : 'AUTHENTIC KIT';

    $isPremium = auth()->check() && auth()->user()->isPremiumActive();
    $finalPrice = $product->getFinalPrice(auth()->user());
    $reviewsCount = $product->reviews_count;
    $avgRating = $product->average_rating;
@endphp

<div class="product-card group bg-canvas rounded-none">
    {{-- Image Frame dengan Dual POV Transition pada soft-cloud studio surface --}}
    <div class="image-frame bg-soft-cloud aspect-square overflow-hidden relative">
        <a href="{{ route('shop.show', $product->slug) }}" class="block w-full h-full">
            <img src="{{ $frontImage }}" alt="{{ $product->name }} Tampak Depan" class="img-front object-cover w-full h-full" loading="lazy">
            <img src="{{ $backImage }}" alt="{{ $product->name }} Tampak Belakang (Back POV)" class="img-back object-cover w-full h-full" loading="lazy">
        </a>

        {{-- Badge Tipe & Member Privilege --}}
        <div class="absolute top-2.5 left-2.5 z-10 flex flex-col gap-1 pointer-events-none">
            <span class="px-2.5 py-0.5 bg-ink text-white text-[10px] font-medium tracking-wider uppercase rounded-full">
                {{ $badgeType }}
            </span>
            @if($isPremium)
                <span class="px-2.5 py-0.5 bg-premium-gold text-ink text-[10px] font-medium tracking-wider uppercase rounded-full">
                    ★ Member 5% OFF
                </span>
            @endif
        </div>

        {{-- Quick Custom & Beli Button (Pill Slide-Up) --}}
        <a href="{{ route('shop.show', $product->slug) }}" class="quick-add block">
            Custom Sablon & Beli &rarr;
        </a>
    </div>

    {{-- Detail Metadata Produk (8px spacing rhythm) --}}
    <div class="pt-3 space-y-1">
        {{-- Title and Price Row --}}
        <div class="flex justify-between items-baseline gap-2">
            <h3 class="font-medium text-[15px] text-ink line-clamp-1">
                <a href="{{ route('shop.show', $product->slug) }}" class="hover:text-mute transition">
                    {{ $product->name }}
                </a>
            </h3>

            <div class="text-right shrink-0">
                @if($isPremium)
                    <div class="flex items-baseline gap-1.5 justify-end">
                        <span class="text-xs text-mute line-through tabular-nums">
                            {{ $product->formatted_price }}
                        </span>
                        <span class="font-medium text-[15px] text-sale tabular-nums">
                            Rp {{ number_format($finalPrice, 0, ',', '.') }}
                        </span>
                    </div>
                @else
                    <span class="font-medium text-[15px] text-ink tabular-nums">
                        {{ $product->formatted_price }}
                    </span>
                @endif
            </div>
        </div>

        {{-- Subtitle & Rating --}}
        <div class="flex items-center justify-between text-xs text-mute">
            <p>
                {{ $product->category->name ?? 'Football Kit' }}
                @if($product->allow_custom_nameset)
                    · <span class="text-ink font-medium">+Custom Sablon</span>
                @endif
            </p>

            <div class="flex items-center gap-1 text-xs">
                @if($reviewsCount > 0)
                    <span class="text-premium-gold font-medium">★ {{ number_format($avgRating, 1) }}</span>
                    <span class="text-mute">({{ $reviewsCount }})</span>
                @else
                    <span class="text-mute text-[11px]">Baru</span>
                @endif
            </div>
        </div>

        {{-- Pills Ukuran Stok Real-time (Pills dengan hairline borders) --}}
        <div class="flex flex-wrap gap-1 pt-1">
            @foreach($product->variants->sortBy('size') as $variant)
                @if($variant->stock > 0)
                    <span class="text-[10px] px-2 py-0.5 border {{ $variant->stock <= 3 ? 'border-amber-400 bg-amber-50 text-amber-900' : 'border-hairline text-ink' }} rounded-full font-medium">
                        {{ $variant->size }} @if($variant->stock <= 3)<span class="text-[9px] text-amber-700">(Sisa {{ $variant->stock }})</span>@endif
                    </span>
                @else
                    <span class="text-[10px] px-2 py-0.5 border border-hairline-soft bg-soft-cloud text-stone rounded-full line-through">
                        {{ $variant->size }}
                    </span>
                @endif
            @endforeach
        </div>
    </div>
</div>
