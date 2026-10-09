# ⚙️ Feature Breakdown & Business Logic
## Project: Ngizan Apparel
**Updated Date:** 2026-09-15

---

## 1. Storefront Features & Interactive Jersey Experience

### 👕 1.1 Product Card dengan Hover Back POV & Rating Bintang
* Saat customer melihat daftar jersey di halaman Shop atau Beranda, mengarahkan kursor (*mouse hover*) ke card produk akan menampilkan tampak belakang jersey secara halus dengan zoom 1.05x.
* **Tampilan Rating Bintang**: Card produk menampilkan rata-rata rating bintang emas (1-5) dan total ulasan (misal: `★ 4.9 (24)`).
* **Tampilan Harga Member Premium**: Jika pengguna memiliki keanggotaan **Ngizan Premium** aktif, kartu produk otomatis menampilkan harga asli dicoret dan harga setelah diskon 5% dengan label *"⭐ Member 5% OFF"*.

---

### 🎨 1.2 Live Custom Nameset & Patch Preview (2D Canvas / Dynamic SVG Overlay)
* Data State di Alpine.js: `customName`, `customNumber`, `selectedPatch`, `basePrice`, `customNamePrice`, `patchPrice`.
* Kalkulasi Total Harga Instan:
  ```javascript
  get totalPrice() {
      let total = this.isPremium ? Math.round(this.basePrice * 0.95) : this.basePrice;
      if (this.customName.trim() !== '' || this.customNumber.trim() !== '') total += this.customNamePrice;
      if (this.selectedPatch) total += this.patchPrice;
      return total;
  }
  ```

---

### 🌟 1.3 Sistem Bintang (Rating 1-5) & Ulasan Pelanggan (Verified Buyer)
* **Aturan Kredibilitas (Verified Buyer Rule)**:
  * Pelanggan hanya dapat memberikan ulasan jika telah membeli produk tersebut dan status pesanan telah **`completed`** (*Selesai/Terkirim*).
  * 1 ulasan per item per order untuk mencegah review bombing/spam (`unique: reviews [user_id, product_id, order_id]`).
* **Display Tab di PDP (Product Detail)**:
  * Menampilkan skor agregat, bar distribusi bintang, dan daftar ulasan lengkap dengan badge *"Verified Buyer"*.

---

### 💎 1.4 Ngizan Premium Membership (Diskon 5% Selamanya)
* **Paket Berlangganan**: Rp 100.000 / tahun (365 hari masa aktif).
* **Alur Pembayaran**: Menggunakan Midtrans Snap modal. Begitu status `settlement`, webhook otomatis mengaktifkan status member:
  ```php
  $user->update([
      'is_premium' => true,
      'premium_until' => now()->addDays(365),
  ]);
  ```
* **Penerapan Diskon 5% Otomatis**:
  * Di seluruh kartu produk, halaman detail produk (PDP), keranjang belanja (*cart*), dan form checkout, harga dasar produk otomatis terpotong 5%.

---

### 🔔 1.5 Toastr.js User Feedback Notification
* Setiap aksi interaktif dilengkapi notifikasi popup Toastr:
  * **Add to Cart**: `toastr.success('Jersey berhasil ditambahkan ke tas!');`
  * **Premium Aktif**: `toastr.success('Selamat! Keanggotaan Ngizan Premium Anda telah aktif. Nikmati diskon 5%!');`
  * **Order Placed**: `toastr.success('Pesanan #NGZ... berhasil dibuat!');`
  * **Review Terkirim**: `toastr.success('Terima kasih! Ulasan Anda berhasil diterbitkan.');`

---

## 2. Authentication & Google OAuth Logic (Laravel Socialite)
* Login 1-klik menggunakan akun Google (`/auth/google/redirect` & `/auth/google/callback`), auto-register customer baru, dan redirect ke checkout.

---

## 3. Ekspedisi J&T Express: Gratis Ongkir & Auto-Tracking Status

