<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('users', 'office')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('office')->nullable()->after('role');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('users', 'office')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('office');
            });
        }
    }
};