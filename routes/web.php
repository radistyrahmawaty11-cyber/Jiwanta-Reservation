<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;

// Halaman Login
Route::get('/', function () {
    return view('auth.login');
})->name('login');

// Halaman Register
Route::get('/register', function () {
    return view('auth.register');
})->name('register');

// Halaman Dashboard (setelah login)
Route::get('/pengunjung/dashboard', function () {
    return view('pengunjung.dashboard');
})->name('dashboard');

// Halaman Dashboard (setelah login)
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->name('admin.dashboard');

Route::get('/admin/verifikasi', function () {
    return view('admin.verifikasi');
})->name('verifikasi');

Route::get('/admin/transaksi', function () {
    return view('admin.transaksi');
})->name('admin.transaksi');

Route::get('/admin/tiket-renang', function () {
    return view('admin.tiket-renang');
})->name('tiket-renang');

Route::get('/admin/cabin', function () {
    return view('admin.cabin');
})->name('cabin');

Route::get('/admin/manajemen-pengguna', function () {
    return view('admin.manajemen-pengguna');
})->name('manajemen-pengguna');

Route::get('/admin/laporan', function () {
    return view('admin.laporan');
})->name('laporan');

Route::get('/admin/pengaturan', function () {
    return view('admin.pengaturan');
})->name('pengaturan');

// Halaman Dashboard (setelah login)
Route::get('/petugas/dashboard', function () {
    return view('petugas.dashboard');
})->name('petugas.dashboard');

// Proses Login (nanti ditambah logic)
Route::post('/login', function () {
    return redirect()->route('dashboard');
})->name('login.process');

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
