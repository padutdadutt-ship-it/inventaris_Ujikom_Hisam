@extends('layouts.app')

@section('title', 'Laporan Peminjaman Ruangan')

@section('content')
@php
    $admin = auth()->user()->isAdmin();
    $kelasTabel = $admin
        ? 'table table-hover align-middle mb-0'
        : 'table table-bordered table-hover mb-0 align-middle text-center';
@endphp

{{-- ===== Judul + tombol cetak ===== --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    @if ($admin)
        <h5 class="mb-0">Laporan Peminjaman Ruangan</h5>
    @else
        <h4 class="mb-0">Laporan Peminjaman Ruangan</h4>
    @endif
    <a href="#" class="btn btn-outline-secondary btn-sm" onclick="window.print(); return false;">Cetak</a>
</div>

{{-- ===== Filter ===== --}}
@if ($admin)
    {{-- Tampilan Admin: sama seperti Laporan Inventaris (tanpa card) --}}
    <form method="GET" action="{{ route('laporan.peminjaman-ruang') }}" class="row g-2 mb-3">
        <div class="col-12 col-md-3">
            <div class="input-group input-group-sm">
                <span class="input-group-text">Dari</span>
                <input type="date" name="dari_tanggal" class="form-control" value="{{ $dari_tanggal }}">
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="input-group input-group-sm">
                <span class="input-group-text">Sampai</span>
                <input type="date" name="sampai_tanggal" class="form-control" value="{{ $sampai_tanggal }}">
            </div>
        </div>
        <div class="col-12 col-md-3">
            <select name="status" class="form-select form-select-sm">
                <option value="">Semua status</option>
                <option value="Dipinjam" {{ $status == 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                <option value="Selesai" {{ $status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
            </select>
        </div>
        <div class="col-12 col-md-3 d-flex gap-1">
            <button type="submit" class="btn btn-outline-primary btn-sm">Tampilkan</button>
            <a href="{{ route('laporan.peminjaman-ruang') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
        </div>
    </form>
@else
    {{-- Tampilan Operator: seperti sebelumnya --}}
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('laporan.peminjaman-ruang') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="dari_tanggal" class="form-control" value="{{ $dari_tanggal }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="sampai_tanggal" class="form-control" value="{{ $sampai_tanggal }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua status</option>
                        <option value="Dipinjam" {{ $status == 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                        <option value="Selesai" {{ $status == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">Tampilkan</button>
                    <a href="{{ route('laporan.peminjaman-ruang') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                </div>
            </form>
        </div>
    </div>
@endif

{{-- ===== Tabel data ===== --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="{{ $kelasTabel }}">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Ruangan</th>
                        <th>Peminjam</th>
                        <th>Keperluan</th>
                        <th>Status</th>
                        <th>Dicatat Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $i => $item)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                            <td>{{ $item->jam_mulai }} - {{ $item->jam_selesai }}</td>
                            <td class="text-start">{{ $item->ruang->nama_ruang ?? '-' }}</td>
                            <td class="text-start">{{ $item->nama_peminjam }}</td>
                            <td class="text-start">{{ $item->keperluan }}</td>
                            <td>
                                @if ($item->status == 'Dipinjam')
                                    <span class="badge bg-warning text-dark">Dipinjam</span>
                                @else
                                    <span class="badge bg-success">Selesai</span>
                                @endif
                            </td>
                            <td>{{ $item->dicatat_oleh }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-3">Tidak ada data peminjaman ruangan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 