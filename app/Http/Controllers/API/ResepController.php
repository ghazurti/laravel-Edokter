<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\EnkripsiData;

class ResepController extends Controller
{
    use EnkripsiData;

    public function getObatRanap(Request $request, $bangsal)
    {
        $q = $request->get('q');
        $que = '%' . $q . '%';

        // $depo = DB::table('set_depo_ranap')
        //     ->where('kd_bangsal', $bangsal)
        //     ->first();

        $obat = DB::table('databarang')
            ->join('gudangbarang', 'databarang.kode_brng', '=', 'gudangbarang.kode_brng')
            ->where('status', '1')
            ->where('gudangbarang.stok', '>', '0')
            ->where('gudangbarang.kd_bangsal', $bangsal)
            ->where(function ($query) use ($que) {
                $query->where('databarang.kode_brng', 'like', $que)
                    ->orWhere('databarang.nama_brng', 'like', $que);
            })
            ->selectRaw('gudangbarang.kode_brng AS id, databarang.nama_brng AS text')
            ->get();
        return response()->json($obat, 200);
    }

    public function getObatRalan(Request $request, $poli)
    {
        $q = $request->get('q');
        $que = '%' . $q . '%';

        $depo = DB::table('set_depo_ralan')
            ->where('kd_poli', $poli)
            ->first();

        $obat = DB::table('databarang')
            ->join('gudangbarang', 'databarang.kode_brng', '=', 'gudangbarang.kode_brng')
            ->where('status', '1')
            ->where('gudangbarang.stok', '>', '0')
            ->where('gudangbarang.kd_bangsal', $depo->kd_bangsal)
            ->where(function ($query) use ($que) {
                $query->where('databarang.kode_brng', 'like', $que)
                    ->orWhere('databarang.nama_brng', 'like', $que);
            })
            ->selectRaw('gudangbarang.kode_brng AS id, databarang.nama_brng AS text')
            ->get();
        return response()->json($obat, 200);
    }

    public function getObatLuar(Request $request)
    {
        $q = $request->get('q');
        $que = '%' . $q . '%';

        $obat = DB::table('databarang')
            ->where('status', '1')
            ->where(function ($query) use ($que) {
                $query->where('databarang.kode_brng', 'like', $que)
                    ->orWhere('databarang.nama_brng', 'like', $que);
            })
            ->selectRaw('databarang.kode_brng AS id, databarang.nama_brng AS text')
            ->get();
        return response()->json($obat, 200);
    }

    public function getDataObat(Request $request, $kdObat)
    {
        $input = $request->all();
        $status = $input['status'];
        $kode = $input['kode'];
        $bangsal = "";
        if ($status == 'ralan') {
            $db = DB::table('set_depo_ralan')->where('kd_poli', $kode)->first();
            $bangsal = $db->kd_bangsal;
        } else {
            $db = DB::table('set_depo_ranap')->where('kd_bangsal', $kode)->first();
            $bangsal = $db->kd_depo;
        }
        $maxTgl = DB::table('riwayat_barang_medis')->where('kode_brng', $kdObat)->where('kd_bangsal', $bangsal)->max('tanggal');
        $maxJam = DB::table('riwayat_barang_medis')->where('kode_brng', $kdObat)->where('tanggal', $maxTgl)->where('kd_bangsal', $bangsal)->max('jam');
        $data = DB::table('databarang')
            ->join('riwayat_barang_medis', 'databarang.kode_brng', '=', 'riwayat_barang_medis.kode_brng')
            ->where('databarang.kode_brng', $kdObat)
            ->where('riwayat_barang_medis.tanggal', $maxTgl)
            ->where('riwayat_barang_medis.jam', $maxJam)
            ->select('databarang.*', 'riwayat_barang_medis.stok_akhir')
            ->first();

        return response()->json($data);
    }

    /**
     * Cari restriksi obat (fallback ke ALL). Return null jika tidak ada.
     */
    private function cariRestriksi($kodeObat, $kdPj)
    {
        $sql = "SELECT ro.max_jml, ro.keterangan
                FROM restriksi_obat ro
                INNER JOIN databarang db ON db.kode_brng = ?
                WHERE ro.kode_brng = ?
                  AND ro.aktif = 'Y'
                  AND (
                        (ro.kdjenis = db.kdjns AND ro.kd_pj = ?)
                     OR (ro.kdjenis = db.kdjns AND ro.kd_pj = 'ALL')
                     OR (ro.kdjenis = 'ALL'    AND ro.kd_pj = ?)
                     OR (ro.kdjenis = 'ALL'    AND ro.kd_pj = 'ALL')
                  )
                ORDER BY CASE
                   WHEN ro.kdjenis = db.kdjns AND ro.kd_pj = ? THEN 1
                   WHEN ro.kdjenis = db.kdjns                   THEN 2
                   WHEN ro.kd_pj   = ?                          THEN 3
                   ELSE 4
                END
                LIMIT 1";
        $rows = DB::select($sql, [$kodeObat, $kodeObat, $kdPj, $kdPj, $kdPj, $kdPj]);
        return $rows[0] ?? null;
    }

    public function getRestriksiObat($kodeObat, $noRawat)
    {
        $noRawat = $this->decryptData($noRawat);
        $reg = DB::table('reg_periksa')->where('no_rawat', $noRawat)->first();
        if (!$reg) {
            return response()->json(null);
        }
        $row = $this->cariRestriksi($kodeObat, $reg->kd_pj);
        if (!$row || $row->max_jml <= 0) {
            return response()->json(null);
        }
        return response()->json([
            'max_jml'     => (float) $row->max_jml,
            'keterangan'  => $row->keterangan ?: 'Obat dengan batas peresepan',
        ]);
    }

