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
Route::get('/dashboard', function () {
    return view('pengunjung.dashboard');
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
Route::get('/transaksi', function () {
    return view('pengunjung.transaksi');
})->name('transaksi');
