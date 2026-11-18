@extends('layouts.app')

@section('title', $item->exists ? 'Edit Petugas' : 'Tambah Petugas')

@section('content')
@php
    $edit = $item->exists;
    $diri = $edit && (int) $item->id_petugas === (int) auth()->user()->id_petugas;
@endphp

<div class="mb-3">
    <h5 class="mb-0">{{ $edit ? 'Edit Petugas' : 'Tambah Petugas' }}</h5>
    <nav class="small text-muted">Beranda / Kelola Petugas / {{ $edit ? 'Edit' : 'Tambah' }}</nav>
</div>

<div class="card border-0 shadow-sm rounded-4" style="max-width: 640px;">
    <div class="card-body">
        <form method="POST" action="{{ $edit ? route('petugas.update', $item->id_petugas) : route('petugas.store') }}">
            @csrf
            @if ($edit)
                @method('PUT')
            @endif

            <div class="mb-3">
                <label for="nama_petugas" class="form-label">Nama Petugas</label>
                <input type="text" id="nama_petugas" name="nama_petugas" maxlength="100"
                       class="form-control @error('nama_petugas') is-invalid @enderror"
                       value="{{ old('nama_petugas', $item->nama_petugas) }}">
            </div>

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" maxlength="50" autocomplete="off"
                       class="form-control font-monospace @error('username') is-invalid @enderror"
                       value="{{ old('username', $item->username) }}">
                <div class="form-text">Huruf, angka, strip (-), dan garis bawah (_) saja.</div>
            </div>

            <div class="mb-3">
                <label for="id_level" class="form-label">Level Hak Akses</label>
                <select id="id_level" name="id_level" autocomplete="off"
                        class="form-select @error('id_level') is-invalid @enderror" @disabled($diri)>
                    <option value="">-- Pilih level --</option>
                    @foreach ($daftarLevel as $l)
                        <option value="{{ $l->id_level }}" @selected(old('id_level', $item->id_level) == $l->id_level)>{{ $l->nama_level }}</option>
                    @endforeach
                </select>
                @if ($diri)
                    <div class="form-text">Level akun Anda sendiri tidak dapat diubah.</div>
                @endif
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="password" class="form-label">{{ $edit ? 'Password Baru' : 'Password' }}</label>
                    <input type="password" id="password" name="password" autocomplete="new-password"
                           class="form-control @error('password') is-invalid @enderror">
                </div>
                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
                           class="form-control">
                </div>
            </div>
            <div class="form-text mb-3">
                {{ $edit ? 'Kosongkan jika tidak ingin mengubah password. ' : '' }}Minimal 8 karakter.
            </div>

            <button type="submit" class="btn btn-dark">Simpan</button>
            <a href="{{ route('petugas.index') }}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection
