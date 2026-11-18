<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | Inventaris SMK</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        body { font-size: 12px; color: #000; background: #fff; }
        .kop { text-align: center; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 16px; }
        table.tabel-cetak { width: 100%; border-collapse: collapse; }
        table.tabel-cetak th, table.tabel-cetak td { border: 1px solid #000; padding: 4px 6px; vertical-align: top; }
        table.tabel-cetak th { background: #eee; }
        @@page { size: A4 landscape; margin: 12mm; }
    </style>
</head>
<body>
<div class="container-fluid p-3">

    <div class="d-print-none mb-3">
        <button onclick="window.print()" class="btn btn-primary btn-sm">Cetak / Simpan PDF</button>
    </div>

    <div class="kop">
        <div class="fs-5 fw-bold">INVENTARIS SARANA DAN PRASARANA SMK</div>
        <div class="fw-semibold">@yield('title')</div>
    </div>

    @yield('content')

    <div class="row mt-5">
        <div class="col-8"></div>
        <div class="col-4 text-center">
            <div>{{ now()->translatedFormat('d F Y') }}</div>
            <div>Administrator,</div>
            <div style="height:70px"></div>
            <div class="fw-bold text-decoration-underline">{{ auth()->user()->nama_petugas }}</div>
        </div>
    </div>
</div>

<script>
    window.addEventListener('load', function () { window.print(); });
</script>
</body>
</html>
