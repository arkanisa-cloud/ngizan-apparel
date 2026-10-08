<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Banner
 * Mengelola gambar Hero Section dan Promo Banner di halaman Beranda
 */
class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'image',
    ];

    /**
     * Accessor untuk URL Gambar Banner
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!empty($this->image)) {
            if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
                return $this->image;
            }
            return asset('storage/' . $this->image);
        }

        // Hanya hero yang memiliki default fallback jika kosong
        if ($this->key === 'hero') {
            return 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&w=2000&q=85';
        }

        return null;
    }

    /**
     * Scope helper untuk kompabilitas
     */
    public function scopeActive($query)
    {
        return $query;
    }
}
