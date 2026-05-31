<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Definisi instrumen Asesmen Risiko (akreditasi) — PERSIS Khanza desktop.
 *
 * Tiap instrumen menulis ke tabel Khanza apa adanya (kolom skala enum + nilai),
 * skor dihitung BY INDEX opsi enum (sama dengan getSelectedIndex() di Khanza),
 * sehingga label opsi diambil langsung dari definisi enum DB → anti salah ketik.
 *
 * Struktur baris:
 *   ['col'=>kolom_enum, 'skor_col'=>kolom_nilai|null, 'label'=>teks,
 *    'tipe'=>'pilih'|'teks', 'skor'=>[nilai per index opsi]|null, 'suffix'=>?]
 */
class AsesmenRisiko
{
    /** Cache opsi enum per tabel.kolom dalam satu request. */
    private static array $enumCache = [];

    /** Daftar instrumen per modul (sesuai Khanza desktop). */
    public static function untukModul(string $modul): array
    {
        $jatuh = ['jatuh-dewasa', 'jatuh-anak', 'jatuh-geriatri'];
        return match ($modul) {
            'ranap' => array_merge($jatuh, ['dekubitus', 'nyeri', 'gizi']),
            default => array_merge($jatuh, ['nyeri', 'gizi']), // ralan & igd
        };
    }

    public static function ada(string $key): bool
    {
        return in_array($key, [
            'jatuh-dewasa', 'jatuh-anak', 'jatuh-geriatri', 'dekubitus', 'nyeri', 'gizi',
        ], true);
    }

    /** Ambil opsi enum (urut sesuai definisi DB). */
    public static function opsiEnum(string $tabel, string $kolom): array
    {
        $ck = "$tabel.$kolom";
        if (isset(self::$enumCache[$ck])) {
            return self::$enumCache[$ck];
        }
        $opsi = [];
        try {
            $row = DB::selectOne("SHOW COLUMNS FROM `$tabel` WHERE Field = ?", [$kolom]);
            if ($row && preg_match('/^enum\((.*)\)$/i', $row->Type, $m)) {
                preg_match_all("/'((?:[^'\\\\]|\\\\.|'')*)'/", $m[1], $mm);
                $opsi = array_map(
                    fn ($s) => str_replace(["''", "\\'"], "'", $s),
                    $mm[1]
                );
            }
        } catch (\Throwable $e) {
            $opsi = [];
        }
        return self::$enumCache[$ck] = $opsi;
    }

    /**
     * Definisi lengkap satu instrumen.
     * 'kategori' = closure(int $total): array [hasil, saran].
     */
    public static function definisi(string $key): ?array
    {
        return match ($key) {
            'jatuh-dewasa'   => self::jatuhDewasa(),
            'jatuh-anak'     => self::jatuhAnak(),
            'jatuh-geriatri' => self::jatuhGeriatri(),
            'dekubitus'      => self::dekubitus(),
            'nyeri'          => self::nyeri(),
            'gizi'           => self::gizi(),
            default          => null,
        };
    }

    /* ============ RISIKO JATUH DEWASA — Skala Morse ============ */
    private static function jatuhDewasa(): array
    {
        $t = 'penilaian_lanjutan_resiko_jatuh_dewasa';
        return [
            'judul' => 'Risiko Jatuh Dewasa — Skala Morse',
            'icon'  => 'fas fa-person-walking-arrow-right',
            'tabel' => $t,
            'total_col' => 'penilaian_jatuhmorse_totalnilai',
            'hasil_col' => 'hasil_skrining',
            'saran_col' => 'saran',
            'baris' => [
                ['col' => 'penilaian_jatuhmorse_skala1', 'skor_col' => 'penilaian_jatuhmorse_nilai1', 'label' => 'Riwayat jatuh (baru-baru ini atau dalam 3 bulan)', 'skor' => [0, 25]],
                ['col' => 'penilaian_jatuhmorse_skala2', 'skor_col' => 'penilaian_jatuhmorse_nilai2', 'label' => 'Diagnosis sekunder (≥ 2 diagnosis medis)', 'skor' => [0, 15]],
                ['col' => 'penilaian_jatuhmorse_skala3', 'skor_col' => 'penilaian_jatuhmorse_nilai3', 'label' => 'Alat bantu jalan', 'skor' => [0, 15, 30]],
                ['col' => 'penilaian_jatuhmorse_skala4', 'skor_col' => 'penilaian_jatuhmorse_nilai4', 'label' => 'Terpasang infus / terapi IV', 'skor' => [0, 20]],
                ['col' => 'penilaian_jatuhmorse_skala5', 'skor_col' => 'penilaian_jatuhmorse_nilai5', 'label' => 'Gaya berjalan / cara berpindah', 'skor' => [0, 10, 20]],
                ['col' => 'penilaian_jatuhmorse_skala6', 'skor_col' => 'penilaian_jatuhmorse_nilai6', 'label' => 'Status mental', 'skor' => [0, 15]],
            ],
            'kategori' => fn (int $tot) => $tot < 25
                ? ['Risiko Rendah', 'Intervensi pencegahan risiko jatuh standar']
                : ($tot < 45
                    ? ['Risiko Sedang', 'Intervensi pencegahan risiko jatuh standar']
                    : ['Risiko Tinggi', 'Intervensi pencegahan risiko jatuh standar dan Intervensi risiko jatuh tinggi']),
        ];
    }

