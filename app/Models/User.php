<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Model User
 * Merepresentasikan akun pengguna (Admin dan Pelanggan)
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Kolom yang dapat diisi secara mass assignment
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'google_id',
        'avatar',
        'is_premium',
        'premium_until',
    ];

    /**
     * Kolom yang disembunyikan saat serialisasi JSON
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Type casting atribut
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_premium'        => 'boolean',
            'premium_until'     => 'datetime',
        ];
    }

    /**
     * Relasi ke Keranjang Belanja aktif
     */
    public function cart(): HasOne
    {
        return $this->hasOne(Cart::class);
    }

    /**
     * Relasi ke seluruh riwayat Pesanan
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Relasi ke Buku Alamat Pengiriman
     */
    public function shippingAddresses(): HasMany
    {
        return $this->hasMany(ShippingAddress::class);
    }

    /**
     * Relasi ke Riwayat Perubahan Stok yang dilakukan user
     */
    public function stockHistories(): HasMany
    {
        return $this->hasMany(StockHistory::class);
    }

    /**
     * Relasi ke Ulasan Produk yang ditulis user
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Relasi ke Riwayat Langganan Ngizan Premium
     */
    public function premiumSubscriptions(): HasMany
    {
        return $this->hasMany(PremiumSubscription::class);
    }

    /**
     * Helper: Cek apakah keanggotaan Ngizan Premium aktif
     */
    public function isPremiumActive(): bool
    {
        if (!$this->is_premium) {
            return false;
        }

        if ($this->premium_until === null) {
            return true;
        }

        return $this->premium_until->isFuture();
    }

    /**
     * Helper: Cek apakah user memiliki hak akses Superadmin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Helper: Cek apakah user merupakan Customer biasa
     */
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }
}
