<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PenggunaController extends Controller
{
    public function index()
    {
        $penggunas = Pengguna::orderByRaw("FIELD(role, 'admin', 'petugas', 'pengunjung')")->orderBy('id')->get();

        return view('admin.manajemen-pengguna', compact('penggunas'));
    }

    public function store(Request $request)
    {
        $d = $request->validate([
            'nama_pengguna' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:50', 'unique:penggunas,email'],
            'nohp' => ['required', 'string', 'max:15'],
            'role' => ['required', 'in:admin,petugas,pengunjung'],
            'password' => ['required', 'string', 'min:6', 'max:100'],
        ]);

        $pengguna = Pengguna::create($d);

        return response()->json(['ok' => true, 'data' => $pengguna]);
    }

    public function update(Request $request, Pengguna $pengguna)
    {
        $d = $request->validate([
            'nama_pengguna' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:50', Rule::unique('penggunas', 'email')->ignore($pengguna->id)],
            'nohp' => ['required', 'string', 'max:15'],
            'role' => ['required', 'in:admin,petugas,pengunjung'],
            'password' => ['nullable', 'string', 'min:6', 'max:100'],
        ]);

        if (empty($d['password'])) {
            unset($d['password']);
        }

        $pengguna->update($d);

        return response()->json(['ok' => true, 'data' => $pengguna]);
    }

    public function destroy(Pengguna $pengguna)
    {
        $pengguna->delete();

        return response()->json(['ok' => true]);
    }
}
