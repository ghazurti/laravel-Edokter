# Reverse Proxy aaPanel → E-Dokter (nama domain lokal tanpa :8090)

Tujuan: dokter akses `http://edokter.rsud.local` (tanpa port), sementara
container Docker tetap jalan di `172.17.10.4:8090`. App Docker lain **tidak
terganggu** — aaPanel membagi berdasarkan nama domain di port 80.

```
Tablet ─▶ http://edokter.rsud.local ─▶ aaPanel nginx (port 80) ─▶ 172.17.10.4:8090 (container)
```

---

## Prasyarat: DNS sudah mengarah ke server aaPanel

`edokter.rsud.local` harus resolve ke IP server aaPanel (mis. `172.17.10.5`).
Lihat opsi DNS (router static DNS **atau** dnsmasq) di catatan terpisah.
Tes dari tablet/PC: `ping edokter.rsud.local` → harus balas IP aaPanel.

---

## Langkah di aaPanel

### 1. Buat website
- **Website → Add site**
- Domain: `edokter.rsud.local`
- PHP version: **Pure static / Tidak ada** (tidak perlu PHP, ini cuma proxy)
- Submit.

### 2. Aktifkan Reverse Proxy
- Buka site `edokter.rsud.local` → tab **Reverse Proxy → Add reverse proxy**
- **Proxy Name**: `edokter`
- **Target URL**: `http://172.17.10.4:8090`
- **Send Domain (host)**: `edokter.rsud.local`
- Submit.

### 3. Sempurnakan konfigurasi (penting untuk upload & Livewire)
Buka **Config** proxy yang baru dibuat (atau site Config file), pastikan blok
`location` proxy berisi directive berikut:

```nginx
location / {
    proxy_pass http://172.17.10.4:8090;
    proxy_set_header Host              $host;
    proxy_set_header X-Real-IP         $remote_addr;
    proxy_set_header X-Forwarded-For   $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;

    # Keepalive + WebSocket pass-through (jaga-jaga, Livewire 2 pakai AJAX)
    proxy_http_version 1.1;
    proxy_set_header Upgrade    $http_upgrade;
    proxy_set_header Connection "upgrade";

    # Query/laporan berat jangan ke-cut (samakan dgn FPM 120s)
    proxy_read_timeout 120s;
    proxy_send_timeout 120s;

    # Upload berkas rawat (PDF/scan) sampai ~25 MB
    client_max_body_size 30M;
}
```

> Catatan: kalau aaPanel pakai cache template proxy, matikan opsi **"Cache"**
> agar respons Livewire (POST `/livewire/message/...`) tidak ter-cache.

### 4. Reload nginx aaPanel
Lewat panel (Restart) atau: `nginx -t && nginx -s reload`.

---

## Penyesuaian sisi aplikasi (server Docker)

Edit `.env` (yang ter-mount ke container):

```env
APP_URL=http://edokter.rsud.local
```

`TrustProxies` sudah di-set `*` (membaca X-Forwarded-* dari aaPanel) — tidak
perlu diubah. Lalu terapkan:

```bash
cd /path/ke/laravel-edokter
docker compose up -d --build        # atau: docker exec edokter-rsud php artisan config:cache
```

---

## Verifikasi

1. Dari tablet: buka `http://edokter.rsud.local` → halaman login E-Dokter muncul,
   **tanpa** harus ketik `:8090`.
2. CSS/ikon/Chart tampil normal (aset ikut domain, tidak ada yang gagal load).
3. Login → klik tab/komponen → Livewire jalan (tidak ada error 419/404 di
   Network tab browser).
4. Coba upload berkas > 5 MB → tidak kena error `413 Request Entity Too Large`.
5. App Docker lain tetap bisa dibuka via IP:port masing-masing.

## Troubleshooting

| Gejala | Penyebab & solusi |
|---|---|
| Aset (css/js) gagal / tampilan polos | `APP_URL` belum di-set ke domain → set lalu `config:cache` |
| 413 saat upload | `client_max_body_size` di proxy < 30M → naikkan |
| 419 Page Expired saat submit | aaPanel meng-cache proxy → matikan Cache di reverse proxy |
| 504 saat buka laporan berat | `proxy_read_timeout` terlalu kecil → set 120s |
| Nama tak ke-resolve | DNS belum benar — cek `ping edokter.rsud.local` |
