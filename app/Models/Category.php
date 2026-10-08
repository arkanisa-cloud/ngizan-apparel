<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Model Category
 * Kategori produk jersey (misal: Klub Eropa, Tim Nasional, Retro Classics, Special Edition)
 */
class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'image',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relasi ke seluruh Produk dalam kategori ini
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Scope kategori yang berstatus aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Accessor untuk URL Gambar Kategori
     */
    public function getImageUrlAttribute(): string
    {
        if (!empty($this->image)) {
            if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
                return $this->image;
            }
            return asset('storage/' . $this->image);
        }

        // Fallback default berdasarkan slug
        return match($this->slug) {
            'klub-eropa'     => 'https://images.unsplash.com/photo-1522778119026-d647f0596c20?auto=format&fit=crop&w=800&q=80',
            'tim-nasional'   => 'https://images.unsplash.com/photo-1577223625816-7546f13df25d?auto=format&fit=crop&w=800&q=80',
            'retro-classics' => 'https://images.unsplash.com/photo-1518091043644-c1d4457512c6?auto=format&fit=crop&w=800&q=80',
            'special-edition'=> 'https://images.unsplash.com/photo-1508098682722-e99c43a406b2?auto=format&fit=crop&w=800&q=80',
            default          => 'https://images.unsplash.com/photo-1544698310-74ea9d1c8258?auto=format&fit=crop&w=800&q=80',
        };
    }

    /**
     * Accessor status label
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->is_active ? 'Aktif' : 'Tidak Aktif';
    }

    /**
     * Auto generate slug jika belum diisi saat membuat model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }
}
