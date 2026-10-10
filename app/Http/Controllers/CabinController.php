<?php

namespace App\Http\Controllers;

use App\Models\Cabin;
use Illuminate\Http\Request;

class CabinController extends Controller
{
    public function index()
    {
        $cabins = Cabin::orderBy('jenis_cabin')->orderBy('id')->get();

        return view('admin.cabin', compact('cabins'));
    }

    public function store(Request $request)
    {
        $d = $this->validated($request);

        $cabin = Cabin::create($d + ['status' => $d['status'] ?? 'tersedia']);

        return response()->json(['ok' => true, 'data' => $cabin]);
    }

    public function update(Request $request, Cabin $cabin)
    {
        $d = $this->validated($request);

        $cabin->update($d);

        return response()->json(['ok' => true, 'data' => $cabin]);
    }

    public function destroy(Cabin $cabin)
    {
        $cabin->delete();

        return response()->json(['ok' => true]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nama_cabin' => ['required', 'string', 'max:100'],
            'jenis_cabin' => ['required', 'in:suite,shorts'],
            'kapasitas' => ['required', 'integer', 'min:1', 'max:50'],
            'harga_per_malam' => ['required', 'numeric', 'min:0'],
            'status' => ['nullable', 'in:tersedia,dipesan'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
        ]);
    }
}
