# ⚽ Ngizan Apparel
### Bespoke Football Kits & Customization Store
**Updated Date:** 2026-09-15

Platform e-commerce modern berbasis web untuk katalog, penjualan, dan kustomisasi jersey olahraga premium (*Live 2D Custom Nameset & Patch Preview*, *Dual POV Back Hover*, *Kemitraan J&T Express Gratis Ongkir Flat Rp 0 & Auto-Tracking*, *Ngizan Premium Membership Diskon 5%*, *Sistem Ulasan Bintang Verified Buyer*, *Midtrans Payment Gateway*, dan *Backoffice Admin & Inventory Matrix*).

---

## 🌟 Fitur Utama & Capaian Pengembangan

1. **Kemitraan J&T Express & Gratis Ongkir Flat Rp 0**:
   - Seluruh pembelian jersey mendapatkan fasilitas **Gratis Ongkir (Rp 0)** ke seluruh Indonesia melalui kerjasama resmi dengan J&T Express.
   - Admin cukup menginput nomor resi (waybill) J&T $\rightarrow$ Status pesanan otomatis menjadi `shipped` (*Terkirim*), dan WhatsApp notifikasi otomatis terkirim ke customer.
   - **Auto-Tracking Status**: Scheduler background (`orders:sync-tracking`) secara berkala menyinkronkan status manifest perjalanan kurir J&T via Biteship Tracking API. Begitu kurir menyatakan paket sampai (`delivered`), status pesanan otomatis berubah menjadi `completed` (*Selesai*).

2. **Program Keanggotaan Ngizan Premium (Diskon 5% Selamanya)**:
   - Pelanggan dapat berlangganan keanggotaan tahunan seharga **Rp 100.000 / tahun (365 hari)** via pembayaran otomatis Midtrans Snap (QRIS, VA, E-Wallet).
   - Selama masa aktif member, sistem secara otomatis memotong **5% harga seluruh produk jersey** di kartu produk (dengan harga coret + lencana emas *"⭐ Member 5% OFF"*), halaman detail produk, keranjang belanja, dan checkout.

3. **Sistem Rating Bintang & Ulasan Pelanggan (Verified Buyer)**:
   - Menampilkan skor rata-rata bintang emas (1-5) dan jumlah review di kartu produk dan halaman detail produk.
   - Hak ulasan diproteksi ketat hanya untuk **Verified Buyer** (pelanggan yang telah menyelesaikan transaksi pesanan berstatus `completed`) guna menjaga kredibilitas dan mencegah ulasan fiktif/spam.

4. **Storefront & Live 2D Nameset Studio**:
   - Desain Luxury Sport Palette (`#EFEDE8`, `#101010`, `#0284C7`, `#F59E0B`) dengan Google Fonts (*Archivo, Bebas Neue, Plus Jakarta Sans*).
   - *Dual POV Product Card*: Hover crossfade foto depan ke tampak belakang dengan zoom halus 1.05x.
   - *Live 2D Nameset Studio*: Input nama & nomor punggung langsung ter-render interaktif di atas punggung jersey secara real-time.

5. **Pondasi Database, Service Layer & Keamanan Siber**:
   - Skema database terelasi lengkap: `users`, `reviews`, `premium_subscriptions`, `orders`, `order_items`, `payments`, `products`, `product_variants`, dll.
   - Enums: `OrderStatus`, `PaymentStatus`, `JerseySize`, `JerseyType`, `StockReferenceType`.
   - `ImageOptimizationService`: Auto-konversi upload gambar menjadi WebP kualitas 82%.
   - `WhatsAppService`: Notifikasi WhatsApp otomatis melalui Fonnte (Order Confirmation, Resi Pengiriman J&T, Undangan Review, Aktivasi Premium).
   - `InventoryService`: Anti-overselling dengan pessimistic locking (`lockForUpdate()`).
   - `GoogleAuthController`: Login & Registrasi 1-Klik via Google OAuth 2.0.
   - Keamanan siber: Anti-SQLi, Anti-XSS, Anti-CSRF, dan Anti-IDOR.

---

## 🚀 Environment & Running dengan Laravel Sail

