<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Membaca file CSV di database/data menjadi array asosiatif.
     */
    private function baca(string $nama): array
    {
        $baris = [];
        $h = fopen(database_path('data/' . $nama), 'r');

        if ($h === false) {
            return $baris;
        }

        $kepala = fgetcsv($h, 0, ',');

        while (($r = fgetcsv($h, 0, ',')) !== false) {
            if ($r === [null]) {
                continue;
            }
            $baris[] = array_combine($kepala, $r);
        }

        fclose($h);

        return $baris;
    }

    public function run(): void
    {
        $now = now();

        // ----- Level -----
        DB::table('level')->insert([
            ['id_level' => 1, 'nama_level' => 'Administrator', 'created_at' => $now, 'updated_at' => $now],
            ['id_level' => 2, 'nama_level' => 'Operator',      'created_at' => $now, 'updated_at' => $now],
        ]);

        // ----- Petugas / akun login (password: 12345678) -----
        DB::table('petugas')->insert([
            ['username' => 'admin',    'password' => Hash::make('12345678'), 'nama_petugas' => 'Administrator',    'id_level' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['username' => 'operator', 'password' => Hash::make('12345678'), 'nama_petugas' => 'Operator Sekolah', 'id_level' => 2, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // ----- Ruang (59 data dari file guru) -----
        $ruang = [];
        foreach ($this->baca('ruang.csv') as $r) {
            $ruang[] = [
                'nama_ruang' => $r['nama_ruang'],
                'kode_ruang' => $r['kode_ruang'],
                'keterangan' => $r['keterangan'] !== '' ? $r['keterangan'] : null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('ruang')->insert($ruang);

        // ----- Jenis: 3 kategori dari file guru -----
        DB::table('jenis')->insert([
            ['kode_jenis' => '2', 'nama_jenis' => 'Perabotan',        'keterangan' => 'Kategori 2 pada daftar kode barang', 'created_at' => $now, 'updated_at' => $now],
            ['kode_jenis' => '3', 'nama_jenis' => 'Elektronik',       'keterangan' => 'Kategori 3 pada daftar kode barang', 'created_at' => $now, 'updated_at' => $now],
            ['kode_jenis' => '4', 'nama_jenis' => 'Alat Praktik/Lab', 'keterangan' => 'Kategori 4 pada daftar kode barang', 'created_at' => $now, 'updated_at' => $now],
        ]);

        $idJenis = DB::table('jenis')->pluck('id_jenis', 'kode_jenis');
        $idRuang = DB::table('ruang')->pluck('id_ruang', 'kode_ruang');

        // ----- Peminjam: 362 siswa dari file guru -----
        $this->call(PeminjamSeeder::class);

        // ----- Inventaris: 129 barang dari file guru -----
        // Kode barang (2-01, 3-01, 4-01, ...) diambil dari guru. Kategori ditentukan
        // dari angka di depan kode. Ruang, jumlah, dan kondisi adalah nilai sementara.
        $barang = [];
        foreach ($this->baca('barang.csv') as $r) {
            $kategori = explode('-', $r['kode_jenis'])[0];

            $barang[] = [
                'nama'             => $r['nama_jenis'],
                'kondisi'          => 'Baik',
                'keterangan'       => null,
                'jumlah'           => 1,
                'id_jenis'         => $idJenis[$kategori],
                'tanggal_register' => $now->toDateString(),
                'id_ruang'         => $idRuang['R-12'],
                'kode_inventaris'  => $r['kode_jenis'],
                'id_petugas'       => 1,
                'created_at'       => $now,
                'updated_at'       => $now,
            ];
        }

        foreach (array_chunk($barang, 50) as $potongan) {
            DB::table('inventaris')->insert($potongan);
        }
    }
}
