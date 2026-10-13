<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CabinController;
use App\Http\Controllers\PengunjungController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\TiketController;
use App\Http\Controllers\TransaksiController;
use App\Models\Cabin;
use App\Models\Pengguna;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// ==========================================
// 1. ROUTES TAMU (GUEST) - Belum Login
// ==========================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.process');

    // Lupa & atur ulang kata sandi
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

    // Login cepat: OTP WhatsApp & Google
    Route::post('/otp/send', [AuthController::class, 'sendOtp'])->name('otp.send');
    Route::post('/otp/verify', [AuthController::class, 'verifyOtp'])->name('otp.verify');
    Route::post('/auth/google', [AuthController::class, 'google'])->name('auth.google');
});

// ==========================================
// 2. ROUTES UMUM (Sudah Login)
// ==========================================
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', function () {
        return match (auth()->user()->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'petugas' => redirect()->route('petugas.dashboard'),
            default => redirect()->route('dashboard'),
        };
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
    // ROUTES TRANSAKSI (READ-ONLY: laporan dari pemesanan pengunjung)
    // ==========================================
    Route::get('/admin/transaksi', [TransaksiController::class, 'index'])->name('admin.transaksi');
    Route::get('/admin/transaksi/export', [TransaksiController::class, 'export'])->name('admin.transaksi.export');
    Route::get('/admin/feed', [TransaksiController::class, 'feed'])->name('admin.feed');

    // ==========================================
    // ROUTES LAINNYA (Fallback agar sidebar tidak error)
    // ==========================================
    Route::get('/admin/verifikasi', function () {
        return view('admin.verifikasi');
    })->name('verifikasi');
    Route::get('/verifikasi', function () {
        return view('admin.verifikasi');
    })->name('admin.verifikasi');

    Route::get('/admin/manajemen-pengguna', function () {
        return view('admin.manajemen-pengguna');
    })->name('manajemen-pengguna');
    Route::get('/admin/laporan', function () {
        return view('admin.laporan');
    })->name('laporan');
    Route::get('/admin/pengaturan', function () {
        return view('admin.pengaturan');
    })->name('pengaturan');
});

// ==========================================
// 4. ROUTES KHUSUS PETUGAS
// ==========================================
Route::middleware(['auth', 'role:petugas'])->group(function () {
    Route::get('/petugas/dashboard', [PetugasController::class, 'index'])->name('petugas.dashboard');
    Route::get('/petugas/feed', [PetugasController::class, 'feed'])->name('petugas.feed');
    Route::post('/petugas/scan', [PetugasController::class, 'scan'])->name('petugas.scan');
    Route::post('/petugas/transaksi/{transaksi}/verify', [PetugasController::class, 'verify'])->name('petugas.transaksi.verify');
    Route::post('/petugas/transaksi/{transaksi}/reject', [PetugasController::class, 'reject'])->name('petugas.transaksi.reject');
});

// ==========================================
// 5. ROUTES KHUSUS PENGUNJUNG
// ==========================================
Route::middleware(['auth', 'role:pengunjung'])->group(function () {
    Route::get('/dashboard', [PengunjungController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/feed', [PengunjungController::class, 'feed'])->name('dashboard.feed');
    Route::get('/tiket', [PengunjungController::class, 'tiket'])->name('tiket');
    Route::get('/reservasi', [PengunjungController::class, 'reservasi'])->name('reservasi');
    Route::get('/transaksi', [PengunjungController::class, 'transaksi'])->name('transaksi');

    // Simpan pesanan dari form reservasi ke session + database, lalu arahkan ke checkout
    Route::post('/reservasi/checkout', [PengunjungController::class, 'checkout'])->name('reservasi.checkout');

    // Tandai bukti transfer terkirim lalu arahkan ke halaman Tiket Saya
    Route::post('/transaksi/kirim', [PengunjungController::class, 'kirim'])->name('transaksi.kirim');

    // Pengaturan profil: lihat & ubah data akun
    Route::get('/profil', function (Request $request) {
        return view('pengunjung.profil', ['user' => $request->user()]);
    })->name('profil');

    Route::post('/profil', function (Request $request) {
        $user = $request->user();

        $data = $request->validate([
            'nama_pengguna' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:50', 'unique:penggunas,email,'.$user->id],
            'nohp' => ['required', 'string', 'max:15'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
            'password_lama' => ['nullable', 'string'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ]);

        $user->nama_pengguna = $data['nama_pengguna'];
        $user->email = $data['email'];
        $user->nohp = $data['nohp'];

        if ($request->hasFile('foto')) {
            if ($user->foto) {
                Storage::disk('public')->delete($user->foto);
            }

            $user->foto = $request->file('foto')->store('profil', 'public');
        }

        if (! empty($data['password'])) {
            if (! Hash::check($data['password_lama'] ?? '', $user->password)) {
                return back()
                    ->withErrors(['password_lama' => 'Kata sandi saat ini tidak sesuai.'])
                    ->withInput();
            }

            $user->password = $data['password'];
        }

        $user->save();

        return redirect()->route('profil')->with('status', 'Profil berhasil diperbarui.');
    })->name('profil.update');
});