### 🚚 3.1 Kemitraan Flat Gratis Ongkir (Rp 0)
* Seluruh pembelian jersey di Ngizan Apparel mendapatkan fasilitas **Gratis Ongkir (Rp 0)** melalui kemitraan resmi dengan ekspedisi **J&T Express**.
* Pelanggan tetap mengisi alamat lengkap, memilih kecamatan/kelurahan via **Biteship Area Autocomplete**, dan menandai titik GPS rumah via **Leaflet.js Pinpoint Map** beserta catatan patokan (`benchmark_notes`) agar kurir J&T mengantar ke titik presisi.

### 📦 3.2 Alur Pengiriman di Admin (Input Resi 1-Klik)
1. Pelanggan selesai membayar $\rightarrow$ Pesanan berstatus `paid`.
2. Admin mengemas barang di gudang/workshop sablon.
3. Di detail pesanan backoffice, Admin menginput nomor resi (waybill) J&T (contoh: `JX1234567890`) $\rightarrow$ Status pesanan seketika berubah menjadi **`shipped`** (*Terkirim*).
4. Sistem otomatis memicu `OrderShipped` $\rightarrow$ Mengirim WhatsApp notifikasi ke pelanggan via Fonnte berisi nomor resi dan link pelacakan paket.

### 🛰️ 3.3 Sinkronisasi Otomatis Status Perjalanan Paket (Hybrid Auto-Tracking)
* **Background Scheduler (Cron)**:
  * Command: `php artisan orders:sync-tracking` dijalankan setiap 1 jam via Laravel Scheduler.
  * Mengecek semua pesanan berstatus `shipped` ke API tracking J&T (via Biteship Tracking endpoint).
  * **Auto-Completed**: Begitu status kurir dinyatakan `delivered` / *Paket Diterima*, sistem otomatis mengubah status pesanan menjadi **`completed`** (`completed_at = now()`).
  * Memicu `OrderCompleted` $\rightarrow$ Kirim pesan WhatsApp bahwa paket telah tiba, sekaligus mengajak pelanggan memberikan rating bintang & ulasan produk.
* **Live Fetch On-Demand**:
  * Saat pelanggan atau admin membuka halaman detail pesanan (`/orders/{order_number}`), sistem langsung mengambil status lokasi terkini paket secara real-time dari API.

---

## 4. Payment Gateway Logic (Midtrans Snap & Webhook)
* **Transaksi Jersey**: Create Snap Token dengan total belanja (harga jersey setelah diskon 5% jika member, ongkir Rp 0 J&T, dan custom fee).
* **Transaksi Langganan Ngizan Premium**: Create Snap Token khusus nominal Rp 100.000.
* **Webhook Handler**: Verifikasi SHA512 Signature Key & Idempotency guard untuk update status order maupun aktivasi masa berlaku premium.

---

## 5. Inventory & Anti-Overselling Logic
* Pengurangan stok varian jersey langsung dilakukan saat `OrderCreated` dalam transaksi database (`lockForUpdate()`).
* Pemulihan stok otomatis (`RESTOCK_EXPIRED`) jika pesanan kedaluwarsa via webhook Midtrans atau scheduler `orders:cancel-expired`.

---

## 6. Pipeline Otomatisasi Gambar WebP
* Semua gambar produk dikonversi dan dikompresi menjadi `.webp` (kualitas 82%) via `ImageOptimizationService` untuk kecepatan loading super cepat di storage lokal.

---

## 7. WhatsApp Notification Service (Fonnte API)
* Mengirim notifikasi otomatis saat:
  1. Pesanan Dibuat (`OrderCreated`).
  2. Pembayaran Berhasil Terverifikasi (`PaymentReceived`).
  3. Paket Dikirim dengan Resi J&T (`OrderShipped`).
  4. Paket Tiba di Rumah Pelanggan (`OrderCompleted`) mengajak memberikan ulasan bintang.
