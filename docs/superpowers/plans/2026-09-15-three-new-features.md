# Three New Core Features Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Mengimplementasikan 3 fitur baru di Ngizan Apparel: (1) Sistem Rating Bintang & Ulasan Produk Verified Buyer, (2) Program Keanggotaan Ngizan Premium (Rp 100.000/tahun, Diskon 5% otomatis), dan (3) Kemitraan J&T Express Gratis Ongkir (Rp 0) dengan Auto-Tracking Status Pengiriman via Biteship API.

**Architecture:** Menerapkan Service-Action & Event-Driven Architecture di atas Laravel 12/13. Membangun model & migrasi database terisolasi (`reviews`, `premium_subscriptions`, kolom `users.is_premium`), integrasi Midtrans Snap untuk pembayaran langganan, Biteship tracking J&T hybrid engine (scheduler `orders:sync-tracking` per 1 jam + live fetch), serta dynamic pricing 5% OFF pada katalog & keranjang.

**Tech Stack:** Laravel 12/13, PHP 8.4, MySQL 8.4, Blade, Tailwind CSS, Alpine.js, Toastr.js, Midtrans Snap API, Biteship Tracking API, Fonnte WhatsApp API.

**Spec:** `docs/superpowers/specs/2026-09-15-three-new-features-design.md`

## Global Constraints
- PHP Version: 8.4 (Laravel Sail)
- Database: MySQL 8.4
- Style Guide: PSR-12, clean code, no fat controllers
- Security: Anti-SQLi via Eloquent, Anti-XSS via Blade escaping, Anti-CSRF on all forms, Anti-IDOR (verifikasi kepemilikan pesanan & verified buyer review)
- Pricing Rules: Ngizan Premium diskon 5% otomatis terpotong di Product Card, PDP, Cart, dan Checkout jika user memiliki keanggotaan aktif
- Shipping Rules: Kurir pesanan default ke J&T Express dengan ongkir Rp 0 (Gratis Ongkir)

---

### Task 1: Database Migrations for Reviews, Premium Subscriptions, Users & Orders

**Files:**
- Create: `database/migrations/2026_09_15_000001_add_premium_columns_to_users_table.php`
- Create: `database/migrations/2026_09_15_000002_create_reviews_table.php`
- Create: `database/migrations/2026_09_15_000003_create_premium_subscriptions_table.php`
- Modify: `database/migrations/xxxx_xx_xx_create_orders_table.php` (or add migration to set default courier to 'jnt' and shipping_cost to 0.00)
- Test: `tests/Feature/MigrationsTest.php`

**Interfaces:**
- Produces:
  - Table `reviews`: `id`, `user_id`, `product_id`, `order_id`, `rating`, `comment`, timestamps. Unique: `[user_id, product_id, order_id]`.
  - Table `premium_subscriptions`: `id`, `user_id`, `subscription_code`, `amount`, `duration_days`, `payment_status`, `snap_token`, `snap_redirect_url`, `paid_at`, `expires_at`, timestamps.
  - Table `users`: Columns `is_premium` (boolean default false), `premium_until` (timestamp nullable).

- [ ] **Step 1: Write test for database migration structure**
Create `tests/Feature/MigrationsTest.php` asserting that the required tables and columns exist in the database schema.

- [ ] **Step 2: Run test to verify it fails**
Run: `php artisan test --filter=MigrationsTest`
Expected: FAIL (missing tables/columns).

- [ ] **Step 3: Implement migrations**
Create the migrations for `users`, `reviews`, and `premium_subscriptions` according to `docs/03_DATABASE_SCHEMA.md`.

- [ ] **Step 4: Run migration and test to verify it passes**
Run: `php artisan migrate` then `php artisan test --filter=MigrationsTest`
Expected: PASS.

---

### Task 2: Eloquent Models, Relations & Dynamic Pricing Helper

**Files:**
- Create: `app/Models/Review.php`
- Create: `app/Models/PremiumSubscription.php`
- Modify: `app/Models/User.php`
- Modify: `app/Models/Product.php`
- Modify: `app/Models/Order.php`
- Test: `tests/Unit/ModelsTest.php`

**Interfaces:**
- Produces:
  - `Review` model with `belongsTo(User::class)`, `belongsTo(Product::class)`, `belongsTo(Order::class)`.
  - `PremiumSubscription` model with `belongsTo(User::class)`.
  - `User` model with `hasMany(Review::class)`, `hasMany(PremiumSubscription::class)`, `isPremiumActive(): bool`.
  - `Product` model with `hasMany(Review::class)`, `getAverageRatingAttribute()`, `getReviewsCountAttribute()`, `getFinalPrice(?User $user): float`.

- [ ] **Step 1: Write failing unit test for Model relations & pricing logic**
Test in `tests/Unit/ModelsTest.php`:
- `User::isPremiumActive()` returns true only if `is_premium` is true and `premium_until > now()`.
- `Product::getFinalPrice($user)` returns 5% discounted price for active premium user and base price for regular user.
- Review average rating aggregation calculation.

- [ ] **Step 2: Run test to verify it fails**
Run: `php artisan test --filter=ModelsTest`
Expected: FAIL.

- [ ] **Step 3: Implement Models and methods**
Write `Review.php`, `PremiumSubscription.php`, and update `User.php`, `Product.php`, `Order.php`.

- [ ] **Step 4: Run test to verify it passes**
Run: `php artisan test --filter=ModelsTest`
Expected: PASS.

---

### Task 3: Kemitraan J&T Express: Gratis Ongkir & Auto-Tracking Background Engine

**Files:**
- Modify: `app/Services/BiteshipService.php`
- Create: `app/Console/Commands/SyncJntTrackingCommand.php`
- Modify: `routes/console.php`
- Modify: `app/Http/Controllers/CheckoutController.php`
- Test: `tests/Feature/JntTrackingTest.php`

