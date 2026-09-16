<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel orders dan order_items lengkap dengan tracking status & detail custom nameset
     */
    public function up(): void
    {
        // Tabel orders
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique()->comment('NGZ-YYYYMMDD-XXXX');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('pending_payment')->comment('pending_payment, paid, in_production, shipped, completed, cancelled, expired');
            $table->decimal('subtotal_amount', 12, 2);
            $table->decimal('shipping_cost', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2);
            $table->string('courier_code')->default('jnt')->comment('jnt, sicepat, jne, grab, gojek');
            $table->string('courier_service_code')->default('ez')->comment('ez, reg, instant');
            $table->string('courier_service_name')->default('J&T Express (Gratis Ongkir)')->comment('Nama Layanan Kurir');
            $table->string('tracking_number')->nullable()->comment('Nomor Resi / Waybill ID Biteship');
            $table->string('biteship_order_id')->nullable();
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone')->comment('Nomor WhatsApp');
            $table->json('shipping_address_snapshot')->comment('JSON Alamat Lengkap, Koordinat & Patokan saat checkout');
            $table->text('notes')->nullable();
            $table->dateTime('expires_at')->nullable()->comment('Batas waktu pembayaran (2 Jam)');
            $table->dateTime('paid_at')->nullable();
            $table->dateTime('shipped_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('order_number');
        });

        // Tabel order_items
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->onDelete('set null');
            $table->string('product_name');
            $table->string('size');
            $table->string('type');
            $table->string('custom_name')->nullable();
            $table->string('custom_number')->nullable();
            $table->string('selected_patch')->nullable();
            $table->decimal('unit_price', 12, 2);
            $table->decimal('custom_fee', 12, 2)->default(0);
            $table->integer('quantity')->default(1);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
