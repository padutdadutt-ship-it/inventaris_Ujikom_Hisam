<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';
    protected $primaryKey = 'id_peminjaman';

    protected $fillable = [
        'tanggal_pinjam', 'tanggal_kembali',
        'status_peminjaman', 'id_pegawai', 'id_petugas',
    ];

    protected $casts = [
        'tanggal_pinjam'  => 'datetime',
        'tanggal_kembali' => 'datetime',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'id_pegawai', 'id_pegawai');
    }

    public function petugas()
    {
        return $this->belongsTo(Petugas::class, 'id_petugas', 'id_petugas');
    }

    public function detail()
    {
        return $this->hasMany(DetailPinjam::class, 'id_peminjaman', 'id_peminjaman');
    }
}