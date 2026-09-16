<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel products dan product_variants
     */
    public function up(): void
    {
        // Tabel products (Katalog Jersey Utama)
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('base_price', 12, 2);
            $table->integer('weight_grams')->default(250);
            $table->string('thumbnail_front')->nullable()->comment('WebP front POV image path');
            $table->string('thumbnail_back')->nullable()->comment('WebP back POV image path');
            $table->json('gallery_images')->nullable()->comment('Array of WebP image paths');
            $table->boolean('is_active')->default(true);
            $table->boolean('allow_custom_nameset')->default(false);
            $table->decimal('custom_nameset_price', 12, 2)->default(0);
            $table->boolean('allow_patch')->default(false);
            $table->decimal('patch_price', 12, 2)->default(0);
            $table->json('available_patches')->nullable()->comment('JSON list of patch names/options');
            $table->timestamps();
        });

        // Tabel product_variants (Matriks Ukuran, Tipe Jersey & Stok per SKU)
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('size'); // S, M, L, XL, XXL, 3XL
            $table->string('type'); // Fans Issue, Player Issue, Retro
            $table->integer('stock')->default(0);
            $table->string('sku')->unique();
            $table->decimal('price_adjustment', 12, 2)->default(0);
            $table->timestamps();

            $table->index(['product_id', 'size', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
    }
};
