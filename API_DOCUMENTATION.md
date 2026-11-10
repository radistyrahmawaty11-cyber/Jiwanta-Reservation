# API Documentation — Jiwanta Reservation System

Endpoint memakai **session cookie + CSRF**, tanpa prefix `/api` dan tanpa Bearer token. Ada 3 role: `pengunjung`, `petugas`, `admin`. Pembayaran mendukung transfer manual dan Midtrans. Realtime memakai Laravel Reverb.

- **Base URL:** `http://127.0.0.1:8000`
- **Format:** `application/json`, `form-urlencoded`, atau `multipart/form-data`
- **Auth:** session Laravel (`auth` + `role:...`)
- **CSRF:** wajib untuk `POST`, `PUT`, `PATCH`, `DELETE`

---

## 1. Autentikasi & CSRF

Setelah login, sertakan cookie session. Untuk request yang mengubah data, kirim salah satu:

- field `_token`
- header `X-CSRF-TOKEN`
- header `X-XSRF-TOKEN`

```javascript
fetch('/reservasi/checkout', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
  },
  credentials: 'same-origin',
  body: JSON.stringify(payload)
});
```

Respons akses ditolak: `302` ke `/login` (HTML), `401` (JSON belum login), `403` (role salah).

---

## 2. Konvensi Respons

Sukses JSON umumnya:

```json
{ "ok": true, "data": {} }
```

Error validasi (`422`):

```json
{ "message": "The given data was invalid.", "errors": { "email": ["Email tersebut sudah terdaftar."] } }
```

Error bisnis (`404`/`409`/`422`/`502`/`503`):

```json
{ "ok": false, "message": "Kuota Tiket Renang Classic tidak mencukupi (sisa 3 tiket)." }
```

| Status | Arti |
| --- | --- |
| `200` | Berhasil |
| `302` | Redirect setelah aksi form |
| `400` | Payload callback Midtrans tidak lengkap |
| `401` / `403` | Belum login / tidak berwenang |
| `404` | Data tidak ditemukan |
| `409` | Konflik status (mis. sudah lunas) |
| `422` | Validasi atau aturan bisnis gagal |
| `502` | Gagal terhubung ke Midtrans |
| `503` | Midtrans belum dikonfigurasi |

---

## 3. Daftar Endpoint

### 3.1 Autentikasi (guest)

| Method | Path | Respons |
| --- | --- | --- |
| GET/POST | `/login` | HTML / redirect |
| GET/POST | `/register` | HTML / redirect |
| GET/POST | `/forgot-password` | HTML / redirect + `reset_url` |
| GET | `/reset-password/{token}` | HTML |
| POST | `/reset-password` | Redirect ke `/login` |
| POST | `/otp/send` | JSON |
| POST | `/otp/verify` | JSON |
| GET/POST | `/auth/google` | Redirect ke Google |
| GET | `/auth/google/callback` | Redirect |
| POST | `/logout` | Redirect ke `/login` |
| GET | `/` | Redirect sesuai role |

Login memakai field `whatsapp` (email atau nomor HP), `password`, opsional `remember`.
Redirect role: admin → `/admin/dashboard`, petugas → `/petugas/dashboard`, pengunjung → `/dashboard`.

**OTP demo** — `POST /otp/send` `{ "whatsapp": "0812..." }`:

```json
{ "target": "0812******90", "dev_code": "418205", "message": "Kode OTP berhasil dikirim (mode demo).", "expires_in": 300 }
```

`POST /otp/verify` `{ "code": "418205" }` → `{ "ok": true, "url": "http://127.0.0.1:8000/dashboard" }`. Kode berlaku 5 menit.

### 3.2 Pengunjung

| Method | Path | Respons |
| --- | --- | --- |
| GET | `/dashboard` | HTML |
| GET | `/dashboard/feed` | JSON |
| GET | `/tiket?trx=` | HTML |
| GET | `/reservasi?tab=tiket\|cabin` | HTML |
| GET | `/reservasi/availability` | JSON |
| POST | `/reservasi/checkout` | Redirect ke `/transaksi` |
| GET | `/transaksi` | HTML |
| POST | `/transaksi/metode` | JSON |
| POST | `/transaksi/kirim` | JSON |
| POST | `/transaksi/midtrans/pay` | JSON |
| POST | `/transaksi/midtrans/qris` | JSON |
| POST | `/transaksi/midtrans/charge` | JSON |
| GET | `/transaksi/midtrans/status` | JSON |
| GET/POST | `/profil` | HTML / redirect |

