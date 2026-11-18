@extends('layouts.app')

@section('title', 'Ekspor / Impor Data')
@section('crumb', 'Ekspor / Impor')

@section('content')

@if (session('galat') && count(session('galat')))
    <div class="alert alert-warning">
        <div class="fw-semibold mb-1">Baris yang tidak dapat diimpor (maksimal 20 ditampilkan):</div>
        <ul class="mb-0 small">
            @foreach (session('galat') as $g)
                <li>{{ $g }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-3">

    {{-- ============ EKSPOR ============ --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <h5 class="mb-1">Ekspor Data</h5>
                <p class="text-muted small">Unduh data dalam format CSV. File terbuka rapi di Excel.</p>

                <div class="list-group list-group-flush">
                    <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold">Inventaris</div>
                            <small class="text-muted">{{ $jmlInventaris }} data barang</small>
                        </div>
                        <a href="{{ route('ekspor.inventaris') }}" class="btn btn-outline-dark btn-sm">Unduh CSV</a>
                    </div>
                    <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold">Peminjam (Siswa)</div>
                            <small class="text-muted">{{ $jmlPeminjam }} data peminjam</small>
                        </div>
                        <a href="{{ route('ekspor.peminjam') }}" class="btn btn-outline-dark btn-sm">Unduh CSV</a>
                    </div>
                    <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-semibold">Riwayat Peminjaman</div>
                            <small class="text-muted">{{ $jmlPeminjaman }} transaksi</small>
                        </div>
                        <a href="{{ route('ekspor.peminjaman') }}" class="btn btn-outline-dark btn-sm">Unduh CSV</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============ IMPOR ============ --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h5 class="mb-1">Impor Data</h5>
                <p class="text-muted small">Unggah file CSV (pemisah koma atau titik koma). Data yang sudah ada tidak digandakan.</p>

                <div class="border rounded-3 p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold">Impor Inventaris</div>
                            <small class="text-muted d-block">Kolom: <code>nama</code>, <code>kategori</code>, <code>ruang</code>, <code>jumlah</code>, <code>kondisi</code></small>
                            <small class="text-muted">Kategori dan ruang harus sudah ada di Kategori Barang dan Data Ruang.</small>
                        </div>
                        <a href="{{ route('ekspor.template', 'inventaris') }}" class="btn btn-link btn-sm text-nowrap">Unduh template</a>
                    </div>
                    <form method="POST" action="{{ route('ekspor.impor.inventaris') }}" enctype="multipart/form-data" class="d-flex gap-2 mt-2">
                        @csrf
                        <input type="file" name="berkas" accept=".csv,.txt" class="form-control form-control-sm">
                        <button class="btn btn-dark btn-sm text-nowrap">Impor</button>
                    </form>
                </div>

                <div class="border rounded-3 p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="fw-semibold">Impor Peminjam (Siswa)</div>
                            <small class="text-muted d-block">Kolom: <code>nis</code>, <code>nama</code>, <code>kelas</code></small>
                            <small class="text-muted">NIS berupa angka 8 sampai 20 digit.</small>
                        </div>
                        <a href="{{ route('ekspor.template', 'peminjam') }}" class="btn btn-link btn-sm text-nowrap">Unduh template</a>
                    </div>
                    <form method="POST" action="{{ route('ekspor.impor.peminjam') }}" enctype="multipart/form-data" class="d-flex gap-2 mt-2">
                        @csrf
                        <input type="file" name="berkas" accept=".csv,.txt" class="form-control form-control-sm">
                        <button class="btn btn-dark btn-sm text-nowrap">Impor</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
