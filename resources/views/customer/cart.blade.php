@extends('layouts.customer')

@section('title', 'Tas Belanja · NGIZAN APPAREL')
@section('meta_description', 'Periksa item jersey pesanan Anda dan lanjutkan ke proses pengiriman & pembayaran.')

@section('content')
<div class="py-8 bg-canvas">
    <div class="wrap">
        
        {{-- Header & Title --}}
        <div class="flex items-center justify-between border-b border-hairline-soft pb-5 mb-8">
            <div>
                <span class="text-xs font-medium uppercase tracking-widest text-mute block mb-1">Detail Pesanan Anda</span>
                <h1 class="text-2xl sm:text-3xl font-medium tracking-tight text-ink">
                    Tas Belanja
                </h1>
            </div>

            @if($cart && $cart->items->count() > 0)
                <form action="{{ route('customer.cart.clear') }}" method="POST" onsubmit="return confirm('Kosongkan semua item di tas belanja?');">
                    @csrf
                    <button type="submit" class="text-xs text-sale hover:underline font-medium">
                        ✕ Kosongkan Tas
                    </button>
                </form>
            @endif
        </div>

        @if(!$cart || $cart->items->isEmpty())
            {{-- Empty State --}}
            <div class="text-center py-20 bg-soft-cloud border border-hairline-soft max-w-lg mx-auto my-8 p-8 space-y-4">
                <h2 class="text-xl font-medium text-ink">Tas Belanja Anda Masih Kosong</h2>
                <p class="text-xs text-mute leading-relaxed max-w-sm mx-auto">
                    Jelajahi arsip jersey autentik kami dan tambahkan kustomisasi sablon nama & nomor punggung impian Anda.
                </p>
                <a href="{{ route('shop.index') }}" class="btn-primary py-3 px-8 text-xs inline-flex mt-2 rounded-full">
                    <span>Mulai Belanja &rarr;</span>
                </a>
            </div>
        @else
            {{-- Cart Layout (2 Cols: List Item & Order Summary) --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
                
                {{-- 1. Daftar Item Keranjang (8 Cols) --}}
                <div class="lg:col-span-8 space-y-4">
                    @foreach($cart->items as $item)
                        @php
                            $img = $item->product->thumbnail_front ? asset('storage/' . $item->product->thumbnail_front) : 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=400&q=80';
                        @endphp
                        <div class="bg-white p-5 border border-hairline-soft flex flex-col sm:flex-row gap-5 items-start sm:items-center justify-between">
                            
                            {{-- Thumbnail & Info --}}
                            <div class="flex items-center gap-4">
                                <div class="w-20 h-24 bg-soft-cloud overflow-hidden flex-shrink-0">
                                    <img src="{{ $img }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                </div>
                                <div class="space-y-1">
                                    <span class="text-[10px] uppercase font-medium tracking-wider text-mute">
                                        {{ $item->variant->type ?? 'Authentic' }}
                                    </span>
                                    <h3 class="font-medium text-sm text-ink">
                                        <a href="{{ route('shop.show', $item->product->slug) }}" class="hover:text-mute transition">
                                            {{ $item->product->name }}
                                        </a>
                                    </h3>
                                    <p class="text-xs text-mute">
                                        Ukuran: <strong class="text-ink font-medium">{{ $item->variant->size ?? 'M' }}</strong> · Harga: Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                    </p>

                                    {{-- Customization Badges --}}
                                    <div class="flex flex-wrap gap-1.5 pt-1">
                                        @if($item->hasCustomNameset())
                                            <span class="inline-flex items-center gap-1 text-[10px] font-medium bg-soft-cloud text-ink border border-hairline px-2.5 py-0.5 rounded-full font-jersey tracking-wider uppercase">
                                                SABLON: {{ $item->custom_name ?? '-' }} #{{ $item->custom_number ?? '0' }}
                                            </span>
                                        @endif

                                        @if($item->hasPatch())
                                            <span class="inline-flex items-center gap-1 text-[10px] font-medium bg-soft-cloud text-ink border border-hairline px-2.5 py-0.5 rounded-full uppercase">
                                                ★ {{ $item->selected_patch }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Quantity Form & Subtotal --}}
                            <div class="flex sm:flex-col items-center sm:items-end justify-between w-full sm:w-auto gap-3 pt-3 sm:pt-0 border-t sm:border-t-0 border-hairline-soft">
                                <div class="font-medium text-base text-ink tabular-nums">
                                    Rp {{ number_format($item->total_price, 0, ',', '.') }}
                                </div>

                                <div class="flex items-center gap-3">
                                    {{-- Qty Update --}}
                                    <form action="{{ route('customer.cart.update', $item->id) }}" method="POST" class="flex items-center border border-hairline rounded-full overflow-hidden">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" name="quantity" value="{{ max(1, $item->quantity - 1) }}" class="px-2.5 py-1 bg-soft-cloud hover:bg-neutral-200 font-medium text-xs text-ink">-</button>
                                        <span class="px-3 py-1 text-xs font-medium text-ink bg-white">{{ $item->quantity }}</span>
                                        <button type="submit" name="quantity" value="{{ $item->quantity + 1 }}" class="px-2.5 py-1 bg-soft-cloud hover:bg-neutral-200 font-medium text-xs text-ink">+</button>
                                    </form>

                                    {{-- Delete Button --}}
                                    <form action="{{ route('customer.cart.destroy', $item->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-mute hover:text-sale transition" title="Hapus Item">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                        </div>
                    @endforeach

                    <div class="pt-3">
                        <a href="{{ route('shop.index') }}" class="text-xs font-medium text-ink hover:text-mute underline inline-flex items-center gap-1">
                            &larr; Lanjut Tambah Koleksi Jersey Lainnya
                        </a>
                    </div>
                </div>

                {{-- 2. Ringkasan Pesanan / Summary (4 Cols) --}}
                <div class="lg:col-span-4 bg-soft-cloud p-6 rounded-2xl border border-hairline-soft space-y-5 sticky top-24">
                    <h2 class="font-medium text-base text-ink uppercase tracking-tight border-b border-hairline-soft pb-3">
                        Ringkasan Belanja
                    </h2>

                    <div class="space-y-2.5 text-xs text-mute">
                        <div class="flex justify-between">
                            <span>Total Jumlah Item</span>
                            <span class="font-medium text-ink">{{ $cart->total_quantity }} pcs</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Estimasi Berat Total</span>
                            <span class="font-medium text-ink">{{ $cart->total_weight_grams }} gram</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Subtotal Produk</span>
                            <span class="font-medium text-ink">{{ $cart->formatted_total_price }}</span>
                        </div>
                        <div class="flex justify-between text-mute">
                            <span>Ongkos Kirim J&T / Kurir</span>
                            <span>Dihitung di Checkout</span>
                        </div>
                    </div>

                    <div class="border-t border-hairline-soft pt-4 flex justify-between items-center">
                        <span class="text-xs uppercase font-medium text-ink">Total Sementara</span>
                        <div class="text-xl font-medium text-ink tabular-nums">
                            {{ $cart->formatted_total_price }}
                        </div>
                    </div>

                    <a href="{{ route('customer.checkout.index') }}" class="btn-primary w-full text-center py-4 block rounded-full text-xs font-medium uppercase tracking-wider">
                        <span>Lanjut ke Pengiriman &rarr;</span>
                    </a>

                    <div class="text-[11px] text-mute text-center space-y-1 pt-1">
                        <p>🔒 Transaksi terlindungi enkripsi resmi Midtrans.</p>
                        <p>🚚 Pengiriman akurat kurir reguler & instan via Biteship & J&T.</p>
                    </div>
                </div>

            </div>
        @endif

    </div>
</div>
@endsection
