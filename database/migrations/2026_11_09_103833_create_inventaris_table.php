<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventaris', function (Blueprint $table) {
            $table->id('id_inventaris');
            $table->string('nama', 150);
            $table->string('kondisi', 50);
            $table->text('keterangan')->nullable();
            $table->unsignedInteger('jumlah')->default(0);
            $table->unsignedBigInteger('id_jenis');
            $table->date('tanggal_register');
            $table->unsignedBigInteger('id_ruang');
            $table->string('kode_inventaris', 30)->unique();
            $table->unsignedBigInteger('id_petugas');
            $table->timestamps();

            $table->foreign('id_jenis')->references('id_jenis')->on('jenis')
                  ->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('id_ruang')->references('id_ruang')->on('ruang')
                  ->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('id_petugas')->references('id_petugas')->on('petugas')
                  ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventaris');
    }
};