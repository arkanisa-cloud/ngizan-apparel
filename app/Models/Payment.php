<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model Payment
 * Catatan transaksi pembayaran gateway Midtrans
 */
class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'transaction_id',
        'payment_type',
        'snap_token',
        'snap_redirect_url',
        'gross_amount',
        'transaction_status',
        'raw_payload',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'gross_amount'       => 'decimal:2',
            'raw_payload'        => 'array',
            'paid_at'            => 'datetime',
        ];
    }

    /**
     * Relasi ke Pesanan Terkait
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Cek apakah pembayaran sudah berstatus lunas / settlement
     */
    public function isSettlement(): bool
    {
        return in_array($this->transaction_status, ['settlement', 'capture']);
    }

    /**
     * Cek apakah masih menunggu pembayaran
     */
    public function isPending(): bool
    {
        return $this->transaction_status === 'pending';
    }

    /**
     * Cek apakah pembayaran sudah kadaluarsa
     */
    public function isExpired(): bool
    {
        return $this->transaction_status === 'expire';
    }

    /**
     * Cek apakah pembayaran dibatalkan / gagal
     */
    public function isCancelledOrDenied(): bool
    {
        return in_array($this->transaction_status, ['cancel', 'deny']);
    }
}
