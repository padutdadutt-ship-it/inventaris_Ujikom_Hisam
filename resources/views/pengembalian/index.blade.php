@extends('layouts.app')

@section('title', 'Pengembalian')

@section('content')
<div class="mb-3">
    <h5 class="mb-0">Pengembalian Barang</h5>
    <small class="text-muted">Catat barang yang sudah dikembalikan oleh pegawai.</small>
</div>

<form method="GET" action="{{ route('pengembalian.index') }}" class="row g-2 mb-3">
    <div class="col-12 col-md-4">
        <input type="text" name="q" class="form-control form-control-sm"
               placeholder="Cari nama atau NIP pegawai..." value="{{ request('q') }}">
    </div>
    <div class="col-6 col-md-3">
        <select name="status" autocomplete="off" class="form-select form-select-sm">
            <option value="Dipinjam" @selected($status == 'Dipinjam')>Belum dikembalikan</option>
            <option value="Dikembalikan" @selected($status == 'Dikembalikan')>Sudah dikembalikan</option>
            <option value="semua" @selected($status == 'semua')>Semua</option>
        </select>
    </div>
    <div class="col-6 col-md-3 d-flex gap-1">
        <button class="btn btn-outline-primary btn-sm">Cari</button>
        <a href="{{ route('pengembalian.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
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
                    <th>Status</th>
                    <th>Tanggal Kembali</th>
                    <th class="text-center" style="width:170px">Aksi</th>
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
                        <td>
                            <span class="badge bg-{{ $row->status_peminjaman === 'Dipinjam' ? 'warning text-dark' : 'success' }}">
                                {{ $row->status_peminjaman }}
                            </span>
                        </td>
                        <td>{{ $row->tanggal_kembali ? $row->tanggal_kembali->format('d/m/Y H:i') : '-' }}</td>
                        <td class="text-center">
                            <a href="{{ route('peminjaman.show', $row->id_peminjaman) }}" class="btn btn-info btn-sm text-white">Detail</a>
                            @if ($row->status_peminjaman === 'Dipinjam')
                                <form action="{{ route('pengembalian.kembalikan', $row->id_peminjaman) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Tandai peminjaman ini sudah dikembalikan?')">
                                    @csrf
                                    <button class="btn btn-success btn-sm">Kembalikan</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            {{ $status === 'Dipinjam' ? 'Tidak ada barang yang sedang dipinjam.' : 'Tidak ada data.' }}
                        </td>
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
