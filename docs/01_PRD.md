# 📋 Product Requirement Document (PRD)
## Project: Ngizan Apparel (Jersey E-Commerce & Customization Store)

---

## 1. Executive Summary & Vision
**Ngizan Apparel** adalah platform e-commerce berbasis web modern yang didesain khusus untuk penjualan, pemasaran, dan kustomisasi jersey olahraga (sepak bola, futsal, retro, custom apparel). Platform ini menggabungkan pengalaman belanja premium untuk pelanggan (*Customer*) dengan sistem operasional toko & inventori yang efisien untuk pengelola (*Admin*).

### Server Environment
Website dijalankan menggunakan **Laravel Sail (Docker)** yang terintegrasi dengan PHP 8.4, MySQL 8.4, dan phpMyAdmin.

### 🎯 Tujuan Utama & Pilar Ekosistem:
1. **Pengalaman Belanja Jersey Interaktif**: Termasuk *Live Custom Nameset & Patch 2D Studio*, *Hover Back POV*, dan galeri foto WebP resolusi tinggi.
2. **Kemitraan Eksklusif J&T Express & Gratis Ongkir (Rp 0)**: Seluruh pembelian jersey mendapatkan fasilitas **Gratis Ongkir (Flat Rp 0)** melalui kerjasama resmi dengan ekspedisi **J&T Express**.
3. **Auto-Tracking Status Pengiriman Otomatis**: Admin cukup memasukkan nomor resi (waybill) J&T, status otomatis menjadi `shipped` (*Terkirim*), dan status perjalanan paket otomatis ter-update secara berkala hingga otomatis selesai (`completed`) saat kurir menyatakan paket telah tiba (*delivered*).
4. **Program Loyalitas Ngizan Premium**: Member berlangganan **Rp 100.000 / tahun** mendapatkan potongan harga otomatis **5% untuk semua produk** (tercermin di kartu produk, detail produk, keranjang belanja, dan checkout).
5. **Sistem Ulasan Bintang & Komentar Terverifikasi (Verified Buyer)**: Menampilkan rating bintang (1-5) dan komentar ulasan asli dari pelanggan yang telah menyelesaikan transaksi untuk membangun kepercayaan dan transparansi.
6. **Transaksi Cepat & Otomatis via Midtrans Payment Gateway**: Integrasi pembayaran otomatis instan (QRIS, Virtual Account, GoPay/ShopeePay) baik untuk pembelian jersey maupun langganan Ngizan Premium tahunan.
7. **Notifikasi WhatsApp Real-time (Fonnte API)**: Update otomatis di setiap tahapan order (Pesanan Dibuat, Pembayaran Berhasil, Nomor Resi Pengiriman J&T, dan Notifikasi Paket Tiba).
8. **Keandalan Inventori & Performa Tinggi**: Anti-overselling dengan pessimistic locking (`lockForUpdate()`), pipeline optimasi gambar WebP otomatis (<150KB), dan pengamanan siber tingkat tinggi (Anti-SQLi, Anti-XSS, Anti-CSRF, Anti-IDOR).

---

## 2. Target Pengguna & User Persona

### A. Customer (Pembeli)
* **Kebutuhan**:
  * Menikmati **Gratis Ongkir (Rp 0)** ke seluruh Indonesia via kurir terpercaya **J&T Express**.
  * Melacak posisi paket secara live dan akurat langsung di halaman pesanan.
  * Berlangganan **Ngizan Premium** (Rp 100k/thn) untuk menikmati diskon 5% otomatis pada setiap pembelian.
  * Melihat dan menulis ulasan bintang (1-5) serta ulasan teks setelah menerima jersey pesanan (*Verified Buyer*).
  * Melihat detail jersey tampak depan dan belakang saat kursor di-hover (*Dual POV Hover*).
  * Melakukan kustomisasi nama, nomor punggung, dan patch turnamen secara langsung (*live preview*).
  * Login cepat 1-klik menggunakan akun Google OAuth.

