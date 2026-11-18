@extends('layouts.app')

@section('title', $judul)

@section('content')
<nav class="small text-muted mb-1">Beranda / Peminjam / {{ $golongan ? $tingkat : ($tingkat ? 'Kelas ' . $tingkat : 'Semua Siswa') }}</nav>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h5 class="mb-0">{{ $judul }}</h5>
        <small class="text-muted">{{ $data->total() }} {{ $golongan ? strtolower($sebutan) : 'siswa' }} ditemukan</small>
    </div>
    <a href="{{ route('pegawai.create', $golongan ? ['tingkat' => $tingkat] : request()->only('kelas')) }}"
       class="btn btn-primary btn-sm">+ Tambah {{ $sebutan }}</a>
</div>

<form method="GET" action="{{ route('pegawai.index') }}" class="row g-2 mb-3">
    @if ($tingkat)
        <input type="hidden" name="tingkat" value="{{ $tingkat }}">
    @endif
    <div class="col-12 {{ $golongan ? 'col-md-9' : 'col-md-5' }}">
        <input type="text" name="q" class="form-control form-control-sm"
               placeholder="Cari nama atau {{ $golongan ? 'NIP' : 'NIS' }}..." value="{{ request('q') }}">
    </div>
    @unless ($golongan)
        <div class="col-12 col-md-4">
            <select name="kelas" class="form-select form-select-sm" autocomplete="off">
                <option value="">Semua kelas</option>
                @foreach ($daftarKelas as $k)
                    <option value="{{ $k }}" @selected(request('kelas') == $k)>{{ $k }}</option>
                @endforeach
            </select>
        </div>
    @endunless
    <div class="col-12 col-md-3 d-flex gap-1">
        <button class="btn btn-outline-primary btn-sm">Cari</button>
        <a href="{{ route('pegawai.index', $tingkat ? ['tingkat' => $tingkat] : []) }}" class="btn btn-outline-secondary btn-sm">Reset</a>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:50px">No</th>
                    <th>{{ $golongan ? 'NIP' : 'NIS' }}</th>
                    <th>Nama {{ $sebutan }}</th>
                    <th>{{ $golongan ? 'Golongan' : 'Kelas' }}</th>
                    <th>Sedang Dipinjam</th>
                    <th class="text-center" style="width:150px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $i => $row)
                    @php
                        $dipinjam = $row->peminjaman->flatMap(function ($p) {
                            return $p->detail;
                        });
                    @endphp
                    <tr>
                        <td>{{ $data->firstItem() + $i }}</td>
                        <td>{{ $row->nip }}</td>
                        <td class="fw-semibold">{{ $row->nama_pegawai }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $row->alamat ?: '-' }}</span></td>
                        <td>
                            @forelse ($dipinjam as $d)
                                <div class="small">{{ $d->inventaris->nama ?? '-' }} <span class="text-muted">x{{ $d->jumlah }}</span></div>
                            @empty
                                <span class="text-muted small">Tidak ada</span>
                            @endforelse
                        </td>
                        <td class="text-center">
                            <a href="{{ route('pegawai.edit', $row->id_pegawai) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('pegawai.destroy', $row->id_pegawai) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Hapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Tidak ada data {{ strtolower($sebutan) }}.</td>
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