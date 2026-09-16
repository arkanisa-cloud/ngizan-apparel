<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NGIZAN APPAREL · Bespoke Football Kits & Archive Store')</title>
    <meta name="description" content="@yield('meta_description', 'Ngizan Apparel - Toko jersey sepak bola autentik, edisi retro, dan studio kustomisasi sablon nama resmi.')">

    <!-- Google Fonts Preconnect -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700;800;900&family=Bebas+Neue&family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Toastr.js CSS (CDN) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" />

    <!-- jQuery for Toastr.js -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="bg-canvas text-ink antialiased flex flex-col min-h-screen">

    {{-- ===== TOP NAVIGATION BAR ===== --}}
    <nav class="sticky top-0 z-50 bg-[#EFEDE8]/90 backdrop-blur-md border-b border-black/10">
        <div class="wrap">
            <div class="grid grid-cols-3 items-center h-[76px]">

                {{-- Kiri: Category Links --}}
                <div class="hidden lg:flex items-center gap-7">
                    <a href="{{ route('shop.index', ['category' => 'klub-eropa']) }}"
                        class="nav-link {{ request('category') === 'klub-eropa' ? 'active' : '' }}">Klub Eropa</a>
                    <a href="{{ route('shop.index', ['category' => 'tim-nasional']) }}"
                        class="nav-link {{ request('category') === 'tim-nasional' ? 'active' : '' }}">Tim Nasional</a>
                    <a href="{{ route('shop.index', ['category' => 'retro-classics']) }}"
                        class="nav-link {{ request('category') === 'retro-classics' ? 'active' : '' }}">Retro
                        Archive</a>
                    <a href="{{ route('home') }}#custom-studio" class="nav-link text-cyan-600 font-bold">Custom
                        Studio</a>
                </div>

                {{-- Mobile Menu Trigger --}}
                <div class="lg:hidden flex items-center" x-data>
                    <button @click="$dispatch('toggle-mobile-menu')" class="p-2 text-ink hover:opacity-70">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

                {{-- Tengah: Brand Wordmark --}}
                <div class="text-center">
                    <a href="{{ route('home') }}"
                        class="font-display font-black text-2xl md:text-3xl tracking-[0.28em] text-ink uppercase pl-[0.28em] hover:opacity-85 transition inline-block">
                        NGIZAN
                    </a>
                </div>

                {{-- Kanan: Search, Auth & Cart --}}
                <div
                    class="flex items-center justify-end gap-3 md:gap-5 text-[11px] font-bold tracking-[0.16em] uppercase">

                    {{-- Search link / button --}}
                    <a href="{{ route('shop.index') }}"
                        class="hidden sm:flex items-center gap-1.5 hover:opacity-70 transition p-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span>Cari</span>
                    </a>

                    {{-- Ngizan Premium Trigger / Badge --}}
                    @auth
                        @if(auth()->user()->isPremiumActive())
                            <button type="button" @click="$dispatch('open-premium-modal')" class="hidden sm:inline-flex items-center gap-1 px-3 py-1 bg-gradient-to-r from-amber-400 to-amber-500 text-neutral-900 rounded-full font-black text-[10px] tracking-wider uppercase shadow-xs hover:brightness-105 transition">
                                <span>⭐ MEMBER PREMIUM</span>
                            </button>
                        @else
                            <button type="button" @click="$dispatch('open-premium-modal')" class="hidden sm:inline-flex items-center gap-1 px-3 py-1 bg-amber-100 hover:bg-amber-200 text-amber-900 border border-amber-300 rounded-full font-bold text-[10px] tracking-wider uppercase transition">
                                <span>⭐ GABUNG PREMIUM</span>
                            </button>
                        @endif
                    @else
                        <button type="button" @click="$dispatch('open-premium-modal')" class="hidden sm:inline-flex items-center gap-1 px-3 py-1 bg-amber-100 hover:bg-amber-200 text-amber-900 border border-amber-300 rounded-full font-bold text-[10px] tracking-wider uppercase transition">
                            <span>⭐ NGIZAN PREMIUM</span>
                        </button>
                    @endauth

                    @guest
                        <a href="{{ route('auth.google') }}"
                            class="hidden sm:flex items-center gap-2 px-3 py-1.5 border border-black/20 rounded-full hover:bg-black hover:text-white transition">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                                <path fill="currentColor"
                                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                                <path fill="currentColor"
                                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                                <path fill="currentColor"
                                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
                            </svg>
                            <span>Google</span>
                        </a>

                        <a href="{{ route('login') }}" class="px-3 py-1.5 text-ink hover:underline">Masuk</a>
                    @else
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                class="flex items-center gap-2 px-3 py-1.5 border border-black/20 rounded-full hover:bg-black hover:text-white transition">
                                @if (Auth::user()->avatar)
                                    <img src="{{ Auth::user()->avatar }}" class="w-4 h-4 rounded-full object-cover">
                                @else
                                    <span
                                        class="w-4 h-4 rounded-full bg-black text-white text-[9px] flex items-center justify-center font-bold">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                                @endif
                                <span class="max-w-[90px] truncate">{{ Auth::user()->name }}</span>
                            </button>

                            <div x-show="open" @click.away="open = false" x-cloak x-transition
                                class="absolute right-0 mt-2 w-48 bg-white border border-black/10 rounded shadow-xl py-2 z-50 text-xs normal-case">
                                @if (Auth::user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}"
                                        class="block px-4 py-2 hover:bg-neutral-100 font-bold text-amber-600">⚡ Backoffice
                                        Admin</a>
                                @endif
                                @if (Auth::user()->isPremiumActive())
                                    <div class="px-4 py-2 bg-amber-50/80 border-b border-amber-200/60 text-[11px] text-amber-900 font-semibold flex items-center justify-between">
                                        <span>⭐ Member Premium</span>
                                        <span class="text-[9px] text-amber-700 font-mono font-bold">{{ Auth::user()->premium_until?->format('d/m/Y') }}</span>
                                    </div>
                                @else
                                    <button type="button" @click="open = false; $dispatch('open-premium-modal')" class="w-full text-left px-4 py-2 hover:bg-amber-50 text-amber-700 font-bold flex items-center justify-between border-b border-neutral-100">
                                        <span>⭐ Gabung Premium</span>
                                        <span class="text-[9px] bg-amber-200 text-amber-800 px-1.5 py-0.5 rounded font-bold">5% OFF</span>
                                    </button>
                                @endif
                                <a href="{{ route('customer.orders.index') }}"
                                    class="block px-4 py-2 hover:bg-neutral-100">Pesanan Saya</a>
                                <a href="{{ route('profile.edit') }}"
                                    class="block px-4 py-2 hover:bg-neutral-100">Pengaturan Akun</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="w-full text-left px-4 py-2 hover:bg-rose-50 text-rose-600 font-semibold border-t border-neutral-100 mt-1">Logout</button>
                                </form>
                            </div>
                        </div>
                    @endguest

                    {{-- Keranjang Belanja Cart Button --}}
                    <a href="{{ route('customer.cart.index') }}"
                        class="flex items-center gap-1.5 bg-black text-[#EFEDE8] px-3.5 py-1.5 rounded-full hover:bg-neutral-800 transition">
                        <span>Keranjang</span>
                        @php
                            $cartCount =
                                auth()->check() && auth()->user()->cart
                                    ? auth()->user()->cart->items->sum('quantity')
                                    : 0;
                        @endphp
                        <span
                            class="w-4 h-4 rounded-full bg-cyan-500 text-[9px] font-bold text-white flex items-center justify-center">{{ $cartCount }}</span>
                    </a>
                </div>

            </div>
        </div>

        {{-- Mobile Drawer Menu --}}
        <div x-data="{ open: false }" @toggle-mobile-menu.window="open = !open" x-show="open" x-cloak
            class="lg:hidden border-t border-black/10 bg-[#EFEDE8] p-4 space-y-3">
            <a href="{{ route('shop.index', ['category' => 'klub-eropa']) }}"
                class="block py-2 font-bold uppercase tracking-wider text-xs">Klub Eropa</a>
            <a href="{{ route('shop.index', ['category' => 'tim-nasional']) }}"
                class="block py-2 font-bold uppercase tracking-wider text-xs">Tim Nasional</a>
            <a href="{{ route('shop.index', ['category' => 'retro-classics']) }}"
                class="block py-2 font-bold uppercase tracking-wider text-xs">Retro Archive</a>
            <a href="{{ route('home') }}#custom-studio"
                class="block py-2 font-bold uppercase tracking-wider text-xs text-cyan-600">⚡ Custom Studio</a>
            <a href="{{ route('shop.index') }}"
                class="block py-2 font-bold uppercase tracking-wider text-xs text-neutral-600">Semua Koleksi</a>
        </div>
    </nav>

    {{-- ===== MAIN CONTENT ===== --}}
    <main class="flex-grow">
        @yield('content')
    </main>

    {{-- ===== EDITORIAL FOOTER ===== --}}
    <footer class="border-t border-black/10 bg-neutral-950 text-[#EFEDE8] py-16 text-xs mt-20">
        <div class="wrap grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">
            <div>
                <div class="font-display font-black text-xl tracking-[0.2em] mb-3 text-white uppercase">NGIZAN APPAREL
                </div>
                <p class="text-neutral-400 leading-relaxed">
                    Penyedia jersey sepak bola autentik, arsip vintage terkurasi, dan studio kustomisasi nameset
                    berstandar internasional.
                </p>
                <div class="mt-4 flex gap-3">
                    <span
                        class="text-[10px] px-2.5 py-1 bg-neutral-900 border border-neutral-800 rounded font-semibold text-cyan-400">⚡
                        Midtrans Verified</span>
                    <span
                        class="text-[10px] px-2.5 py-1 bg-neutral-900 border border-neutral-800 rounded font-semibold text-lime-400">🚚
                        Biteship Logistics</span>
                </div>
            </div>

            <div>
                <div class="font-bold uppercase tracking-wider mb-3 text-white font-display">Koleksi Jersey</div>
                <ul class="space-y-2 text-neutral-400">
                    <li><a href="{{ route('shop.index', ['category' => 'klub-eropa']) }}"
                            class="hover:text-white transition">Klub Eropa (EPL, La Liga, Serie A)</a></li>
                    <li><a href="{{ route('shop.index', ['category' => 'tim-nasional']) }}"
                            class="hover:text-white transition">Tim Nasional & World Cup</a></li>
                    <li><a href="{{ route('shop.index', ['category' => 'retro-classics']) }}"
                            class="hover:text-white transition">Retro Classics (90s & 2000s)</a></li>
                    <li><a href="{{ route('shop.index', ['category' => 'special-edition']) }}"
                            class="hover:text-white transition">Special Anime Edition</a></li>
                </ul>
            </div>

            <div>
                <div class="font-bold uppercase tracking-wider mb-3 text-white font-display">Layanan Pelanggan</div>
                <ul class="space-y-2 text-neutral-400">
                    <li><a href="{{ route('home') }}#custom-studio" class="hover:text-white transition">Live Custom
                            Nameset Studio</a></li>
                    <li><a href="{{ route('customer.orders.index') }}" class="hover:text-white transition">Lacak
                            Pengiriman Pesanan</a></li>
                    <li><a href="{{ route('shop.index') }}" class="hover:text-white transition">Panduan Ukuran (Size
                            Chart)</a></li>
                    <li><a href="{{ route('auth.google') }}" class="hover:text-white transition">Login 1-Klik
                            Google</a></li>
                </ul>
            </div>

            <div>
                <div class="font-bold uppercase tracking-wider mb-3 text-white font-display">Kontak & Workshop</div>
                <p class="text-neutral-400 leading-relaxed">
                    WhatsApp: <strong>+62 812-3456-7890</strong><br>
                    Email: <strong>support@ngizanapparel.com</strong><br>
                    Workshop: Bandung & Jakarta, Indonesia
                </p>
            </div>
        </div>

        <div
            class="wrap border-t border-neutral-800 pt-8 flex flex-col sm:flex-row justify-between items-center gap-4 text-[11px] text-neutral-500">
            <p>&copy; {{ date('Y') }} NGIZAN APPAREL. Seluruh hak cipta dilindungi.</p>
            <p class="tracking-widest uppercase text-neutral-400 font-semibold">Bespoke Football Kits & Archive Store
            </p>
        </div>
    </footer>

    {{-- ===== TOASTR.JS CONFIGURATION & SESSION FLASH HANDLER ===== --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            toastr.options = {
                "closeButton": true,
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": true,
                "showDuration": "300",
                "hideDuration": "800",
                "timeOut": "4000",
                "extendedTimeOut": "1500",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };

            @if (session('success'))
                toastr.success("{{ session('success') }}");
            @endif

            @if (session('error'))
                toastr.error("{{ session('error') }}");
            @endif

            @if (session('info'))
                toastr.info("{{ session('info') }}");
            @endif

            @if (session('warning'))
                toastr.warning("{{ session('warning') }}");
            @endif
        });
    </script>

    <!-- Midtrans Snap JS (Sandbox / Production) -->
    <script src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" 
            data-client-key="{{ config('services.midtrans.client_key') }}"></script>

    {{-- ===== NGIZAN PREMIUM MEMBERSHIP MODAL ===== --}}
    <div x-data="{
        isOpen: false,
        isLoading: false,
        async startSubscription() {
            this.isLoading = true;
            try {
                const res = await fetch('{{ route('customer.premium.subscribe') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (!res.ok) {
                    throw new Error(data.message || 'Gagal memproses langganan.');
                }
                if (data.snap_token) {
                    if (typeof window.snap !== 'undefined') {
                        window.snap.pay(data.snap_token, {
                            onSuccess: () => {
                                toastr.success('Selamat! Pembayaran berhasil. Akun Anda kini menjadi Ngizan Premium.');
                                setTimeout(() => window.location.reload(), 1500);
                            },
                            onPending: () => {
                                toastr.info('Menunggu penyelesaian pembayaran.');
                                setTimeout(() => window.location.reload(), 1500);
                            },
                            onError: () => {
                                toastr.error('Pembayaran gagal atau dibatalkan.');
                            },
                            onClose: () => {
                                toastr.warning('Popup pembayaran ditutup.');
                            }
                        });
                    } else {
                        toastr.error('Midtrans Snap SDK tidak terdeteksi.');
                    }
                }
            } catch (err) {
                toastr.error(err.message || 'Terjadi kesalahan sistem.');
            } finally {
                this.isLoading = false;
            }
        }
    }" 
    @open-premium-modal.window="isOpen = true" 
    x-cloak>
        <div x-show="isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-xs">
            <div @click.away="isOpen = false" class="relative max-w-lg w-full bg-neutral-950 text-white rounded-2xl p-6 sm:p-8 shadow-2xl border border-amber-500/30 overflow-hidden">
                {{-- Glow background --}}
                <div class="absolute -top-20 -right-20 w-60 h-60 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-20 -left-20 w-60 h-60 bg-yellow-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative space-y-6">
                    {{-- Header --}}
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-400/10 border border-amber-400/30 text-amber-400 text-[10px] font-black uppercase tracking-widest">
                                <span>★ VIP PRIVILEGE</span>
                            </div>
                            <h2 class="font-display font-black text-2xl sm:text-3xl uppercase tracking-tight text-white mt-2">
                                NGIZAN <span class="bg-gradient-to-r from-amber-300 via-amber-400 to-yellow-500 bg-clip-text text-transparent">PREMIUM</span>
                            </h2>
                            <p class="text-xs text-neutral-400 mt-1">Keistimewaan tak tertandingi untuk kolektor jersey sejati.</p>
                        </div>
                        <button type="button" @click="isOpen = false" class="text-neutral-400 hover:text-white text-2xl font-bold transition">&times;</button>
                    </div>

                    {{-- Features List --}}
                    <div class="space-y-3 bg-neutral-900/80 p-4 sm:p-5 rounded-xl border border-neutral-800 text-xs">
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0 font-bold text-xs">✓</div>
                            <div>
                                <strong class="text-white">Diskon Otomatis 5%</strong>
                                <p class="text-[11px] text-neutral-400">Potongan langsung 5% di setiap produk tanpa batas minimum transaksi.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0 font-bold text-xs">✓</div>
                            <div>
                                <strong class="text-white">Gratis Ongkir J&T Express</strong>
                                <p class="text-[11px] text-neutral-400">Kemitraan resmi J&T Express flat Rp 0 ke seluruh pelosok Nusantara.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0 font-bold text-xs">✓</div>
                            <div>
                                <strong class="text-white">Badge Eksklusif & Prioritas Produksi</strong>
                                <p class="text-[11px] text-neutral-400">Antrian sablon nameset & patch diprioritaskan di workshop Ngizan.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Price Tag --}}
                    <div class="flex items-baseline justify-between border-t border-neutral-800 pt-4">
                        <div>
                            <span class="text-[10px] text-neutral-400 uppercase tracking-wider block font-bold">Biaya Langganan</span>
                            <div class="flex items-baseline gap-1">
                                <span class="font-display font-black text-2xl sm:text-3xl text-amber-400">Rp 100.000</span>
                                <span class="text-xs text-neutral-400">/ tahun</span>
                            </div>
                        </div>
                        <span class="text-[10px] text-neutral-400 font-mono">Hanya ~Rp 8.300/bulan</span>
                    </div>

                    {{-- Action Button --}}
                    <div>
                        @guest
                            <a href="{{ route('login') }}" class="block text-center w-full py-3.5 px-4 bg-gradient-to-r from-amber-400 via-amber-500 to-yellow-500 hover:from-amber-500 hover:to-yellow-600 text-neutral-950 font-display font-black text-xs uppercase tracking-widest rounded-xl transition shadow-lg shadow-amber-500/20">
                                Masuk untuk Berlangganan &rarr;
                            </a>
                        @else
                            @if(auth()->user()->isPremiumActive())
                                <div class="p-3 bg-amber-500/10 border border-amber-500/30 rounded-xl text-center">
                                    <p class="text-xs font-bold text-amber-400">
                                        ★ Anda adalah Member Premium Aktif hingga {{ auth()->user()->premium_until?->format('d F Y') }}
                                    </p>
                                    <p class="text-[11px] text-neutral-400 mt-0.5">Nikmati seluruh diskon 5% otomatis di katalog kami.</p>
                                </div>
                            @else
                                <button type="button" @click="startSubscription()" :disabled="isLoading" class="w-full py-3.5 px-4 bg-gradient-to-r from-amber-400 via-amber-500 to-yellow-500 hover:from-amber-500 hover:to-yellow-600 text-neutral-950 font-display font-black text-xs uppercase tracking-widest rounded-xl transition shadow-lg shadow-amber-500/20 flex items-center justify-center gap-2">
                                    <span x-show="!isLoading">⚡ Berlangganan Sekarang via Midtrans</span>
                                    <span x-show="isLoading" class="inline-flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4 text-neutral-950" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Menyiapkan Pembayaran...
                                    </span>
                                </button>
                            @endif
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>

</html>
