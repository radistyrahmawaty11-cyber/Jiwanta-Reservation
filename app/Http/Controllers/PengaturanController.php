<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function index()
    {
        $banks = Bank::orderByDesc('is_utama')->orderBy('kode')->get();

        return view('admin.pengaturan', compact('banks'));
    }

    public function storeBank(Request $request)
    {
        $d = $this->validated($request);
        $d['is_utama'] = (bool) ($d['is_utama'] ?? false);

        if ($d['is_utama']) {
            Bank::query()->update(['is_utama' => false]);
        }

        $bank = Bank::create($d);

        return response()->json(['ok' => true, 'data' => $bank]);
    }

    public function updateBank(Request $request, Bank $bank)
    {
        $d = $this->validated($request);
        $d['is_utama'] = (bool) ($d['is_utama'] ?? false);

        if ($d['is_utama']) {
            Bank::whereKeyNot($bank->id)->update(['is_utama' => false]);
        }

        $bank->update($d);

        return response()->json(['ok' => true, 'data' => $bank]);
    }

    public function destroyBank(Bank $bank)
    {
        $bank->delete();

        return response()->json(['ok' => true]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'kode' => ['required', 'in:bca,mandiri'],
            'nama' => ['required', 'string', 'max:100'],
            'nomor_rekening' => ['required', 'string', 'max:50'],
            'atas_nama' => ['required', 'string', 'max:100'],
            'cabang' => ['nullable', 'string', 'max:100'],
            'is_utama' => ['nullable', 'boolean'],
        ]);
    }
}
