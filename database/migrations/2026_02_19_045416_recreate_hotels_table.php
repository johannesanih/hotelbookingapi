<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop the existing table
        Schema::dropIfExists('hotels');

        // 2. Recreate it with the new column definition
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('address');
            // New status options here
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])->default('pending');

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // To reverse this, you'd technically have to recreate the old version
        Schema::dropIfExists('hotels');
        // ... (Optional: recreate the original 2-option version here)
    }
};
