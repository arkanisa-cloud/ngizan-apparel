<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Model Product
 * Produk Utama Jersey Ngizan Apparel
 */
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'size_chart_id',
        'name',
        'slug',
        'description',
        'base_price',
        'weight_grams',
        'thumbnail_front',
        'thumbnail_back',
        'gallery_images',
        'is_active',
        'allow_custom_nameset',
        'custom_nameset_price',
        'allow_patch',
        'patch_price',
        'available_patches',
    ];

    protected function casts(): array
    {
        return [
            'base_price'           => 'decimal:2',
            'custom_nameset_price' => 'decimal:2',
            'patch_price'          => 'decimal:2',
            'weight_grams'         => 'integer',
            'is_active'            => 'boolean',
            'allow_custom_nameset' => 'boolean',
            'allow_patch'          => 'boolean',
            'gallery_images'       => 'array',
            'available_patches'    => 'array',
        ];
    }

    /**
     * Relasi ke Kategori produk
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi ke Master Panduan Ukuran (Size Chart)
     */
    public function sizeChart(): BelongsTo
    {
        return $this->belongsTo(SizeChart::class);
    }

    /**
     * Relasi ke Matriks Varian Ukuran & Tipe Jersey
     */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Relasi ke Item Keranjang
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Relasi ke Item Pesanan
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relasi ke Kartu Stok / Histori Mutasi
     */
    public function stockHistories(): HasMany
    {
        return $this->hasMany(StockHistory::class);
    }

    /**
     * Relasi ke Ulasan Pelanggan
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Rata-rata rating bintang produk (1 - 5)
     */
    public function getAverageRatingAttribute(): float
    {
        return round((float) ($this->reviews()->avg('rating') ?? 0.0), 1);
    }

    /**
     * Total ulasan produk
     */
    public function getReviewsCountAttribute(): int
    {
        return (int) $this->reviews()->count();
    }

    /**
     * Ambil harga produk berdasarkan status membership user (Diskon 5% jika member aktif)
     */
    public function getFinalPrice(?User $user = null): float
    {
        if ($user && $user->isPremiumActive()) {
            return (float) round($this->base_price * 0.95);
        }

        return (float) $this->base_price;
    }

    /**
     * Hitung total stok dari seluruh varian ukuran
     */
    public function getTotalStockAttribute(): int
    {
        return (int) $this->variants()->sum('stock');
    }

    /**
     * Format harga dasar ke Rupiah
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->base_price, 0, ',', '.');
    }

    /**
     * Scope produk aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Auto generate slug jika kosong
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
        });
    }
}
