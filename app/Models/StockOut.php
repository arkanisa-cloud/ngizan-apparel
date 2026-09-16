<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model StockOut
 * Catatan pengurangan stok non-penjualan (misal: barang rusak, endorse/promosi, sampel display)
 */
class StockOut extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_variant_id',
        'quantity',
        'reason',
        'out_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'out_date' => 'date',
        ];
    }

    /**
     * Relasi ke Varian Produk yang dikeluarkan
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
