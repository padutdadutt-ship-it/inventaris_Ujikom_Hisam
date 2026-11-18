<?php

namespace App\Http\Controllers;

use App\Models\Ruang;
use Illuminate\Http\Request;

class RuangController extends Controller
{
    protected $pesan = [
        'required' => ':attribute wajib diisi.',
        'max'      => ':attribute maksimal :max karakter.',
        'unique'   => ':attribute sudah dipakai.',
    ];

    public function index()
    {
        $data = Ruang::withCount('inventaris')->orderBy('nama_ruang')->paginate(10);

        return view('ruang.index', compact('data'));
    }

    public function create()
    {
        return view('ruang.form', ['item' => new Ruang()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_ruang' => 'required|max:100',
            'kode_ruang' => 'required|max:20|unique:ruang,kode_ruang',
            'keterangan' => 'nullable|max:500',
        ], $this->pesan);

        Ruang::create($data);

        return redirect()->route('ruang.index')->with('success', 'Data ruang berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $item = Ruang::findOrFail($id);

        return view('ruang.form', compact('item'));
    }

    public function update(Request $request, $id)
    {
        $item = Ruang::findOrFail($id);

        $data = $request->validate([
            'nama_ruang' => 'required|max:100',
            'kode_ruang' => 'required|max:20|unique:ruang,kode_ruang,' . $item->id_ruang . ',id_ruang',
            'keterangan' => 'nullable|max:500',
        ], $this->pesan);

        $item->update($data);

        return redirect()->route('ruang.index')->with('success', 'Data ruang berhasil diubah.');
    }

    public function destroy($id)
    {
        $item = Ruang::withCount('inventaris')->findOrFail($id);

        if ($item->inventaris_count > 0) {
            return redirect()->route('ruang.index')
                ->with('error', 'Ruang tidak dapat dihapus karena masih dipakai oleh data inventaris.');
        }

        $item->delete();

        return redirect()->route('ruang.index')->with('success', 'Data ruang berhasil dihapus.');
    }
}
