<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CabinController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\PengunjungController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\TiketController;
use App\Http\Controllers\TransaksiController;
use App\Models\Bank;
use App\Models\Cabin;
use App\Models\Detailtransaksi;
use App\Models\Pengguna;
use App\Models\Tiket;
use App\Models\Transaksi;
use App\Support\ImageOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

// ==========================================
// 1. ROUTES TAMU (GUEST) - Belum Login
// ==========================================
Route::middleware(['guest', 'no-store'])->group(function () {
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
    Route::match(['get', 'post'], '/auth/google', [AuthController::class, 'googleRedirect'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'googleCallback'])->name('auth.google.callback');
});

// ==========================================
// 2. ROUTES UMUM (Sudah Login)
// ==========================================
Route::middleware(['auth', 'no-store'])->group(function () {
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
Route::middleware(['auth', 'role:admin', 'no-store'])->group(function () {

    // Dashboard
    Route::get('/admin/dashboard', function () {
        $today = now()->startOfDay();

        $totalCabin = Cabin::count();
        $totalCabinTerisi = Cabin::where('status', 'terisi')->count();
        $totalPengguna = Pengguna::where('role', 'pengunjung')->count();
        $totalPendapatan = Transaksi::where('status', 'lunas')->sum('total_bayar');
        $tiketTerjual = Transaksi::where('status', 'lunas')->count();
        $perluVerifikasi = Transaksi::where('status', 'pending')->count();
        $totalTransaksi = Transaksi::count();

        $pendapatanHariIni = Transaksi::where('status', 'lunas')
            ->whereDate('tanggal_transaksi', $today)->sum('total_bayar');
        $pendapatanKemarin = Transaksi::where('status', 'lunas')
            ->whereDate('tanggal_transaksi', $today->copy()->subDay())->sum('total_bayar');
        $growthPct = $pendapatanKemarin > 0
            ? round((($pendapatanHariIni - $pendapatanKemarin) / $pendapatanKemarin) * 100, 1)
            : ($pendapatanHariIni > 0 ? 100.0 : 0.0);

        // Kuota tiket: stok = sisa kuota (berkurang otomatis saat pengunjung memesan)
        $tiketKuota = Tiket::orderBy('id')->get()->map(fn ($t) => [
            'id' => (int) $t->id,
            'nama' => $t->nama_tiket,
            'kapasitas' => (int) $t->stok,
            'terjual' => (int) Detailtransaksi::where('tiket_id', $t->id)
                ->whereHas('transaksi', fn ($q) => $q->where('status', '!=', 'gagal'))
                ->sum('jumlah'),
        ])->values();

        $cabinQuota = Cabin::orderBy('id')->get(['id', 'nama_cabin', 'kapasitas'])->map(fn ($c) => [
            'id' => (int) $c->id,
            'nama' => $c->nama_cabin,
            'kapasitas' => (int) $c->kapasitas,
        ])->values();

        $avBgs = ['bg-[#CDEFD5]', 'bg-[#FBD9B0]', 'bg-[#DDE6FB]'];
        $codeBgs = ['bg-[#DDE2F5]', 'bg-[#F5C89A]', 'bg-[#D9E6FB]'];

        // Antrean verifikasi cepat (transaksi pending) dari DB
        $queue = Transaksi::with(['pengguna', 'detailtransaksis.tiket', 'detailtransaksis.reservasi'])
            ->where('status', 'pending')
            ->latest('tanggal_transaksi')
            ->limit(6)
            ->get()
            ->values()
            ->map(function ($t, $i) use ($avBgs, $codeBgs) {
                $nama = $t->pengguna->nama_pengguna ?? 'Tamu Umum';
                $ini = collect(preg_split('/\s+/', trim($nama)))
                    ->filter()
                    ->map(fn ($w) => strtoupper(substr($w, 0, 1)))
                    ->take(2)
                    ->implode('');
                $d = $t->detailtransaksis->first();
                $item = $d && $d->tiket
                    ? ['Tiket '.$d->tiket->nama_tiket, 'x'.$d->jumlah]
                    : ($d && $d->reservasi ? ['Cabin', 'x'.$d->jumlah] : ['Reservasi', 'x1']);

                return [
                    $ini,
                    $avBgs[$i % count($avBgs)],
                    $nama,
                    $t->kode_transaksi,
                    $codeBgs[$i % count($codeBgs)],
                    [$t->metode_pembayaran ?: 'Transfer', 'Transfer'],
                    $item,
                    (int) $t->total_bayar,
                    (int) $t->detailtransaksis->sum('jumlah'),
                ];
            });

        // Tabel reservasi terbaru dari DB
        $reservations = Transaksi::with(['pengguna', 'detailtransaksis.tiket', 'detailtransaksis.reservasi.cabin'])
            ->latest('tanggal_transaksi')
            ->limit(6)
            ->get()
            ->map(function ($t) {
                $d = $t->detailtransaksis->first();
                $isCabin = $d && $d->reservasi && $d->reservasi->cabin_id;
                $nama = $t->pengguna->nama_pengguna ?? 'Tamu Umum';
                $parts = preg_split('/\s+/', trim($nama));
                $guest = count($parts) > 1 ? [$parts[0], implode(' ', array_slice($parts, 1))] : [$nama];
                $service = $isCabin
                    ? ['Cabin', $d->reservasi->cabin->nama_cabin ?? 'Suite']
                    : ($d && $d->tiket ? ['Tiket Renang', $d->tiket->nama_tiket] : ['Reservasi', '-']);
                $label = match ($t->status) {
                    'lunas' => 'Lunas',
                    'gagal' => 'Gagal',
                    default => 'Menunggu',
                };

                return [
                    $isCabin ? 'cabin' : 'tiket',
                    [$t->kode_transaksi],
                    $guest,
                    [$t->pengguna->nohp ?? '-'],
                    $service,
                    [Carbon::parse($t->tanggal_transaksi)->format('d M Y, H:i').' WIB'],
                    $label,
                ];
            });

        // Ketersediaan cabin dari DB
        $cabins = Cabin::with(['reservasis' => fn ($q) => $q->where('status', 'confirmed')->with('pengguna')])
            ->orderBy('id')->limit(6)->get()->map(function ($c) {
                $busy = $c->status === 'terisi';
                $tamu = $busy ? $c->reservasis->first()?->pengguna?->nama_pengguna : null;

                return [
                    $c->nama_cabin,
                    $tamu ? 'Tamu: '.$tamu : ($busy ? 'Sedang dihuni' : 'Siap Reservasi'),
                    $busy ? 'Terisi' : 'Tersedia',
                    $busy,
                    'bed',
                ];
            });

        // Petugas lapangan dari DB
        $staffBgs = ['bg-[#DDE6FB]', 'bg-[#FBD9B0]', 'bg-[#CDEFD5]'];
        $staff = Pengguna::where('role', 'petugas')->orderBy('nama_pengguna')->limit(6)->get()
            ->values()
            ->map(function ($p, $i) use ($staffBgs) {
                $ini = collect(preg_split('/\s+/', trim($p->nama_pengguna)))
                    ->filter()->map(fn ($w) => strtoupper(substr($w, 0, 1)))->take(2)->implode('');

                return [
                    $ini,
                    $staffBgs[$i % count($staffBgs)],
                    $p->nama_pengguna,
                    ['Petugas Lapangan', $p->nohp ?: 'Tanpa No. HP'],
                ];
            });

        $todayLabel = now()->format('d M Y');

        return view('admin.dashboard', compact(
            'totalCabin', 'totalCabinTerisi', 'totalPengguna',
            'totalPendapatan', 'tiketTerjual', 'perluVerifikasi', 'tiketKuota',
            'growthPct', 'queue', 'reservations', 'cabins', 'staff', 'totalTransaksi', 'todayLabel', 'cabinQuota'
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
    Route::put('/admin/cabin/{id}/kapasitas', [CabinController::class, 'updateKapasitas'])->name('admin.cabin.kapasitas');
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
    // ROUTES TRANSAKSI (laporan + verifikasi pembayaran)
    // ==========================================
    Route::get('/admin/transaksi', [TransaksiController::class, 'index'])->name('admin.transaksi');
    Route::get('/admin/transaksi/export', [TransaksiController::class, 'export'])->name('admin.transaksi.export');
    Route::post('/admin/transaksi/{transaksi}/verify', [TransaksiController::class, 'verify'])->name('admin.transaksi.verify');
    Route::post('/admin/transaksi/{transaksi}/reject', [TransaksiController::class, 'reject'])->name('admin.transaksi.reject');
    Route::get('/admin/feed', [TransaksiController::class, 'feed'])->name('admin.feed');

    // ==========================================
    // ROUTES MANAJEMEN PENGGUNA (data asli dari DB)
    // ==========================================
    Route::get('/admin/manajemen-pengguna', [PenggunaController::class, 'index'])->name('manajemen-pengguna');
    Route::post('/admin/pengguna', [PenggunaController::class, 'store'])->name('admin.pengguna.store');
    Route::put('/admin/pengguna/{pengguna}', [PenggunaController::class, 'update'])->name('admin.pengguna.update');
    Route::delete('/admin/pengguna/{pengguna}', [PenggunaController::class, 'destroy'])->name('admin.pengguna.destroy');
    Route::get('/admin/pengaturan', function () {
        $user = auth()->user();

        $banks = Bank::orderByDesc('status')->orderBy('id')->get()->map(function ($b) {
            $nama = strtolower($b->nama_bank);
            $code = match (true) {
                str_contains($nama, 'central asia') || str_contains($nama, 'bca') => 'BCA',
                str_contains($nama, 'mandiri') => 'MDR',
                str_contains($nama, 'bni') => 'BNI',
                str_contains($nama, 'bri') => 'BRI',
                default => strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $b->nama_bank), 0, 3)),
            };

            return [
                'id' => $b->id,
                'code' => $code,
                'no' => $b->nomor_rekening,
                'an' => $b->atas_nama,
                'br' => '',
                'main' => (bool) $b->status,
            ];
        })->values();

        return view('admin.pengaturan', compact('user', 'banks'));
    })->name('pengaturan');
});

// ==========================================
// 4. ROUTES KHUSUS PETUGAS
// ==========================================
Route::middleware(['auth', 'role:petugas', 'no-store'])->group(function () {
    Route::get('/petugas/dashboard', [PetugasController::class, 'index'])->name('petugas.dashboard');
    Route::get('/petugas/feed', [PetugasController::class, 'feed'])->name('petugas.feed');
    Route::post('/petugas/scan', [PetugasController::class, 'scan'])->name('petugas.scan');
    Route::post('/petugas/walkin', [PetugasController::class, 'walkIn'])->name('petugas.walkin');
    Route::post('/petugas/transaksi/{transaksi}/verify', [PetugasController::class, 'verify'])->name('petugas.transaksi.verify');
    Route::post('/petugas/transaksi/{transaksi}/reject', [PetugasController::class, 'reject'])->name('petugas.transaksi.reject');
    Route::post('/petugas/transaksi/{transaksi}/check-in', [PetugasController::class, 'checkIn'])->name('petugas.transaksi.checkin');
});

// ==========================================
// 5. ROUTES KHUSUS PENGUNJUNG
// ==========================================
Route::middleware(['auth', 'role:pengunjung', 'no-store'])->group(function () {
    Route::get('/dashboard', [PengunjungController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/feed', [PengunjungController::class, 'feed'])->name('dashboard.feed');
    Route::get('/tiket', [PengunjungController::class, 'tiket'])->name('tiket');
    Route::get('/reservasi', [PengunjungController::class, 'reservasi'])->name('reservasi');
    Route::get('/reservasi/availability', [PengunjungController::class, 'availability'])->name('reservasi.availability');
    Route::get('/transaksi', [PengunjungController::class, 'transaksi'])->name('transaksi');

    // Simpan pesanan dari form reservasi ke session + database, lalu arahkan ke checkout
    Route::post('/reservasi/checkout', [PengunjungController::class, 'checkout'])->name('reservasi.checkout');

    // Pilih metode pembayaran (BCA / Mandiri / online) untuk pesanan aktif
    Route::post('/transaksi/metode', [PengunjungController::class, 'metode'])->name('transaksi.metode');

    // Tandai bukti transfer terkirim lalu arahkan ke halaman Tiket Saya
    Route::post('/transaksi/kirim', [PengunjungController::class, 'kirim'])->name('transaksi.kirim');

    // Pembayaran online Midtrans (Snap): buat token & pantau status
    Route::post('/transaksi/midtrans/pay', [MidtransController::class, 'pay'])->name('transaksi.midtrans.pay');
    // QRIS via Core API: QR ditampilkan langsung di card pembayaran
    Route::post('/transaksi/midtrans/qris', [MidtransController::class, 'qris'])->name('transaksi.midtrans.qris');
    // BCA VA / Mandiri Bill / QRIS via Core API
    Route::post('/transaksi/midtrans/charge', [MidtransController::class, 'charge'])->name('transaksi.midtrans.charge');
    Route::get('/transaksi/midtrans/status', [MidtransController::class, 'status'])->name('transaksi.midtrans.status');

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
            ImageOptimizer::optimize(Storage::disk('public')->path($user->foto));
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

// ==========================================
// 6. NOTIFIKASI MIDTRANS (server-to-server, tanpa login; diverifikasi signature)
// ==========================================
Route::post('/midtrans/callback', [MidtransController::class, 'callback'])->name('midtrans.callback');
