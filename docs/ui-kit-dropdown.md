# 🎛️ UI Kit — Dropdown Component Specification
## Project: Ngizan Apparel
**Design Reference:** [DESIGN.md](./DESIGN.md)  
**Framework:** Laravel Blade + Alpine.js + Tailwind CSS

---

## 1. Overview & Design Philosophy

Dropdown di **Ngizan Apparel** mengadopsi standar sistem desain editorial atletik (*Nike Commerce System*). Karakteristik utamanya:

- **Pill Geometry**: Trigger utama menggunakan bentuk pil utuh (`rounded-full`) dengan tinggi proporsional dan padding seimbang.
- **Extreme Neutral Restraint**: Dominasi palet monokrom — Soft Cloud (`#f5f5f5`), Pure White (`#ffffff`), Ink (`#111111`), dan Hairline (`#cacacb` / `#e5e5e5`).
- **Floating Elegance**: Menu melayang memiliki radius lengkung modern (`rounded-2xl`), bayangan halus (`shadow-xl`), dan efek `backdrop-blur-xl`.
- **Micro-Animations**: Rotasi ikon panah (*chevron*) 180° saat terbuka dan transisi *scale/fade* yang cepat dan responsif (150ms).
- **Active State Signal**: Item yang aktif ditandai secara tegas dengan kontras latar belakang, teks tebal, dan ikon checklist (`✓`).

---

## 2. Design Tokens Reference

| Elemen | Token / Nilai Tailwind | Kode Warna Hex / Spek |
|---|---|---|
| **Trigger Background** | `bg-soft-cloud hover:bg-neutral-200` | `#f5f5f5` $\rightarrow$ `#e5e5e5` |
| **Trigger Border** | `border border-hairline` | `#cacacb` (1px solid) |
| **Trigger Text** | `text-ink` | `#111111` |
| **Trigger Shape** | `rounded-full` | `9999px` (Pill geometry) |
| **Menu Card Background**| `bg-white/95 backdrop-blur-xl` | `#ffffff` (95% opacity) |
| **Menu Card Border** | `border border-hairline-soft` | `#e5e5e5` (1px solid) |
| **Menu Card Radius** | `rounded-2xl` | `16px` |
| **Menu Shadow** | `shadow-xl` | Soft deep elevation |
| **Item Hover** | `hover:bg-soft-cloud/80 text-ink` | `#f5f5f5` |
| **Item Active** | `bg-soft-cloud text-ink font-bold` | `#f5f5f5` + Checklist Icon |
| **Divider Line** | `border-hairline-soft` | `#e5e5e5` (1px solid) |
| **Typography** | Font sans (Inter) | Trigger: `text-xs font-semibold`<br>Item: `text-xs font-medium` |

---

## 3. Standard Dropdown Variants & Code Snippets

### 🔹 Varian A: Sort & Filter Navigation Dropdown (Katalog / Shop)
Digunakan untuk navigasi pemilihan urutan produk atau filter dengan tautan langsung (`<a>`) yang otomatis mempertahankan query URL lainnya.

