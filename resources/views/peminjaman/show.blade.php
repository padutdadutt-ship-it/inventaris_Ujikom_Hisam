@extends('layouts.app')

@section('title', 'Detail Peminjaman')

@section('content')
@php
    $bisaKembalikan = $item->status_peminjaman === 'Dipinjam'
        && (auth()->user()->isAdmin() || auth()->user()->isOperator());
@endphp

<div class="card border-0 shadow-sm" style="max-width: 860px;">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold">Detail Peminjaman #{{ $item->id_peminjaman }}</span>
        <span class="badge bg-{{ $item->status_peminjaman === 'Dipinjam' ? 'warning text-dark' : 'success' }}">
            {{ $item->status_peminjaman }}
        </span>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="text-muted small">Pegawai</div>
                <div class="fw-semibold">{{ $item->pegawai->nama_pegawai ?? '-' }}</div>
                <div class="small">NIP: {{ $item->pegawai->nip ?? '-' }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted small">Dicatat oleh</div>
                <div class="fw-semibold">{{ $item->petugas->nama_petugas ?? 'Peminjam (tanpa login)' }}</div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="text-muted small">Tanggal Pinjam</div>
                <div>{{ $item->tanggal_pinjam->format('d/m/Y H:i') }}</div>
            </div>
            <div class="col-md-6">
                <div class="text-muted small">Tanggal Kembali</div>
                <div>{{ $item->tanggal_kembali ? $item->tanggal_kembali->format('d/m/Y H:i') : '-' }}</div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width:50px">No</th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Jenis</th>
                        <th class="text-center">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($item->detail as $i => $d)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $d->inventaris->kode_inventaris ?? '-' }}</td>
                            <td>{{ $d->inventaris->nama ?? '-' }}</td>
                            <td>{{ $d->inventaris->jenis->nama_jenis ?? '-' }}</td>
                            <td class="text-center">{{ $d->jumlah }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-end">Total unit</th>
                        <th class="text-center">{{ $item->detail->sum('jumlah') }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary">Kembali</a>

            @if ($bisaKembalikan)
                <form action="{{ route('pengembalian.kembalikan', $item->id_peminjaman) }}" method="POST"
                      onsubmit="return confirm('Tandai peminjaman ini sudah dikembalikan?')">
                    @csrf
                    <button class="btn btn-success">Kembalikan Barang</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
