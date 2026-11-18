<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id('id_peminjaman');
            $table->dateTime('tanggal_pinjam');
            $table->dateTime('tanggal_kembali')->nullable();
            $table->string('status_peminjaman', 20)->default('Dipinjam'); // Dipinjam / Dikembalikan
            $table->unsignedBigInteger('id_pegawai');
            $table->unsignedBigInteger('id_petugas')->nullable(); // tambahan
            $table->timestamps();

            $table->foreign('id_pegawai')->references('id_pegawai')->on('pegawai')
                  ->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('id_petugas')->references('id_petugas')->on('petugas')
                  ->onUpdate('cascade')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};