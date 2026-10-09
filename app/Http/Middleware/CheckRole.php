<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        // 1. Cek apakah user sudah login?
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 2. Cek apakah role user sesuai dengan yang diizinkan?
        // (Auth::user() akan mengambil data pengguna yang sedang login dari tabel 'penggunas')
        if (Auth::user()->role !== $role) {
            // Jika tidak sesuai, lempar error 403 (Forbidden) atau redirect ke dashboard mereka sendiri
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // 3. Jika lolos semua, izinkan masuk
        return $next($request);
    }
}
