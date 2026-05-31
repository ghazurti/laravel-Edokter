<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

/**
 * CPPT Terintegrasi — gabungkan catatan semua profesi (dokter, perawat, gizi)
 * untuk satu no_rawat menjadi satu timeline kronologis.
 *
 * Murni baca data Khanza yang sudah ada. Tiap sumber dibungkus try/catch
 * supaya tidak crash kalau tabel tidak ada di versi Khanza tertentu.
 */
class Cppt
{
    public static function timeline($noRawat, $isRanap = false)
    {
        $entries = collect();
        $sufix   = $isRanap ? 'ranap' : 'ralan';

        $entries = $entries
            ->concat(self::dariPemeriksaan($noRawat, $sufix))
            ->concat(self::dariCatatanKeperawatan($noRawat, $sufix))
            ->concat(self::dariAsuhanGizi($noRawat));

        // Resolve penulis (nip -> nama + profesi) sekali jalan
        $nips = $entries->pluck('nip')->filter()->unique()->values()->all();
        $peta = self::petaPenulis($nips);

        foreach ($entries as $e) {
            $info = $peta[$e->nip] ?? ['nama' => $e->nip ?: '-', 'profesi' => $e->profesi_default, 'warna' => 'secondary'];
            // pemeriksaan_* bisa diisi dokter atau perawat; pakai hasil resolve
            $e->penulis = $info['nama'];
            $e->profesi = $e->profesi_fixed ?: $info['profesi'];
            $e->warna   = $e->warna_fixed ?: $info['warna'];
        }

        return $entries->sortByDesc('dt')->values();
    }

    private static function dariPemeriksaan($noRawat, $sufix)
    {
        try {
            $tbl = "pemeriksaan_$sufix";
            return DB::table($tbl)
                ->where('no_rawat', $noRawat)
                ->get()
                ->map(function ($r) {
                    return (object) [
                        'dt'             => trim(($r->tgl_perawatan ?? '') . ' ' . ($r->jam_rawat ?? '00:00:00')),
                        'tgl'            => $r->tgl_perawatan ?? null,
                        'jam'            => substr($r->jam_rawat ?? '', 0, 5),
                        'nip'            => $r->nip ?? null,
                        'tipe'           => 'soap',
                        'profesi_default' => 'Dokter/Perawat',
                        'profesi_fixed'  => null,
                        'warna_fixed'    => null,
                        'data'           => (object) [
                            'S' => $r->keluhan ?? '',
                            'O' => $r->pemeriksaan ?? '',
                            'A' => $r->penilaian ?? '',
                            'P' => $r->rtl ?? '',
                            'instruksi' => $r->instruksi ?? '',
                            'evaluasi'  => $r->evaluasi ?? '',
                            'vital' => [
                                'TD'  => $r->tensi ?? null,
                                'N'   => $r->nadi ?? null,
                                'S'   => $r->suhu_tubuh ?? null,
                                'RR'  => $r->respirasi ?? null,
                                'SpO2' => $r->spo2 ?? null,
                                'GCS' => $r->gcs ?? null,
                                'Kes' => $r->kesadaran ?? null,
                            ],
                            'alergi' => $r->alergi ?? '',
                        ],
                    ];
                });
        } catch (\Throwable $e) {
            return collect();
        }
    }

    private static function dariCatatanKeperawatan($noRawat, $sufix)
    {
        try {
            $tbl = "catatan_keperawatan_$sufix";
            return DB::table($tbl)
                ->where('no_rawat', $noRawat)
                ->get()
                ->map(function ($r) {
                    return (object) [
                        'dt'             => trim(($r->tanggal ?? '') . ' ' . ($r->jam ?? '00:00:00')),
                        'tgl'            => $r->tanggal ?? null,
                        'jam'            => substr($r->jam ?? '', 0, 5),
                        'nip'            => $r->nip ?? null,
                        'tipe'           => 'catatan',
                        'profesi_default' => 'Perawat',
                        'profesi_fixed'  => null,
                        'warna_fixed'    => null,
                        'data'           => (object) ['uraian' => $r->uraian ?? ''],
                    ];
                });
        } catch (\Throwable $e) {
            return collect();
        }
    }

