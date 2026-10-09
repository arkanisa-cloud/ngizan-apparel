<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NGIZAN APPAREL · Bespoke Football Kits & Archive Store')</title>
    <meta name="description" content="@yield('meta_description', 'Ngizan Apparel - Toko jersey sepak bola autentik, edisi player issue, dan arsip retro terkurasi.')">

    {{-- Preconnect & Google Fonts CDN with display=swap for instantaneous font render without FOUT --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    @stack('styles')
</head>

<body class="bg-canvas text-ink antialiased flex flex-col min-h-screen font-sans">

    @php
        $isHomeRoute = request()->routeIs('home');
    @endphp

    {{-- ===== TOP NAVIGATION BAR WITH SMART AUTO-HIDE & HERO COLOR MORPH ===== --}}
    <nav x-data="{
        isHome: {{ $isHomeRoute ? 'true' : 'false' }},
        isPastHero: {{ $isHomeRoute ? 'false' : 'true' }},
        isHidden: false,
        lastScrollY: 0,
        hideTimer: null,
        showTimer: null,
        mobileOpen: false,
        searchBarOpen: false,
        searchFocused: false,
        heroHeight: 600,
        init() {
            this.updateHeroHeight();
            this.handleScroll();
            window.addEventListener('resize', () => this.updateHeroHeight(), { passive: true });
            window.addEventListener('scroll', () => this.handleScroll(), { passive: true });
        },
        updateHeroHeight() {
            if (this.isHome) {
                const hero = document.getElementById('hero-section');
                this.heroHeight = hero ? (hero.offsetHeight - 64) : (window.innerHeight * 0.85);
            } else {
                this.heroHeight = 0;
            }
        },
        handleScroll() {
            const currentY = window.pageYOffset || document.documentElement.scrollTop;
            const diff = currentY - this.lastScrollY;
    
            // Color change threshold: exactly when hero section finishes
            if (this.isHome) {
                this.isPastHero = currentY >= this.heroHeight;
            } else {
                this.isPastHero = true;
            }
    
            // Zona Puncak Halaman: Selalu tampil dan reset semua timer
            if (currentY <= 60) {
                if (this.hideTimer) {
                    clearTimeout(this.hideTimer);
                    this.hideTimer = null;
                }
                if (this.showTimer) {
                    clearTimeout(this.showTimer);
                    this.showTimer = null;
                }
                this.isHidden = false;
            } else if (!this.mobileOpen && !this.searchFocused && !this.searchBarOpen) {
                if (diff > 6) {
                    // Scrolling ke bawah: Batalkan timer muncul jika ada
                    if (this.showTimer) {
                        clearTimeout(this.showTimer);
                        this.showTimer = null;
                    }
    
                    // Beri jeda 1 detik (1000ms) sebelum navbar mulai bergeser hilang
                    if (!this.isHidden && !this.hideTimer && currentY > 100) {
                        this.hideTimer = setTimeout(() => {
                            const nowY = window.pageYOffset || document.documentElement.scrollTop;
                            if (nowY > 100 && !this.mobileOpen && !this.searchFocused && !this.searchBarOpen) {
                                this.isHidden = true;
                            }
                            this.hideTimer = null;
                        }, 1000);
                    }
                } else if (diff < -6) {
                    // Scrolling ke atas: Batalkan timer hilang jika ada
                    if (this.hideTimer) {
                        clearTimeout(this.hideTimer);
                        this.hideTimer = null;
                    }
    
                    // Beri jeda 1 detik (1000ms) sebelum navbar meluncur muncul kembali
                    if (this.isHidden && !this.showTimer) {
                        this.showTimer = setTimeout(() => {
                            this.isHidden = false;
                            this.showTimer = null;
                        }, 1000);
                    }
                }
            }
    
            this.lastScrollY = Math.max(0, currentY);
        }
    }"
        :class="{
            '-translate-y-full': isHidden && !mobileOpen && !searchBarOpen,
            'translate-y-0': !isHidden || mobileOpen || searchBarOpen,
            'bg-white/90 backdrop-blur-xl border-b border-black/5 shadow-xs text-ink': isPastHero || !isHome ||
                mobileOpen || searchBarOpen,
            'bg-gradient-to-b from-black/75 via-black/30 to-transparent text-white border-b-0 border-transparent shadow-none':
                !isPastHero && isHome && !mobileOpen && !searchBarOpen
        }"
        class="fixed top-0 left-0 right-0 z-50 transform transition-[transform,background-color,border-color,color] duration-300 ease-[cubic-bezier(0.16,1,0.3,1)] {{ !$isHomeRoute ? 'bg-white/90 backdrop-blur-xl border-b border-black/5 shadow-xs text-ink' : 'bg-gradient-to-b from-black/75 via-black/30 to-transparent text-white border-b-0 border-transparent shadow-none' }}">

        <div class="wrap">
            <div class="flex items-center justify-between h-[64px] gap-2 sm:gap-6">

                {{-- Kiri: Animated Hamburger Trigger & Logo Wordmark --}}
                <div class="flex items-center gap-2 sm:gap-3">
                    {{-- Mobile Animated Hamburger Button --}}
                    <div class="lg:hidden flex items-center">
                        <button type="button" @click="mobileOpen = !mobileOpen; if (mobileOpen) searchBarOpen = false;"
                            class="w-9 h-9 flex flex-col items-center justify-center gap-[5px] p-1.5 rounded-full focus:outline-none transition-colors"
                            :class="{
                                'text-white hover:bg-white/15': isHome && !isPastHero && !mobileOpen && !searchBarOpen,
                                'text-ink hover:bg-neutral-100': isPastHero || !isHome || mobileOpen || searchBarOpen
                            }"
                            aria-label="Toggle Menu">
                            {{-- Top bar --}}
                            <span
                                class="w-5 h-[2px] rounded-full transition-all duration-300 ease-in-out transform origin-center {{ !$isHomeRoute ? 'bg-ink' : 'bg-white' }}"
                                :class="{
                                    'rotate-45 translate-y-[7px] bg-ink': mobileOpen,
                                    'bg-white': !mobileOpen && isHome && !isPastHero && !searchBarOpen,
                                    'bg-ink': !mobileOpen && (isPastHero || !isHome || searchBarOpen)
                                }"></span>
                            {{-- Middle bar --}}
                            <span
                                class="w-5 h-[2px] rounded-full transition-all duration-200 ease-in-out {{ !$isHomeRoute ? 'bg-ink' : 'bg-white' }}"
                                :class="{
                                    'opacity-0 scale-0': mobileOpen,
                                    'bg-white': !mobileOpen && isHome && !isPastHero && !searchBarOpen,
                                    'bg-ink': !mobileOpen && (isPastHero || !isHome || searchBarOpen)
                                }"></span>
                            {{-- Bottom bar --}}
                            <span
                                class="w-5 h-[2px] rounded-full transition-all duration-300 ease-in-out transform origin-center {{ !$isHomeRoute ? 'bg-ink' : 'bg-white' }}"
                                :class="{
                                    '-rotate-45 -translate-y-[7px] bg-ink': mobileOpen,
                                    'bg-white': !mobileOpen && isHome && !isPastHero && !searchBarOpen,
                                    'bg-ink': !mobileOpen && (isPastHero || !isHome || searchBarOpen)
                                }"></span>
                        </button>
                    </div>

                    <a href="{{ route('home') }}"
                        class="font-display font-medium text-2xl sm:text-3xl tracking-wider uppercase transition inline-block select-none {{ !$isHomeRoute ? 'text-ink' : 'text-white' }}"
                        :class="{
                            'text-white hover:text-white/85': isHome && !isPastHero && !mobileOpen && !searchBarOpen,
                            'text-ink hover:opacity-80': isPastHero || !isHome || mobileOpen || searchBarOpen
                        }">
                        NGIZAN
                    </a>
                </div>

                {{-- Tengah: 3 Nav Links (Home, Katalog, Contact) --}}
                <div class="hidden lg:flex items-center gap-8 text-[14px] font-medium tracking-wide">
                    {{-- 1. Home --}}
                    <a href="{{ route('home') }}"
                        :class="{
                            'text-white hover:text-white': isHome && !isPastHero && !mobileOpen,
                            'text-ink hover:text-black': isPastHero || !isHome || mobileOpen,
                            'font-semibold opacity-100': {{ $isHomeRoute ? 'true' : 'false' }},
                            'opacity-75 hover:opacity-100': !{{ $isHomeRoute ? 'true' : 'false' }}
                        }"
                        class="relative py-1 transition duration-200 group {{ $isHomeRoute ? 'text-white font-semibold' : 'text-ink opacity-75 hover:opacity-100' }}">
                        <span>Home</span>
                        <span
                            class="absolute left-0 bottom-0 w-full h-[2px] transition-transform duration-300 origin-left {{ $isHomeRoute ? 'scale-x-100 bg-white shadow-xs' : 'scale-x-0 group-hover:scale-x-100 bg-ink' }}"
                            :class="{
                                'bg-white shadow-xs': isHome && !isPastHero && !mobileOpen,
                                'bg-ink': isPastHero || !isHome || mobileOpen
                            }"></span>
                    </a>

                    {{-- 2. Katalog --}}
                    <a href="{{ route('shop.index') }}"
                        :class="{
                            'text-white hover:text-white': isHome && !isPastHero && !mobileOpen,
                            'text-ink hover:text-black': isPastHero || !isHome || mobileOpen,
                            'font-semibold opacity-100': {{ request()->routeIs('shop.index') ? 'true' : 'false' }},
                            'opacity-75 hover:opacity-100': !{{ request()->routeIs('shop.index') ? 'true' : 'false' }}
                        }"
                        class="relative py-1 transition duration-200 group {{ request()->routeIs('shop.index') ? 'text-ink font-semibold opacity-100' : ($isHomeRoute ? 'text-white opacity-75 hover:opacity-100' : 'text-ink opacity-75 hover:opacity-100') }}">
                        <span>Katalog</span>
                        <span
                            class="absolute left-0 bottom-0 w-full h-[2px] transition-transform duration-300 origin-left {{ request()->routeIs('shop.index') ? 'scale-x-100 bg-ink' : 'scale-x-0 group-hover:scale-x-100 ' . ($isHomeRoute ? 'bg-white shadow-xs' : 'bg-ink') }}"
                            :class="{
                                'bg-white shadow-xs': isHome && !isPastHero && !mobileOpen,
                                'bg-ink': isPastHero || !isHome || mobileOpen
                            }"></span>
                    </a>

                    {{-- 3. Contact --}}
                    <a href="{{ $isHomeRoute ? '#contact' : route('home') . '#contact' }}"
                        :class="{
                            'text-white hover:text-white': isHome && !isPastHero && !mobileOpen,
                            'text-ink hover:text-black': isPastHero || !isHome || mobileOpen
                        }"
                        class="relative py-1 opacity-75 hover:opacity-100 transition duration-200 group {{ $isHomeRoute ? 'text-white' : 'text-ink' }}">
                        <span>Contact</span>
                        <span
                            class="absolute left-0 bottom-0 w-full h-[2px] transition-transform duration-300 origin-left scale-x-0 group-hover:scale-x-100"
                            :class="{
                                'bg-white shadow-xs': isHome && !isPastHero && !mobileOpen,
                                'bg-ink': isPastHero || !isHome || mobileOpen
                            }"></span>
                    </a>
                </div>

                {{-- Kanan: Search Icon (Mobile) / Search Pill (Desktop), Cart Icon, Login Button --}}
                <div class="flex items-center gap-2 sm:gap-3">

                    {{-- 1. Desktop Pill Search Form --}}
                    <form action="{{ route('shop.index') }}" method="GET"
                        class="relative hidden md:flex items-center">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari jersey..." @focus="searchFocused = true" @blur="searchFocused = false"
                            :class="{
                                'bg-white/15 text-white placeholder-white/65 border-white/25 focus:bg-white/25 focus:border-white/50 shadow-inner': isHome &&
                                    !isPastHero && !mobileOpen && !searchBarOpen,
                                'bg-neutral-100 text-ink placeholder-neutral-400 border-neutral-200 focus:bg-white focus:border-ink/20 focus:ring-1 focus:ring-ink/10': isPastHero ||
                                    !isHome || mobileOpen || searchBarOpen
                            }"
                            class="w-36 lg:w-56 focus:w-48 lg:focus:w-64 pl-8 lg:pl-9 pr-3 py-1.5 text-xs rounded-full border transition-all duration-300 outline-none backdrop-blur-xs {{ $isHomeRoute ? 'bg-white/15 text-white placeholder-white/65 border-white/25' : 'bg-neutral-100 text-ink placeholder-neutral-400 border-neutral-200' }}">

                        <button type="submit"
                            class="absolute left-2.5 sm:left-3 top-1/2 -translate-y-1/2 focus:outline-none"
                            aria-label="Cari">
                            <svg class="w-3.5 h-3.5 transition-colors duration-200"
                                :class="{
                                    'text-white/80': isHome && !isPastHero && !mobileOpen && !searchBarOpen,
                                    'text-neutral-500': isPastHero || !isHome || mobileOpen || searchBarOpen
                                }"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </form>

                    {{-- 2. Mobile Simple Search Icon Button (Tepat di samping logo Cart) --}}
                    <button type="button"
                        @click="searchBarOpen = !searchBarOpen; if (searchBarOpen) { mobileOpen = false; $nextTick(() => $refs.mobileSearchInput?.focus()); }"
                        :class="{
                            'text-white hover:bg-white/20 bg-white/10 border-white/20': isHome && !isPastHero && !
                                mobileOpen && !searchBarOpen,
                            'text-ink hover:bg-neutral-100 bg-neutral-100/70 border-neutral-200/70': isPastHero || !
                                isHome || mobileOpen || searchBarOpen
                        }"
                        class="md:hidden relative w-9 h-9 rounded-full flex items-center justify-center transition-all duration-200 border shrink-0 backdrop-blur-xs focus:outline-none {{ $isHomeRoute ? 'text-white bg-white/10 border-white/20 hover:bg-white/20' : 'text-ink bg-neutral-100/70 border-neutral-200/70 hover:bg-neutral-100' }}"
                        title="Cari Jersey" aria-label="Cari Jersey">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>

                    {{-- 3. Keranjang Belanja (Cart Icon Doang) --}}
                    @php
                        $cartCount =
                            auth()->check() && auth()->user()->cart ? auth()->user()->cart->items->sum('quantity') : 0;
                    @endphp
                    <a href="{{ route('customer.cart.index') }}"
                        :class="{
                            'text-white hover:bg-white/20 bg-white/10 border-white/20': isHome && !isPastHero && !
                                mobileOpen && !searchBarOpen,
                            'text-ink hover:bg-neutral-100 bg-neutral-100/70 border-neutral-200/70': isPastHero || !
                                isHome || mobileOpen || searchBarOpen
                        }"
                        class="relative w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center transition-all duration-200 border shrink-0 backdrop-blur-xs {{ $isHomeRoute ? 'text-white bg-white/10 border-white/20 hover:bg-white/20' : 'text-ink bg-neutral-100/70 border-neutral-200/70 hover:bg-neutral-100' }}"
                        title="Keranjang Belanja" aria-label="Keranjang Belanja">
                        <svg class="w-4 h-4 sm:w-[18px] sm:h-[18px]" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        @if ($cartCount > 0)
                            <span
                                class="absolute -top-1 -right-1 min-w-[17px] h-[17px] px-1 rounded-full bg-red-600 text-white text-[9px] font-bold flex items-center justify-center shadow-md">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>

                    {{-- 4. 1 Tombol Login / Profil Dropdown --}}
                    @guest
                        <a href="{{ route('login') }}"
                            :class="{
                                'bg-white text-ink hover:bg-white/90 shadow-sm': isHome && !isPastHero && !mobileOpen &&
                                    !searchBarOpen,
                                'bg-ink text-white hover:bg-neutral-800 shadow-sm': isPastHero || !isHome ||
                                    mobileOpen || searchBarOpen
                            }"
                            class="px-3 sm:px-5 py-1.5 sm:py-2 rounded-full text-[11px] sm:text-xs font-semibold tracking-wide uppercase transition duration-200 inline-flex items-center justify-center shrink-0 {{ $isHomeRoute ? 'bg-white text-ink hover:bg-white/90 shadow-sm' : 'bg-ink text-white hover:bg-neutral-800 shadow-sm' }}">
                            Masuk
                        </a>
                    @else
                        <div class="relative" x-data="{ userMenuOpen: false }">
                            <button @click="userMenuOpen = !userMenuOpen"
                                :class="{
                                    'bg-white/15 text-white border-white/25 hover:bg-white/25': isHome && !isPastHero &&
                                        !mobileOpen && !searchBarOpen,
                                    'bg-white text-ink border-neutral-200 hover:border-ink shadow-xs': isPastHero || !
                                        isHome || mobileOpen || searchBarOpen
                                }"
                                class="flex items-center gap-1.5 sm:gap-2 px-2 sm:px-3 py-1 sm:py-1.5 border rounded-full transition text-xs font-medium backdrop-blur-xs {{ $isHomeRoute ? 'bg-white/15 text-white border-white/25 hover:bg-white/25' : 'bg-white text-ink border-neutral-200 hover:border-ink shadow-xs' }}">
                                @if (Auth::user()->avatar)
                                    <img src="{{ Auth::user()->avatar }}" class="w-5 h-5 rounded-full object-cover">
                                @else
                                    <span
                                        class="w-5 h-5 rounded-full bg-ink text-white text-[10px] flex items-center justify-center font-bold">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </span>
                                @endif
                                <span
                                    class="max-w-[70px] sm:max-w-[100px] truncate hidden md:inline-block">{{ Auth::user()->name }}</span>
                                <svg class="w-3 h-3 opacity-70 hidden sm:inline-block" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="userMenuOpen" @click.away="userMenuOpen = false" x-cloak
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                                class="absolute right-0 mt-2 w-52 bg-white text-ink border border-neutral-200 rounded-2xl py-2 z-50 text-xs shadow-xl backdrop-blur-xl">

                                <div class="px-4 py-2.5 border-b border-hairline-soft">
                                    <p class="font-bold text-ink truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-[11px] text-mute truncate">{{ Auth::user()->email }}</p>
                                    @if (Auth::user()->isPremiumActive())
                                        <span
                                            class="inline-flex items-center gap-1 mt-1.5 px-2.5 py-0.5 bg-amber-50 border border-amber-200/60 text-amber-900 rounded-full text-[10px] font-bold">
                                            ⭐ Member Premium
                                        </span>
                                    @endif
                                </div>

                                <div class="py-1">
                                    @if (Auth::user()->isAdmin())
                                        <a href="{{ route('admin.dashboard') }}"
                                            class="flex items-center gap-2.5 px-4 py-2 hover:bg-soft-cloud font-medium text-ink transition">
                                            <svg class="w-4 h-4 text-mute" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                            <span>Backoffice Admin</span>
                                        </a>
                                    @endif

                                    <a href="{{ route('customer.orders.index') }}"
                                        class="flex items-center gap-2.5 px-4 py-2 hover:bg-soft-cloud text-ink font-medium transition">
                                        <svg class="w-4 h-4 text-mute" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                        </svg>
                                        <span>Pesanan Saya</span>
                                    </a>

                                    <a href="{{ route('customer.addresses.index') }}"
                                        class="flex items-center gap-2.5 px-4 py-2 hover:bg-soft-cloud text-ink font-medium transition">
                                        <svg class="w-4 h-4 text-mute" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span>Alamat Pengiriman</span>
                                    </a>

                                    <a href="{{ route('profile.edit') }}"
                                        class="flex items-center gap-2.5 px-4 py-2 hover:bg-soft-cloud text-ink font-medium transition">
                                        <svg class="w-4 h-4 text-mute" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span>Profil & Keanggotaan</span>
                                    </a>

                                    @if (!Auth::user()->isPremiumActive())
                                        <button type="button"
                                            @click="$dispatch('open-premium-modal'); userMenuOpen = false"
                                            class="flex items-center justify-between w-full text-left px-4 py-2 hover:bg-amber-50 text-amber-900 font-semibold transition border-t border-b border-amber-100/60 my-0.5">
                                            <span>Gabung Ngizan Premium</span>
                                            <span class="text-amber-600 text-xs">⭐</span>
                                        </button>
                                    @endif
                                </div>

                                <div class="border-t border-hairline-soft pt-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="w-full text-left px-4 py-2 hover:bg-rose-50 text-sale font-medium transition flex items-center gap-2.5">
                                            <svg class="w-4 h-4 text-sale" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            <span>Keluar</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endguest

                </div>

            </div>
        </div>

        {{-- Mobile Expandable Floating Search Bar (Tapped from Search Icon) --}}
        <div x-show="searchBarOpen" x-cloak @click.away="searchBarOpen = false"
            x-transition:enter="transition ease-out duration-200 transform"
            x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150 transform"
            x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
            class="md:hidden absolute top-[64px] left-0 right-0 p-3 bg-white/95 backdrop-blur-2xl border-t border-b border-black/5 shadow-xl z-50">
            <form action="{{ route('shop.index') }}" method="GET" class="relative flex items-center">
                <input x-ref="mobileSearchInput" type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari jersey klub, timnas, retro..."
                    class="w-full bg-neutral-100 text-ink placeholder-neutral-400 pl-10 pr-10 py-2.5 text-xs rounded-full border border-neutral-200 focus:bg-white focus:border-ink/30 focus:outline-none transition">
                <button type="submit" class="absolute left-3.5 top-1/2 -translate-y-1/2 text-neutral-400"
                    aria-label="Submit Search">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
                <button type="button" @click="searchBarOpen = false"
                    class="absolute right-3.5 top-1/2 -translate-y-1/2 text-neutral-400 hover:text-ink text-base font-bold leading-none"
                    aria-label="Close Search">
                    &times;
                </button>
            </form>
        </div>

        {{-- Mobile Full-Height Smooth Slide Overlay & Drawer --}}
        <div x-show="mobileOpen" x-cloak x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 -translate-y-3" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-3"
            class="lg:hidden fixed top-[64px] left-0 right-0 max-h-[calc(100vh-64px)] overflow-y-auto bg-white/95 backdrop-blur-2xl border-t border-b border-black/5 p-4 shadow-xl text-ink space-y-3">

            {{-- Mobile Main Navlinks (Simple & Sederhana) --}}
            <nav class="flex flex-col divide-y divide-neutral-100 text-xs uppercase tracking-wider font-semibold">
                <a href="{{ route('home') }}" @click="mobileOpen = false"
                    class="flex items-center justify-between py-2.5 transition {{ request()->routeIs('home') ? 'text-ink font-bold' : 'text-neutral-600 hover:text-ink' }}">
                    <span>Home</span>
                    <span class="text-[10px] text-neutral-400 font-normal">01</span>
                </a>

                <a href="{{ route('shop.index') }}" @click="mobileOpen = false"
                    class="flex items-center justify-between py-2.5 transition {{ request()->routeIs('shop.index') ? 'text-ink font-bold' : 'text-neutral-600 hover:text-ink' }}">
                    <span>Katalog</span>
                    <span class="text-[10px] text-neutral-400 font-normal">02</span>
                </a>

                <a href="{{ request()->routeIs('home') ? '#contact' : route('home') . '#contact' }}"
                    @click="mobileOpen = false"
                    class="flex items-center justify-between py-2.5 text-neutral-600 hover:text-ink transition">
                    <span>Contact</span>
                    <span class="text-[10px] text-neutral-400 font-normal">03</span>
                </a>
            </nav>

            {{-- Mobile Quick Status --}}
            <div
                class="pt-2 border-t border-neutral-100 flex items-center justify-between text-[11px] text-neutral-400">
                <span>Free Shipping J&T Express</span>
                <span class="font-semibold text-neutral-600 uppercase tracking-widest text-[10px]">Ngizan
                    Apparel</span>
            </div>
        </div>
    </nav>

    {{-- ===== MAIN CONTENT WRAPPER ===== --}}
    <main class="flex-grow {{ request()->routeIs('home') ? '' : 'pt-[64px]' }}">
        @yield('content')
    </main>

    @if (request()->routeIs('checkout.index') ||
            request()->routeIs('customer.checkout.index') ||
            request()->routeIs('checkout.*') ||
            request()->routeIs('customer.checkout.*'))
        {{-- ===== DISTRACTION-FREE CHECKOUT MINIMAL FOOTER ===== --}}
        <footer class="border-t border-hairline-soft bg-white text-ink py-6 text-xs mt-8">
            <div class="wrap flex flex-col sm:flex-row justify-between items-center gap-3 text-xs text-mute">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    <span>Enkripsi 256-Bit SSL · Pembayaran Aman Terverifikasi Midtrans</span>
                </div>
                <p>&copy; {{ date('Y') }} NGIZAN APPAREL. Seluruh hak cipta dilindungi.</p>
            </div>
        </footer>
    @else
        {{-- ===== NIKE EDITORIAL FOOTER ===== --}}
        <footer id="contact"
            class="border-t border-hairline bg-white text-ink pt-10 pb-10 sm:pt-16 sm:pb-12 text-xs mt-5 sm:mt-8 scroll-mt-20">
            <div class="wrap grid grid-cols-1 md:grid-cols-4 gap-10 mb-12">
                <div>
                    <div class="font-display font-medium text-2xl tracking-wider text-ink uppercase mb-3">
                        NGIZAN APPAREL
                    </div>
                    <p class="text-mute leading-relaxed">
                        Penyedia jersey sepak bola autentik, edisi player issue, dan arsip retro terkurasi berstandar
                        editorial.
                    </p>
                </div>

                <div>
                    <div class="font-medium text-sm text-ink mb-4">Koleksi Ngizan Apparel</div>
                    <ul class="space-y-2.5 text-mute">
                        <li><a href="{{ route('shop.index', ['category' => 'jersey']) }}"
                                class="hover:text-ink transition">Jersey Berkualitas Tinggi</a></li>
                        <li><a href="{{ route('shop.index', ['category' => 'tactical-board']) }}"
                                class="hover:text-ink transition">Tactical Board</a></li>
                        <li><a href="{{ route('shop.index', ['category' => 'kaos-kaki']) }}"
                                class="hover:text-ink transition">Kaos Kaki</a></li>
                        <li><a href="{{ route('shop.index', ['category' => 'celana']) }}"
                                class="hover:text-ink transition">Celana</a></li>
                    </ul>
                </div>

                <div>
                    <div class="font-medium text-sm text-ink mb-4">Layanan Pelanggan</div>
                    <ul class="space-y-2.5 text-mute">
                        <li><a href="{{ route('shop.index') }}" class="hover:text-ink transition">Kustomisasi Sablon
                                &
                                Nameset</a></li>
                        <li><a href="{{ route('customer.orders.index') }}" class="hover:text-ink transition">Lacak
                                Pengiriman Pesanan</a></li>
                        <li><a href="{{ route('shop.index') }}" class="hover:text-ink transition">Panduan Ukuran
                                (Size
                                Chart)</a></li>
                        <li><a href="{{ route('auth.google') }}" class="hover:text-ink transition">Login 1-Klik
                                Google</a></li>
                    </ul>
                </div>

                <div>
                    <div class="font-medium text-sm text-ink mb-4">Kontak & Workshop</div>
                    <p class="text-mute leading-relaxed">
                        WhatsApp: <span class="text-ink font-medium">+62 812-3456-7890</span><br>
                        Email: <span class="text-ink font-medium">ngizanapparel@gmail.com</span><br>
                        Workshop: Yogyakarta, Indonesia
                    </p>
                </div>
            </div>

            <div
                class="wrap border-t border-hairline-soft pt-8 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-mute">
                <p>&copy; {{ date('Y') }} NGIZAN APPAREL. Seluruh hak cipta dilindungi.</p>
                <p class="font-medium text-ink tracking-wide uppercase">Bespoke Football Kits & Archive Store</p>
            </div>
        </footer>
    @endif

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
    <script
        src="{{ config('services.midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
        data-client-key="{{ config('services.midtrans.client_key') }}"></script>

    {{-- ===== NGIZAN PREMIUM MEMBERSHIP MODAL (HIGH-FASHION ATHLETIC EDITORIAL) ===== --}}
    <div x-data="{
        isOpen: false,
        isLoading: false,
        init() {
            {{-- Auto-popup HANYA di Homepage, 1x per sesi, jika user belum berlangganan premium --}}
            @if (request()->routeIs('home') && (!auth()->check() || !auth()->user()->isPremiumActive())) if (!sessionStorage.getItem('ngizan_premium_modal_seen')) {
                    setTimeout(() => {
                        this.isOpen = true;
                        sessionStorage.setItem('ngizan_premium_modal_seen', 'true');
                    }, 3500);
                } @endif
        },
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
                            onSuccess: async () => {
                                try {
                                    await fetch('{{ route('customer.premium.sync') }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                            'Accept': 'application/json'
                                        }
                                    });
                                } catch (e) {
                                    console.error('Sync premium error:', e);
                                }
                                toastr.success('Selamat! Pembayaran berhasil. Akun Anda kini menjadi Ngizan Premium.');
                                setTimeout(() => window.location.reload(), 1200);
                            },
                            onPending: async () => {
                                try {
                                    await fetch('{{ route('customer.premium.sync') }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                            'Accept': 'application/json'
                                        }
                                    });
                                } catch (e) {
                                    console.error('Sync premium error:', e);
                                }
                                toastr.info('Menunggu penyelesaian pembayaran.');
                                setTimeout(() => window.location.reload(), 1200);
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
    }" @open-premium-modal.window="isOpen = true" x-cloak>
        {{-- Backdrop Overlay --}}
        <div x-show="isOpen" x-cloak style="display: none;" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6 bg-black/75 backdrop-blur-sm overflow-y-auto">

            {{-- Modal Box Container --}}
            <div @click.away="isOpen = false" x-show="isOpen" x-cloak style="display: none;"
                x-transition:enter="transition cubic-bezier(0.16, 1, 0.3, 1) duration-350"
                x-transition:enter-start="opacity-0 scale-95 translate-y-6"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-6"
                class="relative max-w-lg w-full bg-white text-ink rounded-3xl p-6 sm:p-8 shadow-2xl border border-hairline overflow-hidden my-auto">

                {{-- Close Button --}}
                <button type="button" @click="isOpen = false"
                    class="absolute top-5 right-5 w-8 h-8 rounded-full bg-soft-cloud hover:bg-neutral-200 text-ink flex items-center justify-center text-lg font-bold transition focus:outline-none"
                    aria-label="Tutup Modal">
                    &times;
                </button>

                <div class="space-y-6">
                    {{-- Header with Badge & Display Headline --}}
                    <div>
                        <div
                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 border border-amber-200/60 text-amber-900 text-[10px] font-bold tracking-[0.15em] uppercase">
                            <span>⭐ MEMBER PRIVILEGE</span>
                        </div>
                        <h2
                            class="font-display font-medium text-3xl sm:text-4xl uppercase tracking-wider text-ink mt-2.5">
                            NGIZAN <span class="text-amber-500">PREMIUM</span>
                        </h2>
                        <p class="text-xs sm:text-sm text-mute leading-relaxed mt-1">
                            Dapatkan keuntungan eksklusif potongan harga otomatis 5% di setiap pembelian seluruh jersey
                            dan produk tanpa batas minimum transaksi.
                        </p>
                    </div>

                    {{-- Features Benefit Box --}}
                    <div class="bg-soft-cloud/90 p-5 rounded-2xl border border-hairline-soft space-y-3.5">
                        <div class="flex items-start gap-3.5">
                            <div
                                class="w-8 h-8 rounded-full bg-ink text-amber-400 flex items-center justify-center shrink-0 font-bold text-sm shadow-xs">
                                %
                            </div>
                            <div class="space-y-1">
                                <strong class="text-ink text-xs sm:text-sm font-bold uppercase tracking-wide block">
                                    Diskon 5% Disetiap Pembelian
                                </strong>
                                <p class="text-[11px] sm:text-xs text-mute leading-relaxed">
                                    Potongan harga 5% langsung otomatis terpasang pada semua produk di katalog,
                                    keranjang belanja, hingga tahap checkout.
                                </p>
                            </div>
                        </div>

                        <div
                            class="pt-3 border-t border-hairline-soft/80 grid grid-cols-2 gap-2 text-[11px] text-mute">
                            <div class="flex items-center gap-1.5">
                                <span class="text-ink font-bold">✓</span>
                                <span>Semua Produk Katalog</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-ink font-bold">✓</span>
                                <span>Tanpa Minimum Belanja</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-ink font-bold">✓</span>
                                <span>Tanpa Kuota / Kode Kupon</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-ink font-bold">✓</span>
                                <span>Aktif Selama 365 Hari</span>
                            </div>
                        </div>
                    </div>

                    {{-- Transparent Pricing Box --}}
                    <div class="flex items-center justify-between p-4 bg-neutral-900 text-white rounded-2xl">
                        <div>
                            <span class="text-[10px] text-neutral-400 uppercase tracking-widest font-bold block">Biaya
                                Keanggotaan</span>
                            <div class="flex items-baseline gap-1 mt-0.5">
                                <span class="font-display font-medium text-2xl sm:text-3xl text-white">Rp
                                    100.000</span>
                                <span class="text-[11px] text-neutral-400 font-sans">/ 1 tahun penuh</span>
                            </div>
                        </div>
                    </div>

                    {{-- Action CTA --}}
                    <div class="space-y-2">
                        @guest
                            <a href="{{ route('login') }}"
                                class="btn-primary w-full py-4 text-xs font-bold uppercase tracking-[0.15em] rounded-full flex items-center justify-center gap-2 shadow-lg hover:bg-neutral-800 transition">
                                Masuk untuk Berlangganan &rarr;
                            </a>
                        @else
                            @if (auth()->user()->isPremiumActive())
                                <div class="p-4 bg-amber-50 border border-amber-200/60 rounded-2xl text-center">
                                    <p class="text-xs font-bold text-amber-950 flex items-center justify-center gap-1.5">
                                        <span>⭐</span> Anda adalah Member Premium Aktif
                                    </p>
                                    <p class="text-[11px] text-amber-800 mt-1">
                                        Masa aktif berlaku hingga
                                        <strong>{{ auth()->user()->premium_until?->format('d F Y') }}</strong>.
                                    </p>
                                </div>
                            @else
                                <button type="button" @click="startSubscription()" :disabled="isLoading"
                                    class="btn-primary w-full py-4 text-xs font-bold uppercase tracking-[0.15em] rounded-full flex items-center justify-center gap-2 shadow-lg hover:bg-neutral-800 transition cursor-pointer disabled:opacity-50">
                                    <span x-show="!isLoading" class="flex items-center gap-1.5">
                                        <span>⭐</span> Aktifkan Membership Sekarang
                                    </span>
                                    <span x-show="isLoading" class="inline-flex items-center gap-2"
                                        style="display: none;">
                                        <svg class="animate-spin h-4 w-4 text-white" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        Menyiapkan Pembayaran Midtrans...
                                    </span>
                                </button>
                            @endif
                        @endguest

                        <button type="button" @click="isOpen = false"
                            class="w-full text-center py-2 text-[11px] text-mute hover:text-ink font-medium tracking-wide transition">
                            Nanti saja
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>

</html>
