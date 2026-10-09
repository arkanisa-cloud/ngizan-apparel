# 🤖 AI Agent Implementation Roadmap & Execution Guide
## Project: Ngizan Apparel (Laravel Sail & MySQL Stack)
**Updated Date:** 2026-09-15

---

## 🚨 MANDATORY PRINCIPLES UNTUK AI AGENT (HARUS DIPATUHI)

> ### 📢 PESAN PENTING & PRINSIP UTAMA:
> 1. **KODE RAPI, BERSIH & MUDAH DIPAHAMI (CLEAN & MAINTAINABLE CODE)**:
>    * Seluruh kode PHP harus mengikuti standar PSR-12, terstruktur rapi, dan mudah dibaca agar dapat dikembangkan lebih lanjut secara mandiri oleh pemilik proyek.
>    * Terapkan arsitektur *Service-Action* & *Single Responsibility*: jangan membuat *Fat Controller*.
>    * Buat To Do list dulu sebelum mulai eksekusi kode, lalu update secara berkala.
>
> 2. **KEAMANAN TINGKAT TINGGI DARI SERANGAN CYBER (CYBERSECURITY & HARDENING)**:
>    * **Anti-SQL Injection**: Gunakan Eloquent ORM atau Parameterized PDO Binding.
>    * **Anti-XSS**: Sanitasi input dan gunakan Blade escaping `{{ }}`.
>    * **Anti-CSRF & Rate Limiting**: `@csrf` pada seluruh form dan middleware `throttle`.
>    * **Anti-IDOR (Insecure Direct Object References)**: Verifikasi kepemilikan data pengguna (keranjang, alamat, pesanan, dan submit review hanya untuk order miliknya yang `completed`).
>    * **Webhook Security & Idempotency**: Verifikasi hash SHA512 Signature Key pada Midtrans webhook baik untuk order jersey maupun langganan Ngizan Premium.
>
> 3. **KELANCARAN, KECEPATAN & PERFORMA TINGGI (HIGH PERFORMANCE)**:
>    * **Anti-Overselling Guard**: Wajib gunakan `DB::transaction()` dan `lockForUpdate()` saat reservasi stok varian jersey.
>    * **Optimasi Gambar WebP**: Konversi otomatis upload gambar ke format `.webp` berkualitas ~82% (<150KB).
>    * **Kemitraan J&T Gratis Ongkir**: Default tarif ongkir Rp 0 kurir J&T Express.
>    * **Feedback Instan**: Gunakan notifikasi **Toastr.js** untuk seluruh aksi pelanggan.

---

## 🗺️ Rencana Fase Pengerjaan (Phase Breakdown)

---

### 📦 FASE 1: Foundation, Database Migrations & Eloquent Models
* **Task List**:
  1. [ ] Update migrasi `users` (tambahkan `role`, `google_id`, `avatar`, `phone`, `is_premium`, `premium_until`).
  2. [ ] Buat migrasi `reviews` (`id`, `user_id`, `product_id`, `order_id`, `rating` [1-5], `comment`, timestamps, unique `[user_id, product_id, order_id]`).
  3. [ ] Buat migrasi `premium_subscriptions` (`id`, `user_id`, `subscription_code`, `amount`, `duration_days`, `payment_status`, `snap_token`, `snap_redirect_url`, `paid_at`, `expires_at`).
  4. [ ] Update migrasi `orders` (`shipping_cost` default 0.00, `courier_code` default 'jnt', `courier_service_name` default 'J&T Express (Gratis Ongkir)', `tracking_number`, `shipped_at`, `completed_at`).
  5. [ ] Update migrasi `products`, `product_variants`, `shipping_addresses`, `cart_items`, `order_items`, `payments`, `stock_histories`.
  6. [ ] Definisikan Enums di `app/Enums/`: `OrderStatus`, `PaymentStatus`, `StockReferenceType`, `JerseySize`, `JerseyType`.
  7. [ ] Definisikan relasi Eloquent lengkap pada seluruh Model (`User`, `Product`, `Review`, `PremiumSubscription`, `Order`, dll.) dengan `$fillable` ketat dan helper perhitungan harga diskon 5% `Product::getFinalPrice(?User $user)`.
  8. [ ] Perbaiki middleware alias di `bootstrap/app.php` dan buat Database Seeders realistis.

---

### ⚙️ FASE 2: Core Services & External API Integrations
* **Task List**:
  1. [ ] `ImageOptimizationService.php`: Auto-convert upload gambar ke WebP 82% quality.
  2. [ ] `BiteshipService.php`: Area autocomplete search dan **J&T Waybill Tracking Status API** (`getTracking($waybillId, 'jnt')`).
  3. [ ] `MidtransService.php`:
     * Generate Snap Token pesanan jersey (dengan 5% discount jika premium member).
     * Generate Snap Token langganan **Ngizan Premium** Rp 100.000 (`createSubscriptionSnapToken`).
     * Verifikasi hash SHA512 Signature Key.
  4. [ ] `WhatsAppService.php`: Pengiriman pesan transaksional Fonnte (Order Created, Payment Paid, J&T Tracking Resi, Paket Delivered & Ajak Review, Premium Active).
  5. [ ] `InventoryService.php`: Anti-overselling stock reservation & auto-restore saat order expired.
  6. [ ] Setup Google OAuth login flow (Laravel Socialite).

