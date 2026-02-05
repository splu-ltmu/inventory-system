<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('office');

            $table->enum('status', [
                'pending',
                'approved',          // has at least 1 approved item
                'rejected',          // all items rejected
                'ready_to_receive',  // code generated
                'released'           // moved to outbound
            ])->default('pending');

            $table->string('verification_code')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_requests');
    }
};