**Interfaces:**
- Consumes: `BiteshipService::getTracking($waybillId, 'jnt')`
- Produces:
  - Artisan command `orders:sync-tracking` which polls all `shipped` J&T orders, updates status to `completed` if `delivered`, and logs checkpoints.
  - Checkout controller locking shipping cost to Rp 0 for courier `jnt`.

- [ ] **Step 1: Write failing feature test for J&T tracking & auto-completed**
In `tests/Feature/JntTrackingTest.php`:
- Mock `BiteshipService::getTracking()` returning `delivered`.
- Running `orders:sync-tracking` transitions order from `shipped` to `completed` and sets `completed_at`.

- [ ] **Step 2: Run test to verify it fails**
Run: `php artisan test --filter=JntTrackingTest`
Expected: FAIL.

- [ ] **Step 3: Implement J&T tracking service method & command**
- Add `getTracking($waybillId, 'jnt')` in `BiteshipService`.
- Create `SyncJntTrackingCommand.php` and schedule it in `routes/console.php` to run hourly.
- Update `CheckoutController` to set default shipping to J&T Free Shipping (Rp 0).

- [ ] **Step 4: Run test to verify it passes**
Run: `php artisan test --filter=JntTrackingTest`
Expected: PASS.

---

### Task 4: Ngizan Premium Subscription Flow & Midtrans Webhook Handler

**Files:**
- Modify: `app/Services/MidtransService.php`
- Create: `app/Http/Controllers/PremiumSubscriptionController.php`
- Modify: `app/Http/Controllers/Webhooks/MidtransWebhookController.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/PremiumSubscriptionTest.php`

**Interfaces:**
- Consumes: `MidtransService::createSubscriptionSnapToken($user, $subscription)`
- Produces:
  - Endpoint `POST /customer/premium/subscribe` generating Midtrans Snap token for Rp 100.000.
  - Webhook handler processing `PREM-` order IDs, activating `is_premium = true` and `premium_until = now()->addDays(365)`.

- [ ] **Step 1: Write failing feature test for Premium Subscription purchase & webhook**
In `tests/Feature/PremiumSubscriptionTest.php`:
- Test subscribing generates subscription record.
- Test webhook with valid SHA512 signature and `settlement` status sets user `is_premium = true`.

- [ ] **Step 2: Run test to verify it fails**
Run: `php artisan test --filter=PremiumSubscriptionTest`
Expected: FAIL.

- [ ] **Step 3: Implement subscription controller & webhook routing**
- Implement `createSubscriptionSnapToken()` in `MidtransService`.
- Implement `PremiumSubscriptionController@store`.
- Update `MidtransWebhookController` to handle `PREM-` order IDs.

- [ ] **Step 4: Run test to verify it passes**
Run: `php artisan test --filter=PremiumSubscriptionTest`
Expected: PASS.

---

### Task 5: Verified Buyer Reviews & Star Ratings Subsystem

**Files:**
- Create: `app/Http/Controllers/ReviewController.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/ReviewTest.php`

**Interfaces:**
- Produces:
  - Endpoint `POST /customer/reviews` accepting `product_id`, `order_id`, `rating` (1-5), `comment`.
  - Validation: Only allows review if `order.user_id === Auth::id()`, `order.status === 'completed'`, and order contains `product_id`.

- [ ] **Step 1: Write failing feature test for Review submission**
In `tests/Feature/ReviewTest.php`:
- Verified Buyer with `completed` order can submit review.
- Non-buyer or customer with non-completed order gets 403 Forbidden.
- Duplicate review for the same item/order is prevented.

- [ ] **Step 2: Run test to verify it fails**
Run: `php artisan test --filter=ReviewTest`
Expected: FAIL.

- [ ] **Step 3: Implement ReviewController**
Write `ReviewController@store` with rigorous verification checks and Toastr flash feedback.

- [ ] **Step 4: Run test to verify it passes**
Run: `php artisan test --filter=ReviewTest`
Expected: PASS.

---

### Task 6: Storefront & Backoffice UI Integration

**Files:**
- Modify: `resources/views/components/product-card.blade.php` (or product card partial)
- Modify: `resources/views/customer/product-detail.blade.php`
- Modify: `resources/views/customer/orders/show.blade.php`
- Modify: `resources/views/admin/orders/show.blade.php`
- Modify: `resources/views/layouts/customer.blade.php`

- [ ] **Step 1: Update Product Card & PDP for Star Ratings & Member 5% Discount**
Display gold stars, average rating score, and review count.
Display strikethrough original price + 5% OFF price with badge `"⭐ Member 5% OFF"` if user is active premium member.
Add "Ulasan Pelanggan" tab on PDP with rating summary and verified buyer comments.

- [ ] **Step 2: Add Ngizan Premium modal/banner**
Add "Gabung Ngizan Premium" modal and navbar badge on `layouts/customer.blade.php`.

- [ ] **Step 3: Add J&T Tracking timeline & Review Submission button**
On `customer/orders/show.blade.php`, display J&T waybill, live tracking timeline, and button `"⭐ Tulis Ulasan"` when order status is `completed`.
On `admin/orders/show.blade.php`, provide input for J&T waybill that automatically sets status to `shipped`.

---

### Task 7: Full System Verification & Regression Suite

**Files:**
- Run: all automated tests
- Test: `tests/Feature/`

- [ ] **Step 1: Run complete automated test suite**
Run: `php artisan test`
Expected: 100% tests PASS (all existing + new tests).

- [ ] **Step 2: Build frontend assets**
Run: `npm run build`
Expected: Assets compiled cleanly.
