<?php

namespace App\Http\Livewire\Component\AwalPenyakitMulut;

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
    public $tanggal, $anamnesis, $hubungan, $keluhan_utama, $rps, $rpk, $alergi;
    public $keadaan, $kesadaran, $nyeri;
    public $td, $nadi, $suhu, $rr, $bb, $tb, $status_nutrisi;
    public $kulit, $keterangan_kulit, $kepala, $keterangan_kepala, $mata, $keterangan_mata;
    public $leher, $keterangan_leher, $kelenjar, $keterangan_kelenjar;
    public $dada, $keterangan_dada, $perut, $keterangan_perut;
    public $ekstremitas, $keterangan_ekstremitas;
    public $wajah, $intra, $gigigeligi;
    public $lab, $rad, $penunjang;
    public $diagnosis, $diagnosis2, $permasalahan, $terapi, $tindakan, $edukasi;

    protected $listeners = ['hapusMedisRalanPenyakitMulut' => 'hapus'];

    public function mount()
    {
        $this->tanggal    = Carbon::now()->format('Y-m-d H:i:s');
        $this->anamnesis  = 'Autoanamnesis';
        $this->keadaan    = 'Baik';
        $this->kesadaran  = 'Compos Mentis';
        $this->nyeri      = 'Tidak Nyeri';
        $this->kulit      = 'Tidak';
        $this->kepala     = 'Tidak';
        $this->mata       = 'Tidak';
        $this->leher      = 'Tidak';
        $this->kelenjar   = 'Tidak';
        $this->dada       = 'Tidak';
        $this->perut      = 'Tidak';
        $this->ekstremitas = 'Tidak';
    }

    public function resetInput()
    {
        $this->resetExcept(['no_rawat', 'editMode']);
        $this->mount();
    }

    public function render()
    {
        return view('livewire.component.awal-penyakit-mulut.form');
    }

    public function updatedNoRawat()
    {
        try {
            $this->resetInput();
            $data = DB::table('penilaian_medis_ralan_penyakit_mulut')
                ->where('no_rawat', $this->no_rawat)
                ->first();

            if ($data) {
                $this->tanggal              = $data->tanggal;
                $this->anamnesis            = $data->anamnesis;
                $this->hubungan             = $data->hubungan;
                $this->keluhan_utama        = $data->keluhan_utama;
                $this->rps                  = $data->rps;
                $this->rpk                  = $data->rpk;
                $this->alergi               = $data->alergi;
                $this->keadaan              = $data->keadaan;
                $this->kesadaran            = $data->kesadaran;
                $this->nyeri                = $data->nyeri;
                $this->td                   = $data->td;
                $this->nadi                 = $data->nadi;
                $this->suhu                 = $data->suhu;
                $this->rr                   = $data->rr;
                $this->bb                   = $data->bb;
                $this->tb                   = $data->tb;
                $this->status_nutrisi       = $data->status_nutrisi;
                $this->kulit                = $data->kulit;
                $this->keterangan_kulit     = $data->keterangan_kulit;
                $this->kepala               = $data->kepala;
                $this->keterangan_kepala    = $data->keterangan_kepala;
                $this->mata                 = $data->mata;
                $this->keterangan_mata      = $data->keterangan_mata;
                $this->leher                = $data->leher;
                $this->keterangan_leher     = $data->keterangan_leher;
                $this->kelenjar             = $data->kelenjar;
                $this->keterangan_kelenjar  = $data->keterangan_kelenjar;
                $this->dada                 = $data->dada;
                $this->keterangan_dada      = $data->keterangan_dada;
                $this->perut                = $data->perut;
                $this->keterangan_perut     = $data->keterangan_perut;
                $this->ekstremitas          = $data->ekstremitas;
                $this->keterangan_ekstremitas = $data->keterangan_ekstremitas;
                $this->wajah                = $data->wajah;
                $this->intra                = $data->intra;
                $this->gigigeligi           = $data->gigigeligi;
                $this->lab                  = $data->lab;
                $this->rad                  = $data->rad;
                $this->penunjang            = $data->penunjang;
                $this->diagnosis            = $data->diagnosis;
                $this->diagnosis2           = $data->diagnosis2;
                $this->permasalahan         = $data->permasalahan;
                $this->terapi               = $data->terapi;
                $this->tindakan             = $data->tindakan;
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

    public function ambilDariCppt()
    {
        try {
            $cppt = DB::table('pemeriksaan_ralan')
                ->where('no_rawat', $this->no_rawat)
                ->orderBy('tgl_perawatan', 'desc')->orderBy('jam_rawat', 'desc')
                ->first();
            if (!$cppt) { $this->alert('warning', 'Data CPPT tidak ditemukan'); return; }
            $this->keluhan_utama = $cppt->keluhan;
            $this->alergi        = $cppt->alergi;
            $this->td            = $cppt->tensi;
            $this->nadi          = $cppt->nadi;
            $this->suhu          = $cppt->suhu_tubuh;
            $this->rr            = $cppt->respirasi;
            $this->bb            = $cppt->berat;
            $this->tb            = $cppt->tinggi;
            $this->kesadaran     = $cppt->kesadaran;
            $this->keadaan       = $cppt->kesadaran;
            $this->diagnosis     = $cppt->penilaian;
            $this->terapi        = $cppt->rtl;
            $this->alert('success', 'Data CPPT berhasil diambil', ['timer' => 2000, 'toast' => true, 'position' => 'top-right']);
        } catch (\Exception $e) {
            $this->alert('error', 'Gagal mengambil data CPPT');
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
            'rpk'                   => $this->rpk ?? '',
            'alergi'                => $this->alergi ?? '',
            'keadaan'               => $this->keadaan,
            'kesadaran'             => $this->kesadaran,
            'nyeri'                 => $this->nyeri,
            'td'                    => $this->td ?? '',
            'nadi'                  => $this->nadi ?? '',
            'suhu'                  => $this->suhu ?? '',
            'rr'                    => $this->rr ?? '',
            'bb'                    => $this->bb ?? '',
            'tb'                    => $this->tb ?? '',
            'status_nutrisi'        => $this->status_nutrisi ?? '',
            'kulit'                 => $this->kulit,
            'keterangan_kulit'      => $this->keterangan_kulit ?? '',
            'kepala'                => $this->kepala,
            'keterangan_kepala'     => $this->keterangan_kepala ?? '',
            'mata'                  => $this->mata,
            'keterangan_mata'       => $this->keterangan_mata ?? '',
            'leher'                 => $this->leher,
            'keterangan_leher'      => $this->keterangan_leher ?? '',
            'kelenjar'              => $this->kelenjar,
            'keterangan_kelenjar'   => $this->keterangan_kelenjar ?? '',
            'dada'                  => $this->dada,
            'keterangan_dada'       => $this->keterangan_dada ?? '',
            'perut'                 => $this->perut,
            'keterangan_perut'      => $this->keterangan_perut ?? '',
            'ekstremitas'           => $this->ekstremitas,
            'keterangan_ekstremitas' => $this->keterangan_ekstremitas ?? '',
            'wajah'                 => $this->wajah ?? '',
            'intra'                 => $this->intra ?? '',
            'gigigeligi'            => $this->gigigeligi ?? '',
            'lab'                   => $this->lab ?? '',
            'rad'                   => $this->rad ?? '',
            'penunjang'             => $this->penunjang ?? '',
            'diagnosis'             => $this->diagnosis ?? '',
            'diagnosis2'            => $this->diagnosis2 ?? '',
            'permasalahan'          => $this->permasalahan ?? '',
            'terapi'                => $this->terapi ?? '',
            'tindakan'              => $this->tindakan ?? '',
            'edukasi'               => $this->edukasi ?? '',
        ];

        try {
            if ($this->editMode) {
                DB::table('penilaian_medis_ralan_penyakit_mulut')
                    ->where('no_rawat', $this->no_rawat)
                    ->update(Arr::except($data, ['no_rawat', 'kd_dokter']));

                $this->alert('success', 'Data berhasil diubah');
            } else {
                DB::table('penilaian_medis_ralan_penyakit_mulut')->insert($data);

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
            $data = DB::table('penilaian_medis_ralan_penyakit_mulut')
                ->where('no_rawat', $this->no_rawat)
                ->first();

            if ($data) {
                $this->confirm('Apakah anda yakin ingin menghapus data ini?', [
                    'onConfirmed'       => 'hapusMedisRalanPenyakitMulut',
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
            DB::table('penilaian_medis_ralan_penyakit_mulut')
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
