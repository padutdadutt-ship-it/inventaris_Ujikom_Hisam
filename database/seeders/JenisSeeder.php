<?php

namespace Database\Seeders;

use App\Models\Jenis;
use Illuminate\Database\Seeder;

class JenisSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['kode_jenis' => 'OLR', 'nama_jenis' => 'Alat Olahraga', 'keterangan' => 'Peralatan dan sarana olahraga'],
            ['kode_jenis' => 'ELK', 'nama_jenis' => 'Elektronik',    'keterangan' => 'Peralatan elektronik'],
            ['kode_jenis' => 'KSN', 'nama_jenis' => 'Kesenian',      'keterangan' => 'Peralatan musik dan kesenian'],
            ['kode_jenis' => 'LAB', 'nama_jenis' => 'Laboratorium',  'keterangan' => 'Peralatan praktikum laboratorium'],
            ['kode_jenis' => 'PBT', 'nama_jenis' => 'Perabotan',     'keterangan' => 'Perabot meja, kursi, dan lemari'],
        ];

        foreach ($data as $d) {
            // Kalau kode sudah ada, dilewati (tidak dobel)
            Jenis::firstOrCreate(['kode_jenis' => $d['kode_jenis']], $d);
        }
    }
}