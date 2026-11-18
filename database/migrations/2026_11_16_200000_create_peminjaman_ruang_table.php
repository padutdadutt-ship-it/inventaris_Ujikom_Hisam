<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('peminjaman_ruang', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_ruang');
            $table->string('nama_peminjam', 100);
            $table->string('keperluan');
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('status', 20)->default('Dipinjam');
            $table->string('dicatat_oleh', 100)->nullable();
            $table->timestamps();
            $table->index(['id_ruang', 'tanggal']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('peminjaman_ruang');
    }
};