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
        Schema::table('client_subaccount_allocations', function (Blueprint $table) {
            $table->unsignedInteger('used_qty')->default(0)->after('allocated_qty');
        });
    }

    public function down(): void
    {
        Schema::table('client_subaccount_allocations', function (Blueprint $table) {
            $table->dropColumn('used_qty');
        });
    }
};
