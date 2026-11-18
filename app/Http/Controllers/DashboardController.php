<?php

namespace App\Http\Controllers;

use App\Models\Inventaris;
use App\Models\Jenis;
use App\Models\Pegawai;
use App\Models\Peminjaman;
use App\Models\Ruang;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'totalInventaris'   => Inventaris::sum('jumlah') ?? 0,
            'totalJenis'        => Jenis::count(),
            'totalRuang'        => Ruang::count(),
            'totalPegawai'      => Pegawai::count(),
            'peminjamanAktif'   => Peminjaman::where('status_peminjaman', 'Dipinjam')->count(),
            'peminjamanSelesai' => Peminjaman::where('status_peminjaman', 'Dikembalikan')->count(),
            'terbaru'           => Peminjaman::with('pegawai')
                                        ->latest('tanggal_pinjam')
                                        ->take(5)
                                        ->get(),
        ]);
    }
}