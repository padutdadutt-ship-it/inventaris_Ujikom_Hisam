<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventaris extends Model
{
    public $timestamps = false;

    protected $table = 'inventaris';
    protected $primaryKey = 'id_inventaris';

    protected $fillable = [
        'nama', 'kondisi', 'keterangan', 'jumlah',
        'id_jenis', 'tanggal_register', 'id_ruang',
        'kode_inventaris', 'id_petugas',
    ];

    protected $casts = [
        'tanggal_register' => 'date',
    ];

    public function jenis()
    {
        return $this->belongsTo(Jenis::class, 'id_jenis', 'id_jenis');
    }

    public function ruang()
    {
        return $this->belongsTo(Ruang::class, 'id_ruang', 'id_ruang');
    }

    public function petugas()
    {
        return $this->belongsTo(Petugas::class, 'id_petugas', 'id_petugas');
    }

    public function detailPinjam()
    {
        return $this->hasMany(DetailPinjam::class, 'id_inventaris', 'id_inventaris');
    }
}