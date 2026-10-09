<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hapus kolom supplier_id dari tabel stock_ins dan hapus tabel suppliers
     */
    public function up(): void
    {
        if (Schema::hasTable('stock_ins') && Schema::hasColumn('stock_ins', 'supplier_id')) {
            Schema::table('stock_ins', function (Blueprint $table) {
                $table->dropForeign(['supplier_id']);
                $table->dropColumn('supplier_id');
            });
        }

        Schema::dropIfExists('suppliers');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse needed as suppliers is permanently removed
    }
};
