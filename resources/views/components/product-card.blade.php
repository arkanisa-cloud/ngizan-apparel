@props(['product'])

@php
    $frontImage = $product->thumbnail_front ? asset('storage/' . $product->thumbnail_front) : 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=700&q=80';
    $backImage  = $product->thumbnail_back  ? asset('storage/' . $product->thumbnail_back)  : 'https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&w=700&q=80';
    
    // Ambil varian pertama atau tipe
    $firstVariant = $product->variants->first();
    $badgeType = $firstVariant ? strtoupper($firstVariant->type) : 'AUTHENTIC KIT';

    $isPremium = auth()->check() && auth()->user()->isPremiumActive();
    $finalPrice = $product->getFinalPrice(auth()->user());
    $reviewsCount = $product->reviews_count;
    $avgRating = $product->average_rating;
@endphp

<div class="product-card group">
    {{-- Image Frame dengan Dual POV Transition --}}
    <div class="image-frame">
        <a href="{{ route('shop.show', $product->slug) }}" class="block w-full h-full">
            <img src="{{ $frontImage }}" alt="{{ $product->name }} Tampak Depan" class="img-front" loading="lazy">
            <img src="{{ $backImage }}" alt="{{ $product->name }} Tampak Belakang (Back POV)" class="img-back" loading="lazy">
        </a>

        {{-- Badge Tipe / Edisi & Member Discount --}}
        <div class="absolute top-2.5 left-2.5 z-10 flex flex-col gap-1 pointer-events-none">
            <span class="px-2 py-0.5 bg-black/90 text-white text-[9px] font-bold tracking-widest uppercase rounded">
                {{ $badgeType }}
            </span>
            @if($isPremium)
                <span class="px-2 py-0.5 bg-amber-500 text-black text-[9px] font-black tracking-wider uppercase rounded shadow">
                    ⭐ Member 5% OFF
                </span>
            @endif
        </div>

        {{-- Quick Custom & Beli Button (Slide-Up) --}}
        <a href="{{ route('shop.show', $product->slug) }}" class="quick-add block">
            ⚡ Custom Sablon & Beli
        </a>
    </div>

    {{-- Detail Metadata Produk --}}
    <div class="pt-3">
        <div class="flex justify-between items-start gap-2">
            <h3 class="font-bold text-sm text-ink uppercase tracking-tight line-clamp-1">
                <a href="{{ route('shop.show', $product->slug) }}" class="hover:text-cyan-600 transition">
                    {{ $product->name }}
                </a>
            </h3>
            <div class="text-right">
                @if($isPremium)
                    <span class="text-[10px] text-neutral-400 line-through block">
                        {{ $product->formatted_price }}
                    </span>
                    <span class="font-bold text-sm text-amber-600 tabular-nums whitespace-nowrap">
                        Rp {{ number_format($finalPrice, 0, ',', '.') }}
                    </span>
                @else
                    <span class="font-bold text-sm text-ink tabular-nums whitespace-nowrap">
                        {{ $product->formatted_price }}
                    </span>
                @endif
            </div>
        </div>

        <div class="flex items-center justify-between text-xs mt-1">
            <p class="text-ink-muted text-[11px]">
                {{ $product->category->name ?? 'Football Kit' }}
                @if($product->allow_custom_nameset)
                    · <span class="text-cyan-600 font-semibold">+Custom Sablon</span>
                @endif
            </p>

            <div class="flex items-center gap-1 text-[11px]">
                @if($reviewsCount > 0)
                    <span class="text-amber-500 font-bold">★ {{ number_format($avgRating, 1) }}</span>
                    <span class="text-neutral-400">({{ $reviewsCount }})</span>
                @else
                    <span class="text-neutral-400 text-[10px]">Baru</span>
                @endif
            </div>
        </div>

        {{-- Pills Ukuran dengan Indikator Stok Real-time --}}
        <div class="flex flex-wrap gap-1 mt-2.5">
            @foreach($product->variants->sortBy('size') as $variant)
                @if($variant->stock > 0)
                    <span class="text-[9.5px] px-1.5 py-0.5 border {{ $variant->stock <= 3 ? 'border-amber-500 bg-amber-50 text-amber-800 font-bold' : 'border-black/15 text-ink-muted' }} rounded font-semibold">
                        {{ $variant->size }} @if($variant->stock <= 3)<span class="text-[8.5px]">(Sisa {{ $variant->stock }})</span>@endif
                    </span>
                @else
                    <span class="text-[9.5px] px-1.5 py-0.5 border border-black/10 bg-neutral-200/50 text-neutral-400 rounded line-through">
                        {{ $variant->size }}
                    </span>
                @endif
            @endforeach
        </div>
    </div>
</div>
