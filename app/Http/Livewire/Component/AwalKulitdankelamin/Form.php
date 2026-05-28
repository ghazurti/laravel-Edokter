<?php

namespace App\Http\Livewire\Component\AwalKulitdankelamin;

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
    public $tanggal, $anamnesis, $hubungan, $keluhan_utama, $rps, $rpd, $rpo, $rpk, $alergi;
    public $kesadaran, $status, $td, $nadi, $suhu, $rr, $bb, $nyeri, $gcs;
    public $statusderma;
    public $pemeriksaan;
    public $diagnosis, $diagnosis2, $permasalahan, $terapi, $tindakan, $edukasi;

    protected $listeners = ['hapusMedisRalanKulitdankelamin' => 'hapus'];

    public function mount()
    {
        $this->tanggal   = Carbon::now()->format('Y-m-d H:i:s');
        $this->anamnesis = 'Autoanamnesis';
        $this->kesadaran = 'Compos Mentis';
    }

    public function resetInput()
    {
        $this->resetExcept(['no_rawat', 'editMode']);
        $this->mount();
    }

    public function render()
    {
        return view('livewire.component.awal-kulitdankelamin.form');
    }

    public function updatedNoRawat()
    {
        try {
            $this->resetInput();
            $data = DB::table('penilaian_medis_ralan_kulitdankelamin')
                ->where('no_rawat', $this->no_rawat)
                ->first();

            if ($data) {
                $this->tanggal       = $data->tanggal;
                $this->anamnesis     = $data->anamnesis;
                $this->hubungan      = $data->hubungan;
                $this->keluhan_utama = $data->keluhan_utama;
                $this->rps           = $data->rps;
                $this->rpd           = $data->rpd;
                $this->rpo           = $data->rpo;
                $this->rpk           = $data->rpk;
                $this->alergi        = $data->alergi;
                $this->kesadaran     = $data->kesadaran;
                $this->status        = $data->status;
                $this->td            = $data->td;
                $this->nadi          = $data->nadi;
                $this->suhu          = $data->suhu;
                $this->rr            = $data->rr;
                $this->bb            = $data->bb;
                $this->nyeri         = $data->nyeri;
                $this->gcs           = $data->gcs;
                $this->statusderma   = $data->statusderma;
                $this->pemeriksaan   = $data->pemeriksaan;
                $this->diagnosis     = $data->diagnosis;
                $this->diagnosis2    = $data->diagnosis2;
                $this->permasalahan  = $data->permasalahan;
                $this->terapi        = $data->terapi;
                $this->tindakan      = $data->tindakan;
                $this->edukasi       = $data->edukasi;

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
            'no_rawat'      => $this->no_rawat,
            'tanggal'       => $this->tanggal,
            'kd_dokter'     => session()->get('username'),
            'anamnesis'     => $this->anamnesis,
            'hubungan'      => $this->hubungan ?? '',
            'keluhan_utama' => $this->keluhan_utama ?? '',
            'rps'           => $this->rps ?? '',
            'rpd'           => $this->rpd ?? '',
            'rpo'           => $this->rpo ?? '',
            'rpk'           => $this->rpk ?? '',
            'alergi'        => $this->alergi ?? '',
            'kesadaran'     => $this->kesadaran,
            'status'        => $this->status ?? '',
            'td'            => $this->td ?? '',
            'nadi'          => $this->nadi ?? '',
            'suhu'          => $this->suhu ?? '',
            'rr'            => $this->rr ?? '',
            'bb'            => $this->bb ?? '',
            'nyeri'         => $this->nyeri ?? '',
            'gcs'           => $this->gcs ?? '',
            'statusderma'   => $this->statusderma ?? '',
            'pemeriksaan'   => $this->pemeriksaan ?? '',
            'diagnosis'     => $this->diagnosis ?? '',
            'diagnosis2'    => $this->diagnosis2 ?? '',
            'permasalahan'  => $this->permasalahan ?? '',
            'terapi'        => $this->terapi ?? '',
            'tindakan'      => $this->tindakan ?? '',
            'edukasi'       => $this->edukasi ?? '',
        ];

        try {
            if ($this->editMode) {
                DB::table('penilaian_medis_ralan_kulitdankelamin')
                    ->where('no_rawat', $this->no_rawat)
                    ->update(Arr::except($data, ['no_rawat', 'kd_dokter']));

                $this->alert('success', 'Data berhasil diubah');
            } else {
                DB::table('penilaian_medis_ralan_kulitdankelamin')->insert($data);

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
            $data = DB::table('penilaian_medis_ralan_kulitdankelamin')
                ->where('no_rawat', $this->no_rawat)
                ->first();

            if ($data) {
                $this->confirm('Apakah anda yakin ingin menghapus data ini?', [
                    'onConfirmed'       => 'hapusMedisRalanKulitdankelamin',
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
            DB::table('penilaian_medis_ralan_kulitdankelamin')
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
