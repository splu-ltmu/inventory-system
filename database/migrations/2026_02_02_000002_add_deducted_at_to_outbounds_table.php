<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('outbounds', function (Blueprint $table) {
            // marks when stock was deducted (null = not yet deducted)
            $table->timestamp('deducted_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('outbounds', function (Blueprint $table) {
            $table->dropColumn('deducted_at');
        });
    }
};
