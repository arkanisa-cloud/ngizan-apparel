---
version: 1.0.0
name: Ngizan-Apparel-Design-System
description: |
  A bespoke football jersey commerce and kit-customization design system built on high-fashion athletic editorial aesthetics. Features extreme typographic contrast — towering uppercase display headlines paired with Swiss-precision neutral UI chrome, dynamic auto-hide glassmorphism navigation, full-bleed campaign photography, soft-cloud product stages, pill-shaped action buttons, and a strict 40px/64px mathematical vertical rhythm.

colors:
  primary: "#111111"
  on-primary: "#ffffff"
  canvas: "#ffffff"
  soft-cloud: "#f5f5f5"
  canvas-card: "#f5f5f5"
  ink: "#111111"
  charcoal: "#39393b"
  ash: "#4b4b4d"
  mute: "#707072"
  stone: "#9e9ea0"
  hairline: "#cacacb"
  hairline-soft: "#e5e5e5"
  sale: "#d30005"
  sale-deep: "#780700"
  success: "#007d48"
  success-bright: "#1eaa52"
  info: "#1151ff"
  info-deep: "#0034e3"
  premium-gold: "#d97706"
  premium-gold-soft: "#fef3c7"
  premium-gold-deep: "#92400e"
  glass-surface: "rgba(255, 255, 255, 0.90)"
  glass-border: "rgba(0, 0, 0, 0.05)"
  hero-overlay: "rgba(0, 0, 0, 0.75)"

typography:
  display-campaign:
    fontFamily: "Bebas Neue, sans-serif"
    fontSize: 96px
    fontWeight: 500
    lineHeight: 0.9
    letterSpacing: "0.08em"
    textTransform: uppercase
  heading-xl:
    fontFamily: "Inter, Helvetica Neue, sans-serif"
    fontSize: 32px
    fontWeight: 800
    lineHeight: 1.2
    letterSpacing: "-0.02em"
    textTransform: uppercase
  heading-lg:
    fontFamily: "Inter, Helvetica Neue, sans-serif"
    fontSize: 24px
    fontWeight: 800
    lineHeight: 1.2
    letterSpacing: "-0.01em"
    textTransform: uppercase
  heading-md:
    fontFamily: "Inter, Helvetica Neue, sans-serif"
    fontSize: 18px
    fontWeight: 700
    lineHeight: 1.3
    letterSpacing: "-0.01em"
  body-md:
    fontFamily: "Inter, Helvetica Neue, sans-serif"
    fontSize: 15px
    fontWeight: 400
    lineHeight: 1.5
    letterSpacing: 0
  body-strong:
    fontFamily: "Inter, Helvetica Neue, sans-serif"
    fontSize: 15px
    fontWeight: 600
    lineHeight: 1.5
    letterSpacing: 0
  button-lg:
    fontFamily: "Inter, Helvetica Neue, sans-serif"
    fontSize: 15px
    fontWeight: 700
    lineHeight: 1.2
    letterSpacing: "0.15em"
    textTransform: uppercase
  button-md:
    fontFamily: "Inter, Helvetica Neue, sans-serif"
    fontSize: 13px
    fontWeight: 700
    lineHeight: 1.4
    letterSpacing: "0.12em"
    textTransform: uppercase
  button-sm:
    fontFamily: "Inter, Helvetica Neue, sans-serif"
    fontSize: 11px
    fontWeight: 700
    lineHeight: 1.4
    letterSpacing: "0.1em"
    textTransform: uppercase
  kicker-label:
    fontFamily: "Inter, Helvetica Neue, sans-serif"
    fontSize: 11px
    fontWeight: 700
    lineHeight: 1.4
    letterSpacing: "0.2em"
    textTransform: uppercase
  caption-md:
    fontFamily: "Inter, Helvetica Neue, sans-serif"
    fontSize: 13px
    fontWeight: 500
    lineHeight: 1.5
    letterSpacing: 0
  caption-sm:
    fontFamily: "Inter, Helvetica Neue, sans-serif"
    fontSize: 11px
    fontWeight: 500
    lineHeight: 1.4
    letterSpacing: 0
  utility-xs:
    fontFamily: "Inter, Helvetica Neue, sans-serif"
    fontSize: 10px
    fontWeight: 600
    lineHeight: 1.4
    letterSpacing: "0.08em"
    textTransform: uppercase

