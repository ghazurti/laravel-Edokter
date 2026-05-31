<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CetakController extends Controller
{
    public function __construct()
    {
        $this->middleware('loginauth');
    }

    /**
     * Header pasien + registrasi yang dipakai semua dokumen.
     */
    private function infoPasien($noRawat)
    {
        return DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->leftJoin('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->where('reg_periksa.no_rawat', $noRawat)
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.umurdaftar',
                'reg_periksa.sttsumur',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.jk',
                'pasien.tgl_lahir',
                'pasien.alamat',
                'pasien.no_ktp',
                'pasien.pekerjaan',
                'dokter.nm_dokter',
                'poliklinik.nm_poli'
            )
            ->first();
    }

    private function diagnosaList($noRawat)
    {
        return DB::table('diagnosa_pasien')
            ->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
            ->where('diagnosa_pasien.no_rawat', $noRawat)
            ->select('penyakit.kd_penyakit', 'penyakit.nm_penyakit')
            ->get();
    }

    public function resep(Request $request, $noRawat)
    {
        $pasien = $this->infoPasien($noRawat);
        abort_unless($pasien, 404, 'Data pasien tidak ditemukan');

        // resep terakhir untuk no_rawat ini (atau spesifik no_resep via query string)
        $noResep = $request->query('no_resep');
        $resepHeaderQ = DB::table('resep_obat')->where('no_rawat', $noRawat);
        if ($noResep) {
            $resepHeaderQ->where('no_resep', $noResep);
        }
        $header = $resepHeaderQ->orderByDesc('tgl_peresepan')->orderByDesc('jam_peresepan')->first();

        $items = collect();
        $racikan = collect();
        if ($header) {
            $items = DB::table('resep_dokter')
                ->join('databarang', 'resep_dokter.kode_brng', '=', 'databarang.kode_brng')
                ->where('resep_dokter.no_resep', $header->no_resep)
                ->select('databarang.nama_brng', 'databarang.kode_sat', 'resep_dokter.jml', 'resep_dokter.aturan_pakai')
                ->get();

            $racikan = DB::table('resep_dokter_racikan')
                ->leftJoin('metode_racik', 'resep_dokter_racikan.kd_racik', '=', 'metode_racik.kd_racik')
                ->where('resep_dokter_racikan.no_resep', $header->no_resep)
                ->select('resep_dokter_racikan.no_racik', 'resep_dokter_racikan.nama_racik', 'resep_dokter_racikan.jml_dr', 'resep_dokter_racikan.aturan_pakai', 'metode_racik.nm_racik')
                ->get();

            foreach ($racikan as $r) {
                $r->detail = DB::table('resep_dokter_racikan_detail')
                    ->join('databarang', 'resep_dokter_racikan_detail.kode_brng', '=', 'databarang.kode_brng')
                    ->where('resep_dokter_racikan_detail.no_resep', $header->no_resep)
                    ->where('resep_dokter_racikan_detail.no_racik', $r->no_racik)
                    ->select('databarang.nama_brng', 'resep_dokter_racikan_detail.jml')
                    ->get();
            }
        }

        $pdf = Pdf::loadView('cetak.resep', compact('pasien', 'header', 'items', 'racikan'))
            ->setPaper('a5', 'portrait');

        return $pdf->stream('resep-' . $noRawat . '.pdf');
    }

    public function resumeMedis($noRawat)
    {
        $pasien = $this->infoPasien($noRawat);
        abort_unless($pasien, 404, 'Data pasien tidak ditemukan');

        $resume = DB::table('resume_pasien')->where('no_rawat', $noRawat)->first();
        $diagnosa = $this->diagnosaList($noRawat);

        $pdf = Pdf::loadView('cetak.resume-medis', compact('pasien', 'resume', 'diagnosa'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('resume-medis-' . $noRawat . '.pdf');
    }

    public function suratSakit(Request $request, $noRawat)
    {
        $pasien = $this->infoPasien($noRawat);
        abort_unless($pasien, 404, 'Data pasien tidak ditemukan');

        $lama   = (int) $request->query('lama', 1);
        $mulai  = $request->query('mulai', date('Y-m-d'));
        $diagnosa = $this->diagnosaList($noRawat);

        $mulaiC = \Carbon\Carbon::parse($mulai);
        $selesaiC = $mulaiC->copy()->addDays(max(0, $lama - 1));

        $pdf = Pdf::loadView('cetak.surat-sakit', [
            'pasien'   => $pasien,
            'diagnosa' => $diagnosa,
            'lama'     => $lama,
            'mulai'    => $mulaiC,
            'selesai'  => $selesaiC,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('surat-sakit-' . $noRawat . '.pdf');
    }

    public function suratRujukan(Request $request, $noRawat)
    {
        $pasien = $this->infoPasien($noRawat);
        abort_unless($pasien, 404, 'Data pasien tidak ditemukan');

        $diagnosa  = $this->diagnosaList($noRawat);
        $tujuanRs  = $request->query('tujuan_rs', '');
        $tujuanPoli = $request->query('tujuan_poli', '');
        $alasan    = $request->query('alasan', '');

        $pdf = Pdf::loadView('cetak.surat-rujukan', [
            'pasien'     => $pasien,
            'diagnosa'   => $diagnosa,
            'tujuanRs'   => $tujuanRs,
            'tujuanPoli' => $tujuanPoli,
            'alasan'     => $alasan,
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('surat-rujukan-' . $noRawat . '.pdf');
    }

    public function suratKontrol(Request $request, $noRawat)
    {
        $pasien = $this->infoPasien($noRawat);
        abort_unless($pasien, 404, 'Data pasien tidak ditemukan');

        $tglKontrol = $request->query('tgl_kontrol', date('Y-m-d'));
        $poliTujuan = $request->query('poli', $pasien->nm_poli);
        $catatan    = $request->query('catatan', '');

        $pdf = Pdf::loadView('cetak.surat-kontrol', [
            'pasien'     => $pasien,
            'tglKontrol' => \Carbon\Carbon::parse($tglKontrol),
            'poliTujuan' => $poliTujuan,
            'catatan'    => $catatan,
        ])->setPaper('a5', 'portrait');

        return $pdf->stream('surat-kontrol-' . $noRawat . '.pdf');
    }
}
