<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cabin;

class CabinController extends Controller
{
    public function index()
{
    // 1. Ambil semua data dari database
    $cabins = Cabin::all();

    // 2. Kirim ke view 'cabin' (file cabin.blade.php)
    return view('admin.cabin', compact('cabins'));
}


    public function create() { }
    public function store(Request $request) { }
    public function show($id) { }
    public function edit($id) { }
    public function update(Request $request, $id) { }
    public function destroy($id) { }
}
