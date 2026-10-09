<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model StockIn
 * Catatan restock barang masuk ke gudang
 */
class StockIn extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_variant_id',
        'quantity',
        'purchase_price',
        'invoice_number',
        'received_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity'       => 'integer',
            'purchase_price' => 'decimal:2',
            'received_date'  => 'date',
        ];
    }

    /**
     * Relasi ke Varian Produk yang masuk
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
