<?php

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
})->name('dashboard');

Route::get('/admin/verifikasi', function () {
    return view('admin.verifikasi');
})->name('verifikasi');

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
})->name('dashboard');

// Proses Login (nanti ditambah logic)
Route::post('/login', function () {
    return redirect()->route('dashboard');
})->name('login.process');

// Proses Register (nanti ditambah logic)
Route::post('/register', function () {
    return redirect()->route('dashboard');
})->name('register.process');

// Halaman Transaksi
Route::get('/pengunjung/transaksi', function () {
    return view('pengunjung.transaksi');
})->name('transaksi');

Route::get('/pengunjung/reservasi', function () {
    return view('pengunjung.reservasi');
})->name('reservasi');

Route::get('/pengunjung/tiket', function () {
    return view('pengunjung.tiket');
})->name('tiket');
