<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->string('approval_status')->default('Pending')->after('reason_notes');

            $table->unsignedBigInteger('approved_by_user_id')->nullable()->after('approval_status');
            $table->foreign('approved_by_user_id')->references('id')->on('users')->onDelete('set null');

            $table->dateTime('approved_at')->nullable()->after('approved_by_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('stock_adjustments', function (Blueprint $table) {
            $table->dropForeign(['approved_by_user_id']);
            $table->dropColumn(['approval_status', 'approved_by_user_id', 'approved_at']);
        });
    }
};
