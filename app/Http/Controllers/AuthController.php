<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // Tampilkan halaman login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Tampilkan halaman register
    public function showRegister()
    {
        return view('auth.register');
    }

    // Proses login (Disesuaikan dengan input 'whatsapp' dari frontend)
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'whatsapp' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Cek apakah input berupa email atau nomor HP
        $field = filter_var($request->whatsapp, FILTER_VALIDATE_EMAIL) ? 'email' : 'nohp';

        $credentials = [
            $field => $request->whatsapp,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirect berdasarkan role
            if ($user->role === 'admin') {
                return redirect()->intended('/admin/dashboard');
            } elseif ($user->role === 'petugas') {
                return redirect()->intended('/petugas/dashboard');
            } else {
                return redirect()->intended('/');
            }
        }

        // Jika gagal, kembalikan dengan pesan error yang sesuai label frontend
        return back()->withErrors([
            'whatsapp' => 'No. WhatsApp / Email atau kata sandi salah.',
        ])->withInput();
    }

    // Proses register
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_pengguna' => ['required', 'string', 'max:100', 'regex:/^[\pL\pM\s\.\'-]+$/u'],
            'email' => 'required|email|max:50|unique:penggunas,email',
            'nohp' => ['required', 'regex:/^[0-9+\-\s()]+$/'],
            'password' => 'required|string|min:6|confirmed',
        ], [
            'nama_pengguna.required' => 'Nama lengkap wajib diisi.',
            'nama_pengguna.regex' => 'Nama hanya boleh berisi huruf, spasi, titik, apostrof, dan tanda hubung.',
            'email.unique' => 'Email tersebut sudah terdaftar.',
            'nohp.required' => 'Nomor WhatsApp wajib diisi.',
            'nohp.regex' => 'Nomor WhatsApp hanya boleh berisi angka dan karakter yang valid.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $pengguna = Pengguna::create([
            'nama_pengguna' => strip_tags(trim((string) $request->nama_pengguna)),
            'email' => trim((string) $request->email),
            'password' => $request->password,
            'nohp' => trim((string) $request->nohp),
            'role' => 'pengunjung', // Default role
        ]);

        Auth::login($pengguna, true);
        $request->session()->regenerate();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Registrasi berhasil! Selamat datang di Jiwanta.');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $response = redirect('/login');

        // Buang cache browser agar halaman dashboard lama tidak bisa muncul
        // lagi lewat tombol "kembali" atau refresh setelah logout.
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        $response->headers->set('Clear-Site-Data', '"cache"');

        return $response;
    }

    // Tampilkan halaman lupa sandi
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    // Buat token reset & tampilkan tautannya (mode demo)
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'identifier' => ['required', 'string'],
        ]);

        $user = $this->findByWhatsappOrEmail($request->input('identifier'));

        if (! $user) {
            return back()->withErrors([
                'identifier' => 'Akun dengan email / nomor WhatsApp tersebut tidak ditemukan.',
            ])->withInput();
        }

        $token = Password::broker()->createToken($user);

        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]);

        return back()
            ->with('status', 'Tautan reset kata sandi berhasil dibuat. Silakan atur ulang kata sandi Anda.')
            ->with('reset_url', $resetUrl);
    }

    // Tampilkan formulir reset kata sandi
    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    // Simpan kata sandi baru
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (Pengguna $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')
                ->with('status', 'Kata sandi berhasil diperbarui. Silakan masuk kembali.');
        }

        $messages = [
            Password::INVALID_TOKEN => 'Tautan reset tidak valid atau sudah kedaluwarsa.',
            Password::INVALID_USER => 'Email tersebut tidak terdaftar.',
            Password::RESET_THROTTLED => 'Terlalu banyak permintaan. Silakan coba lagi nanti.',
        ];

        return back()->withErrors([
            'email' => $messages[$status] ?? 'Gagal mengatur ulang kata sandi.',
        ])->withInput();
    }

    // Kirim kode OTP WhatsApp (mode demo: kode dikembalikan ke frontend)
    public function sendOtp(Request $request)
    {
        $data = $request->validate([
            'whatsapp' => ['required', 'string'],
        ]);

        $user = $this->findByWhatsappOrEmail($data['whatsapp']);

        if (! $user) {
            return response()->json([
                'message' => 'Nomor WhatsApp / email belum terdaftar.',
            ], 404);
        }

        $code = (string) random_int(100000, 999999);

        $request->session()->put('otp', [
            'id' => $user->id,
            'code' => $code,
            'expires_at' => now()->addMinutes(5)->toDateTimeString(),
        ]);

        return response()->json([
            'target' => $this->maskIdentifier($user),
            'dev_code' => $code,
            'message' => 'Kode OTP berhasil dikirim (mode demo).',
            'expires_in' => 300,
        ]);
    }

    // Verifikasi kode OTP & login
    public function verifyOtp(Request $request)
    {
        $data = $request->validate([
            'code' => ['required', 'string'],
        ]);

        $otp = $request->session()->get('otp');

        if (! is_array($otp) || ! isset($otp['id'], $otp['code'], $otp['expires_at'])) {
            return response()->json([
                'ok' => false,
                'message' => 'Sesi OTP tidak ditemukan. Silakan kirim ulang kode.',
            ], 422);
        }

        if (now()->greaterThan(Carbon::parse($otp['expires_at']))) {
            $request->session()->forget('otp');

            return response()->json([
                'ok' => false,
                'message' => 'Kode OTP sudah kedaluwarsa. Silakan kirim ulang kode.',
            ], 422);
        }

        if (trim($data['code']) !== (string) $otp['code']) {
            return response()->json([
                'ok' => false,
                'message' => 'Kode OTP salah.',
            ], 422);
        }

        $user = Pengguna::find($otp['id']);

        if (! $user) {
            $request->session()->forget('otp');

            return response()->json([
                'ok' => false,
                'message' => 'Akun tidak ditemukan.',
            ], 422);
        }

        $request->session()->forget('otp');
        Auth::login($user, true);
        $request->session()->regenerate();

        return response()->json([
            'ok' => true,
            'url' => $this->homeUrlFor($user),
        ]);
    }

    // Login cepat dengan Google: arahkan ke halaman pilih akun Google.
    public function googleRedirect(Request $request)
    {
        $clientId = config('services.google.client_id');

        if (! $clientId || ! config('services.google.client_secret')) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'whatsapp' => 'Login dengan Google sedang dalam tahap pengembangan. '
                        .'Fitur ini belum tersedia saat ini. Silakan login menggunakan email/nomor HP atau kode OTP.',
                ]);
        }

        $state = Str::random(40);
        $redirectUri = $this->googleRedirectUri($request);

        $request->session()->put('google_oauth_state', $state);
        $request->session()->put('google_redirect_uri', $redirectUri);
        $request->session()->put('google_intended', $request->query('intended', '/dashboard'));

        $params = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid email profile',
            'state' => $state,
            'prompt' => 'select_account',
            'access_type' => 'online',
        ];

        return redirect()->away('https://accounts.google.com/o/oauth2/v2/auth?'.http_build_query($params));
    }

    // Callback Google: tukar kode dengan token, lalu masuk memakai akun asli.
    public function googleCallback(Request $request)
    {
        $expected = $request->session()->pull('google_oauth_state');
        $redirectUri = $request->session()->pull('google_redirect_uri') ?: $this->googleRedirectUri($request);

        if (! $expected || ! hash_equals($expected, (string) $request->query('state'))) {
            return redirect()
                ->route('login')
                ->withErrors(['whatsapp' => 'Sesi login Google tidak valid atau kedaluwarsa. Silakan coba lagi.']);
        }

        if ($request->filled('error')) {
            return redirect()
                ->route('login')
                ->withErrors(['whatsapp' => 'Login Google dibatalkan.']);
        }

        try {
            $token = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'code' => (string) $request->query('code'),
                'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'redirect_uri' => $redirectUri,
                'grant_type' => 'authorization_code',
            ])->throw()->json();

            $profile = Http::withToken((string) ($token['access_token'] ?? ''))
                ->get('https://www.googleapis.com/oauth2/v3/userinfo')
                ->throw()->json();
        } catch (\Throwable $e) {
            return redirect()
                ->route('login')
                ->withErrors(['whatsapp' => 'Gagal terhubung dengan Google: '.$e->getMessage()]);
        }

        $email = trim((string) ($profile['email'] ?? ''));
        $verified = (bool) ($profile['email_verified'] ?? false);

        if ($email === '' || ! $verified) {
            return redirect()
                ->route('login')
                ->withErrors(['whatsapp' => 'Email Google belum terverifikasi, login dibatalkan.']);
        }

        $nama = strip_tags(trim((string) ($profile['name'] ?? ''))) ?: Str::before($email, '@');
        $foto = trim((string) ($profile['picture'] ?? ''));

        $user = Pengguna::where('email', $email)->first();

        if ($user) {
            $user->forceFill(array_filter([
                'nama_pengguna' => $nama !== '' ? $nama : null,
                'foto' => $foto !== '' ? $foto : null,
            ]))->save();
        } else {
            $user = Pengguna::create([
                'nama_pengguna' => $nama,
                'email' => $email,
                'nohp' => $this->generateUniqueNohp(),
                'foto' => $foto !== '' ? $foto : null,
                'role' => 'pengunjung',
                'password' => Str::random(32),
            ]);
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        $intended = $request->session()->pull('google_intended');

        return redirect()
            ->intended($intended ?: $this->homeUrlFor($user))
            ->with('success', 'Berhasil masuk dengan akun Google '.$email.'.');
    }

    /**
     * Redirect URI OAuth. Kalau GOOGLE_REDIRECT_URI kosong di .env, otomatis
     * mengikuti alamat yang sedang dipakai (localhost / domain / tunnel).
     */
    private function googleRedirectUri(?Request $request = null): string
    {
        if ($configured = config('services.google.redirect_uri')) {
            return $configured;
        }

        if ($request) {
            return rtrim($request->getSchemeAndHttpHost(), '/').'/auth/google/callback';
        }

        return rtrim((string) config('app.url'), '/').'/auth/google/callback';
    }

    private function generateUniqueNohp(): string
    {
        do {
            $nohp = '08'.random_int(100000000, 999999999);
        } while (Pengguna::where('nohp', $nohp)->exists());

        return $nohp;
    }

    // Cari pengguna berdasarkan email atau nomor HP
    private function findByWhatsappOrEmail(?string $identifier): ?Pengguna
    {
        $identifier = trim((string) $identifier);

        if ($identifier === '') {
            return null;
        }

        $field = filter_var($identifier, FILTER_VALIDATE_EMAIL) ? 'email' : 'nohp';

        return Pengguna::where($field, $identifier)->first();
    }

    // Tujuan redirect setelah login sesuai role
    private function homeUrlFor(Pengguna $user): string
    {
        return match ($user->role) {
            'admin' => route('admin.dashboard'),
            'petugas' => route('petugas.dashboard'),
            default => route('home'),
        };
    }

    // Samarkan nomor HP / email untuk ditampilkan di panel OTP
    private function maskIdentifier(Pengguna $user): string
    {
        $nohp = (string) $user->nohp;

        if (strlen($nohp) >= 6) {
            $masked = substr($nohp, 0, 4).str_repeat('*', strlen($nohp) - 6).substr($nohp, -2);

            return $masked;
        }

        return $nohp !== '' ? $nohp : $user->email;
    }
}
