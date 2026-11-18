<?php

namespace App\Http\Controllers;

use App\Models\Inventaris;
use App\Models\Jenis;
use App\Models\Pegawai;
use App\Models\Peminjaman;
use App\Models\Ruang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EksporImporController extends Controller
{
    protected $daftarKondisi = ['Baik', 'Rusak Ringan', 'Rusak Berat'];

    public function index()
    {
        return view('eksporimpor.index', [
            'jmlInventaris' => Inventaris::count(),
            'jmlPeminjam'   => Pegawai::count(),
            'jmlPeminjaman' => Peminjaman::count(),
        ]);
    }

    /* ======================= EKSPOR ======================= */

    protected function unduh($nama, array $judul, $baris)
    {
        return response()->streamDownload(function () use ($judul, $baris) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM agar Excel membaca huruf dengan benar
            fputcsv($out, $judul, ';');
            foreach ($baris as $b) {
                fputcsv($out, $b, ';');
            }
            fclose($out);
        }, $nama, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function eksporInventaris()
    {
        $baris = Inventaris::with(['jenis', 'ruang'])->orderBy('id_inventaris')->get()->map(function ($b) {
            return [
                $b->nama,
                $b->jenis->nama_jenis ?? '',
                $b->ruang->nama_ruang ?? '',
                $b->jumlah,
                $b->kondisi,
                $b->kode_inventaris,
                $b->tanggal_register->format('Y-m-d'),
                $b->keterangan,
            ];
        });

        return $this->unduh(
            'inventaris_' . date('Ymd') . '.csv',
            ['nama', 'kategori', 'ruang', 'jumlah', 'kondisi', 'kode', 'tanggal_register', 'keterangan'],
            $baris
        );
    }

    public function eksporPeminjam()
    {
        // NIS dibungkus ="..." agar Excel tidak mengubahnya menjadi angka ilmiah (2.6E+12).
        // Saat diimpor kembali, karakter selain angka dibuang otomatis.
        $baris = Pegawai::orderBy('id_pegawai')->get()->map(function ($p) {
            return ['="' . $p->nip . '"', $p->nama_pegawai, $p->alamat];
        });

        return $this->unduh('peminjam_' . date('Ymd') . '.csv', ['nis', 'nama', 'kelas'], $baris);
    }

    public function eksporPeminjaman()
    {
        $baris = Peminjaman::with(['pegawai', 'petugas', 'detail.inventaris'])
            ->orderBy('tanggal_pinjam', 'desc')->get()->map(function ($p) {
                return [
                    $p->tanggal_pinjam->format('Y-m-d H:i'),
                    $p->pegawai->nama_pegawai ?? '',
                    $p->pegawai->alamat ?? '',
                    $p->detail->map(function ($d) {
                        return ($d->inventaris->nama ?? '-') . ' x' . $d->jumlah;
                    })->implode(', '),
                    $p->detail->sum('jumlah'),
                    $p->status_peminjaman,
                    $p->tanggal_kembali ? $p->tanggal_kembali->format('Y-m-d H:i') : '',
                    $p->petugas->nama_petugas ?? 'Peminjam (tanpa login)',
                ];
            });

        return $this->unduh(
            'peminjaman_' . date('Ymd') . '.csv',
            ['tanggal_pinjam', 'nama', 'kelas', 'barang', 'total_unit', 'status', 'tanggal_kembali', 'dicatat_oleh'],
            $baris
        );
    }

    public function template($jenis)
    {
        if ($jenis === 'peminjam') {
            return $this->unduh('template_impor_peminjam.csv', ['nis', 'nama', 'kelas'],
                [['2627012119001', 'NAMA SISWA CONTOH', 'X PPLG 1']]);
        }

        if ($jenis === 'inventaris') {
            return $this->unduh('template_impor_inventaris.csv', ['nama', 'kategori', 'ruang', 'jumlah', 'kondisi'],
                [['LapTop', 'Alat Praktik/Lab', 'LAB 1 PPLG', 10, 'Baik']]);
        }

        abort(404);
    }

    /* ======================= IMPOR ======================= */

    protected function periksaBerkas(Request $request)
    {
        $request->validate([
            'berkas' => ['required', 'file', 'max:2048', function ($atribut, $nilai, $gagal) {
                if (! in_array(strtolower($nilai->getClientOriginalExtension()), ['csv', 'txt'], true)) {
                    $gagal('File harus berformat .csv');
                }
            }],
        ], [
            'berkas.required' => 'Pilih file CSV terlebih dahulu.',
            'berkas.file'     => 'File tidak valid.',
            'berkas.max'      => 'Ukuran file maksimal 2 MB.',
        ]);
    }

    protected function bacaCsv($berkas)
    {
        $isi = file_get_contents($berkas->getRealPath());
        $isi = preg_replace('/^\xEF\xBB\xBF/', '', $isi);
        $isi = str_replace(["\r\n", "\r"], "\n", $isi);

        $teks = array_values(array_filter(explode("\n", $isi), function ($l) {
            return trim($l) !== '';
        }));

        if (count($teks) < 2) {
            return [];
        }

        $pemisah = substr_count($teks[0], ';') >= substr_count($teks[0], ',') ? ';' : ',';

        $kepala = array_map(function ($h) {
            return Str::lower(trim($h));
        }, str_getcsv($teks[0], $pemisah));

        $baris = [];
        foreach (array_slice($teks, 1) as $i => $l) {
            $kolom = array_pad(array_slice(str_getcsv($l, $pemisah), 0, count($kepala)), count($kepala), '');
            $baris[] = ['no' => $i + 2, 'data' => array_combine($kepala, $kolom)];
        }

        return $baris;
    }

    public function imporPeminjam(Request $request)
    {
        $this->periksaBerkas($request);
        $baris = $this->bacaCsv($request->file('berkas'));

        if (! $baris) {
            return back()->with('error', 'File kosong. Baris pertama harus berisi judul kolom (nis, nama, kelas), lalu data di bawahnya.');
        }

        $ada = [];
        foreach (Pegawai::get(['nip', 'nama_pegawai']) as $p) {
            $ada[$p->nip . '|' . Str::lower($p->nama_pegawai)] = true;
        }

        $simpan = [];
        $lewat = 0;
        $galat = [];
        $now = now();

        foreach ($baris as $r) {
            $d = $r['data'];
            $nis   = preg_replace('/\D/', '', $d['nis'] ?? ($d['nip'] ?? ''));
            $nama  = trim($d['nama'] ?? ($d['nama_siswa'] ?? ($d['nama_pegawai'] ?? '')));
            $kelas = trim($d['kelas'] ?? ($d['alamat'] ?? ''));

            if (! preg_match('/^\d{8,20}$/', $nis)) {
                $galat[] = 'Baris ' . $r['no'] . ': NIS harus berupa angka 8 sampai 20 digit.';
                continue;
            }
            if ($nama === '' || mb_strlen($nama) > 100) {
                $galat[] = 'Baris ' . $r['no'] . ': nama wajib diisi (maksimal 100 karakter).';
                continue;
            }
            if ($kelas === '' || mb_strlen($kelas) > 100) {
                $galat[] = 'Baris ' . $r['no'] . ': kelas wajib diisi (maksimal 100 karakter).';
                continue;
            }

            $kunci = $nis . '|' . Str::lower($nama);
            if (isset($ada[$kunci])) {
                $lewat++;
                continue;
            }
            $ada[$kunci] = true;

            $simpan[] = [
                'nama_pegawai' => $nama,
                'nip'          => $nis,
                'alamat'       => $kelas,
                'created_at'   => $now,
                'updated_at'   => $now,
            ];
        }

        DB::transaction(function () use ($simpan) {
            foreach (array_chunk($simpan, 100) as $potongan) {
                Pegawai::insert($potongan);
            }
        });

        return redirect()->route('ekspor.index')
            ->with('success', 'Impor peminjam selesai: ' . count($simpan) . ' ditambahkan, ' . $lewat . ' dilewati (sudah ada), ' . count($galat) . ' baris gagal.')
            ->with('galat', array_slice($galat, 0, 20));
    }

    public function imporInventaris(Request $request)
    {
        $this->periksaBerkas($request);
        $baris = $this->bacaCsv($request->file('berkas'));

        if (! $baris) {
            return back()->with('error', 'File kosong. Baris pertama harus berisi judul kolom (nama, kategori, ruang, jumlah, kondisi), lalu data di bawahnya.');
        }

        $jenis = [];
        foreach (Jenis::all() as $j) {
            $jenis[Str::lower(trim($j->nama_jenis))] = $j->id_jenis;
        }

        $ruang = [];
        foreach (Ruang::all() as $r) {
            $ruang[Str::lower(trim($r->nama_ruang))] = $r->id_ruang;
            $ruang[Str::lower(trim($r->kode_ruang))] = $r->id_ruang;
        }

        $kondisi = [];
        foreach ($this->daftarKondisi as $k) {
            $kondisi[Str::lower($k)] = $k;
        }

        $kodeAda = array_flip(Inventaris::pluck('kode_inventaris')->all());
        $urut = (int) Inventaris::max('id_inventaris');

        $ada = [];
        foreach (Inventaris::get(['nama', 'id_jenis', 'id_ruang']) as $b) {
            $ada[Str::lower($b->nama) . '|' . $b->id_jenis . '|' . $b->id_ruang] = true;
        }

        $simpan = [];
        $lewat = 0;
        $galat = [];
        $now = now();

        foreach ($baris as $r) {
            $d = $r['data'];
            $nama    = trim($d['nama'] ?? ($d['nama_barang'] ?? ''));
            $katAsli = trim($d['kategori'] ?? ($d['jenis'] ?? ''));
            $rgAsli  = trim($d['ruang'] ?? '');
            $jumlah  = trim($d['jumlah'] ?? '');
            $kon     = Str::lower(trim($d['kondisi'] ?? ''));

            if ($nama === '' || mb_strlen($nama) > 150) {
                $galat[] = 'Baris ' . $r['no'] . ': nama barang wajib diisi (maksimal 150 karakter).';
                continue;
            }
            if (! isset($jenis[Str::lower($katAsli)])) {
                $galat[] = 'Baris ' . $r['no'] . ': kategori "' . $katAsli . '" tidak ditemukan di Kategori Barang.';
                continue;
            }
            if (! isset($ruang[Str::lower($rgAsli)])) {
                $galat[] = 'Baris ' . $r['no'] . ': ruang "' . $rgAsli . '" tidak ditemukan di Data Ruang.';
                continue;
            }
            if (! ctype_digit($jumlah) || (int) $jumlah > 100000) {
                $galat[] = 'Baris ' . $r['no'] . ': jumlah harus berupa angka bulat 0 sampai 100000.';
                continue;
            }
            if ($kon === '') {
                $kon = 'baik';
            }
            if (! isset($kondisi[$kon])) {
                $galat[] = 'Baris ' . $r['no'] . ': kondisi harus Baik, Rusak Ringan, atau Rusak Berat.';
                continue;
            }

            $idJenis = $jenis[Str::lower($katAsli)];
            $idRuang = $ruang[Str::lower($rgAsli)];

            $kunci = Str::lower($nama) . '|' . $idJenis . '|' . $idRuang;
            if (isset($ada[$kunci])) {
                $lewat++;
                continue;
            }
            $ada[$kunci] = true;

            do {
                $urut++;
                $kode = 'INV-' . str_pad($urut, 4, '0', STR_PAD_LEFT);
            } while (isset($kodeAda[$kode]));
            $kodeAda[$kode] = true;

            $tgl = trim($d['tanggal_register'] ?? '');
            if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $tgl)) {
                $tgl = $now->toDateString();
            }

            $ket = trim($d['keterangan'] ?? '');

            $simpan[] = [
                'nama'             => $nama,
                'kondisi'          => $kondisi[$kon],
                'keterangan'       => $ket !== '' ? mb_substr($ket, 0, 500) : null,
                'jumlah'           => (int) $jumlah,
                'id_jenis'         => $idJenis,
                'tanggal_register' => $tgl,
                'id_ruang'         => $idRuang,
                'kode_inventaris'  => $kode,
                'id_petugas'       => auth()->user()->id_petugas,
                'created_at'       => $now,
                'updated_at'       => $now,
            ];
        }

        DB::transaction(function () use ($simpan) {
            foreach (array_chunk($simpan, 50) as $potongan) {
                Inventaris::insert($potongan);
            }
        });

        return redirect()->route('ekspor.index')
            ->with('success', 'Impor inventaris selesai: ' . count($simpan) . ' ditambahkan, ' . $lewat . ' dilewati (sudah ada), ' . count($galat) . ' baris gagal.')
            ->with('galat', array_slice($galat, 0, 20));
    }
}
