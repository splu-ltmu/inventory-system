<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('client_direct_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('stock_request_item_id')
                ->constrained('stock_request_items')
                ->cascadeOnDelete();
            $table->foreignId('member_id')
                ->nullable()
                ->constrained('client_members')
                ->nullOnDelete();
            $table->integer('deducted_qty')->unsigned();
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->index(['client_id', 'created_at']);
            $table->index(['member_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_direct_deductions');
    }
};