    public function postResep(Request $request, $noRawat)
    {
        $dokter = session()->get('username');
        $resObat = $request->get('obat');
        $resJml = $request->get('jumlah');
        $resAturan = $request->get('aturan_pakai');
        $status = $request->get('status');
        $kode = $request->get('kode');
        $noRawat = $this->decryptData($noRawat);
        $bangsal = "";

        // Cek Restriksi Obat BPJS — hard-block jika total jml melebihi max_jml
        $reg = DB::table('reg_periksa')->where('no_rawat', $noRawat)->first();
        if ($reg) {
            $totalPerObat = [];
            for ($i = 0; $i < count($resObat); $i++) {
                $kodeBrng = $resObat[$i];
                $jml      = (float) ($resJml[$i] ?? 0);
                if (empty($kodeBrng) || $jml <= 0) continue;
                $totalPerObat[$kodeBrng] = ($totalPerObat[$kodeBrng] ?? 0) + $jml;
            }
            $pelanggaran = [];
            foreach ($totalPerObat as $kodeBrng => $total) {
                $row = $this->cariRestriksi($kodeBrng, $reg->kd_pj);
                if ($row && $row->max_jml > 0 && $total > $row->max_jml) {
                    $nama = DB::table('databarang')->where('kode_brng', $kodeBrng)->value('nama_brng') ?: $kodeBrng;
                    $pelanggaran[] = "- {$nama} : diminta " . (int)$total . ", maksimal " . (int)$row->max_jml;
                }
            }
            if (!empty($pelanggaran)) {
                return response()->json([
                    'status' => 'gagal',
                    'pesan'  => "RESTRIKSI OBAT BPJS DILANGGAR — resep tidak dapat disimpan:\n\n"
                              . implode("\n", $pelanggaran)
                              . "\n\nSilakan kurangi jumlah obat sesuai batas restriksi.",
                ]);
            }
        }

        DB::beginTransaction();
        try {
            if ($status == 'Ralan') {
                $db = DB::table('set_depo_ralan')->where('kd_poli', $kode)->first();
                $bangsal = $db->kd_bangsal;
            } else {
                $db = DB::table('set_depo_ranap')->where('kd_bangsal', $kode)->first();
                $bangsal = $db->kd_depo;
            }

            for ($i = 0; $i < count($resObat); $i++) {
                $obat = $resObat[$i];
                $jml = $resJml[$i];
                $aturan = $resAturan[$i] ?? '-';

                if (empty($jml) || $jml < 1) {
                    continue;
                }

                $maxTgl = DB::table('riwayat_barang_medis')->where('kode_brng', $obat)->where('kd_bangsal', $bangsal)->max('tanggal');
                $maxJam = DB::table('riwayat_barang_medis')->where('kode_brng', $obat)->where('tanggal', $maxTgl)->where('kd_bangsal',  $bangsal)->max('jam');
                $maxStok = DB::table('riwayat_barang_medis')->where('kode_brng', $obat)->where('kd_bangsal', $bangsal)->where('tanggal', $maxTgl)->where('jam', $maxJam)->max('stok_akhir');

                if ($maxStok < $jml) {
                    continue;
                }

                $resep = DB::table('resep_obat')->where('no_rawat', $noRawat)->where('tgl_peresepan', date('Y-m-d'))->first();
                if (!empty($resep)) {
                    if ($resep->tgl_perawatan != '0000-00-00') {
                        return response()->json([
                            'status' => 'gagal',
                            'pesan' => 'Resep obat sudah tervalidasi'
                        ]);
                    }
                }
                $no = DB::table('resep_obat')->where('tgl_perawatan', 'like', '%' . date('Y-m-d') . '%')->orWhere('tgl_peresepan', 'like', '%' . date('Y-m-d') . '%')->selectRaw("ifnull(MAX(CONVERT(RIGHT(no_resep,4),signed)),0) as resep")->first();
                $maxNo = substr($no->resep, 0, 4);
                $nextNo = sprintf('%04s', ($maxNo + 1));
                $tgl = date('Ymd');
                $noResep = $tgl . '' . $nextNo;

                if ($resep) {
                    DB::table('resep_dokter')->insert([
                        'no_resep' => $resep->no_resep,
                        'kode_brng' => $obat,
                        'jml' => $jml,
                        'aturan_pakai' => $aturan,
                    ]);
                } else {
                    DB::table('resep_obat')->insert([
                        'no_resep' => $noResep,
                        'tgl_perawatan' => '0000-00-00',
                        'jam' => '00:00:00',
                        'no_rawat' => $noRawat,
                        'kd_dokter' => $dokter,
                        'tgl_peresepan' => $tgl,
                        'jam_peresepan' => date('H:i:s'),
                        'status' => $status,
                    ]);
                    DB::table('resep_dokter')->insert([
                        'no_resep' => $noResep,
                        'kode_brng' => $obat,
                        'jml' => $jml,
                        'aturan_pakai' => $aturan,
                    ]);
                }
            }
            $resep = DB::table('resep_dokter')
                ->join('databarang', 'resep_dokter.kode_brng', '=', 'databarang.kode_brng')
                ->join('resep_obat', 'resep_obat.no_resep', '=', 'resep_dokter.no_resep')
                ->where('resep_obat.no_rawat', $noRawat)
                ->where('resep_obat.kd_dokter', $dokter)
                ->select('resep_dokter.no_resep', 'resep_dokter.kode_brng', 'resep_dokter.jml', 'databarang.nama_brng', 'resep_dokter.aturan_pakai', 'resep_dokter.no_resep', 'databarang.nama_brng', 'resep_obat.tgl_peresepan', 'resep_obat.jam_peresepan')
                ->orderBy('resep_obat.jam_peresepan', 'desc')
                ->get();
            DB::commit();

            $pilihIterasi = $request->get('pilih_iterasi');
            if ($pilihIterasi && $resep->isNotEmpty()) {
                try {
                    DB::table('antrianiterasi')->insertOrIgnore([
                        'no_resep'     => $resep->first()->no_resep,
                        'pilihiterasi' => $pilihIterasi,
                    ]);
                } catch (\Exception $e) {}
            }

            return response()->json([
                'status' => 'sukses',
                'pesan' => 'Input resep berhasil',
                'data' => $resep,
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollback();
            return response()->json([
                'status' => 'gagal',
                'pesan' => $ex->getMessage()
            ]);
        }
    }

    public function postResepRanap(Request $request, $noRawat)
    {
        $dokter = $request->get('dokter');
        $resObat = $request->get('obat');
        $resJml = $request->get('jumlah');
        $resAturan = $request->get('aturan_pakai');
        $status = $request->get('status');
        $kode = $request->get('kode');
        $noRawat = $this->decryptData($noRawat);
        $bangsal = "";

        if (empty($dokter)) {
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Dokter tidak boleh kosong'
            ]);
        }

        // Cek Restriksi Obat BPJS — hard-block
        $reg = DB::table('reg_periksa')->where('no_rawat', $noRawat)->first();
        if ($reg) {
            $totalPerObat = [];
            for ($i = 0; $i < count($resObat); $i++) {
                $kodeBrng = $resObat[$i];
                $jml      = (float) ($resJml[$i] ?? 0);
                if (empty($kodeBrng) || $jml <= 0) continue;
                $totalPerObat[$kodeBrng] = ($totalPerObat[$kodeBrng] ?? 0) + $jml;
            }
            $pelanggaran = [];
            foreach ($totalPerObat as $kodeBrng => $total) {
                $row = $this->cariRestriksi($kodeBrng, $reg->kd_pj);
                if ($row && $row->max_jml > 0 && $total > $row->max_jml) {
                    $nama = DB::table('databarang')->where('kode_brng', $kodeBrng)->value('nama_brng') ?: $kodeBrng;
                    $pelanggaran[] = "- {$nama} : diminta " . (int)$total . ", maksimal " . (int)$row->max_jml;
                }
            }
            if (!empty($pelanggaran)) {
                return response()->json([
                    'status' => 'gagal',
                    'pesan'  => "RESTRIKSI OBAT BPJS DILANGGAR — resep tidak dapat disimpan:\n\n"
                              . implode("\n", $pelanggaran)
                              . "\n\nSilakan kurangi jumlah obat sesuai batas restriksi.",
                ]);
            }
        }

        try {
            DB::beginTransaction();
            // $db = DB::table('set_depo_ranap')->where('kd_bangsal', $kode)->first();
            // $bangsal = $db->kd_depo;
            $bangsal = $kode;

            $no = DB::table('resep_obat')->where('tgl_perawatan', 'like', '%' . date('Y-m-d') . '%')->orWhere('tgl_peresepan', 'like', '%' . date('Y-m-d') . '%')->selectRaw("ifnull(MAX(CONVERT(RIGHT(no_resep,4),signed)),0) as resep")->first();
            $maxNo = substr($no->resep, 0, 4);
            $nextNo = sprintf('%04s', ($maxNo + 1));
            $tgl = date('Ymd');
            $noResep = $tgl . '' . $nextNo;

            for ($i = 0; $i < count($resObat); $i++) {
                $obat = $resObat[$i];
                $jml = $resJml[$i] < 1 ? 1 : $resJml[$i];
                $aturan = $resAturan[$i] ?? '-';

                $maxTgl = DB::table('riwayat_barang_medis')->where('kode_brng', $obat)->where('kd_bangsal', $bangsal)->max('tanggal');
                $maxJam = DB::table('riwayat_barang_medis')->where('kode_brng', $obat)->where('tanggal', $maxTgl)->where('kd_bangsal',  $bangsal)->max('jam');
                $maxStok = DB::table('riwayat_barang_medis')->where('kode_brng', $obat)->where('kd_bangsal', $bangsal)->where('tanggal', $maxTgl)->where('jam', $maxJam)->max('stok_akhir');

                if ($maxStok < $jml) {
                    continue;
                    // if(empty($obat)){
                    //     return response()->json([
                    //         'status' => 'gagal',
                    //         'pesan' => 'Obat tidak boleh kosong'
                    //     ]);
                    // }else{
                    //     $dataBarang = DB::table('databarang')->where('kode_brng', $obat)->first();
                    //     return response()->json([
                    //         'status' => 'gagal',
                    //         'pesan' => 'Stok obat '.$dataBarang->nama_brng.' kosong'
                    //     ]);
                    // }

                }

                $maxTglResep = DB::table('resep_obat')->where('no_rawat', $noRawat)->where('tgl_peresepan', date('Y-m-d'))->where('kd_dokter', $dokter)->max('jam_peresepan');
                $resep = DB::table('resep_obat')->where('no_rawat', $noRawat)->where('tgl_peresepan', date('Y-m-d'))->where('kd_dokter', $dokter)->where('jam_peresepan', $maxTglResep)->first();

                if (!empty($resep) && $resep->tgl_perawatan != '0000-00-00') {
                    //resep sudah divalidasi

                    DB::table('resep_obat')->insert([
                        'no_resep' => $noResep,
                        'tgl_perawatan' => '0000-00-00',
                        'jam' => '00:00:00',
                        'no_rawat' => $noRawat,
                        'kd_dokter' => $dokter,
                        'tgl_peresepan' => $tgl,
                        'jam_peresepan' => date('H:i:s'),
                        'status' => $status,
                    ]);

                    DB::table('resep_dokter')->insert([
                        'no_resep' => $noResep,
                        'kode_brng' => $obat,
                        'jml' => $jml,
                        'aturan_pakai' => $aturan ?? '-',
                    ]);
                } else if (empty($resep)) {
                    //resep belum ada

                    DB::table('resep_obat')->insert([
                        'no_resep' => $noResep,
                        'tgl_perawatan' => '0000-00-00',
                        'jam' => '00:00:00',
                        'no_rawat' => $noRawat,
                        'kd_dokter' => $dokter,
                        'tgl_peresepan' => $tgl,
                        'jam_peresepan' => date('H:i:s'),
                        'status' => $status,
                    ]);

                    DB::table('resep_dokter')->insert([
                        'no_resep' => $noResep,
                        'kode_brng' => $obat,
                        'jml' => $jml,
                        'aturan_pakai' => $aturan ?? '-',
                    ]);
                } else {
                    //resep sudah ada dan belum divalidasi

                    DB::table('resep_dokter')->insert([
                        'no_resep' => $resep->no_resep,
                        'kode_brng' => $obat,
                        'jml' => $jml,
                        'aturan_pakai' => $aturan ?? '-',
                    ]);
                }
            }
            DB::commit();
            return response()->json([
                'status' => 'sukses',
                'pesan' => 'Input resep berhasil'
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollback();
            return response()->json([
                'status' => 'gagal',
                'pesan' => $ex->getMessage()
            ]);
        }
    }

    public function postResepIterasi(Request $request, $noRawat)
    {
        try {
            $noRawat = $this->decryptData($noRawat);
            $noResepAwal = $request->no_resep_awal;
            $pilihIterasi = $request->pilih_iterasi; // "1. Iterasi 1x" atau "2. Iterasi 2x"

            $resepAwal = DB::table('resep_obat')->where('no_resep', $noResepAwal)->first();
            if (!$resepAwal) {
                return response()->json(['status' => 'error', 'pesan' => 'Resep awal tidak ditemukan']);
            }

            $detailAwal = DB::table('resep_dokter')->where('no_resep', $noResepAwal)->get();
            $dokter = session()->get('username');

            DB::beginTransaction();

            $jumlahIter = ($pilihIterasi === '2. Iterasi 2x') ? 2 : 1;

            for ($iter = 1; $iter <= $jumlahIter; $iter++) {
                $lastNo = DB::table('resep_obat')
                    ->where(function($q) {
                        $q->whereDate('tgl_perawatan', today())
                          ->orWhereDate('tgl_peresepan', today());
                    })
                    ->selectRaw('IFNULL(MAX(CAST(RIGHT(no_resep,4) AS UNSIGNED)), 0) as no')
                    ->first();
                $nextNo = sprintf('%04d', ($lastNo->no + 1));
                $noResepBaru = now()->format('Ymd') . $nextNo;

                DB::table('resep_obat')->insert([
                    'no_resep'       => $noResepBaru,
                    'tgl_perawatan'  => '0000-00-00',
                    'jam'            => '00:00:00',
                    'no_rawat'       => $noRawat,
                    'kd_dokter'      => $dokter,
                    'tgl_peresepan'  => now()->format('Y-m-d'),
                    'jam_peresepan'  => now()->format('H:i:s'),
                    'status'         => $resepAwal->status,
                    'tgl_penyerahan' => '0000-00-00',
                    'jam_penyerahan' => '00:00:00',
                ]);

                foreach ($detailAwal as $detail) {
                    DB::table('resep_dokter')->insert([
                        'no_resep'     => $noResepBaru,
                        'kode_brng'    => $detail->kode_brng,
                        'jml'          => $detail->jml,
                        'aturan_pakai' => $detail->aturan_pakai,
                    ]);
                }

                $statusIter = ($iter === 1) ? 'Iterasi Ke 1' : 'Iterasi Ke 2';
                DB::table('permintaan_resep_iterasi_bpjs')->insert([
                    'no_resep_awal' => $noResepAwal,
                    'no_resep'      => $noResepBaru,
                    'status_iter'   => $statusIter,
                ]);
            }

            try {
                DB::table('antrianiterasi')->insert([
                    'no_resep'     => $noResepAwal,
                    'pilihiterasi' => $pilihIterasi,
                ]);
            } catch (\Exception $e) {
                // tabel antrianiterasi mungkin tidak ada di semua instalasi
            }

            DB::commit();
            return response()->json(['status' => 'sukses', 'pesan' => 'Resep iterasi berhasil disimpan']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'pesan' => $e->getMessage()]);
        }
    }

    public function hapusResepIterasi($noResep)
    {
        try {
            DB::beginTransaction();
            DB::table('permintaan_resep_iterasi_bpjs')->where('no_resep', $noResep)->delete();
            DB::table('resep_dokter')->where('no_resep', $noResep)->delete();
            DB::table('resep_obat')->where('no_resep', $noResep)->delete();
            DB::commit();
            return response()->json(['status' => 'sukses']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'pesan' => $e->getMessage()]);
        }
    }

