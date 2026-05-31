<?php

namespace App\Http\Livewire\Ralan;

use App\Traits\SwalResponse;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class PermintaanKonsultasi extends Component
{
    use SwalResponse;

    public $noRawat;
    public $noPermintaan, $tanggal, $jenisPermintaan = 'Konsultasi';
    public $kdDokter = '', $nmDokter = '', $kdDokterDikonsuli = '', $nmDokterDikonsuli = '';
    public $diagnosaKerja = '', $uraianKonsultasi = '';
    public $daftarKonsultasi = [];
    public $editMode = false;

    protected $rules = [
        'kdDokterDikonsuli' => 'required',
        'diagnosaKerja'     => 'required',
    ];

    protected $messages = [
        'kdDokterDikonsuli.required' => 'Dokter yang dikonsuli harus diisi',
        'diagnosaKerja.required'     => 'Diagnosa kerja harus diisi',
    ];

    protected $listeners = ['deleteKonsultasi', 'setDokterKonsul', 'setDokterDikonsuli'];

    public function mount($noRawat)
    {
        $this->noRawat = $noRawat;
        $this->tanggal = now()->format('Y-m-d\TH:i');
        $this->loadDokterLogin();
        $this->getDaftarKonsultasi();
    }

    public function hydrate()
    {
        $this->getDaftarKonsultasi();
    }

    public function render()
    {
        $dokterList = DB::table('dokter')
            ->whereNotIn('kd_dokter', ['-', '--'])
            ->select('kd_dokter', 'nm_dokter')
            ->orderBy('nm_dokter')
            ->get();

        return view('livewire.ralan.permintaan-konsultasi', compact('dokterList'));
    }

    private function loadDokterLogin()
    {
        $username = session()->get('username');
        $dokter = DB::table('dokter')->where('kd_dokter', $username)->first();
        if ($dokter) {
            $this->kdDokter = $dokter->kd_dokter;
            $this->nmDokter = $dokter->nm_dokter;
        }
    }

    private function generateNoPermintaan()
    {
        $last = DB::table('konsultasi_medik')
            ->whereDate('tanggal', today())
            ->selectRaw('IFNULL(MAX(CAST(RIGHT(no_permintaan, 4) AS UNSIGNED)), 0) as no')
            ->first();

        $next = sprintf('%04d', ($last->no + 1));
        return 'KM' . now()->format('Ymd') . $next;
    }

    public function simpan()
    {
        $this->validate();

        // Input datetime-local -> format datetime DB (Y-m-d H:i:s)
        $tanggalDb = \Carbon\Carbon::parse($this->tanggal)->format('Y-m-d H:i:s');

        try {
            if ($this->editMode) {
                DB::table('konsultasi_medik')
                    ->where('no_permintaan', $this->noPermintaan)
                    ->update([
                        'tanggal'            => $tanggalDb,
                        'jenis_permintaan'   => $this->jenisPermintaan,
                        'kd_dokter'          => $this->kdDokter,
                        'kd_dokter_dikonsuli'=> $this->kdDokterDikonsuli,
                        'diagnosa_kerja'     => $this->diagnosaKerja,
                        'uraian_konsultasi'  => $this->uraianKonsultasi,
                    ]);
                $this->dispatchBrowserEvent('swal', $this->toastResponse('Data konsultasi berhasil diubah'));
            } else {
                $noPermintaan = $this->generateNoPermintaan();
                DB::table('konsultasi_medik')->insert([
                    'no_permintaan'      => $noPermintaan,
                    'no_rawat'           => $this->noRawat,
                    'tanggal'            => $tanggalDb,
                    'jenis_permintaan'   => $this->jenisPermintaan,
                    'kd_dokter'          => $this->kdDokter,
                    'kd_dokter_dikonsuli'=> $this->kdDokterDikonsuli,
                    'diagnosa_kerja'     => $this->diagnosaKerja,
                    'uraian_konsultasi'  => $this->uraianKonsultasi,
                ]);
                $this->dispatchBrowserEvent('swal', $this->toastResponse('Permintaan konsultasi berhasil disimpan'));
            }

            $this->getDaftarKonsultasi();
            $this->resetForm();
        } catch (\Illuminate\Database\QueryException $ex) {
            $this->dispatchBrowserEvent('swal', $this->toastResponse($ex->getMessage() ?? 'Gagal menyimpan', 'error'));
        }
    }

    public function edit($noPermintaan)
    {
        $data = DB::table('konsultasi_medik')
            ->join('dokter as dk', 'konsultasi_medik.kd_dokter', '=', 'dk.kd_dokter')
            ->join('dokter as dd', 'konsultasi_medik.kd_dokter_dikonsuli', '=', 'dd.kd_dokter')
            ->where('konsultasi_medik.no_permintaan', $noPermintaan)
            ->selectRaw('konsultasi_medik.*, dk.nm_dokter as nm_dokter_konsul, dd.nm_dokter as nm_dokter_dikonsuli')
            ->first();

        if ($data) {
            $this->editMode           = true;
            $this->noPermintaan       = $data->no_permintaan;
            $this->tanggal            = \Carbon\Carbon::parse($data->tanggal)->format('Y-m-d\TH:i');
            $this->jenisPermintaan    = $data->jenis_permintaan;
            $this->kdDokter           = $data->kd_dokter;
            $this->nmDokter           = $data->nm_dokter_konsul;
            $this->kdDokterDikonsuli  = $data->kd_dokter_dikonsuli;
            $this->nmDokterDikonsuli  = $data->nm_dokter_dikonsuli;
            $this->diagnosaKerja      = $data->diagnosa_kerja;
            $this->uraianKonsultasi   = $data->uraian_konsultasi;
        }
    }

    public function konfirmasiHapus($noPermintaan)
    {
        $this->dispatchBrowserEvent('swal:confirm', [
            'title'             => 'Konfirmasi Hapus',
            'text'              => 'Hapus permintaan konsultasi ini?',
            'type'              => 'warning',
            'confirmButtonText' => 'Ya, Hapus',
            'cancelButtonText'  => 'Batal',
            'function'          => 'deleteKonsultasi',
            'params'            => [$noPermintaan],
        ]);
    }

    public function deleteKonsultasi($noPermintaan)
    {
        try {
            DB::table('konsultasi_medik')->where('no_permintaan', $noPermintaan)->delete();
            $this->getDaftarKonsultasi();
            $this->resetForm();
            $this->dispatchBrowserEvent('swal', $this->toastResponse('Data berhasil dihapus'));
        } catch (\Illuminate\Database\QueryException $ex) {
            $this->dispatchBrowserEvent('swal', $this->toastResponse('Gagal menghapus data', 'error'));
        }
    }

    public function getDaftarKonsultasi()
    {
        $this->daftarKonsultasi = DB::table('konsultasi_medik')
            ->leftJoin('dokter as dk', 'konsultasi_medik.kd_dokter', '=', 'dk.kd_dokter')
            ->leftJoin('dokter as dd', 'konsultasi_medik.kd_dokter_dikonsuli', '=', 'dd.kd_dokter')
            ->leftJoin('jawaban_konsultasi_medik', 'konsultasi_medik.no_permintaan', '=', 'jawaban_konsultasi_medik.no_permintaan')
            ->where('konsultasi_medik.no_rawat', $this->noRawat)
            ->selectRaw('konsultasi_medik.*, dk.nm_dokter as nm_dokter_konsul, dd.nm_dokter as nm_dokter_dikonsuli,
                IF(jawaban_konsultasi_medik.no_permintaan IS NULL, "Menunggu", "Sudah Dijawab") as status_jawab')
            ->orderBy('konsultasi_medik.tanggal', 'desc')
            ->get();
    }

    public function resetForm()
    {
        $this->editMode          = false;
        $this->noPermintaan      = null;
        $this->tanggal           = now()->format('Y-m-d\TH:i');
        $this->jenisPermintaan   = 'Konsultasi';
        $this->kdDokterDikonsuli = '';
        $this->nmDokterDikonsuli = '';
        $this->diagnosaKerja     = '';
        $this->uraianKonsultasi  = '';
        $this->loadDokterLogin();
    }
}
