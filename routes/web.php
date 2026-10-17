<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CabinController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\PengunjungController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\TiketController;
use App\Http\Controllers\TransaksiController;
use App\Models\Bank;
use App\Models\Cabin;
use App\Models\Detailtransaksi;
use App\Models\Pengguna;
use App\Models\Reservasi;
use App\Models\Tiket;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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

        // Kuota tiket (data asli dari tabel tikets + detail_tikets)
        $tiketKuota = Tiket::withCount(['detail_tikets as terjual' => function ($q) {
            $q->select(DB::raw('COALESCE(SUM(jumlah), 0)'));
        }])->get()->map(fn ($t) => [
            'nama' => $t->nama_tiket,
            'kapasitas' => (int) $t->stok,
            'terjual' => (int) $t->terjual,
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
            'growthPct', 'queue', 'reservations', 'cabins', 'staff', 'totalTransaksi', 'todayLabel'
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

    // ==========================================
    // ROUTES MANAJEMEN PENGGUNA (data asli dari DB)
    // ==========================================
    Route::get('/admin/manajemen-pengguna', [PenggunaController::class, 'index'])->name('manajemen-pengguna');
    Route::post('/admin/pengguna', [PenggunaController::class, 'store'])->name('admin.pengguna.store');
    Route::put('/admin/pengguna/{pengguna}', [PenggunaController::class, 'update'])->name('admin.pengguna.update');
    Route::delete('/admin/pengguna/{pengguna}', [PenggunaController::class, 'destroy'])->name('admin.pengguna.destroy');
    Route::get('/admin/laporan', function () {
        $namaHari = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
        ];
        $namaBulan = ['Jan' => 'Jan', 'Feb' => 'Feb', 'Mar' => 'Mar', 'Apr' => 'Apr', 'May' => 'Mei', 'Jun' => 'Jun', 'Jul' => 'Jul', 'Aug' => 'Agu', 'Sep' => 'Sep', 'Oct' => 'Okt', 'Nov' => 'Nov', 'Dec' => 'Des'];

        $totalCabins = Cabin::count();
        $end = now()->startOfDay();

        $hariNama = fn ($d) => $namaHari[$d->format('l')].', '.$d->format('d').' '.$namaBulan[$d->format('M')].' '.$d->format('Y');
        $hariSingkat = fn ($d) => substr($namaHari[$d->format('l')], 0, 3).' ('.$d->format('d').')';

        $days = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = $end->copy()->subDays($i);
            $dateStr = $date->toDateString();

            $agg = Detailtransaksi::query()
                ->join('transaksis', 'transaksis.id', '=', 'detailtransaksis.transaksi_id')
                ->leftJoin('tikets', 'tikets.id', '=', 'detailtransaksis.tiket_id')
                ->leftJoin('reservasis', 'reservasis.id', '=', 'detailtransaksis.reservasi_id')
                ->whereDate('transaksis.tanggal_transaksi', $dateStr)
                ->where('transaksis.status', 'lunas')
                ->selectRaw('COALESCE(SUM(CASE WHEN tikets.id IS NOT NULL THEN detailtransaksis.jumlah ELSE 0 END), 0) as tiket_qty')
                ->selectRaw("COALESCE(SUM(CASE WHEN tikets.nama_tiket LIKE '%Premier%' THEN detailtransaksis.jumlah ELSE 0 END), 0) as prem")
                ->selectRaw('COALESCE(SUM(CASE WHEN detailtransaksis.tiket_id IS NOT NULL THEN detailtransaksis.subtotal ELSE 0 END), 0) as tk')
                ->selectRaw('COALESCE(SUM(CASE WHEN reservasis.cabin_id IS NOT NULL THEN detailtransaksis.subtotal ELSE 0 END), 0) as cb')
                ->first();

            $prem = (int) $agg->prem;
            $cls = max(0, (int) $agg->tiket_qty - $prem);

            $cab = Reservasi::where('status', 'confirmed')
                ->whereNotNull('cabin_id')
                ->whereDate('tanggal_checkin', '<=', $dateStr)
                ->whereDate('tanggal_checkout', '>=', $dateStr)
                ->distinct()->count('cabin_id');

            $days[] = [
                'd' => $hariNama($date),
                's' => $hariSingkat($date),
                'cls' => $cls,
                'prem' => $prem,
                'cab' => $cab,
                'tk' => (int) $agg->tk,
                'cb' => (int) $agg->cb,
                'peak' => false,
                'audit' => 'ok',
            ];
        }

        // Tandai hari puncak (omzet bersih tertinggi)
        $peakIdx = null;
        $peakNet = 0;
        foreach ($days as $idx => $d) {
            $n = $d['tk'] + $d['cb'];
            if ($n > $peakNet) {
                $peakNet = $n;
                $peakIdx = $idx;
            }
        }
        if ($peakIdx !== null && $peakNet > 0) {
            $days[$peakIdx]['peak'] = true;
        }
        $peakDay = $peakIdx !== null && $peakNet > 0 ? $days[$peakIdx] : null;
        $peakOmzet = $peakDay ? $peakDay['tk'] + $peakDay['cb'] : 0;
        $peakTiket = $peakDay ? $peakDay['cls'] + $peakDay['prem'] : 0;

        $sum = fn (string $k) => array_sum(array_column($days, $k));
        $cabNights = $sum('cab');
        $capacity = max(1, $totalCabins * 6);
        $occupancy = round($cabNights / $capacity * 100, 1);
        $revpar = (int) round($sum('cb') / $capacity);

        $base = [
            'omzet' => $sum('tk') + $sum('cb'),
            'pax' => $sum('cls') + $sum('prem'),
            'cls' => $sum('cls'),
            'prem' => $sum('prem'),
        ];

        // Sparkline: omzet bersih 12 hari terakhir (dalam juta rupiah)
        $spark = [];
        for ($i = 11; $i >= 0; $i--) {
            $dateStr = $end->copy()->subDays($i)->toDateString();
            $val = Transaksi::where('status', 'lunas')->whereDate('tanggal_transaksi', $dateStr)->sum('total_bayar');
            $spark[] = round($val / 1e6, 2);
        }

        $periodStart = $end->copy()->subDays(5);
        $periodLabel = $periodStart->format('d M Y').' – '.$end->format('d M Y');
        $monthLabel = $namaBulan[$end->format('M')].' '.$end->format('Y');
        $totalCabinsLabel = $totalCabins.' Unit';

        return view('admin.laporan', compact(
            'days', 'base', 'spark', 'occupancy', 'revpar', 'cabNights',
            'totalCabins', 'totalCabinsLabel', 'periodLabel', 'monthLabel',
            'peakDay', 'peakOmzet', 'peakTiket'
        ));
    })->name('laporan');
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
    Route::get('/reservasi/availability', [PengunjungController::class, 'availability'])->name('reservasi.availability');
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
