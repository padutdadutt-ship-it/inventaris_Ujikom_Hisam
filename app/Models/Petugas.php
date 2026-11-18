<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Petugas extends Authenticatable
{
    // Konstanta id level (sesuai isi tabel level dari seeder)
    public const ADMIN    = 1;
    public const OPERATOR = 2;
    public const PEMINJAM = 3;

    protected $table = 'petugas';
    protected $primaryKey = 'id_petugas';

    protected $fillable = ['username', 'password', 'nama_petugas', 'id_level'];
    protected $hidden   = ['password', 'remember_token'];

    // ---------- Relasi ----------
    public function level()
    {
        return $this->belongsTo(Level::class, 'id_level', 'id_level');
    }

    public function inventaris()
    {
        return $this->hasMany(Inventaris::class, 'id_petugas', 'id_petugas');
    }

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'id_petugas', 'id_petugas');
    }

    // ---------- Helper level ----------
    public function isAdmin(): bool
    {
        return $this->id_level === self::ADMIN;
    }

    public function isOperator(): bool
    {
        return $this->id_level === self::OPERATOR;
    }

    public function isPeminjam(): bool
    {
        return $this->id_level === self::PEMINJAM;
    }
}