<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
            'nama_pengguna' => 'required|string|max:100',
            'email' => 'required|email|max:50|unique:penggunas,email',
            'password' => 'required|string|min:6|confirmed',
            'nohp' => 'required|string|max:15',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $pengguna = Pengguna::create([
            'nama_pengguna' => $request->nama_pengguna,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nohp' => $request->nohp,
            'role' => 'pengunjung', // Default role
        ]);

        Auth::login($pengguna);

        return redirect('/')->with('success', 'Registrasi berhasil! Selamat datang di Jiwanta.');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
