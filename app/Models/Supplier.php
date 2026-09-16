<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Supplier
 * Data vendor penyuplai jersey & bahan
 */
class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
    ];

    /**
     * Relasi ke Seluruh Riwayat Barang Masuk dari supplier ini
     */
    public function stockIns(): HasMany
    {
        return $this->hasMany(StockIn::class);
    }
}