```html
{{-- PHP Controller / View Setup --}}
@php
    $currentSort = request('sort', 'latest');
    $sortLabels = [
        'latest'     => 'Rilis Terbaru',
        'price_low'  => 'Harga: Rendah ke Tinggi',
        'price_high' => 'Harga: Tinggi ke Rendah',
        'name'       => 'Nama: A - Z',
    ];
    $currentSortLabel = $sortLabels[$currentSort] ?? 'Rilis Terbaru';
@endphp

{{-- Dropdown Component --}}
<div class="relative" x-data="{ sortOpen: false }">
    {{-- Trigger Pill Button --}}
    <button type="button" 
        @click="sortOpen = !sortOpen" 
        @keydown.escape="sortOpen = false"
        class="inline-flex items-center gap-2 bg-soft-cloud hover:bg-neutral-200 border border-hairline text-ink text-xs font-semibold rounded-full px-4 py-2 transition focus:outline-none cursor-pointer shadow-2xs select-none"
        aria-haspopup="true"
        :aria-expanded="sortOpen">
        <span class="text-mute font-normal hidden sm:inline">Urutkan:</span>
        <span class="font-bold">{{ $currentSortLabel }}</span>
        <svg class="w-3.5 h-3.5 text-mute transition-transform duration-200"
            :class="sortOpen ? 'rotate-180 text-ink' : ''" 
            fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    {{-- Floating Menu Card --}}
    <div x-show="sortOpen" 
        @click.away="sortOpen = false" 
        x-cloak
        x-transition:enter="transition ease-out duration-150 transform"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100 transform"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
        class="absolute right-0 mt-2 w-56 bg-white border border-hairline-soft rounded-2xl py-1.5 shadow-xl z-40 text-xs overflow-hidden backdrop-blur-xl">

        {{-- Group Header --}}
        <div class="px-3.5 py-1.5 border-b border-hairline-soft text-[10px] font-bold uppercase tracking-wider text-mute">
            Urutkan Berdasarkan
        </div>

        {{-- Items List --}}
        <div class="py-1">
            @foreach ($sortLabels as $val => $label)
                @php $isSelected = ($currentSort === $val); @endphp
                <a href="{{ route('shop.index', array_merge(request()->except('page', 'sort'), $val !== 'latest' ? ['sort' => $val] : [])) }}"
                    class="flex items-center justify-between px-3.5 py-2.5 transition {{ $isSelected ? 'bg-soft-cloud text-ink font-bold' : 'text-neutral-600 hover:text-ink hover:bg-soft-cloud/70 font-medium' }}">
                    <span>{{ $label }}</span>
                    @if ($isSelected)
                        <svg class="w-4 h-4 text-ink shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div>
```

---

### 🔹 Varian B: Form Select / Interactive State Dropdown
Digunakan untuk pemilihan opsi di dalam form tanpa me-reload halaman (misalnya pemilihan alamat pengiriman aktif, status filter admin, dll).

```html
<div class="relative" x-data="{ 
    open: false, 
    selected: 'jakarta', 
    options: [
        { id: 'jakarta', label: 'DKI Jakarta' },
        { id: 'jabar', label: 'Jawa Barat' },
        { id: 'jateng', label: 'Jawa Tengah' },
        { id: 'jatim', label: 'Jawa Timur' }
    ],
    get currentLabel() {
        return this.options.find(o => o.id === this.selected)?.label || 'Pilih Provinsi';
    }
}">
    <input type="hidden" name="province_code" :value="selected">

    {{-- Trigger Button --}}
    <button type="button" 
        @click="open = !open" 
        @keydown.escape="open = false"
        class="w-full flex items-center justify-between bg-soft-cloud hover:bg-neutral-200 border border-hairline text-ink text-xs font-semibold rounded-2xl px-4 py-3 transition focus:outline-none focus:border-ink shadow-2xs">
        <span x-text="currentLabel"></span>
        <svg class="w-4 h-4 text-mute transition-transform duration-200"
            :class="open ? 'rotate-180 text-ink' : ''" 
            fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    {{-- Menu Card --}}
    <div x-show="open" 
        @click.away="open = false" 
        x-cloak
        x-transition:enter="transition ease-out duration-150 transform"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100 transform"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
        class="absolute left-0 right-0 mt-2 bg-white border border-hairline-soft rounded-2xl py-1.5 shadow-xl z-40 text-xs overflow-hidden max-h-60 overflow-y-auto">
        
        <template x-for="item in options" :key="item.id">
            <button type="button" 
                @click="selected = item.id; open = false"
                class="w-full flex items-center justify-between px-4 py-2.5 transition text-left"
                :class="selected === item.id ? 'bg-soft-cloud text-ink font-bold' : 'text-neutral-600 hover:text-ink hover:bg-soft-cloud/70 font-medium'">
                <span x-text="item.label"></span>
                <span x-show="selected === item.id" class="text-ink font-bold">✓</span>
            </button>
        </template>
    </div>
</div>
```

---

### 🔹 Varian C: User Profile / Action Menu Dropdown (Navbar / Admin)
Digunakan untuk menu profil user, kartu aksi, atau opsi tabel admin.

