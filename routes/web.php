<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CabinController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\TiketController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\VerifikasiController;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CabinController;
use App\Http\Controllers\TiketController;
use App\Http\Controllers\RenangController;
use App\Http\Controllers\TransaksiController;

// ================= AUTH =================
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register', function () { return view('auth.register'); })->name('register');

// ================= PENGUNJUNG =================
Route::get('/pengunjung/dashboard', function () { return view('pengunjung.dashboard'); })->middleware('role:pengunjung')->name('dashboard');
Route::get('/pengunjung/transaksi', function () { return view('pengunjung.transaksi'); })->middleware('role:pengunjung')->name('transaksi');
Route::get('/pengunjung/reservasi', function () { return view('pengunjung.reservasi'); })->middleware('role:pengunjung')->name('reservasi');
Route::get('/pengunjung/tiket', function () { return view('pengunjung.tiket'); })->middleware('role:pengunjung')->name('tiket');

// ================= ADMIN =================
Route::get('/admin/dashboard', function () { return view('admin.dashboard'); })->middleware('role:admin')->name('admin.dashboard');
Route::get('/admin/verifikasi', function () { return view('admin.verifikasi'); })->middleware('role:admin')->name('verifikasi');
Route::get('/admin/tiket-renang', function () { return view('admin.tiket-renang'); })->middleware('role:admin')->name('tiket-renang');

<<<<<<< HEAD
// Panel Admin (full desktop)
Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
Route::post('/admin/dashboard/kuota', [DashboardController::class, 'updateKuota'])->name('admin.dashboard.kuota');

Route::get('/admin/verifikasi', [VerifikasiController::class, 'index'])->name('verifikasi');
Route::post('/admin/verifikasi/{transaksi}/approve', [VerifikasiController::class, 'approve'])->name('verifikasi.approve');
Route::post('/admin/verifikasi/{transaksi}/reject', [VerifikasiController::class, 'reject'])->name('verifikasi.reject');

Route::get('/admin/transaksi', [TransaksiController::class, 'index'])->name('admin.transaksi');
Route::get('/admin/transaksi/export', [TransaksiController::class, 'export'])->name('admin.transaksi.export');
Route::post('/admin/transaksi', [TransaksiController::class, 'store'])->name('admin.transaksi.store');
Route::put('/admin/transaksi/{transaksi}', [TransaksiController::class, 'update'])->name('admin.transaksi.update');
Route::delete('/admin/transaksi/{transaksi}', [TransaksiController::class, 'destroy'])->name('admin.transaksi.destroy');

Route::get('/admin/tiket-renang', [TiketController::class, 'index'])->name('tiket-renang');
Route::get('/admin/tiket', [TiketController::class, 'index']);
Route::post('/admin/tiket', [TiketController::class, 'store'])->name('tiket.store');
Route::post('/admin/tiket/buka-semua', [TiketController::class, 'bukaSemua'])->name('tiket.buka-semua');
Route::put('/admin/tiket/{tiket}', [TiketController::class, 'update'])->name('tiket.update');
Route::delete('/admin/tiket/{tiket}', [TiketController::class, 'destroy'])->name('tiket.destroy');
Route::put('/admin/tiket/{tiket}/kuota', [TiketController::class, 'kuota'])->name('tiket.kuota');

Route::get('/admin/cabin', [CabinController::class, 'index'])->name('cabin');
Route::post('/admin/cabin', [CabinController::class, 'store'])->name('cabin.store');
Route::put('/admin/cabin/{cabin}', [CabinController::class, 'update'])->name('cabin.update');
Route::delete('/admin/cabin/{cabin}', [CabinController::class, 'destroy'])->name('cabin.destroy');

Route::get('/admin/manajemen-pengguna', [PenggunaController::class, 'index'])->name('manajemen-pengguna');
Route::post('/admin/pengguna', [PenggunaController::class, 'store'])->name('pengguna.store');
Route::put('/admin/pengguna/{pengguna}', [PenggunaController::class, 'update'])->name('pengguna.update');
Route::delete('/admin/pengguna/{pengguna}', [PenggunaController::class, 'destroy'])->name('pengguna.destroy');

