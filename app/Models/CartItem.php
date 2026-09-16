<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model CartItem
 * Detail item dalam keranjang belanja lengkap dengan konfigurasi kustomisasi sablon & patch
 */
class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'product_variant_id',
        'quantity',
        'custom_name',
        'custom_number',
        'selected_patch',
        'unit_price',
        'custom_fee',
        'total_price',
    ];

    protected function casts(): array
    {
        return [
            'quantity'    => 'integer',
            'unit_price'  => 'decimal:2',
            'custom_fee'  => 'decimal:2',
            'total_price' => 'decimal:2',
        ];
    }

    /**
     * Relasi ke Keranjang Induk
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Relasi ke Produk
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi ke Varian Produk (Ukuran & Tipe)
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Cek apakah item ini memiliki sablon kustom nama/nomor
     */
    public function hasCustomNameset(): bool
    {
        return !empty($this->custom_name) || !empty($this->custom_number);
    }

    /**
     * Cek apakah item ini memiliki patch kompetisi
     */
    public function hasPatch(): bool
    {
        return !empty($this->selected_patch);
    }
}
