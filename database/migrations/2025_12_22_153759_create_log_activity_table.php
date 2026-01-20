<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('log_activity', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // penilai
            $table->unsignedBigInteger('allocation_id'); // alokasi yang dinilai
            $table->string('action'); // 'draft', 'submit'
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('allocation_id')->references('id')->on('allocations')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('log_activity');
    }
};