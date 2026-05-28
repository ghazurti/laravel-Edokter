<?php

namespace App\Http\Livewire\Component\AwalMata;

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
    public $status, $td, $nadi, $rr, $suhu, $nyeri, $bb;
    // Bilateral eye exam - right (kanan)
    public $visus_kanan, $cc_kanan, $pal_kanan, $con_kanan, $cornea_kanan, $coa_kanan;
    public $pupil_kanan, $lensa_kanan, $fundus_kanan, $papil_kanan, $retina_kanan, $makula_kanan, $tio_kanan, $mbo_kanan;
    // Bilateral eye exam - left (kiri)
    public $visus_kiri, $cc_kiri, $pal_kiri, $con_kiri, $cornea_kiri, $coa_kiri;
    public $pupil_kiri, $lensa_kiri, $fundus_kiri, $papil_kiri, $retina_kiri, $makula_kiri, $tio_kiri, $mbo_kiri;
    public $lab, $rad, $penunjang, $tes, $pemeriksaan;
    public $diagnosis, $diagnosisbdg, $permasalahan, $terapi, $tindakan, $edukasi;

    protected $listeners = ['hapusMedisRalanMata' => 'hapus'];

    public function mount()
    {
        $this->tanggal   = Carbon::now()->format('Y-m-d H:i:s');
        $this->anamnesis = 'Autoanamnesis';
    }

    public function resetInput()
    {
        $this->resetExcept(['no_rawat', 'editMode']);
        $this->mount();
    }

    public function render()
    {
        return view('livewire.component.awal-mata.form');
    }

    public function updatedNoRawat()
    {
        try {
            $this->resetInput();
            $data = DB::table('penilaian_medis_ralan_mata')
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
                $this->alergi        = $data->alergi;
                $this->status        = $data->status;
                $this->td            = $data->td;
                $this->nadi          = $data->nadi;
                $this->rr            = $data->rr;
                $this->suhu          = $data->suhu;
                $this->nyeri         = $data->nyeri;
                $this->bb            = $data->bb;
                // Right eye
                $this->visus_kanan   = $data->visus_kanan;
                $this->cc_kanan      = $data->cc_kanan;
                $this->pal_kanan     = $data->pal_kanan;
                $this->con_kanan     = $data->con_kanan;
                $this->cornea_kanan  = $data->cornea_kanan;
                $this->coa_kanan     = $data->coa_kanan;
                $this->pupil_kanan   = $data->pupil_kanan;
                $this->lensa_kanan   = $data->lensa_kanan;
                $this->fundus_kanan  = $data->fundus_kanan;
                $this->papil_kanan   = $data->papil_kanan;
                $this->retina_kanan  = $data->retina_kanan;
                $this->makula_kanan  = $data->makula_kanan;
                $this->tio_kanan     = $data->tio_kanan;
                $this->mbo_kanan     = $data->mbo_kanan;
                // Left eye
                $this->visus_kiri    = $data->visus_kiri;
                $this->cc_kiri       = $data->cc_kiri;
                $this->pal_kiri      = $data->pal_kiri;
                $this->con_kiri      = $data->con_kiri;
                $this->cornea_kiri   = $data->cornea_kiri;
                $this->coa_kiri      = $data->coa_kiri;
                $this->pupil_kiri    = $data->pupil_kiri;
                $this->lensa_kiri    = $data->lensa_kiri;
                $this->fundus_kiri   = $data->fundus_kiri;
                $this->papil_kiri    = $data->papil_kiri;
                $this->retina_kiri   = $data->retina_kiri;
                $this->makula_kiri   = $data->makula_kiri;
                $this->tio_kiri      = $data->tio_kiri;
                $this->mbo_kiri      = $data->mbo_kiri;
                // Penunjang
                $this->lab           = $data->lab;
                $this->rad           = $data->rad;
                $this->penunjang     = $data->penunjang;
                $this->tes           = $data->tes;
                $this->pemeriksaan   = $data->pemeriksaan;
                // Asesmen
                $this->diagnosis     = $data->diagnosis;
                $this->diagnosisbdg  = $data->diagnosisbdg;
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
            'alergi'        => $this->alergi ?? '',
            'status'        => $this->status ?? '',
            'td'            => $this->td ?? '',
            'nadi'          => $this->nadi ?? '',
            'rr'            => $this->rr ?? '',
            'suhu'          => $this->suhu ?? '',
            'nyeri'         => $this->nyeri ?? '',
            'bb'            => $this->bb ?? '',
            // Right
            'visus_kanan'   => $this->visus_kanan ?? '',
            'cc_kanan'      => $this->cc_kanan ?? '',
            'pal_kanan'     => $this->pal_kanan ?? '',
            'con_kanan'     => $this->con_kanan ?? '',
            'cornea_kanan'  => $this->cornea_kanan ?? '',
            'coa_kanan'     => $this->coa_kanan ?? '',
            'pupil_kanan'   => $this->pupil_kanan ?? '',
            'lensa_kanan'   => $this->lensa_kanan ?? '',
            'fundus_kanan'  => $this->fundus_kanan ?? '',
            'papil_kanan'   => $this->papil_kanan ?? '',
            'retina_kanan'  => $this->retina_kanan ?? '',
            'makula_kanan'  => $this->makula_kanan ?? '',
            'tio_kanan'     => $this->tio_kanan ?? '',
            'mbo_kanan'     => $this->mbo_kanan ?? '',
            // Left
            'visus_kiri'    => $this->visus_kiri ?? '',
            'cc_kiri'       => $this->cc_kiri ?? '',
            'pal_kiri'      => $this->pal_kiri ?? '',
            'con_kiri'      => $this->con_kiri ?? '',
            'cornea_kiri'   => $this->cornea_kiri ?? '',
            'coa_kiri'      => $this->coa_kiri ?? '',
            'pupil_kiri'    => $this->pupil_kiri ?? '',
            'lensa_kiri'    => $this->lensa_kiri ?? '',
            'fundus_kiri'   => $this->fundus_kiri ?? '',
            'papil_kiri'    => $this->papil_kiri ?? '',
            'retina_kiri'   => $this->retina_kiri ?? '',
            'makula_kiri'   => $this->makula_kiri ?? '',
            'tio_kiri'      => $this->tio_kiri ?? '',
            'mbo_kiri'      => $this->mbo_kiri ?? '',
            // Penunjang
            'lab'           => $this->lab ?? '',
            'rad'           => $this->rad ?? '',
            'penunjang'     => $this->penunjang ?? '',
            'tes'           => $this->tes ?? '',
            'pemeriksaan'   => $this->pemeriksaan ?? '',
            // Asesmen
            'diagnosis'     => $this->diagnosis ?? '',
            'diagnosisbdg'  => $this->diagnosisbdg ?? '',
            'permasalahan'  => $this->permasalahan ?? '',
            'terapi'        => $this->terapi ?? '',
            'tindakan'      => $this->tindakan ?? '',
            'edukasi'       => $this->edukasi ?? '',
        ];

        try {
            if ($this->editMode) {
                DB::table('penilaian_medis_ralan_mata')
                    ->where('no_rawat', $this->no_rawat)
                    ->update(Arr::except($data, ['no_rawat', 'kd_dokter']));

                $this->alert('success', 'Data berhasil diubah');
            } else {
                DB::table('penilaian_medis_ralan_mata')->insert($data);

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
            $data = DB::table('penilaian_medis_ralan_mata')
                ->where('no_rawat', $this->no_rawat)
                ->first();

            if ($data) {
                $this->confirm('Apakah anda yakin ingin menghapus data ini?', [
                    'onConfirmed'       => 'hapusMedisRalanMata',
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
            DB::table('penilaian_medis_ralan_mata')
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
