# Deploy E-Dokter ke aaPanel Docker

Server: **172.17.10.4** (Docker host) — terhubung ke Khanza/MySQL di **172.17.10.5**.

## 1. Persiapan di Server 172.17.10.4

SSH ke server, install Docker via aaPanel:

```bash
# Login aaPanel UI → Docker → install kalau belum
# Atau via CLI:
ssh root@172.17.10.4
curl -fsSL https://get.docker.com | sh
systemctl enable --now docker
```

## 2. Clone Repo

```bash
mkdir -p /www/wwwroot/edokter && cd /www/wwwroot/edokter
git clone https://github.com/ghazurti/laravel-Edokter.git .
```

## 3. Setup `.env`

```bash
cp .env.example .env
nano .env
```

Isi minimal:

```env
APP_NAME="E-Dokter RSUD"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://172.17.10.4:8090

# Database Khanza (server 172.17.10.5)
DB_CONNECTION=mysql
DB_HOST=172.17.10.5
DB_PORT=3306
DB_DATABASE=sik
DB_USERNAME=root
DB_PASSWORD=PASSWORD_MYSQL_KHANZA

# Timezone
APP_TIMEZONE=Asia/Makassar

# BPJS (kalau pakai)
BPJS_CONS_ID=...
BPJS_CONS_PWD=...
BPJS_USER_KEY=...
BPJS_BASE_URL=https://apijkn.bpjs-kesehatan.go.id/vclaim-rest/
BPJS_ICARE_BASE_URL=https://apijkn.bpjs-kesehatan.go.id/icare-rest/

# Berkas digital — kosong = simpan di volume Docker
# Atau mount folder Khanza di compose, set ke path internal container
BERKAS_UPLOAD_PATH=
```

**Penting**: pastikan MySQL Khanza di 172.17.10.5 mengizinkan koneksi dari 172.17.10.4:

```sql
-- Di Khanza server (172.17.10.5):
GRANT ALL ON sik.* TO 'root'@'172.17.10.4' IDENTIFIED BY 'PASSWORD';
FLUSH PRIVILEGES;

-- Atau lebih spesifik dengan user terpisah:
CREATE USER 'edokter'@'172.17.10.4' IDENTIFIED BY 'PASSWORD_EDOKTER';
GRANT SELECT, INSERT, UPDATE, DELETE ON sik.* TO 'edokter'@'172.17.10.4';
FLUSH PRIVILEGES;
```

Cek di `/etc/my.cnf` MySQL Khanza: `bind-address = 0.0.0.0` (jangan 127.0.0.1).

## 4. Build & Run

```bash
cd /www/wwwroot/edokter
docker compose up -d --build
```

Tunggu ~3-5 menit. Cek log:

```bash
docker compose logs -f edokter
```

## 5. Verifikasi

```bash
# Container running?
docker compose ps

# Test koneksi DB dari container:
docker compose exec edokter php artisan tinker --execute='DB::connection()->getPdo();'

# Akses dari browser:
# http://172.17.10.4:8090
```

## ⚠️ Checklist Security Production

Sebelum buka untuk dokter, **wajib** cek:

- [ ] `APP_ENV=production` di `.env`
- [ ] `APP_DEBUG=false` di `.env` (penting! kalau true, stack trace error akan expose path & SQL)
- [ ] `APP_KEY` ter-generate (cek `grep APP_KEY .env` ada base64:xxx)
- [ ] `DB_PASSWORD` strong (bukan default, bukan `root`/`123456`)
- [ ] `KHANZA_AES_USER_KEY` dan `KHANZA_AES_PASS_KEY` di-override dari default `nur`/`windi` kalau memungkinkan (sesuaikan dengan setting Khanza Anda)
- [ ] Firewall server hanya allow port yang perlu (22/80/443/8090)
- [ ] MySQL Khanza tidak expose ke internet (cek `bind-address`)
- [ ] HTTPS via Cloudflare Tunnel atau Let's Encrypt kalau akses dari luar LAN
- [ ] Backup `.env` & DB dijadwalkan
- [ ] Update Laravel & dependencies berkala: `docker compose exec edokter composer update --no-dev`