```html
<div class="relative" x-data="{ userMenuOpen: false }">
    {{-- Trigger with Avatar --}}
    <button @click="userMenuOpen = !userMenuOpen"
        class="flex items-center gap-2 px-3 py-1.5 bg-white text-ink border border-neutral-200 hover:border-ink rounded-full transition text-xs font-medium shadow-xs">
        <span class="w-5 h-5 rounded-full bg-ink text-white text-[10px] flex items-center justify-center font-bold">
            A
        </span>
        <span class="truncate max-w-[100px]">Alvaro</span>
        <svg class="w-3 h-3 opacity-70 transition-transform duration-200" 
            :class="userMenuOpen ? 'rotate-180 opacity-100' : ''" 
            fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    {{-- Floating Menu --}}
    <div x-show="userMenuOpen" @click.away="userMenuOpen = false" x-cloak
        x-transition:enter="transition ease-out duration-150 transform"
        x-transition:enter-start="opacity-0 scale-95 translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100 transform"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-1"
        class="absolute right-0 mt-2 w-52 bg-white text-ink border border-neutral-200 rounded-2xl py-2 z-50 text-xs shadow-xl backdrop-blur-xl">

        {{-- User Identity Header --}}
        <div class="px-4 py-2.5 border-b border-hairline-soft">
            <p class="font-bold text-ink truncate">Alvaro Dwi</p>
            <p class="text-[11px] text-mute truncate">alvaro@gmail.com</p>
        </div>

        {{-- Links List --}}
        <div class="py-1">
            <a href="#" class="flex items-center gap-2.5 px-4 py-2 hover:bg-soft-cloud font-medium text-ink transition">
                <span>Pesanan Saya</span>
            </a>
            <a href="#" class="flex items-center gap-2.5 px-4 py-2 hover:bg-soft-cloud font-medium text-ink transition">
                <span>Profil & Keanggotaan</span>
            </a>
        </div>

        {{-- Danger / Logout Action --}}
        <div class="border-t border-hairline-soft pt-1">
            <button type="button" class="w-full text-left px-4 py-2 hover:bg-rose-50 text-sale font-medium transition flex items-center gap-2.5">
                <span>Keluar</span>
            </button>
        </div>
    </div>
</div>
```

---

## 4. Design Guidelines: Do's & Don'ts

### ✅ Do:
1. **Gunakan Pill Geometry (`rounded-full`)**: Selalu gunakan bentuk pil bulat utuh untuk trigger utama dropdown.
2. **Sertakan Micro-animation Chevron**: Selalu tambahkan `:class="open ? 'rotate-180 text-ink' : ''"` pada ikon panah agar user mendapat respon visual instan.
3. **Tambahkan `x-cloak` & `@click.away`**: Pastikan menu tertutup secara intuitif ketika user mengklik di luar area dropdown atau menekan tombol `ESC`.
4. **Berikan Indikator Item Aktif**: Opsi yang sedang terpilih wajib memiliki latar belakang `bg-soft-cloud`, font tebal (`font-bold`), dan ikon checklist (`✓`).
5. **Gunakan Border Hairline Halus**: Batasi divider antar-grup dengan warna netral `border-hairline-soft` (`#e5e5e5`).

### ❌ Don't:
1. **Jangan Gunakan Default Browser `<select>`**: Hindari tag native `<select>` di halaman berstandar editorial karena tampilannya berbeda-beda di tiap OS dan merusak konsistensi desain.
2. **Jangan Gunakan Warna Non-Brand pada Dropdown**: Hindari memberi warna biru, hijau, atau gradasi warna-warni pada trigger atau card dropdown — tetap gunakan palet netral monokrom sesuai [DESIGN.md](./DESIGN.md).
3. **Jangan Gunakan Sudut Kotak Tajam**: Trigger tidak boleh bersudut lancip (`rounded-none` atau `rounded-xs`). Selalu gunakan `rounded-full` untuk trigger dan `rounded-2xl` untuk menu card.
4. **Jangan Lupa Mempertahankan Parameter URL**: Saat membuat filter/sort dropdown dengan tag `<a>`, selalu sertakan `request()->except('page', 'param_terkait')` agar filter lain tidak ter-reset secara tidak sengaja.