**`GET /dashboard/feed`**

```json
{ "ok": true, "data": { "tikets": [{ "id": 1, "stok": 78 }], "cabins": [{ "id": 1, "status": "tersedia" }] } }
```

**`GET /reservasi/availability`** — `sold` = `true` jika cabin `terisi` atau kapasitas 0.

```json
{ "ok": true, "data": [{ "key": 1, "sold": false }, { "key": 2, "sold": true }] }
```

**`POST /reservasi/checkout`** — menerima format multi-item atau single-item. Multi-item:

```json
{
  "items": [
    { "tipe": "tiket", "tiket_id": 1, "jumlah": 2, "harga_satuan": 50000 },
    { "tipe": "cabin", "cabin_id": 3, "malam": 2, "durasi": 0, "harga_satuan": 750000 }
  ],
  "tanggal": "2026-11-10"
}
```

Single-item tiket: `{ "tipe": "tiket", "kategori": 1, "jumlah": 2, "total": 100000 }`.
Single-item cabin: `{ "tipe": "cabin", "unit": 3, "malam": 2, "total": 1500000 }`.
`durasi` 3–23 dianggap paket jam. `harga_satuan`/`total` 0 → pakai harga database.
Efek: transaksi `pending` dibuat, kuota tiket dikurangi, cabin ditandai `terisi`, ringkasan disimpan di session `order`. Gagal → redirect `/reservasi` dengan flash `error`.

**`POST /transaksi/metode`** `{ "metode": "BCA" }` (atau `online`).

```json
{ "ok": true, "metode": "BCA" }
```

Error: `404` pesanan tidak ditemukan, `409` sudah lunas, `422` metode tidak tersedia.

**`POST /transaksi/kirim`** (`multipart/form-data`) — field `bukti` (opsional, JPG/PNG maks 5 MB). Menandai bukti terkirim, update `bukti_transfer`, kirim event `order.placed`.

```json
{ "url": "http://127.0.0.1:8000/tiket" }
```

**Midtrans**

- `POST /transaksi/midtrans/pay` → `{ "ok": true, "token": "...", "redirect_url": "...", "order_id": "TRX-..." }`
  Jika token sudah di-cache, `redirect_url` tidak dikirim.
- `POST /transaksi/midtrans/charge` `{ "metode": "bca" | "mandiri" | "qris" }`:
  - BCA → `{ "ok": true, "metode": "bca", "bank": "bca", "va_number": "...", "expiry_time": "..." }`
  - Mandiri → `{ "ok": true, "metode": "mandiri", "bill_key": "...", "biller_code": "70012", "expiry_time": "..." }`
  - QRIS → `{ "ok": true, "metode": "qris", "qr_url": "...", "expiry_time": "..." }`
- `POST /transaksi/midtrans/qris` setara `charge` dengan metode `qris`.
- `GET /transaksi/midtrans/status?sync=1`:

```json
{ "ok": true, "kode": "TRX-...", "status": "lunas", "payment_type": "bank_transfer", "paid_at": "...", "redirect_url": "http://127.0.0.1:8000/tiket" }
```

**`POST /profil`** (`multipart/form-data`) — `nama_pengguna`, `email`, `nohp`, opsional `foto` (maks 2 MB), `password_lama`, `password` + `password_confirmation`. Foto otomatis dioptimalkan.

### 3.3 Admin

| Method | Path | Respons |
| --- | --- | --- |
| GET | `/admin/dashboard` | HTML |
| GET | `/admin/cabin`, `/cabin` | HTML |
| GET | `/admin/cabin/tambah` | HTML |
| POST | `/admin/cabin` | Redirect |
| GET | `/admin/cabin/{id}/edit` | HTML |
| PUT | `/admin/cabin/{id}` | Redirect |
| PUT | `/admin/cabin/{id}/kapasitas` | JSON |
| DELETE | `/admin/cabin/{id}` | Redirect |
| GET | `/admin/tiket-renang`, `/tiket-renang` | HTML |
| POST | `/admin/tiket` | JSON |
| PUT | `/admin/tiket/{id}` | JSON |
| DELETE | `/admin/tiket/{id}` | JSON |
| PUT | `/admin/tiket/{id}/kuota` | JSON |
| POST | `/admin/tiket/buka-semua` | JSON |
| GET | `/admin/transaksi` | HTML |
| GET | `/admin/transaksi/export` | CSV |
| POST | `/admin/transaksi/{id}/verify` | JSON |
| POST | `/admin/transaksi/{id}/reject` | JSON |
| GET | `/admin/feed` | JSON |
| GET | `/admin/manajemen-pengguna` | HTML |
| POST | `/admin/pengguna` | JSON |
| PUT | `/admin/pengguna/{id}` | JSON |
| DELETE | `/admin/pengguna/{id}` | JSON |
| GET | `/admin/pengaturan` | HTML |

