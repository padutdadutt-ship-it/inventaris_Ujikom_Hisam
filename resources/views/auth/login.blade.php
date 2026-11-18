<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | Inventaris SMK</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/logo-bn666.png') }}">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        :root {
            --gelap: #212529;
            --kuning: #ffc107;
            --teks-redup: #8a97a8;
        }
        body { min-height: 100vh; background: #f8f9fa; }

        .panel-kiri {
            background:
                radial-gradient(circle at 85% 12%, rgba(255,193,7,.16), transparent 42%),
                radial-gradient(circle at 10% 95%, rgba(255,255,255,.06), transparent 40%),
                var(--gelap);
            color: #fff;
        }
        .panel-kiri .garis { width: 56px; height: 4px; background: var(--kuning); border-radius: 2px; }
        .panel-kiri .fitur-ikon {
            width: 38px; height: 38px; border-radius: .6rem;
            background: rgba(255,193,7,.14); color: var(--kuning);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; flex-shrink: 0;
        }

        .kartu-login { max-width: 420px; width: 100%; }
        .kartu-login .form-control { padding: .7rem .9rem; border-radius: .5rem; }
        .kartu-login .form-control:focus {
            border-color: var(--kuning);
            box-shadow: 0 0 0 .25rem rgba(255,193,7,.25);
        }
        .kartu-login .form-check-input:checked { background-color: var(--kuning); border-color: var(--kuning); }
        .kartu-login .form-check-input:focus { border-color: var(--kuning); box-shadow: 0 0 0 .25rem rgba(255,193,7,.25); }
        .btn-kuning {
            background: var(--kuning); border: 0; color: #212529;
            padding: .75rem; border-radius: .5rem; font-weight: 700;
        }
        .btn-kuning:hover { background: #ffca2c; color: #212529; }
        .btn-lihat { border-color: #dee2e6; color: #6c757d; }
        .btn-lihat:hover { background: #f8f9fa; color: #212529; border-color: #dee2e6; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row min-vh-100">

        {{-- ============ Panel kiri (desktop) ============ --}}
        <div class="col-lg-6 panel-kiri d-none d-lg-flex flex-column justify-content-between p-5">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ asset('assets/img/logo-bn666.png') }}" alt="Logo" width="48" height="48">
                <div class="lh-sm">
                    <div class="fw-bold fs-5">Inventaris SMK</div>
                    <small style="color: var(--teks-redup)">Sarana &amp; Prasarana</small>
                </div>
            </div>

            <div>
                <div class="garis mb-4"></div>
                <h1 class="fw-bold display-6 mb-3">Kelola sarana &amp; prasarana sekolah dengan rapi.</h1>
                <p class="mb-4" style="color: var(--teks-redup)">
                    Catat inventaris, peminjaman, dan pengembalian barang dalam satu aplikasi.
                </p>

                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="fitur-ikon">1</div>
                    <div>Data ruang, kategori, dan inventaris terpusat</div>
                </div>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="fitur-ikon">2</div>
                    <div>Peminjaman dan pengembalian tercatat otomatis</div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="fitur-ikon">3</div>
                    <div>Laporan siap dilihat dan dicetak</div>
                </div>
            </div>

            <small style="color: var(--teks-redup)">&copy; {{ date('Y') }} SMK Bakti Nusantara 666</small>
        </div>

        {{-- ============ Form login ============ --}}
        <div class="col-lg-6 d-flex align-items-center justify-content-center p-4">
            <div class="kartu-login">

                <div class="d-flex d-lg-none align-items-center gap-2 mb-4 justify-content-center">
                    <img src="{{ asset('assets/img/logo-bn666.png') }}" alt="Logo" width="42" height="42">
                    <span class="fw-bold fs-5">Inventaris SMK</span>
                </div>

                <div class="card border-0 shadow-sm" style="border-radius: .75rem;">
                    <div class="card-body p-4 p-md-5">

                        <h3 class="fw-bold mb-1">Selamat Datang</h3>
                        <p class="text-muted mb-4">Masuk sebagai Administrator atau Operator.</p>

                        @if (session('success'))
                            <div class="alert alert-success py-2">{{ session('success') }}</div>
                        @endif

                        @if (session('status'))
                            <div class="alert alert-success py-2">{{ session('status') }}</div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger py-2">{{ session('error') }}</div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger py-2">
                                @foreach ($errors->all() as $e)
                                    <div>{{ $e }}</div>
                                @endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login.proses') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="username" class="form-label fw-semibold">Username</label>
                                <input type="text" id="username" name="username"
                                       value="{{ old('username') }}" class="form-control"
                                       placeholder="Masukkan username" autocomplete="username" autofocus required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">Password</label>
                                <div class="input-group">
                                    <input type="password" id="password" name="password"
                                           class="form-control"
                                           placeholder="Masukkan password" autocomplete="current-password" required>
                                    <button type="button" id="btnLihat" class="btn btn-lihat">Lihat</button>
                                </div>
                            </div>

                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                                <label class="form-check-label" for="remember">Ingat saya</label>
                            </div>

                            <button type="submit" class="btn btn-kuning w-100">Masuk</button>
                        </form>
                    </div>
                </div>

                <p class="text-center text-muted small mt-4 mb-0">Sarana &amp; Prasarana &bull; SMK Bakti Nusantara 666</p>
            </div>
        </div>

    </div>
</div>

<script>
    const pw = document.getElementById('password');
    const btn = document.getElementById('btnLihat');
    btn.addEventListener('click', () => {
        const tampil = pw.type === 'password';
        pw.type = tampil ? 'text' : 'password';
        btn.textContent = tampil ? 'Sembunyi' : 'Lihat';
    });
</script>
</body>
</html>
