<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PeminjamSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $h = fopen(database_path('data/siswa.csv'), 'r');

        if ($h === false) {
            return;
        }

        $kepala = fgetcsv($h, 0, ',');
        $baris = [];

        while (($r = fgetcsv($h, 0, ',')) !== false) {
            if ($r === [null]) {
                continue;
            }
            $baris[] = array_combine($kepala, $r);
        }

        fclose($h);

        // Hapus pegawai contoh yang belum pernah meminjam
        DB::table('pegawai')
            ->whereNotIn('id_pegawai', DB::table('peminjaman')->select('id_pegawai'))
            ->delete();

        // Aman dijalankan berulang: lewati siswa yang sudah ada (NIS dan nama sama)
        $ada = [];
        foreach (DB::table('pegawai')->get(['nip', 'nama_pegawai']) as $p) {
            $ada[$p->nip . '|' . $p->nama_pegawai] = true;
        }

        $data = [];
        foreach ($baris as $r) {
            if (isset($ada[$r['nis'] . '|' . $r['nama_siswa']])) {
                continue;
            }

            $data[] = [
                'nama_pegawai' => $r['nama_siswa'],
                'nip'          => $r['nis'],
                'alamat'       => $r['nama_kelas'],
                'created_at'   => $now,
                'updated_at'   => $now,
            ];
        }

        foreach (array_chunk($data, 100) as $potongan) {
            DB::table('pegawai')->insert($potongan);
        }
    }
}
