<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Cart
 * Keranjang belanja aktif milik pengguna
 */
class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
    ];

    /**
     * Relasi ke Pelanggan Pemilik Keranjang
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Item di dalam Keranjang
     */
    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Hitung total nilai belanja dalam keranjang
     */
    public function getTotalPriceAttribute(): float
    {
        return (float) $this->items->sum('total_price');
    }

    /**
     * Hitung total kuantitas barang
     */
    public function getTotalQuantityAttribute(): int
    {
        return (int) $this->items->sum('quantity');
    }

    /**
     * Hitung total berat dalam gram untuk kalkulasi ongkos kirim Biteship
     */
    public function getTotalWeightGramsAttribute(): int
    {
        return (int) $this->items->reduce(function ($carry, $item) {
            $weight = $item->product ? $item->product->weight_grams : 250;
            return $carry + ($weight * $item->quantity);
        }, 0);
    }

    /**
     * Format total harga ke Rupiah
     */
    public function getFormattedTotalPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->total_price, 0, ',', '.');
    }
}