Project ini dikonfigurasi dan dijalankan secara resmi menggunakan **Laravel Sail** (Docker Container Environment) yang mencakup **PHP 8.4**, **MySQL 8.4**, dan **phpMyAdmin**.

### 1. Menjalankan Server & Database (Laravel Sail)
```bash
# Menjalankan container di background
./vendor/bin/sail up -d

# Atau via Docker Compose langsung:
docker compose up -d
```

### 2. Mengakses Layanan
* **Aplikasi Web**: [http://localhost](http://localhost) atau [http://localhost:8000](http://localhost:8000)
* **Backoffice Admin**: [http://localhost/admin/dashboard](http://localhost/admin/dashboard)
* **phpMyAdmin Web UI**: [http://localhost:8080](http://localhost:8080)
  * Server: `mysql`
  * Username: `sail`
  * Password: `password`

### 3. Menjalankan Database Migrations & Seeders
```bash
./vendor/bin/sail artisan migrate:fresh --seed
# atau
php artisan migrate:fresh --seed
```

### 4. Menjalankan Scheduler Auto-Tracking J&T
```bash
./vendor/bin/sail artisan orders:sync-tracking
```

### 5. Menjalankan Automated Testing
```bash
./vendor/bin/sail artisan test
# atau
php artisan test
```

### 6. Build Asset Frontend (Vite)
```bash
npm run build
```

### 7. Akun Pengguna Bawaan (Seeded Users)
* **Super Admin**:
  * Email: `admin@ngizanapparel.com`
  * Password: `password`
  * Role: `admin`
* **Demo Customer**:
  * Email: `customer@ngizanapparel.com`
  * Password: `password`
  * Role: `customer`

---

## 📚 Dokumentasi Spesifikasi Proyek (`/docs`)
* [01_PRD.md](file:///home/alvaro/Documents/SMK%20XII/Project/ngizan-apparel/docs/01_PRD.md) - Product Requirement Document & Visi Produk
* [02_SYSTEM_ARCHITECTURE.md](file:///home/alvaro/Documents/SMK%20XII/Project/ngizan-apparel/docs/02_SYSTEM_ARCHITECTURE.md) - Arsitektur Sistem, Service Layer & Alur Integrasi
* [03_DATABASE_SCHEMA.md](file:///home/alvaro/Documents/SMK%20XII/Project/ngizan-apparel/docs/03_DATABASE_SCHEMA.md) - Entity Relationship Diagram (ERD), Enums, & Data Models
* [04_FEATURE_BREAKDOWN_AND_LOGIC.md](file:///home/alvaro/Documents/SMK%20XII/Project/ngizan-apparel/docs/04_FEATURE_BREAKDOWN_AND_LOGIC.md) - Logika Bisnis, Review, Premium & J&T Tracking
* [05_UI_UX_DESIGN_SPEC.md](file:///home/alvaro/Documents/SMK%20XII/Project/ngizan-apparel/docs/05_UI_UX_DESIGN_SPEC.md) - Spesifikasi UI/UX, Bintang Review & Badge Premium
* [06_API_AND_INTEGRATION_SPEC.md](file:///home/alvaro/Documents/SMK%20XII/Project/ngizan-apparel/docs/06_API_AND_INTEGRATION_SPEC.md) - Spesifikasi API Midtrans, Biteship J&T & Fonnte WhatsApp
* [07_AI_AGENT_ROADMAP.md](file:///home/alvaro/Documents/SMK%20XII/Project/ngizan-apparel/docs/07_AI_AGENT_ROADMAP.md) - Roadmap Pengembangan 6 Fase
* [TASK_PROMPTS_FOR_AI.md](file:///home/alvaro/Documents/SMK%20XII/Project/ngizan-apparel/docs/TASK_PROMPTS_FOR_AI.md) - Master Prompts AI Agent per Fase
* [2026-09-15-three-new-features-design.md](file:///home/alvaro/Documents/SMK%20XII/Project/ngizan-apparel/docs/superpowers/specs/2026-09-15-three-new-features-design.md) - Dokumen Spesifikasi Desain Arsitektur 3 Fitur Baru
