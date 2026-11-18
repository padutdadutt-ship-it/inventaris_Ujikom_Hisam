<?php

namespace Database\Seeders;

use App\Models\Inventaris;
use App\Models\Jenis;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PindahKategoriSeeder extends Seeder
{
    public function run()
    {
        $model = new Inventaris();
        $tabel = $model->getTable();
        $pk    = $model->getKeyName();
        $fk    = (new Jenis())->inventaris()->getForeignKeyName();

        // Cari kolom nama barang
        $kolomNama = null;
        foreach (['nama', 'nama_inventaris', 'nama_barang', 'nama_item'] as $kandidat) {
            if (Schema::hasColumn($tabel, $kandidat)) {
                $kolomNama = $kandidat;
                break;
            }
        }
        if (! $kolomNama) {
            $this->command->error('Kolom nama barang tidak ditemukan. Kolom di tabel ' . $tabel . ': '
                . implode(', ', Schema::getColumnListing($tabel)));
            return;
        }

        // Cari id tiap kategori dari namanya
        $semuaJenis = Jenis::all();
        $jenisKey   = (new Jenis())->getKeyName();
        $cari = function (string $kata) use ($semuaJenis, $jenisKey) {
            foreach ($semuaJenis as $j) {
                if (stripos($j->nama_jenis, $kata) !== false) {
                    return $j->{$jenisKey};
                }
            }
            return null;
        };

        $kategori = [
            'Alat Olahraga' => [
                'id'   => $cari('olahraga'),
                'kata' => [
                    'bola', 'voli', 'basket', 'futsal', 'sepak bola', 'sepakbola', 'bulu tangkis', 'badminton',
                    'raket', 'shuttlecock', 'matras', 'lembing', 'cakram', 'tolak peluru', 'gawang', 'tenis',
                    'meja pingpong', 'meja tenis', 'pingpong', 'peluit', 'senam', 'dumbbell', 'barbel',
                    'skipping', 'tongkat estafet', 'estafet', 'lompat tinggi', 'lompat jauh', 'hoop', 'takraw',
                    'papan skor', 'net', 'stopwatch', 'cone', 'pompa bola', 'palang', 'trampolin', 'karate',
                    'taekwondo', 'pencak silat', 'panahan', 'catur', 'tolak',
                ],
            ],
            'Kesenian' => [
                'id'   => $cari('kesenian'),
                'kata' => [
                    'gitar', 'piano', 'pianika', 'organ', 'keyboard musik', 'drum', 'drumband', 'drum band',
                    'biola', 'cello', 'angklung', 'gamelan', 'saron', 'kendang', 'gong', 'kolintang', 'suling',
                    'seruling', 'terompet', 'trompet', 'saxophone', 'saksofon', 'klarinet', 'ukulele',
                    'tamborin', 'marching band', 'rebana', 'kecapi', 'harmonika', 'calung', 'bonang', 'kenong',
                    'kempul', 'bass', 'cajon', 'lukisan', 'kanvas', 'easel', 'kostum', 'topeng', 'wayang',
                ],
            ],
            'Alat Praktik/Lab' => [
                'id'   => $cari('praktik') ?? $cari('lab'),
                'kata' => [
                    'obeng', 'tang', 'multimeter', 'avometer', 'solder', 'bor', 'gergaji', 'palu', 'kunci pas',
                    'kunci ring', 'kunci inggris', 'kunci sok', 'set kunci', 'mesin bubut', 'bubut', 'mesin las',
                    'las', 'gerinda', 'oscilloscope', 'osiloskop', 'jangka sorong', 'mikrometer', 'mistar',
                    'waterpass', 'tespen', 'kikir', 'ragum', 'pahat', 'amplas', 'toolkit', 'tool kit', 'tool set',
                    'trainer', 'jig', 'kompresor', 'mesin frais', 'frais', 'scrap', 'clamp meter', 'power supply',
                    'breadboard', 'mikroskop', 'tabung', 'gelas ukur', 'labu', 'erlenmeyer', 'buret', 'pipet',
                    'bunsen', 'neraca', 'timbangan', 'termometer', 'engine stand', 'scanner otomotif',
                    'dongkrak', 'treker', 'kompresi', 'tester', 'peralatan', 'perkakas', 'mesin diesel',
                    'mesin bor', 'mesin potong', 'mesin gergaji', 'toolbox', 'tool box',
                ],
            ],
            'Elektronik' => [
                'id'   => $cari('elektronik'),
                'kata' => [
                    'komputer', 'computer', 'pc', 'laptop', 'notebook', 'monitor', 'layar', 'proyektor',
                    'projector', 'lcd', 'led', 'tv', 'televisi', 'printer', 'scanner', 'ac', 'air conditioner',
                    'kipas', 'kipas angin', 'speaker', 'sound system', 'mikrofon', 'microphone', 'mic',
                    'amplifier', 'mixer', 'router', 'switch', 'hub', 'access point', 'modem', 'ups',
                    'stabilizer', 'cctv', 'kamera', 'camera', 'dispenser', 'kulkas', 'lemari es', 'fotokopi',
                    'telepon', 'handphone', 'hp', 'tablet', 'mouse', 'keyboard', 'hardisk', 'harddisk',
                    'flashdisk', 'server', 'rak server', 'lampu', 'jam digital', 'fingerprint', 'finger print',
                    'mesin absensi', 'setrika', 'rice cooker', 'teko listrik', 'water heater', 'mesin ketik',
                    'kabel', 'genset', 'stop kontak', 'terminal listrik', 'headset', 'webcam', 'tv led',
                    'interactive panel', 'smart tv', 'walkie talkie', 'ht',
                ],
            ],
            'Perabotan' => [
                'id'   => $cari('perabot'),
                'kata' => [
                    'meja', 'kursi', 'bangku', 'lemari', 'rak', 'loker', 'papan tulis', 'whiteboard',
                    'white board', 'papan pengumuman', 'papan informasi', 'papan nama', 'papan tempel',
                    'papan flanel', 'sofa', 'filing cabinet', 'brankas', 'podium', 'mimbar', 'tempat sampah',
                    'tong sampah', 'karpet', 'gorden', 'tirai', 'tikar', 'kasur', 'tempat tidur', 'ranjang',
                    'cermin', 'jam dinding', 'vas', 'bufet', 'buffet', 'almari', 'laci', 'kabinet', 'cabinet',
                    'partisi', 'sekat', 'gantungan', 'keset', 'sapu', 'pel', 'ember', 'standing hanger',
                ],
            ],
        ];

        foreach ($kategori as $nama => $k) {
            if ($k['id'] === null) {
                $this->command->error("Kategori \"{$nama}\" tidak ditemukan di tabel jenis. Dibatalkan, tidak ada data yang diubah.");
                return;
            }
        }

        $rows = DB::table($tabel)->select($pk, $kolomNama, $fk)->get();

        // Cadangan kategori lama
        $cadangan = storage_path('app/backup_kategori_' . date('Ymd_His') . '.json');
        file_put_contents($cadangan, json_encode($rows->pluck($fk, $pk)->all()));

        $namaJenis = $semuaJenis->pluck('nama_jenis', $jenisKey)->all();
        $dipindah  = [];
        $tidakCocok = [];
        $sudahBenar = 0;

        foreach ($rows as $r) {
            $namaBarang = (string) $r->{$kolomNama};
            $hasil = $this->tebak(mb_strtolower(trim($namaBarang)), $kategori);

            if ($hasil === null) {
                $tidakCocok[] = $namaBarang . '  [kategori sekarang: ' . ($namaJenis[$r->{$fk}] ?? 'kosong') . ']';
                continue;
            }

            [$namaKategori, $idKategori] = $hasil;

            if ((string) $r->{$fk} === (string) $idKategori) {
                $sudahBenar++;
                continue;
            }

            DB::table($tabel)->where($pk, $r->{$pk})->update([$fk => $idKategori]);
            $dipindah[] = sprintf('%s : %s -> %s', $namaBarang, $namaJenis[$r->{$fk}] ?? 'kosong', $namaKategori);
        }

        // Tulis laporan
        $laporan  = "== DIPINDAHKAN (" . count($dipindah) . ") ==\n" . implode("\n", $dipindah) . "\n\n";
        $laporan .= "== NAMA TIDAK DIKENALI, DIBIARKAN (" . count($tidakCocok) . ") ==\n" . implode("\n", $tidakCocok) . "\n";
        file_put_contents(storage_path('app/hasil_pindah.txt'), $laporan);

        $this->command->info('Dipindahkan      : ' . count($dipindah) . ' barang');
        $this->command->info('Sudah benar      : ' . $sudahBenar . ' barang');
        $this->command->info('Tidak dikenali   : ' . count($tidakCocok) . ' barang (dibiarkan)');
        $this->command->info('Laporan lengkap  : storage/app/hasil_pindah.txt');
        $this->command->info('Cadangan         : ' . basename($cadangan));
    }

    /** Kata yang muncul paling awal di nama dianggap nama barang utamanya. */
    private function tebak(string $teks, array $kategori): ?array
    {
        $terbaik = null;
        $posTerbaik = PHP_INT_MAX;
        $panjang = 0;

        foreach ($kategori as $nama => $k) {
            foreach ($k['kata'] as $kata) {
                if (preg_match('/\b' . preg_quote($kata, '/') . '\b/u', $teks, $m, PREG_OFFSET_CAPTURE)) {
                    $pos = $m[0][1];
                    $len = strlen($kata);

                    if ($pos < $posTerbaik || ($pos === $posTerbaik && $len > $panjang)) {
                        $terbaik = [$nama, $k['id']];
                        $posTerbaik = $pos;
                        $panjang = $len;
                    }
                }
            }
        }

        return $terbaik;
    }
}