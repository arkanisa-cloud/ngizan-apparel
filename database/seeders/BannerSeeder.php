<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Seed record awal Hero Section dan Promo Banner
     */
    public function run(): void
    {
        $banners = [
            ['key' => 'hero', 'image' => null],
            ['key' => 'promo_banner', 'image' => null],
        ];

        foreach ($banners as $banner) {
            Banner::updateOrCreate(
                ['key' => $banner['key']],
                $banner
            );
        }
    }
}
