<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_adjustment_items', function (Blueprint $table) {
            // Add the ERD-aligned columns
            $table->integer('before_quantity')->default(0)->after('product_id');
            $table->integer('adjusted_quantity')->default(0)->after('before_quantity');
            $table->integer('after_quantity')->default(0)->after('adjusted_quantity');

            // Remove the old quantity_change column
            $table->dropColumn('quantity_change');
        });
    }

    public function down(): void
    {
        Schema::table('stock_adjustment_items', function (Blueprint $table) {
            $table->integer('quantity_change')->after('product_id');
            $table->dropColumn(['before_quantity', 'adjusted_quantity', 'after_quantity']);
        });
    }
};
