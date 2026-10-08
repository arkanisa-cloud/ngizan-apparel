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
     * Relasi ke Order milik pelanggan yang menggunakan alamat ini
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'user_id')
            ->where(function ($query) {
                $query->where('shipping_address_snapshot->full_address', $this->full_address)
                      ->orWhere('shipping_address_snapshot->phone_number', $this->phone_number);
            });
    }

    /**
     * Cek apakah alamat sedang digunakan pada pesanan aktif
     */
    public function hasActiveOrders(): bool
    {
        $activeStatuses = [
            \App\Enums\OrderStatus::PENDING_PAYMENT->value,
            \App\Enums\OrderStatus::PAID->value,
            \App\Enums\OrderStatus::IN_PRODUCTION->value,
            \App\Enums\OrderStatus::SHIPPED->value,
            'pending', 'processed', 'shipped'
        ];

        return $this->orders()->whereIn('status', $activeStatuses)->count() > 0;
    }

    // Accessors & Mutators for backward compatibility
    public function getPhoneAttribute(): ?string
    {
        return $this->phone_number;
    }

    public function setPhoneAttribute($value): void
    {
        $this->attributes['phone_number'] = $value;
    }

    public function getAddressAttribute(): ?string
    {
        return $this->full_address;
    }

    public function setAddressAttribute($value): void
    {
        $this->attributes['full_address'] = $value;
    }

    public function getCityAttribute(): ?string
    {
        return $this->city_name;
    }

    public function setCityAttribute($value): void
    {
        $this->attributes['city_name'] = $value;
    }

    public function getProvinceAttribute(): ?string
    {
        return $this->province_name;
    }

    public function setProvinceAttribute($value): void
    {
        $this->attributes['province_name'] = $value;
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
