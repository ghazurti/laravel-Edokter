<?php

namespace App\Http\Livewire\Component;

use App\Traits\SwalResponse;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

/**
 * Persetujuan / Penolakan Tindakan Kedokteran (Informed Consent).
 * Menulis ke tabel Khanza `persetujuan_penolakan_tindakan` persis (37 kolom),
 * format no_pernyataan = PM + YYYYMMDD + 3 digit (sama dengan Khanza desktop).
 */
class PersetujuanTindakan extends Component
{
    use SwalResponse;

    public $noRawat;
    public $kdDokter;
    public $tanggal;
    public $isCollapsed = true;

    // Petugas/perawat pencatat (kolom nip — FK ke tabel petugas, wajib)
    public $nip;
    public $namaPetugas;
    public $searchPetugas = '';

    public $kodeTemplate;

    // Item informasi (teks + konfirmasi "sudah dijelaskan")
    public $diagnosa, $diagnosaK = true;
    public $tindakan, $tindakanK = true;
    public $indikasi, $indikasiK = true;
    public $tataCara, $tataCaraK = true;
    public $tujuan, $tujuanK = true;
    public $risiko, $risikoK = true;
    public $komplikasi, $komplikasiK = true;
    public $prognosis, $prognosisK = true;
    public $alternatif, $alternatifK = true;
    public $biaya = 0, $biayaK = true;
    public $lainLain, $lainLainK = true;

    // Penerima informasi
    public $penerima;
    public $hubungan = 'Diri Sendiri';
    public $alasanDiwakilkan;
    public $jkPenerima = 'L';
    public $tglLahirPenerima;
    public $umurPenerima;
    public $alamatPenerima;
    public $noHp;

    public $pernyataan = 'Persetujuan';
    public $saksiKeluarga;

    protected $listeners = ['deletePersetujuan'];

    protected $rules = [
        'tindakan'  => 'required',
        'penerima'  => 'required',
        'hubungan'  => 'required',
        'nip'       => 'required',
        'pernyataan' => 'required|in:Persetujuan,Penolakan',
    ];

    protected $messages = [
        'tindakan.required' => 'Nama tindakan wajib diisi',
        'penerima.required' => 'Nama penerima informasi wajib diisi',
        'nip.required'      => 'Pilih petugas/perawat pencatat',
    ];

    public function mount($noRawat)
    {
        $this->noRawat  = $noRawat;
        $this->kdDokter = session('username');
        $this->tanggal  = date('Y-m-d');

        // diagnosa default dari diagnosa pasien (kalau ada)
        $diag = DB::table('diagnosa_pasien')
            ->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
            ->where('diagnosa_pasien.no_rawat', $noRawat)
            ->value('penyakit.nm_penyakit');
        $this->diagnosa = $diag ?: '';

        $this->isiDariPasien();
    }

    public function updatedHubungan()
    {
        if ($this->hubungan === 'Diri Sendiri') {
            $this->isiDariPasien();
        }
    }

    private function isiDariPasien()
    {
        if ($this->hubungan !== 'Diri Sendiri') {
            return;
        }
        $p = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->where('reg_periksa.no_rawat', $this->noRawat)
            ->select('pasien.nm_pasien', 'pasien.jk', 'pasien.tgl_lahir', 'pasien.alamat', 'pasien.umur', 'pasien.no_tlp')
            ->first();
        if ($p) {
            $this->penerima        = $p->nm_pasien;
            $this->jkPenerima      = $p->jk === 'P' ? 'P' : 'L';
            $this->tglLahirPenerima = $p->tgl_lahir;
            $this->umurPenerima    = $p->umur;
            $this->alamatPenerima  = $p->alamat;
            $this->noHp            = $p->no_tlp ?? '';
            $this->alasanDiwakilkan = '';
        }
    }

    public function render()
    {
        $hasilPetugas = collect();
        if (strlen($this->searchPetugas) >= 2 && !$this->nip) {
            $hasilPetugas = DB::table('petugas')
                ->where('status', '1')
                ->where(function ($q) {
                    $q->where('nama', 'like', '%' . $this->searchPetugas . '%')
                      ->orWhere('nip', 'like', $this->searchPetugas . '%');
                })
                ->orderBy('nama')->limit(10)->get(['nip', 'nama']);
        }

        $templates = DB::table('template_persetujuan_penolakan_tindakan')
            ->orderBy('kode_template')->get();

        $riwayat = DB::table('persetujuan_penolakan_tindakan')
            ->leftJoin('dokter', 'persetujuan_penolakan_tindakan.kd_dokter', '=', 'dokter.kd_dokter')
            ->where('persetujuan_penolakan_tindakan.no_rawat', $this->noRawat)
            ->orderByDesc('persetujuan_penolakan_tindakan.tanggal')
            ->select(
                'persetujuan_penolakan_tindakan.no_pernyataan',
                'persetujuan_penolakan_tindakan.tanggal',
                'persetujuan_penolakan_tindakan.tindakan',
                'persetujuan_penolakan_tindakan.pernyataan',
                'persetujuan_penolakan_tindakan.penerima_informasi',
                'dokter.nm_dokter'
            )
            ->get();

        return view('livewire.component.persetujuan-tindakan', compact('templates', 'riwayat', 'hasilPetugas'));
    }

