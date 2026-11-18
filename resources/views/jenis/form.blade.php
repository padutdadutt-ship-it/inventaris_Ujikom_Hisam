@extends('layouts.app')

@section('title', $item->exists ? 'Edit Kategori' : 'Tambah Kategori')

@section('content')
@php $edit = $item->exists; @endphp

<div class="card border-0 shadow-sm" style="max-width: 640px;">
    <div class="card-header bg-white fw-semibold">{{ $edit ? 'Edit Kategori' : 'Tambah Kategori' }}</div>
    <div class="card-body">
        <form method="POST" action="{{ $edit ? route('jenis.update', $item->id_jenis) : route('jenis.store') }}">
            @csrf
            @if ($edit)
                @method('PUT')
            @endif

            <div class="mb-3">
                <label for="kode_jenis" class="form-label">Kode Kategori</label>
                <input type="text" id="kode_jenis" name="kode_jenis" maxlength="20"
                       class="form-control @error('kode_jenis') is-invalid @enderror"
                       value="{{ old('kode_jenis', $item->kode_jenis) }}">
            </div>

            <div class="mb-3">
                <label for="nama_jenis" class="form-label">Nama Kategori</label>
                <input type="text" id="nama_jenis" name="nama_jenis" maxlength="100"
                       class="form-control @error('nama_jenis') is-invalid @enderror"
                       value="{{ old('nama_jenis', $item->nama_jenis) }}">
            </div>

            <div class="mb-3">
                <label for="keterangan" class="form-label">Keterangan</label>
                <textarea id="keterangan" name="keterangan" rows="3"
                          class="form-control @error('keterangan') is-invalid @enderror">{{ old('keterangan', $item->keterangan) }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('jenis.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection
