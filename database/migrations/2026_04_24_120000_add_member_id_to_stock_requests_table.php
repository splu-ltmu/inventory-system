<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('stock_requests', function (Blueprint $table) {
            $table->foreignId('member_id')
                ->nullable()
                ->constrained('client_members')
                ->nullOnDelete()
                ->after('client_id');

            $table->text('reason')
                ->nullable()
                ->after('member_id');
        });

        Schema::table('stock_request_items', function (Blueprint $table) {
            $table->text('rejection_reason')
                ->nullable()
                ->after('approved_qty');
        });
    }

    public function down(): void
    {
        Schema::table('stock_requests', function (Blueprint $table) {
            $table->dropForeign(['member_id']);
            $table->dropColumn(['member_id', 'reason']);
        });

        Schema::table('stock_request_items', function (Blueprint $table) {
            $table->dropColumn('rejection_reason');
        });
    }
};
