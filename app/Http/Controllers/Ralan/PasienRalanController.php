<?php

namespace App\Http\Controllers\Ralan;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Request;

class PasienRalanController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('loginauth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $kd_poli = session()->get('kd_poli');
        $kd_dokter = session()->get('username');
        $tglAwal = Request::get('tgl_awal') ?: date('Y-m-d');
        $tglAkhir = Request::get('tgl_akhir') ?: date('Y-m-d');
        // jaga-jaga kalau user membalik urutan tanggal
        if ($tglAwal > $tglAkhir) {
            [$tglAwal, $tglAkhir] = [$tglAkhir, $tglAwal];
        }
        // true kalau user memang sedang mencari tanggal tertentu (bukan default hari ini)
        $cariTanggal = Request::filled('tgl_awal') || Request::filled('tgl_akhir');
        $heads = ['No. Reg', 'Nama Pasien', 'No Rawat', 'Telp', 'Dokter', 'Status'];
        $headsInternal = ['No. Reg', 'No. RM', 'Nama Pasien', 'Dokter', 'Status'];
        $data = DB::table('reg_periksa')
                    ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                    ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
                    ->leftJoin('resume_pasien', 'reg_periksa.no_rawat', '=', 'resume_pasien.no_rawat')
                    ->where('reg_periksa.kd_poli', $kd_poli)
                    ->whereBetween('tgl_registrasi', [$tglAwal, $tglAkhir])
                    ->where('reg_periksa.kd_dokter', $kd_dokter)
                    ->orderBy('reg_periksa.tgl_registrasi', 'desc')
                    ->orderBy('reg_periksa.jam_reg', 'desc')
                    ->select('reg_periksa.no_reg', 'pasien.nm_pasien', 'reg_periksa.no_rawat', 'pasien.no_tlp', 'dokter.nm_dokter', 'reg_periksa.stts', 'pasien.no_rkm_medis', 'resume_pasien.diagnosa_utama')
                    ->get();


        return view('ralan.pasien-ralan', [
            'nm_poli' => $this->getPoliklinik($kd_poli),
            'kd_poli' => $kd_poli,
            'heads' => $heads,
            'data' => $data,
            'tglAwal' => $tglAwal,
            'tglAkhir' => $tglAkhir,
            'cariTanggal' => $cariTanggal,
            'headsInternal' => $headsInternal,
            'dataInternal' => $this->getRujukInternal($tglAwal, $tglAkhir)
        ]);
    }

    private function getPoliklinik($kd_poli)
    {
        $poli = DB::table('poliklinik')->where('kd_poli', $kd_poli)->first();
        return $poli->nm_poli;
    }

    private function getRujukInternal($tglAwal, $tglAkhir = null)
    {
        $tglAkhir = $tglAkhir ?: $tglAwal;
        return DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('rujukan_internal_poli', 'reg_periksa.no_rawat', '=', 'rujukan_internal_poli.no_rawat')
            ->join('dokter', 'dokter.kd_dokter', '=', 'rujukan_internal_poli.kd_dokter')
            ->where('rujukan_internal_poli.kd_poli', session()->get('kd_poli'))
            ->whereBetween('reg_periksa.tgl_registrasi', [$tglAwal, $tglAkhir])
            ->select('reg_periksa.no_reg', 'reg_periksa.no_rkm_medis', 'reg_periksa.no_rawat', 'pasien.nm_pasien', 'dokter.nm_dokter', 'reg_periksa.stts')
            ->get();
    }

    public static function encryptData($data)
    {
        $data = Crypt::encrypt($data);
        return $data;
    }
}
