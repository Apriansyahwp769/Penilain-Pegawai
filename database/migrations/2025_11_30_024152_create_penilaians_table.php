<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('allocation_id')->constrained('allocations')->onDelete('cascade');
            $table->decimal('skor_akhir', 5, 2)->nullable();
            $table->enum('status', ['belum_dinilai','draft', 'menunggu_verifikasi', 'selesai'])->default('belum_dinilai');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // Auto insert penilaian untuk setiap allocation yang sudah ada
        DB::statement("
            INSERT INTO penilaian (allocation_id, status, created_at, updated_at)
            SELECT id, 'belum_dinilai', NOW(), NOW()
            FROM allocations
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penilaians');
    }
};