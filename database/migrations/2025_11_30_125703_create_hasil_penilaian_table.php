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
        Schema::create('hasil_penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penilaian_id')->constrained('penilaian')->onDelete('cascade');
            $table->foreignId('criterion_id')->constrained('criteria')->onDelete('cascade');
            $table->decimal('skor', 3, 2); // Skor 1.00 - 5.00
            $table->timestamps();

            // Unique constraint: satu kriteria hanya bisa dinilai sekali per penilaian
            $table->unique(['penilaian_id', 'criterion_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_penilaian');
    }
};