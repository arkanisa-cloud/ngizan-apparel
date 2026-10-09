# 🎨 UI/UX Design & Component Specifications
## Project: Ngizan Apparel (Bespoke Football Kits & Archive Store)
**Updated Date:** 2026-09-15

---

## 1. Filosofi & Konsep Visual: *Hybrid Luxury-Sport & Non-AI Craft*
Desain **Ngizan Apparel** menggabungkan estetika **High-Fashion Editorial** dengan **Energi Jersey Sepak Bola**:
* **Human-Crafted & Non-Generic**: Layout editorial yang bersih, terstruktur, berbobot, dan mengutamakan kecepatan akses serta keamanan data.
* **Palet Warna Hangat & Organik (*Warm Canvas & Deep Ink*)**:
  * Base Background: Linen Bone hangat (`#EFEDE8`)
  * Card Surface: Sedikit lebih pekat (`#E4E1DC`)
  * Primary Text & Buttons: Deep Organic Black (`#101010`)
  * Subtext & Borders: Warm Gray (`#6B6862` / `rgba(16, 16, 16, 0.12)`)
  * Sport Accent: Electric Cyan (`#0284C7`), Neon Lime (`#84CC16`), dan Crimson Red (`#B91C1C`)
  * **Premium Gold Accent**: Amber Gold (`#F59E0B` / `#D97706`) untuk lencana member *Ngizan Premium* dan bintang ulasan.
  * **J&T Express Accent**: Signature Logistics Red (`#ED1C24`) untuk badge kemitraan resmi gratis ongkir.

---

## 2. Komponen Ulasan Bintang & Review Pelanggan (Verified Buyer)

### ⭐ 2.1 Product Card Star Rating
* Terletak di bawah judul produk dan di atas harga:
  * Menampilkan deretan bintang emas SVG, skor rata-rata (misal: `4.9`), dan jumlah review dalam kurung `(28)`.
  * Jika belum ada ulasan: Menampilkan teks halus `"Belum ada ulasan"`.

### 📝 2.2 Product Detail Page (PDP) Review Section
* **Rating Summary Header**:
  * Angka rata-rata besar (misal: `4.9 / 5.0`).
  * Bar horizontal distribusi bintang (5★, 4★, 3★, 2★, 1★).
* **Review Item Card**:
  * Nama pengguna (format privat: `Alvaro P.`).
  * Badge hijau daun halus: `✓ Verified Buyer` (menegaskan bahwa pengulas telah membeli produk tersebut).
  * 5-Star visual rating + tanggal ulasan.
  * Isi komentar ulasan pelanggan.

### ✍️ 2.3 Modal Input Ulasan di Detail Pesanan Customer
* Pada pesanan yang berstatus `completed`, muncul tombol interaktif `"⭐ Tulis Ulasan"`.
* Modal pop-up dengan:
  * Pemilih rating bintang 1 s.d. 5 yang responsif saat disentuh (*hover/click*).
  * Textarea komentar review yang bersih.
  * Tombol simpan dengan feedback pop-up Toastr.js.

---

## 3. Komponen Ngizan Premium Membership (Diskon 5%)

### 💎 3.1 Badge & Visual Harga Khusus Member
* **Di Kartu Produk**:
  * Pengguna Biasa: Tampil harga normal (misal: `Rp 285.000`).
  * Pengguna Member Premium: Tampil harga asli dicoret (`text-neutral-400 line-through text-xs`) dan harga diskon 5% (`Rp 270.750 font-bold text-black`), dilengkapi badge emas mini: `"⭐ Member 5% OFF"`.
* **Di Navbar & Profil**:
  * Tampil status lencana emas `"PREMIUM MEMBER"` dengan tanggal kadaluarsa (misal: *Aktif s.d. 15 Sep 2027*).
  * Jika belum menjadi member: Tampil tombol ajakan `"⭐ Gabung Premium - Diskon 5%"`.

### 💳 3.2 Modal Berlangganan Ngizan Premium
* Kartu promosi eksklusif:
  * Judul: *"Tingkatkan ke Ngizan Premium"*.
  * Harga: `Rp 100.000 / Tahun` (Akses 365 hari).
  * Keuntungan: Potongan 5% otomatis untuk seluruh pesanan jersey, akses prioritas koleksi retro langka, dan lencana eksklusif.
  * Tombol CTA: *"Bayar Sekarang via QRIS / Virtual Account"* yang memicu pop-up Midtrans Snap.

---

## 4. Komponen Ekspedisi J&T Express & Gratis Ongkir

### 🚚 4.1 Card Kurir J&T di Halaman Checkout
* Menampilkan panel kurir tunggal terpilih:
  * Logo / Badge **J&T Express** dengan label merah khas.
  * Layanan: `J&T EZ (Reguler Kilat)`.
  * Ongkos Kirim: `GRATIS (Rp 0)` berkat kemitraan resmi Ngizan Apparel x J&T Express.
  * Estimasi Pengiriman: `1 - 2 Hari Kerja`.

### 📍 4.2 Timeline Tracking Perjalanan Paket
* Terletak pada halaman Detail Pesanan Customer & Admin:
  * **Stepper Progress Bar**:
    1. Pesanan Dibuat & Dibayar.
    2. Sedang Dikemas / Sablon di Workshop.
    3. Diserahkan ke Kurir J&T (`shipped` - nomor resi tampil dengan tombol copy 1-klik).
    4. Paket Tiba di Tujuan (`completed`).
  * **Tabel Manifest Perjalanan J&T (Live Fetch)**:
    * Tanggal & Jam.
    * Lokasi Drop Point / Gateway J&T (contoh: *[Jakarta] Paket telah sampai di Gateway Kebayoran*).
    * Status operasional kurir.

---

## 5. Sistem Notifikasi: Toastr.js Integration
* **Konfigurasi Global**: Notifikasi muncul di kanan-atas (`toast-top-right`) dengan progress bar waktu 4 detik.
* **Event Pemicu Baru**:
  * `toastr.success('Keanggotaan Ngizan Premium aktif! Diskon 5% kini diterapkan otomatis.');`
  * `toastr.success('Ulasan produk Anda berhasil disimpan. Terima kasih!');`
  * `toastr.info('Status pengiriman J&T berhasil disinkronkan.');`
