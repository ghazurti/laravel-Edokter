<?php

namespace App\Http\Livewire\Ralan;

use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class JawabanKonsul extends Component
{
    use LivewireAlert;

    public $noRawat;

    // Header konsul (dari dokter perujuk, read-only)
    public $perujukNama, $perujukPoli, $tglRujukan, $catatanKonsul;

    // Jawaban konsul (diisi dokter konsul)
    public $pemeriksaan, $diagnosa, $saran;

    public $hasData = false;

    public function mount($noRawat)
    {
        $this->noRawat = $noRawat;
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.ralan.jawaban-konsul');
    }

    public function loadData()
    {
        $kdDokter = session('username');

        $rujukan = DB::table('rujukan_internal_poli')
            ->join('rujukan_internal_poli_detail', 'rujukan_internal_poli.no_rawat', '=', 'rujukan_internal_poli_detail.no_rawat')
            ->join('reg_periksa', 'rujukan_internal_poli.no_rawat', '=', 'reg_periksa.no_rawat')
            ->join('dokter as dperujuk', 'reg_periksa.kd_dokter', '=', 'dperujuk.kd_dokter')
            ->join('poliklinik as pperujuk', 'reg_periksa.kd_poli', '=', 'pperujuk.kd_poli')
            ->where('rujukan_internal_poli.no_rawat', $this->noRawat)
            ->where('rujukan_internal_poli.kd_dokter', $kdDokter)
            ->select(
                'rujukan_internal_poli.tanggal',
                'rujukan_internal_poli_detail.konsul',
                'rujukan_internal_poli_detail.pemeriksaan',
                'rujukan_internal_poli_detail.diagnosa',
                'rujukan_internal_poli_detail.saran',
                'dperujuk.nm_dokter as perujuk_nama',
                'pperujuk.nm_poli as perujuk_poli'
            )
            ->first();

        if ($rujukan) {
            $this->hasData       = true;
            $this->perujukNama   = $rujukan->perujuk_nama;
            $this->perujukPoli   = $rujukan->perujuk_poli;
            $this->tglRujukan    = $rujukan->tanggal;
            $this->catatanKonsul = $rujukan->konsul;
            $this->pemeriksaan   = $rujukan->pemeriksaan;
            $this->diagnosa      = $rujukan->diagnosa;
            $this->saran         = $rujukan->saran;
        }
    }

    public function simpan()
    {
        try {
            if (empty(trim((string) $this->saran))) {
                $this->alert('warning', 'Saran/Jawaban konsul wajib diisi', [
                    'position' => 'center', 'timer' => 2500, 'toast' => false,
                ]);
                return;
            }

            DB::table('rujukan_internal_poli_detail')
                ->where('no_rawat', $this->noRawat)
                ->update([
                    'pemeriksaan' => $this->pemeriksaan ?? '',
                    'diagnosa'    => $this->diagnosa ?? '',
                    'saran'       => $this->saran,
                ]);

            $this->loadData();
            $this->alert('success', 'Jawaban konsul berhasil disimpan', [
                'position' => 'top-end', 'timer' => 2000, 'toast' => true,
            ]);
        } catch (\Exception $e) {
            $this->alert('error', 'Gagal: ' . $e->getMessage(), [
                'position' => 'center', 'timer' => 4000, 'toast' => false,
            ]);
        }
    }
}
