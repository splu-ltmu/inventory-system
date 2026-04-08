<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_subaccounts', function (Blueprint $table) {
            if (!Schema::hasColumn('client_subaccounts', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete()->after('client_user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('client_subaccounts', function (Blueprint $table) {
            if (Schema::hasColumn('client_subaccounts', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};