---

### 🎨 FASE 3: Storefront UI, Reviews, Ngizan Premium & Live Custom Studio
* **Task List**:
  1. [ ] Setup Google Fonts & Toastr.js pada layout Blade utama (`resources/views/layouts/`).
  2. [ ] Hero Section: *Model Cutout in Typography Wordmark*.
  3. [ ] Banner & Modal Keanggotaan **Ngizan Premium** (Diskon 5% Selamanya Rp 100.000/tahun).
  4. [ ] Komponen **Product Card**:
     * Efek **Hover Back POV**.
     * Indikator **Rating Bintang (1-5)** & total review.
     * Penerapan visual **Diskon 5% OFF** untuk member premium aktif (harga coret + badge emas *"⭐ Member 5% OFF"*).
  5. [ ] Halaman Detail Produk (`/product/{slug}`):
     * Galeri foto WebP.
     * **Live Custom Nameset & Patch Preview** (Alpine.js + font Bebas Neue).
     * **Tab Ulasan Pelanggan (Verified Buyer)**: Skor rata-rata, persentase rating, dan daftar ulasan pelanggan.
  6. [ ] Halaman & Modal Pemesanan Langganan Ngizan Premium dengan trigger pop-up Midtrans Snap.

---

### 🛒 FASE 4: Cart, Dynamic Checkout, J&T Free Shipping & Auto-Tracking
* **Task List**:
  1. [ ] Halaman & Drawer Keranjang (`/cart`): Kalkulasi otomatis potongan 5% bagi member premium.
  2. [ ] Halaman Checkout (`/checkout`):
     * Biteship Area Autocomplete + Leaflet.js Pinpoint Geocoding (catatan patokan `benchmark_notes`).
     * Panel Pengiriman: Otomatis terkunci ke **J&T Express - Gratis Ongkir (Rp 0)**.
     * Midtrans Snap pop-up untuk pembayaran pesanan.
  3. [ ] Webhook Handler (`MidtransWebhookController`):
     * Menangani webhook `PREM-` untuk aktivasi Ngizan Premium.
     * Menangani webhook `NGZ-` untuk konfirmasi pembayaran pesanan jersey.
  4. [ ] Command Scheduler:
     * `orders:cancel-expired`: Batalkan pesanan expired 2 jam dan kembalikan stok.
     * `orders:sync-tracking`: Auto-sync status J&T tiap 1 jam, dan auto-update status pesanan ke `completed` saat paket `delivered`.
  5. [ ] Form Submission Ulasan di Riwayat Pesanan Customer:
     * Tombol *"⭐ Tulis Ulasan"* aktif begitu pesanan berstatus `completed`.
     * Submit rating 1-5 dan komentar ulasan (Anti-IDOR validation).

---

### 👑 FASE 5: Admin Backoffice, J&T Resi Input & Mutasi Inventori
* **Task List**:
  1. [ ] Dashboard KPI: Omset, pesanan, grafik penjualan, statistik member Ngizan Premium, dan Low Stock Alert.
  2. [ ] Manajemen Produk: CRUD jersey dengan upload foto multi-sudut, WebP converter, dan monitoring ulasan.
  3. [ ] Manajemen Pesanan & Logistik J&T:
     * Input nomor resi (waybill) J&T 1-klik $\rightarrow$ Status otomatis menjadi `shipped` $\rightarrow$ Notifikasi WhatsApp resi terkirim.
     * Monitoring timeline status perjalanan J&T yang tersinkronisasi otomatis.
     * Cetak Shipping Label & Invoice.
  4. [ ] Manajemen Mutasi Stok: Barang Masuk (*Stock In*), Barang Keluar (*Stock Out*), dan mutasi *Stock History*.

---

### ✅ FASE 6: Quality Assurance, Automated Tests & Performance Polish
* **Task List**:
  1. [ ] Automated Feature Tests:
     * Ulasan produk: Hanya Verified Buyer yang berhasil submit review, non-buyer ditolak.
     * Ngizan Premium: Alur bayar 100k Midtrans mengaktifkan member 1 tahun, harga diskon 5% otomatis terpotong di card & cart.
     * J&T Shipping: Input resi mengubah status ke `shipped`, scheduler `orders:sync-tracking` mengubah status ke `completed` saat `delivered`.
  2. [ ] Asset bundling Vite (`npm run build`).
  3. [ ] Audit keamanan (Anti-SQLi, Anti-XSS, CSRF, Anti-IDOR).
