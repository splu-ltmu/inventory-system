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
        Schema::table('password_reset_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('password_reset_requests', 'email')) {
                $table->string('email')->nullable();
            }
            if (!Schema::hasColumn('password_reset_requests', 'token')) {
                $table->string('token')->nullable()->unique();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('password_reset_requests', function (Blueprint $table) {
            if (Schema::hasColumn('password_reset_requests', 'email')) {
                $table->dropColumn('email');
            }
            if (Schema::hasColumn('password_reset_requests', 'token')) {
                $table->dropColumn('token');
            }
        });
    }
};