## 6. Setup Reverse Proxy di aaPanel (Opsional, untuk domain & HTTPS)

aaPanel UI → **Website → Add Site** → buat site `edokter.rsudbaubau.local` (atau IP), lalu:

**Site → Settings → Reverse Proxy**:
- Target URL: `http://127.0.0.1:8090`
- Send Domain: `$host`
- Enable cache: off (Laravel handle sendiri)

Lalu install SSL via Let's Encrypt kalau ada domain publik.

## 7. Berkas Digital — Share dengan Khanza (Opsional)

Kalau mau folder upload berkas dipakai bersama Khanza HYBRIDWEB di 172.17.10.5:

### Opsi A: NFS Mount
Di 172.17.10.5 (Khanza server) — install NFS server, export folder webapps:
```bash
apt install nfs-kernel-server
echo "/www/wwwroot/khanza/webapps  172.17.10.4(rw,sync,no_root_squash)" >> /etc/exports
exportfs -ra && systemctl restart nfs-kernel-server
```

Di 172.17.10.4 (Laravel server) — mount:
```bash
apt install nfs-common
mkdir -p /mnt/khanza-webapps
mount -t nfs 172.17.10.5:/www/wwwroot/khanza/webapps /mnt/khanza-webapps
# Persist di /etc/fstab:
echo "172.17.10.5:/www/wwwroot/khanza/webapps /mnt/khanza-webapps nfs defaults 0 0" >> /etc/fstab
```

Update `docker-compose.yml`:
```yaml
volumes:
  - /mnt/khanza-webapps:/khanza-webapps
```

Update `.env`:
```env
BERKAS_UPLOAD_PATH=/khanza-webapps/berkasrawat/pages/upload
```

### Opsi B: SMB/Samba (kalau Khanza di Windows Server)
Sama konsepnya, mount SMB share dari Khanza ke container.

### Opsi C: Local-only (paling sederhana)
Biarkan `BERKAS_UPLOAD_PATH=` kosong. File disimpan di volume `./public/webapps` Laravel container. File hanya bisa diakses via Laravel, tidak tampil di Khanza desktop.

## 8. Bridging PACS Orthanc (Radiologi)

Set di `.env`:
```env
ORTHANC_URL=http://IP-PACS-ORTHANC
ORTHANC_PORT=8042
ORTHANC_USER=...
ORTHANC_PASS=...
# Folder arsip JPG hasil PACS (mount volume kalau pakai Docker, supaya tidak hilang saat rebuild)
ORTHANC_ARCHIVE_PATH=/var/orthanc-archive
```

Kalau mau persist arsip JPG, tambahkan volume mount di `docker-compose.yml`:
```yaml
volumes:
  - ./orthanc-archive:/var/orthanc-archive
```

Asumsi: `PatientID` di Orthanc = `no_rkm_medis` Khanza (sama persis dengan pola bridging Khanza desktop).
Credential Orthanc TIDAK pernah kirim ke browser — semua request diproxy via Laravel.

## 9. BPJS I-Care / VClaim

```env
BPJS_CONS_ID=<cons-id-faskes>
BPJS_CONS_PWD=<secret-key>
BPJS_USER_KEY=<user-key>
BPJS_BASE_URL=https://apijkn.bpjs-kesehatan.go.id/vclaim-rest
# I-Care WAJIB lengkap sampai "/api/rs" (TANPA trailing slash)
BPJS_ICARE_BASE_URL=https://apijkn.bpjs-kesehatan.go.id/wsihs/api/rs
# Kosongkan kalau user_key Vclaim = user_key I-Care (umum di banyak RS)
BPJS_ICARE_USER_KEY=
```

