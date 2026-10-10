<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private function home(Pengguna $pengguna): string
    {
        return match ($pengguna->role) {
            'admin' => route('admin.dashboard'),
            'petugas' => route('petugas.dashboard'),
            default => route('dashboard'),
        };
    }

    private function normalizePhone(string $value): string
    {
        $digits = preg_replace('/\D+/', '', $value);

        if (str_starts_with($digits, '62')) {
            $digits = '0'.substr($digits, 2);
        }

        return $digits;
    }

    private function findUser(string $identifier): ?Pengguna
    {
        $identifier = trim($identifier);

        if (str_contains($identifier, '@')) {
            return Pengguna::where('email', $identifier)->first();
        }

        $phone = $this->normalizePhone($identifier);

        return Pengguna::where('nohp', $phone)->first()
            ?? Pengguna::where('email', $identifier)->first();
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'whatsapp' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = $this->findUser($data['whatsapp']);

        if (! $user || ! Auth::validate(['email' => $user->email, 'password' => $data['password']])) {
            return back()
                ->withInput($request->only('whatsapp'))
                ->withErrors(['whatsapp' => 'Email / nomor WhatsApp atau kata sandi salah.']);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended($this->home($user));
    }

    public function sendOtp(Request $request): JsonResponse
    {
        $data = $request->validate([
            'whatsapp' => ['required', 'string'],
        ]);

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $request->session()->put('auth.otp', [
            'code' => $code,
            'target' => $data['whatsapp'],
            'expires_at' => now()->addMinutes(5)->timestamp,
        ]);

        Log::info("Kode OTP WhatsApp demo untuk {$data['whatsapp']}: {$code}");

        return response()->json([
            'ok' => true,
            'target' => $data['whatsapp'],
            'dev_code' => $code,
        ]);
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string'],
        ]);

        $otp = $request->session()->get('auth.otp');

        if (! $otp || ($otp['expires_at'] ?? 0) < now()->timestamp || $otp['code'] !== $data['code']) {
            return response()->json([
                'ok' => false,
                'message' => 'Kode OTP salah atau sudah kedaluwarsa. Kirim ulang kode.',
            ], 422);
        }

        $user = $this->findUser($otp['target']);

        if (! $user) {
            $phone = $this->normalizePhone($otp['target']);
            $user = Pengguna::create([
                'nama_pengguna' => 'Pengunjung WhatsApp',
                'email' => 'wa'.($phone ?: Str::random(8)).'@jiwanta.local',
                'nohp' => $phone ?: '0000000000',
                'role' => 'pengunjung',
                'password' => Str::random(16),
            ]);
        }

        Auth::login($user);
        $request->session()->forget('auth.otp');
        $request->session()->regenerate();

        return response()->json(['ok' => true, 'url' => $this->home($user)]);
    }

    public function google(Request $request)
    {
        $user = Pengguna::firstOrCreate(
            ['email' => 'google@jiwanta.local'],
            [
                'nama_pengguna' => 'Pengguna Google',
                'nohp' => '080000000000',
                'role' => 'pengunjung',
                'password' => Str::random(16),
            ]
        );

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended($this->home($user));
    }

    public function showForgot()
    {
        return view('auth.forgot-password');
    }

    public function sendReset(Request $request)
    {
        $data = $request->validate([
            'identifier' => ['required', 'string'],
        ]);

        $user = $this->findUser($data['identifier']);

        if (! $user) {
            return back()
                ->withInput()
                ->withErrors(['identifier' => 'Akun tidak ditemukan. Periksa email / nomor WhatsApp Anda.']);
        }

        $token = Password::broker()->createToken($user);
        $url = route('password.reset', ['token' => $token, 'email' => $user->email]);

        Log::info("Tautan reset kata sandi Jiwanta untuk {$user->email}: {$url}");

        return back()->with('status', 'Tautan reset kata sandi telah dibuat.')
            ->with('reset_url', $url)
            ->with('reset_email', $user->email);
    }

    public function showReset(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }

    public function resetPassword(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $status = Password::broker()->reset($data, function (Pengguna $user, string $password) {
            $user->forceFill(['password' => $password])->save();
            $user->setRememberToken(Str::random(60));
        });

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', 'Kata sandi berhasil diubah. Silakan masuk dengan kata sandi baru.');
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => __($status)]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
