<?php

namespace App\Http\Livewire\Component\AwalRehabMedik;

use Illuminate\Support\Arr;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Form extends Component
{
    use LivewireAlert;
    public $no_rawat, $editMode = false;
    public $tanggal, $anamnesis, $hubungan, $keluhan_utama, $rps, $rpd, $alergi;
    public $kesadaran, $nyeri, $skala_nyeri, $td, $nadi, $suhu, $rr, $bb;
    public $kepala, $keterangan_kepala, $thoraks, $keterangan_thoraks, $abdomen, $keterangan_abdomen;
    public $ekstremitas, $keterangan_ekstremitas, $columna, $keterangan_columna, $muskulos, $keterangan_muskulos;
    public $lainnya;
    public $resiko_jatuh, $resiko_nutrisional, $kebutuhan_fungsional;
    public $diagnosa_medis, $diagnosa_fungsi, $penunjang_lain;
    public $fisio, $okupasi, $wicara, $akupuntur, $tatalain;
    public $frekuensi_terapi;
    public $fisioterapi, $terapi_okupasi, $terapi_wicara, $terapi_akupuntur, $terapi_lainnya;
    public $edukasi;

    protected $listeners = ['hapusMedisRalanRehabMedik' => 'hapus'];

    public function mount()
    {
        $this->tanggal              = Carbon::now()->format('Y-m-d H:i:s');
        $this->anamnesis            = 'Autoanamnesis';
        $this->kesadaran            = 'Compos Mentis';
        $this->nyeri                = 'Tidak Nyeri';
        $this->skala_nyeri          = '0';
        $this->kepala               = 'Normal';
        $this->thoraks              = 'Normal';
        $this->abdomen              = 'Normal';
        $this->ekstremitas          = 'Normal';
        $this->columna              = 'Normal';
        $this->muskulos             = 'Normal';
        $this->resiko_jatuh         = 'Tidak Berisiko';
        $this->resiko_nutrisional   = 'Tidak Berisiko Malnutrisi';
        $this->kebutuhan_fungsional = 'Tidak Perlu Bantuan';
    }

    public function resetInput()
    {
        $this->resetExcept(['no_rawat', 'editMode']);
        $this->mount();
    }

    public function render()
    {
        return view('livewire.component.awal-rehab-medik.form');
    }

    public function updatedNoRawat()
    {
        try {
            $this->resetInput();
            $data = DB::table('penilaian_medis_ralan_rehab_medik')
                ->where('no_rawat', $this->no_rawat)
                ->first();

            if ($data) {
                $this->tanggal              = $data->tanggal;
                $this->anamnesis            = $data->anamnesis;
                $this->hubungan             = $data->hubungan;
                $this->keluhan_utama        = $data->keluhan_utama;
                $this->rps                  = $data->rps;
                $this->rpd                  = $data->rpd;
                $this->alergi               = $data->alergi;
                $this->kesadaran            = $data->kesadaran;
                $this->nyeri                = $data->nyeri;
                $this->skala_nyeri          = $data->skala_nyeri;
                $this->td                   = $data->td;
                $this->nadi                 = $data->nadi;
                $this->suhu                 = $data->suhu;
                $this->rr                   = $data->rr;
                $this->bb                   = $data->bb;
                $this->kepala               = $data->kepala;
                $this->keterangan_kepala    = $data->keterangan_kepala;
                $this->thoraks              = $data->thoraks;
                $this->keterangan_thoraks   = $data->keterangan_thoraks;
                $this->abdomen              = $data->abdomen;
                $this->keterangan_abdomen   = $data->keterangan_abdomen;
                $this->ekstremitas          = $data->ekstremitas;
                $this->keterangan_ekstremitas = $data->keterangan_ekstremitas;
                $this->columna              = $data->columna;
                $this->keterangan_columna   = $data->keterangan_columna;
                $this->muskulos             = $data->muskulos;
                $this->keterangan_muskulos  = $data->keterangan_muskulos;
                $this->lainnya              = $data->lainnya;
                $this->resiko_jatuh         = $data->resiko_jatuh;
                $this->resiko_nutrisional   = $data->resiko_nutrisional;
                $this->kebutuhan_fungsional = $data->kebutuhan_fungsional;
                $this->diagnosa_medis       = $data->diagnosa_medis;
                $this->diagnosa_fungsi      = $data->diagnosa_fungsi;
                $this->penunjang_lain       = $data->penunjang_lain;
                $this->fisio                = $data->fisio;
                $this->okupasi              = $data->okupasi;
                $this->wicara               = $data->wicara;
                $this->akupuntur            = $data->akupuntur;
                $this->tatalain             = $data->tatalain;
                $this->frekuensi_terapi     = $data->frekuensi_terapi;
                $this->fisioterapi          = $data->fisioterapi;
                $this->terapi_okupasi       = $data->terapi_okupasi;
                $this->terapi_wicara        = $data->terapi_wicara;
                $this->terapi_akupuntur     = $data->terapi_akupuntur;
                $this->terapi_lainnya       = $data->terapi_lainnya;
                $this->edukasi              = $data->edukasi;

                $this->editMode = true;
            }
        } catch (\Exception $e) {
            $this->alert('error', 'Gagal', [
                'position' => 'center',
                'timer'    => '',
                'toast'    => false,
                'text'     => App::environment('local') ? $e->getMessage() : 'Terjadi Kesalahan',
                'confirmButtonText' => 'Oke',
            ]);
        }
    }

    public function simpan()
    {
        $data = [
            'no_rawat'              => $this->no_rawat,
            'tanggal'               => $this->tanggal,
            'kd_dokter'             => session()->get('username'),
            'anamnesis'             => $this->anamnesis,
            'hubungan'              => $this->hubungan ?? '',
            'keluhan_utama'         => $this->keluhan_utama ?? '',
            'rps'                   => $this->rps ?? '',
            'rpd'                   => $this->rpd ?? '',
            'alergi'                => $this->alergi ?? '',
            'kesadaran'             => $this->kesadaran,
            'nyeri'                 => $this->nyeri,
            'skala_nyeri'           => $this->skala_nyeri,
            'td'                    => $this->td ?? '',
            'nadi'                  => $this->nadi ?? '',
            'suhu'                  => $this->suhu ?? '',
            'rr'                    => $this->rr ?? '',
            'bb'                    => $this->bb ?? '',
            'kepala'                => $this->kepala,
            'keterangan_kepala'     => $this->keterangan_kepala ?? '',
            'thoraks'               => $this->thoraks,
            'keterangan_thoraks'    => $this->keterangan_thoraks ?? '',
            'abdomen'               => $this->abdomen,
            'keterangan_abdomen'    => $this->keterangan_abdomen ?? '',
            'ekstremitas'           => $this->ekstremitas,
            'keterangan_ekstremitas' => $this->keterangan_ekstremitas ?? '',
            'columna'               => $this->columna,
            'keterangan_columna'    => $this->keterangan_columna ?? '',
            'muskulos'              => $this->muskulos,
            'keterangan_muskulos'   => $this->keterangan_muskulos ?? '',
            'lainnya'               => $this->lainnya ?? '',
            'resiko_jatuh'          => $this->resiko_jatuh,
            'resiko_nutrisional'    => $this->resiko_nutrisional,
            'kebutuhan_fungsional'  => $this->kebutuhan_fungsional,
            'diagnosa_medis'        => $this->diagnosa_medis ?? '',
            'diagnosa_fungsi'       => $this->diagnosa_fungsi ?? '',
            'penunjang_lain'        => $this->penunjang_lain ?? '',
            'fisio'                 => $this->fisio ?? '',
            'okupasi'               => $this->okupasi ?? '',
            'wicara'                => $this->wicara ?? '',
            'akupuntur'             => $this->akupuntur ?? '',
            'tatalain'              => $this->tatalain ?? '',
            'frekuensi_terapi'      => $this->frekuensi_terapi ?? '',
            'fisioterapi'           => $this->fisioterapi ?? '',
            'terapi_okupasi'        => $this->terapi_okupasi ?? '',
            'terapi_wicara'         => $this->terapi_wicara ?? '',
            'terapi_akupuntur'      => $this->terapi_akupuntur ?? '',
            'terapi_lainnya'        => $this->terapi_lainnya ?? '',
            'edukasi'               => $this->edukasi ?? '',
        ];

        try {
            if ($this->editMode) {
                DB::table('penilaian_medis_ralan_rehab_medik')
                    ->where('no_rawat', $this->no_rawat)
                    ->update(Arr::except($data, ['no_rawat', 'kd_dokter']));

                $this->alert('success', 'Data berhasil diubah');
            } else {
                DB::table('penilaian_medis_ralan_rehab_medik')->insert($data);

                $this->editMode = true;
                $this->alert('success', 'Data berhasil disimpan');
            }
        } catch (\Exception $e) {
            $this->alert('error', 'Gagal', [
                'position' => 'center',
                'timer'    => '',
                'toast'    => false,
                'text'     => App::environment('local') ? $e->getMessage() : 'Terjadi Kesalahan',
                'confirmButtonText' => 'Oke',
            ]);
        }
    }

    public function confirmHapus()
    {
        try {
            $data = DB::table('penilaian_medis_ralan_rehab_medik')
                ->where('no_rawat', $this->no_rawat)
                ->first();

            if ($data) {
                $this->confirm('Apakah anda yakin ingin menghapus data ini?', [
                    'onConfirmed'       => 'hapusMedisRalanRehabMedik',
                    'cancelButtonText'  => 'Batal',
                    'confirmButtonText' => 'Hapus',
                ]);
            } else {
                $this->alert('warning', 'Data tidak ditemukan', [
                    'position' => 'center',
                    'timer'    => '',
                    'toast'    => false,
                    'showConfirmButton' => true,
                    'confirmButtonText' => 'Oke',
                ]);
            }
        } catch (\Exception $e) {
        }
    }

    public function hapus()
    {
        try {
            DB::table('penilaian_medis_ralan_rehab_medik')
                ->where('no_rawat', $this->no_rawat)
                ->delete();

            $this->resetInput();
            $this->editMode = false;
            $this->alert('success', 'Berhasil hapus data');
        } catch (\Exception $e) {
            $this->alert('error', 'Gagal', [
                'position' => 'center',
                'timer'    => '',
                'toast'    => false,
                'text'     => App::environment('local') ? $e->getMessage() : 'Terjadi Kesalahan',
                'confirmButtonText' => 'Oke',
            ]);
        }
    }
}