    public function pilihPetugas($nip, $nama)
    {
        $this->nip = $nip;
        $this->namaPetugas = $nama;
        $this->searchPetugas = $nama;
    }

    public function batalPetugas()
    {
        $this->reset(['nip', 'namaPetugas', 'searchPetugas']);
    }

    public function collapsed()
    {
        $this->isCollapsed = !$this->isCollapsed;
    }

    public function pilihTemplate()
    {
        if (!$this->kodeTemplate) {
            return;
        }
        $t = DB::table('template_persetujuan_penolakan_tindakan')
            ->where('kode_template', $this->kodeTemplate)->first();
        if (!$t) {
            return;
        }
        $this->diagnosa   = $t->diagnosa;
        $this->tindakan   = $t->tindakan;
        $this->indikasi   = $t->indikasi_tindakan;
        $this->tataCara   = $t->tata_cara;
        $this->tujuan     = $t->tujuan;
        $this->risiko     = $t->risiko;
        $this->komplikasi = $t->komplikasi;
        $this->prognosis  = $t->prognosis;
        $this->alternatif = $t->alternatif_dan_risikonya;
        $this->lainLain   = $t->lain_lain;
        $this->biaya      = $t->biaya;
    }

    private function ya($b)
    {
        return $b ? 'true' : 'false';
    }

    private function generateNoPernyataan()
    {
        $prefix = 'PM' . date('Ymd', strtotime($this->tanggal));
        $last = DB::table('persetujuan_penolakan_tindakan')
            ->where('tanggal', $this->tanggal)
            ->where('no_pernyataan', 'like', $prefix . '%')
            ->orderByDesc('no_pernyataan')
            ->value('no_pernyataan');
        $next = $last ? (int) substr($last, -3) + 1 : 1;
        return $prefix . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }

    public function simpan()
    {
        $this->validate();

        $no = $this->generateNoPernyataan();

        DB::table('persetujuan_penolakan_tindakan')->insert([
            'no_pernyataan' => $no,
            'no_rawat'      => $this->noRawat,
            'tanggal'       => $this->tanggal,
            'diagnosa'      => (string) $this->diagnosa,
            'diagnosa_konfirmasi' => $this->ya($this->diagnosaK),
            'tindakan'      => (string) $this->tindakan,
            'tindakan_konfirmasi' => $this->ya($this->tindakanK),
            'indikasi_tindakan' => (string) $this->indikasi,
            'indikasi_tindakan_konfirmasi' => $this->ya($this->indikasiK),
            'tata_cara'     => (string) $this->tataCara,
            'tata_cara_konfirmasi' => $this->ya($this->tataCaraK),
            'tujuan'        => (string) $this->tujuan,
            'tujuan_konfirmasi' => $this->ya($this->tujuanK),
            'risiko'        => (string) $this->risiko,
            'risiko_konfirmasi' => $this->ya($this->risikoK),
            'komplikasi'    => (string) $this->komplikasi,
            'komplikasi_konfirmasi' => $this->ya($this->komplikasiK),
            'prognosis'     => (string) $this->prognosis,
            'prognosis_konfirmasi' => $this->ya($this->prognosisK),
            'alternatif_dan_risikonya' => (string) $this->alternatif,
            'alternatif_konfirmasi' => $this->ya($this->alternatifK),
            'biaya'         => (float) ($this->biaya ?: 0),
            'biaya_konfirmasi' => $this->ya($this->biayaK),
            'lain_lain'     => (string) $this->lainLain,
            'lain_lain_konfirmasi' => $this->ya($this->lainLainK),
            'kd_dokter'     => $this->kdDokter,
            'nip'           => $this->nip,
            'penerima_informasi' => (string) $this->penerima,
            'alasan_diwakilkan_penerima_informasi' => (string) $this->alasanDiwakilkan,
            'jk_penerima_informasi' => $this->jkPenerima === 'P' ? 'P' : 'L',
            'tanggal_lahir_penerima_informasi' => $this->tglLahirPenerima ?: '0000-00-00',
            'umur_penerima_informasi' => (string) $this->umurPenerima,
            'alamat_penerima_informasi' => (string) $this->alamatPenerima,
            'no_hp'         => (string) $this->noHp,
            'hubungan_penerima_informasi' => $this->hubungan,
            'pernyataan'    => $this->pernyataan,
            'saksi_keluarga' => (string) $this->saksiKeluarga,
        ]);

        $this->dispatchBrowserEvent('swal', $this->toastResponse('Persetujuan/penolakan tindakan tersimpan (' . $no . ')'));
        $this->reset(['nip', 'namaPetugas', 'searchPetugas', 'saksiKeluarga']);
    }

    public function deletePersetujuan($noPernyataan)
    {
        DB::table('persetujuan_penolakan_tindakan')->where('no_pernyataan', $noPernyataan)->delete();
        $this->dispatchBrowserEvent('swal', $this->toastResponse('Data dihapus'));
    }
}
