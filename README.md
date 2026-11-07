# Jiwanta Reservation App

Aplikasi reservasi dan penjualan tiket **Jiwanta Ciwidey Resort** berbasis web. Aplikasi ini mengelola pemesanan tiket kolam air panas, reservasi Cabin Suite, pembayaran, verifikasi tiket, hingga pemindaian tiket di gerbang masuk.

Dibangun dengan **Laravel 12**, **MySQL**, dan **Tailwind CSS**, serta mendukung pembayaran **Midtrans** (Snap, QRIS, BCA Virtual Account, Mandiri Bill) dan notifikasi realtime menggunakan **Laravel Reverb**.

---

## Daftar Isi

- [Fitur Utama](#fitur-utama)
- [Teknologi yang Digunakan](#teknologi-yang-digunakan)
- [Kebutuhan Sistem](#kebutuhan-sistem)
- [Instalasi](#instalasi)
- [Konfigurasi Tambahan](#konfigurasi-tambahan)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Akun Demo](#akun-demo)
- [Struktur Database](#struktur-database)
- [Alur Pemesanan](#alur-pemesanan)
- [Pengujian](#pengujian)

---

## Fitur Utama

### Pengunjung

- Registrasi, login, lupa kata sandi, dan reset kata sandi.
- Login cepat melalui kode OTP (mode demo) dan Google OAuth.
- Melihat katalog tiket renang dan Cabin Suite beserta harga, fasilitas, dan sisa kuota.
- Memesan tiket renang dan/atau cabin dalam satu transaksi.
- Reservasi cabin per jam (paket 3 jam) atau per malam (1–5 malam) dengan ketersediaan real-time.
- Memilih metode pembayaran:
  - Transfer bank manual (unggah bukti transfer).
  - Midtrans Snap.
  - QRIS.
  - BCA Virtual Account.
  - Mandiri Bill.
- Melihat tiket digital berupa QR Code/barcode setelah pembayaran lunas.
- Melihat riwayat transaksi dan status pembayaran.
- Mengubah profil, foto profil, dan kata sandi.
- Notifikasi status pesanan secara realtime.

### Petugas

- Dashboard pemantauan transaksi yang menunggu pembayaran.
- Memindai tiket (barcode/QR) untuk memeriksa validitas, status pembayaran, dan masa berlaku.
- Memverifikasi atau menolak pembayaran.
- Check-in tiket di gerbang masuk.
- Membuat transaksi **walk-in** langsung di lokasi (tiket atau cabin), otomatis lunas dan tercatat check-in.
- Melihat aktivitas dan notifikasi pesanan terbaru.

### Admin

- Dashboard statistik: pendapatan, jumlah transaksi, tiket terjual, cabin terisi, dan antrean verifikasi.
- Kelola tiket renang: tambah, ubah, hapus, unggah foto, fasilitas, serta atur kuota.
- Kelola Cabin Suite: tambah, ubah, hapus, atur harga, kapasitas, dan status unit.
- Melihat timeline booking cabin dan detail tamu.
- Kelola transaksi: verifikasi, tolak, dan ekspor data transaksi ke CSV.
- Kelola pengguna (admin, petugas, pengunjung): tambah, ubah, dan hapus.
- Melihat daftar rekening bank dan pengaturan profil admin.
- Notifikasi pesanan baru secara realtime.

---

## Teknologi yang Digunakan

| Komponen | Teknologi |
| --- | --- |
| Backend | Laravel 12, PHP 8.2 |
| Database | MySQL |
| Frontend | Blade Template, Tailwind CSS, JavaScript |
| Build Tool | Vite, Node.js |
| Realtime | Laravel Reverb + Pusher JS |
| Pembayaran | Midtrans |
| Barcode / QR | picqer/php-barcode-generator, bacon/bacon-qr-code |
| Autentikasi | Session, Google OAuth 2.0, OTP demo |
| Pengujian | PHPUnit, Laravel Pint |

---

## Kebutuhan Sistem

- PHP **8.2** atau lebih baru
- Composer
- MySQL / MariaDB
- Node.js dan npm
- Web server (Apache/Nginx) atau `php artisan serve`

---

## Instalasi

1. **Clone repositori**

   ```bash
   git clone <url-repositori> Jiwanta-reservation-app
   cd Jiwanta-reservation-app
   ```

2. **Pasang dependensi PHP dan JavaScript**

   ```bash
   composer install
   npm install
   ```

3. **Salin file environment**

   ```bash
   copy .env.example .env
   ```

   Di Linux/macOS:

   ```bash
   cp .env.example .env
   ```

4. **Generate application key**

   ```bash
   php artisan key:generate
   ```

5. **Buat database MySQL** dengan nama `jiwanta_db`, lalu sesuaikan `.env`:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=jiwanta_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. **Jalankan migrasi dan seeder**

   ```bash
   php artisan migrate --seed
   ```

   > Gunakan `php artisan migrate:fresh --seed` hanya jika ingin menghapus seluruh data lama.

7. **Buat symbolic link untuk penyimpanan file**

   ```bash
   php artisan storage:link
   ```

8. **Build aset frontend**

   ```bash
   npm run build
   ```

---

## Konfigurasi Tambahan

### Midtrans

Isi kredensial Midtrans pada `.env`:

```env
MIDTRANS_MERCHANT_ID=
MIDTRANS_CLIENT_KEY=
MIDTRANS_SERVER_KEY=
MIDTRANS_SANDBOX=true
```

Gunakan kredensial **Sandbox** untuk pengembangan dan **Production** saat aplikasi sudah siap dirilis.

### Google OAuth

Buat OAuth Client ID di Google Cloud Console, lalu isi:

```env
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI="${APP_URL}/auth/google/callback"
```

Pastikan Authorized Redirect URI di Google Cloud Console sama dengan nilai `GOOGLE_REDIRECT_URI`.

### Realtime dengan Laravel Reverb

```bash
php artisan reverb:install
php artisan reverb:start
```

Sesuaikan nilai berikut di `.env` jika diperlukan:

```env
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=
REVERB_APP_KEY=
REVERB_APP_SECRET=
REVERB_HOST=127.0.0.1
REVERB_PORT=8081
REVERB_SCHEME=http
```

Jika Reverb tidak dijalankan, aplikasi tetap dapat digunakan, tetapi notifikasi realtime tidak aktif.

---

## Menjalankan Aplikasi

Jalankan server Laravel:

```bash
php artisan serve
```

Jalankan Reverb di terminal terpisah (opsional):

```bash
php artisan reverb:start
```

Buka aplikasi di browser:

```text
http://127.0.0.1:8000
```

Untuk mode pengembangan dengan Vite dan queue:

```bash
composer dev
```

---

## Akun Demo

Akun berikut tersedia setelah menjalankan `php artisan migrate --seed`:

| Role | Email | Password |
| --- | --- | --- |
| Admin | `admin@jiwanta.com` | `admin123` |
| Petugas | `petugas@jiwanta.com` | `petugas123` |
| Pengunjung | `siti@gmail.com` | `123456` |

> Segera ubah kata sandi akun demo sebelum digunakan di lingkungan produksi.

---

## Struktur Database

Tabel utama yang digunakan:

| Tabel | Keterangan |
| --- | --- |
| `penggunas` | Data pengguna dengan role `admin`, `petugas`, dan `pengunjung`. |
| `tikets` | Data tiket renang, harga, stok/kuota, foto, dan fasilitas. |
| `cabins` | Data Cabin Suite, harga per malam, kapasitas, dan status unit. |
| `renangs` | Data fasilitas kolam renang. |
| `reservasis` | Data reservasi cabin dan status booking. |
| `transaksis` | Data transaksi, metode pembayaran, status, dan bukti transfer. |
| `detailtransaksis` | Rincian item transaksi (tiket atau reservasi cabin). |
| `detailtikets` | Detail tiket. |
| `banks` | Daftar rekening bank untuk pembayaran manual. |

Status transaksi: `pending`, `lunas`, `gagal`.

Status reservasi: `pending`, `confirmed`, `cancelled`.

---

## Alur Pemesanan

1. Pengunjung memilih tiket dan/atau cabin pada halaman **Reservasi**.
2. Sistem memeriksa sisa kuota tiket dan ketersediaan cabin.
3. Pesanan disimpan sebagai transaksi berstatus `pending`.
4. Pengunjung memilih metode pembayaran:
   - Transfer manual: unggah bukti, lalu petugas/admin memverifikasi.
   - Midtrans/QRIS/VA: status diperbarui otomatis melalui callback.
5. Setelah lunas, tiket digital dengan QR/barcode dapat digunakan.
6. Petugas memindai tiket di gerbang, lalu melakukan check-in.
7. Kuota tiket berkurang otomatis dan cabin ditandai terisi saat reservasi dikonfirmasi.

---

## Pengujian

Menjalankan seluruh test:

```bash
php artisan test
```

Menjalankan test tertentu:

```bash
php artisan test --filter=PengunjungFlowTest
```

Memeriksa format kode dengan Laravel Pint:

```bash
vendor/bin/pint
```

---

## Lisensi

Proyek ini dibuat untuk keperluan **UKK (Uji Kompetensi Keahlian) 2026** dan pengembangan sistem reservasi Jiwanta Ciwidey Resort.
