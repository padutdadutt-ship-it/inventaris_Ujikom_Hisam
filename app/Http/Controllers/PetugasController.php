<?php

namespace App\Http\Controllers;

use App\Models\Level;
use App\Models\Petugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PetugasController extends Controller
{
    protected $pesan = [
        'required'   => ':attribute wajib diisi.',
        'max'        => ':attribute maksimal :max karakter.',
        'min'        => ':attribute minimal :min karakter.',
        'unique'     => ':attribute sudah dipakai.',
        'exists'     => ':attribute yang dipilih tidak valid.',
        'confirmed'  => 'Konfirmasi password tidak cocok.',
        'alpha_dash' => ':attribute hanya boleh berisi huruf, angka, strip (-), dan garis bawah (_).',
    ];

    protected $atribut = [
        'nama_petugas' => 'nama petugas',
        'id_level'     => 'level',
    ];

    protected function diriSendiri(Petugas $item): bool
    {
        return (int) $item->id_petugas === (int) auth()->user()->id_petugas;
    }

    public function index(Request $request)
    {
        $query = Petugas::with('level');

        if ($request->filled('q')) {
            $kata = $request->q;
            $query->where(function ($w) use ($kata) {
                $w->where('nama_petugas', 'like', "%{$kata}%")
                  ->orWhere('username', 'like', "%{$kata}%");
            });
        }

        if ($request->filled('id_level')) {
            $query->where('id_level', $request->id_level);
        }

        return view('petugas.index', [
            'data'        => $query->orderBy('id_level')->orderBy('nama_petugas')->paginate(10)->withQueryString(),
            'daftarLevel' => Level::orderBy('id_level')->get(),
        ]);
    }

    public function create()
    {
        return view('petugas.form', [
            'item'        => new Petugas(),
            'daftarLevel' => Level::orderBy('id_level')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_petugas' => 'required|max:100',
            'username'     => ['required', 'alpha_dash', 'min:3', 'max:50', 'unique:petugas,username'],
            'password'     => ['required', 'min:8', 'confirmed'],
            'id_level'     => ['required', 'exists:level,id_level'],
        ], $this->pesan, $this->atribut);

        $data['password'] = Hash::make($data['password']);

        Petugas::create($data);

        return redirect()->route('petugas.index')->with('success', 'Petugas berhasil ditambahkan.');
    }

    public function edit($id)
    {
        return view('petugas.form', [
            'item'        => Petugas::findOrFail($id),
            'daftarLevel' => Level::orderBy('id_level')->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $item = Petugas::findOrFail($id);
        $diri = $this->diriSendiri($item);

        $aturan = [
            'nama_petugas' => 'required|max:100',
            'username'     => ['required', 'alpha_dash', 'min:3', 'max:50',
                               Rule::unique('petugas', 'username')->ignore($item->id_petugas, 'id_petugas')],
            'password'     => ['nullable', 'min:8', 'confirmed'],
        ];

        // Level akun sendiri tidak boleh diubah (mencegah admin mengunci dirinya)
        if (! $diri) {
            $aturan['id_level'] = ['required', 'exists:level,id_level'];
        }

        $data = $request->validate($aturan, $this->pesan, $this->atribut);

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $item->update($data);

        return redirect()->route('petugas.index')->with('success', 'Data petugas berhasil diubah.');
    }

    public function destroy($id)
    {
        $item = Petugas::withCount('inventaris')->findOrFail($id);

        if ($this->diriSendiri($item)) {
            return redirect()->route('petugas.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($item->inventaris_count > 0) {
            return redirect()->route('petugas.index')
                ->with('error', 'Petugas tidak dapat dihapus karena tercatat sebagai pencatat ' . $item->inventaris_count . ' data inventaris.');
        }

        $item->delete();

        return redirect()->route('petugas.index')->with('success', 'Petugas berhasil dihapus.');
    }
}