    /* ============ RISIKO JATUH ANAK — Humpty Dumpty ============ */
    private static function jatuhAnak(): array
    {
        $t = 'penilaian_lanjutan_resiko_jatuh_anak';
        return [
            'judul' => 'Risiko Jatuh Anak — Humpty Dumpty',
            'icon'  => 'fas fa-child',
            'tabel' => $t,
            'total_col' => 'penilaian_humptydumpty_totalnilai',
            'hasil_col' => 'hasil_skrining',
            'saran_col' => 'saran',
            'baris' => [
                ['col' => 'penilaian_humptydumpty_skala1', 'skor_col' => 'penilaian_humptydumpty_nilai1', 'label' => 'Usia', 'skor' => [4, 3, 2, 1]],
                ['col' => 'penilaian_humptydumpty_skala2', 'skor_col' => 'penilaian_humptydumpty_nilai2', 'label' => 'Jenis kelamin', 'skor' => [2, 1]],
                ['col' => 'penilaian_humptydumpty_skala3', 'skor_col' => 'penilaian_humptydumpty_nilai3', 'label' => 'Diagnosis', 'skor' => [4, 3, 2, 1]],
                ['col' => 'penilaian_humptydumpty_skala4', 'skor_col' => 'penilaian_humptydumpty_nilai4', 'label' => 'Gangguan kognitif', 'skor' => [3, 2, 1]],
                ['col' => 'penilaian_humptydumpty_skala5', 'skor_col' => 'penilaian_humptydumpty_nilai5', 'label' => 'Faktor lingkungan', 'skor' => [4, 3, 2, 1]],
                ['col' => 'penilaian_humptydumpty_skala6', 'skor_col' => 'penilaian_humptydumpty_nilai6', 'label' => 'Respon terhadap pembedahan/sedasi/anestesi', 'skor' => [3, 2, 1]],
                ['col' => 'penilaian_humptydumpty_skala7', 'skor_col' => 'penilaian_humptydumpty_nilai7', 'label' => 'Penggunaan obat', 'skor' => [3, 2, 1]],
            ],
            'kategori' => fn (int $tot) => $tot >= 12
                ? ['Risiko Tinggi', 'Intervensi risiko jatuh tinggi (pasang gelang kuning)']
                : ['Risiko Rendah', 'Intervensi pencegahan risiko jatuh standar'],
        ];
    }

    /* ============ RISIKO JATUH GERIATRI (11 item) ============ */
    private static function jatuhGeriatri(): array
    {
        $t = 'penilaian_lanjutan_resiko_jatuh_geriatri';
        // skor Ya per item (Tidak selalu 0)
        $ya = [4, 3, 3, 3, 2, 2, 2, 2, 1, 1, 1];
        $label = [
            'Gangguan gaya berjalan (diseret, menghentak, berayun)',
            'Pusing/pingsan pada posisi tegak',
            'Kebingungan setiap saat',
            'Nokturia/inkontinen',
            'Kebingungan intermiten',
            'Kelemahan umum',
            'Obat-obatan berisiko tinggi (diuretik, narkotik, sedatif, antipsikotik, laksatif, vasodilator, antiaritmia, antihipertensi, antidiabetik, antidepresan, neuroleptik, NSAID)',
            'Riwayat jatuh dalam 12 bulan terakhir',
            'Osteoporosis',
            'Gangguan pendengaran atau penglihatan',
            'Usia 70 tahun ke atas',
        ];
        $baris = [];
        for ($i = 1; $i <= 11; $i++) {
            $baris[] = [
                'col' => "penilaian_jatuh_skala$i",
                'skor_col' => "penilaian_jatuh_nilai$i",
                'label' => $label[$i - 1],
                'skor' => [0, $ya[$i - 1]], // enum('Tidak','Ya')
            ];
        }
        return [
            'judul' => 'Risiko Jatuh Geriatri',
            'icon'  => 'fas fa-person-cane',
            'tabel' => $t,
            'total_col' => 'penilaian_jatuh_totalnilai',
            'hasil_col' => 'hasil_skrining',
            'saran_col' => 'saran',
            'baris' => $baris,
            'kategori' => fn (int $tot) => $tot >= 4
                ? ['Risiko Tinggi', 'Intervensi pencegahan risiko jatuh standar dan Intervensi risiko jatuh tinggi']
                : ['Risiko Rendah', 'Intervensi pencegahan risiko jatuh standar'],
        ];
    }

