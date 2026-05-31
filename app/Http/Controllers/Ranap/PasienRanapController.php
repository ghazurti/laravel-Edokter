<?php

namespace App\Http\Controllers\Ranap;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PasienRanapController extends Controller
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
        $kd_dokter = session()->get('username');
        $heads = ['Nama', 'No. RM', 'Kamar', 'Bed', 'Tanggal Masuk', 'Cara Bayar'];

        // Filter rentang tanggal masuk (opsional). Tanpa filter = semua pasien aktif.
        $tglAwal = request('tgl_awal');
        $tglAkhir = request('tgl_akhir');
        $cariTanggal = filled($tglAwal) || filled($tglAkhir);
        if ($cariTanggal) {
            $tglAwal = $tglAwal ?: $tglAkhir;
            $tglAkhir = $tglAkhir ?: $tglAwal;
            if ($tglAwal > $tglAkhir) {
                [$tglAwal, $tglAkhir] = [$tglAkhir, $tglAwal];
            }
        }

        $semuaDpjp = in_array($kd_dokter, ['86062112', 'SP0000005', 'SP0000002']);

        $query = DB::table('kamar_inap')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'kamar_inap.no_rawat')
            ->join('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
            ->join('bangsal', 'bangsal.kd_bangsal', '=', 'kamar.kd_bangsal')
            ->join('penjab', 'penjab.kd_pj', '=', 'reg_periksa.kd_pj')
            ->where('kamar_inap.stts_pulang', '-');

        if (!$semuaDpjp) {
            $query->join('dpjp_ranap', 'dpjp_ranap.no_rawat', '=', 'reg_periksa.no_rawat')
                ->where('dpjp_ranap.kd_dokter', $kd_dokter);
        }

        if ($cariTanggal) {
            $query->whereBetween(DB::raw('DATE(kamar_inap.tgl_masuk)'), [$tglAwal, $tglAkhir]);
        }

        $data = $query
            ->orderByDesc('kamar_inap.tgl_masuk')
            ->select('pasien.nm_pasien', 'reg_periksa.no_rkm_medis', 'bangsal.nm_bangsal', 'kamar_inap.kd_kamar', 'kamar_inap.tgl_masuk', 'penjab.png_jawab', 'reg_periksa.no_rawat', 'bangsal.kd_bangsal')
            ->get();

        return view('ranap.pasien-ranap', [
            'heads' => $heads,
            'data' => $data,
            'tglAwal' => $tglAwal,
            'tglAkhir' => $tglAkhir,
            'cariTanggal' => $cariTanggal,
        ]);
    }

    private function getPoliklinik($kd_poli)
    {
        $poli = DB::table('poliklinik')->where('kd_poli', $kd_poli)->first();
        return $poli->nm_poli;
    }

    public static function encryptData($data)
    {
        $data = Crypt::encrypt($data);
        return $data;
    }
}
