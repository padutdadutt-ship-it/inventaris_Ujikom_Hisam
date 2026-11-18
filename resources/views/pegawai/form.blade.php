@extends('layouts.app')

@section('title', $item->exists ? 'Edit Siswa' : 'Tambah Siswa')

@section('content')
@php $edit = $item->exists; @endphp

<div class="card border-0 shadow-sm" style="max-width: 640px;">
    <div class="card-header bg-white fw-semibold">{{ $edit ? 'Edit Siswa' : 'Tambah Siswa' }}</div>
    <div class="card-body">
        <form method="POST" action="{{ $edit ? route('pegawai.update', $item->id_pegawai) : route('pegawai.store') }}">
            @csrf
            @if ($edit)
                @method('PUT')
            @endif

            <div class="mb-3">
                <label for="nip" class="form-label">NIS</label>
                <input type="text" id="nip" name="nip" maxlength="20" inputmode="numeric"
                       class="form-control @error('nip') is-invalid @enderror"
                       value="{{ old('nip', $item->nip) }}">
            </div>

            <div class="mb-3">
                <label for="nama_pegawai" class="form-label">Nama Siswa</label>
                <input type="text" id="nama_pegawai" name="nama_pegawai" maxlength="100"
                       class="form-control @error('nama_pegawai') is-invalid @enderror"
                       value="{{ old('nama_pegawai', $item->nama_pegawai) }}">
            </div>

            <div class="mb-3">
                <label for="alamat" class="form-label">Kelas</label>
                <input type="text" id="alamat" name="alamat" maxlength="100" list="daftar-kelas"
                       class="form-control @error('alamat') is-invalid @enderror"
                       value="{{ old('alamat', $item->alamat) }}" placeholder="contoh: X PPLG 1">
                <datalist id="daftar-kelas">
                    @foreach ($daftarKelas as $k)
                        <option value="{{ $k }}">
                    @endforeach
                </datalist>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('pegawai.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection
