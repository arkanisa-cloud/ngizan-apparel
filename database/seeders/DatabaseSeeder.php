<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed seluruh pondasi data Ngizan Apparel
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            // CategorySeeder::class,
            // ProductAndVariantSeeder::class,
            // BannerSeeder::class,
            // SizeChartSeeder::class,
        ]);
    }
}
