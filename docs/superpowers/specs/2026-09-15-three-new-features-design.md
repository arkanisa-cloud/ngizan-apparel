# 📐 Architectural Design Specification: Three New Core Features
## Project: Ngizan Apparel (Bespoke Football Kits & Customization Store)
**Date:** 2026-09-15  
**Status:** Approved by Stakeholder / Ready for Planning & Implementation

---

## 1. Executive Summary & Goals

Dokumen ini memformalkan spesifikasi arsitektur dan teknis untuk 3 fitur baru yang ditambahkan ke dalam ekosistem **Ngizan Apparel**:

1. **Sistem Rating Bintang & Ulasan Pelanggan (Product Reviews & Ratings)**:
   Membangun sistem review berbasis *Verified Buyer* (hanya pembeli dengan order berstatus `completed` yang dapat mengulas produk yang dibeli) untuk menjaga kredibilitas dan mencegah ulasan fiktif.
2. **Ngizan Premium Membership (Rp 100.000 / Tahun & Diskon 5% Otomatis)**:
   Program loyalitas berbayar tahunan via pembayaran Midtrans Snap. Saat membership aktif, sistem secara dinamis memotong 5% harga seluruh produk di *Product Card*, *Product Detail Page (PDP)*, *Cart*, dan *Checkout*.
3. **Kemitraan J&T Express: Gratis Ongkir (Rp 0) & Auto-Tracking Status Pengiriman**:
   Kemitraan logistik eksklusif dengan J&T Express sehingga semua pesanan mendapatkan flat Gratis Ongkir (Rp 0). Admin hanya perlu memasukkan nomor resi (waybill) J&T untuk mengubah status ke `shipped`, kemudian sistem secara otomatis melacak pergerakan paket via API tracking J&T (melalui Biteship API) dan otomatis mengubah status pesanan ke `completed` saat paket sampai (`delivered`).

---

## 2. Detailed Subsystem Specifications

### Subsystem 1: Reviews & Star Ratings (Rating Bintang & Komentar)

#### 2.1 Database Schema
* **Table:** `reviews`
  * `id`: `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
  * `user_id`: `BIGINT UNSIGNED FK -> users.id (CASCADE)`
  * `product_id`: `BIGINT UNSIGNED FK -> products.id (CASCADE)`
  * `order_id`: `BIGINT UNSIGNED FK -> orders.id (CASCADE)`
  * `rating`: `TINYINT UNSIGNED` (Range: 1 s.d. 5)
  * `comment`: `TEXT`
  * `created_at`, `updated_at`: `TIMESTAMP`
  * **Constraint:** `UNIQUE KEY unique_user_product_order (user_id, product_id, order_id)`

#### 2.2 Business Rules & Logic
* **Eligibility (Verified Buyer)**:
  * Customer dapat memberikan ulasan hanya jika:
    1. User terautentikasi (`Auth::check()`).
    2. User memiliki pesanan (`orders.user_id = auth_id`) dengan status `completed`.
    3. Order tersebut memuat `product_id` yang bersangkutan pada tabel `order_items`.
    4. Belum pernah membuat ulasan untuk kombinasi `(user_id, product_id, order_id)`.
* **Aggregations & Eloquent Helpers**:
  * Pada Model `Product`:
    * Relasi: `hasMany(Review::class)`.
    * Aksesor/Atribut: `average_rating` (dihitung via `reviews()->avg('rating') ?? 0.0`), `reviews_count`.
    * Optimized Query: `$products = Product::withAvg('reviews', 'rating')->withCount('reviews')->get();`.

#### 2.3 User Interface (UI/UX)
* **Product Card**:
  * Menampilkan bintang emas/kuning, skor rata-rata (1 desimal), dan total review. Misal: `★ 4.9 (18)`.
* **Product Detail Page (PDP)**:
  * Tab khusus *"Ulasan Pelanggan"*.
  * Bar ringkasan rating 5-bintang, 4-bintang, dst.
  * Daftar review berisikan nama pengulas (atau inisial/masking), badge *"Verified Buyer"*, tanggal ulasan, rating bintang, dan teks ulasan.
* **Order Detail Page (Customer)**:
  * Pada pesanan yang berstatus `completed`, muncul tombol *"⭐ Tulis Ulasan"* di samping setiap item produk yang belum diulas.
  * Modal input ulasan dengan pilihan interaktif bintang 1-5 dan textarea ulasan.

---

### Subsystem 2: Ngizan Premium Membership

#### 2.1 Database Schema
* **Table:** `users` (Penambahan Kolom):
  * `is_premium`: `BOOLEAN DEFAULT false` (Indexed)
  * `premium_until`: `TIMESTAMP NULLABLE`
* **Table:** `premium_subscriptions`:
  * `id`: `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
  * `user_id`: `BIGINT UNSIGNED FK -> users.id (CASCADE)`
  * `subscription_code`: `VARCHAR(50) UNIQUE` (Contoh: `PREM-20260915-XXXX`)
  * `amount`: `DECIMAL(12, 2) DEFAULT 100000.00`
  * `duration_days`: `INT DEFAULT 365`
  * `payment_status`: `ENUM('pending', 'settlement', 'expire', 'cancel') DEFAULT 'pending'`
  * `snap_token`: `VARCHAR(255) NULLABLE`
  * `snap_redirect_url`: `TEXT NULLABLE`
  * `paid_at`: `TIMESTAMP NULLABLE`
  * `expires_at`: `TIMESTAMP NULLABLE`
  * `created_at`, `updated_at`: `TIMESTAMP`

