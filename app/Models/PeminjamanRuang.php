<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeminjamanRuang extends Model
{
    protected $table = 'peminjaman_ruang';

    protected $fillable = [
        'id_ruang', 'nama_peminjam', 'keperluan', 'tanggal',
        'jam_mulai', 'jam_selesai', 'status', 'dicatat_oleh',
    ];

    protected $casts = ['tanggal' => 'date'];

    public function ruang()
    {
        return $this->belongsTo(Ruang::class, 'id_ruang');
    }
}