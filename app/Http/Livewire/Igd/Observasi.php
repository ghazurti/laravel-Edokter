<?php

namespace App\Http\Livewire\Igd;

use App\Traits\SwalResponse;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Observasi extends Component
{
    use SwalResponse, LivewireAlert;

    public $noRawat;
    public $listObservasi = [];
    public $gcs, $td, $hr, $rr, $suhu, $spo2;

    public function mount($noRawat)
    {
        $this->noRawat = $noRawat;
        $this->loadObservasi();
    }

    public function render()
    {
        return view('livewire.igd.observasi');
    }

    public function loadObservasi()
    {
        $this->listObservasi = DB::table('catatan_observasi_igd')
            ->join('pegawai', 'catatan_observasi_igd.nip', '=', 'pegawai.nik')
            ->where('catatan_observasi_igd.no_rawat', $this->noRawat)
            ->orderBy('catatan_observasi_igd.tgl_perawatan', 'desc')
            ->orderBy('catatan_observasi_igd.jam_rawat', 'desc')
            ->select('catatan_observasi_igd.*', 'pegawai.nama')
            ->get();
    }

    public function simpan()
    {
        try {
            if (empty($this->gcs) && empty($this->td) && empty($this->hr)
                && empty($this->rr) && empty($this->suhu) && empty($this->spo2)) {
                $this->alert('warning', 'Minimal satu kolom observasi harus diisi', [
                    'position' => 'center', 'timer' => 2500, 'toast' => false,
                ]);
                return;
            }

            $nip = $this->resolvePetugasNip();
            if (!$nip) {
                $this->alert('error', 'Akun Anda tidak terdaftar di tabel petugas. Hubungi admin SIMRS.', [
                    'position' => 'center', 'timer' => 4000, 'toast' => false,
                ]);
                return;
            }

            DB::table('catatan_observasi_igd')->insert([
                'no_rawat'    => $this->noRawat,
                'tgl_perawatan' => date('Y-m-d'),
                'jam_rawat'   => date('H:i:s'),
                'gcs'         => $this->gcs ?: '-',
                'td'          => $this->td ?: '-',
                'hr'          => $this->hr ?: '-',
                'rr'          => $this->rr ?: '-',
                'suhu'        => $this->suhu ?: '-',
                'spo2'        => $this->spo2 ?: '-',
                'nip'         => $nip,
            ]);
            $this->loadObservasi();
            $this->alert('success', 'Observasi berhasil ditambahkan', [
                'position' => 'top-end', 'timer' => 2000, 'toast' => true,
            ]);
        } catch (\Exception $e) {
            $this->alert('error', 'Gagal: ' . $e->getMessage(), [
                'position' => 'center', 'timer' => 4000, 'toast' => false,
            ]);
        }
    }

    private function resolvePetugasNip()
    {
        $username = session('username');
        if (!$username) return null;

        if (DB::table('petugas')->where('nip', $username)->exists()) {
            return $username;
        }

        $dokter = DB::table('dokter')->where('kd_dokter', $username)->first();
        if ($dokter && !empty($dokter->nik)
            && DB::table('petugas')->where('nip', $dokter->nik)->exists()) {
            return $dokter->nik;
        }

        return null;
    }

    public function hapus($tgl, $jam)
    {
        try {
            DB::table('catatan_observasi_igd')
                ->where('no_rawat', $this->noRawat)
                ->where('tgl_perawatan', $tgl)
                ->where('jam_rawat', $jam)
                ->delete();
            $this->loadObservasi();
        } catch (\Exception $e) {
            $this->alert('error', 'Gagal hapus: ' . $e->getMessage(), [
                'position' => 'center', 'timer' => 3000, 'toast' => false,
            ]);
        }
    }
}
