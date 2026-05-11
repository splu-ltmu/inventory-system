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
        Schema::create('urgent_outbound_recipients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('office')->nullable();
            $table->text('reason')->nullable();
            $table->timestamps();
            
            $table->index(['name', 'office']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('urgent_outbound_recipients');
    }
};
