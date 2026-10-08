<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed kategori utama jersey Ngizan Apparel
     */
    public function run(): void
    {
        $categories = [
            [
                'name'      => 'Jersey',
                'slug'      => 'jersey',
                'image'     => 'categories/jersey.webp',
                'is_active' => true,
            ],
            [
                'name'      => 'kaos-kaki',
                'slug'      => 'kaos-kaki',
                'image'     => 'categories/kaos-kaki.webp',
                'is_active' => true,
            ],
            [
                'name'      => 'Bola Sepak',
                'slug'      => 'bola-sepak',
                'image'     => 'categories/bola-sepak.webp',
                'is_active' => true,
            ],
            [
                'name'      => 'Bola Futsal',
                'slug'      => 'bola-futsal',
                'image'     => 'categories/bola-futsal.webp',
                'is_active' => true,
            ],
            [
                'name'      => 'Manset',
                'slug'      => 'manset',
                'image'     => 'categories/manset.webp',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['slug' => $cat['slug']],
                $cat
            );
        }
    }
}