**Tiket** (`POST`/`PUT`, multipart): `name`, `price`, `cap`, opsional `desc`, `fasilitas`, `gambar` (maks 2 MB). Respons `formatTiket`:

```json
{ "ok": true, "data": { "id": 5, "name": "Tiket Renang Classic", "price": 50000, "cap": 100, "kategori": "dewasa", "sold": 0, "safeMax": 125, "image": "...", "updatedAt": 1760000000000 } }
```

`DELETE /admin/tiket/{id}` → `{ "ok": true, "message": "Tiket berhasil dihapus" }`.
`PUT /admin/tiket/{id}/kuota` `{ "cap": 80 }` (0–100000). `POST /admin/tiket/buka-semua` → `{ "ok": true }`.

**Cabin** (`POST`/`PUT`, multipart): `nama_cabin`, `harga_per_malam`, `kapasitas`, `status` (`tersedia`/`terisi`), opsional `deskripsi`, `fasilitas`, `gambar` (maks 2 MB).
`PUT /admin/cabin/{id}/kapasitas` `{ "kapasitas": 4 }` → `{ "ok": true, "id": 1, "kapasitas": 4 }`.

**Transaksi** — `verify` → `{ "ok": true, "status": "lunas" }`; `reject` → `{ "ok": true, "status": "gagal" }` (kuota dikembalikan).
`GET /admin/feed` mengembalikan `stats`, `rows` (maks 100), dan `pending_rows`.
`GET /admin/transaksi/export` mengunduh CSV kolom: `Kode, Tanggal, Pengguna, Metode, Total, Status`.

**Pengguna** (`POST`/`PUT`): `nama_pengguna`, `email`, `nohp`, `role`, `password` (saat update opsional). `DELETE` → `{ "ok": true }`.

### 3.4 Petugas

| Method | Path | Respons |
| --- | --- | --- |
| GET | `/petugas/dashboard` | HTML |
| GET | `/petugas/feed` | JSON |
| POST | `/petugas/scan` | JSON |
| POST | `/petugas/walkin` | JSON |
| POST | `/petugas/transaksi/{id}/verify` | JSON |
| POST | `/petugas/transaksi/{id}/reject` | JSON |
| POST | `/petugas/transaksi/{id}/check-in` | JSON |

**`GET /petugas/feed`** — `stats` (`gate`, `cap`, `verified`), `pending`, `history`, `layanan`, serta `scanUrl`, `walkinUrl`, `actionUrl`, `feedUrl`.

**`POST /petugas/scan`** `{ "code": "TRX-20261109-123" }` → `{ "ok": true, "reason": null, "transaksi": { ... } }`.
`reason`: `not_found`, `pending`, `rejected`, `used`, `expired` (semua tetap HTTP `200`).

**`POST /petugas/walkin`**:

```json
{ "nama": "Nama Tamu", "nohp": "081234567890", "layanan": "tiket:1", "tanggal": "2026-11-09", "jumlah": 3 }
```

`layanan` = `tiket:{id}` atau `cabin:{id}` (cabin memakai `malam`). Transaksi langsung `lunas`, metode `Tunai`, sudah check-in.

**`check-in`** → `{ "ok": true, "status": "lunas", "checked_in_at": "..." }`.
Gagal `422`: `pending`, `rejected`, `used` (+`checked_in_at`), `expired` (+`visit_date`).

### 3.5 Midtrans callback

`POST /midtrans/callback` — tanpa login, wajib signature valid:

```
SHA512(order_id + status_code + gross_amount + server_key)
```

Body wajib: `order_id`, `status_code`, `gross_amount`, `signature_key`, `transaction_status`.
Respons: `{ "ok": true, "status": "lunas" }`; error `400 invalid_payload`, `403 invalid_signature` / `amount_mismatch`.

