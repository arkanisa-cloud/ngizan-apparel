<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('premium_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('subscription_code')->unique()->comment('PREM-YYYYMMDD-XXXX');
            $table->decimal('amount', 12, 2)->default(100000.00);
            $table->integer('duration_days')->default(365);
            $table->string('payment_status')->default('pending')->comment('pending, settlement, expire, cancel');
            $table->string('snap_token')->nullable();
            $table->text('snap_redirect_url')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'payment_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('premium_subscriptions');
    }
};