Mapping dokter: pastikan tabel `maping_dokter_dpjpvclaim` di Khanza sudah berisi `kd_dokter_bpjs`
untuk setiap dokter yang akan pakai I-Care. Tanpa mapping, BPJS akan reject.

## 10. Update & Maintenance

**WAJIB urutan ini setiap kali ada `git pull`:**

```bash
cd /www/wwwroot/edokter
git pull
docker compose exec edokter composer install --no-dev --optimize-autoloader
docker compose exec edokter rm -f bootstrap/cache/config.php
docker compose exec edokter php artisan config:clear
docker compose exec edokter php artisan cache:clear
docker compose exec edokter php artisan view:clear
docker compose exec edokter php artisan migrate --force   # kalau ada migration
docker compose restart edokter
```

**Kalau ada perubahan Dockerfile / package PHP baru:**
```bash
docker compose down
docker compose up -d --build
```

**Kalau perlu cek vendor package tertentu sudah ter-install:**
```bash
docker compose exec edokter test -f vendor/<vendor>/<package>/composer.json && echo OK || echo MISSING
```

## Troubleshooting

| Masalah | Solusi |
|---------|--------|
| `SQLSTATE[HY000] [2002] Connection refused` | MySQL Khanza belum allow 172.17.10.4. Cek `bind-address` di my.cnf & GRANT user |
| `Permission denied` di storage/ | `docker compose exec edokter chown -R www-data:www-data storage` |
| Upload berkas 413 Request Entity Too Large | Naikkan `client_max_body_size` di `docker/nginx.conf` + `upload_max_filesize` di PHP |
| Halaman blank / 500 | `docker compose logs edokter`, dan `docker compose exec edokter cat storage/logs/laravel.log` |
| `config()` lama setelah ubah .env | `docker compose exec edokter rm -f bootstrap/cache/config.php && php artisan config:clear` + restart |
| Timezone salah | Pastikan `APP_TIMEZONE=Asia/Makassar` di .env + restart container |
| **I-Care 404 / `Could not resolve host: validate`** | Config cache stale. Hapus `bootstrap/cache/config.php` + `config:clear` + restart |
| **I-Care `Class "LZCompressor\LZString" not found`** | `composer install --no-dev` belum jalan setelah git pull. Lihat section 10 |
| **I-Care 404 `No Mapping Rule matched`** | `BPJS_ICARE_BASE_URL` di .env salah path. Harus lengkap sampai `/api/rs` tanpa trailing slash |
| **I-Care: pop-up "Dokter belum di-mapping"** | Tambahkan record di tabel `maping_dokter_dpjpvclaim` di Khanza dengan `kd_dokter_bpjs` valid |
| **I-Care: error "Named Pipes Provider, error: 40"** | Bukan dari kita — SQL Server di sisi BPJS yang error. Tunggu/lapor BPJS. Coba juga di Khanza desktop |
| **Orthanc: gambar tidak muncul, log "Connection refused"** | Container Laravel tidak bisa reach IP Orthanc. Cek firewall + `ORTHANC_URL` di .env |
| **Orthanc: gambar muncul lalu hilang setelah rebuild container** | `ORTHANC_ARCHIVE_PATH` belum di-mount sebagai volume. Lihat section 8 |
| `LOG_LEVEL=warning` di .env | Wajar log `[ICare] Request/Response` info tidak muncul. Ganti ke `debug` saat troubleshooting, lalu balikkan |

## Backup

```bash
# Backup .env + volume Laravel
cd /www/wwwroot/edokter
tar czf edokter-backup-$(date +%F).tar.gz .env public/webapps
docker run --rm -v edokter_edokter-storage:/data -v $PWD:/backup alpine \
    tar czf /backup/storage-$(date +%F).tar.gz -C /data .
```

DB Khanza di-backup terpisah di server 172.17.10.5.
