<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('log_activity', function (Blueprint $table) {
            $table->unsignedBigInteger('penilaian_id')->nullable()->after('allocation_id');
            $table->foreign('penilaian_id')->references('id')->on('penilaian')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('log_activity', function (Blueprint $table) {
            $table->dropForeign(['penilaian_id']);
            $table->dropColumn('penilaian_id');
        });
    }
};