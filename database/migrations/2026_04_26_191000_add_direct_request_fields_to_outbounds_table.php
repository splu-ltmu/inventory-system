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
        Schema::table('outbounds', function (Blueprint $table) {
            $table->foreignId('member_id')->nullable()->after('urgent_recipient_office');
            $table->boolean('is_direct_request')->default(false)->after('member_id');
            
            $table->foreign('member_id')->references('id')->on('client_members')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outbounds', function (Blueprint $table) {
            $table->dropForeign(['member_id']);
            $table->dropColumn(['member_id', 'is_direct_request']);
        });
    }
};
