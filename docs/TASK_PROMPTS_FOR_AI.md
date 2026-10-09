# 📝 Task List & Master Prompts untuk AI Agent
## Project: Ngizan Apparel (Bespoke Football Kits & Customization Store)
**Updated Date:** 2026-09-15

Gunakan daftar prompt terstruktur di bawah ini secara bertahap (**satu fase per satu sesi/chat**) untuk diserahkan kepada AI Agent. Setiap prompt sudah dirancang agar AI mematuhi prinsip kebersihan kode, keamanan siber, dan performa tinggi tanpa ada *misconception*.

---

### 📌 FASE 1: Foundation, Migrasi Database (Reviews, Premium, J&T), Enums & Models

```text
Halo AI Agent, tolong kerjakan FASE 1 pengembangan Ngizan Apparel berdasarkan panduan di docs/03_DATABASE_SCHEMA.md dan docs/07_AI_AGENT_ROADMAP.md:

1. DAFTARKAN MIDDLEWARE:
   - Daftarkan alias middleware 'admin' dan 'customer' di bootstrap/app.php.

2. BUAT ENUMS (app/Enums/):
   - OrderStatus.php (PENDING_PAYMENT, PAID, IN_PRODUCTION, SHIPPED, COMPLETED, CANCELLED, EXPIRED)
   - PaymentStatus.php (PENDING, SETTLEMENT, EXPIRE, CANCEL, DENY)
   - StockReferenceType.php (ORDER_PLACED, RESTOCK_EXPIRED, RESTOCK_CANCELLED, MANUAL_IN, MANUAL_OUT)
   - JerseySize.php (S, M, L, XL, XXL, 3XL)
   - JerseyType.php (FANS_ISSUE, PLAYER_ISSUE, RETRO)

3. SESUAIKAN DATABASE MIGRATIONS:
   - users: tambahkan role, google_id, phone, avatar, is_premium (boolean default false), premium_until (timestamp nullable).
   - reviews: id, user_id (FK), product_id (FK), order_id (FK), rating (tinyint 1-5), comment (text), timestamps, unique ['user_id', 'product_id', 'order_id'].
   - premium_subscriptions: id, user_id (FK), subscription_code (unique), amount (100000.00), duration_days (365), payment_status, snap_token, snap_redirect_url, paid_at, expires_at, timestamps.
   - orders: status, courier_code (default 'jnt'), courier_service_code (default 'ez'), courier_service_name (default 'J&T Express (Gratis Ongkir)'), shipping_cost (default 0.00), tracking_number, biteship_order_id, shipping_address_snapshot (json), expires_at, paid_at, shipped_at, completed_at.
   - products, product_variants, shipping_addresses, cart_items, order_items, payments, stock_histories sesuai skema.

4. PERBARUI ELOQUENT MODELS (app/Models/):
   - Pasang relasi lengkap ($hasMany, $belongsTo), casts ($casts json/datetime), dan $fillable ketat.
   - Pada Model Product: tambahkan relasi reviews(), aksesor average_rating, reviews_count, dan method getFinalPrice(?User $user) yang memotong 5% jika user adalah member Ngizan Premium aktif.

5. BUAT SEEDERS & JALANKAN:
   - AdminSeeder, CategorySeeder, ProductAndVariantSeeder lengkap dengan data realistis.
   - Jalankan migrasi dan seeder: php artisan migrate:fresh --seed

Pastikan kode rapi, ada komentar penjelas, dan tidak ada error!
```

---

### 📌 FASE 2: Core Services & External API Integrations (Midtrans, Biteship J&T, WhatsApp, OAuth)