    private static function dariAsuhanGizi($noRawat)
    {
        try {
            return DB::table('asuhan_gizi')
                ->where('no_rawat', $noRawat)
                ->get()
                ->map(function ($r) {
                    $antro = trim(sprintf(
                        'BB %s kg, TB %s cm, IMT %s',
                        $r->antropometri_bb ?? '-',
                        $r->antropometri_tb ?? '-',
                        $r->antropometri_imt ?? '-'
                    ));
                    return (object) [
                        'dt'             => trim(($r->tanggal ?? '') . ' 00:00:00'),
                        'tgl'            => $r->tanggal ?? null,
                        'jam'            => '',
                        'nip'            => $r->nip ?? null,
                        'tipe'           => 'adime',
                        'profesi_default' => 'Ahli Gizi',
                        'profesi_fixed'  => 'Ahli Gizi',
                        'warna_fixed'    => 'warning',
                        'data'           => (object) [
                            'A'  => trim($antro . ' | ' . ($r->biokimia ?? '') . ' | ' . ($r->fisik_klinis ?? ''), ' |'),
                            'D'  => $r->diagnosis ?? '',
                            'I'  => $r->intervensi_gizi ?? '',
                            'ME' => $r->monitoring_evaluasi ?? '',
                        ],
                    ];
                });
        } catch (\Throwable $e) {
            return collect();
        }
    }

    /**
     * Bangun peta nip => [nama, profesi, warna].
     * nip bisa kode dokter (di tabel dokter) atau nip pegawai (di tabel petugas).
     */
    private static function petaPenulis(array $nips)
    {
        $peta = [];
        if (empty($nips)) {
            return $peta;
        }

        try {
            $dokters = DB::table('dokter')->whereIn('kd_dokter', $nips)->pluck('nm_dokter', 'kd_dokter');
            foreach ($dokters as $kd => $nama) {
                $peta[$kd] = ['nama' => $nama, 'profesi' => 'Dokter', 'warna' => 'info'];
            }
        } catch (\Throwable $e) {
        }

        try {
            $petugas = DB::table('petugas')
                ->leftJoin('jabatan', 'petugas.kd_jbtn', '=', 'jabatan.kd_jbtn')
                ->whereIn('petugas.nip', $nips)
                ->select('petugas.nip', 'petugas.nama', 'jabatan.nm_jbtn')
                ->get();
            foreach ($petugas as $p) {
                if (isset($peta[$p->nip])) {
                    continue; // sudah ke-resolve sebagai dokter
                }
                // Profesi dari jabatan; kalau jabatan berbasis ruangan,
                // tebak dari gelar di nama (AMK, S.Kep, AMG, Apt, dst)
                [$profesi, $warna] = self::tebakProfesi($p->nm_jbtn, $p->nama);
                $peta[$p->nip] = [
                    'nama'    => $p->nama,
                    'profesi' => $profesi,
                    'warna'   => $warna,
                ];
            }
        } catch (\Throwable $e) {
        }

        return $peta;
    }

    /**
     * Tentukan profesi + warna dari jabatan, fallback ke gelar di nama.
     * @return array [profesi, warna]
     */
    private static function tebakProfesi($jabatan, $nama)
    {
        $j = strtolower((string) $jabatan);
        if (str_contains($j, 'dokter'))                          return ['Dokter', 'info'];
        if (str_contains($j, 'perawat'))                         return ['Perawat', 'success'];
        if (str_contains($j, 'bidan'))                           return ['Bidan', 'success'];
        if (str_contains($j, 'gizi') || str_contains($j, 'nutri')) return ['Ahli Gizi', 'warning'];
        if (str_contains($j, 'farmasi') || str_contains($j, 'apotek')) return ['Farmasi', 'purple'];

        // Fallback: gelar di nama
        $n = strtolower((string) $nama);
        if (preg_match('/\bdr\.?\b|\bdrg\.?\b/', $n))            return ['Dokter', 'info'];
        if (preg_match('/\bamk\b|s\.?\s?kep|\bns\b|amd\.?\s?kep/', $n)) return ['Perawat', 'success'];
        if (preg_match('/\bamd\.?\s?keb\b|str\.?\s?keb|s\.?\s?keb|\bbd\b/', $n)) return ['Bidan', 'success'];
        if (preg_match('/\bamg\b|s\.?\s?gz|amd\.?\s?gz|\bgz\b/', $n)) return ['Ahli Gizi', 'warning'];
        if (preg_match('/\bapt\.?\b|s\.?\s?farm|amd\.?\s?farm/', $n)) return ['Farmasi', 'purple'];

        // Terakhir: pakai jabatan apa adanya (mis. nama ruangan)
        return [$jabatan ?: 'Petugas', 'secondary'];
    }
}
