@extends('layouts.app')

@section('title', 'Data Ruang')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0">Data Ruang</h5>
    <a href="{{ route('ruang.create') }}" class="btn btn-primary btn-sm">+ Tambah Ruang</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:60px">No</th>
                    <th>Kode</th>
                    <th>Nama Ruang</th>
                    <th>Keterangan</th>
                    <th class="text-center">Jumlah Barang</th>
                    <th class="text-center" style="width:160px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $i => $row)
                    <tr>
                        <td>{{ $data->firstItem() + $i }}</td>
                        <td>{{ $row->kode_ruang }}</td>
                        <td>{{ $row->nama_ruang }}</td>
                        <td>{{ $row->keterangan ?: '-' }}</td>
                        <td class="text-center">{{ $row->inventaris_count }}</td>
                        <td class="text-center">
                            <a href="{{ route('ruang.edit', $row->id_ruang) }}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="{{ route('ruang.destroy', $row->id_ruang) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Hapus data ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">Belum ada data ruang.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $data->links() }}
</div>
@endsection
