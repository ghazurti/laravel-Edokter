<?php

namespace App\Http\Livewire\Component\AwalJantung;

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
    public $tanggal, $anamnesis, $hubungan, $keluhan_utama, $rps, $rpk, $rpd, $rpo, $alergi;
    public $td, $bb, $tb, $suhu, $nadi, $rr;
    public $keadaan_umum, $nyeri, $status_nutrisi;
    public $jantung, $keterangan_jantung, $paru, $keterangan_paru, $ekstrimitas, $keterangan_ekstrimitas;
    public $lainnya;
    public $lab, $ekg, $penunjang_lain;
    public $diagnosis, $diagnosis2, $permasalahan, $terapi, $tindakan, $edukasi;

    protected $listeners = ['hapusMedisRalanJantung' => 'hapus'];

    public function mount()
    {
        $this->tanggal      = Carbon::now()->format('Y-m-d H:i:s');
        $this->anamnesis    = 'Autoanamnesis';
        $this->keadaan_umum = 'Sehat';
        $this->jantung      = 'Normal';
        $this->paru         = 'Normal';
        $this->ekstrimitas  = 'Normal';
    }

    public function resetInput()
    {
        $this->resetExcept(['no_rawat', 'editMode']);
        $this->mount();
    }

    public function render()
    {
        return view('livewire.component.awal-jantung.form');
    }

    public function updatedNoRawat()
    {
        try {
            $this->resetInput();
            $data = DB::table('penilaian_medis_ralan_jantung')
                ->where('no_rawat', $this->no_rawat)
                ->first();

            if ($data) {
                $this->tanggal              = $data->tanggal;
                $this->anamnesis            = $data->anamnesis;
                $this->hubungan             = $data->hubungan;
                $this->keluhan_utama        = $data->keluhan_utama;
                $this->rps                  = $data->rps;
                $this->rpk                  = $data->rpk;
                $this->rpd                  = $data->rpd;
                $this->rpo                  = $data->rpo;
                $this->alergi               = $data->alergi;
                $this->td                   = $data->td;
                $this->bb                   = $data->bb;
                $this->tb                   = $data->tb;
                $this->suhu                 = $data->suhu;
                $this->nadi                 = $data->nadi;
                $this->rr                   = $data->rr;
                $this->keadaan_umum         = $data->keadaan_umum;
                $this->nyeri                = $data->nyeri;
                $this->status_nutrisi       = $data->status_nutrisi;
                $this->jantung              = $data->jantung;
                $this->keterangan_jantung   = $data->keterangan_jantung;
                $this->paru                 = $data->paru;
                $this->keterangan_paru      = $data->keterangan_paru;
                $this->ekstrimitas          = $data->ekstrimitas;
                $this->keterangan_ekstrimitas = $data->keterangan_ekstrimitas;
                $this->lainnya              = $data->lainnya;
                $this->lab                  = $data->lab;
                $this->ekg                  = $data->ekg;
                $this->penunjang_lain       = $data->penunjang_lain;
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
            $this->keluhan_utama  = $cppt->keluhan;
            $this->alergi         = $cppt->alergi;
            $this->td             = $cppt->tensi;
            $this->bb             = $cppt->berat;
            $this->tb             = $cppt->tinggi;
            $this->suhu           = $cppt->suhu_tubuh;
            $this->nadi           = $cppt->nadi;
            $this->rr             = $cppt->respirasi;
            $this->keadaan_umum   = $cppt->kesadaran;
            $this->diagnosis      = $cppt->penilaian;
            $this->terapi         = $cppt->rtl;
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
            'rpd'                   => $this->rpd ?? '',
            'rpo'                   => $this->rpo ?? '',
            'alergi'                => $this->alergi ?? '',
            'td'                    => $this->td ?? '',
            'bb'                    => $this->bb ?? '',
            'tb'                    => $this->tb ?? '',
            'suhu'                  => $this->suhu ?? '',
            'nadi'                  => $this->nadi ?? '',
            'rr'                    => $this->rr ?? '',
            'keadaan_umum'          => $this->keadaan_umum,
            'nyeri'                 => $this->nyeri ?? '',
            'status_nutrisi'        => $this->status_nutrisi ?? '',
            'jantung'               => $this->jantung,
            'keterangan_jantung'    => $this->keterangan_jantung ?? '',
            'paru'                  => $this->paru,
            'keterangan_paru'       => $this->keterangan_paru ?? '',
            'ekstrimitas'           => $this->ekstrimitas,
            'keterangan_ekstrimitas' => $this->keterangan_ekstrimitas ?? '',
            'lainnya'               => $this->lainnya ?? '',
            'lab'                   => $this->lab ?? '',
            'ekg'                   => $this->ekg ?? '',
            'penunjang_lain'        => $this->penunjang_lain ?? '',
            'diagnosis'             => $this->diagnosis ?? '',
            'diagnosis2'            => $this->diagnosis2 ?? '',
            'permasalahan'          => $this->permasalahan ?? '',
            'terapi'                => $this->terapi ?? '',
            'tindakan'              => $this->tindakan ?? '',
            'edukasi'               => $this->edukasi ?? '',
        ];

        try {
            if ($this->editMode) {
                DB::table('penilaian_medis_ralan_jantung')
                    ->where('no_rawat', $this->no_rawat)
                    ->update(Arr::except($data, ['no_rawat', 'kd_dokter']));

                $this->alert('success', 'Data berhasil diubah');
            } else {
                DB::table('penilaian_medis_ralan_jantung')->insert($data);

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
            $data = DB::table('penilaian_medis_ralan_jantung')
                ->where('no_rawat', $this->no_rawat)
                ->first();

            if ($data) {
                $this->confirm('Apakah anda yakin ingin menghapus data ini?', [
                    'onConfirmed'       => 'hapusMedisRalanJantung',
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
            DB::table('penilaian_medis_ralan_jantung')
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
