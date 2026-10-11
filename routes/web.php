<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CabinController;
use App\Http\Controllers\TiketController;
use App\Http\Controllers\TransaksiController;
use App\Models\Cabin;
use App\Models\Pengguna;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Route;

// ==========================================
// 1. ROUTES TAMU (GUEST) - Belum Login
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.process');

    // Route dummy agar komponen frontend tidak error
    Route::get('/forgot-password', function () {
        return back()->with('status', 'Fitur Lupa Password sedang dalam pengembangan.');
    })->name('password.request');

    Route::post('/forgot-password', function () {
        return back()->with('status', 'Fitur Lupa Password sedang dalam pengembangan.');
    })->name('password.email');

    Route::post('/otp/send', function () {
        return response()->json(['target' => request('whatsapp'), 'dev_code' => '123456', 'message' => 'OTP dikirim (Demo)']);
    })->name('otp.send');

    Route::post('/otp/verify', function () {
        $code = request('code');
        if ($code === '123456') {
            $user = \App\Models\Pengguna::first();
            \Illuminate\Support\Facades\Auth::login($user);
            return response()->json(['ok' => true, 'url' => '/']);
        }
        return response()->json(['ok' => false, 'message' => 'Kode OTP salah'], 422);
    })->name('otp.verify');

    Route::post('/auth/google', function () {
        return redirect('/login')->with('status', 'Fitur Google Login akan segera hadir!');
    })->name('auth.google');
});

// ==========================================
// 2. ROUTES UMUM (Sudah Login)
// ==========================================
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    })->name('home');
});

// ==========================================
// 3. ROUTES KHUSUS ADMIN
// ==========================================
Route::middleware(['auth', 'role:admin'])->group(function () {

    // Dashboard
    Route::get('/admin/dashboard', function () {
        $totalCabin = Cabin::count();
        $totalCabinTerisi = Cabin::where('status', 'terisi')->count();
        $totalPengguna = Pengguna::where('role', 'pengunjung')->count();
        $totalPendapatan = Transaksi::where('status', 'lunas')->sum('total_bayar');
        $tiketTerjual = Transaksi::where('status', 'lunas')->count();
        $perluVerifikasi = Transaksi::where('status', 'pending')->count();

        return view('admin.dashboard', compact(
            'totalCabin', 'totalCabinTerisi', 'totalPengguna',
            'totalPendapatan', 'tiketTerjual', 'perluVerifikasi'
        ));
    })->name('admin.dashboard');

    // ==========================================
    // ROUTES CABIN
    // ==========================================
    Route::get('/admin/cabin', [CabinController::class, 'index'])->name('admin.cabin.index');
    Route::get('/cabin', [CabinController::class, 'index'])->name('cabin');
    Route::get('/admin/cabin/tambah', [CabinController::class, 'create'])->name('admin.cabin.create');
    Route::post('/admin/cabin', [CabinController::class, 'store'])->name('admin.cabin.store');
    Route::get('/admin/cabin/{id}/edit', [CabinController::class, 'edit'])->name('admin.cabin.edit');
    Route::put('/admin/cabin/{id}', [CabinController::class, 'update'])->name('admin.cabin.update');
    Route::delete('/admin/cabin/{id}', [CabinController::class, 'destroy'])->name('admin.cabin.destroy');

    // ==========================================
    // ROUTES TIKET
    // ==========================================
    Route::get('/admin/tiket-renang', [TiketController::class, 'index'])->name('admin.tiket.index');
    Route::get('/tiket-renang', [TiketController::class, 'index'])->name('tiket-renang');
    Route::post('/admin/tiket', [TiketController::class, 'store'])->name('admin.tiket.store');
    Route::put('/admin/tiket/{id}', [TiketController::class, 'update'])->name('admin.tiket.update');
    Route::delete('/admin/tiket/{id}', [TiketController::class, 'destroy'])->name('admin.tiket.destroy');
    Route::put('/admin/tiket/{id}/kuota', [TiketController::class, 'updateQuota'])->name('admin.tiket.kuota');
    Route::post('/admin/tiket/buka-semua', [TiketController::class, 'openAll'])->name('admin.tiket.buka-semua');

    // ==========================================
    // ROUTES TRANSAKSI (SUDAH DIPERBAIKI NAMANYA)
    // ==========================================
    Route::get('/admin/transaksi', [TransaksiController::class, 'index'])->name('admin.transaksi'); // <-- INI YANG DIPERBAIKI
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi');
    Route::post('/admin/transaksi', [TransaksiController::class, 'store'])->name('admin.transaksi.store');
    Route::put('/admin/transaksi/{id}', [TransaksiController::class, 'update'])->name('admin.transaksi.update');
    Route::delete('/admin/transaksi/{id}', [TransaksiController::class, 'destroy'])->name('admin.transaksi.destroy');
    Route::get('/admin/transaksi/export', [TransaksiController::class, 'export'])->name('admin.transaksi.export');

    // ==========================================
    // ROUTES LAINNYA (Fallback agar sidebar tidak error)
    // ==========================================
    Route::get('/admin/verifikasi', function () { return view('admin.verifikasi'); })->name('verifikasi');
    Route::get('/verifikasi', function () { return view('admin.verifikasi'); })->name('admin.verifikasi');

    Route::get('/admin/manajemen-pengguna', function () { return view('admin.manajemen-pengguna'); })->name('manajemen-pengguna');
    Route::get('/admin/laporan', function () { return view('admin.laporan'); })->name('laporan');
    Route::get('/admin/pengaturan', function () { return view('admin.pengaturan'); })->name('pengaturan');
});
