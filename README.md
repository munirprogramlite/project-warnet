# Warnet Booking

Aplikasi reservasi PC warnet. Pengguna bisa melihat PC yang tersedia, membuat booking, dan melihat riwayatnya. Admin bisa mengonfirmasi atau membatalkan booking.

- **Frontend:** Android, dibuat dengan MIT App Inventor
- **Backend:** REST API sederhana dengan PHP dan MySQL
- **Komunikasi:** HTTP + JSON, autentikasi dengan token

## Struktur repository

```
project-warnet/
├── README.md
├── app-api/                    # backend PHP
│   ├── index.php               # pintu masuk (router berdasarkan ?action=)
│   ├── config.php              # koneksi database dan fungsi bantu
│   ├── auth/                   # login dan register
│   ├── dashboard.php
│   ├── tentang.php
│   ├── pc.php
│   └── booking.php
├── database/
│   └── schema.sql              # struktur tabel dan data contoh
├── app-inventor/
│   └── TugasAkhir_35_final.aia # project App Inventor
└── screenshots/                # tangkapan layar aplikasi
```

## Cara menjalankan

1. Pasang **XAMPP** dan nyalakan **Apache** dan **MySQL**.
2. Salin folder `app-api` ke `htdocs/project-warnet/app-api`.
3. Buka phpMyAdmin, buat database `app_warnet`, lalu **import** `database/schema.sql`.
4. Buka [MIT App Inventor](https://appinventor.mit.edu), pilih **Projects → Import project (.aia)**, lalu pilih file di folder `app-inventor/`.
5. Cek IP laptop (`ipconfig` di Windows, lihat *IPv4 Address*), lalu ubah `baseURL` di `Screen1` menjadi:
   ```
   http://<IP-LAPTOP>/project-warnet/app-api/index.php
   ```
6. HP dan laptop harus berada di **WiFi yang sama**. Sambungkan lewat **Connect → AI Companion** lalu scan QR.

Untuk memastikan API jalan, buka di browser HP:

```
http://<IP-LAPTOP>/project-warnet/app-api/index.php?action=login&username=user1&password=user123
```

Hasilnya harus berupa JSON dengan `"status":true`.

## Akun uji

| Username | Password | Role |
|---|---|---|
| admin | admin123 | admin |
| user1 | user123 | user |

## Fitur

| Screen | Fungsi | Role |
|---|---|---|
| Login | Masuk dengan username dan password | Semua |
| Home | Sapaan, statistik booking, menu | Semua |
| Booking Baru | Pilih PC, tanggal, jam, dan durasi | Semua |
| Riwayat | Daftar booking milik sendiri | Semua |
| Tentang | Profil akun dan info aplikasi | Semua |
| Kelola Booking | Konfirmasi atau batalkan booking | Admin |
| Logout | Mengosongkan token | Semua |

Tombol **Kelola Booking** hanya tampil untuk admin. Pembatasan sebenarnya ada di server (`require_admin()`), jadi user biasa tetap ditolak walau memanggil URL-nya langsung.

## Endpoint API

Semua request lewat `index.php?action=...`. Selain `login` dan `register`, setiap request memerlukan `token`.

| action | mode | Fungsi | Role |
|---|---|---|---|
| `login` | | Login, mengembalikan token (berlaku 7 hari) | Semua |
| `register` | | Daftar akun baru (role selalu `user`) | Semua |
| `dashboard` | | Data user dan statistik | Semua |
| `tentang` | | Profil dan info aplikasi | Semua |
| `pc` | `list` | Daftar PC | Semua |
| `pc` | `tambah`, `toggle-status`, `hapus` | Kelola PC | Admin |
| `booking` | `list` | Booking milik sendiri | Semua |
| `booking` | `tambah` | Buat booking (jadwal bentrok ditolak) | Semua |
| `booking` | `batal` | Batalkan booking sendiri yang masih pending | Semua |
| `booking` | `admin-list` | Semua booking | Admin |
| `booking` | `admin-update` | Ubah status booking | Admin |

## Catatan keamanan

Aplikasi ini dirancang untuk dijalankan di **jaringan lokal** sebagai tugas. Yang sudah diterapkan:

- Query memakai *prepared statement* (aman dari SQL injection)
- Password disimpan sebagai hash bcrypt (`password_hash`)
- Hak akses admin dicek di server, bukan hanya di tampilan app
- Token acak dengan masa berlaku

Yang **belum** siap untuk dipasang di internet:

- Password dikirim lewat URL (GET), seharusnya POST
- Belum memakai HTTPS
- Database memakai `root` tanpa password (bawaan XAMPP)
- Belum ada pembatasan percobaan login
- Password akun uji lemah dan tertulis di README ini

## Keterbatasan yang diketahui

- **Menekan tombol dua kali saat koneksi lambat.** Tombol Kembali dan Login belum dinonaktifkan setelah ditekan. Dua tekanan cepat pada Kembali dapat menutup dua screen sekaligus dan membuat pengguna kembali ke Login. Dua tekanan pada Login mengirim dua request sehingga token pertama tertimpa. Solusinya: `set tombol.Enabled to false` di awal event.
- **Menghapus PC** (`pc&mode=hapus`) ikut menghapus semua booking PC itu (`ON DELETE CASCADE`). Fitur ini tidak dipakai di app. Untuk menonaktifkan PC, gunakan `toggle-status`.
- Admin dapat mengubah status booking apa pun, termasuk yang sudah `cancelled`.
- Pembatalan booking oleh user lewat app belum dibuat (API `booking&mode=batal` sudah tersedia).
- Belum ada screen pendaftaran akun (API `register` sudah tersedia).
- Alamat server (`baseURL`) harus diubah manual bila IP laptop berganti. Disarankan memberi IP statis atau DHCP reservation di router.

## Teknologi

PHP 8, MySQL 8, XAMPP, MIT App Inventor (komponen Web, TinyDB, ListView, Notifier).