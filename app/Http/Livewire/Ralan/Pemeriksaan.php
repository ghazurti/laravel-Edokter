<?php

namespace App\Http\Livewire\Ralan;

use App\Traits\SwalResponse;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Pemeriksaan extends Component
{
    use SwalResponse, LivewireAlert;
    public $listPemeriksaan, $isCollapsed = false, $noRawat, $noRm, $isMaximized = true, $keluhan, $pemeriksaan, $penilaian, $instruksi, $rtl, $alergi, $suhu, $berat, $tinggi, $tensi, $nadi, $respirasi, $evaluasi, $gcs, $kesadaran = 'Compos Mentis', $lingkar, $spo2;
    public $carryInfo;
    public $tgl, $jam;
    public $listeners = ['refreshData' => '$refresh', 'hapusPemeriksaan' => 'hapus'];

    public function mount($noRawat, $noRm)
    {
        $this->noRawat = $noRawat;
        $this->noRm = $noRm;
        if (!$this->isCollapsed) {
            $this->getPemeriksaan();
            $this->getListPemeriksaan();
        }
    }

    public function openModal()
    {
        $this->emit('openModalRehabMedik');
    }

    public function render()
    {
        return view('livewire.ralan.pemeriksaan');
    }

    public function hydrate()
    {
        $this->getPemeriksaan();
        $this->getListPemeriksaan();
    }

    public function getListPemeriksaan()
    {
        $this->listPemeriksaan = DB::table('pemeriksaan_ralan')
            ->join('pegawai', 'pemeriksaan_ralan.nip', '=', 'pegawai.nik')
            ->where('no_rawat', $this->noRawat)
            ->select('pemeriksaan_ralan.*', 'pegawai.nama')
            ->get();
    }

    public function collapsed()
    {
        $this->isCollapsed = !$this->isCollapsed;
    }

    public function expanded()
    {
        $this->isMaximized = !$this->isMaximized;
    }

    public function getPemeriksaan()
    {
        $username = session()->get('username');

        // Entri TERAKHIR siapa pun (umumnya perawat) — sumber carry-over TTV + keluhan.
        $terakhir = DB::table('pemeriksaan_ralan')
            ->where('no_rawat', $this->noRawat)
            ->orderBy('tgl_perawatan', 'desc')
            ->orderBy('jam_rawat', 'desc')
            ->first();

        // Entri milik DOKTER ini (untuk melanjutkan Asesmen/Plan miliknya, bukan milik perawat).
        $milikDokter = DB::table('pemeriksaan_ralan')
            ->where('no_rawat', $this->noRawat)
            ->where('nip', $username)
            ->orderBy('tgl_perawatan', 'desc')
            ->orderBy('jam_rawat', 'desc')
            ->first();

        $this->carryInfo = null;

        if ($terakhir) {
            // --- Carry-over: TTV + keluhan + alergi (tetap baris terpisah saat disimpan) ---
            $this->keluhan   = $terakhir->keluhan;
            $this->alergi    = $terakhir->alergi ?: 'Tidak Ada';
            $this->suhu      = $terakhir->suhu_tubuh;
            $this->berat     = $terakhir->berat;
            $this->tinggi    = $terakhir->tinggi;
            $this->tensi     = $terakhir->tensi;
            $this->nadi      = $terakhir->nadi;
            $this->respirasi = $terakhir->respirasi;
            $this->gcs       = $terakhir->gcs;
            $this->kesadaran = $terakhir->kesadaran ?: 'Compos Mentis';
            $this->lingkar   = $terakhir->lingkar_perut;
            $this->spo2      = $terakhir->spo2;

            // Catatan transparansi bila TTV/keluhan berasal dari entri orang lain (perawat).
            if ($terakhir->nip !== $username) {
                $nama = DB::table('pegawai')->where('nik', $terakhir->nip)->value('nama')
                    ?? DB::table('dokter')->where('kd_dokter', $terakhir->nip)->value('nm_dokter')
                    ?? $terakhir->nip;
                $this->carryInfo = 'TTV & keluhan otomatis dari entri ' . $nama
                    . ' (' . substr((string) $terakhir->jam_rawat, 0, 5) . '). Lengkapi Asesmen & Plan Anda — tersimpan sebagai entri terpisah atas nama Anda.';
            }
        }

        // --- Asesmen/Objek/Plan = milik dokter sendiri (kosong bila belum pernah isi) ---
        $this->pemeriksaan = $milikDokter->pemeriksaan ?? null;
        $this->penilaian   = $milikDokter->penilaian ?? null;
        $this->instruksi   = $milikDokter->instruksi ?? null;
        $this->rtl         = $milikDokter->rtl ?? null;
        $this->evaluasi    = $milikDokter->evaluasi ?? null;
    }

    public function simpanPemeriksaan()
    {
        try {
            DB::beginTransaction();
            DB::table('pemeriksaan_ralan')
                ->insert([
                    'no_rawat' => $this->noRawat,
                    'keluhan' => $this->keluhan ?? '-',
                    'pemeriksaan' => $this->pemeriksaan ?? '-',
                    'penilaian' => $this->penilaian ?? '-',
                    'instruksi' => $this->instruksi ?? '-',
                    'rtl' => $this->rtl ?? '-',
                    'alergi' => $this->alergi ?? '-',
                    'suhu_tubuh' => $this->suhu,
                    'berat' => $this->berat ?? '0',
                    'tinggi' => $this->tinggi ?? '0',
                    'tensi' => $this->tensi ?? '-',
                    'nadi' => $this->nadi ?? '-',
                    'respirasi' => $this->respirasi ?? '-',
                    'gcs' => $this->gcs ?? '-',
                    'kesadaran' => $this->kesadaran ?? 'Compos Mentis',
                    'lingkar_perut' => $this->lingkar ?? '0',
                    'spo2' => $this->spo2 ?? '-',
                    'evaluasi' => $this->evaluasi ?? '-',
                    'tgl_perawatan' => date('Y-m-d'),
                    'jam_rawat' => date('H:i:s'),
                    'nip' => session()->get('username'),
                ]);

            DB::table('reg_periksa')
                ->where('no_rawat', $this->noRawat)
                ->update(['stts' => 'Sudah']);

            DB::commit();
            $this->getListPemeriksaan();
            // $this->dispatchBrowserEvent('swal:pemeriksaan', $this->toastResponse('Pemeriksaan berhasil ditambahkan'));
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollback();
            $this->dispatchBrowserEvent('swal:pemeriksaan', $this->toastResponse($ex->getMessage() ?? 'Pemeriksaan gagal ditambahkan', 'error'));
        }
    }

    public function confirmHapus($noRawat, $tgl, $jam)
    {
        $this->noRawat = $noRawat;
        $this->tgl = $tgl;
        $this->jam = $jam;
        $this->confirm('Yakin ingin menghapus pemeriksaan ini?', [
            'toast' => false,
            'position' => 'center',
            'showConfirmButton' => true,
            'cancelButtonText' => 'Tidak',
            'onConfirmed' => 'hapusPemeriksaan',
        ]);
    }

    public function hapus()
    {
        try {
            DB::table('pemeriksaan_ralan')
                ->where('no_rawat', $this->noRawat)
                ->where('tgl_perawatan', $this->tgl)
                ->where('jam_rawat', $this->jam)
                ->delete();
            $this->getListPemeriksaan();
            $this->alert('success', 'Pemeriksaan berhasil dihapus', [
                'position' =>  'center',
                'timer' =>  3000,
                'toast' =>  false,
            ]);
        } catch (\Exception $e) {
            $this->alert('error', 'Gagal', [
                'position' =>  'center',
                'timer' =>  3000,
                'toast' =>  false,
                'text' =>  $e->getMessage(),
            ]);
        }
    }
}
