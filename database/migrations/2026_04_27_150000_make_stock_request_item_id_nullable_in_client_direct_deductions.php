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
        Schema::table('client_direct_deductions', function (Blueprint $table) {
            $table->unsignedBigInteger('stock_request_item_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_direct_deductions', function (Blueprint $table) {
            $table->unsignedBigInteger('stock_request_item_id')->nullable(false)->change();
        });
    }
};
