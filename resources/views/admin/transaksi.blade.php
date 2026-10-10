@extends('layouts.admin')

@section('title', 'Transaksi - Jiwanta')
@section('page-class') bg-[#F9F8FF] font-['Plus_Jakarta_Sans',ui-sans-serif,system-ui,sans-serif] text-slate-900 antialiased @endsection

{{-- Font desain: Plus Jakarta Sans (aman jika layout belum punya @stack('styles'), hanya tidak terpakai) --}}
@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    @keyframes pop-in { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: none; } }
    .pop-in { animation: pop-in .35s ease-out both; }
    @media (prefers-reduced-motion: reduce) { .pop-in { animation: none; } }
</style>
@endpush

@section('page-content')

@php
    // ---------- Helper ----------
    $ic = fn (string $d, string $c = 'h-4 w-4', string $sw = '1.8') =>
        '<svg class="'.$c.'" fill="none" stroke="currentColor" stroke-width="'.$sw.'" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="'.$d.'"/></svg>';

    $p = [
        'dashboard' => 'M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z',
        'receipt'   => 'M6 3h12v18l-3-2-3 2-3-2-3 2V3zM9 8h6M9 12h6',
        'ticket'    => 'M4 7h16v3a2 2 0 000 4v3H4v-3a2 2 0 000-4V7zM14 7v10',
        'cabin'     => 'M3 11l9-8 9 8M5 10v10h14V10M10 20v-5h4v5',
        'pool'      => 'M3 19c1.5 0 1.5-1 3-1s1.5 1 3-1 1.5 1 3-1 1.5 1 3-1 1.5 1 3-1 1.5 1 3 1M8 15V6M14 15V6M8 8h6M8 12h6',
        'users'     => 'M17 20v-1a4 4 0 00-4-4H7a4 4 0 00-4 4v1M10 11a4 4 0 100-8 4 4 0 000 8zM21 20v-1a4 4 0 00-3-3.9M16 3.1a4 4 0 010 7.8',
        'chart'     => 'M5 20V10M12 20V4M19 20v-7',
        'gear'      => 'M12 15a3 3 0 100-6 3 3 0 000 6zM2 12h3M19 12h3M5 5l2 2M17 17l2 2M19 5l-2 2M7 17l-2 2',
        'bell'      => 'M6 8a6 6 0 0112 0c0 7 3 8 3 8H3s3-1 3-8M10 20a2 2 0 004 0',
        'download'  => 'M12 4v11m-4-4l4 4 4-4M5 20h14',
        'clock'     => 'M12 8v4l2 2M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'logout'    => 'M15 4h4a1 1 0 011 1v14a1 1 0 01-1 1h-4M10 16l-4-4 4-4M6 12h10',
        'snow'      => 'M12 3v18M4.5 7.5l15 9M4.5 16.5l15-9M9 4l3 2 3-2M9 20l3-2 3 2',
        'tree'      => 'M12 3l4 6h-2.5l3.5 5h-3l3 5H7l3-5H7l3.5-5H8l4-6z',
        'search'    => 'M21 21l-4.3-4.3M17 11a6 6 0 11-12 0 6 6 0 0112 0z',
        'sliders'   => 'M4 7h9M17 7h3M4 17h3M11 17h9M15 5v4M9 15v4',
        'chevron'   => 'M6 9l6 6 6-6',
        'chevl'     => 'M15 18l-6-6 6-6',
        'chevr'     => 'M9 6l6 6-6 6',
        'refresh'   => 'M4 4v6h6M20 20v-6h-6M20 9A8 8 0 006 6L4 10M4 15a8 8 0 0014 3l2-4',
        'check'     => 'M5 13l4 4L19 7',
        'checkc'    => 'M9 12l2 2 4-4M12 21a9 9 0 100-18 9 9 0 000 18z',
        'x'         => 'M6 6l12 12M18 6L6 18',
        'eye'       => 'M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12zM12 15a3 3 0 100-6 3 3 0 000 6z',
        'alert'     => 'M12 4l9 16H3L12 4zM12 10v4M12 17h.01',
        'history'   => 'M3 12a9 9 0 109-9 9 9 0 00-6.4 2.6L3 8M3 3v5h5M12 8v4l3 2',
<<<<<<< HEAD
        'plus'      => 'M12 5v14M5 12h14',
        'edit'      => 'M12 20h9M16.5 3.5a2.1 2.1 0 013 3L7 21l-4 1 1-4L16.5 3.5z',
        'trash'     => 'M4 7h16M9 7V5a1 1 0 011-1h4a1 1 0 011 1v2M6 7l1 13a1 1 0 001 1h8a1 1 0 001-1l1-13M10 11v6M14 11v6',
        'save'      => 'M5 4h11l3 3v13H5zM8 4v5h6V4M8 20v-6h8v6',
=======
>>>>>>> d0c70c8d31d52cecb4d10594c5c6910a3c2cf03b
    ];

    $mint  = 'bg-[#CDEFD5]';
    $peach = 'bg-[#FBD9B0]';
    $lav   = 'bg-[#DDE6FB]';
    $soft  = 'bg-[#EEF1FC]';
    $card  = 'rounded-2xl bg-white shadow-[0_4px_18px_rgba(15,69,39,0.08)]';

<<<<<<< HEAD
    $banks = [['all', 'Semua'], ['BCA', 'BCA'], ['Mandiri', 'Mandiri']];
    $statuses = [['all', 'Semua Status'], ['lunas', 'Lunas'], ['pending', 'Menunggu Verifikasi'], ['gagal', 'Gagal']];
@endphp

