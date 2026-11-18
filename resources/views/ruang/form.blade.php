@extends('layouts.app')

@section('title', $item->exists ? 'Edit Ruang' : 'Tambah Ruang')

@section('content')
@php $edit = $item->exists; @endphp

<div class="card border-0 shadow-sm" style="max-width: 640px;">
    <div class="card-header bg-white fw-semibold">{{ $edit ? 'Edit Ruang' : 'Tambah Ruang' }}</div>
    <div class="card-body">
        <form method="POST" action="{{ $edit ? route('ruang.update', $item->id_ruang) : route('ruang.store') }}">
            @csrf
            @if ($edit)
                @method('PUT')
            @endif

            <div class="mb-3">
                <label for="kode_ruang" class="form-label">Kode Ruang</label>
                <input type="text" id="kode_ruang" name="kode_ruang" maxlength="20"
                       class="form-control @error('kode_ruang') is-invalid @enderror"
                       value="{{ old('kode_ruang', $item->kode_ruang) }}">
            </div>

            <div class="mb-3">
                <label for="nama_ruang" class="form-label">Nama Ruang</label>
                <input type="text" id="nama_ruang" name="nama_ruang" maxlength="100"
                       class="form-control @error('nama_ruang') is-invalid @enderror"
                       value="{{ old('nama_ruang', $item->nama_ruang) }}">
            </div>

            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea id="keterangan" name="keterangan" rows="3"
                          class="form-control @error('keterangan') is-invalid @enderror">{{ old('keterangan', $item->keterangan) }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('ruang.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection
