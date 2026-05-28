<?php

namespace App\Http\Livewire\Component\AwalGeriatri;

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
    public $tulang_belakang, $td, $nadi, $suhu, $rr;
    public $kondisi_umum, $status_psikologis_gds, $kondisi_sosial, $status_kognitif_mmse;
    public $kepala, $keterangan_kepala, $thoraks, $keterangan_thoraks, $abdomen, $keterangan_abdomen;
    public $ekstremitas, $keterangan_ekstremitas;
    public $Integument_kebersihan, $Integument_warna, $Integument_kelembaban, $Integument_gangguan_kulit;
    public $status_fungsional, $skrining_jatuh, $status_nutrisi;
    public $lainnya;
    public $lab, $rad, $pemeriksaan;
    public $diagnosis, $diagnosis2, $permasalahan, $terapi, $tindakan, $edukasi;

    protected $listeners = ['hapusMedisRalanGeriatri' => 'hapus'];

    public function mount()
    {
        $this->tanggal                = Carbon::now()->format('Y-m-d H:i:s');
        $this->anamnesis              = 'Autoanamnesis';
        $this->tulang_belakang        = 'Tegap';
        $this->status_psikologis_gds  = 'Skor 1-4 Tidak Ada Depresi';
        $this->status_kognitif_mmse   = '24-30 : Tidak Ada Gangguan Kognitif';
        $this->kepala                 = 'Normal';
        $this->thoraks                = 'Normal';
        $this->abdomen                = 'Normal';
        $this->ekstremitas            = 'Normal';
        $this->Integument_kebersihan  = 'Normal';
        $this->Integument_warna       = 'Normal';
        $this->Integument_kelembaban  = 'Lembab';
        $this->Integument_gangguan_kulit = 'Normal';
        $this->status_fungsional      = '20 : Mandiri (A)';
        $this->skrining_jatuh         = 'Risiko Rendah Skor 0-5';
        $this->status_nutrisi         = 'Skor 12-14 : Status Gizi Normal';
    }

    public function resetInput()
    {
        $this->resetExcept(['no_rawat', 'editMode']);
        $this->mount();
    }

    public function render()
    {
        return view('livewire.component.awal-geriatri.form');
    }

    public function updatedNoRawat()
    {
        try {
            $this->resetInput();
            $data = DB::table('penilaian_medis_ralan_geriatri')
                ->where('no_rawat', $this->no_rawat)
                ->first();

            if ($data) {
                $this->tanggal                   = $data->tanggal;
                $this->anamnesis                 = $data->anamnesis;
                $this->hubungan                  = $data->hubungan;
                $this->keluhan_utama             = $data->keluhan_utama;
                $this->rps                       = $data->rps;
                $this->rpd                       = $data->rpd;
                $this->rpo                       = $data->rpo;
                $this->alergi                    = $data->alergi;
                $this->tulang_belakang           = $data->tulang_belakang;
                $this->td                        = $data->td;
                $this->nadi                      = $data->nadi;
                $this->suhu                      = $data->suhu;
                $this->rr                        = $data->rr;
                $this->kondisi_umum              = $data->kondisi_umum;
                $this->status_psikologis_gds     = $data->status_psikologis_gds;
                $this->kondisi_sosial            = $data->kondisi_sosial;
                $this->status_kognitif_mmse      = $data->status_kognitif_mmse;
                $this->kepala                    = $data->kepala;
                $this->keterangan_kepala         = $data->keterangan_kepala;
                $this->thoraks                   = $data->thoraks;
                $this->keterangan_thoraks        = $data->keterangan_thoraks;
                $this->abdomen                   = $data->abdomen;
                $this->keterangan_abdomen        = $data->keterangan_abdomen;
                $this->ekstremitas               = $data->ekstremitas;
                $this->keterangan_ekstremitas    = $data->keterangan_ekstremitas;
                $this->Integument_kebersihan     = $data->Integument_kebersihan;
                $this->Integument_warna          = $data->Integument_warna;
                $this->Integument_kelembaban     = $data->Integument_kelembaban;
                $this->Integument_gangguan_kulit = $data->Integument_gangguan_kulit;
                $this->status_fungsional         = $data->status_fungsional;
                $this->skrining_jatuh            = $data->skrining_jatuh;
                $this->status_nutrisi            = $data->status_nutrisi;
                $this->lainnya                   = $data->lainnya;
                $this->lab                       = $data->lab;
                $this->rad                       = $data->rad;
                $this->pemeriksaan               = $data->pemeriksaan;
                $this->diagnosis                 = $data->diagnosis;
                $this->diagnosis2                = $data->diagnosis2;
                $this->permasalahan              = $data->permasalahan;
                $this->terapi                    = $data->terapi;
                $this->tindakan                  = $data->tindakan;
                $this->edukasi                   = $data->edukasi;

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
            $this->nadi           = $cppt->nadi;
            $this->suhu           = $cppt->suhu_tubuh;
            $this->rr             = $cppt->respirasi;
            $this->kondisi_umum   = $cppt->kesadaran;
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
            'no_rawat'                   => $this->no_rawat,
            'tanggal'                    => $this->tanggal,
            'kd_dokter'                  => session()->get('username'),
            'anamnesis'                  => $this->anamnesis,
            'hubungan'                   => $this->hubungan ?? '',
            'keluhan_utama'              => $this->keluhan_utama ?? '',
            'rps'                        => $this->rps ?? '',
            'rpd'                        => $this->rpd ?? '',
            'rpo'                        => $this->rpo ?? '',
            'alergi'                     => $this->alergi ?? '',
            'tulang_belakang'            => $this->tulang_belakang,
            'td'                         => $this->td ?? '',
            'nadi'                       => $this->nadi ?? '',
            'suhu'                       => $this->suhu ?? '',
            'rr'                         => $this->rr ?? '',
            'kondisi_umum'               => $this->kondisi_umum ?? '',
            'status_psikologis_gds'      => $this->status_psikologis_gds,
            'kondisi_sosial'             => $this->kondisi_sosial ?? '',
            'status_kognitif_mmse'       => $this->status_kognitif_mmse,
            'kepala'                     => $this->kepala,
            'keterangan_kepala'          => $this->keterangan_kepala ?? '',
            'thoraks'                    => $this->thoraks,
            'keterangan_thoraks'         => $this->keterangan_thoraks ?? '',
            'abdomen'                    => $this->abdomen,
            'keterangan_abdomen'         => $this->keterangan_abdomen ?? '',
            'ekstremitas'                => $this->ekstremitas,
            'keterangan_ekstremitas'     => $this->keterangan_ekstremitas ?? '',
            'Integument_kebersihan'      => $this->Integument_kebersihan,
            'Integument_warna'           => $this->Integument_warna,
            'Integument_kelembaban'      => $this->Integument_kelembaban,
            'Integument_gangguan_kulit'  => $this->Integument_gangguan_kulit,
            'status_fungsional'          => $this->status_fungsional,
            'skrining_jatuh'             => $this->skrining_jatuh,
            'status_nutrisi'             => $this->status_nutrisi,
            'lainnya'                    => $this->lainnya ?? '',
            'lab'                        => $this->lab ?? '',
            'rad'                        => $this->rad ?? '',
            'pemeriksaan'                => $this->pemeriksaan ?? '',
            'diagnosis'                  => $this->diagnosis ?? '',
            'diagnosis2'                 => $this->diagnosis2 ?? '',
            'permasalahan'               => $this->permasalahan ?? '',
            'terapi'                     => $this->terapi ?? '',
            'tindakan'                   => $this->tindakan ?? '',
            'edukasi'                    => $this->edukasi ?? '',
        ];

        try {
            if ($this->editMode) {
                DB::table('penilaian_medis_ralan_geriatri')
                    ->where('no_rawat', $this->no_rawat)
                    ->update(Arr::except($data, ['no_rawat', 'kd_dokter']));

                $this->alert('success', 'Data berhasil diubah');
            } else {
                DB::table('penilaian_medis_ralan_geriatri')->insert($data);

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
            $data = DB::table('penilaian_medis_ralan_geriatri')
                ->where('no_rawat', $this->no_rawat)
                ->first();

            if ($data) {
                $this->confirm('Apakah anda yakin ingin menghapus data ini?', [
                    'onConfirmed'       => 'hapusMedisRalanGeriatri',
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
            DB::table('penilaian_medis_ralan_geriatri')
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