rounded:
  none: "0px"
  sm: "8px"
  md: "12px"
  lg: "16px"
  xl: "24px"
  full: "9999px"

spacing:
  xxs: "2px"
  xs: "4px"
  sm: "8px"
  md: "12px"
  lg: "16px"
  xl: "24px"
  xxl: "32px"
  section-mobile: "40px"
  section-desktop: "64px"

components:
  navbar-floating:
    height: "64px"
    backgroundColor: "{colors.glass-surface}"
    backdropBlur: "20px"
    borderColor: "{colors.glass-border}"
    transition: "transform 500ms cubic-bezier(0.16, 1, 0.3, 1), background-color 300ms ease"
  navbar-hero-state:
    backgroundColor: "transparent"
    textColor: "{colors.on-primary}"
    borderColor: "transparent"
  button-primary:
    backgroundColor: "{colors.ink}"
    textColor: "{colors.on-primary}"
    typography: "{typography.button-md}"
    rounded: "{rounded.full}"
    padding: "10px 24px"
    shadow: "0 1px 3px rgba(0,0,0,0.1)"
  button-primary-active:
    backgroundColor: "{colors.charcoal}"
    transform: "scale(0.97)"
  button-secondary:
    backgroundColor: "{colors.soft-cloud}"
    textColor: "{colors.ink}"
    typography: "{typography.button-md}"
    rounded: "{rounded.full}"
    padding: "10px 24px"
  button-on-image-badge:
    backgroundColor: "rgba(17, 17, 17, 0.90)"
    textColor: "{colors.on-primary}"
    backdropBlur: "12px"
    typography: "{typography.body-strong}"
    rounded: "{rounded.lg}"
    padding: "8px 18px"
  button-icon-circular:
    backgroundColor: "{colors.canvas}"
    textColor: "{colors.ink}"
    rounded: "{rounded.full}"
    size: "36px"
    borderColor: "{colors.hairline}"
  search-pill:
    backgroundColor: "{colors.soft-cloud}"
    textColor: "{colors.ink}"
    typography: "{typography.caption-md}"
    rounded: "{rounded.full}"
    padding: "6px 16px"
    height: "36px"
  filter-chip:
    backgroundColor: "{colors.soft-cloud}"
    textColor: "{colors.ink}"
    typography: "{typography.caption-sm}"
    rounded: "{rounded.md}"
    padding: "6px 12px"
  filter-chip-active:
    backgroundColor: "{colors.ink}"
    textColor: "{colors.on-primary}"
    typography: "{typography.caption-sm}"
    rounded: "{rounded.md}"
  product-card:
    backgroundColor: "{colors.canvas}"
    textColor: "{colors.ink}"
    rounded: "{rounded.lg}"
    border: "1px solid {colors.hairline-soft}"
  product-card-image:
    backgroundColor: "{colors.soft-cloud}"
    aspectRatio: "1/1"
    rounded: "{rounded.lg}"
  category-card:
    backgroundColor: "{colors.ink}"
    aspectRatio: "4/5"
    rounded: "{rounded.xl}"
    overflow: "hidden"
  badge-premium:
    backgroundColor: "{colors.premium-gold-soft}"
    textColor: "{colors.premium-gold-deep}"
    typography: "{typography.utility-xs}"
    rounded: "{rounded.full}"
    padding: "3px 10px"
  footer:
    backgroundColor: "{colors.canvas}"
    textColor: "{colors.ink}"
    borderColor: "{colors.hairline}"
---

# Ngizan Apparel — Master Design System Specification (`DESIGN.md`)

## 1. Overview & Brand Identity

**Ngizan Apparel** is a high-fashion, athletic editorial commerce platform and bespoke jersey customization studio. The design language fuses the bold, kinetic authority of European sports archives with Swiss typographic precision and clean Japanese editorial minimalism.

