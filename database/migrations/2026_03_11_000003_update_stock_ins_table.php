<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_ins', function (Blueprint $table) {
            $table->unsignedBigInteger('supplier_id')->nullable()->after('id');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('restrict');

            $table->decimal('subtotal', 10, 2)->default(0)->after('received_by_user_id');
            $table->decimal('tax_amount', 10, 2)->default(0)->after('subtotal');
            $table->decimal('discount_amount', 10, 2)->default(0)->after('tax_amount');
            $table->decimal('total_cost', 10, 2)->default(0)->after('discount_amount');
        });
    }

    public function down(): void
    {
        Schema::table('stock_ins', function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
            $table->dropColumn(['supplier_id', 'subtotal', 'tax_amount', 'discount_amount', 'total_cost']);
        });
    }
};
