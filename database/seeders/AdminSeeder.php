<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Seed akun superadmin dan customer demo
     */
    public function run(): void
    {
        // Superadmin Akun
        User::updateOrCreate(
            ['email' => 'admin@ngizanapparel.com'],
            [
                'name'              => 'Super Admin Ngizan',
                'password'          => Hash::make('password'),
                'role'              => 'admin',
                'phone'             => '081234567890',
                'email_verified_at' => now(),
            ]
        );

        // Akun Customer Demo untuk testing checkout & cart
        User::updateOrCreate(
            ['email' => 'customer@ngizanapparel.com'],
            [
                'name'              => 'Alvaro Customer',
                'password'          => Hash::make('password'),
                'role'              => 'customer',
                'phone'             => '081298765432',
                'email_verified_at' => now(),
            ]
        );
    }
}
