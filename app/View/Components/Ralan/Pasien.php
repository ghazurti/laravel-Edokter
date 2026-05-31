<?php

namespace App\View\Components\ralan;

use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;

class Pasien extends Component
{
    public $data;
    public $alergi;
    public $diagnosaTerakhir;
    public $kunjunganTerakhir;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($noRawat)
    {
        $this->data = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->leftJoin('catatan_pasien', 'reg_periksa.no_rkm_medis', '=', 'catatan_pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->leftJoin('personal_pasien', 'pasien.no_rkm_medis', '=', 'personal_pasien.no_rkm_medis')
            ->where('reg_periksa.no_rawat', $noRawat)
            ->select(
                'pasien.*',
                'penjab.png_jawab',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'reg_periksa.status_lanjut',
                'reg_periksa.kd_pj',
                'dokter.nm_dokter',
                'poliklinik.nm_poli',
                'reg_periksa.kd_poli',
                'catatan_pasien.catatan',
                'personal_pasien.gambar',
            )
            ->first();

        $rm = $this->data->no_rkm_medis ?? null;
        if ($rm) {
            $this->alergi           = $this->cariAlergi($rm);
            $this->diagnosaTerakhir = $this->cariDiagnosa($rm);
            $this->kunjunganTerakhir = $this->cariKunjungan($rm, $noRawat);
        } else {
            $this->diagnosaTerakhir = collect();
        }
    }

    /** Alergi terakhir yang tercatat (pemeriksaan ralan/ranap). */
    private function cariAlergi($rm)
    {
        foreach (['pemeriksaan_ralan', 'pemeriksaan_ranap'] as $tbl) {
            try {
                $rows = DB::table($tbl)
                    ->join('reg_periksa', "$tbl.no_rawat", '=', 'reg_periksa.no_rawat')
                    ->where('reg_periksa.no_rkm_medis', $rm)
                    ->orderByDesc("$tbl.tgl_perawatan")
                    ->orderByDesc("$tbl.jam_rawat")
                    ->limit(20)
                    ->pluck("$tbl.alergi");
                foreach ($rows as $a) {
                    $a = trim((string) $a);
                    if ($a !== '' && !in_array(strtolower($a), ['-', 'tidak ada', 'tidak', 'tidak ada alergi', 'tak ada'])) {
                        return $a;
                    }
                }
            } catch (\Throwable $e) {
            }
        }
        return null;
    }

    /** 3 diagnosa terakhir lintas kunjungan. */
    private function cariDiagnosa($rm)
    {
        try {
            return DB::table('diagnosa_pasien')
                ->join('reg_periksa', 'diagnosa_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
                ->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
                ->where('reg_periksa.no_rkm_medis', $rm)
                ->orderByDesc('reg_periksa.tgl_registrasi')
                ->limit(3)
                ->get(['penyakit.kd_penyakit', 'penyakit.nm_penyakit', 'reg_periksa.tgl_registrasi']);
        } catch (\Throwable $e) {
            return collect();
        }
    }

    /** Kunjungan sebelumnya (selain no_rawat ini). */
    private function cariKunjungan($rm, $noRawat)
    {
        try {
            return DB::table('reg_periksa')
                ->leftJoin('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                ->where('reg_periksa.no_rkm_medis', $rm)
                ->where('reg_periksa.no_rawat', '<>', $noRawat)
                ->orderByDesc('reg_periksa.tgl_registrasi')
                ->first(['reg_periksa.tgl_registrasi', 'poliklinik.nm_poli']);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ralan.pasien')
            ->with('data', $this->data)
            ->with('dokter', session()->get('username'))
            ->with('alergi', $this->alergi)
            ->with('diagnosaTerakhir', $this->diagnosaTerakhir)
            ->with('kunjunganTerakhir', $this->kunjunganTerakhir);
    }
}