    public function postResepRacikan(Request $request, $noRawat)
    {
        $input = $request->all();
        $namaRacikan = $input['nama_racikan'];
        $aturanPakai = $input['aturan_racikan'];
        $jumlahRacikan = $input['jumlah_racikan'];
        $metodeRacikan = $input['metode_racikan'];
        $keteranganRacikan = $input['keterangan_racikan'];
        $satu_resep = $input['satu_resep'] ?? 0;

        $kdObat = $input['kd_obat'];
        $p1 = $input['p1'];
        $p2 = $input['p2'];
        $kandungan = $input['kandungan'];
        $jml = $input['jml'];

        $no_rawat = $this->decryptData($noRawat);
        $dokter = session()->get('username');
        $status = $request->get('status');
        $kode = $request->get('kode');

        $request->validate([
            'nama_racikan' => 'required',
            'aturan_racikan' => 'required',
            'jumlah_racikan' => 'required',
            'metode_racikan' => 'required',
            'keterangan_racikan' => 'required',
            'kd_obat' => 'required',
            'kd_obat.*' => 'required',
            'p1' => 'required',
            'p1.*' => 'required',
            'p2' => 'required',
            'p2.*' => 'required',
            'kandungan' => 'required',
            'kandungan.*' => 'required',
            'jml' => 'required',
            'jml.*' => 'required',
        ], [
            'kd_obat.*.required' => 'Obat tidak boleh kosong',
            'p1.*.required' => 'P1 tidak boleh kosong',
            'p2.*.required' => 'P2 tidak boleh kosong',
            'kandungan.*.required' => 'Kandungan tidak boleh kosong',
            'jml.*.required' => 'Jumlah tidak boleh kosong',
        ]);

        // Cek Restriksi Obat BPJS (per komponen racikan × jumlah racikan) — hard-block
        $reg = DB::table('reg_periksa')->where('no_rawat', $no_rawat)->first();
        if ($reg) {
            $totalPerObat = [];
            for ($i = 0; $i < count($kdObat); $i++) {
                $kb  = $kdObat[$i];
                $qty = (float) ($jml[$i] ?? 0) * (float) $jumlahRacikan;
                if (empty($kb) || $qty <= 0) continue;
                $totalPerObat[$kb] = ($totalPerObat[$kb] ?? 0) + $qty;
            }
            $pelanggaran = [];
            foreach ($totalPerObat as $kb => $total) {
                $row = $this->cariRestriksi($kb, $reg->kd_pj);
                if ($row && $row->max_jml > 0 && $total > $row->max_jml) {
                    $nama = DB::table('databarang')->where('kode_brng', $kb)->value('nama_brng') ?: $kb;
                    $pelanggaran[] = "- {$nama} : diminta " . (int)$total . ", maksimal " . (int)$row->max_jml;
                }
            }
            if (!empty($pelanggaran)) {
                return response()->json([
                    'status' => 'gagal',
                    'pesan'  => "RESTRIKSI OBAT BPJS DILANGGAR — racikan tidak dapat disimpan:\n\n"
                              . implode("\n", $pelanggaran)
                              . "\n\nSilakan kurangi jumlah obat sesuai batas restriksi.",
                ]);
            }
        }

        try {
            DB::beginTransaction();
            $noResep = '';

            if ($satu_resep == 0) {
                $no = DB::table('resep_obat')->where('tgl_perawatan', 'like', '%' . date('Y-m-d') . '%')->orWhere('tgl_peresepan', 'like', '%' . date('Y-m-d') . '%')->selectRaw("ifnull(MAX(CONVERT(RIGHT(no_resep,4),signed)),0) as resep")->first();
                $maxNo = substr($no->resep, 0, 4);
                $nextNo = sprintf('%04s', ($maxNo + 1));
                $tgl = date('Ymd');
                $noResep = $tgl . '' . $nextNo;

                DB::table('resep_obat')
                    ->insert([
                        'no_resep' => $noResep,
                        'tgl_perawatan' => '0000-00-00',
                        'jam' => '00:00:00',
                        'no_rawat' => $no_rawat,
                        'kd_dokter' => $dokter,
                        'tgl_peresepan' => date('Y-m-d'),
                        'jam_peresepan' => date('H:i:s'),
                        'status' => 'ralan',
                        'tgl_penyerahan' => '0000-00-00',
                        'jam_penyerahan' => '00:00:00',
                    ]);
            } else {
                $resep = DB::table('resep_obat')->where('no_rawat', $no_rawat)->where('tgl_peresepan', date('Y-m-d'))->first();
                if (!empty($resep)) {
                    $noResep = $resep->no_resep;
                } else {
                    $no = DB::table('resep_obat')->where('tgl_perawatan', 'like', '%' . date('Y-m-d') . '%')->orWhere('tgl_peresepan', 'like', '%' . date('Y-m-d') . '%')->selectRaw("ifnull(MAX(CONVERT(RIGHT(no_resep,4),signed)),0) as resep")->first();
                    $maxNo = substr($no->resep, 0, 4);
                    $nextNo = sprintf('%04s', ($maxNo + 1));
                    $tgl = date('Ymd');
                    $noResep = $tgl . '' . $nextNo;

                    DB::table('resep_obat')
                        ->insert([
                            'no_resep' => $noResep,
                            'tgl_perawatan' => '0000-00-00',
                            'jam' => '00:00:00',
                            'no_rawat' => $no_rawat,
                            'kd_dokter' => $dokter,
                            'tgl_peresepan' => date('Y-m-d'),
                            'jam_peresepan' => date('H:i:s'),
                            'status' => 'ralan',
                            'tgl_penyerahan' => '0000-00-00',
                            'jam_penyerahan' => '00:00:00',
                        ]);
                }
            }

            $noRacik = (DB::table('resep_dokter_racikan')->where('no_resep', $noResep)->max('no_racik') ?? 0) + 1;

            DB::table('resep_dokter_racikan')
                ->insert([
                    'no_resep' => $noResep,
                    'no_racik' => $noRacik,
                    'nama_racik' => $namaRacikan,
                    'kd_racik' => $metodeRacikan,
                    'jml_dr' => $jumlahRacikan,
                    'aturan_pakai' => $aturanPakai,
                    'keterangan' => $keteranganRacikan,
                ]);

            for ($i = 0; $i < count($kdObat); $i++) {
                DB::table('resep_dokter_racikan_detail')->insert([
                    'no_resep' => $noResep,
                    'no_racik' => $noRacik,
                    'kode_brng' => $kdObat[$i],
                    'p1' => $p1[$i],
                    'p2' => $p2[$i],
                    'kandungan' => $kandungan[$i],
                    'jml' => $jml[$i],
                ]);
            }
            DB::commit();

            $pilihIterasi = $request->get('pilih_iterasi');
            if ($pilihIterasi && $noResep) {
                try {
                    DB::table('antrianiterasi')->insertOrIgnore([
                        'no_resep'     => $noResep,
                        'pilihiterasi' => $pilihIterasi,
                    ]);
                } catch (\Exception $e) {}
            }

            return response()->json(['status' => 'sukses', 'message' => 'Racikan berhasil ditambahkan']);

            // $cek = DB::table('resep_obat')
            //     ->join('resep_dokter_racikan', 'resep_obat.no_resep', '=', 'resep_dokter_racikan.no_resep')
            //     ->where('resep_obat.no_rawat', $no_rawat)->where('resep_obat.tgl_peresepan', date('Y-m-d'))
            //     ->select('resep_obat.no_resep', 'resep_obat.tgl_perawatan')
            //     ->first();

            // if (!empty($cek) && $cek->tgl_perawatan != '0000-00-00') {
            //     $noRacik = DB::table('resep_dokter_racikan')->where('no_resep', $cek->no_resep)->max('no_racik');
            //     $nextNoRacik = $noRacik + 1;
            //     $insert = DB::table('resep_dokter_racikan')
            //         ->insert([
            //             'no_resep' => $cek->no_resep,
            //             'no_racik' => $nextNoRacik,
            //             'nama_racik' => $namaRacikan,
            //             'kd_racik' => $metodeRacikan,
            //             'jml_dr' => $jumlahRacikan,
            //             'aturan_pakai' => $aturanPakai,
            //             'keterangan' => $keteranganRacikan,
            //         ]);
            //     if ($insert) {
            //         return response()->json(['status' => 'sukses', 'message' => 'Racikan berhasil ditambahkan']);
            //     }
            // } else {
            //     $insert = DB::table('resep_obat')
            //         ->insert([
            //             'no_resep' => $noResep,
            //             'tgl_perawatan' => '0000-00-00',
            //             'jam' => '00:00:00',
            //             'no_rawat' => $no_rawat,
            //             'kd_dokter' => $dokter,
            //             'tgl_peresepan' => date('Y-m-d'),
            //             'jam_peresepan' => date('H:i:s'),
            //             'status' => 'ralan',
            //             'tgl_penyerahan' => '0000-00-00',
            //             'jam_penyerahan' => '00:00:00',
            //         ]);
            //     if ($insert) {
            //         $insert = DB::table('resep_dokter_racikan')
            //             ->insert([
            //                 'no_resep' => $noResep,
            //                 'no_racik' => '1',
            //                 'nama_racik' => $namaRacikan,
            //                 'kd_racik' => $metodeRacikan,
            //                 'jml_dr' => $jumlahRacikan,
            //                 'aturan_pakai' => $aturanPakai,
            //                 'keterangan' => $keteranganRacikan,
            //             ]);
            //         if ($insert) {
            //             for ($i = 0; $i < count($kdObat); $i++) {
            //                 DB::table('resep_dokter_racikan_detail')->insert([
            //                     'no_resep' => $noResep,
            //                     'no_racik' => '1',
            //                     'kode_brng' => $kdObat[$i],
            //                     'p1' => $p1[$i],
            //                     'p2' => $p2[$i],
            //                     'kandungan' => $kandungan[$i],
            //                     'jml' => $jml[$i],
            //                 ]);
            //             }
            //             DB::commit();
            //             return response()->json(['status' => 'sukses', 'message' => 'Racikan berhasil ditambahkan']);
            //         }
            //     } else {
            //         DB::rollBack();
            //         return response()->json(['status' => 'gagal', 'message' => 'Racikan gagal ditambahkan']);
            //     }
            // }
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollBack();
            return response()->json(['status' => 'gagal', 'message' => 'Maaf ada obat masih kosong']);
        }
    }

