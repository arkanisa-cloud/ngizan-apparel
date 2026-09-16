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
                'name'      => 'Klub Eropa',
                'slug'      => 'klub-eropa',
                'image'     => 'categories/klub-eropa.webp',
                'is_active' => true,
            ],
            [
                'name'      => 'Tim Nasional',
                'slug'      => 'tim-nasional',
                'image'     => 'categories/tim-nasional.webp',
                'is_active' => true,
            ],
            [
                'name'      => 'Retro Classics',
                'slug'      => 'retro-classics',
                'image'     => 'categories/retro-classics.webp',
                'is_active' => true,
            ],
            [
                'name'      => 'Special Edition',
                'slug'      => 'special-edition',
                'image'     => 'categories/special-edition.webp',
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
