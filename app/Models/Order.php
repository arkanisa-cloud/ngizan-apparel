<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Model Order
 * Transaksi Pesanan Pelanggan Ngizan Apparel
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'subtotal_amount',
        'shipping_cost',
        'grand_total',
        'courier_code',
        'courier_service_code',
        'courier_service_name',
        'tracking_number',
        'biteship_order_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address_snapshot',
        'notes',
        'expires_at',
        'paid_at',
        'shipped_at',
        'completed_at',
        'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'status'                    => OrderStatus::class,
            'subtotal_amount'           => 'decimal:2',
            'shipping_cost'             => 'decimal:2',
            'grand_total'               => 'decimal:2',
            'shipping_address_snapshot' => 'array',
            'expires_at'                => 'datetime',
            'paid_at'                   => 'datetime',
            'shipped_at'                => 'datetime',
            'completed_at'              => 'datetime',
            'cancelled_at'              => 'datetime',
        ];
    }

    /**
     * Relasi ke Pelanggan pemesan
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Ulasan yang diberikan untuk pesanan ini
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Relasi ke Detail Item Pesanan
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relasi ke Catatan Pembayaran Gateway (Midtrans)
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Generate Nomor Order Unik: NGZ-YYYYMMDD-XXXX
     */
    public static function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');
        $random = strtoupper(substr(uniqid(), -4));
        return "NGZ-{$date}-{$random}";
    }

    /**
     * Helper status
     */
    public function isPendingPayment(): bool
    {
        return $this->status === OrderStatus::PENDING_PAYMENT;
    }

    public function isPaid(): bool
    {
        return $this->status === OrderStatus::PAID;
    }

    public function isInProduction(): bool
    {
        return $this->status === OrderStatus::IN_PRODUCTION;
    }

    public function isShipped(): bool
    {
        return $this->status === OrderStatus::SHIPPED;
    }

    public function isCompleted(): bool
    {
        return $this->status === OrderStatus::COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->status === OrderStatus::CANCELLED;
    }

    public function isExpired(): bool
    {
        return $this->status === OrderStatus::EXPIRED;
    }

    /**
     * Format grand total ke format Rupiah
     */
    public function getFormattedGrandTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->grand_total, 0, ',', '.');
    }

    /**
     * Cek apakah order ini memiliki item yang perlu proses sablon khusus di bagian produksi
     */
    public function hasCustomSablon(): bool
    {
        return $this->items->contains(function ($item) {
            return !empty($item->custom_name) || !empty($item->custom_number);
        });
    }
}
