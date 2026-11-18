<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Profiling Peminjam: siswa, staff, guru, caraka (tabel pegawai).
 * nip = NIS/NIP, nama_pegawai = nama, alamat = kelas / golongan.
 */
class PegawaiController extends Controller
{
    protected $pesan = [
        'required'       => ':attribute wajib diisi.',
        'max'            => ':attribute maksimal :max karakter.',
        'unique'         => ':attribute sudah dipakai orang lain.',
        'digits_between' => ':attribute harus berupa angka :min sampai :max digit.',
    ];

    protected $atribut = [
        'nip'          => 'NIS/NIP',
        'nama_pegawai' => 'nama',
        'alamat'       => 'kelas/golongan',
    ];

    protected $polaTingkat = ['X' => 'X %', 'XI' => 'XI %', 'XII' => 'XII %'];

    // Golongan non-siswa (isi kolom alamat harus persis sama)
    protected $golongan = ['Staff', 'Guru', 'Caraka'];

    protected function semuaKelas()
    {
        return Pegawai::orderBy('id_pegawai')->pluck('alamat')->filter()->unique()->values();
    }

    protected function aturan(Request $request, $item = null)
    {
        $nip = ['required', 'digits_between:8,20'];
        if (! $item || $request->nip !== $item->nip) {
            $nip[] = Rule::unique('pegawai', 'nip');
        }

        return [
            'nama_pegawai' => 'required|max:100',
            'nip'          => $nip,
            'alamat'       => 'required|max:100',
        ];
    }

    // Setelah simpan: guru/staff/caraka ke halaman golongannya, siswa ke kelasnya
    protected function tujuan($alamat)
    {
        return in_array($alamat, $this->golongan)
            ? ['tingkat' => $alamat]
            : ['kelas' => $alamat];
    }

    public function index(Request $request)
    {
        $tingkat = $request->query('tingkat');

        $query = Pegawai::withCount('peminjaman')
            ->with(['peminjaman' => function ($p) {
                $p->where('status_peminjaman', 'Dipinjam')->with('detail.inventaris');
            }]);

        $golongan = false;

        if (isset($this->polaTingkat[$tingkat])) {
            $query->where('alamat', 'like', $this->polaTingkat[$tingkat]);
        } elseif (in_array($tingkat, $this->golongan)) {
            $golongan = true;
            $query->where('alamat', $tingkat);
        } else {
            $tingkat = null;
            // "Semua Siswa" tidak mencampur staff/guru/caraka
            if (! $request->filled('kelas')) {
                $query->whereNotIn('alamat', $this->golongan);
            }
        }

        if ($request->filled('kelas')) {
            $query->where('alamat', $request->kelas);
        }

        if ($request->filled('q')) {
            $kata = $request->q;
            $query->where(function ($w) use ($kata) {
                $w->where('nama_pegawai', 'like', "%{$kata}%")
                  ->orWhere('nip', 'like', "%{$kata}%");
            });
        }

        $semuaKelas = $this->semuaKelas()->reject(function ($k) {
            return in_array($k, $this->golongan);
        })->values();

        $daftarKelas = ($tingkat && ! $golongan)
            ? $semuaKelas->filter(function ($k) use ($tingkat) {
                return Str::startsWith($k, $tingkat . ' ');
            })->values()
            : $semuaKelas;

        if ($golongan) {
            $judul   = 'Data ' . $tingkat;
            $sebutan = $tingkat;
        } else {
            $judul   = $tingkat ? 'Data Siswa Kelas ' . $tingkat : 'Data Semua Siswa';
            $sebutan = 'Siswa';
        }

        return view('pegawai.index', [
            'data'        => $query->orderBy('id_pegawai')->paginate(25)->withQueryString(),
            'tingkat'     => $tingkat,
            'golongan'    => $golongan,
            'sebutan'     => $sebutan,
            'judul'       => $judul,
            'daftarKelas' => $daftarKelas,
        ]);
    }

    public function create(Request $request)
    {
        $tingkat = $request->query('tingkat');
        $awal = in_array($tingkat, $this->golongan) ? $tingkat : $request->query('kelas');

        return view('pegawai.form', [
            'item'        => new Pegawai(['alamat' => $awal]),
            'daftarKelas' => $this->semuaKelas(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->aturan($request), $this->pesan, $this->atribut);

        Pegawai::create($data);

        return redirect()->route('pegawai.index', $this->tujuan($data['alamat']))
            ->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit($id)
    {
        return view('pegawai.form', [
            'item'        => Pegawai::findOrFail($id),
            'daftarKelas' => $this->semuaKelas(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = Pegawai::findOrFail($id);

        $data = $request->validate($this->aturan($request, $item), $this->pesan, $this->atribut);

        $item->update($data);

        return redirect()->route('pegawai.index', $this->tujuan($data['alamat']))
            ->with('success', 'Data berhasil diubah.');
    }

    public function destroy($id)
    {
        $item = Pegawai::withCount('peminjaman')->findOrFail($id);

        if ($item->peminjaman_count > 0) {
            return redirect()->route('pegawai.index')
                ->with('error', 'Data tidak dapat dihapus karena sudah memiliki riwayat peminjaman.');
        }

        $item->delete();

        return redirect()->route('pegawai.index')->with('success', 'Data berhasil dihapus.');
    }
}