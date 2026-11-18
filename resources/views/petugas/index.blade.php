@extends('layouts.app')

@section('title', 'Kelola Petugas')

@section('content')
@php $saya = (int) auth()->user()->id_petugas; @endphp

<div class="mb-3">
    <h5 class="mb-0">Kelola Petugas</h5>
    <nav class="small text-muted">Beranda / Kelola Petugas</nav>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body">

        <form method="GET" action="{{ route('petugas.index') }}" class="row g-2 align-items-center mb-3">
            <div class="col-12 col-md-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </span>
                    <input type="text" name="q" class="form-control" placeholder="Cari nama atau username petugas" value="{{ request('q') }}">
                    <button class="btn btn-outline-secondary" type="submit">Cari</button>
                </div>
            </div>
            <div class="col-7 col-md-3">
                <select name="id_level" class="form-select form-select-sm" autocomplete="off" onchange="this.form.submit()">
                    <option value="">Semua Level</option>
                    @foreach ($daftarLevel as $l)
                        <option value="{{ $l->id_level }}" @selected(request('id_level') == $l->id_level)>{{ $l->nama_level }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-5 col-md-4 text-end">
                <a href="{{ route('petugas.create') }}" class="btn btn-dark btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                    Tambah Petugas
                </a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="small text-muted">
                    <tr>
                        <th style="width:50px">No</th>
                        <th>Nama Petugas</th>
                        <th>Username</th>
                        <th>Level Hak Akses</th>
                        <th class="text-end" style="width:110px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $i => $row)
                        @php $diri = (int) $row->id_petugas === $saya; @endphp
                        <tr>
                            <td class="text-muted small">{{ $data->firstItem() + $i }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-light border d-flex align-items-center justify-content-center fw-semibold flex-shrink-0"
                                         style="width:34px;height:34px;">
                                        {{ strtoupper(mb_substr($row->nama_petugas, 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="fw-semibold">{{ $row->nama_petugas }}</span>
                                        @if ($diri)
                                            <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle ms-1">Anda</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark border font-monospace fw-normal">{{ $row->username }}</span></td>
                            <td>
                                @if ($row->isAdmin())
                                    <span class="badge bg-dark">{{ $row->level->nama_level ?? 'Administrator' }}</span>
                                @else
                                    <span class="badge bg-primary-subtle text-primary-emphasis border border-primary-subtle">{{ $row->level->nama_level ?? '-' }}</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('petugas.edit', $row->id_petugas) }}" class="btn btn-outline-secondary btn-sm" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                </a>
                                @unless ($diri)
                                    <form action="{{ route('petugas.destroy', $row->id_petugas) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus petugas ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-outline-danger btn-sm" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                        </button>
                                    </form>
                                @endunless
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Tidak ada data petugas.</td>
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
