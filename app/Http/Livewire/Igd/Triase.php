<?php

namespace App\Http\Livewire\Igd;

use App\Traits\SwalResponse;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Triase extends Component
{
    use SwalResponse, LivewireAlert;

    public $noRawat;

    // data_triase_igd (header + vital signs)
    public $cara_masuk = 'Jalan', $alat_transportasi = '-', $alasan_kedatangan = '-';
    public $keterangan_kedatangan, $kode_kasus;
    public $tekanan_darah, $nadi, $pernapasan, $suhu, $saturasi_o2, $nyeri;

    // data_triase_igdprimer
    public $keluhan_utama, $kebutuhan_khusus = '-', $catatan, $plan = 'Ruang Resusitasi';

    // data_triase_igdsekunder
    public $anamnesa_singkat, $catatan_sekunder, $plan_sekunder = 'Zona Kuning';

    // Pemeriksaan selection & skala checkboxes
    public $selectedPemeriksaan = '001';
    public $skala1Selected = [], $skala2Selected = [];

    // Status badge data (primitives only — avoids Livewire serialization issues)
    public $primerTanggal, $primerPetugas;
    public $sekunderTanggal, $sekunderPetugas;

    public function mount($noRawat)
    {
        $this->noRawat = $noRawat;
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.igd.triase', [
            'masterKasus'       => DB::table('master_triase_macam_kasus')->get(),
            'masterPemeriksaan' => DB::table('master_triase_pemeriksaan')->get(),
            'masterSkala1'      => DB::table('master_triase_skala1')->get(),
            'masterSkala2'      => DB::table('master_triase_skala2')->get(),
            'riwayatTriase'     => $this->getRiwayatTriase(),
        ]);
    }

    private function getRiwayatTriase()
    {
        $rkm = DB::table('reg_periksa')->where('no_rawat', $this->noRawat)->value('no_rkm_medis');
        if (!$rkm) return collect();

        return DB::table('reg_periksa')
            ->leftJoin('data_triase_igd', 'reg_periksa.no_rawat', '=', 'data_triase_igd.no_rawat')
            ->leftJoin('data_triase_igdprimer', 'reg_periksa.no_rawat', '=', 'data_triase_igdprimer.no_rawat')
            ->leftJoin('data_triase_igdsekunder', 'reg_periksa.no_rawat', '=', 'data_triase_igdsekunder.no_rawat')
            ->where('reg_periksa.no_rkm_medis', $rkm)
            ->where(function ($q) {
                $q->whereNotNull('data_triase_igdprimer.no_rawat')
                  ->orWhereNotNull('data_triase_igdsekunder.no_rawat');
            })
            ->orderBy('reg_periksa.tgl_registrasi', 'desc')
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'data_triase_igd.tekanan_darah',
                'data_triase_igd.nadi',
                'data_triase_igd.suhu',
                'data_triase_igdprimer.keluhan_utama',
                'data_triase_igdprimer.plan as plan_primer',
                'data_triase_igdsekunder.plan as plan_sekunder'
            )
            ->get();
    }

    public function loadData()
    {
        $dataTriase = DB::table('data_triase_igd')->where('no_rawat', $this->noRawat)->first();
        if ($dataTriase) {
            $this->cara_masuk            = $dataTriase->cara_masuk;
            $this->alat_transportasi     = $dataTriase->alat_transportasi;
            $this->alasan_kedatangan     = $dataTriase->alasan_kedatangan;
            $this->keterangan_kedatangan = $dataTriase->keterangan_kedatangan;
            $this->kode_kasus            = $dataTriase->kode_kasus;
            $this->tekanan_darah         = $dataTriase->tekanan_darah;
            $this->nadi                  = $dataTriase->nadi;
            $this->pernapasan            = $dataTriase->pernapasan;
            $this->suhu                  = $dataTriase->suhu;
            $this->saturasi_o2           = $dataTriase->saturasi_o2;
            $this->nyeri                 = $dataTriase->nyeri;
        }

        $primer = DB::table('data_triase_igdprimer')
            ->leftJoin('pegawai', 'pegawai.nik', '=', 'data_triase_igdprimer.nik')
            ->where('data_triase_igdprimer.no_rawat', $this->noRawat)
            ->select('data_triase_igdprimer.*', 'pegawai.nama as nama_petugas')
            ->first();
        if ($primer) {
            $this->keluhan_utama    = $primer->keluhan_utama;
            $this->kebutuhan_khusus = $primer->kebutuhan_khusus;
            $this->catatan          = $primer->catatan;
            $this->plan             = $primer->plan;
            $this->primerTanggal    = $primer->tanggaltriase;
            $this->primerPetugas    = $primer->nama_petugas ?? $primer->nik;
        } else {
            $this->primerTanggal = null;
            $this->primerPetugas = null;
        }

        $sekunder = DB::table('data_triase_igdsekunder')
            ->leftJoin('pegawai', 'pegawai.nik', '=', 'data_triase_igdsekunder.nik')
            ->where('data_triase_igdsekunder.no_rawat', $this->noRawat)
            ->select('data_triase_igdsekunder.*', 'pegawai.nama as nama_petugas')
            ->first();
        if ($sekunder) {
            $this->anamnesa_singkat  = $sekunder->anamnesa_singkat;
            $this->catatan_sekunder  = $sekunder->catatan;
            $this->plan_sekunder     = $sekunder->plan;
            $this->sekunderTanggal   = $sekunder->tanggaltriase;
            $this->sekunderPetugas   = $sekunder->nama_petugas ?? $sekunder->nik;
        } else {
            $this->sekunderTanggal = null;
            $this->sekunderPetugas = null;
        }

        $this->skala1Selected = DB::table('data_triase_igddetail_skala1')
            ->where('no_rawat', $this->noRawat)->pluck('kode_skala1')->toArray();

        $this->skala2Selected = DB::table('data_triase_igddetail_skala2')
            ->where('no_rawat', $this->noRawat)->pluck('kode_skala2')->toArray();
    }

    public function simpanPrimer()
    {
        try {
            if (empty(trim((string) $this->keluhan_utama))) {
                $this->alert('warning', 'Keluhan Utama wajib diisi', [
                    'position' => 'center', 'timer' => 2500, 'toast' => false,
                ]);
                return;
            }
            if (empty($this->tekanan_darah) && empty($this->nadi) && empty($this->suhu)
                && empty($this->pernapasan) && empty($this->saturasi_o2)) {
                $this->alert('warning', 'Vital sign minimal salah satu harus diisi', [
                    'position' => 'center', 'timer' => 2500, 'toast' => false,
                ]);
                return;
            }
            if (empty($this->plan)) {
                $this->alert('warning', 'Plan / Keputusan wajib dipilih', [
                    'position' => 'center', 'timer' => 2500, 'toast' => false,
                ]);
                return;
            }

            $this->saveHeaderVital();

            DB::table('data_triase_igdprimer')->updateOrInsert(
                ['no_rawat' => $this->noRawat],
                [
                    'keluhan_utama'    => $this->keluhan_utama ?? '',
                    'kebutuhan_khusus' => $this->kebutuhan_khusus ?? '-',
                    'catatan'          => $this->catatan ?? '',
                    'plan'             => $this->plan,
                    'tanggaltriase'    => now(),
                    'nik'              => session('username') ?? '',
                ]
            );

            DB::table('data_triase_igddetail_skala1')->where('no_rawat', $this->noRawat)->delete();
            foreach ($this->skala1Selected as $kode) {
                DB::table('data_triase_igddetail_skala1')->insert([
                    'no_rawat'    => $this->noRawat,
                    'kode_skala1' => $kode,
                ]);
            }

            DB::table('data_triase_igddetail_skala2')->where('no_rawat', $this->noRawat)->delete();
            foreach ($this->skala2Selected as $kode) {
                DB::table('data_triase_igddetail_skala2')->insert([
                    'no_rawat'    => $this->noRawat,
                    'kode_skala2' => $kode,
                ]);
            }

            $this->loadData();
            $this->alert('success', 'Triase Primer berhasil disimpan', [
                'position' => 'top-end', 'timer' => 2000, 'toast' => true,
            ]);
        } catch (\Exception $e) {
            $this->alert('error', 'Gagal: ' . $e->getMessage(), [
                'position' => 'center', 'timer' => 4000, 'toast' => false,
            ]);
        }
    }

    public function simpanSekunder()
    {
        try {
            if (empty(trim((string) $this->anamnesa_singkat))) {
                $this->alert('warning', 'Anamnesa Singkat wajib diisi', [
                    'position' => 'center', 'timer' => 2500, 'toast' => false,
                ]);
                return;
            }
            if (empty($this->plan_sekunder)) {
                $this->alert('warning', 'Plan / Keputusan Sekunder wajib dipilih', [
                    'position' => 'center', 'timer' => 2500, 'toast' => false,
                ]);
                return;
            }

            $this->saveHeaderVital();

            DB::table('data_triase_igdsekunder')->updateOrInsert(
                ['no_rawat' => $this->noRawat],
                [
                    'anamnesa_singkat' => $this->anamnesa_singkat ?? '',
                    'catatan'          => $this->catatan_sekunder ?? '',
                    'plan'             => $this->plan_sekunder,
                    'tanggaltriase'    => now(),
                    'nik'              => session('username') ?? '',
                ]
            );

            $this->loadData();
            $this->alert('success', 'Triase Sekunder berhasil disimpan', [
                'position' => 'top-end', 'timer' => 2000, 'toast' => true,
            ]);
        } catch (\Exception $e) {
            $this->alert('error', 'Gagal: ' . $e->getMessage(), [
                'position' => 'center', 'timer' => 4000, 'toast' => false,
            ]);
        }
    }

    private function saveHeaderVital()
    {
        DB::table('data_triase_igd')->updateOrInsert(
            ['no_rawat' => $this->noRawat],
            [
                'tgl_kunjungan'         => now(),
                'cara_masuk'            => $this->cara_masuk,
                'alat_transportasi'     => $this->alat_transportasi,
                'alasan_kedatangan'     => $this->alasan_kedatangan,
                'keterangan_kedatangan' => $this->keterangan_kedatangan ?? '-',
                'kode_kasus'            => $this->kode_kasus ?? '',
                'tekanan_darah'         => $this->tekanan_darah ?? '-',
                'nadi'                  => $this->nadi ?? '-',
                'pernapasan'            => $this->pernapasan ?? '-',
                'suhu'                  => $this->suhu ?? '-',
                'saturasi_o2'           => $this->saturasi_o2 ?? '-',
                'nyeri'                 => $this->nyeri ?? '-',
            ]
        );
    }
}
