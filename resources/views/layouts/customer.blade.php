<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NGIZAN APPAREL · Bespoke Football Kits & Archive Store')</title>
    <meta name="description" content="@yield('meta_description', 'Ngizan Apparel - Toko jersey sepak bola autentik, edisi retro, dan studio kustomisasi sablon nama resmi.')">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="bg-canvas text-ink antialiased flex flex-col min-h-screen font-sans">

    {{-- ===== TOP NAVIGATION BAR ===== --}}
    <nav class="sticky top-0 z-50 bg-white border-b border-hairline-soft transition-colors duration-200">
        <div class="wrap">
            <div class="flex items-center justify-between h-[60px] gap-4">

                {{-- Kiri: Logo Wordmark & Mobile Trigger --}}
                <div class="flex items-center gap-3">
                    <div class="lg:hidden flex items-center" x-data>
                        <button @click="$dispatch('toggle-mobile-menu')" class="p-2 text-ink hover:opacity-70 focus:outline-none" aria-label="Menu">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>

                    <a href="{{ route('home') }}" class="font-display font-medium text-2xl md:text-3xl tracking-wider text-ink uppercase hover:opacity-80 transition inline-block">
                        NGIZAN
                    </a>
                </div>

                {{-- Tengah: Nav Links (Desktop) --}}
                <div class="hidden lg:flex items-center gap-8 text-[15px] font-medium text-ink">
                    <a href="{{ route('shop.index', ['category' => 'klub-eropa']) }}"
                        class="nav-link {{ request('category') === 'klub-eropa' ? 'active' : '' }}">Klub Eropa</a>
                    <a href="{{ route('shop.index', ['category' => 'tim-nasional']) }}"
                        class="nav-link {{ request('category') === 'tim-nasional' ? 'active' : '' }}">Tim Nasional</a>
                    <a href="{{ route('shop.index', ['category' => 'retro-classics']) }}"
                        class="nav-link {{ request('category') === 'retro-classics' ? 'active' : '' }}">Retro Archive</a>
                    <a href="{{ route('home') }}#custom-studio" class="nav-link text-ink">Custom Studio</a>
                    <a href="{{ route('shop.index') }}" class="nav-link {{ !request('category') && request()->routeIs('shop.index') ? 'active' : '' }}">Semua Koleksi</a>
                </div>

                {{-- Kanan: Search Pill, Premium, Auth, Cart --}}
                <div class="flex items-center gap-2 sm:gap-3">

                    {{-- Search Pill --}}
                    <a href="{{ route('shop.index') }}"
                        class="hidden md:flex items-center gap-2 px-3.5 py-1.5 bg-soft-cloud hover:bg-neutral-200 text-ink text-xs font-medium rounded-full transition"
                        title="Cari Jersey">
                        <svg class="w-3.5 h-3.5 text-mute" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span class="text-mute">Cari</span>
                    </a>

                    {{-- Ngizan Premium Trigger / Badge --}}
                    @auth
                        @if(auth()->user()->isPremiumActive())
                            <button type="button" @click="$dispatch('open-premium-modal')" class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-premium-gold text-ink rounded-full font-medium text-xs tracking-tight hover:brightness-95 transition">
                                <span class="text-xs">⭐</span>
                                <span>MEMBER</span>
                            </button>
                        @else
                            <button type="button" @click="$dispatch('open-premium-modal')" class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-soft-cloud hover:bg-neutral-200 text-ink border border-hairline rounded-full font-medium text-xs tracking-tight transition">
                                <span class="text-amber-500">⭐</span>
                                <span>PREMIUM</span>
                            </button>
                        @endif
                    @else
                        <button type="button" @click="$dispatch('open-premium-modal')" class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-soft-cloud hover:bg-neutral-200 text-ink border border-hairline rounded-full font-medium text-xs tracking-tight transition">
                            <span class="text-amber-500">⭐</span>
                            <span>PREMIUM</span>
                        </button>
                    @endauth

                    {{-- Auth Dropdown / Login --}}
                    @guest
                        <a href="{{ route('auth.google') }}"
                            class="hidden sm:flex items-center gap-2 px-3.5 py-1.5 bg-soft-cloud hover:bg-neutral-200 text-ink text-xs font-medium rounded-full transition">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                                <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                                <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z" />
                                <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z" />
                            </svg>
                            <span>Google</span>
                        </a>

                        <a href="{{ route('login') }}" class="px-3 py-1.5 text-xs font-medium text-ink hover:opacity-75 transition">Masuk</a>
                    @else
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                class="flex items-center gap-2 px-3 py-1.5 border border-hairline rounded-full hover:border-ink transition text-xs font-medium">
                                @if (Auth::user()->avatar)
                                    <img src="{{ Auth::user()->avatar }}" class="w-4 h-4 rounded-full object-cover">
                                @else
                                    <span class="w-4 h-4 rounded-full bg-ink text-white text-[9px] flex items-center justify-center font-bold">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                                @endif
                                <span class="max-w-[80px] truncate">{{ Auth::user()->name }}</span>
                            </button>

                            <div x-show="open" @click.away="open = false" x-cloak x-transition
                                class="absolute right-0 mt-2 w-52 bg-white border border-hairline rounded-xl py-2 z-50 text-xs shadow-lg">
                                @if (Auth::user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}"
                                        class="block px-4 py-2 hover:bg-soft-cloud font-medium text-ink">⚡ Backoffice Admin</a>
                                @endif
                                @if (Auth::user()->isPremiumActive())
                                    <div class="px-4 py-2 bg-soft-cloud border-b border-hairline-soft text-[11px] text-ink font-medium flex items-center justify-between">
                                        <span>⭐ Member Premium</span>
                                        <span class="text-[10px] text-mute font-mono">{{ Auth::user()->premium_until?->format('d/m/Y') }}</span>
                                    </div>
                                @else
                                    <button type="button" @click="open = false; $dispatch('open-premium-modal')" class="w-full text-left px-4 py-2 hover:bg-soft-cloud text-ink font-medium flex items-center justify-between border-b border-hairline-soft">
                                        <span>⭐ Gabung Premium</span>
                                        <span class="text-[10px] bg-amber-100 text-amber-900 px-1.5 py-0.5 rounded-full font-bold">5% OFF</span>
                                    </button>
                                @endif
                                <a href="{{ route('customer.orders.index') }}" class="block px-4 py-2 hover:bg-soft-cloud text-ink">Pesanan Saya</a>
                                <a href="{{ route('profile.edit') }}" class="block px-4 py-2 hover:bg-soft-cloud text-ink">Pengaturan Akun</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 hover:bg-rose-50 text-sale font-medium border-t border-hairline-soft mt-1">Logout</button>
                                </form>
                            </div>
                        </div>
                    @endguest

                    {{-- Keranjang Belanja Cart Icon Pill --}}
                    @php
                        $cartCount = auth()->check() && auth()->user()->cart
                            ? auth()->user()->cart->items->sum('quantity')
                            : 0;
                    @endphp
                    <a href="{{ route('customer.cart.index') }}"
                        class="w-10 h-10 rounded-full bg-ink text-white flex items-center justify-center relative hover:opacity-85 transition shrink-0"
                        title="Keranjang Belanja">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        @if($cartCount > 0)
                            <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-ink text-white border-2 border-white text-[10px] font-bold flex items-center justify-center">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
                </div>

            </div>
        </div>

        {{-- Mobile Drawer Menu --}}
        <div x-data="{ open: false }" @toggle-mobile-menu.window="open = !open" x-show="open" x-cloak
            class="lg:hidden border-t border-hairline-soft bg-white p-4 space-y-3">
            <a href="{{ route('shop.index', ['category' => 'klub-eropa']) }}"
                class="block py-2 text-sm font-medium text-ink hover:opacity-75">Klub Eropa</a>
            <a href="{{ route('shop.index', ['category' => 'tim-nasional']) }}"
                class="block py-2 text-sm font-medium text-ink hover:opacity-75">Tim Nasional</a>
            <a href="{{ route('shop.index', ['category' => 'retro-classics']) }}"
                class="block py-2 text-sm font-medium text-ink hover:opacity-75">Retro Archive</a>
            <a href="{{ route('home') }}#custom-studio"
                class="block py-2 text-sm font-medium text-ink hover:opacity-75">Custom Studio</a>
            <a href="{{ route('shop.index') }}"
                class="block py-2 text-sm font-medium text-mute hover:text-ink">Semua Koleksi</a>
            <button type="button" @click="open = false; $dispatch('open-premium-modal')"
                class="w-full text-left py-2 text-sm font-medium text-amber-700 flex items-center gap-1.5">
                <span>⭐ Ngizan Premium (5% OFF)</span>
            </button>
        </div>
    </nav>

    {{-- ===== MAIN CONTENT ===== --}}
    <main class="flex-grow">
        @yield('content')
    </main>

    {{-- ===== NIKE EDITORIAL FOOTER ===== --}}
    <footer class="border-t border-hairline bg-white text-ink pt-16 pb-12 text-xs mt-20">
        <div class="wrap grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">
            <div>
                <div class="font-display font-medium text-2xl tracking-wider text-ink uppercase mb-3">
                    NGIZAN APPAREL
                </div>
                <p class="text-mute leading-relaxed">
                    Penyedia jersey sepak bola autentik, arsip vintage terkurasi, dan studio kustomisasi nameset berstandar editorial.
                </p>
                <div class="mt-5 flex flex-wrap gap-2">
                    <span class="text-[11px] px-3 py-1 bg-soft-cloud border border-hairline-soft rounded-full font-medium text-ink">
                        Midtrans Verified
                    </span>
                    <span class="text-[11px] px-3 py-1 bg-soft-cloud border border-hairline-soft rounded-full font-medium text-ink">
                        J&T Express Partner
                    </span>
                </div>
            </div>

            <div>
                <div class="font-medium text-sm text-ink mb-4">Koleksi Jersey</div>
                <ul class="space-y-2.5 text-mute">
                    <li><a href="{{ route('shop.index', ['category' => 'klub-eropa']) }}" class="hover:text-ink transition">Klub Eropa (EPL, La Liga, Serie A)</a></li>
                    <li><a href="{{ route('shop.index', ['category' => 'tim-nasional']) }}" class="hover:text-ink transition">Tim Nasional & World Cup</a></li>
                    <li><a href="{{ route('shop.index', ['category' => 'retro-classics']) }}" class="hover:text-ink transition">Retro Classics (90s & 2000s)</a></li>
                    <li><a href="{{ route('shop.index', ['category' => 'special-edition']) }}" class="hover:text-ink transition">Special Edition</a></li>
                </ul>
            </div>

            <div>
                <div class="font-medium text-sm text-ink mb-4">Layanan Pelanggan</div>
                <ul class="space-y-2.5 text-mute">
                    <li><a href="{{ route('home') }}#custom-studio" class="hover:text-ink transition">Live Custom Nameset Studio</a></li>
                    <li><a href="{{ route('customer.orders.index') }}" class="hover:text-ink transition">Lacak Pengiriman Pesanan</a></li>
                    <li><a href="{{ route('shop.index') }}" class="hover:text-ink transition">Panduan Ukuran (Size Chart)</a></li>
                    <li><a href="{{ route('auth.google') }}" class="hover:text-ink transition">Login 1-Klik Google</a></li>
                </ul>
            </div>

            <div>
                <div class="font-medium text-sm text-ink mb-4">Kontak & Workshop</div>
                <p class="text-mute leading-relaxed">
                    WhatsApp: <span class="text-ink font-medium">+62 812-3456-7890</span><br>
                    Email: <span class="text-ink font-medium">support@ngizanapparel.com</span><br>
                    Workshop: Bandung & Jakarta, Indonesia
                </p>
            </div>
        </div>

        <div class="wrap border-t border-hairline-soft pt-8 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-mute">
            <p>&copy; {{ date('Y') }} NGIZAN APPAREL. Seluruh hak cipta dilindungi.</p>
            <p class="font-medium text-ink tracking-wide uppercase">Bespoke Football Kits & Archive Store</p>
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

    {{-- ===== NGIZAN PREMIUM MEMBERSHIP MODAL (NIKE EDITORIAL) ===== --}}
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
        <div x-show="isOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs">
            <div @click.away="isOpen = false" class="relative max-w-lg w-full bg-white text-ink rounded-2xl p-6 sm:p-8 shadow-2xl border border-hairline overflow-hidden">
                <div class="space-y-6">
                    {{-- Header --}}
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-soft-cloud border border-hairline text-ink text-[11px] font-medium tracking-wide">
                                <span>⭐ VIP PRIVILEGE</span>
                            </div>
                            <h2 class="font-display font-medium text-2xl sm:text-3xl uppercase tracking-wider text-ink mt-2">
                                NGIZAN <span class="text-premium-gold-deep">PREMIUM</span>
                            </h2>
                            <p class="text-xs text-mute mt-1">Keistimewaan tak tertandingi untuk kolektor jersey sejati.</p>
                        </div>
                        <button type="button" @click="isOpen = false" class="text-mute hover:text-ink text-2xl font-bold transition">&times;</button>
                    </div>

                    {{-- Features List --}}
                    <div class="space-y-3 bg-soft-cloud p-4 sm:p-5 rounded-xl border border-hairline-soft text-xs">
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-ink text-white flex items-center justify-center shrink-0 font-bold text-xs">✓</div>
                            <div>
                                <strong class="text-ink">Diskon Otomatis 5%</strong>
                                <p class="text-[11px] text-mute">Potongan langsung 5% di setiap produk tanpa batas minimum transaksi.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-ink text-white flex items-center justify-center shrink-0 font-bold text-xs">✓</div>
                            <div>
                                <strong class="text-ink">Gratis Ongkir J&T Express</strong>
                                <p class="text-[11px] text-mute">Kemitraan resmi J&T Express flat Rp 0 ke seluruh pelosok Nusantara.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-ink text-white flex items-center justify-center shrink-0 font-bold text-xs">✓</div>
                            <div>
                                <strong class="text-ink">Prioritas Workshop Sablon</strong>
                                <p class="text-[11px] text-mute">Antrian nameset & patch diprioritaskan di workshop Ngizan.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Price Tag --}}
                    <div class="flex items-baseline justify-between border-t border-hairline-soft pt-4">
                        <div>
                            <span class="text-[11px] text-mute uppercase font-medium block">Biaya Langganan</span>
                            <div class="flex items-baseline gap-1 mt-0.5">
                                <span class="font-display font-medium text-3xl text-ink">Rp 100.000</span>
                                <span class="text-xs text-mute">/ tahun</span>
                            </div>
                        </div>
                        <span class="text-[11px] text-mute font-mono">Hanya ~Rp 8.300/bulan</span>
                    </div>

                    {{-- Action Button --}}
                    <div>
                        @guest
                            <a href="{{ route('login') }}" class="btn-primary w-full text-center py-3.5 text-xs font-medium uppercase tracking-wider rounded-full">
                                Masuk untuk Berlangganan &rarr;
                            </a>
                        @else
                            @if(auth()->user()->isPremiumActive())
                                <div class="p-3.5 bg-soft-cloud border border-hairline rounded-xl text-center">
                                    <p class="text-xs font-medium text-ink">
                                        ⭐ Anda adalah Member Premium Aktif hingga {{ auth()->user()->premium_until?->format('d F Y') }}
                                    </p>
                                    <p class="text-[11px] text-mute mt-0.5">Nikmati diskon 5% otomatis di setiap checkout.</p>
                                </div>
                            @else
                                <button type="button" @click="startSubscription()" :disabled="isLoading" class="btn-primary w-full py-3.5 text-xs font-medium uppercase tracking-wider rounded-full flex items-center justify-center gap-2">
                                    <span x-show="!isLoading">Berlangganan Sekarang via Midtrans</span>
                                    <span x-show="isLoading" class="inline-flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4 text-white" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
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
