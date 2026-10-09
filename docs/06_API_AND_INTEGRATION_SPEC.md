# 🔌 API & Third-Party Integration Specifications
## Project: Ngizan Apparel
**Updated Date:** 2026-09-15

---

## 1. Midtrans Payment Gateway Integration

### 🔑 1.1 Konfigurasi Environment (`.env`)
```ini
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxxxxxxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxxxxxxxx
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true
MIDTRANS_SNAP_URL=https://app.sandbox.midtrans.com/snap/snap.js
```

---

### 📤 1.2 Request Snap Token Pembelian Jersey (`MidtransService::createSnapToken`)
* **Payload Request**:
  ```json
  {
    "transaction_details": {
      "order_id": "NGZ-20260915-8921",
      "gross_amount": 270750
    },
    "customer_details": {
      "first_name": "Alvaro Pratama",
      "email": "alvaro@example.com",
      "phone": "081234567890"
    },
    "item_details": [
      {
        "id": "VAR-45",
        "price": 270750,
        "quantity": 1,
        "name": "MU Home 24/25 Player Issue (L) [Member 5% OFF]"
      },
      {
        "id": "SHIPPING-JNT",
        "price": 0,
        "quantity": 1,
        "name": "Gratis Ongkir J&T Express (Partnership)"
      }
    ],
    "callbacks": {
      "finish": "http://ngizan-apparel.test/customer/orders/NGZ-20260915-8921"
    }
  }
  ```

---

### 💎 1.3 Request Snap Token Langganan Ngizan Premium (`MidtransService::createSubscriptionSnapToken`)
* Digunakan saat pelanggan mendaftar membership tahunan Rp 100.000:
  ```json
  {
    "transaction_details": {
      "order_id": "PREM-20260915-1042",
      "gross_amount": 100000
    },
    "customer_details": {
      "first_name": "Alvaro Pratama",
      "email": "alvaro@example.com",
      "phone": "081234567890"
    },
    "item_details": [
      {
        "id": "NGIZAN-PREMIUM-1YR",
        "price": 100000,
        "quantity": 1,
        "name": "Langganan 1 Tahun Ngizan Premium (Diskon 5%)"
      }
    ]
  }
  ```

---

### 📥 1.4 Webhook Notification Handler (`POST /api/webhooks/midtrans`)
* **Logika Verifikasi Signature SHA512**:
  ```php
  $calculatedSignature = hash("sha512", $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . config('services.midtrans.server_key'));
  if ($calculatedSignature !== $payload['signature_key']) {
      return response()->json(['message' => 'Invalid signature'], 403);
  }
  ```
* **Cabang Pemrosesan Berdasarkan Order ID**:
  1. **Jika Order ID berawalan `PREM-` (Langganan Premium)**:
     * Status `settlement`/`capture` $\rightarrow$ Update `premium_subscriptions.payment_status = 'settlement'`, update `users.is_premium = true`, `users.premium_until = now()->addDays(365)`.
     * Trigger notifikasi WhatsApp selamat bergabung membership premium.
  2. **Jika Order ID berawalan `NGZ-` (Pesanan Jersey)**:
     * Status `settlement`/`capture` $\rightarrow$ Update `orders.status = OrderStatus::PAID`.
     * Trigger `OrderCreated`/`PaymentReceived` WhatsApp notifikasi.
     * Status `expire`/`cancel` $\rightarrow$ Kembalikan stok produk (`InventoryService::restoreStock`).

---

## 2. Biteship Logistics API (Khusus Kurir J&T Express)

### 🔑 2.1 Konfigurasi Environment (`.env`)
```ini
BITESHIP_API_KEY=biteship_live.eyJxxxxxxxxxxxxxxxxxxxxxx
BITESHIP_BASE_URL=https://api.biteship.com/v1
```

---

### 📍 2.2 Endpoints Utama yang Digunakan

#### A. Maps Area Autocomplete (`GET /v1/maps/areas`)
Digunakan pada form input alamat checkout agar customer bisa mencari kelurahan/kecamatan/kota dan mengunci `biteship_area_id` serta kode pos resmi untuk kurir J&T.
* **URL**: `https://api.biteship.com/v1/maps/areas?countries=ID&input={query}&type=single`
* **Headers**: `Authorization: Bearer {BITESHIP_API_KEY}`

---

#### B. Tracking Paket Real-Time Kurir J&T (`GET /v1/trackings/{waybill_id}/couriers/jnt`)
Digunakan oleh **Background Scheduler** (`orders:sync-tracking`) dan **Live Fetch** pada halaman detail pesanan:
* **URL**: `https://api.biteship.com/v1/trackings/{waybill_id}/couriers/jnt`
* **Response Contoh**:
  ```json
  {
    "success": true,
    "waybill_id": "JX1234567890",
    "courier": {
      "company": "jnt",
      "name": "J&T Express"
    },
    "status": "delivered",
    "history": [
      {
        "note": "Paket telah diterima oleh YBS (Alvaro)",
        "service_type": "ez",
        "updated_at": "2026-09-16 14:20:00"
      },
      {
        "note": "Paket dibawa kurir menuju alamat penerima",
        "service_type": "ez",
        "updated_at": "2026-09-16 09:15:00"
      },
      {
        "note": "Paket sampai di Gateway Kebayoran Baru",
        "service_type": "ez",
        "updated_at": "2026-09-15 22:30:00"
      }
    ]
  }
  ```
* **Logika Pembaruan Status Pesanan Otomatis**:
  * Status kurir `allocated`, `picking_up`, `in_transit`: Status pesanan tetap **`shipped`**.
  * Status kurir **`delivered`**:
    * Sistem otomatis mengupdate `orders.status = OrderStatus::COMPLETED` dan `orders.completed_at = now()`.
    * Mengirim WhatsApp notifikasi bahwa paket telah sampai.
    * Membuka akses form **Ulasan & Rating Bintang (Verified Buyer)** bagi pelanggan.

---

## 3. WhatsApp Notification Gateway (Fonnte API)

### 🔑 3.1 Konfigurasi Environment (`.env`)
```ini
FONNTE_API_TOKEN=your_fonnte_token_here
FONNTE_API_URL=https://api.fonnte.com/send
```

### 📲 3.2 Template Notifikasi Transaksional
1. **Paket Dikirim (Admin Input Resi J&T)**:
   ```text
   Halo *{{customer_name}}*! Pesanan jersey Anda #{{order_number}} telah dikemas dan diserahkan ke *J&T Express* (Gratis Ongkir).

   Nomor Resi: *{{tracking_number}}*
   Lacak perjalanan paket secara langsung di: {{tracking_url}}
   ```
2. **Paket Tiba / Selesai (Auto-Delivered Trigger)**:
   ```text
   Kabar gembira *{{customer_name}}*! Paket pesanan #{{order_number}} telah tiba di alamat tujuan.

   Bagaimana kualitas jersey Anda? Berikan rating bintang & ulasan Anda di sini:
   {{review_url}}
   ```
3. **Aktivasi Ngizan Premium Berhasil**:
   ```text
   Selamat bergabung di *Ngizan Premium*, *{{user_name}}*! 💎
   Keanggotaan Anda aktif selama 1 tahun. Anda kini otomatis menikmati diskon 5% untuk semua jersey di Ngizan Apparel.
   ```

---

## 4. Google OAuth 2.0 (Laravel Socialite)
```ini
GOOGLE_CLIENT_ID=your-google-client-id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```
