<?php

namespace App\Http\Controllers;

use App\Models\Jenis;
use Illuminate\Http\Request;

class JenisController extends Controller
{
    protected $pesan = [
        'required' => ':attribute wajib diisi.',
        'max'      => ':attribute maksimal :max karakter.',
        'unique'   => ':attribute sudah dipakai.',
    ];

    protected $atribut = [
        'nama_jenis' => 'nama kategori',
        'kode_jenis' => 'kode kategori',
    ];

    public function index(Request $request)
    {
        $query = Jenis::withCount('inventaris');

        if ($request->filled('q')) {
            $kata = $request->q;
            $query->where(function ($w) use ($kata) {
                $w->where('nama_jenis', 'like', "%{$kata}%")
                  ->orWhere('kode_jenis', 'like', "%{$kata}%")
                  ->orWhere('keterangan', 'like', "%{$kata}%");
            });
        }

        $data = $query->orderBy('id_jenis')->paginate(10)->withQueryString();

        return view('jenis.index', compact('data'));
    }

    public function create()
    {
        return view('jenis.form', ['item' => new Jenis()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_jenis' => 'required|max:100',
            'kode_jenis' => 'required|max:20|unique:jenis,kode_jenis',
            'keterangan' => 'nullable|max:500',
        ], $this->pesan, $this->atribut);

        Jenis::create($data);

        return redirect()->route('jenis.index')->with('success', 'Kategori barang berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $item = Jenis::findOrFail($id);

        return view('jenis.form', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = Jenis::findOrFail($id);

        $data = $request->validate([
            'nama_jenis' => 'required|max:100',
            'kode_jenis' => 'required|max:20|unique:jenis,kode_jenis,' . $item->id_jenis . ',id_jenis',
            'keterangan' => 'nullable|max:500',
        ], $this->pesan, $this->atribut);

        $item->update($data);

        return redirect()->route('jenis.index')->with('success', 'Kategori barang berhasil diubah.');
    }

    public function destroy($id)
    {
        $item = Jenis::withCount('inventaris')->findOrFail($id);

        if ($item->inventaris_count > 0) {
            return redirect()->route('jenis.index')
                ->with('error', 'Kategori tidak dapat dihapus karena masih dipakai oleh ' . $item->inventaris_count . ' barang.');
        }

        $item->delete();

        return redirect()->route('jenis.index')->with('success', 'Kategori barang berhasil dihapus.');
    }
}
