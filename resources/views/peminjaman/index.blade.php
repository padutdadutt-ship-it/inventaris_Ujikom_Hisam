@extends('layouts.app')

@section('title', 'Peminjaman')

@section('content')
@php $saya = auth()->user()->isPeminjam(); @endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">{{ $saya ? 'Peminjaman Saya' : 'Data Peminjaman' }}</h5>
    <a href="{{ route('peminjaman.create') }}" class="btn btn-primary btn-sm">+ Pinjam Barang</a>
</div>

<form method="GET" action="{{ route('peminjaman.index') }}" class="row g-2 mb-3">
    <div class="col-8 col-md-3">
        <select name="status" class="form-select form-select-sm">
            <option value="">Semua status</option>
            <option value="Dipinjam" @selected(request('status') == 'Dipinjam')>Dipinjam</option>
            <option value="Dikembalikan" @selected(request('status') == 'Dikembalikan')>Dikembalikan</option>
        </select>
    </div>
    <div class="col-4 col-md-3 d-flex gap-1">
        <button class="btn btn-outline-primary btn-sm">Filter</button>
        <a href="{{ route('peminjaman.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:50px">No</th>
                    <th>Tanggal Pinjam</th>
                    <th>Pegawai</th>
                    <th>Barang</th>
                    <th class="text-center">Total Unit</th>
                    <th>Dicatat Oleh</th>
                    <th>Status</th>
                    <th class="text-center" style="width:90px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $i => $row)
                    <tr>
                        <td>{{ $data->firstItem() + $i }}</td>
                        <td>{{ $row->tanggal_pinjam->format('d/m/Y H:i') }}</td>
                        <td>{{ $row->pegawai->nama_pegawai ?? '-' }}</td>
                        <td>
                            @foreach ($row->detail as $d)
                                <div>{{ $d->inventaris->nama ?? '-' }} <span class="text-muted">x{{ $d->jumlah }}</span></div>
                            @endforeach
                        </td>
                        <td class="text-center">{{ $row->total_unit }}</td>
                        <td>{{ $row->petugas->nama_petugas ?? 'Peminjam (tanpa login)' }}</td>
                        <td>
                            <span class="badge bg-{{ $row->status_peminjaman === 'Dipinjam' ? 'warning text-dark' : 'success' }}">
                                {{ $row->status_peminjaman }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('peminjaman.show', $row->id_peminjaman) }}" class="btn btn-info btn-sm text-white">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Belum ada data peminjaman.</td>
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
