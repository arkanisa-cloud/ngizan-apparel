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
        Schema::create('size_charts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category_type')->default('tops'); // tops, bottoms, outerwear, other
            $table->text('description')->nullable();
            $table->json('columns')->comment('List of column header labels');
            $table->json('rows')->comment('List of size row data objects');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('size_chart_id')
                ->nullable()
                ->after('category_id')
                ->constrained('size_charts')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('size_chart_id');
        });

        Schema::dropIfExists('size_charts');
    }
};
