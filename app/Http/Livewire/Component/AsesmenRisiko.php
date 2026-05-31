<?php

namespace App\Http\Livewire\Component;

use App\Support\AsesmenRisiko as Def;
use App\Traits\SwalResponse;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

/**
 * Komponen generik Asesmen Risiko (akreditasi).
 * Satu komponen melayani semua instrumen (jatuh dewasa/anak/geriatri,
 * dekubitus, nyeri, gizi) berdasarkan definisi di App\Support\AsesmenRisiko.
 * Menulis ke tabel Khanza apa adanya; skor & kategori dihitung otomatis.
 */
class AsesmenRisiko extends Component
{
    use SwalResponse;

    public $noRawat;
    public $instrumen;   // key: jatuh-dewasa, ...
    public $modul;       // ralan|igd|ranap
    public $readonly = false; // true = tugas perawat, dokter hanya review
    public $isCollapsed = true;

    public array $jawab = [];

    // Petugas pencatat (kolom nip — FK ke petugas)
    public $nip;
    public $namaPetugas;
    public $searchPetugas = '';

    public function mount($noRawat, $instrumen, $modul = 'ranap', $readonly = false)
    {
        $this->noRawat = $noRawat;
        $this->instrumen = $instrumen;
        $this->modul = $modul;
        $this->readonly = $readonly;

        $def = Def::definisi($instrumen);
        if ($def && !$readonly) {
            foreach ($def['baris'] as $b) {
                $tipe = $b['tipe'] ?? 'pilih';
                if ($tipe === 'teks') {
                    $this->jawab[$b['col']] = '';
                } else {
                    // default = opsi pertama (index 0), sama seperti default Khanza
                    $opsi = Def::opsiEnum($def['tabel'], $b['col']);
                    $this->jawab[$b['col']] = $opsi[0] ?? '';
                }
            }
        }

        // prefill BB/TB untuk gizi dari pemeriksaan terakhir (kalau ada)
        if ($instrumen === 'gizi' && !$readonly) {
            $this->prefillGizi();
        }
    }

    private function prefillGizi(): void
    {
        foreach (['pemeriksaan_ranap', 'pemeriksaan_ralan'] as $tbl) {
            try {
                $r = DB::table($tbl)->where('no_rawat', $this->noRawat)
                    ->orderByDesc('tgl_perawatan')->orderByDesc('jam_rawat')
                    ->first(['berat', 'tinggi']);
                if ($r) {
                    if (!empty($r->berat))  $this->jawab['skrining_bb'] = (string) $r->berat;
                    if (!empty($r->tinggi)) $this->jawab['skrining_tb'] = (string) $r->tinggi;
                    return;
                }
            } catch (\Throwable $e) {
                // tabel beda versi — abaikan
            }
        }
    }

    /** Hitung skor index opsi terpilih untuk satu baris. */
    private function skorBaris(array $def, array $b): int
    {
        if (empty($b['skor_col']) || !isset($b['skor'])) {
            return 0;
        }
        $opsi = Def::opsiEnum($def['tabel'], $b['col']);
        $idx = array_search($this->jawab[$b['col']] ?? null, $opsi, true);
        if ($idx === false) {
            return 0;
        }
        return (int) ($b['skor'][$idx] ?? 0);
    }

    private function hitungTotal(array $def): int
    {
        $tot = 0;
        foreach ($def['baris'] as $b) {
            $tot += $this->skorBaris($def, $b);
        }
        return $tot;
    }

    public function render()
    {
        $def = Def::definisi($this->instrumen);

        // opsi enum per baris (untuk select)
        $opsiBaris = [];
        foreach ($def['baris'] as $b) {
            if (($b['tipe'] ?? 'pilih') !== 'teks') {
                $opsiBaris[$b['col']] = Def::opsiEnum($def['tabel'], $b['col']);
            }
        }

        $total = $def['total_col'] ? $this->hitungTotal($def) : null;
        $hasil = null;
        $saran = null;
        if ($def['kategori']) {
            [$hasil, $saran] = ($def['kategori'])($total ?? 0);
        }

        // pencarian petugas
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

        // riwayat penilaian
        $riwayat = collect();
        try {
            $q = DB::table($def['tabel'])
                ->leftJoin('petugas', $def['tabel'] . '.nip', '=', 'petugas.nip')
                ->where($def['tabel'] . '.no_rawat', $this->noRawat)
                ->orderByDesc($def['tabel'] . '.tanggal');
            $sel = [$def['tabel'] . '.tanggal', 'petugas.nama as petugas'];
            if ($def['total_col']) {
                $sel[] = $def['tabel'] . '.' . $def['total_col'] . ' as total';
            }
            if ($def['hasil_col']) {
                $sel[] = $def['tabel'] . '.' . $def['hasil_col'] . ' as hasil';
            }
            $riwayat = $q->limit(20)->get($sel);
        } catch (\Throwable $e) {
            $riwayat = collect();
        }

        return view('livewire.component.asesmen-risiko', compact(
            'def', 'opsiBaris', 'total', 'hasil', 'saran', 'hasilPetugas', 'riwayat'
        ));
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

    public function simpan()
    {
        if ($this->readonly) {
            return; // instrumen tugas perawat — dokter hanya review
        }
        if (!$this->nip) {
            $this->dispatchBrowserEvent('swal', $this->toastResponse('Pilih petugas pencatat dulu', 'error'));
            return;
        }

        $def = Def::definisi($this->instrumen);
        $now = date('Y-m-d H:i:s');

        $data = [
            'no_rawat' => $this->noRawat,
            'tanggal'  => $now,
            'nip'      => $this->nip,
        ];

        foreach ($def['baris'] as $b) {
            $data[$b['col']] = (string) ($this->jawab[$b['col']] ?? '');
            if (!empty($b['skor_col'])) {
                $data[$b['skor_col']] = $this->skorBaris($def, $b);
            }
        }

        $total = $def['total_col'] ? $this->hitungTotal($def) : null;
        if ($def['total_col']) {
            $data[$def['total_col']] = $total;
        }
        if ($def['kategori']) {
            [$hasil, $saran] = ($def['kategori'])($total ?? 0);
            if ($def['hasil_col']) {
                $data[$def['hasil_col']] = $hasil;
            }
            if (!empty($def['saran_col'])) {
                $data[$def['saran_col']] = (string) $saran;
            }
        }

        try {
            DB::table($def['tabel'])->insert($data);
        } catch (\Throwable $e) {
            $this->dispatchBrowserEvent('swal', $this->toastResponse('Gagal simpan: ' . $e->getMessage(), 'error'));
            return;
        }

        $this->dispatchBrowserEvent('swal', $this->toastResponse('Asesmen tersimpan'));
    }

    public function hapus($tanggal)
    {
        if ($this->readonly) {
            return; // tidak boleh hapus data perawat dari E-Dokter
        }
        $def = Def::definisi($this->instrumen);
        DB::table($def['tabel'])
            ->where('no_rawat', $this->noRawat)
            ->where('tanggal', $tanggal)
            ->delete();
        $this->dispatchBrowserEvent('swal', $this->toastResponse('Data dihapus'));
    }
}
