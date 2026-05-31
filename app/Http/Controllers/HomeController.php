<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Session;

class HomeController extends Controller
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
        $hariIni = date('Y-m-d');
        $bulanIni = date('Y-m');

        // Metrik relevan dokter
        $antrianBelum = DB::table('reg_periksa')->where('tgl_registrasi', $hariIni)
            ->where('kd_poli', $kd_poli)->where('stts', 'Belum')->count();
        $sudahDilayani = DB::table('reg_periksa')->where('tgl_registrasi', $hariIni)
            ->where('kd_poli', $kd_poli)->where('stts', 'Sudah')->count();
        $pasienPoliHariIni = DB::table('reg_periksa')->where('tgl_registrasi', $hariIni)
            ->where('kd_poli', $kd_poli)->count();
        $pasienPoliBulanIni = DB::table('reg_periksa')->where('tgl_registrasi', 'like', $bulanIni.'%')
            ->where('kd_poli', $kd_poli)->where('stts', '<>', 'Belum')->count();
        $totalPasienSaya = DB::table('reg_periksa')->where('kd_dokter', $kd_dokter)
            ->distinct()->count('no_rkm_medis');
        $pasienAktif = DB::table('reg_periksa')
                            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                            ->where('kd_dokter', $kd_dokter)
                            ->groupBy('no_rkm_medis')
                            ->orderBy('jumlah', 'desc')
                            ->selectRaw("reg_periksa.no_rkm_medis, pasien.nm_pasien, count(reg_periksa.no_rkm_medis) jumlah")
                            ->limit(10)->get();
        $antrianHariIni = DB::table('reg_periksa')
                            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                            ->where('reg_periksa.kd_poli', $kd_poli)
                            ->where('tgl_registrasi', $hariIni)
                            ->orderBy('reg_periksa.no_reg', 'asc')
                            ->select('reg_periksa.no_rawat', 'reg_periksa.no_rkm_medis', 'reg_periksa.no_reg',
                                     'pasien.nm_pasien', 'reg_periksa.jam_reg', 'reg_periksa.stts')
                            ->limit(15)
                            ->get();
        $headPasienAktif = ['No Rekam Medis', 'Nama Pasien', 'Kunjungan'];
        return view('home',[
            'antrianBelum' => $antrianBelum,
            'sudahDilayani' => $sudahDilayani,
            'pasienPoliBulanIni' => $pasienPoliBulanIni,
            'pasienPoliHariIni' => $pasienPoliHariIni,
            'totalPasienSaya' => $totalPasienSaya,
            'pasienAktif' => array_values($pasienAktif->toArray()),
            'headPasienAktif' => $headPasienAktif,
            'antrianHariIni' => $antrianHariIni,
            'poliklinik' => $this->getPoliklinik($kd_poli),
            'statistikKunjungan' => $this->statistikKunjungan($kd_dokter),
            'nm_dokter' => $this->getDokter($kd_dokter),
        ]);
    }

    private function getPoliklinik($kd_poli)
    {
        $poli = DB::table('poliklinik')->where('kd_poli', $kd_poli)->first();
        return $poli->nm_poli ?? '-';
    }

    private function getDokter($kd_dokter)
    {
        $dokter = DB::table('dokter')->where('kd_dokter', $kd_dokter)->first();
        return $dokter->nm_dokter ?? $kd_dokter;
    }
    
    public function statistikKunjungan($kd_dokter)
    {
        $data = DB::table('reg_periksa')
                    ->where('kd_dokter', $kd_dokter)
                    ->where('tgl_registrasi', 'like', date('Y').'-%')
                    ->selectRaw("MONTHNAME (tgl_registrasi) as bulan, COUNT(DISTINCT  no_rawat) as jumlah")
                    ->groupByRaw("MONTH(tgl_registrasi)")
                    ->get();
        return $data;
    }

    public function logout()
    {
        Session::flush();
        return redirect()->route('login');
    }
}
