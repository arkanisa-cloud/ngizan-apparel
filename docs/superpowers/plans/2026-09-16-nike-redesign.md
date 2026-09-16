# Redesain Seluruh UI Ngizan Apparel — Nike Editorial Commerce System

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Mengubah seluruh tampilan frontend Ngizan Apparel dari palet "Warm Luxury Sport" (`#EFEDE8`, `#101010`) menjadi sistem desain Nike Editorial Commerce (`#ffffff` canvas, `#111111` ink, `#f5f5f5` soft-cloud) sesuai `docs/DESIGN.md`, dengan prinsip *photography-first*, *pill-shaped CTAs*, *flat cards tanpa shadow*, dan *8px spacing system*.

**Architecture:** Pure CSS + Tailwind CSS token overhaul. Mengganti seluruh CSS variables, Tailwind config colors, typography stack (Archivo/Plus Jakarta Sans → Inter + Bebas Neue), border-radius (sharp → pill `rounded-full`), dan komponen button/card/navigation mengikuti DESIGN.md tokens secara verbatim.

**Tech Stack:** Laravel Blade, Tailwind CSS, Alpine.js, Google Fonts (Inter, Bebas Neue), Toastr.js.

**Spec:** `docs/DESIGN.md`

## Global Constraints

- **Canvas color**: `#ffffff` (bukan `#EFEDE8`)
- **Ink color**: `#111111` (bukan `#101010`)
- **Soft-cloud surface**: `#f5f5f5` — untuk product card image backgrounds, search pill, utility bar
- **Button shape**: Semua CTA harus pill-shaped `rounded-full` (30px). Tidak boleh ada button sharp-cornered
- **Card shape**: `rounded-none`, tanpa shadow, tanpa border — fotografi adalah kartu itu sendiri
- **Typography**: `Inter` (400/500/700) untuk body/button/caption, `Bebas Neue` (96px, line-height 0.9, uppercase) hanya untuk campaign hero headline
- **Spacing system**: 8px base. Section gap 48px. Card grid gutter 8px. PDP disclosure row padding 24px
- **No drop shadows pada retail chrome**. Satu-satunya depth cue = 1px `#cacacb` hairline divider dan 1px inset `#e5e5e5` bottom pada sticky bars
- **Sale color**: `#d30005` — hanya untuk harga diskon, bukan untuk background/badge
- **Premium gold accent**: tetap `#F59E0B` / `#D97706` untuk badge Ngizan Premium
- **J&T logistics red**: tetap `#ED1C24` untuk badge kemitraan J&T
- **Tailwind v3/v4** — mengikuti konfigurasi yang sudah ada
- **JANGAN MENGUBAH**: Logic PHP/Controller, routes, Model, Service layer, migrations, atau test files. Hanya file frontend (`.blade.php`, `.css`, `tailwind.config.js`, `.js`) yang berubah.
- **PERTAHANKAN**: Semua fungsionalitas Alpine.js (modal premium, checkout snap, review form, live nameset studio, cart add-to-cart, dll.) — hanya styling yang berubah.

---

## Proposed Changes

### Task 1: Design System Foundation — Tailwind Config, CSS Variables & Google Fonts

**Files:**
- Modify: `tailwind.config.js`
- Modify: `resources/css/app.css`

**Interfaces:**
- Produces: Design tokens yang dikonsumsi oleh seluruh view files (colors, fonts, spacing, radius)

- [ ] **Step 1: Update `tailwind.config.js`** — Ganti seluruh `colors` extend sesuai DESIGN.md tokens:

```javascript
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                canvas: '#ffffff',
                'soft-cloud': '#f5f5f5',
                ink: '#111111',
                charcoal: '#39393b',
                ash: '#4b4b4d',
                mute: '#707072',
                stone: '#9e9ea0',
                hairline: '#cacacb',
                'hairline-soft': '#e5e5e5',
                sale: '#d30005',
                'sale-deep': '#780700',
                success: '#007d48',
                'success-bright': '#1eaa52',
                info: '#1151ff',
                'info-deep': '#0034e3',
                'premium-gold': '#F59E0B',
                'premium-gold-deep': '#D97706',
                'jnt-red': '#ED1C24',
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['"Bebas Neue"', 'Anton', 'sans-serif'],
                jersey: ['"Bebas Neue"', 'sans-serif'],
            },
            borderRadius: {
                'pill': '30px',
                'pill-md': '24px',
                'pill-sm': '18px',
            },
            spacing: {
                'section': '48px',
            },
        },
    },
    plugins: [forms],
};
```