#### 2.2 Subscription & Payment Workflow (Midtrans Snap)
1. Customer mengklik banner/tombol *"Gabung Ngizan Premium"* (di Navbar / Profil).
2. Tampil modal ringkasan benefit: Diskon 5% selamanya selama 1 tahun untuk seluruh item jersey, akses koleksi eksklusif, dan lencana Member Premium.
3. Customer mengklik *"Bayar Rp 100.000 / Tahun"* $\rightarrow$ request AJAX ke `POST /customer/premium/subscribe`.
4. Controller membuat record `PremiumSubscription` baru dan memanggil `MidtransService::createSnapToken()` untuk nominal Rp 100.000.
5. Pop-up `window.snap.pay(snapToken)` terbuka di browser.
6. Callback Webhook Midtrans (`MidtransWebhookController`):
   * Menerima notifikasi status `settlement` atau `capture`.
   * Verifikasi signature key SHA512.
   * Update `premium_subscriptions.payment_status = 'settlement'`, `paid_at = now()`, `expires_at = now()->addDays(365)`.
   * Update `users.is_premium = true`, `users.premium_until = now()->addDays(365)`.

#### 2.3 Dynamic 5% Discount Pricing Engine
* **Penerapan Diskon Konsisten**:
  * Pengecekan status: `$isMember = Auth::check() && Auth::user()->is_premium && Auth::user()->premium_until > now();`.
  * Formula harga: `$finalPrice = $isMember ? round($basePrice * 0.95) : $basePrice;`.
* **Storefront Visibility**:
  * **Product Card (Katalog & Home)**: Jika customer adalah member aktif, tampilkan harga asli dicoret (`text-neutral-400 line-through`) dan harga diskon 5% berwarna tegas dengan badge *"⭐ Member 5% OFF"*.
  * **PDP**: Harga dasar langsung menampilkan potongan 5%.
  * **Cart & Checkout**: Unit price item dikalkulasikan dengan harga 5% OFF, dan subtotal mencerminkan penghematan member.

---

### Subsystem 3: Ekspedisi J&T Express: Gratis Ongkir & Auto-Tracking Status

