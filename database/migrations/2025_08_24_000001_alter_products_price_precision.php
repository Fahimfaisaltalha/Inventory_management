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
        // Increase price column precision to allow larger values (was DECIMAL(8,2))
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 12, 2)->change(); // max 9,999,999,999.99
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->change();
        });
    }
};
