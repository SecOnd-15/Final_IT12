<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fix suppliers.supplier_name: VARCHAR(150) → VARCHAR(180) per ERD
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('supplier_name', 180)->change();
        });

        // Fix products.manufacturer_barcode: VARCHAR(30) → VARCHAR(50) per ERD
        Schema::table('products', function (Blueprint $table) {
            $table->string('manufacturer_barcode', 50)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('supplier_name', 150)->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('manufacturer_barcode', 30)->nullable()->change();
        });
    }
};