Pemetaan status:

| Midtrans | Aplikasi |
| --- | --- |
| `settlement`, `capture` | `lunas` |
| `capture` + `fraud_status=deny` | `gagal` |
| `deny`, `cancel`, `expire` | `gagal` |
| Status lain (termasuk `pending`, `failure`) | `pending` |

Status `lunas` tidak pernah diturunkan kembali.

---

## 4. Realtime (Laravel Reverb)

| Event | Channel | Kapan |
| --- | --- | --- |
| `order.placed` | `admin.orders` | Pengunjung mengirim bukti pembayaran |
| `order.status` | `user.{pengguna_id}` | Status berubah (verify/reject/check-in/callback) |

Payload `order.placed`: `transaksi_id`, `kode`, `judul`, `tipe`, `tamu`, `total`, `total_label`, `pemesan`, `waktu`.
Payload `order.status`: `kode`, `status`, `checked_in`.

```javascript
window.Echo.channel('admin.orders').listen('.order.placed', console.log);
window.Echo.channel('user.' + userId).listen('.order.status', console.log);
```

---

## 5. Model Data & Enum

| Tabel | Field penting |
| --- | --- |
| `penggunas` | `nama_pengguna`, `email` (unik), `password`, `nohp`, `foto`, `role` |
| `tikets` | `nama_tiket`, `deskripsi`, `harga`, `stok`, `kategori`, `gambar`, `fasilitas` |
| `cabins` | `nama_cabin`, `deskripsi`, `harga_per_malam`, `kapasitas`, `fasilitas`, `gambar`, `status` |
| `reservasis` | `pengguna_id`, `cabin_id`, `renang_id`, `tanggal_checkin`, `tanggal_checkout`, `jumlah_tamu`, `total_harga`, `status` |
| `transaksis` | `kode_transaksi` (unik, `TRX-YYYYMMDD-NNN`), `midtrans_order_id`, `snap_token`, `total_amount`, `total_bayar`, `kembalian`, `metode_pembayaran`, `payment_type`, `payment_detail`, `paid_at`, `verified_at`, `checked_in_at`, `bukti_transfer`, `status`, `tanggal_transaksi` |
| `detailtransaksis` | `transaksi_id`, `tiket_id`, `reservasi_id`, `jumlah`, `harga_satuan`, `subtotal` |
| `banks` | `nama_bank`, `nomor_rekening`, `atas_nama`, `status` |

Enum/logika status:

| Data | Nilai |
| --- | --- |
| Role | `admin`, `petugas`, `pengunjung` |
| Transaksi | `pending`, `lunas`, `gagal` |
| Reservasi | `pending`, `confirmed`, `cancelled` |
| Cabin | `tersedia`, `terisi` |
| Kategori tiket | `dewasa`, `anak-anak`, `lansia` |

---

## 6. Alur Singkat

**Pengunjung:** checkout → transaksi `pending` + kuota dipotong → pilih metode → kirim bukti (transfer) atau bayar Midtrans → admin/petugas verifikasi → `lunas`/`gagal`. Jika `gagal`, kuota dikembalikan dan cabin dibuka.

**Walk-in:** petugas input tamu + layanan → pengguna baru otomatis → transaksi `lunas` (`Tunai`) + check-in + kuota dipotong.

**Aturan:** `lunas` final; tiket yang sudah check-in tidak bisa check-in ulang; tiket dengan tanggal kunjungan lewat dianggap `expired`; cabin kapasitas 0 atau `terisi` tidak bisa dipesan.

---

## 7. Catatan Implementasi

- Jalankan: `php artisan serve`; realtime: `php artisan reverb:start`.
- Env penting: `APP_URL`, `DB_*`, `SESSION_DRIVER`, `BROADCAST_CONNECTION=reverb`, `REVERB_*`, `MIDTRANS_CLIENT_KEY/SERVER_KEY/SANDBOX`, `GOOGLE_CLIENT_ID/SECRET/REDIRECT_URI`.
- Upload: foto tiket/cabin/profil maks 2 MB, bukti transfer maks 5 MB. Foto tiket/cabin/profil dikompres via `App\Support\ImageOptimizer`.
- File disimpan di disk `public` dan diakses lewat `/storage/{path}` (jalankan `php artisan storage:link`).
- OTP dan tautan reset kata sandi berjalan dalam mode demo.
