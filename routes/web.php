<?php

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