=======
    $banks = [['all', 'Semua'], ['BCA', 'BCA'], ['BNI', 'BNI'], ['Mandiri', 'Mandiri'], ['QRIS', 'QRIS']];
    $statuses = [['all', 'Semua Status'], ['lunas', 'Lunas'], ['pending', 'Menunggu Verifikasi'], ['gagal', 'Gagal']];

    // ---------- Data transaksi (ganti dengan data dari controller) ----------
    $rows = [
        ['t' => '10:02', 'id' => 'JW-20260920-098', 'name' => 'Dewi Maharani',      'unit' => '2x Premier Onsen Hot Spring',     'method' => 'BCA',     'amount' => 170000,   'status' => 'lunas'],
        ['t' => '09:54', 'id' => 'JW-20260920-097', 'name' => 'Bambang Hariyanto',  'unit' => 'Camellia Family Cabin (1 Malam)', 'method' => 'BCA',     'amount' => 1850320,  'status' => 'lunas'],
        ['t' => '09:48', 'id' => 'JW-20260920-096', 'name' => 'Priscilla Chandra',  'unit' => '4x Classic Ticket Onsen',        'method' => 'Mandiri', 'amount' => 180115,   'status' => 'lunas'],
        ['t' => '09:30', 'id' => 'JW-20260920-095', 'name' => 'Kurniawan Tejo',     'unit' => 'Private Spa Cabana Sesi Pagi',   'method' => 'BCA',     'amount' => 450401,   'status' => 'pending'],
        ['t' => '09:12', 'id' => 'JW-20260920-094', 'name' => 'Nadira Alatas',      'unit' => '1x Classic Ticket Hot Spring',   'method' => 'Mandiri', 'amount' => 45109,    'status' => 'lunas'],
        ['t' => '08:57', 'id' => 'JW-20260920-093', 'name' => 'Farhan Maulana',     'unit' => '2x Tiket Premier Onsen',         'method' => 'QRIS',    'amount' => 170000,   'status' => 'lunas'],
        ['t' => '08:41', 'id' => 'JW-20260920-092', 'name' => 'Larasati Dewi',      'unit' => 'Suite Cabin (2 Malam)',          'method' => 'BNI',     'amount' => 2080000,  'status' => 'pending'],
        ['t' => '08:26', 'id' => 'JW-20260920-091', 'name' => 'Reza Fahlevi',       'unit' => '3x Classic Ticket + Gazebo Pinus', 'method' => 'BCA',   'amount' => 225230,   'status' => 'lunas'],
        ['t' => '08:03', 'id' => 'JW-20260920-090', 'name' => 'Maya Sari',          'unit' => 'Shorts Cabin (3 Jam)',           'method' => 'QRIS',    'amount' => 340000,   'status' => 'gagal'],
        ['t' => '07:48', 'id' => 'JW-20260920-089', 'name' => 'Bagus Wicaksono',    'unit' => '2x Tiket Classic Hot Spring',    'method' => 'Mandiri', 'amount' => 120000,   'status' => 'lunas'],
    ];
@endphp


                {{-- Header Section --}}
>>>>>>> d0c70c8d31d52cecb4d10594c5c6910a3c2cf03b
                <div class="flex items-start justify-between gap-6">
                    <div class="max-w-xl">
                        <p class="mb-2 flex items-center gap-2">
                            <span class="rounded-md {{ $mint }} px-2.5 py-1 text-[9px] font-bold uppercase tracking-wide text-[#0B3A22]">Finance Ledger</span>
                            <span class="text-[10px] text-slate-600">Rekonsiliasi Otomatis Aktif</span>
                        </p>
                        <h1 class="text-[32px] font-bold leading-tight text-[#0B3A22]">Riwayat Transaksi</h1>
<<<<<<< HEAD
                        <p class="mt-2 text-[13px] leading-relaxed text-slate-600">Seluruh pemesanan yang tercatat beserta metode pembayaran, nominal, dan status penyelesaiannya dalam satu daftar.</p>
=======
                        <p class="mt-2 text-[13px] leading-relaxed text-slate-600">Seluruh pemesanan yang tercatat hari ini beserta metode pembayaran, nominal, dan status penyelesaiannya dalam satu daftar.</p>
