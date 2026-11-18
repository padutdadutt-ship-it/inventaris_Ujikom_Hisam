@extends($cetak ? 'layouts.cetak' : 'layouts.app')

@section('title', 'Laporan Inventaris')

@section('content')
@unless ($cetak)
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Laporan Inventaris</h5>
        <a href="{{ request()->fullUrlWithQuery(['cetak' => 1]) }}" target="_blank" class="btn btn-outline-secondary btn-sm">Cetak</a>
    </div>

    <form method="GET" action="{{ route('laporan.inventaris') }}" class="row g-2 mb-3">
        <div class="col-6 col-md-3">
            <select name="id_jenis" class="form-select form-select-sm" autocomplete="off">
                <option value="">Semua jenis</option>
                @foreach ($daftarJenis as $j)
                    <option value="{{ $j->id_jenis }}" @selected(request('id_jenis') == $j->id_jenis)>{{ $j->nama_jenis }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-3">
            <select name="id_ruang" class="form-select form-select-sm" autocomplete="off">
                <option value="">Semua ruang</option>
                @foreach ($daftarRuang as $r)
                    <option value="{{ $r->id_ruang }}" @selected(request('id_ruang') == $r->id_ruang)>{{ $r->nama_ruang }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-3">
            <select name="kondisi" class="form-select form-select-sm" autocomplete="off">
                <option value="">Semua kondisi</option>
                @foreach ($daftarKondisi as $k)
                    <option value="{{ $k }}" @selected(request('kondisi') == $k)>{{ $k }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-3 d-flex gap-1">
            <button class="btn btn-outline-primary btn-sm">Tampilkan</button>
            <a href="{{ route('laporan.inventaris') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
        </div>
    </form>
@endunless

<div class="{{ $cetak ? '' : 'card border-0 shadow-sm' }}">
    <div class="table-responsive">
        <table class="{{ $cetak ? 'tabel-cetak' : 'table table-hover align-middle mb-0' }}">
            <thead class="{{ $cetak ? '' : 'table-light' }}">
                <tr>
                    <th style="width:40px">No</th>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th>Jenis</th>
                    <th>Ruang</th>
                    <th>Kondisi</th>
                    <th>Tgl Register</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-center">Dipinjam</th>
                    <th class="text-center">Tersedia</th>
                    <th class="text-center">Kali Dipinjam</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $i => $row)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $row->kode_inventaris }}</td>
                        <td>{{ $row->nama }}</td>
                        <td>{{ $row->jenis->nama_jenis ?? '-' }}</td>
                        <td>{{ $row->ruang->nama_ruang ?? '-' }}</td>
                        <td>{{ $row->kondisi }}</td>
                        <td>{{ $row->tanggal_register->format('d/m/Y') }}</td>
                        <td class="text-center">{{ $row->jumlah }}</td>
                        <td class="text-center">{{ (int) $row->dipinjam }}</td>
                        <td class="text-center">{{ $row->jumlah - (int) $row->dipinjam }}</td>
                        <td class="text-center">{{ (int) $row->kali_dipinjam }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted py-4">Tidak ada data.</td>
                    </tr>
                @endforelse
            </tbody>
            @if ($data->count())
                <tfoot>
                    <tr>
                        <th colspan="7" class="text-end">Total</th>
                        <th class="text-center">{{ $totalJumlah }}</th>
                        <th class="text-center">{{ $totalDipinjam }}</th>
                        <th class="text-center">{{ $totalJumlah - $totalDipinjam }}</th>
                        <th class="text-center">{{ $totalKali }}</th>
                    </tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection