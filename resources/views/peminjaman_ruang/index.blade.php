@extends('layouts.app')

@section('title', 'Peminjaman Ruangan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h4 class="mb-0">Data Peminjaman Ruangan</h4>
    <a href="{{ route('peminjaman-ruang.create') }}" class="btn btn-primary btn-sm">+ Pinjam Ruangan</a>
</div>

<form method="GET" action="{{ route('peminjaman-ruang.index') }}" class="d-flex gap-2 mb-3" style="max-width:420px">
    <select name="status" class="form-select form-select-sm">
        <option value="">Semua status</option>
        <option value="Dipinjam" {{ $status === 'Dipinjam' ? 'selected' : '' }}>Dipinjam</option>
        <option value="Selesai" {{ $status === 'Selesai' ? 'selected' : '' }}>Selesai</option>
    </select>
    <button class="btn btn-outline-primary btn-sm">Filter</button>
    <a href="{{ route('peminjaman-ruang.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
</form>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th style="width:50px">No</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Ruangan</th>
                    <th>Peminjam</th>
                    <th>Keperluan</th>
                    <th>Dicatat Oleh</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $item)
                    <tr>
                        <td>{{ $data->firstItem() + $loop->index }}</td>
                        <td>{{ $item->tanggal->format('d/m/Y') }}</td>
                        <td>{{ substr($item->jam_mulai, 0, 5) }} - {{ substr($item->jam_selesai, 0, 5) }}</td>
                        <td class="fw-semibold">{{ $item->ruang->nama_ruang ?? '-' }}</td>
                        <td>{{ $item->nama_peminjam }}</td>
                        <td>{{ $item->keperluan }}</td>
                        <td>{{ $item->dicatat_oleh ?: '-' }}</td>
                        <td>
                            @if ($item->status === 'Dipinjam')
                                <span class="badge bg-warning text-dark">Dipinjam</span>
                            @else
                                <span class="badge bg-success">Selesai</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if ($item->status === 'Dipinjam')
                                <form method="POST" action="{{ route('peminjaman-ruang.selesai', $item->id) }}" class="d-inline"
                                      onsubmit="return confirm('Tandai peminjaman ini selesai?')">
                                    @csrf
                                    <button class="btn btn-success btn-sm">Selesai</button>
                                </form>
                            @else
                                <span class="text-secondary">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-secondary py-4">Belum ada data peminjaman ruangan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $data->links() }}</div>
@endsection