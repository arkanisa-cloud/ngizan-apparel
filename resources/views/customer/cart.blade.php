@extends('layouts.customer')

@section('title', 'Tas Belanja · NGIZAN APPAREL')
@section('meta_description', 'Periksa item jersey pesanan Anda dan lanjutkan ke proses pengiriman & pembayaran.')

@section('content')
<div class="py-10">
    <div class="wrap">
        
        {{-- Breadcrumb & Title --}}
        <div class="flex items-center justify-between border-b border-black/10 pb-6 mb-8">
            <div>
                <span class="lbl text-ink-muted">DETAIL PESANAN ANDA</span>
                <h1 class="font-display font-black text-2xl md:text-3xl text-ink uppercase tracking-tight mt-1">
                    Tas Belanja
                </h1>
            </div>

            @if($cart && $cart->items->count() > 0)
                <form action="{{ route('customer.cart.clear') }}" method="POST" onsubmit="return confirm('Kosongkan semua item di tas belanja?');">
                    @csrf
                    <button type="submit" class="text-xs text-rose-600 hover:underline font-bold uppercase tracking-wider">
                        ✕ Kosongkan Tas
                    </button>
                </form>
            @endif
        </div>

        @if(!$cart || $cart->items->isEmpty())
            {{-- Empty State --}}
            <div class="text-center py-24 bg-white rounded border border-black/10 space-y-4 max-w-lg mx-auto my-8 p-8">
                <div class="text-5xl">🛍️</div>
                <h2 class="font-display font-black text-xl text-ink uppercase tracking-tight">Tas Belanja Anda Masih Kosong</h2>
                <p class="text-xs text-ink-muted leading-relaxed">
                    Jelajahi arsip jersey autentik kami dan tambahkan kustomisasi sablon nama & nomor punggung impian Anda.
                </p>
                <a href="{{ route('shop.index') }}" class="btn-curtain py-3.5 px-8 text-xs inline-flex mt-2">
                    <span>Mulai Belanja &rarr;</span>
                </a>
            </div>
        @else
            {{-- Cart Layout (2 Cols: List Item & Order Summary) --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
                
                {{-- 1. Daftar Item Keranjang (8 Cols) --}}
                <div class="lg:col-span-8 space-y-4">
                    @foreach($cart->items as $item)
                        @php
                            $img = $item->product->thumbnail_front ? asset('storage/' . $item->product->thumbnail_front) : 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=400&q=80';
                        @endphp
                        <div class="bg-white p-5 rounded border border-black/10 flex flex-col sm:flex-row gap-5 items-start sm:items-center justify-between">
                            
                            {{-- Thumbnail & Info --}}
                            <div class="flex items-center gap-4">
                                <div class="w-20 h-24 bg-canvas rounded overflow-hidden flex-shrink-0 border border-black/5">
                                    <img src="{{ $img }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                </div>
                                <div class="space-y-1">
                                    <span class="text-[10px] uppercase font-bold tracking-wider text-cyan-600">
                                        {{ $item->variant->type ?? 'Authentic' }}
                                    </span>
                                    <h3 class="font-bold text-sm text-ink uppercase tracking-tight">
                                        <a href="{{ route('shop.show', $item->product->slug) }}" class="hover:text-cyan-600 transition">
                                            {{ $item->product->name }}
                                        </a>
                                    </h3>
                                    <p class="text-xs text-ink-muted">
                                        Ukuran: <strong class="text-ink font-bold">{{ $item->variant->size ?? 'M' }}</strong> · Harga: Rp {{ number_format($item->unit_price, 0, ',', '.') }}
                                    </p>

                                    {{-- Customization Badges --}}
                                    <div class="flex flex-wrap gap-1.5 pt-1">
                                        @if($item->hasCustomNameset())
                                            <span class="inline-flex items-center gap-1 text-[9.5px] font-bold bg-neutral-900 text-white px-2 py-0.5 rounded font-jersey tracking-widest uppercase">
                                                ⚡ SABLON: {{ $item->custom_name ?? '-' }} #{{ $item->custom_number ?? '0' }}
                                            </span>
                                        @endif

                                        @if($item->hasPatch())
                                            <span class="inline-flex items-center gap-1 text-[9.5px] font-bold bg-cyan-100 text-cyan-800 px-2 py-0.5 rounded uppercase">
                                                ★ {{ $item->selected_patch }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Quantity Form & Subtotal --}}
                            <div class="flex sm:flex-col items-center sm:items-end justify-between w-full sm:w-auto gap-3 pt-3 sm:pt-0 border-t sm:border-t-0 border-black/10">
                                <div class="font-display font-bold text-base text-ink tabular-nums">
                                    Rp {{ number_format($item->total_price, 0, ',', '.') }}
                                </div>

                                <div class="flex items-center gap-3">
                                    {{-- Qty Update --}}
                                    <form action="{{ route('customer.cart.update', $item->id) }}" method="POST" class="flex items-center border border-black/20 rounded overflow-hidden">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" name="quantity" value="{{ max(1, $item->quantity - 1) }}" class="px-2.5 py-1 bg-canvas hover:bg-neutral-300 font-bold text-xs">-</button>
                                        <span class="px-3 py-1 text-xs font-bold text-ink bg-white">{{ $item->quantity }}</span>
                                        <button type="submit" name="quantity" value="{{ $item->quantity + 1 }}" class="px-2.5 py-1 bg-canvas hover:bg-neutral-300 font-bold text-xs">+</button>
                                    </form>

                                    {{-- Delete Button --}}
                                    <form action="{{ route('customer.cart.destroy', $item->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-neutral-400 hover:text-rose-600 transition" title="Hapus Item">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                        </div>
                    @endforeach

                    <div class="pt-2">
                        <a href="{{ route('shop.index') }}" class="text-xs font-bold text-cyan-600 hover:underline inline-flex items-center gap-1.5 uppercase tracking-wider">
                            &larr; Lanjut Tambah Koleksi Jersey Lainnya
                        </a>
                    </div>
                </div>

                {{-- 2. Ringkasan Pesanan / Summary (4 Cols) --}}
                <div class="lg:col-span-4 bg-white p-6 rounded border border-black/10 space-y-5 sticky top-28 shadow-sm">
                    <h2 class="font-display font-black text-lg text-ink uppercase tracking-tight border-b border-black/10 pb-3">
                        Ringkasan Belanja
                    </h2>

                    <div class="space-y-2.5 text-xs text-ink-muted">
                        <div class="flex justify-between">
                            <span>Total Jumlah Item</span>
                            <span class="font-bold text-ink">{{ $cart->total_quantity }} pcs</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Estimasi Berat Total</span>
                            <span class="font-bold text-ink">{{ $cart->total_weight_grams }} gram</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Subtotal Produk</span>
                            <span class="font-bold text-ink">{{ $cart->formatted_total_price }}</span>
                        </div>
                        <div class="flex justify-between text-neutral-400">
                            <span>Ongkos Kirim Biteship</span>
                            <span>Dihitung di Checkout</span>
                        </div>
                    </div>

                    <div class="border-t border-black/10 pt-4 flex justify-between items-center">
                        <span class="text-xs uppercase font-bold text-ink">Total Sementara</span>
                        <div class="font-display font-black text-xl text-cyan-600">
                            {{ $cart->formatted_total_price }}
                        </div>
                    </div>

                    <a href="{{ route('customer.checkout.index') }}" class="btn-curtain w-full text-center py-4 block">
                        <span>Lanjut ke Pengiriman &rarr;</span>
                    </a>

                    <div class="text-[11px] text-ink-muted text-center space-y-1 pt-2">
                        <p>🔒 Transaksi terlindungi enkripsi SHA512 & Midtrans.</p>
                        <p>🚚 Pengiriman akurat kurir instan & reguler via Biteship.</p>
                    </div>
                </div>

            </div>
        @endif

    </div>
</div>
@endsection
