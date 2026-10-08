<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model SizeChart
 * Master template panduan ukuran (Jersey Dewasa, Jersey Wanita, Jersey Anak, Celana, Trackpants, dll)
 */
class SizeChart extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_type',
        'description',
        'columns',
        'rows',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'columns'    => 'array',
            'rows'       => 'array',
            'is_default' => 'boolean',
        ];
    }

    /**
     * Relasi ke seluruh Produk yang menggunakan size chart ini
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Scope template default
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
