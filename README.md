# E-Dokter BLUD RSUD Kota Baubau

Sistem informasi klinis berbasis web untuk dokter di BLUD RSUD Kota Baubau. Mengintegrasikan workflow Rawat Jalan, Rawat Inap, dan IGD dalam satu aplikasi yang ringan dan responsif, dengan tetap kompatibel dengan database SIMRS Khanza.

## Tentang Aplikasi

E-Dokter dirancang untuk mempercepat dokumentasi klinis tanpa mengorbankan kelengkapan data. Aplikasi ini dibangun di atas Laravel 9 + Livewire dan AdminLTE 3, berjalan di browser sehingga bisa dipakai di komputer poli, tablet di bangsal, maupun perangkat di ruang IGD.

Aplikasi ini bekerja bersama SIMRS Khanza desktop: data pasien, dokter, dan registrasi tetap dikelola Khanza, sementara E-Dokter fokus pada layar pemeriksaan dokter yang sering dipakai.

## Modul Utama

### Rawat Jalan (Ralan)
- Pemeriksaan SOAP (Subjek, Objek, Asesmen, Plan)
- Resep biasa & resep racikan
- Resume pasien
- Diagnosa & ICD-10
- Permintaan Lab, Radiologi, dan Konsultasi Medik
- Rujuk Internal antar dokter/poli
- Penilaian Awal Medis per spesialisasi (Umum, Anak, Bedah, Mata, THT, Jantung, Paru, Penyakit Dalam, Kandungan, Neurologi, Orthopedi, Rehab Medik, Urologi, Kulit & Kelamin, Psikiatri, Geriatri, Bedah Mulut, Penyakit Mulut, Gawat Darurat Psikiatri)
- Ambil otomatis dari CPPT pemeriksaan sebelumnya

### Rawat Inap (Ranap)
- CPPT (Catatan Perkembangan Pasien Terintegrasi)
- Resep ranap dengan template
- Diagnosa & prosedur (utama + sekunder 1-4)
- Resume pasien dengan auto-fill dari pemeriksaan, lab, radiologi, operasi, obat, diet, dan lab pending
- Catatan pasien
- Permintaan Lab & Radiologi
- Laporan Operasi

### IGD
- Triase primer dan sekunder dengan skala 1 (Immediate) dan skala 2 (Emergent)
- Penilaian Medis IGD (CPPT IGD) dengan riwayat across kunjungan
- Catatan Observasi
- Pengambilan otomatis vital sign & keluhan dari triase ke penilaian medis
- Form lebih singkat dibanding poli, sesuai workflow emergency

### Rujukan Internal & Konsul Antar Dokter
- Dokter penerima rujukan mendapat tampilan khusus "Mode Konsul"
- Card Resume, Diagnosa utama, dan Rujuk Internal disembunyikan untuk dokter konsul
- Form Jawaban Konsul muncul otomatis dengan info dari dokter perujuk
- Notifikasi Resep Iterasi BPJS otomatis tidak muncul untuk dokter konsul

### Integrasi BPJS
- I-Care BPJS (riwayat perawatan via Vclaim)
- Permintaan Resep Iterasi BPJS

### Berkas Digital Perawatan
- Upload berkas pasien (KTP, KK, BPJS, surat rujukan, hasil lab/rad/EKG, dll) dengan kategori
- Folder upload kompatibel dengan struktur Khanza HYBRIDWEB (`webapps/berkasrawat/pages/upload`)
- Folder bisa di-share antara Laravel dan Khanza desktop via env `BERKAS_UPLOAD_PATH`

## Teknologi

- **Backend**: Laravel 9 (PHP 8.x)
- **Frontend**: Livewire 2, AdminLTE 3, Bootstrap 4, jQuery, DataTables
- **Database**: MySQL/MariaDB (skema SIMRS Khanza)
- **Timezone**: Asia/Makassar (WITA)
- **Theme**: Custom RSUD Baubau (hijau #1f8a3a + kuning #f4c81f)
- **Authentication**: Session-based login dengan NIP Dokter + pemilihan Poliklinik (multi-poli per dokter)

## Setup & Instalasi

```bash
# Clone
git clone https://github.com/ghazurti/laravel-Edokter.git
cd laravel-Edokter

# Dependencies
composer install
npm install && npm run dev

# Environment
cp .env.example .env
php artisan key:generate
# Set DB_*, BPJS_*, BERKAS_UPLOAD_PATH di .env

# Cache & assets
php artisan config:clear
php artisan view:clear
```

### Konfigurasi `.env` penting

```env
# Database (skema Khanza)
DB_HOST=127.0.0.1
DB_DATABASE=sik

# BPJS Vclaim / I-Care
BPJS_CONS_ID=...
BPJS_CONS_PWD=...
BPJS_USER_KEY=...
BPJS_BASE_URL=https://apijkn-dev.bpjs-kesehatan.go.id/vclaim-rest-dev/
BPJS_ICARE_BASE_URL=https://apijkn-dev.bpjs-kesehatan.go.id/icare-rest/

# Folder upload berkas digital
# Set ke path Khanza HYBRIDWEB agar share folder dengan Khanza desktop.
# Kosongkan untuk pakai public/webapps/berkasrawat/pages/upload/ Laravel.
BERKAS_UPLOAD_PATH=
```

## Workflow Login

Login menggunakan **NIP Dokter** dengan pemilihan **Poliklinik** (untuk dokter yang aktif di lebih dari satu poli). Berdasarkan kode poli yang dipilih, menu yang muncul otomatis difilter:

- Dokter Poli (`kd_poli ≠ IGDK`) → menu Rawat Jalan & Rawat Inap muncul, IGD disembunyikan
- Dokter IGD (`kd_poli = IGDK`) → menu IGD muncul, Rawat Jalan & Rawat Inap disembunyikan

Sesi login otomatis logout 30 menit jika tidak ada aktivitas.

## Kompatibilitas Khanza

E-Dokter membaca dan menulis ke skema database SIMRS Khanza tanpa modifikasi tabel. Tabel-tabel yang dipakai:

- `reg_periksa`, `pasien`, `dokter`, `poliklinik`
- `pemeriksaan_ralan`, `pemeriksaan_ranap`
- `resep_obat`, `resep_dokter`, `resep_dokter_racikan`
- `diagnosa_pasien`, `prosedur_pasien`, `penyakit`, `icd9`
- `permintaan_lab`, `permintaan_radiologi`, `hasil_radiologi`, `detail_periksa_lab`
- `rujukan_internal_poli`, `rujukan_internal_poli_detail`
- `data_triase_igd`, `data_triase_igdprimer`, `data_triase_igdsekunder`, `data_triase_igddetail_skala1/2`
- `penilaian_medis_igd`, `catatan_observasi_igd`
- `resume_pasien`, `resume_pasien_ranap`
- `berkas_digital_perawatan`, `retensi_pasien`
- dan tabel master lainnya

## Lisensi & Kontak

Aplikasi internal BLUD RSUD Kota Baubau. Untuk pertanyaan teknis, hubungi tim IT RSUD.