### Core Philosophy: "Photography Speaks, Chrome Restrains"
1. **Photographic Primacy:** Hero campaign images and high-fidelity kit textures provide all chromatic and emotional energy. The UI chrome (navigation, buttons, rails, cards, footer) remains disciplined in warm monochrome (`#111111`, `#ffffff`, `#f5f5f5`).
2. **Extreme Typographic Contrast:** Condensed display headlines (`Bebas Neue` uppercase) contrast directly with modern geometric text (`Inter` / `Helvetica Neue`).
3. **Pill & Curved Geometry:** Actionable surfaces (buttons, badges, search pills, floating slider paddles) use soft rounded corners (`{rounded.full}`, `{rounded.lg}`, `{rounded.xl}`).
4. **Fluid Glassmorphism:** Navigation seamlessly floats over photography with ultra-smooth 1-second auto-hide hysteresis and frosted acrylic glass transitions.
5. **Strict Mathematical Spacing:** All content sections and transitions adhere to a strict, rhythmic spacing scale (`40px` on mobile, `64px` on desktop).

---

## 2. Color Palette & Semantics

| Token | Hex / Value | Semantic Role & Surface Application |
| :--- | :--- | :--- |
| `{colors.primary}` / `{colors.ink}` | `#111111` | Primary text, primary CTA pills, active filter pills, hero overlays, dark badges |
| `{colors.canvas}` | `#ffffff` | Page background, cards canvas, white button surface, input focus states |
| `{colors.on-primary}` | `#ffffff` | Inverted text on dark buttons, hero headers, badge text |
| `{colors.soft-cloud}` | `#f5f5f5` | Product card staging background, search pill, category chips, subtle fills |
| `{colors.charcoal}` | `#39393b` | Secondary text, dark button hover/active states |
| `{colors.mute}` | `#707072` | Subtitles, product metadata ("Men's Official Kit"), footer links |
| `{colors.stone}` | `#9e9ea0` | Inactive placeholders, subtle secondary utility labels |
| `{colors.hairline}` | `#cacacb` | 1px border dividers, circular paddle borders |
| `{colors.hairline-soft}` | `#e5e5e5` | Subtle container borders, header underline separators |
| `{colors.sale}` | `#d30005` | Discount badges, strike-through sale pricing callouts |
| `{colors.sale-deep}` | `#780700` | Pressed sale badges |
| `{colors.success}` | `#007d48` | Order confirmed, in-stock badge, verified payment |
| `{colors.premium-gold}` | `#d97706` | Ngizan Premium badge icon and highlight text |
| `{colors.premium-gold-soft}` | `#fef3c7` | Ngizan Premium badge pill background |
| `{colors.glass-surface}` | `rgba(255, 255, 255, 0.90)` | Floating navbar background (frosted blur `20px`) |
| `{colors.glass-border}` | `rgba(0, 0, 0, 0.05)` | Floating navbar bottom border |

---

## 3. Typography Scale & Hierarchy

| Token | Family | Size (Desktop/Mobile) | Weight | Line Height | Tracking / Transform | Usage |
| :--- | :--- | :---: | :---: | :---: | :---: | :--- |
| `{typography.display-campaign}` | Bebas Neue | 96px / 56px | 500 | 0.9 | `0.08em` UPPERCASE | Hero campaign titles & magazine editorial statements |
| `{typography.heading-xl}` | Inter | 32px / 20px | 800 | 1.2 | `-0.02em` UPPERCASE | Section titles (*Kategori Pilihan*, *New Arrivals*) |
| `{typography.heading-lg}` | Inter | 24px / 18px | 800 | 1.2 | `-0.01em` UPPERCASE | PDP product title, banner headlines, modal titles |
| `{typography.heading-md}` | Inter | 18px / 16px | 700 | 1.3 | `-0.01em` Normal | Card titles, checkout step titles, filter group names |
| `{typography.kicker-label}` | Inter | 11px / 10px | 700 | 1.4 | `0.2em` UPPERCASE | Section kickers (*Official Archive*, *Fresh Releases*) |
| `{typography.body-strong}` | Inter | 15px / 14px | 600 | 1.5 | Normal | Product names, prices, navigation items |
| `{typography.body-md}` | Inter | 15px / 14px | 400 | 1.5 | Normal | Descriptions, editorial body copy, input text |
| `{typography.button-lg}` | Inter | 15px / 13px | 700 | 1.2 | `0.15em` UPPERCASE | Primary checkout CTA, add-to-cart hero buttons |
| `{typography.button-md}` | Inter | 13px / 12px | 700 | 1.4 | `0.12em` UPPERCASE | "View All" pills, catalog filter toggles |
| `{typography.caption-md}` | Inter | 13px / 12px | 500 | 1.5 | Normal | Subtitles, category labels, order status |
| `{typography.utility-xs}` | Inter | 10px / 9px | 600 | 1.4 | `0.08em` UPPERCASE | Number badges (`01`, `02`), copyright, tag pills |

