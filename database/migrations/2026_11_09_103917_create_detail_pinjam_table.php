<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_pinjam', function (Blueprint $table) {
            $table->id('id_detail_pinjam');
            $table->unsignedBigInteger('id_peminjaman');
            $table->unsignedBigInteger('id_inventaris');
            $table->unsignedInteger('jumlah');
            $table->timestamps();

            $table->foreign('id_peminjaman')->references('id_peminjaman')->on('peminjaman')
                  ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_inventaris')->references('id_inventaris')->on('inventaris')
                  ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pinjam');
    }
};