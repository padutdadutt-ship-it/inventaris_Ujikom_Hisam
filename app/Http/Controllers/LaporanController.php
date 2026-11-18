<?php

namespace App\Http\Controllers;

use App\Models\Inventaris;
use App\Models\Jenis;
use App\Models\Pegawai;
use App\Models\Peminjaman;
use App\Models\Ruang;
use Illuminate\Http\Request;
use App\Models\PeminjamanRuang;

class LaporanController extends Controller
{
    protected $kondisi = ['Baik', 'Rusak Ringan', 'Rusak Berat'];

    public function index()
    {
        return redirect()->route('laporan.inventaris');
    }

    public function inventaris(Request $request)
    {
        $query = Inventaris::with(['jenis', 'ruang'])
            ->withSum(['detailPinjam as dipinjam' => function ($d) {
                $d->whereHas('peminjaman', function ($p) {
                    $p->where('status_peminjaman', 'Dipinjam');
                });
            }], 'jumlah');

        if ($request->filled('id_jenis')) {
            $query->where('id_jenis', $request->id_jenis);
        }

        if ($request->filled('id_ruang')) {
            $query->where('id_ruang', $request->id_ruang);
        }

        if ($request->filled('kondisi') && in_array($request->kondisi, $this->kondisi, true)) {
            $query->where('kondisi', $request->kondisi);
        }

        $data = $query->orderBy('kode_inventaris')->get();

        return view('laporan.inventaris', [
            'data'          => $data,
            'totalJumlah'   => (int) $data->sum('jumlah'),
            'totalDipinjam' => (int) $data->sum('dipinjam'),
            'daftarJenis'   => Jenis::orderBy('nama_jenis')->get(),
            'daftarRuang'   => Ruang::orderBy('nama_ruang')->get(),
            'daftarKondisi' => $this->kondisi,
            'cetak'         => $request->boolean('cetak'),
        ]);
    }

    public function peminjaman(Request $request)
    {
        $request->validate([
            'dari'   => 'nullable|date',
            'sampai' => 'nullable|date|after_or_equal:dari',
        ], [
            'date'                => ':attribute tidak valid.',
            'sampai.after_or_equal' => 'Tanggal sampai tidak boleh lebih awal dari tanggal dari.',
        ]);

        $query = Peminjaman::with(['pegawai', 'petugas', 'detail.inventaris'])
            ->withSum('detail as total_unit', 'jumlah');

        if ($request->filled('dari')) {
            $query->whereDate('tanggal_pinjam', '>=', $request->dari);
        }

        if ($request->filled('sampai')) {
            $query->whereDate('tanggal_pinjam', '<=', $request->sampai);
        }

        if ($request->filled('status') && in_array($request->status, ['Dipinjam', 'Dikembalikan'], true)) {
            $query->where('status_peminjaman', $request->status);
        }

        if ($request->filled('id_pegawai')) {
            $query->where('id_pegawai', $request->id_pegawai);
        }

        $data = $query->orderBy('tanggal_pinjam', 'desc')->get();

        return view('laporan.peminjaman', [
            'data'              => $data,
            'totalTransaksi'    => $data->count(),
            'totalDipinjam'     => $data->where('status_peminjaman', 'Dipinjam')->count(),
            // PERBAIKAN: Mengambil sum dari relasi detail
            'totalUnit'         => (int) $data->flatMap->detail->sum('jumlah'),
            'totalUnitDipinjam' => (int) $data->where('status_peminjaman', 'Dipinjam')->flatMap->detail->sum('jumlah'),
            'daftarPegawai'     => Pegawai::orderBy('nama_pegawai')->get(),
            'cetak'             => $request->boolean('cetak'),
        ]);
    }

    // =================================================================
    // Function untuk Laporan Peminjaman Ruangan
    // =================================================================
    public function peminjamanRuang(Request $request)
    {
        $dari_tanggal = $request->query('dari_tanggal');
        $sampai_tanggal = $request->query('sampai_tanggal');
        $status = $request->query('status');

        $query = PeminjamanRuang::with('ruang');

        if ($dari_tanggal && $sampai_tanggal) {
            $query->whereBetween('tanggal', [$dari_tanggal, $sampai_tanggal]);
        }

        if (in_array($status, ['Dipinjam', 'Selesai'])) {
            $query->where('status', $status);
        }

        $data = $query->orderByDesc('tanggal')->orderByDesc('jam_mulai')->get();

        return view('laporan.peminjaman_ruang', compact('data', 'dari_tanggal', 'sampai_tanggal', 'status'));
    }
}