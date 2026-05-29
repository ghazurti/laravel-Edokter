<?php

namespace App\Http\Livewire\Ranap;

use App\Traits\SwalResponse;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ResepPulang extends Component
{
    use SwalResponse;

    public $noRawat;
    public $kdDokter;
    public $noPermintaan;
    public $search = '';
    public $kodeBrng;
    public $namaBrng;
    public $jml;
    public $dosis;
    public $isCollapsed = true;

    protected $rules = [
        'kodeBrng' => 'required',
        'jml'      => 'required|numeric|min:1',
        'dosis'    => 'required|max:150',
    ];

    protected $messages = [
        'kodeBrng.required' => 'Pilih obat dulu',
        'jml.required'      => 'Jumlah wajib diisi',
        'jml.numeric'       => 'Jumlah harus angka',
        'jml.min'           => 'Jumlah minimal 1',
        'dosis.required'    => 'Aturan pakai wajib diisi',
        'dosis.max'         => 'Aturan pakai maksimal 150 karakter',
    ];

    protected $listeners = ['deleteResepPulang'];

    public function mount($noRawat)
    {
        $this->noRawat  = $noRawat;
        $this->kdDokter = session('username');
        $this->loadPermintaanAktif();
    }

    private function loadPermintaanAktif()
    {
        $this->noPermintaan = DB::table('permintaan_resep_pulang')
            ->where('no_rawat', $this->noRawat)
            ->where('kd_dokter', $this->kdDokter)
            ->where('status', 'Belum')
            ->orderByDesc('tgl_permintaan')
            ->orderByDesc('jam')
            ->value('no_permintaan');
    }

    public function render()
    {
        $hasilCari = [];
        if (strlen($this->search) >= 2 && !$this->kodeBrng) {
            $hasilCari = DB::table('databarang')
                ->where('status', '1')
                ->where(function ($q) {
                    $q->where('nama_brng', 'like', '%' . $this->search . '%')
                      ->orWhere('kode_brng', 'like', $this->search . '%');
                })
                ->select('kode_brng', 'nama_brng', 'kode_sat')
                ->limit(10)
                ->get();
        }

        $items = collect();
        if ($this->noPermintaan) {
            $items = DB::table('detail_permintaan_resep_pulang as d')
                ->join('databarang as b', 'b.kode_brng', '=', 'd.kode_brng')
                ->where('d.no_permintaan', $this->noPermintaan)
                ->select('d.kode_brng', 'b.nama_brng', 'b.kode_sat', 'd.jml', 'd.dosis')
                ->get();
        }

        $riwayat = DB::table('permintaan_resep_pulang')
            ->where('no_rawat', $this->noRawat)
            ->orderByDesc('tgl_permintaan')
            ->orderByDesc('jam')
            ->select('no_permintaan', 'tgl_permintaan', 'jam', 'status', 'kd_dokter')
            ->limit(5)
            ->get();

        return view('livewire.ranap.resep-pulang', [
            'hasilCari' => $hasilCari,
            'items'     => $items,
            'riwayat'   => $riwayat,
        ]);
    }

    public function collapsed()
    {
        $this->isCollapsed = !$this->isCollapsed;
    }

    public function pilihObat($kode, $nama)
    {
        $this->kodeBrng = $kode;
        $this->namaBrng = $nama;
        $this->search   = $nama;
    }

    public function batalPilih()
    {
        $this->reset(['kodeBrng', 'namaBrng', 'search']);
    }

    private function generateNoPermintaan()
    {
        $prefix = 'RP' . date('Ymd');
        $last = DB::table('permintaan_resep_pulang')
            ->where('no_permintaan', 'like', $prefix . '%')
            ->orderByDesc('no_permintaan')
            ->value('no_permintaan');

        $next = $last
            ? (int) substr($last, -4) + 1
            : 1;

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }

    public function simpan()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            if (!$this->noPermintaan) {
                $this->noPermintaan = $this->generateNoPermintaan();
                DB::table('permintaan_resep_pulang')->insert([
                    'no_permintaan' => $this->noPermintaan,
                    'tgl_permintaan' => date('Y-m-d'),
                    'jam'           => date('H:i:s'),
                    'no_rawat'      => $this->noRawat,
                    'kd_dokter'     => $this->kdDokter,
                    'status'        => 'Belum',
                    'tgl_validasi'  => '0000-00-00',
                    'jam_validasi'  => '00:00:00',
                ]);
            }

            $sudahAda = DB::table('detail_permintaan_resep_pulang')
                ->where('no_permintaan', $this->noPermintaan)
                ->where('kode_brng', $this->kodeBrng)
                ->exists();

            if ($sudahAda) {
                DB::rollBack();
                return $this->dispatchBrowserEvent('swal:error', [
                    'title' => 'Obat ini sudah ada di permintaan, hapus dulu kalau mau ganti jumlah/dosis.',
                ]);
            }

            DB::table('detail_permintaan_resep_pulang')->insert([
                'no_permintaan' => $this->noPermintaan,
                'kode_brng'     => $this->kodeBrng,
                'jml'           => (float) $this->jml,
                'dosis'         => $this->dosis,
            ]);

            DB::commit();
            $this->reset(['kodeBrng', 'namaBrng', 'search', 'jml', 'dosis']);
            $this->dispatchBrowserEvent('swal:success', ['title' => 'Obat ditambahkan ke permintaan resep pulang']);
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->dispatchBrowserEvent('swal:error', ['title' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    public function deleteResepPulang($kodeBrng)
    {
        if (!$this->noPermintaan) {
            return;
        }

        DB::table('detail_permintaan_resep_pulang')
            ->where('no_permintaan', $this->noPermintaan)
            ->where('kode_brng', $kodeBrng)
            ->delete();

        $sisa = DB::table('detail_permintaan_resep_pulang')
            ->where('no_permintaan', $this->noPermintaan)
            ->count();

        if ($sisa === 0) {
            DB::table('permintaan_resep_pulang')
                ->where('no_permintaan', $this->noPermintaan)
                ->where('status', 'Belum')
                ->delete();
            $this->noPermintaan = null;
        }

        $this->dispatchBrowserEvent('swal:success', ['title' => 'Item dihapus']);
    }
}
