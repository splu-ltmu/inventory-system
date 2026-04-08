<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'parent_client_id')) {
                $table->foreignId('parent_client_id')->nullable()->constrained('users')->cascadeOnDelete()->after('office');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'parent_client_id')) {
                $table->dropForeign(['parent_client_id']);
                $table->dropColumn('parent_client_id');
            }
        });
    }
};
