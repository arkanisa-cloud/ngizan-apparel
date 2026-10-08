<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\SizeChart;
use Illuminate\Database\Seeder;

class SizeChartSeeder extends Seeder
{
    /**
     * Seed preset master template panduan ukuran.
     */
    public function run(): void
    {
        // 1. Jersey Dewasa (Pria / Unisex)
        $adultTops = SizeChart::updateOrCreate(
            ['name' => 'Jersey Dewasa (Pria / Unisex)'],
            [
                'category_type' => 'tops',
                'description'   => 'Diukur dalam posisi baju terbentang rata di atas permukaan datar. Toleransi jahitan ± 1-2 cm.',
                'columns'       => ['Ukuran', 'Lebar Dada (cm)', 'Panjang Baju (cm)', 'Rekomendasi TB / BB'],
                'rows'          => [
                    ['size' => 'S',   'col1' => '48 cm', 'col2' => '68 cm', 'col3' => '160 - 168 cm / 50 - 60 kg'],
                    ['size' => 'M',   'col1' => '50 cm', 'col2' => '70 cm', 'col3' => '168 - 175 cm / 60 - 70 kg'],
                    ['size' => 'L',   'col1' => '52 cm', 'col2' => '72 cm', 'col3' => '172 - 180 cm / 70 - 80 kg'],
                    ['size' => 'XL',  'col1' => '54 cm', 'col2' => '74 cm', 'col3' => '178 - 185 cm / 80 - 90 kg'],
                    ['size' => 'XXL', 'col1' => '56 cm', 'col2' => '76 cm', 'col3' => '180 - 190 cm / 90 - 100 kg'],
                    ['size' => '3XL', 'col1' => '58 cm', 'col2' => '78 cm', 'col3' => '> 185 cm / > 100 kg'],
                ],
                'is_default'    => true,
            ]
        );

        // 2. Jersey Wanita (Women's Fit)
        SizeChart::updateOrCreate(
            ['name' => 'Jersey Wanita (Women\'s Fit)'],
            [
                'category_type' => 'tops',
                'description'   => 'Cutting siluet feminin dengan lekuk pinggang yang pas di badan. Toleransi jahitan ± 1-2 cm.',
                'columns'       => ['Ukuran', 'Lebar Dada (cm)', 'Lingkar Pinggang (cm)', 'Panjang Baju (cm)'],
                'rows'          => [
                    ['size' => 'XS', 'col1' => '42 cm', 'col2' => '64 - 68 cm', 'col3' => '62 cm'],
                    ['size' => 'S',  'col1' => '44 cm', 'col2' => '68 - 72 cm', 'col3' => '64 cm'],
                    ['size' => 'M',  'col1' => '46 cm', 'col2' => '72 - 76 cm', 'col3' => '66 cm'],
                    ['size' => 'L',  'col1' => '48 cm', 'col2' => '76 - 82 cm', 'col3' => '68 cm'],
                    ['size' => 'XL', 'col1' => '51 cm', 'col2' => '82 - 88 cm', 'col3' => '70 cm'],
                ],
                'is_default'    => false,
            ]
        );

        // 3. Jersey Anak-Anak (Kids / Youth Kit)
        SizeChart::updateOrCreate(
            ['name' => 'Jersey Anak-Anak (Kids / Youth Kit)'],
            [
                'category_type' => 'tops',
                'description'   => 'Paket setelan lengkap jersey & celana untuk anak-anak / junior. Toleransi ± 1-2 cm.',
                'columns'       => ['Size Kit', 'Perkiraan Usia', 'Lebar Dada (cm)', 'Panjang Baju (cm)', 'Panjang Celana (cm)'],
                'rows'          => [
                    ['size' => 'Size 16', 'col1' => '3 - 4 Tahun',   'col2' => '35 cm', 'col3' => '44 cm', 'col4' => '30 cm'],
                    ['size' => 'Size 18', 'col1' => '4 - 5 Tahun',   'col2' => '37 cm', 'col3' => '47 cm', 'col4' => '32 cm'],
                    ['size' => 'Size 20', 'col1' => '5 - 6 Tahun',   'col2' => '39 cm', 'col3' => '50 cm', 'col4' => '34 cm'],
                    ['size' => 'Size 22', 'col1' => '7 - 8 Tahun',   'col2' => '41 cm', 'col3' => '53 cm', 'col4' => '36 cm'],
                    ['size' => 'Size 24', 'col1' => '9 - 10 Tahun',  'col2' => '43 cm', 'col3' => '56 cm', 'col4' => '38 cm'],
                    ['size' => 'Size 26', 'col1' => '11 - 12 Tahun', 'col2' => '45 cm', 'col3' => '59 cm', 'col4' => '40 cm'],
                    ['size' => 'Size 28', 'col1' => '13 - 14 Tahun', 'col2' => '47 cm', 'col3' => '62 cm', 'col4' => '42 cm'],
                ],
                'is_default'    => false,
            ]
        );

        // 4. Celana Pendek (Shorts / Match Bottoms)
        SizeChart::updateOrCreate(
            ['name' => 'Celana Pendek (Match Shorts)'],
            [
                'category_type' => 'bottoms',
                'description'   => 'Dilengkapi tali elastis pinggang (drawstring). Pengukuran lingkar pinggang saat relaks hingga ditarik.',
                'columns'       => ['Ukuran', 'Lingkar Pinggang (cm)', 'Panjang Celana (cm)', 'Lingkar Paha (cm)'],
                'rows'          => [
                    ['size' => 'S',   'col1' => '64 - 84 cm', 'col2' => '42 cm', 'col3' => '56 cm'],
                    ['size' => 'M',   'col1' => '68 - 90 cm', 'col2' => '44 cm', 'col3' => '60 cm'],
                    ['size' => 'L',   'col1' => '72 - 96 cm', 'col2' => '46 cm', 'col3' => '64 cm'],
                    ['size' => 'XL',  'col1' => '76 - 102 cm', 'col2' => '48 cm', 'col3' => '68 cm'],
                    ['size' => 'XXL', 'col1' => '80 - 110 cm', 'col2' => '50 cm', 'col3' => '72 cm'],
                ],
                'is_default'    => false,
            ]
        );

        // 5. Trackpants / Celana Training Panjang
        SizeChart::updateOrCreate(
            ['name' => 'Trackpants / Celana Training Panjang'],
            [
                'category_type' => 'bottoms',
                'description'   => 'Material stretch poliester dengan resleting ankle. Toleransi ± 1-2 cm.',
                'columns'       => ['Ukuran', 'Lingkar Pinggang (cm)', 'Panjang Total (cm)', 'Lingkar Paha (cm)', 'Rekomendasi TB'],
                'rows'          => [
                    ['size' => 'S',   'col1' => '66 - 86 cm', 'col2' => '94 cm',  'col3' => '56 cm', 'col4' => '160 - 168 cm'],
                    ['size' => 'M',   'col1' => '70 - 92 cm', 'col2' => '97 cm',  'col3' => '60 cm', 'col4' => '168 - 175 cm'],
                    ['size' => 'L',   'col1' => '74 - 98 cm', 'col2' => '100 cm', 'col3' => '64 cm', 'col4' => '172 - 180 cm'],
                    ['size' => 'XL',  'col1' => '78 - 104 cm', 'col2' => '103 cm', 'col3' => '68 cm', 'col4' => '178 - 185 cm'],
                    ['size' => 'XXL', 'col1' => '82 - 112 cm', 'col2' => '106 cm', 'col3' => '72 cm', 'col4' => '180 - 190 cm'],
                ],
                'is_default'    => false,
            ]
        );

        // Assign default size chart ke produk yang belum punya size_chart_id
        Product::whereNull('size_chart_id')->update(['size_chart_id' => $adultTops->id]);
    }
}
