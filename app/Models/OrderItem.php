<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model OrderItem
 * Detail spesifikasi produk yang dipesan (termasuk snapshot nama, nomor sablon & patch)
 */
class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'product_name',
        'size',
        'type',
        'custom_name',
        'custom_number',
        'selected_patch',
        'unit_price',
        'custom_fee',
        'quantity',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'custom_fee' => 'decimal:2',
            'quantity'   => 'integer',
            'subtotal'   => 'decimal:2',
        ];
    }

    /**
     * Relasi ke Pesanan Induk
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relasi ke Produk
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi ke Varian Produk
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Helper apakah item ini memiliki kustomisasi sablon
     */
    public function hasCustomNameset(): bool
    {
        return !empty($this->custom_name) || !empty($this->custom_number);
    }

    /**
     * Tampilan teks ringkas sablon (misal: "MESSI #10")
     */
    public function getNamesetDisplayAttribute(): ?string
    {
        if (!$this->hasCustomNameset()) {
            return null;
        }

        $parts = [];
        if (!empty($this->custom_name)) {
            $parts[] = strtoupper($this->custom_name);
        }
        if (!empty($this->custom_number)) {
            $parts[] = "#{$this->custom_number}";
        }

        return implode(' ', $parts);
    }
}
