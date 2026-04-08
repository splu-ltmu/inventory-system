<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_subaccount_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('client_subaccount_members')->cascadeOnDelete();
            $table->foreignId('stock_request_item_id')->constrained('stock_request_items')->cascadeOnDelete();
            $table->integer('distributed_qty')->unsigned();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_subaccount_distributions');
    }
};
