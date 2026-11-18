<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') | Inventaris SMK</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo-bn666.png') }}">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        #sidebar { width: 250px; min-height: 100vh; }
        #sidebar .nav-link { color: #cfd8e3; border-radius: .375rem; padding: .55rem .9rem; }
        #sidebar .nav-link:hover { background: rgba(255,255,255,.08); color: #fff; }
        #sidebar .nav-link.active { background: #0d6efd; color: #fff; }
        #sidebar .menu-label { font-size: .7rem; letter-spacing: .08em; color: #8a97a8; }
        #sidebar .sub { font-size: .9rem; padding: .4rem .8rem; display: flex; justify-content: space-between; align-items: center; }
        #sidebar .sub.sub-active { background: #0d6efd; color: #fff; }
        #sidebar { transition: transform .25s; }
        #sidebar-toggle { display: none; position: fixed; top: .55rem; left: .6rem; z-index: 1050; }
        #sidebar-backdrop { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 1030; }
        @media (max-width: 991.98px) {
            #sidebar { position: fixed; top: 0; left: 0; bottom: 0; z-index: 1040; transform: translateX(-100%); overflow-y: auto; padding-top: 3.5rem !important; }
            #sidebar.open { transform: translateX(0); }
            #sidebar-backdrop.show { display: block; }
            #sidebar-toggle { display: inline-block; }
            header[class] { padding-left: 3.5rem !important; }
        }
        main { overflow-x: auto; }
        @media (max-width: 575.98px) {
            main.p-4 { padding: 1rem !important; }
            header.bg-white { flex-wrap: wrap; gap: .5rem; }
        }
            main .card .text-primary, main .card .text-info, main .card .text-secondary,
        main .card .text-warning, main .card .text-success, main .card .text-danger,
        main .card .text-body-secondary, main .card .text-muted:not(small) { color: #212529 !important; }
        main .card [style*="color"]:not(.badge) { color: #212529 !important; }
    </style>
    @stack('styles')
</head>
<body>

@php
    // Jika route belum dibuat, link diarahkan ke "#"
    $link = fn ($nama) => \Illuminate\Support\Facades\Route::has($nama) ? route($nama) : '#';
    $user = auth()->user();

    $peminjamAktif = request()->routeIs('pegawai.*');

    $golongan = ['Staff', 'Guru', 'Caraka'];

    $subMenu = [
        ['Semua Siswa', null,     \App\Models\Pegawai::whereNotIn('alamat', $golongan)->count()],
        ['Kelas X',     'X',      \App\Models\Pegawai::where('alamat', 'like', 'X %')->count()],
        ['Kelas XI',    'XI',     \App\Models\Pegawai::where('alamat', 'like', 'XI %')->count()],
        ['Kelas XII',   'XII',    \App\Models\Pegawai::where('alamat', 'like', 'XII %')->count()],
        ['Staff',       'Staff',  \App\Models\Pegawai::where('alamat', 'Staff')->count()],
        ['Guru',        'Guru',   \App\Models\Pegawai::where('alamat', 'Guru')->count()],
        ['Caraka',      'Caraka', \App\Models\Pegawai::where('alamat', 'Caraka')->count()],
    ];
@endphp

<div class="d-flex">

    {{-- ================= SIDEBAR ================= --}}
    <nav id="sidebar" class="bg-dark text-white flex-shrink-0 p-3">
        <div class="d-flex align-items-center gap-2 mb-4 px-2">
            <img src="{{ asset('assets/img/logo-bn666.png') }}" alt="Logo" width="42" height="42">
            <div class="lh-sm">
                <div class="fw-bold fs-5">Inventaris SMK</div>
                <small class="text-secondary">Sarana &amp; Prasarana</small>
            </div>
        </div>

        <ul class="nav flex-column gap-1">
            <li class="menu-label text-uppercase mt-3 mb-1 px-2">Menu</li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                   href="{{ $link('dashboard') }}">Dashboard</a>
            </li>

            {{-- ===== Khusus ADMIN: Inventarisir ===== --}}
            @if ($user->isAdmin())
                <li class="menu-label text-uppercase mt-3 mb-1 px-2">Inventarisir</li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('ruang.*') ? 'active' : '' }}"
                       href="{{ $link('ruang.index') }}">Ruang</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('jenis.*') ? 'active' : '' }}"
                       href="{{ $link('jenis.index') }}">Kategori Barang</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('inventaris.*') ? 'active' : '' }}"
                       href="{{ $link('inventaris.index') }}">Inventaris</a>
                </li>
            @endif

            {{-- ===== Khusus OPERATOR: Transaksi ===== --}}
            @if ($user->isOperator())
                <li class="menu-label text-uppercase mt-3 mb-1 px-2">Transaksi</li>

                @php
                    $aktifPinjam = request()->routeIs('peminjaman.*') || request()->routeIs('peminjaman-ruang.*');
                @endphp
                <li class="nav-item">
                    <a class="nav-link d-flex justify-content-between align-items-center {{ $aktifPinjam ? 'text-white fw-semibold' : '' }}"
                       data-bs-toggle="collapse" href="#menuPeminjaman" role="button"
                       aria-expanded="{{ $aktifPinjam ? 'true' : 'false' }}">
                        <span>Peminjaman</span>
                        <span class="small">&#9662;</span>
                    </a>
                    <div class="collapse {{ $aktifPinjam ? 'show' : '' }}" id="menuPeminjaman">
                        <ul class="nav flex-column gap-1 mt-1 ms-3">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('peminjaman.*') ? 'active' : '' }}"
                                   href="{{ $link('peminjaman.index') }}">Peminjaman Barang</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('peminjaman-ruang.*') ? 'active' : '' }}"
                                   href="{{ $link('peminjaman-ruang.index') }}">Peminjaman Ruangan</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('pengembalian.*') ? 'active' : '' }}"
                       href="{{ $link('pengembalian.index') }}">Pengembalian</a>
                </li>

                <li class="menu-label text-uppercase mt-3 mb-1 px-2">Laporan</li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('laporan.peminjaman') ? 'active' : '' }}"
                       href="{{ $link('laporan.peminjaman') }}">Laporan Peminjaman</a>
                </li>
                {{-- DIUBAH: label dari "Laporan Ruangan" --}}
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('laporan.peminjaman-ruang') ? 'active' : '' }}"
                       href="{{ $link('laporan.peminjaman-ruang') }}">Laporan Peminjaman Ruangan</a>
                </li>
            @endif

            {{-- ===== ADMIN dan OPERATOR: Profiling Peminjam ===== --}}
            @if ($user->isAdmin() || $user->isOperator())
                <li class="menu-label text-uppercase mt-3 mb-1 px-2">Peminjam</li>
                <li class="nav-item">
                    <a class="nav-link d-flex justify-content-between align-items-center {{ $peminjamAktif ? 'text-white fw-semibold' : '' }}"
                       data-bs-toggle="collapse" href="#menuPeminjam" role="button"
                       aria-expanded="{{ $peminjamAktif ? 'true' : 'false' }}" aria-controls="menuPeminjam">
                        <span>Profiling Peminjam</span>
                        <span class="small">&#9662;</span>
                    </a>
                    <div class="collapse {{ $peminjamAktif ? 'show' : '' }}" id="menuPeminjam">
                        <ul class="nav flex-column gap-1 ms-3 mt-1">
                            @foreach ($subMenu as [$label, $kode, $jumlah])
                                <li>
                                    <a class="nav-link sub {{ $peminjamAktif && request('tingkat') == $kode ? 'sub-active' : '' }}"
                                       href="{{ $kode ? route('pegawai.index', ['tingkat' => $kode]) : route('pegawai.index') }}">
                                        <span>{{ $label }}</span>
                                        <span class="badge bg-secondary">{{ $jumlah }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </li>
            @endif

            {{-- ===== Khusus ADMIN: Laporan & Pengelolaan ===== --}}
            @if ($user->isAdmin())
                <li class="menu-label text-uppercase mt-3 mb-1 px-2">Laporan</li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('laporan.inventaris') ? 'active' : '' }}"
                       href="{{ $link('laporan.inventaris') }}">Laporan Inventaris</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('laporan.ruang') ? 'active' : '' }}"
                       href="{{ $link('laporan.ruang') }}">Laporan Ruangan</a>
                </li>
                <li class="menu-label text-uppercase mt-3 mb-1 px-2">Pengelolaan</li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('petugas.*') ? 'active' : '' }}"
                       href="{{ $link('petugas.index') }}">Petugas</a>
                </li>
            @endif
        </ul>
    </nav>

    {{-- ================= KONTEN ================= --}}
    <div class="flex-grow-1 bg-light" style="min-width:0">

        <header class="bg-white border-bottom px-4 py-2 d-flex justify-content-between align-items-center">
            <span class="fw-semibold">@yield('title', 'Dashboard')</span>

            <div class="d-flex align-items-center gap-3">
                <div class="text-end lh-sm">
                    <div class="fw-semibold">{{ $user->nama_petugas }}</div>
                    <span class="badge bg-primary">{{ $user->level->nama_level }}</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-outline-danger btn-sm">Logout</button>
                </form>
            </div>
        </header>

        <main class="p-4">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
<div id="sidebar-backdrop"></div>
<button id="sidebar-toggle" type="button" class="btn btn-primary btn-sm">&#9776;</button>
<script>
(function () {
    var s = document.getElementById('sidebar');
    var b = document.getElementById('sidebar-backdrop');
    var t = document.getElementById('sidebar-toggle');
    if (!s || !t) return;
    function ubah() {
        var buka = s.classList.toggle('open');
        b.classList.toggle('show', buka);
        t.innerHTML = buka ? '&#10005;' : '&#9776;';
    }
    t.addEventListener('click', ubah);
    b.addEventListener('click', ubah);
})();
</script>
<script>
// auto-tabel-responsif
document.querySelectorAll('main table').forEach(function (t) {
    if (!t.closest('.table-responsive')) {
        var w = document.createElement('div');
        w.className = 'table-responsive';
        t.parentNode.insertBefore(w, t);
        w.appendChild(t);
    }
});
</script>
@stack('scripts')
</body>
</html>