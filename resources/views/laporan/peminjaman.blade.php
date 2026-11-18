@extends('layouts.app')

@section('title', 'Laporan Peminjaman')

@section('content')
@php
    $dari   = request('dari_tanggal');
    $sampai = request('sampai_tanggal');
    $status = request('status');

    $q = \Illuminate\Support\Facades\DB::table('peminjaman as p')
        ->leftJoin('pegawai as g', 'g.id_pegawai', '=', 'p.id_pegawai')
        ->leftJoin('petugas as t', 't.id_petugas', '=', 'p.id_petugas')
        ->select('p.id_peminjaman', 'p.tanggal_pinjam', 'p.tanggal_kembali', 'p.status_peminjaman',
                 'g.*', 't.nama_petugas')
        ->orderByDesc('p.tanggal_pinjam')->orderByDesc('p.id_peminjaman');

    if ($dari)   { $q->whereDate('p.tanggal_pinjam', '>=', $dari); }
    if ($sampai) { $q->whereDate('p.tanggal_pinjam', '<=', $sampai); }
    if ($status) { $q->where('p.status_peminjaman', $status); }

    $data = $q->get();

    // PERBAIKAN: Mengubah alias d.jumlah menjadi jumlah_pinjam
    $detail = \Illuminate\Support\Facades\DB::table('detail_pinjam as d')
        ->join('inventaris as i', 'i.id_inventaris', '=', 'd.id_inventaris')
        ->whereIn('d.id_peminjaman', $data->pluck('id_peminjaman'))
        ->select('d.id_peminjaman', 'd.jumlah as jumlah_pinjam', 'i.*')
        ->get()
        ->groupBy('id_peminjaman');

    $namaPeminjam = fn ($r) => $r->nama_pegawai ?? $r->nama ?? '-';
    $namaBarang   = fn ($r) => $r->nama_barang ?? $r->nama_inventaris ?? $r->nama ?? '-';

    // === HITUNG TOTAL LANGSUNG DI VIEW ===
    $totalUnit = 0;
    $totalUnitDipinjam = 0;

    foreach ($data as $p) {
        $items = $detail[$p->id_peminjaman] ?? collect();
        // PERBAIKAN: Menjumlahkan berdasarkan jumlah_pinjam
        $jumlahPerTransaksi = $items->sum('jumlah_pinjam');
        
        $totalUnit += $jumlahPerTransaksi;
        
        if ($p->status_peminjaman == 'Dipinjam') {
            $totalUnitDipinjam += $jumlahPerTransaksi;
        }
    }
    
    // Hitung total transaksi yang sedang dipinjam
    $totalDipinjam = $data->where('status_peminjaman', 'Dipinjam')->count();
@endphp

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Laporan Peminjaman</h5>
    <a href="#" class="btn btn-outline-secondary btn-sm" onclick="window.print(); return false;">Cetak</a>
</div>

<form method="GET" action="{{ route('laporan.peminjaman') }}" class="row g-2 mb-3">
    <div class="col-12 col-md-3">
        <div class="input-group input-group-sm">
            <span class="input-group-text">Dari</span>
            <input type="date" name="dari_tanggal" class="form-control" value="{{ $dari }}">
        </div>
    </div>
    <div class="col-12 col-md-3">
        <div class="input-group input-group-sm">
            <span class="input-group-text">Sampai</span>
            <input type="date" name="sampai_tanggal" class="form-control" value="{{ $sampai }}">
        </div>
    </div>
    <div class="col-12 col-md-3">
        <select name="status" class="form-select form-select-sm">
            <option value="">Semua status</option>
            <option value="Dipinjam" {{ $status == 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
            <option value="Dikembalikan" {{ $status == 'Dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
        </select>
    </div>
    <div class="col-12 col-md-3 d-flex gap-1">
        <button type="submit" class="btn btn-outline-primary btn-sm">Tampilkan</button>
        <a href="{{ route('laporan.peminjaman') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Tgl Pinjam</th>
                        <th>Peminjam</th>
                        <th>Barang</th>
                        <th class="text-center">Jumlah</th>
                        <th>Tgl Kembali</th>
                        <th>Status</th>
                        <th>Dicatat Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $i => $p)
                        @php $items = $detail[$p->id_peminjaman] ?? collect(); @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($p->tanggal_pinjam)->format('d/m/Y') }}</td>
                            <td>{{ $namaPeminjam($p) }}</td>
                            <td>
                                @forelse ($items as $it)
                                    <div>{{ $namaBarang($it) }}</div>
                                @empty
                                    -
                                @endforelse
                            </td>
                            <td class="text-center">
                                @foreach ($items as $it)
                                    <!-- PERBAIKAN: Memanggil jumlah_pinjam -->
                                    <div>{{ $it->jumlah_pinjam }}</div>
                                @endforeach
                            </td>
                            <td>{{ $p->tanggal_kembali ? \Carbon\Carbon::parse($p->tanggal_kembali)->format('d/m/Y') : '-' }}</td>
                            <td>
                                @if ($p->status_peminjaman == 'Dipinjam')
                                    <span class="badge bg-warning text-dark">Dipinjam</span>
                                @else
                                    <span class="badge bg-success">{{ $p->status_peminjaman }}</span>
                                @endif
                            </td>
                            <td>{{ $p->nama_petugas ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-3">Tidak ada data peminjaman.</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="fw-bold">
                        <th colspan="4" class="text-end">Total Unit Barang</th>
                        <th>{{ $totalUnit }} Unit</th>
                        <th>Sedang dipinjam: {{ $totalUnitDipinjam }} Unit ({{ $totalDipinjam }} Transaksi)</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection