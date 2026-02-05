<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('outbounds', function (Blueprint $table) {
            $table->id();

            $table->foreignId('stock_id')->constrained('stocks')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();

            $table->string('office');
            $table->string('description')->nullable(); // copy from stock
            $table->integer('total');

            $table->enum('approval', ['pending','approved','declined'])->default('approved');
            $table->enum('status', ['on process','declined','received'])->default('on process');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outbounds');
    }
};
