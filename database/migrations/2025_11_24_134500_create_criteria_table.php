<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('criteria', function (Blueprint $table) {
            $table->id();
            $table->string('name');        // Nama Kriteria
            $table->decimal('weight', 5, 2); // Bobot 0-100%
            $table->string('category');    // Kategori
            $table->boolean('status')->default(true); // Status
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('criteria');
    }
};