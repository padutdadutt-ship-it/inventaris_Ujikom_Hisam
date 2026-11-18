<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('petugas', function (Blueprint $table) {
            $table->id('id_petugas');
            $table->string('username', 50)->unique();
            $table->string('password');
            $table->string('nama_petugas', 100);
            $table->unsignedBigInteger('id_level');
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('id_level')->references('id_level')->on('level')
                  ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('petugas');
    }
};