# Restriksi Obat di Resep Ralan (e-Dokter)

**Tanggal:** 2026-06-15
**Sumber acuan:** SIMRS Khanza desktop — `src/inventory/DlgPeresepanDokter.java` (validasi) dan
`src/inventory/MasterRestriksiObatBPJS.java` (master).

## Tujuan
Menampilkan peringatan visual dan memblokir penyimpanan resep di halaman e-resep Ralan
ketika jumlah obat yang diinput melebihi batas restriksi yang ditetapkan di tabel
`restriksi_obat`. Perilaku menyamai versi Khanza desktop.

## Cakupan Tahap 1
- Resep Ralan saja (komponen Livewire `App\Http\Livewire\Ralan\Resep`,
  endpoint `POST /api/resep/{noRawat}` di `ResepController::postResep`).
- Tahap berikut (di luar dokumen ini): Ranap (`postResepRanap`), Racikan
  (`postResepRacikan`, `postResepRacikanRanap`).

## Skema Tabel (reuse — tidak ada migrasi)
Tabel `restriksi_obat`:

| Kolom            | Keterangan                                              |
|------------------|---------------------------------------------------------|
| `kode_brng`      | FK ke `databarang.kode_brng`                            |
| `kdjenis`        | Jenis barang (`databarang.kdjns`) atau `'ALL'`          |
| `kd_pj`          | Penjamin (`penjab.kd_pj`) atau `'ALL'`                  |
| `max_jml`        | Batas maksimum jumlah per resep (decimal/int)           |
| `max_iterasi`    | (tidak dipakai Tahap 1)                                 |
| `butuh_approval` | (tidak dipakai Tahap 1)                                 |
| `aktif`          | `'Y'` / `'N'`                                           |
| `keterangan`     | Teks ditampilkan ke dokter                              |

## Resolusi `kd_pj`
Diambil dari `reg_periksa.kd_pj` `WHERE no_rawat = ?`. `noRawat` dipakai dalam bentuk
sudah ter-decrypt (sama dengan jalur `postResep`).

## Algoritma Pencocokan Restriksi (preferensi terspesifik → fallback)
Query tunggal mengikuti pola Khanza:
```sql
SELECT ro.max_jml, ro.keterangan, ro.aktif
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
LIMIT 1
```

## Komponen Baru / Diubah

### Backend
- `app/Http/Controllers/API/ResepController.php`
  - **Baru:** `getRestriksiObat($kodeObat, $noRawat)` → return JSON `null` atau
    `{ max_jml, keterangan }`.
  - **Diubah:** `postResep()` — sebelum loop insert, jalankan cek restriksi untuk
    setiap obat. Jika ada pelanggaran, **rollback** dan return:
    ```json
    { "status": "gagal", "pesan": "RESTRIKSI OBAT BPJS DILANGGAR:\n- <nama>: diminta X, maksimal Y\n..." }
    ```
    Total per `kode_brng` dijumlahkan dulu (kalau dokter input obat sama 2x dalam satu submit).

- `routes/api.php`
  - **Baru:** `Route::get('/obat/restriksi/{kodeObat}/{noRawat}', [ResepController::class, 'getRestriksiObat'])`.

### Frontend — `resources/views/livewire/ralan/resep.blade.php`
- Tambahkan div kosong di bawah tiap baris obat dgn id `restriksi-{i}` (class
  `restriksi-warning`).
- Saat event `select2:select` pada `.obat-{i}` → fetch endpoint restriksi, isi
  div dgn callout warna **kuning** (`alert alert-warning py-1 mb-2`):
  `⚠ Restriksi: {keterangan} — Maks {max_jml}`.
  Simpan `max_jml` ke `data-max` pada input jumlah.
- Saat input `jumlah` ber-`input` → kalau `value > data-max`, ubah callout jadi
  `alert-danger` + border input merah + set flag `data-violated="true"`. Sebaliknya
  reset ke kuning + hilangkan flag.
- Saat klik **Simpan**: kalau ada `data-violated="true"`, blokir submit & tampilkan
  SweetAlert error. (Backend tetap memvalidasi sebagai pengaman.)

## Hard-Block Behavior
- Frontend: tombol Simpan boleh ditekan, tapi handler menghalangi submit kalau ada
  pelanggaran (UX cepat).
- Backend: cek ulang, kalau langgar → **rollback transaksi**, response `status:gagal`,
  livewire menampilkan SweetAlert error.

## Out of Scope
- Master CRUD restriksi (dianggap sudah ada di Khanza desktop).
- Racikan, Ranap, IGD (tahap berikutnya).
- `max_iterasi` dan `butuh_approval`.

## Verifikasi Manual
1. Pilih pasien BPJS, buka e-resep Ralan, pilih obat yang ada di `restriksi_obat`.
2. Callout kuning muncul dgn teks restriksi.
3. Isi jumlah ≤ `max_jml` → callout tetap kuning, submit sukses.
4. Isi jumlah > `max_jml` → callout merah, submit di-block (frontend & backend).
5. Pilih obat yang tidak ada di `restriksi_obat` → tidak ada callout.
6. Pasien umum (kd_pj non-BPJS) — kalau ada restriksi `kd_pj='ALL'`, tetap kena.
