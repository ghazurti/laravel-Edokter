<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait DokterKonsul
{
    /**
     * Cek apakah dokter login adalah dokter konsul (penerima rujukan internal),
     * bukan DPJP utama yang terdaftar di reg_periksa.
     *
     * @param  string  $noRawat
     * @param  string|null  $kdDokter  default = session('username')
     * @return bool
     */
    public function isDokterKonsul(string $noRawat, ?string $kdDokter = null): bool
    {
        $kdDokter = $kdDokter ?: session('username');
        if (!$kdDokter) return false;

        $dpjpUtama = DB::table('reg_periksa')
            ->where('no_rawat', $noRawat)
            ->value('kd_dokter');

        if (!$dpjpUtama || $dpjpUtama === $kdDokter) {
            return false;
        }

        // Verifikasi: ada record rujukan ke dokter ini?
        return DB::table('rujukan_internal_poli')
            ->where('no_rawat', $noRawat)
            ->where('kd_dokter', $kdDokter)
            ->exists();
    }
}
