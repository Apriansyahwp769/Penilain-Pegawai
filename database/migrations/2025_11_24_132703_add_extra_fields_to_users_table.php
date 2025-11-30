<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nip')->unique()->nullable();
            $table->foreignId('division_id')->nullable()->constrained('divisions');
            $table->foreignId('position_id')->nullable()->constrained('positions');
            $table->string('phone')->nullable();
            $table->date('join_date')->nullable();
            $table->enum('role', ['admin', 'ketua_divisi', 'staff'])->default('staff');
            $table->boolean('is_active')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'nip', 
                'division_id', 
                'position_id', 
                'phone', 
                'join_date', 
                'role', 
                'is_active'
            ]);
        });
    }
};