    public function postResepRacikanRanap(Request $request, $noRawat)
    {
        $input = $request->all();
        $namaRacikan = $input['nama_racikan'];
        $aturanPakai = $input['aturan_racikan'];
        $jumlahRacikan = $input['jumlah_racikan'];
        $metodeRacikan = $input['metode_racikan'];
        $keteranganRacikan = $input['keterangan_racikan'];

        $kdObat = $input['kd_obat'];
        $p1 = $input['p1'];
        $p2 = $input['p2'];
        $kandungan = $input['kandungan'];
        $jml = $input['jml'];

        $no_rawat = $this->decryptData($noRawat);
        $dokter = session()->get('username');

        $request->validate([
            'nama_racikan' => 'required',
            'aturan_racikan' => 'required',
            'jumlah_racikan' => 'required',
            'metode_racikan' => 'required',
            'keterangan_racikan' => 'required',
            'kd_obat' => 'required',
            'kd_obat.*' => 'required',
            'p1' => 'required',
            'p1.*' => 'required',
            'p2' => 'required',
            'p2.*' => 'required',
            'kandungan' => 'required',
            'kandungan.*' => 'required',
            'jml' => 'required',
            'jml.*' => 'required',
        ]);

        // Cek Restriksi Obat BPJS (racikan ranap) — hard-block
        $reg = DB::table('reg_periksa')->where('no_rawat', $no_rawat)->first();
        if ($reg) {
            $totalPerObat = [];
            for ($i = 0; $i < count($kdObat); $i++) {
                $kb  = $kdObat[$i];
                $qty = (float) ($jml[$i] ?? 0) * (float) $jumlahRacikan;
                if (empty($kb) || $qty <= 0) continue;
                $totalPerObat[$kb] = ($totalPerObat[$kb] ?? 0) + $qty;
            }
            $pelanggaran = [];
            foreach ($totalPerObat as $kb => $total) {
                $row = $this->cariRestriksi($kb, $reg->kd_pj);
                if ($row && $row->max_jml > 0 && $total > $row->max_jml) {
                    $nama = DB::table('databarang')->where('kode_brng', $kb)->value('nama_brng') ?: $kb;
                    $pelanggaran[] = "- {$nama} : diminta " . (int)$total . ", maksimal " . (int)$row->max_jml;
                }
            }
            if (!empty($pelanggaran)) {
                return response()->json([
                    'status' => 'gagal',
                    'pesan'  => "RESTRIKSI OBAT BPJS DILANGGAR — racikan tidak dapat disimpan:\n\n"
                              . implode("\n", $pelanggaran)
                              . "\n\nSilakan kurangi jumlah obat sesuai batas restriksi.",
                ]);
            }
        }

        try {
            DB::beginTransaction();
            $no = DB::table('resep_obat')->where('tgl_perawatan', 'like', '%' . date('Y-m-d') . '%')->orWhere('tgl_peresepan', 'like', '%' . date('Y-m-d') . '%')->selectRaw("ifnull(MAX(CONVERT(RIGHT(no_resep,4),signed)),0) as resep")->first();
            $maxNo = substr($no->resep, 0, 4);
            $nextNo = sprintf('%04s', ($maxNo + 1));
            $tgl = date('Ymd');
            $noResep = $tgl . '' . $nextNo;

            DB::table('resep_obat')
                ->insert([
                    'no_resep' => $noResep,
                    'tgl_perawatan' => '0000-00-00',
                    'jam' => '00:00:00',
                    'no_rawat' => $no_rawat,
                    'kd_dokter' => $dokter,
                    'tgl_peresepan' => date('Y-m-d'),
                    'jam_peresepan' => date('H:i:s'),
                    'status' => 'ranap',
                    'tgl_penyerahan' => '0000-00-00',
                    'jam_penyerahan' => '00:00:00',
                ]);

            $noRacik = (DB::table('resep_dokter_racikan')->where('no_resep', $noResep)->max('no_racik') ?? 0) + 1;

            DB::table('resep_dokter_racikan')
                ->insert([
                    'no_resep' => $noResep,
                    'no_racik' => $noRacik,
                    'nama_racik' => $namaRacikan,
                    'kd_racik' => $metodeRacikan,
                    'jml_dr' => $jumlahRacikan,
                    'aturan_pakai' => $aturanPakai,
                    'keterangan' => $keteranganRacikan,
                ]);

            for ($i = 0; $i < count($kdObat); $i++) {
                DB::table('resep_dokter_racikan_detail')->insert([
                    'no_resep' => $noResep,
                    'no_racik' => $noRacik,
                    'kode_brng' => $kdObat[$i],
                    'p1' => $p1[$i],
                    'p2' => $p2[$i],
                    'kandungan' => $kandungan[$i],
                    'jml' => $jml[$i],
                ]);
            }
            DB::commit();
            return response()->json(['status' => 'sukses', 'message' => 'Racikan berhasil ditambahkan']);

            // $cek = DB::table('resep_obat')
            //     ->join('resep_dokter_racikan', 'resep_obat.no_resep', '=', 'resep_dokter_racikan.no_resep')
            //     ->where('resep_obat.no_rawat', $no_rawat)->where('resep_obat.tgl_peresepan', date('Y-m-d'))
            //     ->select('resep_obat.no_resep')
            //     ->first();

            // if (!empty($cek)) {
            //     $noRacik = DB::table('resep_dokter_racikan')->where('no_resep', $cek->no_resep)->max('no_racik');
            //     $nextNoRacik = $noRacik + 1;
            //     $insert = DB::table('resep_dokter_racikan')
            //         ->insert([
            //             'no_resep' => $cek->no_resep,
            //             'no_racik' => $nextNoRacik,
            //             'nama_racik' => $namaRacikan,
            //             'kd_racik' => $metodeRacikan,
            //             'jml_dr' => $jumlahRacikan,
            //             'aturan_pakai' => $aturanPakai,
            //             'keterangan' => $keteranganRacikan,
            //         ]);
            //     if ($insert) {
            //         return response()->json(['status' => 'sukses', 'message' => 'Racikan berhasil ditambahkan']);
            //     }
            // } else {
            //     $insert = DB::table('resep_obat')
            //         ->insert([
            //             'no_resep' => $noResep,
            //             'tgl_perawatan' => '0000-00-00',
            //             'jam' => '00:00:00',
            //             'no_rawat' => $no_rawat,
            //             'kd_dokter' => $dokter,
            //             'tgl_peresepan' => date('Y-m-d'),
            //             'jam_peresepan' => date('H:i:s'),
            //             'status' => 'ranap',
            //             'tgl_penyerahan' => '0000-00-00',
            //             'jam_penyerahan' => '00:00:00',
            //         ]);
            //     if ($insert) {
            //         $insert = DB::table('resep_dokter_racikan')
            //             ->insert([
            //                 'no_resep' => $noResep,
            //                 'no_racik' => '1',
            //                 'nama_racik' => $namaRacikan,
            //                 'kd_racik' => $metodeRacikan,
            //                 'jml_dr' => $jumlahRacikan,
            //                 'aturan_pakai' => $aturanPakai,
            //                 'keterangan' => $keteranganRacikan,
            //             ]);
            //         if ($insert) {
            //             for ($i = 0; $i < count($kdObat); $i++) {
            //                 DB::table('resep_dokter_racikan_detail')->insert([
            //                     'no_resep' => $noResep,
            //                     'no_racik' => '1',
            //                     'kode_brng' => $kdObat[$i],
            //                     'p1' => $p1[$i],
            //                     'p2' => $p2[$i],
            //                     'kandungan' => $kandungan[$i],
            //                     'jml' => $jml[$i],
            //                 ]);
            //             }

            //             return response()->json(['status' => 'sukses', 'message' => 'Racikan berhasil ditambahkan']);
            //         }
            //     } else {
            //         return response()->json(['status' => 'gagal', 'message' => 'Racikan gagal ditambahkan']);
            //     }
            // }
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollBack();
            return response()->json(['status' => 'gagal', 'message' => $ex->getMessage()]);
        }
    }