- [ ] **Step 2: Rewrite `resources/css/app.css`** — Ganti seluruh CSS variables dan komponen agar sesuai DESIGN.md:

```css
@tailwind base;
@tailwind components;
@tailwind utilities;

:root {
  --canvas: #ffffff;
  --soft-cloud: #f5f5f5;
  --ink: #111111;
  --charcoal: #39393b;
  --ash: #4b4b4d;
  --mute: #707072;
  --stone: #9e9ea0;
  --hairline: #cacacb;
  --hairline-soft: #e5e5e5;
  --sale: #d30005;
  --nav: 60px;
}

@layer base {
  body {
    background-color: var(--canvas);
    color: var(--ink);
    font-family: 'Inter', system-ui, sans-serif;
    -webkit-font-smoothing: antialiased;
    overflow-x: clip;
  }
}

/* Typography helpers */
.font-jersey { font-family: 'Bebas Neue', sans-serif; letter-spacing: 0.08em; }
.font-display { font-family: 'Bebas Neue', 'Anton', sans-serif; }
.font-body-strong { font-family: 'Inter', sans-serif; font-weight: 500; }

/* Container — Nike 1440px max */
.wrap { max-width: 1440px; margin: 0 auto; padding: 0 clamp(18px, 3.4vw, 80px); }

/* Label utility */
.lbl {
  font: 500 12px/1.5 'Inter', sans-serif;
  letter-spacing: 0;
  text-transform: uppercase;
}

/* ── Primary Nav ── */
.nav-link {
  font: 500 16px/1.5 'Inter', sans-serif;
  position: relative;
  padding: 6px 0;
  color: var(--ink);
  text-decoration: none;
}
.nav-link::after {
  content: '';
  position: absolute;
  left: 0; right: 0; bottom: 0;
  height: 2px;
  background: var(--ink);
  transform: scaleX(0);
  transform-origin: right;
  transition: transform .3s ease;
}
.nav-link:hover::after, .nav-link.active::after {
  transform: scaleX(1);
  transform-origin: left;
}

/* ── Nike Campaign Display ── */
.display-campaign {
  font-family: 'Bebas Neue', 'Anton', sans-serif;
  font-size: clamp(48px, 10vw, 96px);
  font-weight: 500;
  line-height: 0.9;
  letter-spacing: 0;
  text-transform: uppercase;
}

/* ── Button Primary (Pill, Ink) ── */
.btn-primary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  background: var(--ink);
  color: #ffffff;
  padding: 16px 32px;
  font: 500 16px/1.5 'Inter', sans-serif;
  border-radius: 9999px;
  border: none;
  cursor: pointer;
  transition: opacity .2s ease;
}
.btn-primary:hover { opacity: 0.85; }
.btn-primary:active { transform: scale(0.97); opacity: 0.7; }

/* ── Button Secondary (Pill, Soft-cloud) ── */
.btn-secondary {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  background: var(--soft-cloud);
  color: var(--ink);
  padding: 16px 32px;
  font: 500 16px/1.5 'Inter', sans-serif;
  border-radius: 9999px;
  border: none;
  cursor: pointer;
  transition: background .2s ease;
}
.btn-secondary:hover { background: #ececec; }

/* ── Button Outline on Image (White pill on photography) ── */
.btn-outline-image {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  background: #ffffff;
  color: var(--ink);
  padding: 12px 24px;
  font: 500 16px/1.5 'Inter', sans-serif;
  border-radius: 9999px;
  border: none;
  cursor: pointer;
  transition: opacity .2s ease;
}
.btn-outline-image:hover { opacity: 0.9; }

/* ── Filter Chip ── */
.filter-chip {
  display: inline-flex; align-items: center;
  padding: 8px 16px;
  font: 500 16px/1.5 'Inter', sans-serif;
  border-radius: 9999px;
  background: #ffffff;
  color: var(--ink);
  border: 1px solid var(--hairline);
  cursor: pointer;
  transition: all .2s;
}
.filter-chip.active, .filter-chip:hover {
  background: var(--ink);
  color: #ffffff;
  border-color: var(--ink);
}

/* ── Product Card (Nike flat, no shadow) ── */
.product-card { position: relative; }
.product-card .image-frame {
  position: relative;
  aspect-ratio: 1/1;
  overflow: hidden;
  background: var(--soft-cloud);
  border-radius: 0;
}
.product-card .img-front, .product-card .img-back {
  position: absolute; inset: 0;
  width: 100%; height: 100%;
  object-fit: cover;
  transition: opacity 0.5s ease, transform 0.6s ease;
}
.product-card .img-back { opacity: 0; }
.product-card:hover .img-front { opacity: 0; transform: scale(1.03); }
.product-card:hover .img-back  { opacity: 1; transform: scale(1.03); }

.product-card .quick-add {
  position: absolute;
  left: 8px; right: 8px; bottom: 8px;
  z-index: 10;
  background: var(--ink);
  color: #ffffff;
  padding: 12px;
  text-align: center;
  font: 500 14px/1.5 'Inter', sans-serif;
  border-radius: 9999px;
  opacity: 0; transform: translateY(8px);
  transition: all .3s ease;
}
.product-card:hover .quick-add { opacity: 1; transform: translateY(0); }

/* ── Live Nameset ── */
.nameset-stage {
  position: relative;
  aspect-ratio: 1/1;
  max-width: 440px;
  margin: 0 auto;
  background: var(--soft-cloud);
  border-radius: 0;
  overflow: hidden;
}
.nameset-stage img { width: 100%; height: 100%; object-fit: cover; }
.nameset-layer {
  position: absolute; inset: 0;
  display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  pointer-events: none; padding-top: 14%;
}
.nameset-text-name {
  font-family: 'Bebas Neue', sans-serif;
  font-size: clamp(30px, 4vw, 42px);
  color: #FFFFFF;
  letter-spacing: 0.16em;
  line-height: 1;
  text-shadow: 0 2px 6px rgba(0,0,0,0.7);
}
.nameset-text-number {
  font-family: 'Bebas Neue', sans-serif;
  font-size: clamp(86px, 12vw, 140px);
  color: #FFFFFF;
  line-height: 0.85;
  margin-top: 2px;
  text-shadow: 0 4px 12px rgba(0,0,0,0.75);
}

/* ── Hairline Dividers ── */
.hairline-b { border-bottom: 1px solid var(--hairline); }
.hairline-t { border-top: 1px solid var(--hairline); }

/* ── Toastr Nike Overrides ── */
#toast-container > div {
  opacity: 1 !important;
  box-shadow: none !important;
  border-radius: 9999px !important;
  padding: 12px 24px 12px 48px !important;
  font-family: 'Inter', sans-serif !important;
  font-size: 14px !important;
  font-weight: 500 !important;
}
.toast-success { background-color: var(--ink) !important; color: #ffffff !important; }
.toast-error   { background-color: var(--sale) !important; color: #ffffff !important; }
.toast-info    { background-color: var(--ink) !important; color: #ffffff !important; }
.toast-warning { background-color: var(--ink) !important; color: #ffffff !important; }
```