```text
Halo AI Agent, tolong kerjakan FASE 2 pengembangan Ngizan Apparel berdasarkan panduan di docs/02_SYSTEM_ARCHITECTURE.md dan docs/06_API_AND_INTEGRATION_SPEC.md:

1. INSTALL DEPENDENCY:
   - Pastikan laravel/socialite terpasang.

2. BUAT SERVICE LAYER TERISOLASI (app/Services/):
   - ImageOptimizationService.php: Auto-convert upload gambar (.jpg/.png) menjadi .webp 82% quality di storage/app/public/products/.
   - BiteshipService.php:
     * searchAreas($query): Mengambil daftar kelurahan/kecamatan via Biteship Maps API.
     * getTracking($waybillId, 'jnt'): Mengambil status perjalanan dan manifest paket J&T Express secara live.
   - MidtransService.php:
     * createSnapToken($order): Membuat Snap Token pesanan jersey (dengan harga 5% OFF jika member premium dan ongkir Rp 0 J&T).
     * createSubscriptionSnapToken($user, $subscription): Membuat Snap Token langganan Ngizan Premium seharga Rp 100.000.
     * verifySignature($orderId, $statusCode, $grossAmount, $signatureKey): Memverifikasi hash SHA512 signature key Midtrans.
   - WhatsAppService.php (Fonnte API):
     * Kirim notifikasi WhatsApp saat Order Dibuat, Pembayaran Lunas, Resi Pengiriman J&T, Paket Sampai (Ajak Review), dan Selamat Bergabung Ngizan Premium.
   - InventoryService.php:
     * reserveStock($cartItems, $order): Pengurangan stok varian dengan DB::transaction() dan lockForUpdate() (Anti-Overselling).
     * restoreStock($order): Mengembalikan stok jika pesanan expired/dibatalkan.

3. GOOGLE OAUTH CONTROLLER:
   - Buat App\Http\Controllers\Auth\GoogleAuthController.php (redirect & callback) untuk login 1-klik pelanggan.

Tulis kode yang rapi, berikan penanganan exception (try-catch), dan buat Unit Test di tests/Feature/ServicesTest.php.
```

---

### 📌 FASE 3: Storefront UI, Reviews, Ngizan Premium & Live Custom Jersey Studio

```text
Halo AI Agent, tolong kerjakan FASE 3 pengembangan Ngizan Apparel berdasarkan panduan di docs/05_UI_UX_DESIGN_SPEC.md:

1. SETUP DESIGN SYSTEM & TOASTR.JS:
   - Pasang Google Fonts (Archivo, Outfit, Plus Jakarta Sans, Bebas Neue) dan Toastr.js di layout Blade utama.

2. HOMEPAGE & STOREFRONT (resources/views/home.blade.php):
   - Hero Section: "Model Cutout in Typography Wordmark".
   - Banner & Tombol Promosi: "Ngizan Premium - Langganan 100k/thn Diskon 5% Selamanya".

3. PRODUCT CARD INTERAKTIF (Blade Component):
   - Efek DUAL POV HOVER: Crossfade foto depan ke tampak belakang jersey.
   - Indikator RATING BINTANG emas (misal: ★ 4.9 (18)).
   - Penerapan visual Diskon 5% OFF untuk member premium aktif (harga asli dicoret + badge emas "⭐ Member 5% OFF").

4. DETAIL PRODUK & LIVE CUSTOM NAMESET STUDIO (resources/views/customer/product-detail.blade.php):
   - Galeri foto jersey WebP.
   - Live Nameset 2D Studio (Alpine.js & font Bebas Neue).
   - Tab "Ulasan Pelanggan": Ringkasan skor bintang, distribusi rating, dan daftar ulasan dari Verified Buyer.

5. MODAL LANGGANAN NGIZAN PREMIUM:
   - Modal pop-up pendaftaran membership Rp 100.000 / tahun dengan pemicu Midtrans Snap payment.

Pastikan UI 100% responsif di mobile dan desktop, cepat, dan estetik!
```

---

### 📌 FASE 4: Cart, Checkout J&T Gratis Ongkir, Auto-Tracking & Review Submission