Route::get('/admin/laporan', [LaporanController::class, 'index'])->name('laporan');
Route::get('/admin/laporan/export', [LaporanController::class, 'export'])->name('laporan.export');

Route::get('/admin/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan');
Route::post('/admin/bank', [PengaturanController::class, 'storeBank'])->name('bank.store');
Route::put('/admin/bank/{bank}', [PengaturanController::class, 'updateBank'])->name('bank.update');
Route::delete('/admin/bank/{bank}', [PengaturanController::class, 'destroyBank'])->name('bank.destroy');

// Halaman Dashboard (setelah login)
Route::get('/petugas/dashboard', function () {
    return view('petugas.dashboard');
})->name('petugas.dashboard');

// Proses Login (WhatsApp / Email + kata sandi)
Route::post('/login', [AuthController::class, 'login'])->name('login.process');

// Login cepat: OTP WhatsApp (demo) & Google (demo)
Route::post('/login/otp', [AuthController::class, 'sendOtp'])->name('otp.send');
Route::post('/login/otp/verify', [AuthController::class, 'verifyOtp'])->name('otp.verify');
Route::post('/login/google', [AuthController::class, 'google'])->name('auth.google');

// Lupa & reset kata sandi
Route::get('/lupa-sandi', [AuthController::class, 'showForgot'])->name('password.request');
Route::post('/lupa-sandi', [AuthController::class, 'sendReset'])->name('password.email');
Route::get('/reset-sandi/{token}', [AuthController::class, 'showReset'])->name('password.reset');
Route::post('/reset-sandi', [AuthController::class, 'resetPassword'])->name('password.update');

// Keluar
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Proses Register (nanti ditambah logic)
Route::post('/register', function () {
    return redirect()->route('dashboard');
})->name('register.process');

// Halaman Transaksi (membaca pesanan terakhir dari session)
Route::get('/pengunjung/transaksi', function () {
    $order = session('checkout.order');

    return view('pengunjung.transaksi', [
        'order' => $order,
        'expiredAt' => $order ? Carbon::parse($order['berlaku_sampai']) : null,
    ]);
})->name('transaksi');

Route::get('/pengunjung/reservasi', function () {
    return view('pengunjung.reservasi');
})->name('reservasi');

// Proses checkout: validasi & hitung ulang total di server, simpan ke session
Route::post('/reservasi/checkout', function () {
    $d = request()->validate([
        'tipe' => ['required', 'in:tiket,cabin'],
        'tanggal' => ['required', 'date'],
        'kategori' => ['nullable', 'in:classic,premier'],
        'jumlah' => ['nullable', 'integer', 'min:1', 'max:20'],
        'unit' => ['nullable', 'in:shorts,suite'],
        'malam' => ['nullable', 'integer', 'min:1', 'max:5'],
    ]);

    if ($d['tipe'] === 'tiket') {
        $kategori = $d['kategori'] ?? 'premier';
        $jumlah = (int) ($d['jumlah'] ?? 2);
        $harga = $kategori === 'classic' ? 60000 : 75000;

        $order = [
            'tipe' => 'tiket',
            'judul' => 'Tiket Renang '.($kategori === 'classic' ? 'Classic' : 'Premier'),
            'badge' => 'Tiket Masuk & Kolam',
            'tamu' => $jumlah.' Pengunjung Dewasa',
            'tanggal' => $d['tanggal'],
            'harga_label' => 'Harga Satuan (x'.$jumlah.')',
            'harga_value' => 'Rp '.number_format($harga, 0, ',', '.').' x '.$jumlah,
            'subtotal_label' => 'Subtotal Tiket',
            'total' => $harga * $jumlah,
            'kategori' => $kategori,
            'jumlah' => $jumlah,
        ];
    } else {
        $unit = $d['unit'] ?? 'suite';
        $malam = (int) ($d['malam'] ?? 2);
        $info = [
            'shorts' => ['nama' => 'Shorts', 'harga' => 340000, 'per' => 'sewa', 'tamu' => '4 Tamu Dewasa'],
            'suite' => ['nama' => 'Suite', 'harga' => 1040000, 'per' => 'malam', 'tamu' => '4-5 Tamu Dewasa'],
        ][$unit];

        if ($info['per'] === 'sewa') {
            $total = $info['harga'];
            $hargaValue = 'Rp '.number_format($info['harga'], 0, ',', '.').' (per sewa)';
        } else {
            $total = $info['harga'] * $malam;
            $hargaValue = 'Rp '.number_format($info['harga'], 0, ',', '.').' x '.$malam.' Malam';
        }

        $order = [
            'tipe' => 'cabin',
            'judul' => 'Cabin '.$info['nama'],
            'badge' => 'Cabin Suite Ciwidey',
            'tamu' => $info['tamu'],
            'tanggal' => $d['tanggal'],
            'harga_label' => $info['per'] === 'sewa' ? 'Harga Sewa Unit' : 'Harga per Malam',
            'harga_value' => $hargaValue,
            'subtotal_label' => 'Subtotal Menginap',
            'total' => $total,
            'unit' => $unit,
            'malam' => $malam,
        ];
    }

    $order['kode'] = 'JRT-'.strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
    $order['dibuat'] = now()->toDateTimeString();
    $order['berlaku_sampai'] = Carbon::now()->addHours(2)->toIso8601String();

    session(['checkout.order' => $order]);

    return redirect()->route('transaksi');
})->name('checkout.process');

