@extends('layouts.app')

@section('title', 'Data Inventaris')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Data Inventaris</h5>
    <a href="{{ route('inventaris.create') }}" class="btn btn-primary btn-sm">+ Tambah Inventaris</a>
</div>

<form method="GET" action="{{ route('inventaris.index') }}" class="row g-2 mb-3">
    <div class="col-12 col-md-4">
        <input type="text" name="q" class="form-control form-control-sm"
               placeholder="Cari nama atau kode..." value="{{ request('q') }}">
    </div>
    <div class="col-6 col-md-3">
        <select name="id_jenis" class="form-select form-select-sm">
            <option value="">Semua jenis</option>
            @foreach ($daftarJenis as $j)
                <option value="{{ $j->id_jenis }}" @selected(request('id_jenis') == $j->id_jenis)>{{ $j->nama_jenis }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-6 col-md-3">
        <select name="id_ruang" class="form-select form-select-sm">
            <option value="">Semua ruang</option>
            @foreach ($daftarRuang as $r)
                <option value="{{ $r->id_ruang }}" @selected(request('id_ruang') == $r->id_ruang)>{{ $r->nama_ruang }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-12 col-md-2 d-flex gap-1">
        <button class="btn btn-outline-primary btn-sm">Cari</button>
        <a href="{{ route('inventaris.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:50px">No</th>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Jenis</th>
                    <th>Ruang</th>
                    <th>Kondisi</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-center">Tersedia</th>
                    <th>Tgl Register</th>
                    <th class="text-center" style="width:150px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $i => $row)
                    @php
                        $warna = match ($row->kondisi) {
                            'Baik'         => 'success',
                            'Rusak Ringan' => 'warning text-dark',
                            'Rusak Berat'  => 'danger',
                            default        => 'secondary',
                        };
                        $tersedia = $row->jumlah - (int) $row->dipinjam;
                    @endphp
                    <tr>
                        <td>{{ $data->firstItem() + $i }}</td>
                        <td>{{ $row->kode_inventaris }}</td>
                        <td>{{ $row->nama }}</td>
                        <td>{{ $row->jenis->nama_jenis ?? '-' }}</td>
                        <td>{{ $row->ruang->nama_ruang ?? '-' }}</td>
                        <td><span class="badge bg-{{ $warna }}">{{ $row->kondisi }}</span></td>
                        <td class="text-center">{{ $row->jumlah }}</td>
                        <td class="text-center">{{ $tersedia }}</td>
                        <td>{{ $row->tanggal_register->format('d/m/Y') }}</td>
                        <td class="text-center">
                            <a href="{{ route('inventaris.edit', $row->id_inventaris) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('inventaris.destroy', $row->id_inventaris) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Hapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted py-4">Belum ada data inventaris.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $data->links() }}
</div>
@endsection