- [ ] **Step 3: Verify** — Run `npm run build` dan pastikan tidak ada error kompilasi Tailwind/Vite.

---

### Task 2: Customer Layout, Navigation & Footer — Nike Editorial Chrome

**Files:**
- Modify: `resources/views/layouts/customer.blade.php`

**Interfaces:**
- Consumes: Design tokens dari Task 1 (colors, fonts, button classes)
- Produces: Layout shell yang digunakan oleh seluruh halaman storefront customer

**Redesign Spec:**
- [ ] **Step 1: Update `<head>`** — Ganti Google Fonts dari `Archivo, Outfit, Plus Jakarta Sans, Bebas Neue` menjadi `Inter:wght@400;500;700&family=Bebas+Neue`
- [ ] **Step 2: Redesign Navigation Bar** — Mengikuti DESIGN.md `primary-nav` component:
  - Background: `bg-white` (bukan `bg-[#EFEDE8]/90`)
  - Height: 56-64px
  - Layout: Logo wordmark "NGIZAN" di kiri, nav links di tengah (`body-strong` 16px Inter 500), cluster icons kanan (search pill `rounded-pill-md bg-soft-cloud`, wishlist heart, cart bag icon)
  - Active nav link: 2px bottom underline `bg-ink`, tidak ada background fill
  - Semua CTA button berubah ke pill shape `rounded-full`
  - Tombol "Keranjang" menjadi icon bag `rounded-full` 40px (tanpa teks panjang)
  - Tombol "Gabung Premium" tetap gold pill tapi tanpa gradient — flat `bg-premium-gold text-ink rounded-full`
  - Cart badge: `bg-ink text-white` (bukan cyan)
  - Bottom border: `border-b border-hairline-soft` (bukan `border-black/10`)