```text
Halo AI Agent, tolong kerjakan FASE 4 pengembangan Ngizan Apparel berdasarkan panduan di docs/04_FEATURE_BREAKDOWN_AND_LOGIC.md dan docs/06_API_AND_INTEGRATION_SPEC.md:

1. CART (CartController.php & resources/views/customer/cart.blade.php):
   - Kalkulasi otomatis potongan harga 5% untuk user dengan membership Ngizan Premium aktif.

2. CHECKOUT & J&T GRATIS ONGKIR (CheckoutController.php & checkout.blade.php):
   - Form Alamat dengan Biteship Area Autocomplete & Pinpoint Geocoding (Leaflet.js) + patokan rumah (benchmark_notes).
   - Kurir Terpilih: Terkunci otomatis ke "J&T Express - Gratis Ongkir (Rp 0)" berkat kemitraan resmi.
   - Pemicu pop-up pembayaran window.snap.pay() via MidtransService.

3. WEBHOOK HANDLER (MidtransWebhookController.php):
   - Jika order berawalan PREM-: status settlement -> aktifkan users.is_premium = true dan premium_until = now()->addDays(365).
   - Jika order berawalan NGZ-: status settlement -> update order paid. Status expire/cancel -> kembalikan stok.

4. SCHEDULER:
   - orders:cancel-expired: Membatalkan pesanan unpaid dalam 2 jam dan kembalikan stok.
   - orders:sync-tracking: Mengecek status pesanan 'shipped' ke API J&T tiap 1 jam. Jika kurir menyatakan 'delivered', ubah status pesanan otomatis menjadi 'completed'.

5. FORM SUBMISSION ULASAN (ReviewController.php):
   - Di riwayat pesanan customer, jika status 'completed', tampilkan tombol "⭐ Tulis Ulasan".
   - Simpan rating (1-5) dan komentar ulasan (Verified Buyer check).
```

---

### 📌 FASE 5: Admin Backoffice, J&T Resi Input 1-Klik & Mutasi Stok

```text
Halo AI Agent, tolong kerjakan FASE 5 pengembangan Ngizan Apparel berdasarkan panduan di docs/05_UI_UX_DESIGN_SPEC.md:

1. ROUTING & MIDDLEWARE ADMIN:
   - Route group Admin di routes/web.php dengan middleware ['auth', 'admin'] dan prefix 'admin'.

2. DASHBOARD & KPI:
   - Metrik omset, total pesanan, grafik penjualan, jumlah member Ngizan Premium aktif, dan Low Stock Alert.

3. MANAJEMEN PESANAN & LOGISTIK J&T:
   - Filter status pesanan (pending_payment, paid, in_production, shipped, completed, cancelled, expired).
   - Form input nomor resi (waybill) J&T: Saat admin simpan resi, status seketika menjadi 'shipped' dan WhatsApp notifikasi resi langsung terkirim ke customer.
   - Live timeline pelacakan manifest J&T di detail pesanan admin.

4. MANAJEMEN MUTASI STOK:
   - Form Barang Masuk (Stock In) dari Supplier dan Barang Keluar (Stock Out) dengan pencatatan audit trail ke StockHistory.
```

---

### 📌 FASE 6: Quality Assurance, Automated Tests & Performance Polish

```text
Halo AI Agent, tolong kerjakan FASE 6 (Testing & Polish) untuk Ngizan Apparel:

1. AUTOMATED TESTS:
   - Tulis Feature Tests untuk:
     * Review Submission: Hanya Verified Buyer yang bisa submit review; non-buyer / order belum completed dilarang.
     * Ngizan Premium: Alur bayar 100k Midtrans mengaktifkan status member 1 tahun, dan kalkulasi diskon 5% teraplikasi di kartu produk dan keranjang belanja.
     * J&T Shipping: Input resi mengubah status ke 'shipped', command orders:sync-tracking mengubah status ke 'completed' saat status kurir 'delivered'.
   - Jalankan test suite: php artisan test dan pastikan seluruh test hijau (PASS).

2. ASSET BUNDLING & AUDIT KEAMANAN:
   - npm run build
   - Audit sanitasi Blade, CSRF protection, dan verifikasi Anti-IDOR pada form ulasan dan pelacakan pesanan.
```
