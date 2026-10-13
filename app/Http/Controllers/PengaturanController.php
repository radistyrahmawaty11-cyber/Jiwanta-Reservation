<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    public function index()
    {
        $banks = Bank::orderByDesc('status')->orderBy('nama_bank')->get();

        return view('admin.pengaturan', compact('banks'));
    }

    public function storeBank(Request $request)
    {
        $d = $this->validated($request);
        $d['status'] = (bool) ($d['status'] ?? false);

        if ($d['status']) {
            Bank::query()->update(['status' => false]);
        }

        $bank = Bank::create($d);

        return response()->json(['ok' => true, 'data' => $bank]);
    }

    public function updateBank(Request $request, Bank $bank)
    {
        $d = $this->validated($request);
        $d['status'] = (bool) ($d['status'] ?? false);

        if ($d['status']) {
            Bank::whereKeyNot($bank->id)->update(['status' => false]);
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
            'nama_bank' => ['required', 'string', 'max:100'],
            'nomor_rekening' => ['required', 'string', 'max:50'],
            'atas_nama' => ['required', 'string', 'max:100'],
            'status' => ['nullable', 'boolean'],
        ]);
    }
}
