@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $kartu = [];
    if (auth()->user()->isAdmin()) {
        $kartu[] = ['Total Unit Inventaris', $totalInventaris, 'primary'];
        $kartu[] = ['Jenis Barang', $totalJenis, 'info'];
        $kartu[] = ['Ruang', $totalRuang, 'secondary'];
        $kartu[] = ['Peminjam (Siswa)', $totalPegawai, 'dark'];
    }
    $kartu[] = ['Sedang Dipinjam', $peminjamanAktif, 'warning'];
    $kartu[] = ['Sudah Dikembalikan', $peminjamanSelesai, 'success'];
@endphp

<div class="mb-4">
    <h4 class="mb-0">Selamat datang, {{ auth()->user()->nama_petugas }}</h4>
    <small class="text-muted">{{ now()->translatedFormat('l, d F Y') }}</small>
</div>

<div class="row g-3 mb-4">
    @foreach ($kartu as [$judul, $nilai, $warna])
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted small">{{ $judul }}</div>
                    <div class="fs-2 fw-bold text-{{ $warna }}">{{ $nilai }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white fw-semibold">5 Peminjaman Terbaru</div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>Pegawai</th>
                    <th>Tanggal Pinjam</th>
                    <th>Tanggal Kembali</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($terbaru as $i => $p)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $p->pegawai->nama_pegawai ?? '-' }}</td>
                        <td>{{ $p->tanggal_pinjam->format('d/m/Y H:i') }}</td>
                        <td>{{ $p->tanggal_kembali ? $p->tanggal_kembali->format('d/m/Y H:i') : '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $p->status_peminjaman === 'Dipinjam' ? 'warning text-dark' : 'success' }}">
                                {{ $p->status_peminjaman }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">Belum ada data peminjaman.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
