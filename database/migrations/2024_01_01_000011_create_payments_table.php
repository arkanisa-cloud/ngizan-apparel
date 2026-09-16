<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel payments untuk integrasi Midtrans Snap & Webhook logging
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained('orders')->onDelete('cascade');
            $table->string('transaction_id')->nullable()->comment('Midtrans Transaction ID');
            $table->string('payment_type')->nullable()->comment('qris, bank_transfer, gopay, cstore, dll');
            $table->string('snap_token')->nullable();
            $table->string('snap_redirect_url', 500)->nullable();
            $table->decimal('gross_amount', 12, 2);
            $table->string('transaction_status')->default('pending')->comment('pending, settlement, expire, cancel, deny');
            $table->json('raw_payload')->nullable()->comment('Midtrans Webhook notification payload');
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();

            $table->index('transaction_id');
            $table->index('transaction_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
