@extends('layouts.app')

@section('title', 'Pinjam Barang')

@section('content')
<div class="card border-0 shadow-sm" style="max-width: 860px;">
    <div class="card-header bg-white fw-semibold">Form Peminjaman</div>
    <div class="card-body">

        @if ($barang->isEmpty())
            <div class="alert alert-warning mb-0">
                Belum ada barang yang tersedia untuk dipinjam.
                <a href="{{ route('peminjaman.index') }}">Kembali</a>
            </div>
        @else
        <form method="POST" action="{{ route('peminjaman.store') }}">
            @csrf

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="id_pegawai" class="form-label">Peminjam</label>
                    <select id="id_pegawai" name="id_pegawai" class="form-select @error('id_pegawai') is-invalid @enderror">
                        <option value="">-- Pilih peminjam --</option>
                        @foreach ($daftarPegawai as $p)
                            <option value="{{ $p->id_pegawai }}" @selected(old('id_pegawai') == $p->id_pegawai)>
                                {{ $p->nama_pegawai }} - {{ $p->alamat }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="tanggal_pinjam" class="form-label">Tanggal Pinjam</label>
                    <input type="datetime-local" id="tanggal_pinjam" name="tanggal_pinjam"
                           class="form-control @error('tanggal_pinjam') is-invalid @enderror"
                           value="{{ old('tanggal_pinjam', now()->format('Y-m-d\TH:i')) }}">
                </div>
            </div>

            <label class="form-label">Barang yang Dipinjam</label>
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-2">
                    <thead class="table-light">
                        <tr>
                            <th>Barang</th>
                            <th style="width:140px">Jumlah</th>
                            <th style="width:80px"></th>
                        </tr>
                    </thead>
                    <tbody id="wadah-baris"></tbody>
                </table>
            </div>
            <button type="button" id="btn-tambah" class="btn btn-outline-primary btn-sm mb-4">+ Tambah Barang</button>

            <div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary">Batal</a>
            </div>
        </form>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    var barang = @json($barang);
    var awal = @json($baris);
    var wadah = document.getElementById('wadah-baris');
    var tombol = document.getElementById('btn-tambah');
    var idx = 0;

    if (!wadah) { return; }

    function tambahBaris(idInv, jumlah) {
        var i = idx++;
        var tr = document.createElement('tr');

        // Kolom barang
        var td1 = document.createElement('td');
        var sel = document.createElement('select');
        sel.name = 'items[' + i + '][id_inventaris]';
        sel.className = 'form-select';

        var kosong = document.createElement('option');
        kosong.value = '';
        kosong.textContent = '-- Pilih barang --';
        sel.appendChild(kosong);

        barang.forEach(function (b) {
            var o = document.createElement('option');
            o.value = b.id;
            o.textContent = b.label;
            o.dataset.max = b.tersedia;
            if (String(b.id) === String(idInv)) { o.selected = true; }
            sel.appendChild(o);
        });
        td1.appendChild(sel);

        // Kolom jumlah
        var td2 = document.createElement('td');
        var jml = document.createElement('input');
        jml.type = 'number';
        jml.min = 1;
        jml.name = 'items[' + i + '][jumlah]';
        jml.className = 'form-control';
        jml.value = jumlah || 1;
        td2.appendChild(jml);

        function aturMaks() {
            var o = sel.options[sel.selectedIndex];
            if (o && o.dataset.max) {
                jml.max = o.dataset.max;
            } else {
                jml.removeAttribute('max');
            }
        }
        sel.addEventListener('change', aturMaks);
        aturMaks();

        // Kolom hapus
        var td3 = document.createElement('td');
        td3.className = 'text-center';
        var hapus = document.createElement('button');
        hapus.type = 'button';
        hapus.className = 'btn btn-outline-danger btn-sm';
        hapus.textContent = 'Hapus';
        hapus.addEventListener('click', function () {
            if (wadah.children.length > 1) {
                tr.remove();
            } else {
                sel.value = '';
                jml.value = 1;
                aturMaks();
            }
        });
        td3.appendChild(hapus);

        tr.appendChild(td1);
        tr.appendChild(td2);
        tr.appendChild(td3);
        wadah.appendChild(tr);
    }

    awal.forEach(function (r) {
        tambahBaris(r.id_inventaris, r.jumlah);
    });

    tombol.addEventListener('click', function () {
        tambahBaris('', 1);
    });
})();
</script>
@endpush
