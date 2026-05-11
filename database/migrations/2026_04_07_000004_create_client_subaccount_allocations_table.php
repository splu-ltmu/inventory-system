<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_subaccount_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subaccount_id')->constrained('client_subaccounts')->cascadeOnDelete();
            $table->foreignId('stock_request_item_id')->constrained('stock_request_items')->cascadeOnDelete();
            $table->unsignedInteger('allocated_qty');
            $table->timestamps();

            $table->unique(['subaccount_id', 'stock_request_item_id'], 'subaccount_stock_request_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_subaccount_allocations');
    }
};
