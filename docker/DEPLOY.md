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

## 8. Update & Maintenance

```bash
cd /www/wwwroot/edokter
git pull
docker compose up -d --build
docker compose exec edokter php artisan migrate --force  # kalau ada migration
docker compose exec edokter php artisan config:cache
```

## Troubleshooting

| Masalah | Solusi |
|---------|--------|
| `SQLSTATE[HY000] [2002] Connection refused` | MySQL Khanza belum allow 172.17.10.4. Cek `bind-address` di my.cnf & GRANT user |
| `Permission denied` di storage/ | `docker compose exec edokter chown -R www-data:www-data storage` |
| Upload berkas 413 Request Entity Too Large | Naikkan `client_max_body_size` di `docker/nginx.conf` + `upload_max_filesize` di PHP |
| Halaman blank / 500 | `docker compose logs edokter`, dan `docker compose exec edokter cat storage/logs/laravel.log` |
| `config()` lama setelah ubah .env | `docker compose exec edokter php artisan config:clear && config:cache` |
| Timezone salah | Pastikan `APP_TIMEZONE=Asia/Makassar` di .env + restart container |

## Backup

```bash
# Backup .env + volume Laravel
cd /www/wwwroot/edokter
tar czf edokter-backup-$(date +%F).tar.gz .env public/webapps
docker run --rm -v edokter_edokter-storage:/data -v $PWD:/backup alpine \
    tar czf /backup/storage-$(date +%F).tar.gz -C /data .
```

DB Khanza di-backup terpisah di server 172.17.10.5.
