<?php

namespace App\Http\Controllers\Igd;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Request;

class PasienIgdController extends Controller
{
    public function __construct()
    {
        $this->middleware('loginauth');
    }

    public function index()
    {
        $kd_dokter = session()->get('username');
        $tanggal = Request::get('tanggal') ?? date('Y-m-d');
        $heads = ['No. Reg', 'Nama Pasien', 'No Rawat', 'Telp', 'Dokter', 'Status'];

        $data = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->leftJoin('penilaian_medis_igd', 'reg_periksa.no_rawat', '=', 'penilaian_medis_igd.no_rawat')
            ->where('reg_periksa.kd_poli', 'IGDK')
            ->where('reg_periksa.tgl_registrasi', $tanggal)
            ->where('reg_periksa.kd_dokter', $kd_dokter)
            ->orderBy('reg_periksa.jam_reg', 'asc')
            ->select(
                'reg_periksa.no_reg',
                'pasien.nm_pasien',
                'reg_periksa.no_rawat',
                'pasien.no_tlp',
                'dokter.nm_dokter',
                'reg_periksa.stts',
                'pasien.no_rkm_medis',
                'penilaian_medis_igd.tanggal as sudah_periksa'
            )
            ->get();

        return view('igd.pasien-igd', [
            'heads' => $heads,
            'data' => $data,
            'tanggal' => $tanggal,
        ]);
    }

    public static function encryptData($data)
    {
        return Crypt::encrypt($data);
    }
}