- [ ] **Step 3: Redesign Footer** — Mengikuti DESIGN.md `footer` component:
  - Background: `bg-white` (bukan `bg-neutral-950`)
  - Top border: `1px solid hairline`
  - 4-column grid: header `body-strong ink`, links `caption-md mute`
  - Fine-print row: `utility-xs mute`
  - Warna teks: `text-mute` (bukan `text-neutral-400`)
- [ ] **Step 4: Update Premium Modal** — Ganti dark theme (`bg-neutral-950`) menjadi white/clean: `bg-white`, headings in `ink`, CTA pill `bg-ink text-white rounded-full`, feature badges `bg-soft-cloud`
- [ ] **Step 5: Verify** — `npm run build` clean, navigasi dan premium modal tetap berfungsi.

---

### Task 3: Homepage & Hero Section — Nike Campaign Editorial

**Files:**
- Modify: `resources/views/home.blade.php`

**Interfaces:**
- Consumes: Layout dari Task 2, design tokens dari Task 1
- Produces: Homepage yang dipakai sebagai landing page

**Redesign Spec:**
- [ ] **Step 1: Hero Section** — Mengikuti `campaign-tile` dari DESIGN.md:
  - Full-bleed editorial photography
  - `display-campaign` headline (Bebas Neue, 96px, line-height 0.9, uppercase) diposisikan lower-left
  - CTA: `btn-outline-image` pill (white bg on image) di bottom-left
  - Hapus wordmark overlay old-style, ganti ke Nike editorial approach
- [ ] **Step 2: Category Section** — `category-icon-card`: centered icon, `caption-md` label, background `canvas`, no radius
- [ ] **Step 3: Best Seller / Featured Products** — Section heading `heading-xl` (32px, Inter 500), `spacing-section` gap, 3-up product grid desktop → 2-up tablet → 1-up mobile, gutter `spacing-sm` (8px)
- [ ] **Step 4: Premium Membership Banner** — Mengikuti `member-benefit-card`: dark photographic card, `heading-lg` headline white, `btn-outline-image` CTA
- [ ] **Step 5: Verify** — `npm run build`, visual check homepage sections.

---

### Task 4: Product Card, Shop Page & Product Detail Page

**Files:**
- Modify: `resources/views/components/product-card.blade.php`
- Modify: `resources/views/customer/shop.blade.php`
- Modify: `resources/views/customer/product-detail.blade.php`

**Interfaces:**
- Consumes: Design tokens dari Task 1, layout dari Task 2
- Produces: Product browsing experience sesuai DESIGN.md