### B. Admin / Store Manager (Pengelola Toko)
* **Kebutuhan**:
  * Manajemen katalog produk dengan variasi ukuran (S, M, L, XL, XXL, 3XL) dan tipe (Fans Issue, Player Issue, Retro).
  * Manajemen pengiriman J&T: Cukup menginput nomor resi J&T untuk mengubah status menjadi terkirim, dan sistem secara otomatis memantau perjalanan paket hingga selesai.
  * Pemantauan pesanan masuk dan verifikasi pembayaran otomatis via Midtrans webhook (tidak perlu cek mutasi rekening manual).
  * Pemantauan dan audit member aktif *Ngizan Premium*.
  * Manajemen mutasi stok masuk (*Stock In*) dan stok keluar (*Stock Out*) dengan audit trail riwayat mutasi (*Stock History*).
  * Dashboard metrik performa omset, total penjualan, dan notifikasi stok menipis (*low-stock alert*).

---

## 3. Fitur Utama & Kebutuhan Fungsional

### 🛍️ A. Storefront & Customer Features
1. **Landing Page & Pemasaran**:
   * Hero Banner dinamis bergaya *sporty luxury* ("Model Cutout in Typography Wordmark").
   * Banner promosi keanggotaan **Ngizan Premium (Diskon 5% Selamanya)**.
   * Kategori unggulan (Klub, Tim Nasional, Retro Classics, Special Edition).
   * Produk terlaris (*Best Seller*) dengan indikator rating bintang dan jumlah ulasan.
2. **Katalog Produk (Shop) & Navigasi**:
   * Filter dinamis (kategori, ukuran, tipe jersey, rentang harga, rating).
   * **Product Card Interaktif**: Rating bintang rata-rata, Dual POV Hover (tampak belakang), dan penyesuaian harga otomatis 5% OFF jika user merupakan member Ngizan Premium aktif.
3. **Detail Produk & Interactive Customization**:
   * Galeri foto WebP multi-sudut.
   * **Live Custom Nameset & Patch Preview**: Render nama & nomor di atas punggung jersey secara real-time via font athletic Bebas Neue.
   * **Tab Ulasan Pelanggan (Reviews & Ratings)**: Menampilkan ulasan autentik dari Verified Buyer.
4. **Program Loyalitas Ngizan Premium**:
   * Halaman & modal langganan Rp 100.000 / 365 hari.
   * Pembayaran instan via Midtrans Snap (QRIS, VA, E-Wallet).
   * Status member otomatis aktif dan memotong harga seluruh katalog sebesar 5%.
5. **Autentikasi & Profil Customer**:
   * Login 1-klik Google OAuth (Laravel Socialite).
   * Alamat pengiriman presisi: Integrasi Biteship Area Autocomplete, Leaflet.js Pinpoint Geocoding, dan catatan patokan kurir (`benchmark_notes`).
6. **Checkout & Pengiriman J&T Gratis Ongkir**:
   * Kurir otomatis terkunci ke **J&T Express (Gratis Ongkir / Rp 0)**.
   * Midtrans Snap modal untuk pembayaran pesanan jersey.
   * Reservasi stok atomik anti-overselling.
7. **Live Auto-Tracking & Review Submission**:
   * Pelacakan perjalanan paket J&T langsung di halaman detail pesanan.
   * Tombol "Beri Ulasan & Bintang" aktif setelah paket berstatus selesai (`completed`).

---

### ⚙️ B. Backoffice & Admin Features
1. **Dashboard & Analytics**: Omset total, omset bulanan, grafik penjualan, statistik member Ngizan Premium, dan *Low Stock Alert*.
2. **Manajemen Produk & Varian**: CRUD produk jersey, upload foto Depan & Belakang dengan konversi otomatis ke WebP, matriks stok ukuran, serta pemantauan rating produk.
3. **Manajemen Pengiriman J&T (Order Management)**:
   * Filter status pesanan (`pending_payment`, `paid`, `in_production`, `shipped`, `completed`, `cancelled`, `expired`).
   * Input nomor resi J&T 1-klik yang otomatis mengubah status ke `shipped` dan mengirim notifikasi WhatsApp ke pemesan.
   * Pemantauan status paket yang ter-update otomatis oleh scheduler background.
4. **Manajemen Mutasi Inventori**: Audit trail barang masuk (*Stock In*), barang keluar (*Stock Out*), dan mutasi riwayat stok (*Stock History*).
