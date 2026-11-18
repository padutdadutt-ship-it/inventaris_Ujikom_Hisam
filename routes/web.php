<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Halaman awal -> langsung ke login
Route::get('/', fn () => redirect()->route('login'));

// Hanya untuk tamu (belum login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.proses');
});

// Harus login (semua level)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// ===== Route per tahap berikutnya akan ditambahkan di bawah ini =====
// ----- Tahap 6: Jenis (khusus admin) -----
Route::middleware(['auth', 'level:admin'])->group(function () {
    Route::resource('jenis', \App\Http\Controllers\JenisController::class)->except(['show']);
});

// ----- Tahap 6.3: Ruang dan Pegawai (khusus admin) -----
Route::middleware(['auth', 'level:admin'])->group(function () {
    Route::resource('ruang', \App\Http\Controllers\RuangController::class)->except(['show']);
});

// ----- Tahap 6.4: Inventaris (khusus admin) -----
Route::middleware(['auth', 'level:admin'])->group(function () {
    Route::resource('inventaris', \App\Http\Controllers\InventarisController::class)->except(['show']);
});

// ----- Tahap 7: Peminjaman (khusus operator) -----
Route::middleware(['auth', 'level:operator'])->group(function () {
    Route::resource('peminjaman', \App\Http\Controllers\PeminjamanController::class)
        ->only(['index', 'create', 'store', 'show']);
});

// ----- Tahap 8: Pengembalian (khusus operator) -----
Route::middleware(['auth', 'level:operator'])->group(function () {
    Route::get('/pengembalian', [\App\Http\Controllers\PengembalianController::class, 'index'])->name('pengembalian.index');
    Route::post('/pengembalian/{id}', [\App\Http\Controllers\PengembalianController::class, 'kembalikan'])->name('pengembalian.kembalikan');
});

// ----- Tahap 9: Laporan -----
// Laporan Peminjaman (barang) & Laporan Peminjaman Ruangan: khusus operator
Route::middleware(['auth', 'level:operator'])->prefix('laporan')->name('laporan.')->group(function () {
    Route::get('/peminjaman', [\App\Http\Controllers\LaporanController::class, 'peminjaman'])->name('peminjaman');
});

// Laporan Inventaris & Laporan Barang Dipinjam: khusus admin
Route::middleware(['auth', 'level:admin'])->prefix('laporan')->name('laporan.')->group(function () {
    Route::get('/', fn () => redirect()->route('laporan.inventaris'))->name('index');
    Route::get('/inventaris', [\App\Http\Controllers\LaporanController::class, 'inventaris'])->name('inventaris');
    Route::get('/ruang', [\App\Http\Controllers\LaporanController::class, 'ruang'])->name('ruang');
    Route::get('/barang-dipinjam', [\App\Http\Controllers\LaporanBarangDipinjamController::class, 'index'])->name('barang-dipinjam');
});

// ----- Profiling Peminjam: data siswa (admin dan operator) -----
Route::middleware(['auth', 'level:admin,operator'])->group(function () {
    Route::resource('pegawai', \App\Http\Controllers\PegawaiController::class)->except(['show']);
});

// ----- Kelola Petugas (khusus admin) -----
Route::middleware(['auth', 'level:admin'])->group(function () {
    Route::resource('petugas', \App\Http\Controllers\PetugasController::class)->except(['show']);
});

// ----- Ekspor / Impor (khusus admin) -----
Route::middleware(['auth', 'level:admin'])->prefix('ekspor-impor')->name('ekspor.')->group(function () {
    $c = \App\Http\Controllers\EksporImporController::class;
    Route::get('/', [$c, 'index'])->name('index');
    Route::get('/inventaris', [$c, 'eksporInventaris'])->name('inventaris');
    Route::get('/peminjam', [$c, 'eksporPeminjam'])->name('peminjam');
    Route::get('/peminjaman', [$c, 'eksporPeminjaman'])->name('peminjaman');
    Route::get('/template/{jenis}', [$c, 'template'])->name('template');
    Route::post('/impor/inventaris', [$c, 'imporInventaris'])->name('impor.inventaris');
    Route::post('/impor/peminjam', [$c, 'imporPeminjam'])->name('impor.peminjam');
});

// ----- Peminjaman Ruangan (khusus operator) -----
Route::middleware(['auth', 'level:operator'])->prefix('peminjaman-ruang')->name('peminjaman-ruang.')->group(function () {
    Route::get('/', [\App\Http\Controllers\PeminjamanRuangController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\PeminjamanRuangController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\PeminjamanRuangController::class, 'store'])->name('store');
    Route::post('/{id}/selesai', [\App\Http\Controllers\PeminjamanRuangController::class, 'selesai'])->name('selesai');
});

// Laporan Ruangan: admin dan operator
Route::middleware(['auth', 'level:admin,operator'])->prefix('laporan')->name('laporan.')->group(function () {
    Route::get('/peminjaman-ruang', [\App\Http\Controllers\LaporanController::class, 'peminjamanRuang'])->name('peminjaman-ruang');
});