**Redesign Spec:**
- [ ] **Step 1: Product Card** — Mengikuti `product-card` dari DESIGN.md:
  - Container: `bg-canvas`, `rounded-none`, padding 0, no shadow
  - Image: full-bleed square (1:1) on `soft-cloud` background, `rounded-none`
  - Below image (8px gap between rows): swatch dots, product name `body-strong ink`, subtitle `caption-md mute`, price row `body-strong ink`
  - Sale/member price: discounted price `text-sale`, strike-through original `text-mute`, "5% OFF" in `text-sale` (bukan badge background)
  - Star rating: gold stars inline, `caption-md`, rating score + count
  - Quick-add overlay: pill-shaped `bg-ink text-white rounded-full`
  - Dual POV hover tetap berfungsi
- [ ] **Step 2: Shop Page** — Mengikuti PLP layout:
  - Sub-nav strip: breadcrumb `caption-md mute`, "Sort By" dropdown, "Hide Filters" toggle
  - Filter sidebar: ~220px fixed kiri desktop, `body-strong ink` section headers, `hairline` dividers
  - 3-up grid desktop, 2-up tablet, 1-up mobile, gutters `spacing-sm`
  - Filter chips: `filter-chip` component, `rounded-full`
- [ ] **Step 3: Product Detail Page** — Mengikuti PDP layout:
  - Left: square main image `soft-cloud` background + vertical thumbnail rail
  - Right: product name `heading-xl` (32px), subtitle `caption-md mute`, price `heading-lg` (24px)
  - Size/type selector: pill chips `filter-chip`
  - "Add to Cart" CTA: `btn-primary` full-width pill
  - `pdp-disclosure-row`: stacked "View Details", "Shipping & Returns", "Reviews (n)" with `hairline` dividers, `body-strong` label, chevron right-aligned
  - Review tab: rating summary, bar distribution, verified buyer reviews
  - Nameset studio: tetap berfungsi, background `soft-cloud` bukan dark
- [ ] **Step 4: Verify** — `npm run build`, test product card hover, add-to-cart, review tab.

---

### Task 5: Cart, Checkout & Order Pages

**Files:**
- Modify: `resources/views/customer/cart.blade.php`
- Modify: `resources/views/customer/checkout.blade.php`
- Modify: `resources/views/customer/orders/index.blade.php`
- Modify: `resources/views/customer/orders/show.blade.php`
- Modify: `resources/views/customer/profile.blade.php`
- Modify: `resources/views/customer/shipping-addresses/create.blade.php`
- Modify: `resources/views/customer/shipping-addresses/edit.blade.php`
- Modify: `resources/views/customer/shipping-addresses/index.blade.php`

**Interfaces:**
- Consumes: Design tokens dari Task 1, layout dari Task 2
- Produces: Checkout flow dan order management sesuai DESIGN.md

**Redesign Spec:**
- [ ] **Step 1: Cart Page** — White background, product rows dengan `hairline` dividers, price in `body-strong ink`, member discount `text-sale`, CTA "Checkout" → `btn-primary` pill full-width
- [ ] **Step 2: Checkout Page** — Clean form fields (no dark theme), J&T panel `soft-cloud` background, Midtrans CTA `btn-primary` pill
- [ ] **Step 3: Orders Index** — Clean table/cards, status badges `filter-chip` style, `hairline` dividers
- [ ] **Step 4: Order Detail** — Stepper progress bar clean ink/mute colors, tracking timeline `bg-soft-cloud` rows, review button `btn-primary` pill, J&T manifest table clean
- [ ] **Step 5: Profile & Shipping Address Pages** — Form styling clean white, `btn-primary` pill, `hairline` dividers
- [ ] **Step 6: Verify** — `npm run build`, test checkout flow, review submission, tracking display.

---

### Task 6: Auth Pages & Guest Layout

**Files:**
- Modify: `resources/views/layouts/guest.blade.php`
- Modify: `resources/views/auth/login.blade.php`
- Modify: `resources/views/auth/register.blade.php`
- Modify: `resources/views/auth/forgot-password.blade.php`
- Modify: `resources/views/auth/confirm-password.blade.php`
- Modify: `resources/views/auth/reset-password.blade.php`
- Modify: `resources/views/auth/verify-email.blade.php`

