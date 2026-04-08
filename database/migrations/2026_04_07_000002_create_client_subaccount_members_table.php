<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_subaccount_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subaccount_id')->constrained('client_subaccounts')->cascadeOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_subaccount_members');
    }
};