---

## 4. Spacing & Vertical Rhythm System

The layout strictly avoids arbitrary margins. Every content block and section follows a rhythmic formula:

### Mathematical Section Rhythm
- **Mobile Section Gap:** **`40px`** total between content units.
  - Section Top/Bottom Padding: `pt-5 pb-5` (adjacent sections combine to `40px`).
  - First Section under Hero: `pt-10` (`40px`).
  - CTA Button Margin: `mt-5` (`20px`).
  - Section to Footer Margin: `mt-5` (`20px`) + section `pb-5` (`20px`) = `40px`.
- **Desktop Section Gap:** **`64px`** total between content units.
  - Section Top/Bottom Padding: `sm:pt-8 sm:pb-8` (adjacent sections combine to `64px`).
  - First Section under Hero: `sm:pt-16` (`64px`).
  - CTA Button Margin: `sm:mt-6` (`24px`).
  - Section to Footer Margin: `sm:mt-8` + section `sm:pb-8` = `64px`.

### Container Specification
```css
/* Container — Max width 1440px with responsive breathing gutters */
.wrap {
  max-width: 1440px;
  margin-left: auto;
  margin-right: auto;
  padding-left: clamp(18px, 3.4vw, 80px);
  padding-right: clamp(18px, 3.4vw, 80px);
}
```

---

## 5. Key Component Specifications

### 5.1 Smart Auto-Hide Floating Navbar (`navbar-floating`)
- **Structure:**
  - **Left:** Animated 3-bar hamburger icon (mobile) + `NGIZAN` wordmark (`font-display font-medium text-2xl`).
  - **Center:** 3 Core Navlinks (`Home`, `Katalog`, `Contact`) with subtle hover underline indicators.
  - **Right:** Mobile Search Icon / Desktop Search Pill + Cart Icon (`w-9 h-9` circular pill with item counter badge) + Profile / Login Button.
- **Dynamic Scroll Hysteresis:**
  - **On Hero:** Completely transparent, text pure white (`text-white`), borderless.
  - **Past Hero:** Morphs into frosted glassmorphism (`bg-white/90 backdrop-blur-xl border-b border-black/5 text-ink shadow-xs`).
  - **Auto-Hide Behavior:** Scroll down hides navbar with a **1.0-second delay (`1000ms`)**; scroll up reveals navbar with a **1.0-second delay (`1000ms`)**; top 60px zone always remains visible.

### 5.2 Mobile Menu Drawer
- Compact, clean, understated typography (`text-xs uppercase tracking-wider font-semibold`).
- Editorial numbered row indicators (`01 Home`, `02 Katalog`, `03 Contact`).
- Horizontal chip badges for quick subcategories (`Klub Eropa`, `Tim Nasional`, `Retro Archive`).
- Smooth slide transition (`translate-y-0 opacity-100` to `-translate-y-3 opacity-0`).

### 5.3 Large Editorial Category Cards (`category-card`)
- **Aspect Ratio:** Tall portrait `h-[340px]` mobile to `h-[540px]` desktop.
- **Image:** Full-bleed kit/player photography with smooth zoom hover (`group-hover:scale-105 transition-transform duration-700`).
- **Badge:** Rounded dark pill badge (`bg-neutral-900/90 text-white backdrop-blur-md px-4 py-2 rounded-2xl shadow-lg`).

