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
            $table->foreignId('urgent_recipient_id')->nullable()->after('client_id');
            $table->string('urgent_recipient_name')->nullable()->after('urgent_recipient_id');
            $table->string('urgent_recipient_office')->nullable()->after('urgent_recipient_name');
            $table->boolean('is_urgent_outbound')->default(false)->after('urgent_recipient_office');
            
            $table->foreign('urgent_recipient_id')->references('id')->on('urgent_outbound_recipients')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('outbounds', function (Blueprint $table) {
            $table->dropForeign(['urgent_recipient_id']);
            $table->dropColumn(['urgent_recipient_id', 'urgent_recipient_name', 'urgent_recipient_office', 'is_urgent_outbound']);
        });
    }
};
