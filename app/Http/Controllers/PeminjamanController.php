<?php

namespace App\Http\Controllers;

use App\Models\DetailPinjam;
use App\Models\Inventaris;
use App\Models\Pegawai;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PeminjamanController extends Controller
{
    protected $pesan = [
        'required'       => ':attribute wajib diisi.',
        'integer'        => ':attribute harus berupa angka bulat.',
        'min'            => ':attribute minimal :min.',
        'date'           => ':attribute harus berupa tanggal yang valid.',
        'exists'         => ':attribute yang dipilih tidak valid.',
        'distinct'       => 'Barang yang sama tidak boleh dipilih lebih dari satu kali.',
        'items.required' => 'Pilih minimal satu barang yang dipinjam.',
        'items.min'      => 'Pilih minimal satu barang yang dipinjam.',
    ];

    protected $atribut = [
        'id_pegawai'            => 'pegawai',
        'tanggal_pinjam'        => 'tanggal pinjam',
        'items.*.id_inventaris' => 'barang',
        'items.*.jumlah'        => 'jumlah',
    ];

    public function index(Request $request)
    {
        $query = Peminjaman::with(['pegawai', 'petugas', 'detail.inventaris'])
            ->withSum('detail as total_unit', 'jumlah');

        if (auth()->user()->isPeminjam()) {
            $query->where('id_petugas', auth()->user()->id_petugas);
        }

        if ($request->filled('status')) {
            $query->where('status_peminjaman', $request->status);
        }

        $data = $query->latest('tanggal_pinjam')
            ->latest('id_peminjaman')
            ->paginate(10)
            ->withQueryString();

        return view('peminjaman.index', compact('data'));
    }

    public function create()
    {
        $barang = Inventaris::where('kondisi', '!=', 'Rusak Berat')
            ->withSum(['detailPinjam as dipinjam' => function ($d) {
                $d->whereHas('peminjaman', function ($p) {
                    $p->where('status_peminjaman', 'Dipinjam');
                });
            }], 'jumlah')
            ->orderBy('nama')
            ->get()
            ->map(function ($b) {
                $tersedia = $b->jumlah - (int) $b->dipinjam;

                return [
                    'id'       => $b->id_inventaris,
                    'tersedia' => $tersedia,
                    'label'    => $b->nama . ' (' . $b->kode_inventaris . ') - tersedia ' . $tersedia . ' unit',
                ];
            })
            ->filter(function ($b) {
                return $b['tersedia'] > 0;
            })
            ->values();

        $baris = array_values(old('items', [['id_inventaris' => '', 'jumlah' => 1]]));

        return view('peminjaman.form', [
            'barang'        => $barang,
            'baris'         => $baris,
            'daftarPegawai' => Pegawai::orderBy('nama_pegawai')->get(),
        ]);
    }

    public function store(Request $request)
    {
        // Buang baris yang barangnya belum dipilih
        $items = collect($request->input('items', []))
            ->filter(function ($r) {
                return ! empty($r['id_inventaris']);
            })
            ->values()
            ->all();

        $request->merge(['items' => $items]);

        $data = $request->validate([
            'id_pegawai'            => 'required|exists:pegawai,id_pegawai',
            'tanggal_pinjam'        => 'required|date',
            'items'                 => 'required|array|min:1',
            'items.*.id_inventaris' => 'required|distinct|exists:inventaris,id_inventaris',
            'items.*.jumlah'        => 'required|integer|min:1',
        ], $this->pesan, $this->atribut);

        $pinjam = DB::transaction(function () use ($data) {
            // Cek stok dengan mengunci baris inventaris agar tidak bentrok
            foreach ($data['items'] as $row) {
                $inv = Inventaris::lockForUpdate()->findOrFail($row['id_inventaris']);

                if ($inv->kondisi === 'Rusak Berat') {
                    throw ValidationException::withMessages([
                        'items' => "{$inv->nama} berkondisi Rusak Berat dan tidak dapat dipinjam.",
                    ]);
                }

                $dipinjam = (int) DetailPinjam::where('id_inventaris', $inv->id_inventaris)
                    ->whereHas('peminjaman', function ($p) {
                        $p->where('status_peminjaman', 'Dipinjam');
                    })
                    ->sum('jumlah');

                $tersedia = $inv->jumlah - $dipinjam;

                if ((int) $row['jumlah'] > $tersedia) {
                    throw ValidationException::withMessages([
                        'items' => "Stok {$inv->nama} tidak cukup. Tersedia {$tersedia} unit.",
                    ]);
                }
            }

            $pinjam = Peminjaman::create([
                'tanggal_pinjam'    => $data['tanggal_pinjam'],
                'status_peminjaman' => 'Dipinjam',
                'id_pegawai'        => $data['id_pegawai'],
                'id_petugas'        => auth()->user()->id_petugas,
            ]);

            foreach ($data['items'] as $row) {
                $pinjam->detail()->create([
                    'id_inventaris' => $row['id_inventaris'],
                    'jumlah'        => $row['jumlah'],
                ]);
            }

            return $pinjam;
        });

        return redirect()->route('peminjaman.show', $pinjam->id_peminjaman)
            ->with('success', 'Peminjaman berhasil dicatat.');
    }

    public function show($id)
    {
        $item = Peminjaman::with(['pegawai', 'petugas', 'detail.inventaris.jenis'])->findOrFail($id);

        if (auth()->user()->isPeminjam() && (int) $item->id_petugas !== (int) auth()->user()->id_petugas) {
            abort(403, 'Anda tidak memiliki hak akses ke data peminjaman ini.');
        }

        return view('peminjaman.show', compact('item'));
    }
}