### 5.4 Product Card (`product-card`)
- **Container:** Clean `bg-white rounded-xl sm:rounded-2xl border border-gray-100 hover:shadow-lg transition`.
- **Image Stage:** Flat neutral background (`bg-soft-cloud`), 1:1 square crop.
- **Details:** Product name (`font-semibold text-sm text-ink`), Category / Tag subtitle (`text-xs text-mute`), Star Rating (`★ 5.0`), Price in Indonesian Rupiah (`Rp 299.000` with bold formatting).

### 5.5 Action Buttons (`button-primary`, `button-secondary`)
- **Primary Pill:** Background `{colors.ink}`, text `{colors.on-primary}`, shape `{rounded.full}`, uppercase tracking-widest, hover background `#262626`.
- **Secondary Pill:** Background `{colors.soft-cloud}`, text `{colors.ink}`, shape `{rounded.full}`, hover background `#e5e5e5`.
- **Floating Rail Slider Controls:** Circular buttons (`36px` mobile / `44px` desktop) with 1px border and hover scale micro-animations.

---

## 6. Page-by-Page Architectural Guidelines

### 6.1 Home (`home.blade.php`)
1. **Hero Header:** Full-bleed editorial image campaign (`h-[65vh]` mobile, `h-[125vh]` desktop) with dynamic transparent navbar overlay.
2. **Kategori Pilihan:** 1-row horizontally scrollable large cards with floating prev/next navigation.
3. **New Arrivals:** Horizontally scrollable product rail + "View All" CTA pill.
4. **Wide Promo Banner:** Responsive full-width banner (`aspect-[16/9]` mobile, `aspect-[1200/350]` desktop, `rounded-xl sm:rounded-2xl`).
5. **Koleksi Timnas:** Garuda & International kit showcase rail + "View All" CTA pill.
6. **Footer:** 4-column minimal editorial footer with partner badges (Midtrans, J&T Express).

### 6.2 Shop & Catalog (`shop/index.blade.php`)
- **Header:** Minimal catalog title + active category pill filter chips.
- **Grid Layout:** 2-column on mobile (`gap-3 sm:gap-4`), 3-column / 4-column on desktop.
- **Filter Sidebar / Off-Canvas Drawer:** Category filter, Size filter (`S, M, L, XL, XXL`), Sleeve type, and price range.

### 6.3 Product Detail Page PDP (`shop/show.blade.php`)
- **Gallery:** 1:1 primary zoomable kit showcase + thumbnail selector.
- **Product Meta:** Authenticity badge, kit release edition, size selector pills, stock count.
- **Bespoke Nameset Customization Entry:** Quick toggle to customize player name & number before adding to cart.

### 6.4 Live Customization Studio (`custom/index.blade.php`)
- **Interactive Live Preview:** Canvas-rendered jersey mockup (front & back view) with real-time text/font rendering.
- **Nameset Input:** Name (max 12 chars), Number (0-99), Official Font Style selector, Patch selector (Champions League, BRI Liga 1, etc.).

### 6.5 Customer Profile & Membership (`customer/profile.blade.php`)
- **Member Status Card:** Clean profile overview with **Ngizan Premium** golden status badge.
- **Quick Actions:** Order tracking, saved addresses, bespoke custom order history.

---

## 7. Do's and Don'ts

### Do
- Always use `.wrap` for consistent horizontal gutters; set vertical spacing through deliberate `pt-*` / `pb-*` classes.
- Maintain the strict mathematical vertical rhythm (**40px mobile / 64px desktop**).
- Keep all CTA buttons pill-shaped (`rounded-full`).
- Maintain clean high-fashion typography: Bebas Neue for display headlines, Inter for crisp UI readability.
- Stage product images on `{colors.soft-cloud}` (`#f5f5f5`) for a clean, studio-lit aesthetic.

### Don't
- Never use heavy drop-shadows on flat catalog elements. Use subtle border hairlines (`border-gray-100` / `border-black/5`) instead.
- Never add vertical padding directly inside `.wrap` definition in CSS (it must only set `padding-left` and `padding-right`).
- Never use bright uncalibrated colors for UI buttons. Accent colors are reserved for photos, badges, and sale pricing.
- Never use bulky, oversized fonts for mobile navigation. Keep mobile drawer links clean, compact, and elegant.