#### 2.1 Kemitraan Flat Gratis Ongkir (Rp 0)
* Semua pesanan dikirim menggunakan kurir **J&T Express** dengan tarif flat **Rp 0 (Gratis Ongkir)**.
* Nilai `orders.shipping_cost = 0.00`.
* Nilai `orders.courier_code = 'jnt'`, `orders.courier_service_code = 'ez'`, `orders.courier_service_name = 'J&T Express (Gratis Ongkir)'`.
* Customer tetap wajib mengisi detail alamat, Biteship Area Autocomplete, titik koordinat peta (Leaflet.js Pinpoint), dan patokan rumah (`benchmark_notes`) agar kurir J&T tidak tersasar.

#### 2.2 Alur Pemrosesan Pesanan di Admin
1. Pesanan dibayar oleh pembeli via Midtrans $\rightarrow$ Status: `paid`.
2. Admin memproses & mengemas produk di gudang / workshop sablon.
3. Di halaman detail pesanan Admin (`/admin/orders/{order_number}`), Admin menginput nomor resi (waybill) J&T (misal: `JX1234567890`) lalu klik tombol *"Simpan Resi & Kirim"*.
4. Sistem otomatis:
   * Mengisi `orders.tracking_number = $waybill`.
   * Mengubah `orders.status = OrderStatus::SHIPPED`.
   * Mengisi `orders.shipped_at = now()`.
   * Memicu event `OrderShipped` $\rightarrow$ Mengirim pesan notifikasi WhatsApp via Fonnte berisi nomor resi J&T dan tautan cek status.

#### 2.3 Mekanisme Auto-Tracking & Auto-Completed (Hybrid Architecture)
1. **Background Scheduler (Cron Engine)**:
   * Artisan Command: `php artisan orders:sync-tracking`.
   * Dijalankan secara otomatis setiap 1 jam via Laravel Scheduler (`routes/console.php`).
   * Mengambil semua pesanan dengan status `shipped` dan `courier_code = 'jnt'` yang memiliki `tracking_number`.
   * Memanggil `BiteshipService::getTracking($order->tracking_number, 'jnt')`.
   * Menganalisis riwayat manifest dan status kurir:
     * Jika status kurir ekspedisi adalah `delivered` / `selesai`:
       * Update `orders.status = OrderStatus::COMPLETED`.
       * Update `orders.completed_at = now()`.
       * Memicu event `OrderCompleted` (mengirim notifikasi WhatsApp bahwa paket telah sampai dan mengajak customer memberikan bintang & review produk).
2. **Live Fetch On-Demand**:
   * Saat customer membuka `/customer/orders/{order_number}` atau admin membuka detail pesanan, controller secara langsung memanggil endpoint tracking dan menampilkan timeline riwayat lokasi terkini paket secara real-time.

---

## 3. Security, Quality Assurance & Data Integrity

1. **Anti-IDOR & Access Control**:
   * Review submission divalidasi kepemilikannya: `order.user_id === Auth::id()`.
   * Tracking detail pesanan customer hanya dapat diakses oleh pemilik order atau Admin.
2. **Idempotency & Race Condition Guard**:
   * Pengecekan webhook Midtrans untuk langganan premium menggunakan *idempotency lock* agar tidak terjadi perpanjangan durasi ganda akibat replay webhook.
3. **Graceful Fallback**:
   * Jika API Biteship/J&T sedang timeout saat live fetch tracking, sistem menampilkan cache manifest terakhir yang tersimpan di database.

---

## 4. Testing & Verification Strategy

* **Unit & Feature Tests**:
  * `ReviewTest`: Memastikan ulasan hanya dapat dibuat oleh Verified Buyer dan rating rata-rata produk terhitung akurat.
  * `PremiumSubscriptionTest`: Memastikan alur bayar Midtrans mengaktifkan status member 1 tahun dan diskon 5% diaplikasikan di katalog & cart.
  * `JntShippingTrackingTest`: Memastikan admin input resi mengubah status ke `shipped`, scheduler `orders:sync-tracking` memproses manifest, dan status otomatis menjadi `completed` saat `delivered`.