    /* ============ RISIKO DEKUBITUS — Skala Norton ============ */
    private static function dekubitus(): array
    {
        $t = 'penilaian_risiko_dekubitus';
        $s = [4, 3, 2, 1];
        return [
            'judul' => 'Risiko Dekubitus — Skala Norton',
            'icon'  => 'fas fa-bed',
            'tabel' => $t,
            'total_col' => 'totalnilai',
            'hasil_col' => 'kategorinilai', // enum('Risiko Rendah','Risiko Sedang','Risiko Tinggi')
            'saran_col' => null,
            'baris' => [
                ['col' => 'kondisi_fisik',  'skor_col' => 'kondisi_fisik_nilai',  'label' => 'Kondisi fisik',       'skor' => $s],
                ['col' => 'status_mental',  'skor_col' => 'status_mental_nilai',  'label' => 'Status mental',       'skor' => $s],
                ['col' => 'aktifitas',      'skor_col' => 'aktifitas_nilai',      'label' => 'Aktivitas',           'skor' => $s],
                ['col' => 'mobilitas',      'skor_col' => 'mobilitas_nilai',      'label' => 'Mobilitas',           'skor' => $s],
                ['col' => 'inkontinensia',  'skor_col' => 'inkontinensia_nilai',  'label' => 'Inkontinensia',       'skor' => $s],
            ],
            'kategori' => fn (int $tot) => $tot < 12
                ? ['Risiko Tinggi', null]
                : ($tot < 16 ? ['Risiko Sedang', null] : ['Risiko Rendah', null]),
        ];
    }

    /* ============ PENILAIAN NYERI (PQRST) — tanpa skor ============ */
    private static function nyeri(): array
    {
        $t = 'penilaian_ulang_nyeri';
        return [
            'judul' => 'Penilaian Nyeri (PQRST)',
            'icon'  => 'fas fa-hand-dots',
            'tabel' => $t,
            'total_col' => null,
            'hasil_col' => null,
            'saran_col' => null,
            'baris' => [
                ['col' => 'nyeri',       'tipe' => 'pilih', 'label' => 'Tipe nyeri'],
                ['col' => 'provokes',    'tipe' => 'pilih', 'label' => 'Provokes (pemicu)'],
                ['col' => 'ket_provokes', 'tipe' => 'teks', 'label' => 'Ket. pemicu'],
                ['col' => 'quality',     'tipe' => 'pilih', 'label' => 'Quality (kualitas)'],
                ['col' => 'ket_quality', 'tipe' => 'teks', 'label' => 'Ket. kualitas'],
                ['col' => 'lokasi',      'tipe' => 'teks', 'label' => 'Region (lokasi)'],
                ['col' => 'menyebar',    'tipe' => 'pilih', 'label' => 'Menyebar?'],
                ['col' => 'skala_nyeri', 'tipe' => 'pilih', 'label' => 'Severity (skala 0–10)'],
                ['col' => 'durasi',      'tipe' => 'teks', 'label' => 'Time (durasi)'],
                ['col' => 'nyeri_hilang', 'tipe' => 'pilih', 'label' => 'Nyeri berkurang bila'],
                ['col' => 'ket_nyeri',   'tipe' => 'teks', 'label' => 'Keterangan'],
            ],
            'kategori' => null,
        ];
    }

    /* ============ SKRINING GIZI — MST ============ */
    private static function gizi(): array
    {
        $t = 'skrining_gizi';
        return [
            'judul' => 'Skrining Gizi — MST',
            'icon'  => 'fas fa-utensils',
            'tabel' => $t,
            'total_col' => 'skor_total',
            'hasil_col' => 'parameter_total',
            'saran_col' => null,
            'baris' => [
                ['col' => 'skrining_bb', 'tipe' => 'teks', 'label' => 'Berat badan', 'suffix' => 'kg'],
                ['col' => 'skrining_tb', 'tipe' => 'teks', 'label' => 'Tinggi badan', 'suffix' => 'cm'],
                ['col' => 'alergi',      'tipe' => 'teks', 'label' => 'Alergi'],
                ['col' => 'parameter_imt',     'skor_col' => 'skor_imt',     'label' => '1. Skor IMT / z-score', 'skor' => [0, 1, 2]],
                ['col' => 'parameter_bb',      'skor_col' => 'skor_bb',      'label' => '2. Penurunan BB tidak direncanakan (3–6 bln)', 'skor' => [0, 1, 2]],
                ['col' => 'parameter_penyakit', 'skor_col' => 'skor_penyakit', 'label' => '3. Efek penyakit akut terhadap asupan', 'skor' => [0, 2]],
            ],
            'kategori' => fn (int $tot) => $tot <= 0
                ? ['Beresiko rendah, ulangi 7 hari', null]
                : ($tot == 1
                    ? ['Beresiko menengah, monitoring asupan selama 3 hari', null]
                    : ['Beresiko tinggi, bekerja sama dengan tim dukungan gizi upayakan peningkatan asupan gizi dan memberikan makanan sesuai dengan daya terima', null]),
        ];
    }
}