>>>>>>> d0c70c8d31d52cecb4d10594c5c6910a3c2cf03b
                    </div>

                    <div class="grid w-[540px] shrink-0 grid-cols-3 gap-3">
                        <div class="{{ $card }} flex items-center gap-3 px-4 py-3.5">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $mint }} text-[#0B3A22]">{!! $ic($p['receipt'], 'h-5 w-5') !!}</span>
                            <div>
<<<<<<< HEAD
                                <p class="text-[22px] font-bold leading-tight text-slate-900" data-stat-today>{{ $stats['today'] }}</p>
=======
                                <p class="text-[22px] font-bold leading-tight text-slate-900">86</p>
>>>>>>> d0c70c8d31d52cecb4d10594c5c6910a3c2cf03b
                                <p class="text-[10px] leading-tight text-slate-600">Transaksi Hari Ini</p>
                            </div>
                        </div>
                        <div class="{{ $card }} flex items-center gap-3 px-4 py-3.5">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $lav }} text-[#2F3F86]">{!! $ic($p['chart'], 'h-5 w-5') !!}</span>
                            <div>
<<<<<<< HEAD
                                <p class="text-[18px] font-bold leading-tight text-slate-900" data-stat-omzet>Rp {{ number_format($stats['omzet'], 0, ',', '.') }}</p>
=======
                                <p class="text-[18px] font-bold leading-tight text-slate-900">Rp 12.483.975</p>
>>>>>>> d0c70c8d31d52cecb4d10594c5c6910a3c2cf03b
                                <p class="text-[10px] leading-tight text-slate-600">Omzet Hari Ini</p>
                            </div>
                        </div>
                        <div class="{{ $card }} flex items-center gap-3 px-4 py-3.5">
<<<<<<< HEAD
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $peach }} text-[#0B3A22]">{!! $ic($p['clock'], 'h-5 w-5') !!}</span>
                            <div>
                                <p class="text-[22px] font-bold leading-tight text-slate-900" data-stat-pending>{{ $stats['pending'] }}</p>
=======
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $peach }} text-[#8B5E34]">{!! $ic($p['clock'], 'h-5 w-5') !!}</span>
                            <div>
                                <p class="text-[22px] font-bold leading-tight text-slate-900">14</p>
>>>>>>> d0c70c8d31d52cecb4d10594c5c6910a3c2cf03b
                                <p class="text-[10px] leading-tight text-slate-600">Menunggu Verifikasi</p>
                            </div>
                        </div>
                    </div>
                </div>

<<<<<<< HEAD
                <div class="{{ $card }} mt-6 p-5">
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="relative min-w-[220px] flex-1">
                            <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">{!! $ic($p['search'], 'h-4 w-4') !!}</span>
                            <input id="search" type="text" placeholder="Cari kode transaksi, pengguna, atau unit..." class="w-full rounded-xl border border-slate-200 bg-[#F7F8FC] py-2.5 pl-10 pr-4 text-[12px] text-slate-800 outline-none transition focus:border-[#0B3A22] focus:bg-white">
                        </div>

                        <div class="flex items-center gap-1 rounded-xl {{ $soft }} p-1">
                            @foreach ($banks as [$val, $label])
                                <button type="button" data-bank="{{ $val }}" class="rounded-lg px-3.5 py-2 text-[11px] font-semibold text-slate-600 transition {{ $val === 'all' ? 'bg-white text-[#0B3A22] shadow-sm' : 'hover:text-[#0B3A22]' }}">{{ $label }}</button>
=======
                {{-- Filter Bar --}}
                <section class="{{ $card }} p-5">
                    <div class="flex flex-wrap items-center gap-3">
                        <label class="flex min-w-[280px] flex-1 items-center gap-3 rounded-xl {{ $soft }} px-4 py-3 text-slate-500 focus-within:ring-2 focus-within:ring-[#0B3A22]/30">
                            {!! $ic($p['search'], 'h-4 w-4 shrink-0') !!}
                            <input id="search" type="text" autocomplete="off" placeholder="Cari Kode Booking, Nama Tamu, atau Unit..." class="w-full bg-transparent text-[12px] text-slate-800 placeholder:text-slate-500 focus:outline-none">
                        </label>

                        <div class="flex items-center rounded-full {{ $soft }} p-1">
                            @foreach ($banks as [$key, $label])
                                <button type="button" data-bank="{{ $key }}"
                                        class="rounded-full px-4 py-1.5 text-[10px] font-semibold transition {{ $loop->first ? 'bg-[#0B3A22] text-white shadow-sm' : 'text-slate-700 hover:text-[#0B3A22]' }}">{{ $label }}</button>
>>>>>>> d0c70c8d31d52cecb4d10594c5c6910a3c2cf03b
                            @endforeach
                        </div>

                        <div class="relative">
<<<<<<< HEAD
                            <button type="button" id="status-btn" class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-[11px] font-semibold text-slate-700 transition hover:border-[#0B3A22]">
                                {!! $ic($p['sliders'], 'h-3.5 w-3.5') !!}<span id="status-label">Semua Status</span>{!! $ic($p['chevron'], 'h-3.5 w-3.5') !!}
                            </button>
                            <div id="status-menu" class="hidden absolute right-0 top-full z-40 mt-2 w-44 overflow-hidden rounded-xl border border-slate-100 bg-white py-1 shadow-lg">
                                @foreach ($statuses as [$val, $label])
                                    <button type="button" data-status="{{ $val }}" class="block w-full px-4 py-2.5 text-left text-[11px] font-semibold text-slate-600 transition hover:bg-[#F3F5FC] hover:text-[#0B3A22]">{{ $label }}</button>
=======
                            <button type="button" id="status-btn" class="flex items-center gap-2 rounded-xl {{ $soft }} px-4 py-2.5 text-[11px] font-medium text-slate-700 transition hover:bg-[#E3E8FA]">
                                {!! $ic($p['sliders'], 'h-4 w-4') !!} <span id="status-label">Status: Semua</span> {!! $ic($p['chevron'], 'h-3.5 w-3.5') !!}
                            </button>
                            <div id="status-menu" class="absolute right-0 top-full z-20 mt-2 hidden w-48 rounded-xl bg-white p-1.5 shadow-[0_10px_28px_rgba(15,69,39,0.16)]">
                                @foreach ($statuses as [$key, $label])
                                    <button type="button" data-status="{{ $key }}" class="block w-full rounded-lg px-3 py-2 text-left text-[11px] font-medium transition hover:bg-slate-50">{{ $label }}</button>
>>>>>>> d0c70c8d31d52cecb4d10594c5c6910a3c2cf03b
                                @endforeach
                            </div>
                        </div>

<<<<<<< HEAD
                        <button type="button" id="trx-add" class="flex items-center gap-2 rounded-xl bg-[#0B3A22] px-4 py-2.5 text-[11px] font-bold text-white transition hover:bg-[#0e4a2c] active:scale-95">
                            {!! $ic($p['plus'], 'h-4 w-4', '2.2') !!} Tambah Transaksi
                        </button>

                        <a href="{{ route('admin.transaksi.export') }}" class="flex items-center gap-2 rounded-xl {{ $lav }} px-4 py-2.5 text-[11px] font-bold text-slate-800 transition hover:bg-[#cfd9f7] active:scale-95">
                            {!! $ic($p['download'], 'h-4 w-4', '2.2') !!} Unduh XLS
                        </a>
                    </div>
                </div>

                <div class="{{ $card }} mt-6 overflow-hidden">
                    <div class="overflow-x-auto">
                        <div class="min-w-[900px]">
                            <div class="grid grid-cols-[90px_140px_minmax(0,1.4fr)_100px_130px_150px_160px] items-center gap-3 border-b border-slate-100 bg-[#F3F5FC] px-5 py-3 text-[9px] font-bold uppercase tracking-wide text-slate-500">
                                <span>Waktu</span>
                                <span>Kode Booking</span>
                                <span>Customer &amp; Unit</span>
                                <span>Metode</span>
                                <span>Nominal</span>
                                <span>Status</span>
                                <span class="text-right">Opsi</span>
                            </div>
                            <div id="trx-list"></div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between gap-4 border-t border-slate-100 px-5 py-4">
                        <p class="text-[10px] text-slate-600">Menampilkan <span data-shown class="font-bold text-slate-800">0</span> dari <span data-total class="font-bold text-slate-800">0</span> transaksi</p>
                        <div id="pager" class="flex items-center gap-1.5 text-[11px] font-semibold"></div>
                    </div>
                </div>
@endsection

@section('overlays')
    {{-- Toast --}}
    <div id="toast" role="status" class="pointer-events-none fixed right-6 top-20 z-[60] max-w-sm translate-y-[-8px] rounded-xl bg-[#0B3A22] px-4 py-3 text-[12px] font-semibold text-white opacity-0 shadow-[0_10px_24px_-8px_rgba(11,46,34,0.55)] transition duration-300"></div>

    {{-- Modal form (tambah/edit) --}}
    <div id="trx-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/40 p-4 backdrop-blur-sm">
        <div class="w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <h3 id="tf-title" class="text-[15px] font-bold text-[#0B3A22]">Tambah Transaksi</h3>
                <button type="button" data-close="trx-modal" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">{!! $ic($p['x'], 'h-4 w-4') !!}</button>
            </div>
            <form id="tf-form" class="max-h-[70vh] overflow-y-auto px-6 py-5">
                <input type="hidden" id="tf-id">
                <div class="grid grid-cols-2 gap-x-4 gap-y-4">
                    <label class="block">
                        <span class="mb-1.5 block text-[10px] font-bold uppercase tracking-wide text-slate-500">Kode Transaksi</span>
                        <input id="tf-kode" type="text" required class="w-full rounded-xl border border-slate-200 bg-[#F7F8FC] px-3.5 py-2.5 text-[12px] text-slate-800 outline-none transition focus:border-[#0B3A22] focus:bg-white">
                    </label>
                    <label class="block">
                        <span class="mb-1.5 block text-[10px] font-bold uppercase tracking-wide text-slate-500">Pengguna</span>
                        <select id="tf-user" required class="w-full rounded-xl border border-slate-200 bg-[#F7F8FC] px-3.5 py-2.5 text-[12px] text-slate-800 outline-none transition focus:border-[#0B3A22] focus:bg-white">
                            @foreach ($penggunas as $u)
                                <option value="{{ $u->id }}">{{ $u->nama_pengguna }} — {{ $u->email }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="block">
                        <span class="mb-1.5 block text-[10px] font-bold uppercase tracking-wide text-slate-500">Tanggal &amp; Waktu</span>
                        <input id="tf-when" type="datetime-local" required class="w-full rounded-xl border border-slate-200 bg-[#F7F8FC] px-3.5 py-2.5 text-[12px] text-slate-800 outline-none transition focus:border-[#0B3A22] focus:bg-white">
                    </label>
                    <label class="block">
                        <span class="mb-1.5 block text-[10px] font-bold uppercase tracking-wide text-slate-500">Metode Pembayaran</span>
                        <select id="tf-metode" required class="w-full rounded-xl border border-slate-200 bg-[#F7F8FC] px-3.5 py-2.5 text-[12px] text-slate-800 outline-none transition focus:border-[#0B3A22] focus:bg-white">
                            <option value="bca">BCA</option>
                            <option value="mandiri">Mandiri</option>
                        </select>
                    </label>
                    <label class="block">
                        <span class="mb-1.5 block text-[10px] font-bold uppercase tracking-wide text-slate-500">Jenis Transaksi</span>
                        <select id="tf-jenis" required class="w-full rounded-xl border border-slate-200 bg-[#F7F8FC] px-3.5 py-2.5 text-[12px] text-slate-800 outline-none transition focus:border-[#0B3A22] focus:bg-white">
                            <option value="pembayaran_renang">Pembayaran Renang</option>
                            <option value="pembayaran_cabin">Pembayaran Cabin</option>
                        </select>
                    </label>
                    <label class="block">
                        <span class="mb-1.5 block text-[10px] font-bold uppercase tracking-wide text-slate-500">Status</span>
                        <select id="tf-status" required class="w-full rounded-xl border border-slate-200 bg-[#F7F8FC] px-3.5 py-2.5 text-[12px] text-slate-800 outline-none transition focus:border-[#0B3A22] focus:bg-white">
                            <option value="pending">Menunggu Verifikasi</option>
                            <option value="lunas">Lunas</option>
                            <option value="gagal">Gagal</option>
                        </select>
                    </label>
                    <label class="block">
                        <span class="mb-1.5 block text-[10px] font-bold uppercase tracking-wide text-slate-500">Total Bayar (Rp)</span>
                        <input id="tf-total" type="number" min="0" step="1" required class="w-full rounded-xl border border-slate-200 bg-[#F7F8FC] px-3.5 py-2.5 text-[12px] text-slate-800 outline-none transition focus:border-[#0B3A22] focus:bg-white">
                    </label>
                    <label class="block">
                        <span class="mb-1.5 block text-[10px] font-bold uppercase tracking-wide text-slate-500">Jumlah Bayar (Rp)</span>
                        <input id="tf-pay" type="number" min="0" step="1" required class="w-full rounded-xl border border-slate-200 bg-[#F7F8FC] px-3.5 py-2.5 text-[12px] text-slate-800 outline-none transition focus:border-[#0B3A22] focus:bg-white">
                    </label>
                    <label class="block">
                        <span class="mb-1.5 block text-[10px] font-bold uppercase tracking-wide text-slate-500">Kembalian (Rp)</span>
                        <input id="tf-change" type="number" min="0" step="1" readonly class="w-full cursor-not-allowed rounded-xl border border-slate-200 bg-slate-100 px-3.5 py-2.5 text-[12px] text-slate-600 outline-none">
                    </label>
                    <label class="block">
                        <span class="mb-1.5 block text-[10px] font-bold uppercase tracking-wide text-slate-500">Nama Bukti (opsional)</span>
                        <input id="tf-bukti" type="text" placeholder="struk_bca_xxx.jpg" class="w-full rounded-xl border border-slate-200 bg-[#F7F8FC] px-3.5 py-2.5 text-[12px] text-slate-800 outline-none transition focus:border-[#0B3A22] focus:bg-white">
                    </label>
                </div>
                <p id="tf-error" class="mt-4 hidden rounded-lg bg-[#FBD5D5] px-3 py-2 text-[11px] font-semibold text-[#B42318]"></p>
                <div class="mt-5 flex justify-end gap-2">
                    <button type="button" data-close="trx-modal" class="rounded-xl border border-slate-200 px-4 py-2.5 text-[11px] font-bold text-slate-700 transition hover:bg-slate-100">Batal</button>
                    <button type="submit" id="tf-save" class="rounded-xl bg-[#0B3A22] px-5 py-2.5 text-[11px] font-bold text-white transition hover:bg-[#0e4a2c] active:scale-95">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal detail --}}
    <div id="trx-detail" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/40 p-4 backdrop-blur-sm">
        <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <h3 class="text-[15px] font-bold text-[#0B3A22]">Detail Transaksi</h3>
                <button type="button" data-close="trx-detail" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">{!! $ic($p['x'], 'h-4 w-4') !!}</button>
            </div>
            <div id="dt-body" class="max-h-[60vh] space-y-3 overflow-y-auto px-6 py-5"></div>
            <div class="flex justify-end gap-2 border-t border-slate-100 px-6 py-4">
                <button type="button" id="dt-edit" class="rounded-xl {{ $lav }} px-4 py-2 text-[11px] font-bold text-slate-800 transition hover:bg-[#cfd9f7]">Edit</button>
                <button type="button" data-close="trx-detail" class="rounded-xl bg-[#0B3A22] px-4 py-2 text-[11px] font-bold text-white transition hover:bg-[#0e4a2c]">Tutup</button>
            </div>
        </div>
    </div>

    {{-- Modal konfirmasi hapus --}}
    <div id="del-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/40 p-4 backdrop-blur-sm">
        <div class="w-full max-w-sm overflow-hidden rounded-2xl bg-white p-6 text-center shadow-2xl">
            <span class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-[#FBD5D5] text-[#B42318]">{!! $ic($p['alert'], 'h-6 w-6') !!}</span>
            <h3 class="text-[15px] font-bold text-[#0B3A22]">Hapus Transaksi?</h3>
            <p class="mt-1.5 text-[11px] leading-relaxed text-slate-600">Transaksi <span id="del-kode" class="font-bold text-slate-900">-</span> akan dihapus permanen dari database.</p>
            <div class="mt-5 flex justify-center gap-2">
                <button type="button" data-close="del-modal" class="rounded-xl border border-slate-200 px-4 py-2 text-[11px] font-bold text-slate-700 transition hover:bg-slate-100">Batal</button>
                <button type="button" id="del-confirm" class="rounded-xl bg-[#B42318] px-4 py-2 text-[11px] font-bold text-white transition hover:bg-[#8f1c13] active:scale-95">Hapus</button>
            </div>
        </div>
    </div>
=======
                        <button type="button" id="refresh-btn" aria-label="Segarkan daftar transaksi" class="flex h-10 w-10 items-center justify-center rounded-xl {{ $soft }} text-slate-700 transition hover:bg-[#E3E8FA] active:scale-90">
                            <span id="refresh-icon" class="block">{!! $ic($p['refresh'], 'h-4 w-4', '2') !!}</span>
                        </button>
                    </div>
                </section>

                {{-- Tabel Transaksi --}}
                <section class="{{ $card }} p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-[18px] font-bold text-[#0B3A22]">Daftar Transaksi Hari Ini</h2>
                            <p class="mt-1 text-[11px] leading-relaxed text-slate-600">Kolom status menunjukkan hasil pembayaran; tombol detail membuka struk dan E-Ticket pemesanan.</p>
                        </div>
                        <button type="button" id="btn-export" class="flex shrink-0 items-center gap-2 rounded-lg {{ $lav }} px-4 py-2 text-[11px] font-semibold text-slate-800 transition hover:bg-[#cfd9f7] active:scale-95">
                            {!! $ic($p['download'], 'h-4 w-4') !!} Unduh XLS
                        </button>
                    </div>

                    <div class="mt-5 overflow-x-auto">
                        <div class="min-w-[760px]">
                            <div class="grid grid-cols-[100px_130px_minmax(0,1.5fr)_90px_120px_150px_130px] items-center gap-2 rounded-xl bg-[#E8ECFB] px-4 py-3 text-[9px] font-bold uppercase leading-snug tracking-wide text-slate-700">
                                <span>Waktu<br>Transaksi</span><span>Kode Booking</span><span>Customer &amp; Unit</span><span>Metode<br>Bayar</span><span>Nominal</span><span>Status</span><span class="text-right">Opsi</span>
                            </div>
                            <div id="trx-list"></div>
                            <p id="trx-empty" class="hidden rounded-xl bg-white p-6 text-center text-[11px] font-medium text-slate-500 shadow-[0_2px_10px_rgba(15,69,39,0.06)]">Tidak ada transaksi yang cocok dengan filter.</p>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-between gap-4">
                        <p class="text-[10px] text-slate-600">Menampilkan <span data-shown>10</span> dari 86 transaksi hari ini</p>
                        <div id="pager" class="flex items-center gap-2 text-[11px] font-semibold">
                            <button type="button" data-pg="prev" aria-label="Sebelumnya" class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#E8ECFB] text-slate-700 transition hover:bg-[#DDE3F8]">{!! $ic($p['chevl'], 'h-4 w-4', '2') !!}</button>
                            @foreach (['1', '2', '3'] as $pg)
                                <button type="button" data-pg="{{ $pg }}" class="flex h-8 w-8 items-center justify-center rounded-lg transition {{ $loop->first ? 'bg-[#0B3A22] text-white' : 'bg-[#E8ECFB] text-slate-700 hover:bg-[#DDE3F8]' }}">{{ $pg }}</button>
                            @endforeach
                            <span class="px-1 text-slate-500">...</span>
                            <button type="button" data-pg="18" class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#E8ECFB] text-slate-700 transition hover:bg-[#DDE3F8]">18</button>
                            <button type="button" data-pg="next" aria-label="Berikutnya" class="flex h-8 w-8 items-center justify-center rounded-lg bg-[#E8ECFB] text-slate-700 transition hover:bg-[#DDE3F8]">{!! $ic($p['chevr'], 'h-4 w-4', '2') !!}</button>
                        </div>
                    </div>
                </section>
@endsection
@section('overlays')
    {{-- Toast --}}
    <div id="toast" role="status" class="pointer-events-none fixed right-6 top-20 z-50 max-w-sm translate-y-[-8px] rounded-xl bg-[#0B3A22] px-4 py-3 text-[12px] font-semibold text-white opacity-0 shadow-[0_10px_24px_-8px_rgba(11,46,34,0.55)] transition duration-300"></div>
>>>>>>> d0c70c8d31d52cecb4d10594c5c6910a3c2cf03b
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        const $ = (s, r = document) => r.querySelector(s);
        const $$ = (s, r = document) => [...r.querySelectorAll(s)];
        const ICON = @json($p);
        const ic = (n, c = 'h-4 w-4', sw = '1.8') => `<svg class="${c}" fill="none" stroke="currentColor" stroke-width="${sw}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="${ICON[n]}"/></svg>`;
        const rp = (n) => 'Rp ' + Math.round(n).toLocaleString('id-ID');
<<<<<<< HEAD
        const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
        const CSRF = $('meta[name="csrf-token"]').content;

        // ---------- HTTP (unwrap { ok, data }) ----------
        const http = async (method, url, body) => {
            const res = await fetch(url, {
                method,
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
                body: body ? JSON.stringify(body) : undefined,
            });
            let json = null;
            try { json = await res.json(); } catch (e) { /* ignore */ }
            if (!res.ok || (json && json.ok === false)) {
                const msg = (json && json.message) || (json && json.errors ? Object.values(json.errors).flat()[0] : 'Terjadi kesalahan. Coba lagi.');
                throw new Error(msg);
            }
            return json && Object.prototype.hasOwnProperty.call(json, 'data') ? json.data : json;
        };

        // ---------- State ----------
        const state = { rows: @json($transaksis), q: '', bank: 'all', status: 'all', page: 1, per: 8, editing: null, deleting: null, viewing: null };

        // ---------- Toast ----------
        let toastTimer;
        const toast = (msg, bad = false) => {
            const el = $('#toast');
            el.textContent = msg;
            el.classList.toggle('bg-[#0B3A22]', !bad);
            el.classList.toggle('bg-[#B42318]', bad);
=======
        const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

        // ---------- State ----------
        const state = { rows: @json($rows), q: '', bank: 'all', status: 'all', page: 1 };

        // ---------- Toast ----------
        let toastTimer;
        const toast = (msg) => {
            const el = $('#toast');
            el.textContent = msg;
>>>>>>> d0c70c8d31d52cecb4d10594c5c6910a3c2cf03b
            el.classList.remove('opacity-0', 'translate-y-[-8px]');
            el.classList.add('opacity-100', 'translate-y-0');
            clearTimeout(toastTimer);
            toastTimer = setTimeout(() => {
                el.classList.add('opacity-0', 'translate-y-[-8px]');
                el.classList.remove('opacity-100', 'translate-y-0');
            }, 2800);
        };

<<<<<<< HEAD
        // ---------- Modal helper ----------
        const openModal = (id) => { const m = $('#' + id); m.classList.remove('hidden'); m.classList.add('flex'); };
        const closeModal = (id) => { const m = $('#' + id); m.classList.add('hidden'); m.classList.remove('flex'); };
        $$('[data-close]').forEach((b) => b.addEventListener('click', () => closeModal(b.dataset.close)));
        ['trx-modal', 'trx-detail', 'del-modal'].forEach((id) => {
            const m = $('#' + id);
            m.addEventListener('click', (e) => { if (e.target === m) closeModal(id); });
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') ['trx-modal', 'trx-detail', 'del-modal'].forEach(closeModal);
        });

=======
>>>>>>> d0c70c8d31d52cecb4d10594c5c6910a3c2cf03b
        // ---------- Status pill ----------
        const PILL = {
            lunas:  ['bg-[#CDEFD5] text-[#0B3A22]', 'checkc', 'Lunas'],
            pending: ['bg-[#FBD9B0] text-[#6B4520]', 'clock', 'Menunggu'],
            gagal:  ['bg-[#FBD5D5] text-[#B42318]', 'alert', 'Gagal'],
        };
        const pill = (st) => {
            const [cls, icon, label] = PILL[st] || PILL.pending;
            return `<span class="inline-flex items-center gap-1.5 rounded-xl ${cls} px-3 py-1.5 text-[9px] font-bold leading-tight">${ic(icon, 'h-3 w-3 shrink-0', '2')} ${label}</span>`;
        };

        // ---------- Baris tabel ----------
<<<<<<< HEAD
        const row = (h, i) => `<div class="${reduce ? '' : 'pop-in '}grid grid-cols-[90px_140px_minmax(0,1.4fr)_100px_130px_150px_160px] items-center gap-3 border-b border-slate-100 px-5 py-4 last:border-0" style="animation-delay:${i * 40}ms">
            <span class="font-mono text-[10px] text-slate-500">${esc(h.t)} WIB</span>
            <span class="break-words text-[11px] font-semibold text-slate-900">${esc(h.kode)}</span>
            <span><b class="block text-[11px] text-slate-900">${esc(h.name)}</b><span class="block text-[10px] leading-snug text-slate-600">${esc(h.unit)}</span></span>
            <span><span class="inline-block rounded-lg bg-[#DDE6FB] px-3 py-1.5 text-[10px] font-bold text-slate-800">${esc(h.metode)}</span></span>
            <b class="text-[12px] text-slate-900">${rp(h.amount)}</b>
            <span>${pill(h.status)}</span>
            <span class="flex justify-end gap-1.5">
                <button type="button" data-view="${h.id}" class="rounded-lg bg-[#DDE6FB] px-3 py-2 text-[9px] font-bold text-slate-800 transition hover:bg-[#cfd9f7] active:scale-95">Detail</button>
                <button type="button" data-edit="${h.id}" title="Edit" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100 hover:text-[#0B3A22] active:scale-95">${ic('edit', 'h-3.5 w-3.5', '2')}</button>
                <button type="button" data-del="${h.id}" title="Hapus" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 transition hover:bg-[#FBD5D5] hover:text-[#B42318] active:scale-95">${ic('trash', 'h-3.5 w-3.5', '2')}</button>
            </span>
        </div>`;

        const visible = () => state.rows.filter((h) =>
            (state.bank === 'all' || h.metode === state.bank) &&
            (state.status === 'all' || h.status === state.status) &&
            (!state.q || (h.kode + ' ' + h.name + ' ' + h.unit).toLowerCase().includes(state.q)));

        const renderStats = () => {
            const today = state.rows.filter((h) => h.today);
            $('[data-stat-today]').textContent = today.length;
            $('[data-stat-omzet]').textContent = rp(today.reduce((a, h) => a + h.amount, 0));
            $('[data-stat-pending]').textContent = state.rows.filter((h) => h.status === 'pending').length;
        };

        const renderPager = (pages) => {
            const pager = $('#pager');
            if (pages <= 1) { pager.innerHTML = ''; return; }
            let html = `<button type="button" data-pg="prev" class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-600 transition hover:bg-slate-100">${ic('chevl', 'h-4 w-4', '2')}</button>`;
            for (let p = 1; p <= pages; p++) {
                html += `<button type="button" data-pg="${p}" class="flex h-8 w-8 items-center justify-center rounded-lg transition ${p === state.page ? 'bg-[#0B3A22] text-white' : 'border border-slate-200 text-slate-600 hover:bg-slate-100'}">${p}</button>`;
            }
            html += `<button type="button" data-pg="next" class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-600 transition hover:bg-slate-100">${ic('chevr', 'h-4 w-4', '2')}</button>`;
            $('#pager').innerHTML = html;
        };

        const render = () => {
            const list = visible();
            const pages = Math.max(1, Math.ceil(list.length / state.per));
            if (state.page > pages) state.page = pages;
            const start = (state.page - 1) * state.per;
            const slice = list.slice(start, start + state.per);
            $('#trx-list').innerHTML = slice.length
                ? slice.map(row).join('')
                : `<p class="px-5 py-10 text-center text-[11px] font-medium text-slate-500">Tidak ada transaksi yang cocok dengan filter.</p>`;
            $('[data-shown]').textContent = slice.length;
            $('[data-total]').textContent = list.length;
            renderPager(Math.max(1, Math.ceil(list.length / state.per)));
            renderStats();
        };

        // ---------- Filter: bank ----------
        const on = ['bg-white', 'text-[#0B3A22]', 'shadow-sm'];
        const off = ['text-slate-600', 'hover:text-[#0B3A22]'];
=======
        const row = (h, i) => `<div class="${reduce ? '' : 'pop-in '}grid grid-cols-[100px_130px_minmax(0,1.5fr)_90px_120px_150px_130px] items-center gap-2 border-b border-slate-100 px-4 py-4 last:border-0" style="animation-delay:${i * 40}ms">
            <span class="font-mono text-[10px] text-slate-500">${h.t} WIB</span>
            <span class="break-words text-[11px] font-semibold text-slate-900">${h.id}</span>
            <span><b class="block text-[11px] text-slate-900">${esc(h.name)}</b><span class="block text-[10px] leading-snug text-slate-600">${esc(h.unit)}</span></span>
            <span><span class="inline-block rounded-lg bg-[#DDE6FB] px-3 py-1.5 text-[10px] font-bold text-slate-800">${esc(h.method)}</span></span>
            <b class="text-[12px] text-slate-900">${rp(h.amount)}</b>
            <span>${pill(h.status)}</span>
            <span class="text-right"><button type="button" data-detail="${h.id}" class="rounded-lg bg-[#DDE6FB] px-3 py-2 text-center text-[9px] font-bold leading-tight text-slate-800 transition hover:bg-[#cfd9f7] active:scale-95">Lihat<br>Detail</button></span>
        </div>`;

        const visible = () => state.rows.filter((h) =>
            (state.bank === 'all' || h.method === state.bank) &&
            (state.status === 'all' || h.status === state.status) &&
            (!state.q || (h.id + ' ' + h.name + ' ' + h.unit).toLowerCase().includes(state.q)));

        const render = () => {
            const list = visible();
            $('#trx-list').innerHTML = list.map(row).join('');
            $('#trx-empty').classList.toggle('hidden', list.length > 0);
            $('[data-shown]').textContent = list.length;
        };

        // ---------- Filter: bank ----------
        const on = ['bg-[#0B3A22]', 'text-white', 'shadow-sm'];
        const off = ['text-slate-700', 'hover:text-[#0B3A22]'];
>>>>>>> d0c70c8d31d52cecb4d10594c5c6910a3c2cf03b
        $$('[data-bank]').forEach((b) => b.addEventListener('click', () => {
            $$('[data-bank]').forEach((t) => { t.classList.remove(...on); t.classList.add(...off); });
            b.classList.remove(...off); b.classList.add(...on);
            state.bank = b.dataset.bank;
<<<<<<< HEAD
            state.page = 1;
=======
>>>>>>> d0c70c8d31d52cecb4d10594c5c6910a3c2cf03b
            render();
        }));

        // ---------- Filter: pencarian ----------
<<<<<<< HEAD
        $('#search').addEventListener('input', (e) => { state.q = e.target.value.trim().toLowerCase(); state.page = 1; render(); });
=======
        $('#search').addEventListener('input', (e) => { state.q = e.target.value.trim().toLowerCase(); render(); });
>>>>>>> d0c70c8d31d52cecb4d10594c5c6910a3c2cf03b

        // ---------- Filter: status ----------
        const menu = $('#status-menu');
        $('#status-btn').addEventListener('click', (e) => { e.stopPropagation(); menu.classList.toggle('hidden'); });
        document.addEventListener('click', () => menu.classList.add('hidden'));
        $$('[data-status]').forEach((b) => b.addEventListener('click', () => {
            state.status = b.dataset.status;
<<<<<<< HEAD
            $('#status-label').textContent = b.textContent.trim();
            menu.classList.add('hidden');
            state.page = 1;
            render();
        }));

        // ---------- Pagination ----------
        $('#pager').addEventListener('click', (e) => {
            const b = e.target.closest('[data-pg]');
            if (!b) return;
            const pages = Math.max(1, Math.ceil(visible().length / state.per));
            const v = b.dataset.pg;
            if (v === 'prev') state.page = Math.max(1, state.page - 1);
            else if (v === 'next') state.page = Math.min(pages, state.page + 1);
            else state.page = Number(v);
            render();
        });

        // ---------- Form ----------
        const pad = (n) => String(n).padStart(2, '0');
        const autoNow = () => { const d = new Date(); return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`; };
        const autoKode = () => { const d = new Date(); return 'TRX-' + d.getFullYear() + pad(d.getMonth() + 1) + pad(d.getDate()) + '-' + String(Math.floor(Math.random() * 900) + 100); };
        const toServer = (v) => (v ? v.replace('T', ' ') + ':00' : v);
        const showErr = (msg) => { const el = $('#tf-error'); el.textContent = msg || ''; el.classList.toggle('hidden', !msg); };
        const syncChange = () => { $('#tf-change').value = Math.max(0, Number($('#tf-pay').value || 0) - Number($('#tf-total').value || 0)); };
        $('#tf-total').addEventListener('input', syncChange);
        $('#tf-pay').addEventListener('input', syncChange);

        const openForm = (mode, h) => {
            showErr('');
            state.editing = mode === 'edit' ? h.id : null;
            $('#tf-title').textContent = mode === 'edit' ? 'Edit Transaksi' : 'Tambah Transaksi';
            $('#tf-kode').value = mode === 'edit' ? h.kode : autoKode();
            $('#tf-user').value = mode === 'edit' ? h.pengguna_id : ($('#tf-user').options[0]?.value || '');
            $('#tf-when').value = mode === 'edit' ? h.datetime : autoNow();
            $('#tf-metode').value = mode === 'edit' ? h.metode_raw : 'bca';
            $('#tf-jenis').value = mode === 'edit' ? h.jenis : 'pembayaran_renang';
            $('#tf-status').value = mode === 'edit' ? h.status : 'pending';
            $('#tf-total').value = mode === 'edit' ? h.total_bayar : '';
            $('#tf-pay').value = mode === 'edit' ? h.jumlah_bayar : '';
            $('#tf-bukti').value = mode === 'edit' ? (h.bukti || '') : '';
            syncChange();
            openModal('trx-modal');
        };

        $('#tf-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            showErr('');
            const payload = {
                kode_transaksi: $('#tf-kode').value.trim(),
                pengguna_id: $('#tf-user').value,
                tgl_transaksi: toServer($('#tf-when').value),
                total_bayar: Number($('#tf-total').value || 0),
                jumlah_bayar: Number($('#tf-pay').value || 0),
                kembalian: Math.max(0, Number($('#tf-pay').value || 0) - Number($('#tf-total').value || 0)),
                metode: $('#tf-metode').value,
                jenis_transaksi: $('#tf-jenis').value,
                status: $('#tf-status').value,
                bukti: $('#tf-bukti').value.trim() || null,
            };
            if (!payload.kode_transaksi) return showErr('Kode transaksi wajib diisi.');
            if (!payload.pengguna_id) return showErr('Pengguna wajib dipilih.');
            if (!payload.tgl_transaksi) return showErr('Tanggal & waktu wajib diisi.');
            if (payload.jumlah_bayar < payload.total_bayar) return showErr('Jumlah bayar tidak boleh kurang dari total bayar.');

            const saveBtn = $('#tf-save');
            saveBtn.disabled = true;
            try {
                if (state.editing) {
                    const saved = await http('PUT', `/admin/transaksi/${state.editing}`, payload);
                    const idx = state.rows.findIndex((r) => r.id === state.editing);
                    if (idx > -1) state.rows[idx] = saved;
                    toast('Transaksi ' + saved.kode + ' diperbarui.');
                } else {
                    const saved = await http('POST', '/admin/transaksi', payload);
                    state.rows.unshift(saved);
                    toast('Transaksi ' + saved.kode + ' ditambahkan.');
                }
                closeModal('trx-modal');
                state.page = 1;
                render();
            } catch (err) {
                showErr(err.message);
            } finally {
                saveBtn.disabled = false;
            }
        });

        // ---------- Detail ----------
        const detailLine = (label, val) => `<div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-2.5 last:border-0"><span class="shrink-0 text-[11px] text-slate-500">${label}</span><span class="text-right text-[12px] font-semibold text-slate-900">${val}</span></div>`;
        const openDetail = (h) => {
            state.viewing = h.id;
            $('#dt-body').innerHTML =
                detailLine('Kode Transaksi', esc(h.kode)) +
                detailLine('Pengguna', esc(h.name)) +
                detailLine('Unit', esc(h.unit)) +
                detailLine('Waktu', esc(h.date) + ' • ' + esc(h.t) + ' WIB') +
                detailLine('Metode', esc(h.metode)) +
                detailLine('Total Bayar', rp(h.total_bayar)) +
                detailLine('Jumlah Bayar', rp(h.jumlah_bayar)) +
                detailLine('Kembalian', rp(h.kembalian)) +
                detailLine('Status', pill(h.status)) +
                detailLine('Bukti', esc(h.bukti || '-'));
            openModal('trx-detail');
        };
        $('#dt-edit').addEventListener('click', () => {
            const h = state.rows.find((r) => r.id === state.viewing);
            if (!h) return;
            closeModal('trx-detail');
            openForm('edit', h);
        });

        // ---------- Delegasi klik tabel ----------
        const byId = (id) => state.rows.find((r) => r.id === id);
        $('#trx-list').addEventListener('click', (e) => {
            const v = e.target.closest('[data-view]');
            const ed = e.target.closest('[data-edit]');
            const del = e.target.closest('[data-del]');
            if (v) { const h = byId(v.dataset.view); if (h) openDetail(h); }
            else if (ed) { const h = byId(ed.dataset.edit); if (h) openForm('edit', h); }
            else if (del) { const h = byId(del.dataset.del); if (h) { state.deleting = h.id; $('#del-kode').textContent = h.kode; openModal('del-modal'); } }
        });

        // ---------- Hapus ----------
        $('#del-confirm').addEventListener('click', async () => {
            if (!state.deleting) return;
            const btn = $('#del-confirm');
            btn.disabled = true;
            try {
                await http('DELETE', `/admin/transaksi/${state.deleting}`);
                state.rows = state.rows.filter((r) => r.id !== state.deleting);
                closeModal('del-modal');
                toast('Transaksi dihapus.');
                state.page = 1;
                render();
            } catch (err) {
                toast(err.message, true);
            } finally {
                btn.disabled = false;
                state.deleting = null;
            }
        });

        // ---------- Tambah ----------
        $('#trx-add').addEventListener('click', () => openForm('create'));
=======
            $('#status-label').textContent = 'Status: ' + b.textContent.trim().replace(' Status', '');
            menu.classList.add('hidden');
            render();
        }));

        // ---------- Segarkan ----------
        $('#refresh-btn').addEventListener('click', () => {
            const icon = $('#refresh-icon');
            if (icon.animate && !reduce) icon.animate([{ transform: 'rotate(0)' }, { transform: 'rotate(360deg)' }], { duration: 800, easing: 'ease-in-out' });
            render();
            toast('Daftar transaksi diperbarui.');
        });

        // ---------- Ekspor CSV ----------
        $('#btn-export').addEventListener('click', () => {
            const rows = [['Waktu', 'Kode Booking', 'Customer', 'Unit', 'Metode', 'Nominal', 'Status'],
                ...visible().map((h) => [h.t + ' WIB', h.id, h.name, h.unit, h.method, h.amount, h.status])];
            const csv = '\ufeff' + rows.map((r) => r.map((v) => `"${String(v).replace(/"/g, '""')}"`).join(',')).join('\n');
            const a = Object.assign(document.createElement('a'), { href: URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8' })), download: 'transaksi-jiwanta.csv' });
            document.body.appendChild(a); a.click(); a.remove();
            toast('Daftar transaksi diunduh');
        });

        // ---------- Detail ----------
        $('#trx-list').addEventListener('click', (e) => {
            const b = e.target.closest('[data-detail]');
            if (b) toast('Membuka detail transaksi ' + b.dataset.detail);
        });

        // ---------- Pagination (tampilan) ----------
        const pgOn = ['bg-[#0B3A22]', 'text-white'];
        const pgOff = ['bg-[#E8ECFB]', 'text-slate-700', 'hover:bg-[#DDE3F8]'];
        const toggle = (el, list, flag) => list.forEach((c) => el.classList.toggle(c, flag));
        const setPage = (p) => {
            state.page = p;
            $$('#pager [data-pg]').forEach((b) => {
                if (isNaN(Number(b.dataset.pg))) return;
                const act = b.dataset.pg === String(p);
                toggle(b, pgOn, act); toggle(b, pgOff, !act);
            });
        };
        $$('#pager [data-pg]').forEach((b) => b.addEventListener('click', () => {
            const v = b.dataset.pg;
            const next = v === 'prev' ? Math.max(1, state.page - 1) : v === 'next' ? Math.min(18, state.page + 1) : Number(v);
            if ([1, 2, 3, 18].includes(next)) setPage(next);
            else toast('Halaman ' + next);
        }));
>>>>>>> d0c70c8d31d52cecb4d10594c5c6910a3c2cf03b

        // ---------- Mulai ----------
        render();
    });
</script>
@endpush
