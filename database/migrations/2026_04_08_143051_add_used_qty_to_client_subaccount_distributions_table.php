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
        Schema::table('client_subaccount_distributions', function (Blueprint $table) {
            $table->unsignedInteger('used_qty')->default(0)->after('distributed_qty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_subaccount_distributions', function (Blueprint $table) {
            $table->dropColumn('used_qty');
        });
    }
};
