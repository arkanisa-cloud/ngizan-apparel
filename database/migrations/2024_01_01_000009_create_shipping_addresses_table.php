<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel shipping_addresses dengan integrasi Biteship Area ID & Geocoding Koordinat
     */
    public function up(): void
    {
        Schema::create('shipping_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('label')->default('Rumah'); // Rumah, Kantor, dll
            $table->string('recipient_name');
            $table->string('phone_number');
            $table->string('biteship_area_id')->nullable()->index();
            $table->string('province_name');
            $table->string('city_name');
            $table->string('district_name');
            $table->string('postal_code');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->text('full_address');
            $table->string('benchmark_notes')->nullable()->comment('Patokan rumah / warna pagar');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_addresses');
    }
};
