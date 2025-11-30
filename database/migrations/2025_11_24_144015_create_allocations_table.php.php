<?php
// database/migrations/2024_01_01_create_allocations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siklus_id')->constrained('siklus')->onDelete('cascade');
            $table->foreignId('penilai_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('dinilai_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending');
            $table->timestamps();

            // Unique constraint untuk mencegah duplikasi
            $table->unique(['siklus_id', 'penilai_id', 'dinilai_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('allocations');
    }
};