Route::get('/pengunjung/tiket', function () {
    return view('pengunjung.tiket', [
        'order' => session('checkout.order'),
    ]);
})->name('tiket');

// Kirim bukti pembayaran: tandai pesanan di session, lalu buka Tiket Saya
Route::post('/transaksi/kirim', function () {
    request()->validate([
        'bukti' => ['nullable', 'string', 'max:255'],
    ]);

    $order = session('checkout.order');
    if ($order) {
        $order['status'] = 'terkirim';
        $order['dikirim_at'] = now('Asia/Jakarta')->toIso8601String();
        if (request('bukti')) {
            $order['bukti_nama'] = mb_substr(basename(request('bukti')), 0, 120);
        }
        session(['checkout.order' => $order]);
    }

    return response()->json(['ok' => true, 'url' => route('tiket')]);
})->name('kirim.bukti');
=======
Route::get('/admin/cabin', [CabinController::class, 'index'])->middleware('role:admin')->name('cabin');
Route::post('/admin/cabin', [CabinController::class, 'store'])->middleware('role:admin')->name('cabin.store');
Route::delete('/admin/cabin/{id}', [CabinController::class, 'destroy'])->middleware('role:admin')->name('cabin.destroy');

Route::get('/admin/manajemen-pengguna', function () { return view('admin.manajemen-pengguna'); })->middleware('role:admin')->name('manajemen-pengguna');
Route::get('/admin/laporan', function () { return view('admin.laporan'); })->middleware('role:admin')->name('laporan');
Route::get('/admin/pengaturan', function () { return view('admin.pengaturan'); })->middleware('role:admin')->name('pengaturan');

Route::get('/admin/tiket', [TiketController::class, 'index'])->middleware('role:admin')->name('admin.tiket');
Route::post('/admin/tiket', [TiketController::class, 'store'])->middleware('role:admin')->name('admin.tiket.store');

Route::get('/admin/renang', [RenangController::class, 'index'])->middleware('role:admin')->name('admin.renang');
Route::post('/admin/renang', [RenangController::class, 'store'])->middleware('role:admin')->name('admin.renang.store');

Route::get('/admin/transaksi', [TransaksiController::class, 'index'])->middleware('role:admin')->name('admin.transaksi');
Route::post('/admin/transaksi', [TransaksiController::class, 'store'])->middleware('role:admin')->name('admin.transaksi.store');

// ================= PETUGAS =================
Route::get('/petugas/dashboard', function () { return view('petugas.dashboard'); })->middleware('role:petugas')->name('petugas.dashboard');
>>>>>>> d0c70c8d31d52cecb4d10594c5c6910a3c2cf03b
