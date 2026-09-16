<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model ShippingAddress
 * Buku alamat pengiriman pelanggan dengan integrasi Biteship Area ID & Titik GPS
 */
class ShippingAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'label',
        'recipient_name',
        'phone_number',
        'biteship_area_id',
        'province_name',
        'city_name',
        'district_name',
        'postal_code',
        'latitude',
        'longitude',
        'full_address',
        'benchmark_notes',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'latitude'   => 'float',
            'longitude'  => 'float',
            'is_primary' => 'boolean',
        ];
    }

    /**
     * Relasi ke Pelanggan Pemilik Alamat
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper string alamat lengkap berformat rapi
     */
    public function getFormattedAddressAttribute(): string
    {
        return sprintf(
            '%s, %s, %s, %s %s',
            $this->full_address,
            $this->district_name,
            $this->city_name,
            $this->province_name,
            $this->postal_code
        );
    }
}
