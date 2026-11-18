@extends('layouts.app')

@section('title', $item->exists ? 'Edit Inventaris' : 'Tambah Inventaris')

@section('content')
@php $edit = $item->exists; @endphp

<div class="card border-0 shadow-sm" style="max-width: 820px;">
    <div class="card-header bg-white fw-semibold">{{ $edit ? 'Edit Inventaris' : 'Tambah Inventaris' }}</div>
    <div class="card-body">
        <form method="POST" action="{{ $edit ? route('inventaris.update', $item->id_inventaris) : route('inventaris.store') }}">
            @csrf
            @if ($edit)
                @method('PUT')
            @endif

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Kode Inventaris</label>
                    <input type="text" class="form-control bg-light" value="{{ $kodeBaru }}" readonly>
                    @unless ($edit)
                        <div class="form-text">Dibuat otomatis.</div>
                    @endunless
                </div>

                <div class="col-md-8">
                    <label for="nama" class="form-label">Nama Barang</label>
                    <input type="text" id="nama" name="nama" maxlength="150"
                           class="form-control @error('nama') is-invalid @enderror"
                           value="{{ old('nama', $item->nama) }}">
                </div>

                <div class="col-md-6">
                    <label for="id_jenis" class="form-label">Jenis</label>
                    <select id="id_jenis" name="id_jenis" class="form-select @error('id_jenis') is-invalid @enderror">
                        <option value="">-- Pilih jenis --</option>
                        @foreach ($daftarJenis as $j)
                            <option value="{{ $j->id_jenis }}" @selected(old('id_jenis', $item->id_jenis) == $j->id_jenis)>
                                {{ $j->nama_jenis }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="id_ruang" class="form-label">Ruang</label>
                    <select id="id_ruang" name="id_ruang" class="form-select @error('id_ruang') is-invalid @enderror">
                        <option value="">-- Pilih ruang --</option>
                        @foreach ($daftarRuang as $r)
                            <option value="{{ $r->id_ruang }}" @selected(old('id_ruang', $item->id_ruang) == $r->id_ruang)>
                                {{ $r->nama_ruang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="kondisi" class="form-label">Kondisi</label>
                    <select id="kondisi" name="kondisi" class="form-select @error('kondisi') is-invalid @enderror">
                        @foreach ($daftarKondisi as $k)
                            <option value="{{ $k }}" @selected(old('kondisi', $item->kondisi ?? 'Baik') == $k)>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="jumlah" class="form-label">Jumlah</label>
                    <input type="number" id="jumlah" name="jumlah" min="0"
                           class="form-control @error('jumlah') is-invalid @enderror"
                           value="{{ old('jumlah', $item->jumlah ?? 1) }}">
                </div>

                <div class="col-md-4">
                    <label for="tanggal_register" class="form-label">Tanggal Register</label>
                    <input type="date" id="tanggal_register" name="tanggal_register"
                           class="form-control @error('tanggal_register') is-invalid @enderror"
                           value="{{ old('tanggal_register', $item->tanggal_register ? $item->tanggal_register->format('Y-m-d') : date('Y-m-d')) }}">
                </div>

                <div class="col-12">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <textarea id="keterangan" name="keterangan" rows="3"
                              class="form-control @error('keterangan') is-invalid @enderror">{{ old('keterangan', $item->keterangan) }}</textarea>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('inventaris.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
