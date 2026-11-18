@extends('layouts.app')

@section('title', 'Kategori Barang Inventaris')
@section('crumb', 'Kategori Barang')

@push('styles')
<style>
    .bar-atas { background: #f6f7f9; border-radius: 1rem; padding: 1rem; }
    .tabel-foto thead th {
        background: #f6f7f9; font-size: .78rem; letter-spacing: .04em; text-transform: uppercase;
        color: #374151; border: 0; padding: .9rem 1rem;
    }
    .tabel-foto thead th:first-child { border-radius: .75rem 0 0 .75rem; }
    .tabel-foto thead th:last-child  { border-radius: 0 .75rem .75rem 0; }
    .tabel-foto tbody td { padding: 1rem; border-color: #f0f1f3; }
    .kode-badge { background: #eef0f3; color: #111; font-weight: 700; font-size: .78rem; padding: .3rem .6rem; border-radius: .5rem; }
    .pill-total { background: #e8f0fe; color: #1d4ed8; border-radius: 999px; padding: .3rem .8rem; font-size: .82rem; font-weight: 500; white-space: nowrap; }
    .btn-ikon { border-radius: .6rem; padding: .35rem .55rem; line-height: 1; }
</style>
@endpush

@section('content')
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-3 p-md-4">

        <div class="bar-atas d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
            <form method="GET" action="{{ route('jenis.index') }}" class="flex-grow-1" style="max-width: 360px;">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </span>
                    <input type="text" name="q" class="form-control border-start-0" placeholder="Cari nama, kode, atau keterangan..." value="{{ request('q') }}">
                </div>
            </form>
            <a href="{{ route('jenis.create') }}" class="btn btn-dark px-3">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Tambah Kategori
            </a>
        </div>

        <div class="table-responsive">
            <table class="table tabel-foto align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:60px">No</th>
                        <th>Kode</th>
                        <th>Nama Kategori</th>
                        <th>Keterangan</th>
                        <th class="text-center">Total Barang</th>
                        <th class="text-end" style="width:120px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $i => $row)
                        <tr>
                            <td class="text-muted">{{ $data->firstItem() + $i }}</td>
                            <td><span class="kode-badge">{{ $row->kode_jenis }}</span></td>
                            <td class="fw-semibold">{{ $row->nama_jenis }}</td>
                            <td class="text-muted">{{ $row->keterangan ?: '-' }}</td>
                            <td class="text-center"><span class="pill-total">{{ $row->inventaris_count }} unit item</span></td>
                            <td class="text-end">
                                <a href="{{ route('jenis.edit', $row->id_jenis) }}" class="btn btn-outline-dark btn-ikon" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </a>
                                <form action="{{ route('jenis.destroy', $row->id_jenis) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Hapus kategori ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-outline-danger btn-ikon" title="Hapus">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada kategori barang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-3">
    {{ $data->links() }}
</div>
@endsection