    public function hapusObat($noResep, $kdObat, $noRawat)
    {
        $dokter = session()->get('username');
        $noRawat = $this->decryptData($noRawat);
        try {
            $cek = DB::table('resep_obat')->where('no_resep', $noResep)->first();
            if ($cek->tgl_perawatan != '0000-00-00') {
                return response()->json(['status' => 'gagal', 'pesan' => 'Resep sudah tervalidasi, silahkan hubungi farmasi untuk menghapus obat']);
            }
            DB::table('resep_dokter')->where('no_resep', $noResep)->where('kode_brng', $kdObat)->delete();
            $resep = DB::table('resep_dokter')
                ->join('databarang', 'resep_dokter.kode_brng', '=', 'databarang.kode_brng')
                ->join('resep_obat', 'resep_obat.no_resep', '=', 'resep_dokter.no_resep')
                ->where('resep_obat.no_rawat', $noRawat)
                ->where('resep_obat.kd_dokter', $dokter)
                ->select('resep_dokter.no_resep', 'resep_dokter.kode_brng', 'resep_dokter.jml', 'databarang.nama_brng', 'resep_dokter.aturan_pakai', 'resep_dokter.no_resep', 'databarang.nama_brng', 'resep_obat.tgl_peresepan', 'resep_obat.jam_peresepan')
                ->get();
            return response()->json(['status' => 'sukses', 'pesan' => 'Obat berhasil dihapus', 'data' => $resep]);
        } catch (\Exception $ex) {
            return response()->json(['status' => 'gagal', 'pesan' => $ex->getMessage()]);
        }
    }

