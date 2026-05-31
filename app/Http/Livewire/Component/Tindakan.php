<?php

namespace App\Http\Livewire\Component;

use App\Traits\SwalResponse;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

/**
 * Input tindakan/prosedur oleh dokter.
 * Otomatis deteksi konteks ralan vs ranap dari reg_periksa.status_lanjut.
 * Tarif diambil dari jns_perawatan (ralan) / jns_perawatan_inap (ranap)
 * sesuai penjab (kd_pj) + poli/bangsal — sama seperti Khanza desktop.
 */
class Tindakan extends Component
{
    use SwalResponse;

    public $noRawat;
    public $kdDokter;
    public $kdPj;
    public $kdPoli;
    public $kdBangsal;
    public $isRanap = false;
    public $isCollapsed = true;

    public $search = '';
    public $kdJenisPrw;
    public $namaPerawatan;

    protected $listeners = ['deleteTindakan'];

    public function mount($noRawat)
    {
        $this->noRawat  = $noRawat;
        $this->kdDokter = session('username');

        $reg = DB::table('reg_periksa')
            ->where('no_rawat', $noRawat)
            ->select('kd_pj', 'kd_poli', 'status_lanjut')
            ->first();

        $this->kdPj    = $reg->kd_pj ?? '-';
        $this->kdPoli  = $reg->kd_poli ?? '-';
        $this->isRanap = ($reg->status_lanjut ?? '') === 'Ranap';

        if ($this->isRanap) {
            $this->kdBangsal = DB::table('kamar_inap')
                ->join('kamar', 'kamar.kd_kamar', '=', 'kamar_inap.kd_kamar')
                ->where('kamar_inap.no_rawat', $noRawat)
                ->orderByDesc('kamar_inap.tgl_masuk')
                ->value('kamar.kd_bangsal') ?? '-';
        }
    }

    public function render()
    {
        $hasilCari = collect();
        if (strlen($this->search) >= 2 && !$this->kdJenisPrw) {
            $hasilCari = $this->cariTindakan();
        }

        $items = $this->getTindakanTersimpan();

        return view('livewire.component.tindakan', [
            'hasilCari' => $hasilCari,
            'items'     => $items,
        ]);
    }

    private function cariTindakan($pakaiPenjab = true)
    {
        $tbl = $this->isRanap ? 'jns_perawatan_inap' : 'jns_perawatan';

        $q = DB::table($tbl)
            ->where('status', '1')
            ->where('total_byrdr', '>', 0)
            ->where(function ($w) {
                $w->where('nm_perawatan', 'like', '%' . $this->search . '%')
                  ->orWhere('kd_jenis_prw', 'like', $this->search . '%');
            });

        if ($pakaiPenjab) {
            $q->where(function ($w) {
                $w->where('kd_pj', $this->kdPj)->orWhere('kd_pj', '-');
            });
        }

        if ($this->isRanap) {
            $q->where(function ($w) {
                $w->where('kd_bangsal', $this->kdBangsal)->orWhere('kd_bangsal', '-');
            });
        } else {
            $q->where(function ($w) {
                $w->where('kd_poli', $this->kdPoli)->orWhere('kd_poli', '-');
            });
        }

        $hasil = $q->orderBy('nm_perawatan')
            ->limit(10)
            ->get(['kd_jenis_prw', 'nm_perawatan', 'material', 'bhp', 'tarif_tindakandr', 'kso', 'menejemen', 'total_byrdr']);

        // Fallback: kalau tarif penjab pasien belum di-setup, pakai tarif umum
        if ($pakaiPenjab && $hasil->isEmpty()) {
            return $this->cariTindakan(false);
        }

        return $hasil;
    }

