<?php

namespace Database\Seeders;

use App\Enums\JerseySize;
use App\Enums\JerseyType;
use App\Enums\StockReferenceType;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockHistory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductAndVariantSeeder extends Seeder
{
    /**
     * Seed katalog produk jersey realistis beserta matriks varian ukuran, SKU, dan stok awal
     */
    public function run(): void
    {
        // Ambil ID Kategori dari seeder
        $catJersey = Category::where('slug', 'jersey')->first()?->id ?? 1;
        $catKaosKaki = Category::where('slug', 'kaos-kaki')->first()?->id ?? 2;
        $catBolaSepak = Category::where('slug', 'bola-sepak')->first()?->id ?? 3;
        $catBolaFutsal = Category::where('slug', 'bola-futsal')->first()?->id ?? 4;
        $catManset = Category::where('slug', 'manset')->first()?->id ?? 5;

        // 2. Daftar Produk Realistis (Jersey & Non-Jersey)
        $productsData = [
            [
                'category_id'          => $catJersey,
                'name'                 => 'Real Madrid 2024/25 Home Authentic Kit',
                'slug'                 => 'real-madrid-2024-25-home-authentic-kit',
                'description'          => 'Jersey kandang Real Madrid musim 2024/2025 dengan pola houndstooth elegan khas tradisi fiesta San Isidro di Madrid. Dilengkapi teknologi HEAT.RDY berpori mikro untuk sirkulasi udara optimal.',
                'base_price'           => 299000,
                'weight_grams'         => 250,
                'thumbnail_front'      => 'products/real-madrid-home-front.webp',
                'thumbnail_back'       => 'products/real-madrid-home-back.webp',
                'gallery_images'       => [
                    'products/real-madrid-home-front.webp',
                    'products/real-madrid-home-back.webp',
                ],
                'is_active'            => true,
                'allow_custom_nameset' => true,
                'custom_nameset_price' => 50000,
                'allow_patch'          => true,
                'patch_price'          => 35000,
                'available_patches'    => [
                    'UCL 15 Starball + UEFA Foundation',
                    'La Liga EA Sports Champions Gold',
                    'FIFA Club World Cup Champion',
                ],
                'variants'             => [
                    ['size' => JerseySize::S->value, 'type' => JerseyType::PLAYER_ISSUE->value, 'stock' => 10, 'sku' => 'RMA-2425-H-PI-S', 'adj' => 50000],
                    ['size' => JerseySize::M->value, 'type' => JerseyType::PLAYER_ISSUE->value, 'stock' => 15, 'sku' => 'RMA-2425-H-PI-M', 'adj' => 50000],
                    ['size' => JerseySize::L->value, 'type' => JerseyType::PLAYER_ISSUE->value, 'stock' => 12, 'sku' => 'RMA-2425-H-PI-L', 'adj' => 50000],
                    ['size' => JerseySize::XL->value, 'type' => JerseyType::PLAYER_ISSUE->value, 'stock' => 8, 'sku' => 'RMA-2425-H-PI-XL', 'adj' => 50000],
                    ['size' => JerseySize::XXL->value, 'type' => JerseyType::PLAYER_ISSUE->value, 'stock' => 4, 'sku' => 'RMA-2425-H-PI-XXL', 'adj' => 60000],
                    ['size' => JerseySize::S->value, 'type' => JerseyType::FANS_ISSUE->value, 'stock' => 15, 'sku' => 'RMA-2425-H-FI-S', 'adj' => 0],
                    ['size' => JerseySize::M->value, 'type' => JerseyType::FANS_ISSUE->value, 'stock' => 25, 'sku' => 'RMA-2425-H-FI-M', 'adj' => 0],
                    ['size' => JerseySize::L->value, 'type' => JerseyType::FANS_ISSUE->value, 'stock' => 20, 'sku' => 'RMA-2425-H-FI-L', 'adj' => 0],
                    ['size' => JerseySize::XL->value, 'type' => JerseyType::FANS_ISSUE->value, 'stock' => 10, 'sku' => 'RMA-2425-H-FI-XL', 'adj' => 0],
                ],
            ],
            [
                'category_id'          => $catJersey,
                'name'                 => 'Arsenal 2024/25 Away Solar Black Edition',
                'slug'                 => 'arsenal-2024-25-away-solar-black-edition',
                'description'          => 'Jersey tandang Arsenal 2024/25 berkolaborasi dengan Labrum London. Mengusung aksen grafis Pan-Afrika hijau-merah dengan emblem Cannon legendaris di bagian dada kiri.',
                'base_price'           => 279000,
                'weight_grams'         => 250,
                'thumbnail_front'      => 'products/arsenal-away-front.webp',
                'thumbnail_back'       => 'products/arsenal-away-back.webp',
                'gallery_images'       => [
                    'products/arsenal-away-front.webp',
                    'products/arsenal-away-back.webp',
                ],
                'is_active'            => true,
                'allow_custom_nameset' => true,
                'custom_nameset_price' => 50000,
                'allow_patch'          => true,
                'patch_price'          => 35000,
                'available_patches'    => [
                    'Premier League Official Lion Gold',
                    'UEFA Champions League + Foundation',
                ],
                'variants'             => [
                    ['size' => JerseySize::S->value, 'type' => JerseyType::PLAYER_ISSUE->value, 'stock' => 8, 'sku' => 'ARS-2425-A-PI-S', 'adj' => 50000],
                    ['size' => JerseySize::M->value, 'type' => JerseyType::PLAYER_ISSUE->value, 'stock' => 14, 'sku' => 'ARS-2425-A-PI-M', 'adj' => 50000],
                    ['size' => JerseySize::L->value, 'type' => JerseyType::PLAYER_ISSUE->value, 'stock' => 10, 'sku' => 'ARS-2425-A-PI-L', 'adj' => 50000],
                    ['size' => JerseySize::XL->value, 'type' => JerseyType::PLAYER_ISSUE->value, 'stock' => 6, 'sku' => 'ARS-2425-A-PI-XL', 'adj' => 50000],
                    ['size' => JerseySize::S->value, 'type' => JerseyType::FANS_ISSUE->value, 'stock' => 12, 'sku' => 'ARS-2425-A-FI-S', 'adj' => 0],
                    ['size' => JerseySize::M->value, 'type' => JerseyType::FANS_ISSUE->value, 'stock' => 18, 'sku' => 'ARS-2425-A-FI-M', 'adj' => 0],
                    ['size' => JerseySize::L->value, 'type' => JerseyType::FANS_ISSUE->value, 'stock' => 15, 'sku' => 'ARS-2425-A-FI-L', 'adj' => 0],
                    ['size' => JerseySize::XL->value, 'type' => JerseyType::FANS_ISSUE->value, 'stock' => 8, 'sku' => 'ARS-2425-A-FI-XL', 'adj' => 0],
                ],
            ],
            [
                'category_id'          => $catJersey,
                'name'                 => 'Timnas Indonesia 2024/25 Home Garuda Kit',
                'slug'                 => 'timnas-indonesia-2024-25-home-garuda-kit',
                'description'          => 'Jersey kandang Timnas Indonesia edisi kualifikasi Piala Dunia 2026. Balutan merah membara dengan rajutan motif sayap burung Garuda dan detail kerah putih klasik.',
                'base_price'           => 249000,
                'weight_grams'         => 240,
                'thumbnail_front'      => 'products/timnas-indonesia-home-front.webp',
                'thumbnail_back'       => 'products/timnas-indonesia-home-back.webp',
                'gallery_images'       => [
                    'products/timnas-indonesia-home-front.webp',
                    'products/timnas-indonesia-home-back.webp',
                ],
                'is_active'            => true,
                'allow_custom_nameset' => true,
                'custom_nameset_price' => 45000,
                'allow_patch'          => true,
                'patch_price'          => 30000,
                'available_patches'    => [
                    'FIFA World Cup 2026 Qualifiers',
                    'AFC Asian Cup Official Matchday',
                ],
                'variants'             => [
                    ['size' => JerseySize::S->value, 'type' => JerseyType::PLAYER_ISSUE->value, 'stock' => 15, 'sku' => 'INA-2425-H-PI-S', 'adj' => 40000],
                    ['size' => JerseySize::M->value, 'type' => JerseyType::PLAYER_ISSUE->value, 'stock' => 25, 'sku' => 'INA-2425-H-PI-M', 'adj' => 40000],
                    ['size' => JerseySize::L->value, 'type' => JerseyType::PLAYER_ISSUE->value, 'stock' => 20, 'sku' => 'INA-2425-H-PI-L', 'adj' => 40000],
                    ['size' => JerseySize::XL->value, 'type' => JerseyType::PLAYER_ISSUE->value, 'stock' => 12, 'sku' => 'INA-2425-H-PI-XL', 'adj' => 40000],
                    ['size' => JerseySize::XXL->value, 'type' => JerseyType::PLAYER_ISSUE->value, 'stock' => 8, 'sku' => 'INA-2425-H-PI-XXL', 'adj' => 50000],
                    ['size' => JerseySize::S->value, 'type' => JerseyType::FANS_ISSUE->value, 'stock' => 20, 'sku' => 'INA-2425-H-FI-S', 'adj' => 0],
                    ['size' => JerseySize::M->value, 'type' => JerseyType::FANS_ISSUE->value, 'stock' => 30, 'sku' => 'INA-2425-H-FI-M', 'adj' => 0],
                    ['size' => JerseySize::L->value, 'type' => JerseyType::FANS_ISSUE->value, 'stock' => 25, 'sku' => 'INA-2425-H-FI-L', 'adj' => 0],
                ],
            ],
            [
                'category_id'          => $catJersey,
                'name'                 => 'Manchester United 1998/99 Treble Winners Retro',
                'slug'                 => 'manchester-united-1998-99-treble-winners-retro',
                'description'          => 'Replika arsip resmi jersey Manchester United Final Camp Nou 1999 saat mengunci gelar Treble bersejarah. Dilengkapi sablon timbul sponsor Sharp dan kerah resleting ikonik.',
                'base_price'           => 349000,
                'weight_grams'         => 280,
                'thumbnail_front'      => 'products/manchester-united-1999-front.webp',
                'thumbnail_back'       => 'products/manchester-united-1999-back.webp',
                'gallery_images'       => [
                    'products/manchester-united-1999-front.webp',
                    'products/manchester-united-1999-back.webp',
                ],
                'is_active'            => true,
                'allow_custom_nameset' => true,
                'custom_nameset_price' => 60000,
                'allow_patch'          => true,
                'patch_price'          => 40000,
                'available_patches'    => [
                    'UEFA Champions League Final Barcelona May 1999',
                    'Premier League 1998/99 Champions Patch',
                ],
                'variants'             => [
                    ['size' => JerseySize::S->value, 'type' => JerseyType::RETRO->value, 'stock' => 6, 'sku' => 'MUN-1999-RETRO-S', 'adj' => 0],
                    ['size' => JerseySize::M->value, 'type' => JerseyType::RETRO->value, 'stock' => 12, 'sku' => 'MUN-1999-RETRO-M', 'adj' => 0],
                    ['size' => JerseySize::L->value, 'type' => JerseyType::RETRO->value, 'stock' => 10, 'sku' => 'MUN-1999-RETRO-L', 'adj' => 0],
                    ['size' => JerseySize::XL->value, 'type' => JerseyType::RETRO->value, 'stock' => 5, 'sku' => 'MUN-1999-RETRO-XL', 'adj' => 0],
                    ['size' => JerseySize::XXL->value, 'type' => JerseyType::RETRO->value, 'stock' => 3, 'sku' => 'MUN-1999-RETRO-XXL', 'adj' => 20000],
                ],
            ],
            [
                'category_id'          => $catJersey,
                'name'                 => 'Japan National Team 2024 Special Anime Manga Edition',
                'slug'                 => 'japan-national-team-2024-special-anime-manga-edition',
                'description'          => 'Jersey edisi khusus Samurai Blue berkolaborasi dengan kreator manga ternama Jepang. Dilengkapi ilustrasi panel manga halus di atas kain jacquard biru origami.',
                'base_price'           => 329000,
                'weight_grams'         => 260,
                'thumbnail_front'      => 'products/japan-special-front.webp',
                'thumbnail_back'       => 'products/japan-special-back.webp',
                'gallery_images'       => [
                    'products/japan-special-front.webp',
                    'products/japan-special-back.webp',
                ],
                'is_active'            => true,
                'allow_custom_nameset' => true,
                'custom_nameset_price' => 55000,
                'allow_patch'          => true,
                'patch_price'          => 35000,
                'available_patches'    => [
                    'JFA Hologram Authentic Match Patch',
                    'Asian Cup Special Edition',
                ],
                'variants'             => [
                    ['size' => JerseySize::S->value, 'type' => JerseyType::PLAYER_ISSUE->value, 'stock' => 8, 'sku' => 'JPN-SP24-PI-S', 'adj' => 40000],
                    ['size' => JerseySize::M->value, 'type' => JerseyType::PLAYER_ISSUE->value, 'stock' => 15, 'sku' => 'JPN-SP24-PI-M', 'adj' => 40000],
                    ['size' => JerseySize::L->value, 'type' => JerseyType::PLAYER_ISSUE->value, 'stock' => 12, 'sku' => 'JPN-SP24-PI-L', 'adj' => 40000],
                    ['size' => JerseySize::XL->value, 'type' => JerseyType::PLAYER_ISSUE->value, 'stock' => 7, 'sku' => 'JPN-SP24-PI-XL', 'adj' => 40000],
                    ['size' => JerseySize::XXL->value, 'type' => JerseyType::PLAYER_ISSUE->value, 'stock' => 3, 'sku' => 'JPN-SP24-PI-XXL', 'adj' => 50000],
                ],
            ],
            [
                'category_id'          => $catKaosKaki,
                'name'                 => 'Kaos Kaki Grip Anti-Slip Pro Football',
                'slug'                 => 'kaos-kaki-grip-anti-slip-pro-football',
                'description'          => 'Kaos kaki sepak bola berteknologi grip bantalan silikon anti-slip di bagian telapak. Mencegah kaki bergeser di dalam sepatu dan meningkatkan stabilitas manuver.',
                'base_price'           => 49000,
                'weight_grams'         => 100,
                'thumbnail_front'      => 'categories/kaos-kaki.webp',
                'thumbnail_back'       => null,
                'gallery_images'       => [
                    'categories/kaos-kaki.webp',
                ],
                'is_active'            => true,
                'allow_custom_nameset' => false,
                'custom_nameset_price' => 0,
                'allow_patch'          => false,
                'patch_price'          => 0,
                'available_patches'    => [],
                'variants'             => [
                    ['size' => 'All Size', 'type' => 'Apparel', 'stock' => 50, 'sku' => 'SOCK-GRIP-BLK', 'adj' => 0],
                ],
            ],
            [
                'category_id'          => $catBolaSepak,
                'name'                 => 'Bola Sepak Match Ball FIFA Quality Pro Size 5',
                'slug'                 => 'bola-sepak-match-ball-fifa-quality-pro-size-5',
                'description'          => 'Bola pertandingan resmi ukuran 5 standar FIFA. Konstruksi thermally bonded tanpa jahitan (seamless) untuk akurasi tendangan maksimal dan retensi udara sempurna.',
                'base_price'           => 349000,
                'weight_grams'         => 430,
                'thumbnail_front'      => 'categories/bola-sepak.webp',
                'thumbnail_back'       => null,
                'gallery_images'       => [
                    'categories/bola-sepak.webp',
                ],
                'is_active'            => true,
                'allow_custom_nameset' => false,
                'custom_nameset_price' => 0,
                'allow_patch'          => false,
                'patch_price'          => 0,
                'available_patches'    => [],
                'variants'             => [
                    ['size' => 'Size 5', 'type' => 'Equipment', 'stock' => 20, 'sku' => 'BALL-SOCCER-SZ5', 'adj' => 0],
                ],
            ],
            [
                'category_id'          => $catBolaFutsal,
                'name'                 => 'Bola Futsal Competition Low Bounce Size 4',
                'slug'                 => 'bola-futsal-competition-low-bounce-size-4',
                'description'          => 'Bola futsal resmi dengan teknologi low bounce pantulan rendah untuk kontrol bola yang presisi di lapangan vinyl, parket, dan interlock.',
                'base_price'           => 289000,
                'weight_grams'         => 420,
                'thumbnail_front'      => 'categories/bola-futsal.webp',
                'thumbnail_back'       => null,
                'gallery_images'       => [
                    'categories/bola-futsal.webp',
                ],
                'is_active'            => true,
                'allow_custom_nameset' => false,
                'custom_nameset_price' => 0,
                'allow_patch'          => false,
                'patch_price'          => 0,
                'available_patches'    => [],
                'variants'             => [
                    ['size' => 'Size 4', 'type' => 'Equipment', 'stock' => 25, 'sku' => 'BALL-FUTSAL-SZ4', 'adj' => 0],
                ],
            ],
            [
                'category_id'          => $catManset,
                'name'                 => 'Manset Baselayer Compression Long Sleeve Pro',
                'slug'                 => 'manset-baselayer-compression-long-sleeve-pro',
                'description'          => 'Pakaian manset baselayer kompresi lengan panjang dengan bahan spandex polyester elastis. Menjaga suhu otot tetap optimal dan menyerap keringat dengan cepat.',
                'base_price'           => 89000,
                'weight_grams'         => 150,
                'thumbnail_front'      => 'categories/manset.webp',
                'thumbnail_back'       => null,
                'gallery_images'       => [
                    'categories/manset.webp',
                ],
                'is_active'            => true,
                'allow_custom_nameset' => false,
                'custom_nameset_price' => 0,
                'allow_patch'          => false,
                'patch_price'          => 0,
                'available_patches'    => [],
                'variants'             => [
                    ['size' => JerseySize::S->value, 'type' => 'Apparel', 'stock' => 15, 'sku' => 'MANSET-BLK-S', 'adj' => 0],
                    ['size' => JerseySize::M->value, 'type' => 'Apparel', 'stock' => 25, 'sku' => 'MANSET-BLK-M', 'adj' => 0],
                    ['size' => JerseySize::L->value, 'type' => 'Apparel', 'stock' => 20, 'sku' => 'MANSET-BLK-L', 'adj' => 0],
                    ['size' => JerseySize::XL->value, 'type' => 'Apparel', 'stock' => 15, 'sku' => 'MANSET-BLK-XL', 'adj' => 0],
                ],
            ],
        ];

        // 3. Masukkan ke database
        foreach ($productsData as $data) {
            $variants = $data['variants'];
            unset($data['variants']);

            $product = Product::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );

            foreach ($variants as $v) {
                $variant = ProductVariant::updateOrCreate(
                    ['sku' => $v['sku']],
                    [
                        'product_id'       => $product->id,
                        'size'             => $v['size'],
                        'type'             => $v['type'],
                        'stock'            => $v['stock'],
                        'price_adjustment' => $v['adj'],
                    ]
                );

                // Catat mutasi stok awal di StockHistory
                StockHistory::firstOrCreate(
                    [
                        'product_variant_id' => $variant->id,
                        'reference_type'     => StockReferenceType::MANUAL_IN,
                        'reference_id'       => 'INIT-STOCK-' . $variant->id,
                    ],
                    [
                        'product_id'      => $product->id,
                        'quantity_change' => $v['stock'],
                        'stock_before'    => 0,
                        'stock_after'     => $v['stock'],
                        'notes'           => "Initial stock seeder for {$product->name} ({$variant->size} - {$variant->type})",
                    ]
                );
            }
        }
    }
}