    public function hapusObatBatch(Request $request)
    {
        $dokter = session()->get('username');
        $noRawat = $this->decryptData($request->get('no_rawat'));
        $noResep = $request->get('no_resep');
        $kdObat = $request->get('obat');
        // return response()->json(['status' => 'sukses', 'pesan' => 'Obat berhasil dihapus', 'data' => $kdObat]);
        try {
            DB::beginTransaction();
            $cek = DB::table('resep_obat')->where('no_resep', $noResep)->first();
            if ($cek->tgl_perawatan != '0000-00-00') {
                return response()->json(['status' => 'gagal', 'pesan' => 'Resep sudah tervalidasi, silahkan hubungi farmasi untuk menghapus obat']);
            }
            foreach ($kdObat as $key => $value) {
                DB::table('resep_dokter')->where('no_resep', $noResep)->where('kode_brng', $kdObat[$key])->delete();
            }
            DB::commit();
            DB::table('resep_dokter')->where('no_resep', $noResep)->where('kode_brng', $kdObat)->delete();
            $resep = DB::table('resep_dokter')
                ->join('databarang', 'resep_dokter.kode_brng', '=', 'databarang.kode_brng')
                ->join('resep_obat', 'resep_obat.no_resep', '=', 'resep_dokter.no_resep')
                ->where('resep_obat.no_rawat', $noRawat)
                ->where('resep_obat.kd_dokter', $dokter)
                ->select('resep_dokter.no_resep', 'resep_dokter.kode_brng', 'resep_dokter.jml', 'databarang.nama_brng', 'resep_dokter.aturan_pakai', 'resep_dokter.no_resep', 'databarang.nama_brng', 'resep_obat.tgl_peresepan', 'resep_obat.jam_peresepan')
                ->get();
            return response()->json(['status' => 'sukses', 'pesan' => 'Obat berhasil dihapus', 'data' => $resep]);
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json(['status' => 'gagal', 'pesan' => $ex->getMessage()]);
        }
    }
}
