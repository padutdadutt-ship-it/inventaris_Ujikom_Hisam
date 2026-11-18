<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PengembalianController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'Dipinjam');

        $query = Peminjaman::with(['pegawai', 'petugas', 'detail.inventaris'])
            ->withSum('detail as total_unit', 'jumlah');

        if (in_array($status, ['Dipinjam', 'Dikembalikan'], true)) {
            $query->where('status_peminjaman', $status);
        }

        if ($request->filled('q')) {
            $kata = $request->q;
            $query->whereHas('pegawai', function ($p) use ($kata) {
                $p->where('nama_pegawai', 'like', "%{$kata}%")
                  ->orWhere('nip', 'like', "%{$kata}%");
            });
        }

        $data = $query->latest('tanggal_pinjam')
            ->latest('id_peminjaman')
            ->paginate(10)
            ->withQueryString();

        return view('pengembalian.index', compact('data', 'status'));
    }

    public function kembalikan($id)
    {
        $berhasil = DB::transaction(function () use ($id) {
            $item = Peminjaman::lockForUpdate()->findOrFail($id);

            if ($item->status_peminjaman !== 'Dipinjam') {
                return false;
            }

            // Tanggal kembali tidak boleh lebih awal dari tanggal pinjam
            
            $item->update([
                'status_peminjaman' => 'Dikembalikan',
                'tanggal_kembali'   => now()->max($item->tanggal_pinjam),
            ]);

            return true;
        });

        if (! $berhasil) {
            return back()->with('error', 'Peminjaman ini sudah dikembalikan sebelumnya.');
        }

        return back()->with('success', 'Barang berhasil dikembalikan. Stok tersedia sudah dipulihkan.');
    }
}
