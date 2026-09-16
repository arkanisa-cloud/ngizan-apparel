<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model PremiumSubscription
 * Menyimpan riwayat transaksi keanggotaan Ngizan Premium 100k/thn via Midtrans
 */
class PremiumSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_code',
        'amount',
        'duration_days',
        'payment_status',
        'snap_token',
        'snap_redirect_url',
        'paid_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'amount'        => 'decimal:2',
            'duration_days' => 'integer',
            'paid_at'       => 'datetime',
            'expires_at'    => 'datetime',
        ];
    }

    /**
     * Relasi ke User pemilik langganan
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