    private function getTindakanTersimpan()
    {
        $tbl    = $this->isRanap ? 'rawat_inap_dr' : 'rawat_jl_dr';
        $jnsTbl = $this->isRanap ? 'jns_perawatan_inap' : 'jns_perawatan';

        return DB::table($tbl)
            ->leftJoin($jnsTbl, "$jnsTbl.kd_jenis_prw", '=', "$tbl.kd_jenis_prw")
            ->leftJoin('dokter', "$tbl.kd_dokter", '=', 'dokter.kd_dokter')
            ->where("$tbl.no_rawat", $this->noRawat)
            ->orderByDesc("$tbl.tgl_perawatan")
            ->orderByDesc("$tbl.jam_rawat")
            ->get([
                "$tbl.kd_jenis_prw",
                "$jnsTbl.nm_perawatan",
                "$tbl.tgl_perawatan",
                "$tbl.jam_rawat",
                'dokter.nm_dokter',
                "$tbl.biaya_rawat",
            ]);
    }

    public function pilihTindakan($kode, $nama)
    {
        $this->kdJenisPrw    = $kode;
        $this->namaPerawatan = $nama;
        $this->search        = $nama;
    }

    public function batalPilih()
    {
        $this->reset(['kdJenisPrw', 'namaPerawatan', 'search']);
    }

    public function collapsed()
    {
        $this->isCollapsed = !$this->isCollapsed;
    }

    public function simpan()
    {
        $this->validate(
            ['kdJenisPrw' => 'required'],
            ['kdJenisPrw.required' => 'Pilih tindakan dulu']
        );

        $tbl    = $this->isRanap ? 'rawat_inap_dr' : 'rawat_jl_dr';
        $jnsTbl = $this->isRanap ? 'jns_perawatan_inap' : 'jns_perawatan';

        $sudahAda = DB::table($tbl)
            ->where('no_rawat', $this->noRawat)
            ->where('kd_jenis_prw', $this->kdJenisPrw)
            ->where('kd_dokter', $this->kdDokter)
            ->where('tgl_perawatan', date('Y-m-d'))
            ->exists();

        if ($sudahAda) {
            return $this->dispatchBrowserEvent('swal', $this->toastResponse('Tindakan ini sudah diinput hari ini.', 'warning'));
        }

        // Prefer tarif sesuai penjab pasien, fallback ke tarif manapun untuk tindakan ini
        $tarif = DB::table($jnsTbl)
            ->where('kd_jenis_prw', $this->kdJenisPrw)
            ->orderByRaw('kd_pj = ? DESC', [$this->kdPj])
            ->first();
        if (!$tarif) {
            return $this->dispatchBrowserEvent('swal', $this->toastResponse('Tarif tindakan tidak ditemukan.', 'error'));
        }

        $row = [
            'no_rawat'         => $this->noRawat,
            'kd_jenis_prw'     => $this->kdJenisPrw,
            'kd_dokter'        => $this->kdDokter,
            'tgl_perawatan'    => date('Y-m-d'),
            'jam_rawat'        => date('H:i:s'),
            'material'         => $tarif->material,
            'bhp'              => $tarif->bhp,
            'tarif_tindakandr' => $tarif->tarif_tindakandr,
            'kso'              => $tarif->kso,
            'menejemen'        => $tarif->menejemen,
            'biaya_rawat'      => $tarif->total_byrdr,
        ];

        // rawat_jl_dr punya kolom stts_bayar, rawat_inap_dr tidak
        if (!$this->isRanap) {
            $row['stts_bayar'] = 'Belum';
        }

        DB::table($tbl)->insert($row);

        $this->reset(['kdJenisPrw', 'namaPerawatan', 'search']);
        $this->dispatchBrowserEvent('swal', $this->toastResponse('Tindakan berhasil ditambahkan'));
    }

    public function deleteTindakan($kdJenisPrw)
    {
        $tbl = $this->isRanap ? 'rawat_inap_dr' : 'rawat_jl_dr';

        DB::table($tbl)
            ->where('no_rawat', $this->noRawat)
            ->where('kd_jenis_prw', $kdJenisPrw)
            ->where('kd_dokter', $this->kdDokter)
            ->where('tgl_perawatan', date('Y-m-d'))
            ->delete();

        $this->dispatchBrowserEvent('swal', $this->toastResponse('Tindakan dihapus'));
    }
}
