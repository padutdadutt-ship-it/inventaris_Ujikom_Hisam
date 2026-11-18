@extends('layouts.app')

@section('title', 'Laporan Ruangan')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-3">
    <div>
        <h5 class="mb-0">Laporan Data Ruangan</h5>
        <small class="text-muted">Data keseluruhan ruangan beserta jumlah yang sedang dipinjam.</small>
    </div>
    <a href="#" class="btn btn-outline-secondary btn-sm" onclick="window.print(); return false;">Cetak Laporan</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Nama Ruangan</th>
                        <th>Kondisi</th>
                        <th>Kebersihan</th>
                        <th class="text-center">Sedang Dipinjam</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ruang as $r)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $r->kode_ruang }}</td>
                            <td>{{ $r->nama_ruang }}</td>
                            <td>
                                @if ($r->kondisi)
                                    <span class="badge {{ $r->kondisi == 'Baik' ? 'bg-success' : 'bg-warning text-dark' }}">{{ $r->kondisi }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if ($r->kebersihan)
                                    <span class="badge {{ $r->kebersihan == 'Bersih' ? 'bg-success' : 'bg-warning text-dark' }}">{{ $r->kebersihan }}</span>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($r->total_dipinjam > 0)
                                    <span class="badge bg-warning text-dark">{{ $r->total_dipinjam }}</span>
                                @else
                                    <span class="badge bg-light text-dark border">Tidak Dipinjam</span>
                                @endif
                            </td>
                            <td>@if ($r->info_pinjam)Dipinjam oleh {{ $r->info_pinjam }}@else{{ $r->keterangan ?: '-' }}@endif</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-3">Tidak ada data ruangan.</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="fw-bold">
                        <td colspan="5" class="text-end">Total Sedang Dipinjam</td>
                        <td class="text-center">{{ $ruang->sum('total_dipinjam') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection