<?php

namespace App\Http\Livewire\Component\AwalUrologi;

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
    public $tanggal, $anamnesis, $hubungan, $keluhan_utama, $rps, $rpk, $rpd, $rpo;
    public $riwayat_kebiasaan, $riwayat_operasi_urologi, $alergi;
    public $td, $bb, $tb, $suhu, $nadi, $rr;
    public $keadaan_umum, $nyeri, $status_nutrisi;
    public $thoraks, $keterangan_thoraks, $abdomen, $keterangan_abdomen, $ekstrimitas, $keterangan_ekstrimitas;
    public $nyeri_ketok_cva, $genitalia_eksternal, $colok_dubur;
    public $lainnya;
    public $urinalisis, $darah, $usg_urologi, $radiologi, $penunjang_lain;
    public $diagnosis, $diagnosis2, $permasalahan, $terapi, $tindakan, $edukasi;

    protected $listeners = ['hapusMedisRalanUrologi' => 'hapus'];

    public function mount()
    {
        $this->tanggal      = Carbon::now()->format('Y-m-d H:i:s');
        $this->anamnesis    = 'Autoanamnesis';
        $this->keadaan_umum = 'Sehat';
        $this->thoraks      = 'Normal';
        $this->abdomen      = 'Normal';
        $this->ekstrimitas  = 'Normal';
    }

    public function resetInput()
    {
        $this->resetExcept(['no_rawat', 'editMode']);
        $this->mount();
    }

    public function render()
    {
        return view('livewire.component.awal-urologi.form');
    }

    public function updatedNoRawat()
    {
        try {
            $this->resetInput();
            $data = DB::table('penilaian_medis_ralan_urologi')
                ->where('no_rawat', $this->no_rawat)
                ->first();

            if ($data) {
                $this->tanggal                = $data->tanggal;
                $this->anamnesis              = $data->anamnesis;
                $this->hubungan               = $data->hubungan;
                $this->keluhan_utama          = $data->keluhan_utama;
                $this->rps                    = $data->rps;
                $this->rpk                    = $data->rpk;
                $this->rpd                    = $data->rpd;
                $this->rpo                    = $data->rpo;
                $this->riwayat_kebiasaan      = $data->riwayat_kebiasaan;
                $this->riwayat_operasi_urologi = $data->riwayat_operasi_urologi;
                $this->alergi                 = $data->alergi;
                $this->td                     = $data->td;
                $this->bb                     = $data->bb;
                $this->tb                     = $data->tb;
                $this->suhu                   = $data->suhu;
                $this->nadi                   = $data->nadi;
                $this->rr                     = $data->rr;
                $this->keadaan_umum           = $data->keadaan_umum;
                $this->nyeri                  = $data->nyeri;
                $this->status_nutrisi         = $data->status_nutrisi;
                $this->thoraks                = $data->thoraks;
                $this->keterangan_thoraks     = $data->keterangan_thoraks;
                $this->abdomen                = $data->abdomen;
                $this->keterangan_abdomen     = $data->keterangan_abdomen;
                $this->ekstrimitas            = $data->ekstrimitas;
                $this->keterangan_ekstrimitas = $data->keterangan_ekstrimitas;
                $this->nyeri_ketok_cva        = $data->nyeri_ketok_cva;
                $this->genitalia_eksternal    = $data->genitalia_eksternal;
                $this->colok_dubur            = $data->colok_dubur;
                $this->lainnya                = $data->lainnya;
                $this->urinalisis             = $data->urinalisis;
                $this->darah                  = $data->darah;
                $this->usg_urologi            = $data->usg_urologi;
                $this->radiologi              = $data->radiologi;
                $this->penunjang_lain         = $data->penunjang_lain;
                $this->diagnosis              = $data->diagnosis;
                $this->diagnosis2             = $data->diagnosis2;
                $this->permasalahan           = $data->permasalahan;
                $this->terapi                 = $data->terapi;
                $this->tindakan               = $data->tindakan;
                $this->edukasi                = $data->edukasi;

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
            'no_rawat'                => $this->no_rawat,
            'tanggal'                 => $this->tanggal,
            'kd_dokter'               => session()->get('username'),
            'anamnesis'               => $this->anamnesis,
            'hubungan'                => $this->hubungan ?? '',
            'keluhan_utama'           => $this->keluhan_utama ?? '',
            'rps'                     => $this->rps ?? '',
            'rpk'                     => $this->rpk ?? '',
            'rpd'                     => $this->rpd ?? '',
            'rpo'                     => $this->rpo ?? '',
            'riwayat_kebiasaan'       => $this->riwayat_kebiasaan ?? '',
            'riwayat_operasi_urologi' => $this->riwayat_operasi_urologi ?? '',
            'alergi'                  => $this->alergi ?? '',
            'td'                      => $this->td ?? '',
            'bb'                      => $this->bb ?? '',
            'tb'                      => $this->tb ?? '',
            'suhu'                    => $this->suhu ?? '',
            'nadi'                    => $this->nadi ?? '',
            'rr'                      => $this->rr ?? '',
            'keadaan_umum'            => $this->keadaan_umum,
            'nyeri'                   => $this->nyeri ?? '',
            'status_nutrisi'          => $this->status_nutrisi ?? '',
            'thoraks'                 => $this->thoraks,
            'keterangan_thoraks'      => $this->keterangan_thoraks ?? '',
            'abdomen'                 => $this->abdomen,
            'keterangan_abdomen'      => $this->keterangan_abdomen ?? '',
            'ekstrimitas'             => $this->ekstrimitas,
            'keterangan_ekstrimitas'  => $this->keterangan_ekstrimitas ?? '',
            'nyeri_ketok_cva'         => $this->nyeri_ketok_cva ?? '',
            'genitalia_eksternal'     => $this->genitalia_eksternal ?? '',
            'colok_dubur'             => $this->colok_dubur ?? '',
            'lainnya'                 => $this->lainnya ?? '',
            'urinalisis'              => $this->urinalisis ?? '',
            'darah'                   => $this->darah ?? '',
            'usg_urologi'             => $this->usg_urologi ?? '',
            'radiologi'               => $this->radiologi ?? '',
            'penunjang_lain'          => $this->penunjang_lain ?? '',
            'diagnosis'               => $this->diagnosis ?? '',
            'diagnosis2'              => $this->diagnosis2 ?? '',
            'permasalahan'            => $this->permasalahan ?? '',
            'terapi'                  => $this->terapi ?? '',
            'tindakan'                => $this->tindakan ?? '',
            'edukasi'                 => $this->edukasi ?? '',
        ];

        try {
            if ($this->editMode) {
                DB::table('penilaian_medis_ralan_urologi')
                    ->where('no_rawat', $this->no_rawat)
                    ->update(Arr::except($data, ['no_rawat', 'kd_dokter']));

                $this->alert('success', 'Data berhasil diubah');
            } else {
                DB::table('penilaian_medis_ralan_urologi')->insert($data);

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
            $data = DB::table('penilaian_medis_ralan_urologi')
                ->where('no_rawat', $this->no_rawat)
                ->first();

            if ($data) {
                $this->confirm('Apakah anda yakin ingin menghapus data ini?', [
                    'onConfirmed'       => 'hapusMedisRalanUrologi',
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
            DB::table('penilaian_medis_ralan_urologi')
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
