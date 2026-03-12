<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop status from stock_ins (not in ERD)
        Schema::table('stock_ins', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        // 2. Drop supplier_id from stock_in_items (moved to stock_ins header per ERD)
        Schema::table('stock_in_items', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropColumn('supplier_id');
        });

        // 3. Rename total_line_refund -> total_line_refunded in return_items (ERD naming)
        Schema::table('return_items', function (Blueprint $table) {
            $table->renameColumn('total_line_refund', 'total_line_refunded');
        });
    }

    public function down(): void
    {
        Schema::table('stock_ins', function (Blueprint $table) {
            $table->string('status')->default('completed')->after('reference_no');
        });

        Schema::table('stock_in_items', function (Blueprint $table) {
            $table->unsignedBigInteger('supplier_id')->nullable()->after('product_id');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('restrict');
        });

        Schema::table('return_items', function (Blueprint $table) {
            $table->renameColumn('total_line_refunded', 'total_line_refund');
        });
    }
};
