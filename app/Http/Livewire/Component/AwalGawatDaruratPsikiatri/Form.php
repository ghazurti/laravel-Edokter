<?php

namespace App\Http\Livewire\Component\AwalGawatDaruratPsikiatri;

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
    public $tanggal, $anamnesis, $hubungan;
    public $keluhan_utama, $gejala_menyertai, $faktor_pencetus;
    public $riwayat_penyakit_dahulu, $keterangan_riwayat_penyakit_dahulu;
    public $riwayat_kehamilan;
    public $riwayat_sosial, $keterangan_riwayat_sosial;
    public $riwayat_pekerjaan, $keterangan_riwayat_pekerjaan;
    public $riwayat_obat_diminum;
    public $faktor_kepribadian_premorbid;
    public $faktor_keturunan, $keterangan_faktor_keturunan;
    public $faktor_organik, $keterangan_faktor_organik;
    public $riwayat_alergi;
    // Fisik masuk
    public $fisik_kesadaran, $fisik_td, $fisik_rr, $fisik_suhu, $fisik_nyeri;
    public $fisik_nadi, $fisik_bb, $fisik_tb, $fisik_status_nutrisi, $fisik_gcs;
    // Status kelainan
    public $status_kelainan_kepala, $keterangan_status_kelainan_kepala;
    public $status_kelainan_leher, $keterangan_status_kelainan_leher;
    public $status_kelainan_dada, $keterangan_status_kelainan_dada;
    public $status_kelainan_perut, $keterangan_status_kelainan_perut;
    public $status_kelainan_anggota_gerak, $keterangan_status_kelainan_anggota_gerak;
    public $status_lokalisata;
    // Status psikiatrik
    public $psikiatrik_kesan_umum, $psikiatrik_sikap_prilaku, $psikiatrik_kesadaran;
    public $psikiatrik_orientasi, $psikiatrik_daya_ingat, $psikiatrik_persepsi;
    public $psikiatrik_pikiran, $psikiatrik_insight;
    // Penunjang
    public $laborat, $radiologi, $ekg;
    // Asesmen
    public $diagnosis, $permasalahan, $instruksi_medis, $rencana_target;
    // Rencana pulang
    public $pulang_dipulangkan, $keterangan_pulang_dipulangkan;
    public $pulang_dirawat_diruang, $pulang_indikasi_ranap, $pulang_dirujuk_ke;
    public $pulang_alasan_dirujuk;
    public $pulang_paksa, $keterangan_pulang_paksa;
    public $pulang_meninggal_igd, $pulang_penyebab_kematian;
    // Fisik pulang
    public $fisik_pulang_kesadaran, $fisik_pulang_td, $fisik_pulang_nadi;
    public $fisik_pulang_gcs, $fisik_pulang_suhu, $fisik_pulang_rr;
    public $edukasi;

    protected $listeners = ['hapusMedisRalanGawatDaruratPsikiatri' => 'hapus'];

    public function mount()
    {
        $this->tanggal                    = Carbon::now()->format('Y-m-d H:i:s');
        $this->anamnesis                  = 'Autoanamnesis';
        $this->riwayat_penyakit_dahulu    = 'Tidak Ada';
        $this->riwayat_sosial             = 'Bergaul';
        $this->riwayat_pekerjaan          = 'Bekerja';
        $this->faktor_keturunan           = 'Tidak Ada';
        $this->faktor_organik             = 'Tidak Ada';
        $this->fisik_kesadaran            = 'Compos Mentis';
        $this->fisik_nyeri                = 'Tidak Nyeri';
        $this->status_kelainan_kepala     = 'Normal';
        $this->status_kelainan_leher      = 'Normal';
        $this->status_kelainan_dada       = 'Normal';
        $this->status_kelainan_perut      = 'Normal';
        $this->status_kelainan_anggota_gerak = 'Normal';
        $this->pulang_dipulangkan         = '-';
        $this->pulang_alasan_dirujuk      = '-';
        $this->pulang_paksa               = '-';
        $this->pulang_meninggal_igd       = '-';
        $this->fisik_pulang_kesadaran     = 'Compos Mentis';
    }

    public function resetInput()
    {
        $this->resetExcept(['no_rawat', 'editMode']);
        $this->mount();
    }

    public function render()
    {
        return view('livewire.component.awal-gawat-darurat-psikiatri.form');
    }

    public function updatedNoRawat()
    {
        try {
            $this->resetInput();
            $data = DB::table('penilaian_medis_ralan_gawat_darurat_psikiatri')
                ->where('no_rawat', $this->no_rawat)
                ->first();

            if ($data) {
                $this->tanggal                          = $data->tanggal;
                $this->anamnesis                        = $data->anamnesis;
                $this->hubungan                         = $data->hubungan;
                $this->keluhan_utama                    = $data->keluhan_utama;
                $this->gejala_menyertai                 = $data->gejala_menyertai;
                $this->faktor_pencetus                  = $data->faktor_pencetus;
                $this->riwayat_penyakit_dahulu          = $data->riwayat_penyakit_dahulu;
                $this->keterangan_riwayat_penyakit_dahulu = $data->keterangan_riwayat_penyakit_dahulu;
                $this->riwayat_kehamilan                = $data->riwayat_kehamilan;
                $this->riwayat_sosial                   = $data->riwayat_sosial;
                $this->keterangan_riwayat_sosial        = $data->keterangan_riwayat_sosial;
                $this->riwayat_pekerjaan                = $data->riwayat_pekerjaan;
                $this->keterangan_riwayat_pekerjaan     = $data->keterangan_riwayat_pekerjaan;
                $this->riwayat_obat_diminum             = $data->riwayat_obat_diminum;
                $this->faktor_kepribadian_premorbid     = $data->faktor_kepribadian_premorbid;
                $this->faktor_keturunan                 = $data->faktor_keturunan;
                $this->keterangan_faktor_keturunan      = $data->keterangan_faktor_keturunan;
                $this->faktor_organik                   = $data->faktor_organik;
                $this->keterangan_faktor_organik        = $data->keterangan_faktor_organik;
                $this->riwayat_alergi                   = $data->riwayat_alergi;
                $this->fisik_kesadaran                  = $data->fisik_kesadaran;
                $this->fisik_td                         = $data->fisik_td;
                $this->fisik_rr                         = $data->fisik_rr;
                $this->fisik_suhu                       = $data->fisik_suhu;
                $this->fisik_nyeri                      = $data->fisik_nyeri;
                $this->fisik_nadi                       = $data->fisik_nadi;
                $this->fisik_bb                         = $data->fisik_bb;
                $this->fisik_tb                         = $data->fisik_tb;
                $this->fisik_status_nutrisi             = $data->fisik_status_nutrisi;
                $this->fisik_gcs                        = $data->fisik_gcs;
                $this->status_kelainan_kepala           = $data->status_kelainan_kepala;
                $this->keterangan_status_kelainan_kepala = $data->keterangan_status_kelainan_kepala;
                $this->status_kelainan_leher            = $data->status_kelainan_leher;
                $this->keterangan_status_kelainan_leher = $data->keterangan_status_kelainan_leher;
                $this->status_kelainan_dada             = $data->status_kelainan_dada;
                $this->keterangan_status_kelainan_dada  = $data->keterangan_status_kelainan_dada;
                $this->status_kelainan_perut            = $data->status_kelainan_perut;
                $this->keterangan_status_kelainan_perut = $data->keterangan_status_kelainan_perut;
                $this->status_kelainan_anggota_gerak    = $data->status_kelainan_anggota_gerak;
                $this->keterangan_status_kelainan_anggota_gerak = $data->keterangan_status_kelainan_anggota_gerak;
                $this->status_lokalisata                = $data->status_lokalisata;
                $this->psikiatrik_kesan_umum            = $data->psikiatrik_kesan_umum;
                $this->psikiatrik_sikap_prilaku         = $data->psikiatrik_sikap_prilaku;
                $this->psikiatrik_kesadaran             = $data->psikiatrik_kesadaran;
                $this->psikiatrik_orientasi             = $data->psikiatrik_orientasi;
                $this->psikiatrik_daya_ingat            = $data->psikiatrik_daya_ingat;
                $this->psikiatrik_persepsi              = $data->psikiatrik_persepsi;
                $this->psikiatrik_pikiran               = $data->psikiatrik_pikiran;
                $this->psikiatrik_insight               = $data->psikiatrik_insight;
                $this->laborat                          = $data->laborat;
                $this->radiologi                        = $data->radiologi;
                $this->ekg                              = $data->ekg;
                $this->diagnosis                        = $data->diagnosis;
                $this->permasalahan                     = $data->permasalahan;
                $this->instruksi_medis                  = $data->instruksi_medis;
                $this->rencana_target                   = $data->rencana_target;
                $this->pulang_dipulangkan               = $data->pulang_dipulangkan;
                $this->keterangan_pulang_dipulangkan    = $data->keterangan_pulang_dipulangkan;
                $this->pulang_dirawat_diruang           = $data->pulang_dirawat_diruang;
                $this->pulang_indikasi_ranap            = $data->pulang_indikasi_ranap;
                $this->pulang_dirujuk_ke                = $data->pulang_dirujuk_ke;
                $this->pulang_alasan_dirujuk            = $data->pulang_alasan_dirujuk;
                $this->pulang_paksa                     = $data->pulang_paksa;
                $this->keterangan_pulang_paksa          = $data->keterangan_pulang_paksa;
                $this->pulang_meninggal_igd             = $data->pulang_meninggal_igd;
                $this->pulang_penyebab_kematian         = $data->pulang_penyebab_kematian;
                $this->fisik_pulang_kesadaran           = $data->fisik_pulang_kesadaran;
                $this->fisik_pulang_td                  = $data->fisik_pulang_td;
                $this->fisik_pulang_nadi                = $data->fisik_pulang_nadi;
                $this->fisik_pulang_gcs                 = $data->fisik_pulang_gcs;
                $this->fisik_pulang_suhu                = $data->fisik_pulang_suhu;
                $this->fisik_pulang_rr                  = $data->fisik_pulang_rr;
                $this->edukasi                          = $data->edukasi;

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
            $this->keluhan_utama    = $cppt->keluhan;
            $this->riwayat_alergi   = $cppt->alergi;
            $this->fisik_td         = $cppt->tensi;
            $this->fisik_nadi       = $cppt->nadi;
            $this->fisik_suhu       = $cppt->suhu_tubuh;
            $this->fisik_rr         = $cppt->respirasi;
            $this->fisik_bb         = $cppt->berat;
            $this->fisik_tb         = $cppt->tinggi;
            $this->fisik_gcs        = $cppt->gcs;
            $this->fisik_kesadaran  = $cppt->kesadaran;
            $this->diagnosis        = $cppt->penilaian;
            $this->instruksi_medis  = $cppt->rtl;
            $this->alert('success', 'Data CPPT berhasil diambil', ['timer' => 2000, 'toast' => true, 'position' => 'top-right']);
        } catch (\Exception $e) {
            $this->alert('error', 'Gagal mengambil data CPPT');
        }
    }

    public function simpan()
    {
        $data = [
            'no_rawat'                               => $this->no_rawat,
            'tanggal'                                => $this->tanggal,
            'kd_dokter'                              => session()->get('username'),
            'anamnesis'                              => $this->anamnesis,
            'hubungan'                               => $this->hubungan ?? '',
            'keluhan_utama'                          => $this->keluhan_utama ?? '',
            'gejala_menyertai'                       => $this->gejala_menyertai ?? '',
            'faktor_pencetus'                        => $this->faktor_pencetus ?? '',
            'riwayat_penyakit_dahulu'                => $this->riwayat_penyakit_dahulu,
            'keterangan_riwayat_penyakit_dahulu'     => $this->keterangan_riwayat_penyakit_dahulu ?? '',
            'riwayat_kehamilan'                      => $this->riwayat_kehamilan ?? '',
            'riwayat_sosial'                         => $this->riwayat_sosial,
            'keterangan_riwayat_sosial'              => $this->keterangan_riwayat_sosial ?? '',
            'riwayat_pekerjaan'                      => $this->riwayat_pekerjaan,
            'keterangan_riwayat_pekerjaan'           => $this->keterangan_riwayat_pekerjaan ?? '',
            'riwayat_obat_diminum'                   => $this->riwayat_obat_diminum ?? '',
            'faktor_kepribadian_premorbid'           => $this->faktor_kepribadian_premorbid ?? '',
            'faktor_keturunan'                       => $this->faktor_keturunan,
            'keterangan_faktor_keturunan'            => $this->keterangan_faktor_keturunan ?? '',
            'faktor_organik'                         => $this->faktor_organik,
            'keterangan_faktor_organik'              => $this->keterangan_faktor_organik ?? '',
            'riwayat_alergi'                         => $this->riwayat_alergi ?? '',
            'fisik_kesadaran'                        => $this->fisik_kesadaran,
            'fisik_td'                               => $this->fisik_td ?? '',
            'fisik_rr'                               => $this->fisik_rr ?? '',
            'fisik_suhu'                             => $this->fisik_suhu ?? '',
            'fisik_nyeri'                            => $this->fisik_nyeri,
            'fisik_nadi'                             => $this->fisik_nadi ?? '',
            'fisik_bb'                               => $this->fisik_bb ?? '',
            'fisik_tb'                               => $this->fisik_tb ?? '',
            'fisik_status_nutrisi'                   => $this->fisik_status_nutrisi ?? '',
            'fisik_gcs'                              => $this->fisik_gcs ?? '',
            'status_kelainan_kepala'                 => $this->status_kelainan_kepala,
            'keterangan_status_kelainan_kepala'      => $this->keterangan_status_kelainan_kepala ?? '',
            'status_kelainan_leher'                  => $this->status_kelainan_leher,
            'keterangan_status_kelainan_leher'       => $this->keterangan_status_kelainan_leher ?? '',
            'status_kelainan_dada'                   => $this->status_kelainan_dada,
            'keterangan_status_kelainan_dada'        => $this->keterangan_status_kelainan_dada ?? '',
            'status_kelainan_perut'                  => $this->status_kelainan_perut,
            'keterangan_status_kelainan_perut'       => $this->keterangan_status_kelainan_perut ?? '',
            'status_kelainan_anggota_gerak'          => $this->status_kelainan_anggota_gerak,
            'keterangan_status_kelainan_anggota_gerak' => $this->keterangan_status_kelainan_anggota_gerak ?? '',
            'status_lokalisata'                      => $this->status_lokalisata ?? '',
            'psikiatrik_kesan_umum'                  => $this->psikiatrik_kesan_umum ?? '',
            'psikiatrik_sikap_prilaku'               => $this->psikiatrik_sikap_prilaku ?? '',
            'psikiatrik_kesadaran'                   => $this->psikiatrik_kesadaran ?? '',
            'psikiatrik_orientasi'                   => $this->psikiatrik_orientasi ?? '',
            'psikiatrik_daya_ingat'                  => $this->psikiatrik_daya_ingat ?? '',
            'psikiatrik_persepsi'                    => $this->psikiatrik_persepsi ?? '',
            'psikiatrik_pikiran'                     => $this->psikiatrik_pikiran ?? '',
            'psikiatrik_insight'                     => $this->psikiatrik_insight ?? '',
            'laborat'                                => $this->laborat ?? '',
            'radiologi'                              => $this->radiologi ?? '',
            'ekg'                                    => $this->ekg ?? '',
            'diagnosis'                              => $this->diagnosis ?? '',
            'permasalahan'                           => $this->permasalahan ?? '',
            'instruksi_medis'                        => $this->instruksi_medis ?? '',
            'rencana_target'                         => $this->rencana_target ?? '',
            'pulang_dipulangkan'                     => $this->pulang_dipulangkan,
            'keterangan_pulang_dipulangkan'          => $this->keterangan_pulang_dipulangkan ?? '',
            'pulang_dirawat_diruang'                 => $this->pulang_dirawat_diruang ?? '',
            'pulang_indikasi_ranap'                  => $this->pulang_indikasi_ranap ?? '',
            'pulang_dirujuk_ke'                      => $this->pulang_dirujuk_ke ?? '',
            'pulang_alasan_dirujuk'                  => $this->pulang_alasan_dirujuk,
            'pulang_paksa'                           => $this->pulang_paksa,
            'keterangan_pulang_paksa'                => $this->keterangan_pulang_paksa ?? '',
            'pulang_meninggal_igd'                   => $this->pulang_meninggal_igd,
            'pulang_penyebab_kematian'               => $this->pulang_penyebab_kematian ?? '',
            'fisik_pulang_kesadaran'                 => $this->fisik_pulang_kesadaran,
            'fisik_pulang_td'                        => $this->fisik_pulang_td ?? '',
            'fisik_pulang_nadi'                      => $this->fisik_pulang_nadi ?? '',
            'fisik_pulang_gcs'                       => $this->fisik_pulang_gcs ?? '',
            'fisik_pulang_suhu'                      => $this->fisik_pulang_suhu ?? '',
            'fisik_pulang_rr'                        => $this->fisik_pulang_rr ?? '',
            'edukasi'                                => $this->edukasi ?? '',
        ];

        try {
            if ($this->editMode) {
                DB::table('penilaian_medis_ralan_gawat_darurat_psikiatri')
                    ->where('no_rawat', $this->no_rawat)
                    ->update(Arr::except($data, ['no_rawat', 'kd_dokter']));

                $this->alert('success', 'Data berhasil diubah');
            } else {
                DB::table('penilaian_medis_ralan_gawat_darurat_psikiatri')->insert($data);

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
            $data = DB::table('penilaian_medis_ralan_gawat_darurat_psikiatri')
                ->where('no_rawat', $this->no_rawat)
                ->first();

            if ($data) {
                $this->confirm('Apakah anda yakin ingin menghapus data ini?', [
                    'onConfirmed'       => 'hapusMedisRalanGawatDaruratPsikiatri',
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
            DB::table('penilaian_medis_ralan_gawat_darurat_psikiatri')
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
