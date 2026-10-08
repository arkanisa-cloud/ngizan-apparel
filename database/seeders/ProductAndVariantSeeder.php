<?php

namespace Database\Seeders;

use App\Enums\JerseySize;
use App\Enums\JerseyType;
use App\Enums\StockReferenceType;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockHistory;
use App\Models\Supplier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductAndVariantSeeder extends Seeder
{
    /**
     * Seed katalog produk jersey realistis beserta matriks varian ukuran, SKU, dan stok awal
     */
    public function run(): void
    {
        // 1. Buat Supplier
        $supplier = Supplier::updateOrCreate(
            ['name' => 'Ngizan Kit Factory Bandung'],
            [
                'contact_person' => 'Kang Asep Apparel',
                'phone'          => '081122334455',
                'email'          => 'supplier@ngizanapparel.com',
                'address'        => 'Kawasan Industri Tekstil Batununggal No. 45, Bandung, Jawa Barat',
            ]
        );

        // Ambil ID Kategori
        $defaultCatId = Category::first()?->id ?? 1;
        $catEropa = Category::where('slug', 'klub-eropa')->first()?->id ?? $defaultCatId;
        $catTimnas = Category::where('slug', 'tim-nasional')->first()?->id ?? $defaultCatId;
        $catRetro = Category::where('slug', 'retro-classics')->first()?->id ?? Category::where('slug', 'bola-sepak')->first()?->id ?? $defaultCatId;
        $catSpecial = Category::where('slug', 'special-edition')->first()?->id ?? Category::where('slug', 'bola-futsal')->first()?->id ?? $defaultCatId;

        // 2. Daftar 5 Produk Realistis
        $productsData = [
            [
                'category_id'          => $catEropa?->id ?? 1,
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
                'category_id'          => $catEropa?->id ?? 1,
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
                'category_id'          => $catTimnas?->id ?? 2,
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
                'category_id'          => $catRetro?->id ?? 3,
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
                'category_id'          => $catSpecial?->id ?? 4,
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
