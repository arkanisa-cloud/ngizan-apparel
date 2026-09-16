<?php

namespace App\Models;

use App\Enums\StockReferenceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model StockHistory
 * Kartu stok & audit log mutasi perubahan kuantitas inventori
 */
class StockHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'product_variant_id',
        'user_id',
        'reference_type',
        'reference_id',
        'quantity_change',
        'stock_before',
        'stock_after',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'reference_type'  => StockReferenceType::class,
            'quantity_change' => 'integer',
            'stock_before'    => 'integer',
            'stock_after'     => 'integer',
        ];
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
     * Relasi ke User / Admin yang melakukan perubahan (opsional)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
