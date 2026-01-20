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
        Schema::table('hasil_penilaian', function (Blueprint $table) {
            $table->string('file_penunjang')
                  ->nullable()
                  ->after('skor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_penilaian', function (Blueprint $table) {
            $table->dropColumn('file_penunjang');
        });
    }
};
