# Setup Warnet Booking — Panduan Konfigurasi

## Prasyarat
- XAMPP / Laragon running (Apache + MySQL)
- HP dan laptop di **WiFi yang sama**
- MIT App Inventor (http://ai2.appinventor.mit.edu)

## Langkah Cepat

### 1. Import Database
- Buka phpMyAdmin → buat database `app_warnet`
- Import file `schema.sql`

### 2. Taruh PHP
Copy folder `app-api/` ke:
- XAMPP: `C:\xampp\htdocs\app-api\`
- Laragon: `C:\laragon\www\app-api\`

### 3. Cari IP Lokal Laptop
- **Windows:** `ipconfig` → catat `IPv4 Address`
- **Mac/Linux:** `ifconfig | grep "inet "`

Contoh: `192.168.1.10`

### 4. Test dari HP
Buka browser HP:
```
http://192.168.1.10/app-api/index.php?action=dashboard
```
Kalau muncul `{"status":false,"message":"Token kosong"}` → berhasil.

Kalau gagal:
- HP & laptop harus satu WiFi (bukan guest)
- Firewall Windows: Allow Apache
- Apache running di XAMPP

### 5. Sesuaikan URL di App Inventor
Buka `.aia` → Screen1 → Blocks → Screen1.Initialize.
Cari blok:
```
set TinyDB1.StoreValue tag "baseURL" value "http://192.168.1.10/app-api/index.php"
```
Ganti IP dengan IP laptopmu.

Screen Home & Konten otomatis baca dari TinyDB.

### 6. Test Alur
1. Login `user1` / `user123`
2. Booking Baru → pilih PC → isi jadwal → simpan
3. Cek Riwayat Booking
4. Logout → login `admin` / `admin123`
5. Kelola Booking → Approve salah satu
6. Login user1 lagi → status berubah jadi "confirmed"

## Akun Default
| Username | Password | Role |
|---|---|---|
| admin | admin123 | admin |
| user1 | user123 | user |

**Ganti password setelah demo!**

## Akses dari Jaringan Lain
IP lokal hanya jalan di WiFi yang sama. Untuk akses publik:
- **Cloudflare Tunnel:** `cloudflared tunnel --url http://localhost`
- **ngrok:** `ngrok http 80`
- **LocalTunnel:** `npx localtunnel --port 80`

Lalu ganti `baseURL` di TinyDB dengan URL tunnel.