**Interfaces:**
- Consumes: Design tokens dari Task 1

**Redesign Spec:**
- [ ] **Step 1: Guest Layout** — White canvas, centered card form, no shadows, `hairline` borders
- [ ] **Step 2: Login Page** — Clean form, Google OAuth pill button `bg-soft-cloud text-ink rounded-full`, submit `btn-primary` pill, links `text-mute underline`
- [ ] **Step 3: Register & Other Auth Pages** — Same clean treatment, all pill buttons
- [ ] **Step 4: Verify** — `npm run build`, test login/register flow.

---

### Task 7: Admin Backoffice Layout & Pages

**Files:**
- Modify: `resources/views/layouts/admin.blade.php`
- Modify: `resources/views/admin/dashboard.blade.php`
- Modify: `resources/views/admin/orders/index.blade.php`
- Modify: `resources/views/admin/orders/show.blade.php`
- Modify: `resources/views/admin/orders/print-label.blade.php`
- Modify: `resources/views/admin/products/index.blade.php`
- Modify: `resources/views/admin/products/create.blade.php`
- Modify: `resources/views/admin/products/edit.blade.php`
- Modify: `resources/views/admin/products/show.blade.php`
- Modify: `resources/views/admin/categories/index.blade.php`
- Modify: `resources/views/admin/categories/create.blade.php`
- Modify: `resources/views/admin/categories/edit.blade.php`
- Modify: `resources/views/admin/reports/sales.blade.php`
- Modify: `resources/views/admin/reports/stock.blade.php`
- Modify: `resources/views/admin/stock-ins/index.blade.php`
- Modify: `resources/views/admin/stock-ins/create.blade.php`
- Modify: `resources/views/admin/stock-outs/index.blade.php`
- Modify: `resources/views/admin/stock-outs/create.blade.php`

**Interfaces:**
- Consumes: Design tokens dari Task 1

**Redesign Spec:**
- [ ] **Step 1: Admin Layout** — White sidebar, `ink` nav text, active item `bg-soft-cloud`, `hairline` dividers. Top bar white with `hairline-soft` bottom border
- [ ] **Step 2: Dashboard** — KPI cards `bg-soft-cloud rounded-none`, headings `heading-xl`, chart area clean, low-stock alert `text-sale`
- [ ] **Step 3: Order Management Pages** — Table `hairline` row dividers, status badge pills `filter-chip`, J&T resi input `btn-primary` pill
- [ ] **Step 4: Product CRUD Pages** — Form styling white canvas, `hairline` borders, image upload area `bg-soft-cloud`, submit `btn-primary` pill
- [ ] **Step 5: Category, Reports & Stock Pages** — Same clean Nike treatment
- [ ] **Step 6: Verify** — `npm run build`, test admin dashboard, order management, product CRUD.

---

## Verification Plan

### Automated Tests
```bash
php artisan test
```
- Semua 77 test yang ada harus tetap PASS (tidak ada perubahan logic/controller).

### Build Verification
```bash
npm run build
```
- Asset Vite harus compile tanpa error.

### Manual Verification
- Navigasi, footer, mobile drawer berfungsi
- Dual POV hover di product card berfungsi
- Live Nameset Studio berfungsi
- Midtrans Snap pop-up berfungsi
- Premium modal berfungsi
- Toastr notifications muncul
- Review submission form berfungsi
- J&T tracking timeline tampil
- Admin dashboard, order management, product CRUD berfungsi

> [!IMPORTANT]
> Redesain ini **HANYA** mengubah file frontend (Blade views, CSS, Tailwind config). Tidak ada perubahan pada Controllers, Models, Services, Routes, Migrations, atau Tests. Semua fungsionalitas yang sudah berjalan harus tetap 100% utuh.

> [!WARNING]
> File `docs/DESIGN.md` mereferensikan font proprietary Nike (Nike Futura ND, Helvetica Now). Substitusi yang digunakan adalah **Inter** (body/button/caption) dan **Bebas Neue** (display campaign headline) sesuai rekomendasi DESIGN.md bagian "Note on Font Substitutes".
