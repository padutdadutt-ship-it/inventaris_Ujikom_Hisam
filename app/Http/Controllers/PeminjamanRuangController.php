<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use App\Models\PeminjamanRuang;
use App\Models\Ruang;
use Illuminate\Http\Request;

class PeminjamanRuangController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $query = PeminjamanRuang::with('ruang')
            ->orderByDesc('tanggal')
            ->orderByDesc('jam_mulai');

        if ($status) {
            $query->where('status', $status);
        }

        $data = $query->paginate(10)->withQueryString();

        return view('peminjaman_ruang.index', compact('data', 'status'));
    }

    public function create()
    {
        $ruang = Ruang::orderBy('nama_ruang')->get();
        $daftarPegawai = Pegawai::orderBy('nama_pegawai')->get();

        return view('peminjaman_ruang.create', compact('ruang', 'daftarPegawai'));
    }

    public function store(Request $request)
    {
        $valid = $request->validate([
            'id_ruang'    => 'required',
            'id_pegawai'  => 'required',
            'keperluan'   => 'required|string|max:255',
            'tanggal'     => 'required|date',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required',
        ]);

        $pegawai = Pegawai::where('id_pegawai', $valid['id_pegawai'])->firstOrFail();

        PeminjamanRuang::create([
            'id_ruang'      => $valid['id_ruang'],
            'nama_peminjam' => $pegawai->nama_pegawai,
            'keperluan'     => $valid['keperluan'],
            'tanggal'       => $valid['tanggal'],
            'jam_mulai'     => $valid['jam_mulai'],
            'jam_selesai'   => $valid['jam_selesai'],
            'status'        => 'Dipinjam',
            'dicatat_oleh'  => auth()->user()->nama_petugas,
        ]);

        return redirect()->route('peminjaman-ruang.index')
            ->with('success', 'Peminjaman ruangan berhasil dicatat.');
    }

    public function selesai($id)
    {
        $p = PeminjamanRuang::findOrFail($id);
        $p->update(['status' => 'Selesai']);

        return redirect()->route('peminjaman-ruang.index')
            ->with('success', 'Peminjaman ruangan ditandai selesai.');
    }
}