<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

/**
 * Helper riwayat pasien — semua method dibungkus try/catch supaya tidak
 * crash kalau Khanza versi tertentu tidak punya tabel/kolom tersebut.
 */
trait RiwayatPasien
{
    public static function riwayatResep($noRawat)
    {
        try {
            $headers = DB::table('resep_obat')
                ->join('dokter', 'resep_obat.kd_dokter', '=', 'dokter.kd_dokter')
                ->where('resep_obat.no_rawat', $noRawat)
                ->orderByDesc('resep_obat.tgl_peresepan')
                ->orderByDesc('resep_obat.jam_peresepan')
                ->select(
                    'resep_obat.no_resep',
                    'resep_obat.tgl_peresepan',
                    'resep_obat.jam_peresepan',
                    'resep_obat.status',
                    'dokter.nm_dokter'
                )
                ->get();

            foreach ($headers as $h) {
                $h->items = DB::table('resep_dokter')
                    ->join('databarang', 'resep_dokter.kode_brng', '=', 'databarang.kode_brng')
                    ->where('resep_dokter.no_resep', $h->no_resep)
                    ->select('databarang.nama_brng', 'databarang.kode_sat', 'resep_dokter.jml', 'resep_dokter.aturan_pakai')
                    ->get();
            }
            return $headers;
        } catch (\Throwable $e) {
            return collect();
        }
    }

    public static function riwayatOperasi($noRawat)
    {
        try {
            return DB::table('operasi')
                ->leftJoin('paket_operasi', 'operasi.kode_paket', '=', 'paket_operasi.kode_paket')
                ->where('operasi.no_rawat', $noRawat)
                ->orderByDesc('operasi.tgl_operasi')
                ->select(
                    'operasi.tgl_operasi',
                    'operasi.jenis_anasthesi',
                    'operasi.kategori',
                    'operasi.operator1',
                    'operasi.dokter_anestesi',
                    'paket_operasi.nm_perawatan'
                )
                ->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    public static function riwayatTindakanDokter($noRawat, $isRanap = false)
    {
        try {
            $table   = $isRanap ? 'rawat_inap_dr' : 'rawat_jl_dr';
            $jnsTbl  = $isRanap ? 'jns_perawatan_inap' : 'jns_perawatan';
            return DB::table($table)
                ->leftJoin($jnsTbl, "$jnsTbl.kd_jenis_prw", '=', "$table.kd_jenis_prw")
                ->leftJoin('dokter', "$table.kd_dokter", '=', 'dokter.kd_dokter')
                ->where("$table.no_rawat", $noRawat)
                ->orderByDesc("$table.tgl_perawatan")
                ->orderByDesc("$table.jam_rawat")
                ->select(
                    "$table.tgl_perawatan",
                    "$table.jam_rawat",
                    "$jnsTbl.nm_perawatan",
                    'dokter.nm_dokter',
                    "$table.biaya_rawat"
                )
                ->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    public static function riwayatTindakanPerawat($noRawat, $isRanap = false)
    {
        try {
            $table  = $isRanap ? 'rawat_inap_pr' : 'rawat_jl_pr';
            $jnsTbl = $isRanap ? 'jns_perawatan_inap' : 'jns_perawatan';
            return DB::table($table)
                ->leftJoin($jnsTbl, "$jnsTbl.kd_jenis_prw", '=', "$table.kd_jenis_prw")
                ->leftJoin('petugas', "$table.nip", '=', 'petugas.nip')
                ->where("$table.no_rawat", $noRawat)
                ->orderByDesc("$table.tgl_perawatan")
                ->orderByDesc("$table.jam_rawat")
                ->select(
                    "$table.tgl_perawatan",
                    "$table.jam_rawat",
                    "$jnsTbl.nm_perawatan",
                    'petugas.nama',
                    "$table.biaya_rawat"
                )
                ->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    public static function riwayatRujukanInternal($noRawat)
    {
        try {
            return DB::table('rujukan_internal_poli')
                ->leftJoin('dokter', 'rujukan_internal_poli.kd_dokter', '=', 'dokter.kd_dokter')
                ->leftJoin('poliklinik', 'rujukan_internal_poli.kd_poli', '=', 'poliklinik.kd_poli')
                ->where('rujukan_internal_poli.no_rawat', $noRawat)
                ->select(
                    'dokter.nm_dokter',
                    'poliklinik.nm_poli',
                    'rujukan_internal_poli.kd_dokter',
                    'rujukan_internal_poli.kd_poli'
                )
                ->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }

    public static function riwayatResepPulang($noRawat)
    {
        try {
            $headers = DB::table('permintaan_resep_pulang')
                ->leftJoin('dokter', 'permintaan_resep_pulang.kd_dokter', '=', 'dokter.kd_dokter')
                ->where('permintaan_resep_pulang.no_rawat', $noRawat)
                ->orderByDesc('permintaan_resep_pulang.tgl_permintaan')
                ->orderByDesc('permintaan_resep_pulang.jam')
                ->select(
                    'permintaan_resep_pulang.no_permintaan',
                    'permintaan_resep_pulang.tgl_permintaan',
                    'permintaan_resep_pulang.jam',
                    'permintaan_resep_pulang.status',
                    'dokter.nm_dokter'
                )
                ->get();

            foreach ($headers as $h) {
                $h->items = DB::table('detail_permintaan_resep_pulang')
                    ->join('databarang', 'detail_permintaan_resep_pulang.kode_brng', '=', 'databarang.kode_brng')
                    ->where('detail_permintaan_resep_pulang.no_permintaan', $h->no_permintaan)
                    ->select('databarang.nama_brng', 'databarang.kode_sat', 'detail_permintaan_resep_pulang.jml', 'detail_permintaan_resep_pulang.dosis')
                    ->get();
            }
            return $headers;
        } catch (\Throwable $e) {
            return collect();
        }
    }

    public static function riwayatBerkasDigital($noRawat)
    {
        try {
            return DB::table('berkas_digital_perawatan')
                ->where('no_rawat', $noRawat)
                ->select('kode', 'lokasi_file')
                ->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }
}
