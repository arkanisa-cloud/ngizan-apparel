# 🗄️ Database Schema & Data Models
## Project: Ngizan Apparel (Laravel Sail & MySQL Environment)
**Updated Date:** 2026-09-15

---

## 1. Entity Relationship Diagram (ERD)

```mermaid
erDiagram
    USERS ||--o{ SHIPPING_ADDRESSES : has
    USERS ||--o{ CARTS : owns
    USERS ||--o{ ORDERS : places
    USERS ||--o{ REVIEWS : writes
    USERS ||--o{ PREMIUM_SUBSCRIPTIONS : subscribes
    CATEGORIES ||--o{ PRODUCTS : contains
    PRODUCTS ||--o{ PRODUCT_VARIANTS : has
    PRODUCTS ||--o{ CART_ITEMS : references
    PRODUCTS ||--o{ ORDER_ITEMS : references
    PRODUCTS ||--o{ REVIEWS : receives
    PRODUCT_VARIANTS ||--o{ CART_ITEMS : specifies
    CARTS ||--o{ CART_ITEMS : holds
    ORDERS ||--o{ ORDER_ITEMS : contains
    ORDERS ||--|| PAYMENTS : receives
    ORDERS ||--o{ REVIEWS : validates
    PRODUCT_VARIANTS ||--o{ ORDER_ITEMS : specifies
    PRODUCTS ||--o{ STOCK_HISTORIES : tracks
    PRODUCT_VARIANTS ||--o{ STOCK_HISTORIES : tracks
    SUPPLIERS ||--o{ STOCK_INS : supplies

    USERS {
        bigint id PK
        string name
        string email
        string password
        string role "admin | customer"
        string phone
        string google_id "nullable"
        string avatar "nullable"
        boolean is_premium "Default false"
        datetime premium_until "nullable"
        datetime email_verified_at
        timestamps created_at
    }

    REVIEWS {
        bigint id PK
        bigint user_id FK
        bigint product_id FK
        bigint order_id FK "Validasi Verified Buyer"
        tinyint rating "1 - 5"
        text comment
        timestamps created_at
    }

    PREMIUM_SUBSCRIPTIONS {
        bigint id PK
        bigint user_id FK
        string subscription_code UK "PREM-YYYYMMDD-XXXX"
        decimal amount "100000.00"
        integer duration_days "365"
        string payment_status "pending | settlement | expire | cancel"
        string snap_token "nullable"
        text snap_redirect_url "nullable"
        datetime paid_at "nullable"
        datetime expires_at "nullable"
        timestamps created_at
    }

    CATEGORIES {
        bigint id PK
        string name
        string slug UK
        string image "WebP path"
        boolean is_active
        timestamps created_at
    }

    PRODUCTS {
        bigint id PK
        bigint category_id FK
        string name
        string slug UK
        text description
        decimal base_price
        integer weight_grams
        string thumbnail_front "WebP path"
        string thumbnail_back "WebP path (POV Back)"
        json gallery_images "List WebP paths"
        boolean is_active
        boolean allow_custom_nameset
        decimal custom_nameset_price
        boolean allow_patch
        decimal patch_price
        json available_patches "List patches"
        timestamps created_at
    }

    PRODUCT_VARIANTS {
        bigint id PK
        bigint product_id FK
        string size "S, M, L, XL, XXL, 3XL"
        string type "Fans Issue, Player Issue, Retro"
        integer stock
        string sku UK
        decimal price_adjustment "Default 0"
        timestamps created_at
    }

    CARTS {
        bigint id PK
        bigint user_id FK
        timestamps created_at
    }

    CART_ITEMS {
        bigint id PK
        bigint cart_id FK
        bigint product_id FK
        bigint product_variant_id FK
        integer quantity
        string custom_name "nullable"
        string custom_number "nullable"
        string selected_patch "nullable"
        decimal unit_price "Terapkan 5% OFF jika is_premium"
        decimal custom_fee
        decimal total_price
        timestamps created_at
    }

    SHIPPING_ADDRESSES {
        bigint id PK
        bigint user_id FK
        string label "Rumah, Kantor, dll"
        string recipient_name
        string phone_number
        string biteship_area_id "Biteship Area ID"
        string province_name
        string city_name
        string district_name
        string postal_code
        decimal latitude "10,8"
        decimal longitude "11,8"
        text full_address
        string benchmark_notes "Patokan rumah / warna pagar"
        boolean is_primary
        timestamps created_at
    }

    ORDERS {
        bigint id PK
        string order_number UK "NGZ-YYYYMMDD-XXXX"
        bigint user_id FK
        string status "pending_payment | paid | in_production | shipped | completed | cancelled | expired"
        decimal subtotal_amount
        decimal shipping_cost "Default 0.00 (Gratis Ongkir J&T)"
        decimal grand_total
        string courier_code "Default 'jnt'"
        string courier_service_code "Default 'ez'"
        string courier_service_name "Default 'J&T Express (Gratis Ongkir)'"
        string tracking_number "Nomor Resi J&T (Waybill)"
        string biteship_order_id "nullable"
        string customer_name
        string customer_email
        string customer_phone "Nomor WA"
        text shipping_address_snapshot "JSON alamat & koordinat saat checkout"
        text notes "nullable"
        datetime expires_at "Batas bayar Midtrans (2 Jam)"
        datetime paid_at
        datetime shipped_at "Diisi saat admin input resi"
        datetime completed_at "Diisi otomatis saat paket sampai / delivered"
        datetime cancelled_at
        timestamps created_at
    }

    ORDER_ITEMS {
        bigint id PK
        bigint order_id FK
        bigint product_id FK
        bigint product_variant_id FK
        string product_name
        string size
        string type
        string custom_name "nullable"
        string custom_number "nullable"
        string selected_patch "nullable"
        decimal unit_price
        decimal custom_fee
        integer quantity
        decimal subtotal
        timestamps created_at
    }

    PAYMENTS {
        bigint id PK
        bigint order_id FK UK
        string transaction_id "Midtrans ID"
        string payment_type "qris, bank_transfer, gopay, etc"
        string snap_token
        string snap_redirect_url
        decimal gross_amount
        string transaction_status "pending, settlement, expire, cancel, deny"
        json raw_payload "Webhook log"
        datetime paid_at
        timestamps created_at
    }

    STOCK_HISTORIES {
        bigint id PK
        bigint product_id FK
        bigint product_variant_id FK
        bigint user_id FK "nullable"
        string reference_type "ORDER_PLACED, RESTOCK_EXPIRED, MANUAL_IN, MANUAL_OUT"
        string reference_id "Order number or Stock In/Out ID"
        integer quantity_change "Signed: -1, +5, etc"
        integer stock_before
        integer stock_after
        text notes "nullable"
        timestamps created_at
    }

    SUPPLIERS {
        bigint id PK
        string name
        string contact_person
        string phone
        string email "nullable"
        text address "nullable"
        timestamps created_at
    }

    STOCK_INS {
        bigint id PK
        bigint supplier_id FK
        bigint product_variant_id FK
        integer quantity
        decimal purchase_price
        string invoice_number
        date received_date
        text notes
        timestamps created_at
    }

    STOCK_OUTS {
        bigint id PK
        bigint product_variant_id FK
        integer quantity
        string reason "Damaged, Promotion, Sample, Loss"
        date out_date
        text notes
        timestamps created_at
    }
```

