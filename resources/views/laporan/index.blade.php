@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
<div class="mb-3">
    <h5 class="mb-0">Laporan</h5>
    <small class="text-muted">Pilih laporan yang ingin ditampilkan atau dicetak.</small>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h5>Laporan Inventaris</h5>
                <p class="text-muted">Daftar seluruh barang beserta jumlah, kondisi, lokasi ruang, dan stok yang tersedia.</p>
                <a href="{{ route('laporan.inventaris') }}" class="btn btn-primary btn-sm">Buka Laporan</a>
            </div>
        </div>
    </div>
</div>
@endsection