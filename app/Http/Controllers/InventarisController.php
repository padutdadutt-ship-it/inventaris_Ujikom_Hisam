<?php

namespace App\Http\Controllers;

use App\Models\DetailPinjam;
use App\Models\Inventaris;
use App\Models\Jenis;
use App\Models\Ruang;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InventarisController extends Controller
{
    protected $kondisi = ['Baik', 'Rusak Ringan', 'Rusak Berat'];

    protected $pesan = [
        'required' => ':attribute wajib diisi.',
        'max'      => ':attribute maksimal :max.',
        'min'      => ':attribute minimal :min.',
        'integer'  => ':attribute harus berupa angka bulat.',
        'date'     => ':attribute harus berupa tanggal yang valid.',
        'exists'   => ':attribute yang dipilih tidak valid.',
        'in'       => ':attribute yang dipilih tidak valid.',
    ];

    protected $atribut = [
        'id_jenis'         => 'jenis',
        'id_ruang'         => 'ruang',
        'tanggal_register' => 'tanggal register',
    ];

    protected function aturan()
    {
        return [
            'nama'             => 'required|max:150',
            'kondisi'          => ['required', Rule::in($this->kondisi)],
            'jumlah'           => 'required|integer|min:0|max:100000',
            'id_jenis'         => 'required|exists:jenis,id_jenis',
            'id_ruang'         => 'required|exists:ruang,id_ruang',
            'tanggal_register' => 'required|date',
            'keterangan'       => 'nullable|max:500',
        ];
    }

    protected function kodeBaru()
    {
        $n = (int) Inventaris::max('id_inventaris') + 1;

        do {
            $kode = 'INV-' . str_pad($n, 4, '0', STR_PAD_LEFT);
            $n++;
        } while (Inventaris::where('kode_inventaris', $kode)->exists());

        return $kode;
    }

    protected function dataForm(Inventaris $item)
    {
        return [
            'item'          => $item,
            'daftarJenis'   => Jenis::orderBy('nama_jenis')->get(),
            'daftarRuang'   => Ruang::orderBy('nama_ruang')->get(),
            'daftarKondisi' => $this->kondisi,
            'kodeBaru'      => $item->exists ? $item->kode_inventaris : $this->kodeBaru(),
        ];
    }

    public function index(Request $request)
    {
        $query = Inventaris::with(['jenis', 'ruang'])
            ->withSum(['detailPinjam as dipinjam' => function ($d) {
                $d->whereHas('peminjaman', function ($p) {
                    $p->where('status_peminjaman', 'Dipinjam');
                });
            }], 'jumlah');

        if ($request->filled('q')) {
            $kata = $request->q;
            $query->where(function ($w) use ($kata) {
                $w->where('nama', 'like', "%{$kata}%")
                  ->orWhere('kode_inventaris', 'like', "%{$kata}%");
            });
        }

        if ($request->filled('id_jenis')) {
            $query->where('id_jenis', $request->id_jenis);
        }

        if ($request->filled('id_ruang')) {
            $query->where('id_ruang', $request->id_ruang);
        }

        return view('inventaris.index', [
            'data'        => $query->orderBy('kode_inventaris')->paginate(10)->withQueryString(),
            'daftarJenis' => Jenis::orderBy('nama_jenis')->get(),
            'daftarRuang' => Ruang::orderBy('nama_ruang')->get(),
        ]);
    }

    public function create()
    {
        return view('inventaris.form', $this->dataForm(new Inventaris()));
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->aturan(), $this->pesan, $this->atribut);

        $data['kode_inventaris'] = $this->kodeBaru();
        $data['id_petugas']      = auth()->user()->id_petugas;

        Inventaris::create($data);

        return redirect()->route('inventaris.index')->with('success', 'Data inventaris berhasil ditambahkan.');
    }

    public function edit($id)
    {
        return view('inventaris.form', $this->dataForm(Inventaris::findOrFail($id)));
    }

    public function update(Request $request, $id)
    {
        $item = Inventaris::findOrFail($id);

        $data = $request->validate($this->aturan(), $this->pesan, $this->atribut);

        $dipinjam = (int) DetailPinjam::where('id_inventaris', $item->id_inventaris)
            ->whereHas('peminjaman', function ($p) {
                $p->where('status_peminjaman', 'Dipinjam');
            })
            ->sum('jumlah');

        if ($data['jumlah'] < $dipinjam) {
            return back()->withInput()->withErrors([
                'jumlah' => "Jumlah tidak boleh kurang dari {$dipinjam} unit yang sedang dipinjam.",
            ]);
        }

        $item->update($data);

        return redirect()->route('inventaris.index')->with('success', 'Data inventaris berhasil diubah.');
    }

    public function destroy($id)
    {
        $item = Inventaris::findOrFail($id);

        if ($item->detailPinjam()->exists()) {
            return redirect()->route('inventaris.index')
                ->with('error', 'Inventaris tidak dapat dihapus karena sudah memiliki riwayat peminjaman.');
        }

        $item->delete();

        return redirect()->route('inventaris.index')->with('success', 'Data inventaris berhasil dihapus.');
    }
}
