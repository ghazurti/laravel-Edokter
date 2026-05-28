<?php

namespace App\Http\Livewire\Component\AwalBedah;

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
    public $tanggal, $anamnesis, $hubungan, $keluhan_utama, $rps, $rpd, $rpo, $alergi;
    public $kesadaran, $status, $td, $nadi, $suhu, $rr, $bb, $nyeri, $gcs;
    public $kepala, $thoraks, $abdomen, $ekstremitas, $genetalia, $columna, $muskulos, $lainnya, $ket_lokalis;
    public $lab, $rad, $pemeriksaan;
    public $diagnosis, $diagnosis2, $permasalahan, $terapi, $tindakan, $edukasi;

    protected $listeners = ['hapusMedisRalanBedah' => 'hapus'];

    public function mount()
    {
        $this->tanggal    = Carbon::now()->format('Y-m-d H:i:s');
        $this->anamnesis  = 'Autoanamnesis';
        $this->kesadaran  = 'Compos Mentis';
        $this->kepala     = 'Normal';
        $this->thoraks    = 'Normal';
        $this->abdomen    = 'Normal';
        $this->ekstremitas = 'Normal';
        $this->genetalia  = 'Tidak Diperiksa';
        $this->columna    = 'Normal';
        $this->muskulos   = 'Normal';
    }

    public function resetInput()
    {
        $this->resetExcept(['no_rawat', 'editMode']);
        $this->mount();
    }

    public function render()
    {
        return view('livewire.component.awal-bedah.form');
    }

    public function setNoRawat($no_rawat)
    {
        $this->no_rawat = $no_rawat;
    }

    public function updatedNoRawat()
    {
        try {
            $this->resetInput();
            $data = DB::table('penilaian_medis_ralan_bedah')
                ->where('no_rawat', $this->no_rawat)
                ->first();

            if ($data) {
                $this->tanggal      = $data->tanggal;
                $this->anamnesis    = $data->anamnesis;
                $this->hubungan     = $data->hubungan;
                $this->keluhan_utama = $data->keluhan_utama;
                $this->rps          = $data->rps;
                $this->rpd          = $data->rpd;
                $this->rpo          = $data->rpo;
                $this->alergi       = $data->alergi;
                $this->kesadaran    = $data->kesadaran;
                $this->status       = $data->status;
                $this->td           = $data->td;
                $this->nadi         = $data->nadi;
                $this->suhu         = $data->suhu;
                $this->rr           = $data->rr;
                $this->bb           = $data->bb;
                $this->nyeri        = $data->nyeri;
                $this->gcs          = $data->gcs;
                $this->kepala       = $data->kepala;
                $this->thoraks      = $data->thoraks;
                $this->abdomen      = $data->abdomen;
                $this->ekstremitas  = $data->ekstremitas;
                $this->genetalia    = $data->genetalia;
                $this->columna      = $data->columna;
                $this->muskulos     = $data->muskulos;
                $this->lainnya      = $data->lainnya;
                $this->ket_lokalis  = $data->ket_lokalis;
                $this->lab          = $data->lab;
                $this->rad          = $data->rad;
                $this->pemeriksaan  = $data->pemeriksaan;
                $this->diagnosis    = $data->diagnosis;
                $this->diagnosis2   = $data->diagnosis2;
                $this->permasalahan = $data->permasalahan;
                $this->terapi       = $data->terapi;
                $this->tindakan     = $data->tindakan;
                $this->edukasi      = $data->edukasi;

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
            $this->gcs           = $cppt->gcs;
            $this->td            = $cppt->tensi;
            $this->bb            = $cppt->berat;
            $this->suhu          = $cppt->suhu_tubuh;
            $this->nadi          = $cppt->nadi;
            $this->rr            = $cppt->respirasi;
            $this->kesadaran     = $cppt->kesadaran;
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
            'no_rawat'     => $this->no_rawat,
            'tanggal'      => $this->tanggal,
            'kd_dokter'    => session()->get('username'),
            'anamnesis'    => $this->anamnesis,
            'hubungan'     => $this->hubungan ?? '',
            'keluhan_utama' => $this->keluhan_utama ?? '',
            'rps'          => $this->rps ?? '',
            'rpd'          => $this->rpd ?? '',
            'rpo'          => $this->rpo ?? '',
            'alergi'       => $this->alergi ?? '',
            'kesadaran'    => $this->kesadaran,
            'status'       => $this->status ?? '',
            'td'           => $this->td ?? '',
            'nadi'         => $this->nadi ?? '',
            'suhu'         => $this->suhu ?? '',
            'rr'           => $this->rr ?? '',
            'bb'           => $this->bb ?? '',
            'nyeri'        => $this->nyeri ?? '',
            'gcs'          => $this->gcs ?? '',
            'kepala'       => $this->kepala,
            'thoraks'      => $this->thoraks,
            'abdomen'      => $this->abdomen,
            'ekstremitas'  => $this->ekstremitas,
            'genetalia'    => $this->genetalia,
            'columna'      => $this->columna,
            'muskulos'     => $this->muskulos,
            'lainnya'      => $this->lainnya ?? '',
            'ket_lokalis'  => $this->ket_lokalis ?? '',
            'lab'          => $this->lab ?? '',
            'rad'          => $this->rad ?? '',
            'pemeriksaan'  => $this->pemeriksaan ?? '',
            'diagnosis'    => $this->diagnosis ?? '',
            'diagnosis2'   => $this->diagnosis2 ?? '',
            'permasalahan' => $this->permasalahan ?? '',
            'terapi'       => $this->terapi ?? '',
            'tindakan'     => $this->tindakan ?? '',
            'edukasi'      => $this->edukasi ?? '',
        ];

        try {
            if ($this->editMode) {
                DB::table('penilaian_medis_ralan_bedah')
                    ->where('no_rawat', $this->no_rawat)
                    ->update(Arr::except($data, ['no_rawat', 'kd_dokter']));

                $this->alert('success', 'Data berhasil diubah');
            } else {
                DB::table('penilaian_medis_ralan_bedah')->insert($data);

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
            $data = DB::table('penilaian_medis_ralan_bedah')
                ->where('no_rawat', $this->no_rawat)
                ->first();

            if ($data) {
                $this->confirm('Apakah anda yakin ingin menghapus data ini?', [
                    'onConfirmed'       => 'hapusMedisRalanBedah',
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
            DB::table('penilaian_medis_ralan_bedah')
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
