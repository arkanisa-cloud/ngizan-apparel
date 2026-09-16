<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model ProductVariant
 * Matriks SKU varian produk (Kombinasi Ukuran, Tipe Jersey, Stok, dan Selisih Harga)
 */
class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'size',
        'type',
        'stock',
        'sku',
        'price_adjustment',
    ];

    protected function casts(): array
    {
        return [
            'stock'            => 'integer',
            'price_adjustment' => 'decimal:2',
        ];
    }

    /**
     * Relasi ke Produk Induk
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi ke Cart Items
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Relasi ke Order Items
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relasi ke Log Pergerakan Stok
     */
    public function stockHistories(): HasMany
    {
        return $this->hasMany(StockHistory::class);
    }

    /**
     * Relasi ke Barang Masuk dari Supplier
     */
    public function stockIns(): HasMany
    {
        return $this->hasMany(StockIn::class);
    }

    /**
     * Relasi ke Barang Keluar Non-Penjualan
     */
    public function stockOuts(): HasMany
    {
        return $this->hasMany(StockOut::class);
    }

    /**
     * Hitung harga final varian (Harga Dasar Produk + Penyesuaian Harga Varian)
     */
    public function getFinalPriceAttribute(): float
    {
        $basePrice = $this->product ? (float) $this->product->base_price : 0;
        return $basePrice + (float) $this->price_adjustment;
    }

    /**
     * Format harga final ke Rupiah
     */
    public function getFormattedFinalPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->final_price, 0, ',', '.');
    }

    /**
     * Cek apakah varian masih memiliki stok tersedia
     */
    public function isAvailable(): bool
    {
        return $this->stock > 0;
    }
}