---

## 2. Enums Definition (`app/Enums/`)

```php
namespace App\Enums;

enum OrderStatus: string {
    case PENDING_PAYMENT = 'pending_payment';
    case PAID            = 'paid';
    case IN_PRODUCTION   = 'in_production'; // Khusus proses sablon/press custom nameset
    case SHIPPED         = 'shipped';       // Terisi saat admin input resi J&T
    case COMPLETED       = 'completed';     // Terisi otomatis saat paket sampai (delivered)
    case CANCELLED       = 'cancelled';
    case EXPIRED         = 'expired';
}

enum PaymentStatus: string {
    case PENDING    = 'pending';
    case SETTLEMENT = 'settlement';
    case EXPIRE     = 'expire';
    case CANCEL     = 'cancel';
    case DENY       = 'deny';
}

enum StockReferenceType: string {
    case ORDER_PLACED      = 'ORDER_PLACED';
    case RESTOCK_EXPIRED   = 'RESTOCK_EXPIRED';
    case RESTOCK_CANCELLED = 'RESTOCK_CANCELLED';
    case MANUAL_IN         = 'MANUAL_IN';
    case MANUAL_OUT        = 'MANUAL_OUT';
}
```

---

## 3. Media Storage Architecture
* Media disimpan di storage lokal: `storage/app/public/products/{filename}.webp`.
* Akses publik melalui symlink: `php artisan storage:link` $\rightarrow$ `public/storage/products/{filename}.webp`.
* Konfigurasi ini 100% kompatibel dengan Docker Laravel Sail dan VPS deployment (Nginx/Apache).
