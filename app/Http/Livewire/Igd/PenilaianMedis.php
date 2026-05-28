<?php

namespace App\Http\Livewire\Igd;

use App\Traits\SwalResponse;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class PenilaianMedis extends Component
{
    use SwalResponse, LivewireAlert;

    public $noRawat;

    // Anamnesis
    public $anamnesis = 'Autoanamnesis', $hubungan;
    public $keluhan_utama, $rps, $rpd, $rpk, $rpo, $alergi;
    public $keadaan = 'Sakit Ringan';

    // Vital sign
    public $gcs, $kesadaran = 'Compos Mentis';
    public $td, $nadi, $rr, $suhu, $spo, $bb, $tb;

    // Pemeriksaan fisik
    public $kepala = 'Normal', $mata = 'Normal', $gigi = 'Normal';
    public $leher = 'Normal', $thoraks = 'Normal', $abdomen = 'Normal';
    public $genital = 'Tidak Diperiksa', $ekstremitas = 'Normal';
    public $ket_fisik, $ket_lokalis;

    // Penunjang & tatalaksana
    public $ekg, $rad, $lab;
    public $diagnosis, $tata;

    public function mount($noRawat)
    {
        $this->noRawat = $noRawat;
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.igd.penilaian-medis', [
            'listPenilaian' => $this->getListPenilaian(),
        ]);
    }

    private function getListPenilaian()
    {
        $rkm = DB::table('reg_periksa')->where('no_rawat', $this->noRawat)->value('no_rkm_medis');
        if (!$rkm) return collect();

        return DB::table('penilaian_medis_igd')
            ->join('reg_periksa', 'penilaian_medis_igd.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('dokter', 'penilaian_medis_igd.kd_dokter', '=', 'dokter.kd_dokter')
            ->where('reg_periksa.no_rkm_medis', $rkm)
            ->orderBy('penilaian_medis_igd.tanggal', 'desc')
            ->select('penilaian_medis_igd.*', 'dokter.nm_dokter')
            ->get();
    }

    public function loadData()
    {
        $latest = $this->getListPenilaian()->first();
        if ($latest) {
            $this->anamnesis       = $latest->anamnesis;
            $this->hubungan        = $latest->hubungan;
            $this->keluhan_utama   = $latest->keluhan_utama;
            $this->rps             = $latest->rps;
            $this->rpd             = $latest->rpd;
            $this->rpk             = $latest->rpk;
            $this->rpo             = $latest->rpo;
            $this->alergi          = $latest->alergi;
            $this->keadaan         = $latest->keadaan;
            $this->gcs             = $latest->gcs;
            $this->kesadaran       = $latest->kesadaran;
            $this->td              = $latest->td;
            $this->nadi            = $latest->nadi;
            $this->rr              = $latest->rr;
            $this->suhu            = $latest->suhu;
            $this->spo             = $latest->spo;
            $this->bb              = $latest->bb;
            $this->tb              = $latest->tb;
            $this->kepala          = $latest->kepala;
            $this->mata            = $latest->mata;
            $this->gigi            = $latest->gigi;
            $this->leher           = $latest->leher;
            $this->thoraks         = $latest->thoraks;
            $this->abdomen         = $latest->abdomen;
            $this->genital         = $latest->genital;
            $this->ekstremitas     = $latest->ekstremitas;
            $this->ket_fisik       = $latest->ket_fisik;
            $this->ket_lokalis     = $latest->ket_lokalis;
            $this->ekg             = $latest->ekg;
            $this->rad             = $latest->rad;
            $this->lab             = $latest->lab;
            $this->diagnosis       = $latest->diagnosis;
            $this->tata            = $latest->tata;
        }
    }

    public function ambilDariTriase()
    {
        $triase = DB::table('data_triase_igd')->where('no_rawat', $this->noRawat)->first();
        $primer = DB::table('data_triase_igdprimer')->where('no_rawat', $this->noRawat)->first();

        if (!$triase && !$primer) {
            $this->alert('warning', 'Belum ada data triase untuk pasien ini', [
                'position' => 'top-end', 'timer' => 2500, 'toast' => true,
            ]);
            return;
        }

        if ($triase) {
            $this->td   = $triase->tekanan_darah ?: $this->td;
            $this->nadi = $triase->nadi          ?: $this->nadi;
            $this->rr   = $triase->pernapasan    ?: $this->rr;
            $this->suhu = $triase->suhu          ?: $this->suhu;
            $this->spo  = $triase->saturasi_o2   ?: $this->spo;
        }

        if ($primer && !empty($primer->keluhan_utama)) {
            $this->keluhan_utama = $primer->keluhan_utama;
        }

        $this->alert('success', 'Data triase berhasil diambil', [
            'position' => 'top-end', 'timer' => 2000, 'toast' => true,
        ]);
    }

    public function simpan()
    {
        try {
            $kosong = [];
            if (empty(trim((string) $this->keluhan_utama))) $kosong[] = 'Keluhan Utama';
            if (empty(trim((string) $this->rps)))           $kosong[] = 'Riwayat Penyakit Sekarang';
            if (empty($this->td) && empty($this->nadi) && empty($this->rr)
                && empty($this->suhu) && empty($this->spo))  $kosong[] = 'Vital Sign (min. 1)';
            if (empty(trim((string) $this->diagnosis)))     $kosong[] = 'Diagnosis';
            if (empty(trim((string) $this->tata)))          $kosong[] = 'Tatalaksana';

            if (!empty($kosong)) {
                $this->alert('warning', 'Wajib diisi: ' . implode(', ', $kosong), [
                    'position' => 'center', 'timer' => 4000, 'toast' => false,
                ]);
                return;
            }

            DB::table('penilaian_medis_igd')->updateOrInsert(
                ['no_rawat' => $this->noRawat],
                [
                'tanggal'        => now(),
                'kd_dokter'      => session()->get('username'),
                'anamnesis'      => $this->anamnesis,
                'hubungan'       => $this->hubungan ?? '-',
                'keluhan_utama'  => $this->keluhan_utama ?? '-',
                'rps'            => $this->rps ?? '-',
                'rpd'            => $this->rpd ?? '-',
                'rpk'            => $this->rpk ?? '-',
                'rpo'            => $this->rpo ?? '-',
                'alergi'         => $this->alergi ?? 'Tidak Ada',
                'keadaan'        => $this->keadaan,
                'gcs'            => $this->gcs ?? '-',
                'kesadaran'      => $this->kesadaran,
                'td'             => $this->td ?? '-',
                'nadi'           => $this->nadi ?? '-',
                'rr'             => $this->rr ?? '-',
                'suhu'           => $this->suhu ?? '-',
                'spo'            => $this->spo ?? '-',
                'bb'             => $this->bb ?? '-',
                'tb'             => $this->tb ?? '-',
                'kepala'         => $this->kepala,
                'mata'           => $this->mata,
                'gigi'           => $this->gigi,
                'leher'          => $this->leher,
                'thoraks'        => $this->thoraks,
                'abdomen'        => $this->abdomen,
                'genital'        => $this->genital,
                'ekstremitas'    => $this->ekstremitas,
                'ket_fisik'      => $this->ket_fisik ?? '',
                'ket_lokalis'    => $this->ket_lokalis ?? '',
                'ekg'            => $this->ekg ?? '',
                'rad'            => $this->rad ?? '',
                'lab'            => $this->lab ?? '',
                'diagnosis'      => $this->diagnosis ?? '-',
                'tata'           => $this->tata ?? '-',
                ]
            );

            DB::table('reg_periksa')
                ->where('no_rawat', $this->noRawat)
                ->update(['stts' => 'Sudah']);

            $this->loadData();
            $this->alert('success', 'Penilaian medis berhasil disimpan', [
                'position' => 'top-end', 'timer' => 2000, 'toast' => true,
            ]);
        } catch (\Exception $e) {
            $this->alert('error', 'Gagal: ' . $e->getMessage(), [
                'position' => 'center', 'timer' => 4000, 'toast' => false,
            ]);
        }
    }
}
