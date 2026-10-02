@extends('layouts.dashboard')

@section('title', 'Verifikasi & Transaksi - Jiwanta')

{{-- Font desain: Plus Jakarta Sans (aman jika layout belum punya @stack('styles'), hanya tidak terpakai) --}}
@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@endpush

@section('content')

@php
    // ---------- Helper ----------
    $ic = fn (string $d, string $c = 'h-4 w-4', string $sw = '1.8') =>
        '<svg class="'.$c.'" fill="none" stroke="currentColor" stroke-width="'.$sw.'" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="'.$d.'"/></svg>';
    $br = fn (array $lines) => implode('<br>', array_map('e', $lines));

    $p = [
        'dashboard' => 'M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z',
        'receipt'   => 'M6 3h12v18l-3-2-3 2-3-2-3 2V3zM9 8h6M9 12h6',
        'ticket'    => 'M4 7h16v3a2 2 0 000 4v3H4v-3a2 2 0 000-4V7zM14 7v10',
        'cabin'     => 'M3 11l9-8 9 8M5 10v10h14V10M10 20v-5h4v5',
        'pool'      => 'M3 19c1.5 0 1.5-1 3-1s1.5 1 3 1 1.5-1 3-1 1.5 1 3 1 1.5-1 3-1 1.5 1 3 1M8 15V6M14 15V6M8 8h6M8 12h6',
        'users'     => 'M17 20v-1a4 4 0 00-4-4H7a4 4 0 00-4 4v1M10 11a4 4 0 100-8 4 4 0 000 8zM21 20v-1a4 4 0 00-3-3.9M16 3.1a4 4 0 010 7.8',
        'chart'     => 'M5 20V10M12 20V4M19 20v-7',
        'gear'      => 'M12 15a3 3 0 100-6 3 3 0 000 6zM12 2v3M12 19v3M2 12h3M19 12h3M5 5l2 2M17 17l2 2M19 5l-2 2M7 17l-2 2',
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
        'shield'    => 'M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6l8-3zM9 12l2 2 4-4',
        'x'         => 'M6 6l12 12M18 6L6 18',
        'qr'        => 'M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h2v2h-2zM18 14h2v6h-4M14 18h2v2h-2z',
        'bolt'      => 'M13 3L5 13h6l-1 8 8-10h-6l1-8z',
        'eye'       => 'M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12zM12 15a3 3 0 100-6 3 3 0 000 6z',
        'external'  => 'M14 4h6v6M20 4l-9 9M18 14v5a1 1 0 01-1 1H5a1 1 0 01-1-1V7a1 1 0 011-1h5',
        'alert'     => 'M12 4l9 16H3L12 4zM12 10v4M12 17h.01',
        'mail'      => 'M4 6h16v12H4zM4 7l8 6 8-6',
        'history'   => 'M3 12a9 9 0 109-9 9 9 0 00-6.4 2.6L3 8M3 3v5h5M12 8v4l3 2',
        'pin'       => 'M12 21s-6-5.5-6-11a6 6 0 1112 0c0 5.5-6 11-6 11zM12 12a2 2 0 100-4 2 2 0 000 4z',
    ];

    $nav = [
        'Navigasi Utama' => [['Dashboard', 'dashboard', false, null], ['Verifikasi & Transaksi', 'receipt', true, 14]],
        'Manajemen Resort' => [['Kelola Tiket', 'ticket', false, null], ['Kelola Cabin Suite', 'cabin', false, null]],
        'Administrasi & Sistem' => [['Manajemen Pengguna', 'users', false, null], ['Laporan & Export', 'chart', false, null], ['Pengaturan Profil', 'gear', false, null]],
    ];

    $mint  = 'bg-[#CDEFD5]';
    $peach = 'bg-[#FBD9B0]';
    $lav   = 'bg-[#DDE6FB]';
    $soft  = 'bg-[#EEF1FC]';
    $card  = 'rounded-2xl bg-white shadow-[0_4px_18px_rgba(15,69,39,0.08)]';

    $banks = [['all', 'Semua'], ['BCA', 'BCA'], ['BNI', 'BNI'], ['Mandiri', 'Mandiri'], ['QRIS', 'QRIS']];

    // ---------- Data antrean (ganti dengan data dari controller) ----------
    // act: quick = Pratinjau + Cepat Verifikasi | proof = Buka Bukti Transfer | reupload = Minta Upload Ulang
    $proof = asset('images/bukti-transfer.jpg');
    $queue = [
        [
            'id' => 'JW-20260920-001', 'name' => 'Ahmad Fadillah', 'email' => 'ahmad.fadillah@gmail.com', 'phone' => '+62 812-4491-0982',
            'service' => ['2× Tiket', 'Premier Onsen'], 'unit' => '2 Tiket Premier Onsen', 'session' => 'Sesi Siang (13:00 - 16:00)',
            'amount' => 170412, 'method' => 'BCA', 'channel' => 'BCA Virtual Account / Manual', 'pay' => 'BCA Transfer',
            'sender' => ['Ahmad Fadillah', 'BCA • 8271-0329-11'], 'dest' => ['Jiwanta Wisata Mandiri', 'BCA • 4370-9008-21'],
            'file' => 'bukti-transfer-001.jpg', 'proof' => $proof, 'ocr' => 99, 'mins' => 4,
            'tag' => 'Tiket Reguler', 'tone' => 'lav', 'dot' => 'bg-slate-400', 'act' => 'quick', 'problem' => false, 'note' => '',
        ],
        [
            'id' => 'JW-20260920-004', 'name' => 'Siti Nurhaliza', 'email' => 'siti.nurhaliza@gmail.com', 'phone' => '+62 857-1190-3321',
            'service' => ['1× Cabin', 'Pinus Suite (2 Malam)'], 'unit' => 'Pinus Suite Cabin (2 Malam)', 'session' => 'Check-in 14:00 WIB',
            'amount' => 2450789, 'method' => 'Mandiri', 'channel' => 'Mandiri Transfer / Manual', 'pay' => 'Mandiri Transfer',
            'sender' => ['Siti Nurhaliza', 'Mandiri • 1310-0452-88'], 'dest' => ['Jiwanta Wisata Mandiri', 'Mandiri • 1310-0987-65'],
            'file' => 'bukti-transfer-004.jpg', 'proof' => $proof, 'ocr' => 98, 'mins' => 9,
            'tag' => 'Glamping Resort', 'tone' => 'peach', 'dot' => 'bg-[#C2762B]', 'act' => 'quick', 'problem' => false, 'note' => '',
        ],
        [
            'id' => 'JW-20260920-009', 'name' => 'Danang Wijaya', 'email' => 'danang.wijaya@gmail.com', 'phone' => '+62 821-9988-1210',
            'service' => ['4× Classic Ticket', '+ Gazebo Pinus'], 'unit' => '4x Classic Ticket + Gazebo Pinus', 'session' => 'Sesi Pagi (08:00 - 11:00)',
            'amount' => 360120, 'method' => 'BNI', 'channel' => 'BNI Virtual Account', 'pay' => 'BNI Virtual Account',
            'sender' => ['Danang Wijaya', 'BNI • 0456-1129-03'], 'dest' => ['Jiwanta Wisata Mandiri', 'BNI • 8808-0123-45'],
            'file' => 'bukti-transfer-009.jpg', 'proof' => $proof, 'ocr' => 97, 'mins' => 18,
            'tag' => 'Tiket Rombongan', 'tone' => 'lav', 'dot' => 'bg-slate-400', 'act' => 'proof', 'problem' => false, 'note' => '',
        ],
        [
            'id' => 'JW-20260920-012', 'name' => 'Rendi Pratama', 'email' => 'rendi.pratama@gmail.com', 'phone' => '+62 878-3341-0020',
            'service' => ['1× Tiket', 'Classic Hot Spring'], 'unit' => '1x Tiket Classic Hot Spring', 'session' => 'Sesi Siang (13:00 - 16:00)',
            'amount' => 45319, 'method' => 'BCA', 'channel' => 'BCA Transfer / Manual', 'pay' => 'BCA Transfer',
            'sender' => ['Rendi Pratama', 'BCA • 7712-0045-09'], 'dest' => ['Jiwanta Wisata Mandiri', 'BCA • 4370-9008-21'],
            'file' => 'bukti-transfer-012.jpg', 'proof' => $proof, 'ocr' => 61, 'mins' => 26,
            'tag' => 'Struk Buram', 'tone' => 'red', 'dot' => 'bg-red-600', 'act' => 'reupload', 'problem' => true,
            'note' => 'OCR gagal membaca 3 digit terakhir. Perlu audit visual staf.',
        ],
    ];

    $history = [
        ['t' => '10:02', 'id' => 'JW-20260920-098', 'name' => 'Dewi Maharani',      'unit' => '2x Premier Onsen Hot Spring',   'method' => 'BCA',     'amount' => 170000],
        ['t' => '09:54', 'id' => 'JW-20260920-097', 'name' => 'Bambang Hariyanto',  'unit' => 'Camellia Family Cabin (1 Malam)', 'method' => 'BCA',     'amount' => 1850320],
        ['t' => '09:48', 'id' => 'JW-20260920-096', 'name' => 'Priscilla Chandra',  'unit' => '4x Classic Ticket Onsen',        'method' => 'Mandiri', 'amount' => 180115],
        ['t' => '09:30', 'id' => 'JW-20260920-095', 'name' => 'Kurniawan Tejo',     'unit' => 'Private Spa Cabana Sesi Pagi',   'method' => 'BCA',     'amount' => 450401],
        ['t' => '09:12', 'id' => 'JW-20260920-094', 'name' => 'Nadira Alatas',      'unit' => '1x Classic Ticket Hot Spring',   'method' => 'Mandiri', 'amount' => 45109],
    ];

    $first = $queue[0];
@endphp

    {{-- ================= LAYOUT CONTAINER ================= --}}
    <div class="min-w-full bg-[#F9F8FF] flex font-['Plus_Jakarta_Sans',ui-sans-serif,system-ui,sans-serif] text-slate-900 antialiased">

    {{-- ================= SIDEBAR ================= --}}
    <aside class="w-[240px] shrink-0 bg-white border-r border-slate-200 flex flex-col">
        <div class="px-5 pt-6 pb-4">
            <div class="flex items-center gap-3">
                <span class="flex h-[34px] w-[34px] shrink-0 items-center justify-center rounded-lg bg-[#0B3A22] text-[#B5F0BE]">{!! $ic($p['tree'], 'h-5 w-5') !!}</span>
                <div class="leading-tight">
                    <p class="text-[10px] font-bold tracking-wide">JIWANTA</p>
                    <p class="text-[6px] font-semibold uppercase tracking-wider text-slate-600">Ciwidey Resort</p>
                </div>
                <div class="leading-tight">
                    <p class="text-[13px] font-bold text-[#0B3A22]">Jiwanta</p>
                    <p class="text-[8px] font-bold uppercase tracking-wider text-[#8B5E34]">Admin Panel</p>
                </div>
            </div>
            <span class="mt-4 inline-flex items-center gap-2 rounded-full {{ $mint }} px-3 py-1.5 text-[10px] font-bold">
                <span class="h-2 w-2 rounded-full bg-[#0B3A22]"></span> Sistem Operasional Aktif
            </span>
        </div>


        <nav class="flex-1 px-4 space-y-6 overflow-y-auto">
            @foreach ($nav as $label => $items)
                <div>
                    <p class="mb-2 px-2 text-[9px] font-bold uppercase tracking-wider text-slate-500">{{ $label }}</p>
                    <ul class="space-y-1">
                        @foreach ($items as [$text, $icon, $active, $badge])
                            <li>
                                <a href="{{ route('tiket-renang') }}" class="flex h-[36px] items-center gap-3 rounded-lg px-3 text-[13px] font-medium transition {{ $active ? 'bg-[#0B3A22] text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                                    {!! $ic($p[$icon], 'h-4 w-4 shrink-0') !!}
                                    <span class="flex-1">{{ $text }}</span>
                                    @if ($badge)
                                        <span data-c="pending" class="rounded-full {{ $peach }} px-2 py-0.5 text-[10px] font-bold text-[#6B4520]">{{ $badge }}</span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </nav>

        <div class="border-t border-slate-200 p-4">
            <div class="mb-3 flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                <span class="flex items-center gap-2 text-[10px] font-semibold text-slate-700">{!! $ic($p['clock'], 'h-3.5 w-3.5') !!} Shift Pagi 07:00 - 15:00</span>
                <span class="h-2 w-2 rounded-full bg-[#0B3A22]"></span>
            </div>
            <button type="button" class="flex w-full items-center justify-center gap-2 rounded-lg bg-[#DDE6FB] py-2.5 text-[11px] font-bold text-red-700 transition hover:bg-[#cfd9f7]">
                {!! $ic($p['logout'], 'h-4 w-4') !!} Keluar Sistem
            </button>
        </div>
    </aside>

    {{-- ================= MAIN CONTENT ================= --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Topbar --}}
        <header class="h-[60px] bg-white/80 backdrop-blur border-b border-slate-200 px-6 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <p class="text-[11px] text-slate-600">Sistem Jiwanta</p>
                <span class="text-slate-400">/</span>
                <b class="text-[11px] font-semibold text-[#0B3A22]">Panel Kendali Utama</b>
            </div>
            <div class="flex items-center gap-3">
                <span class="flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1.5 text-[10px] font-medium">{!! $ic($p['snow'], 'h-3.5 w-3.5') !!} Ciwidey 18°C Kabut Sejuk</span>
                <span class="flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1.5 text-[10px] font-medium">
                    <span class="relative flex h-2 w-2"><span class="absolute h-full w-full animate-ping rounded-full bg-[#0B3A22] opacity-60"></span><span class="relative h-2 w-2 rounded-full bg-[#0B3A22]"></span></span>
                    Gate Turnstile Online
                </span>
                <button type="button" class="relative h-10 w-10 flex items-center justify-center rounded-full bg-slate-100 transition hover:bg-slate-200" aria-label="Notifikasi">
                    {!! $ic($p['bell'], 'h-5 w-5') !!}
                    <span class="absolute right-2 top-2 h-2.5 w-2.5 rounded-full bg-red-600 border-2 border-white"></span>
                </button>
                <div class="flex items-center gap-3 pl-3 border-l border-slate-200">
                    <img src="{{ asset('images/profil.jpg') }}" alt="Profil" class="h-10 w-10 rounded-full bg-[#C9B99A] object-cover border-2 border-white shadow-sm">
                    <div>
                        <p class="text-[11px] font-bold leading-tight">Bagas Dananjaya</p>
                        <p class="text-[9px] font-medium uppercase leading-tight text-slate-600">Super Admin Resort</p>
                    </div>
                </div>
            </div>
        </header>

        {{-- Content Area --}}
        <main class="flex-1 p-6 overflow-y-auto">
            <div class="max-w-7xl mx-auto space-y-6">

                {{-- Header Section --}}
                <div class="flex items-start justify-between gap-6">
                    <div class="max-w-xl">
                        <p class="mb-2 flex items-center gap-2">
                            <span class="rounded-md {{ $peach }} px-2.5 py-1 text-[9px] font-bold uppercase tracking-wide text-[#6B4520]">Gate Finance Desk</span>
                            <span class="text-[10px] text-slate-600">Realtime OCR Sync Active</span>
                        </p>
                        <h1 class="text-[32px] font-bold leading-tight text-[#0B3A22]">Verifikasi Pembayaran &amp; Riwayat Transaksi</h1>
                        <p class="mt-2 text-[13px] leading-relaxed text-slate-600">Validasi bukti transfer bank manual, pencocokan kode unik 3 digit, dan penerbitan tiket QR otomatis ke WhatsApp tamu.</p>
                    </div>

                    <div class="grid w-[400px] shrink-0 grid-cols-2 gap-3">
                        <div class="{{ $card }} flex items-center gap-3 px-4 py-3.5">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $peach }} text-[#8B5E34]">{!! $ic($p['clock'], 'h-5 w-5') !!}</span>
                            <div>
                                <p data-c="pending" class="text-[22px] font-bold leading-tight text-slate-900">14</p>
                                <p class="text-[10px] leading-tight text-slate-600">Menunggu Verifikasi</p>
                            </div>
                        </div>
                        <div class="{{ $card }} flex items-center gap-3 px-4 py-3.5">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $mint }} text-[#0B3A22]">{!! $ic($p['shield'], 'h-5 w-5') !!}</span>
                            <div>
                                <p data-c="verified" class="text-[22px] font-bold leading-tight text-slate-900">86</p>
                                <p class="text-[10px] leading-tight text-slate-600">Terverifikasi Hari Ini</p>
                            </div>
                        </div>
                        <div class="{{ $card }} flex items-center gap-3 px-4 py-3.5">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#FBD5D5] text-[#B42318]">{!! $ic($p['alert'], 'h-5 w-5') !!}</span>
                            <div>
                                <p data-c="rejected" class="text-[22px] font-bold leading-tight text-[#B42318]">2</p>
                                <p class="text-[10px] leading-tight text-slate-600">Ditolak / Bermasalah</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Filter Bar --}}
                <section class="{{ $card }} p-5">
                    <div class="flex flex-wrap items-center gap-3">
                        <label class="flex min-w-[280px] flex-1 items-center gap-3 rounded-xl {{ $soft }} px-4 py-3 text-slate-500 focus-within:ring-2 focus-within:ring-[#0B3A22]/30">
                            {!! $ic($p['search'], 'h-4 w-4 shrink-0') !!}
                            <input id="search" type="text" autocomplete="off" placeholder="Cari Kode Booking, Nama Tamu, atau No. Rek..." class="w-full bg-transparent text-[12px] text-slate-800 placeholder:text-slate-500 focus:outline-none">
                        </label>

                        <div class="flex items-center rounded-full {{ $soft }} p-1">
                            @foreach ($banks as [$key, $label])
                                <button type="button" data-bank="{{ $key }}"
                                        class="rounded-full px-4 py-1.5 text-[10px] font-semibold transition {{ $loop->first ? 'bg-[#0B3A22] text-white shadow-sm' : 'text-slate-700 hover:text-[#0B3A22]' }}">{{ $label }}</button>
                            @endforeach
                        </div>

                        <div class="relative">
                            <button type="button" id="status-btn" class="flex items-center gap-2 rounded-xl {{ $soft }} px-4 py-2.5 text-[11px] font-medium text-slate-700 transition hover:bg-[#E3E8FA]">
                                {!! $ic($p['sliders'], 'h-4 w-4') !!} <span id="status-label">Status: Menunggu</span> {!! $ic($p['chevron'], 'h-3.5 w-3.5') !!}
                            </button>
                            <div id="status-menu" class="absolute right-0 top-full z-20 mt-2 hidden w-44 rounded-xl bg-white p-1.5 shadow-[0_10px_28px_rgba(15,69,39,0.16)]">
                                <button type="button" data-status="waiting" class="block w-full rounded-lg px-3 py-2 text-left text-[11px] font-medium transition hover:bg-slate-50">Menunggu</button>
                                <button type="button" data-status="problem" class="block w-full rounded-lg px-3 py-2 text-left text-[11px] font-medium transition hover:bg-slate-50">Struk Buram</button>
                            </div>
                        </div>

                        <button type="button" id="refresh-btn" aria-label="Segarkan antrean" class="flex h-10 w-10 items-center justify-center rounded-xl {{ $soft }} text-slate-700 transition hover:bg-[#E3E8FA] active:scale-90">
                            <span id="refresh-icon" class="block">{!! $ic($p['refresh'], 'h-4 w-4', '2') !!}</span>
                        </button>
                    </div>
                </section>

                {{-- Main Content Grid --}}
                <div class="grid grid-cols-[1fr_320px] items-start gap-6">

                    {{-- Left Column --}}
                    <div class="space-y-4">

                        {{-- Review Card --}}
                        <section class="rounded-3xl bg-white p-6 shadow-[0_12px_32px_rgba(15,69,39,0.12)]">
                            <div id="review-body" class="transition duration-200">
                                <div class="flex flex-wrap items-center gap-x-3 gap-y-2">
                                    <span class="rounded-md {{ $peach }} px-2.5 py-1 text-[9px] font-bold uppercase tracking-wide text-[#6B4520]">Prioritas Verifikasi</span>
                                    <h2 data-f="id" class="text-[20px] font-bold text-slate-900">{{ $first['id'] }}</h2>
                                    <span data-f="uploaded" class="text-[10px] text-slate-600">• Diunggah 4 menit lalu (10:14 WIB)</span>
                                </div>
                                <span data-f="channel" class="mt-2 inline-block rounded-md {{ $lav }} px-2.5 py-1 text-[10px] font-semibold text-[#2F3F86]">{{ $first['channel'] }}</span>

                                <div class="mt-5 grid grid-cols-[190px_1fr] gap-5">
                                    {{-- Bukti transfer --}}
                                    <div>
                                        <div class="relative h-[230px] overflow-hidden rounded-2xl bg-slate-300">
                                            <img data-f="proof" src="{{ $first['proof'] }}" alt="Bukti transfer" class="h-full w-full object-cover">
                                            <span class="ocr-line pointer-events-none absolute inset-x-0 top-0 h-6 bg-gradient-to-b from-transparent via-emerald-300/30 to-transparent"></span>
                                        </div>
                                        <div class="mt-3 flex items-center justify-between gap-2 rounded-xl {{ $soft }} px-3 py-2.5 text-[9px] text-slate-700">
                                            <span>File: <b data-f="file" class="break-all font-semibold">{{ $first['file'] }}</b></span>
                                            <span class="flex shrink-0 items-center gap-1.5 font-bold">
                                                <span class="h-1.5 w-1.5 rounded-full bg-[#0B3A22]"></span><span>OCR Terbaca <span data-f="ocr">{{ $first['ocr'] }}</span>%</span>
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Detail --}}
                                    <div class="space-y-3">
                                        <div class="flex justify-between gap-3 rounded-2xl {{ $soft }} p-4">
                                            <div class="min-w-0">
                                                <p class="text-[9px] font-semibold text-slate-600">Nama Pemesan</p>
                                                <p data-f="name" class="mt-0.5 text-[17px] font-bold leading-tight text-slate-900">{{ $first['name'] }}</p>
                                                <p class="mt-1.5 text-[10px] leading-snug text-slate-600"><span data-f="email" class="break-all">{{ $first['email'] }}</span> •<br><span data-f="phone">{{ $first['phone'] }}</span></p>
                                            </div>
                                            <div class="shrink-0 text-right">
                                                <p class="text-[9px] font-semibold text-slate-600">Layanan</p>
                                                <p data-f="service" class="mt-0.5 text-[14px] font-bold leading-tight text-slate-900">{!! $br($first['service']) !!}</p>
                                                <p data-f="session" class="mt-1.5 max-w-[110px] text-[10px] font-semibold leading-snug text-[#8B5E34]">{{ $first['session'] }}</p>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="rounded-2xl {{ $soft }} p-3.5">
                                                <p class="text-[9px] font-semibold text-slate-600">Rekening Pengirim</p>
                                                <p data-f="sender-name" class="mt-1 text-[12px] font-bold text-slate-900">{{ $first['sender'][0] }}</p>
                                                <p data-f="sender-acc" class="mt-0.5 text-[10px] leading-snug text-slate-600">{{ $first['sender'][1] }}</p>
                                            </div>
                                            <div class="rounded-2xl {{ $soft }} p-3.5">
                                                <p class="text-[9px] font-semibold text-slate-600">Rekening Tujuan</p>
                                                <p data-f="dest-name" class="mt-1 text-[12px] font-bold leading-snug text-slate-900">{{ $first['dest'][0] }}</p>
                                                <p data-f="dest-acc" class="mt-0.5 text-[10px] leading-snug text-slate-600">{{ $first['dest'][1] }}</p>
                                            </div>
                                        </div>

                                        {{-- Kecocokan nominal --}}
                                        <div id="match-box" class="flex items-center gap-3 rounded-2xl bg-[#D5F0DC] p-3.5 transition-colors duration-300">
                                            <span id="match-icon" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#0B3A22] text-white transition-colors duration-300">{!! $ic($p['checkc'], 'h-5 w-5', '2') !!}</span>
                                            <div class="min-w-0">
                                                <p class="flex flex-wrap items-center gap-2">
                                                    <b data-f="amount" class="text-[20px] font-bold leading-tight text-[#0B3A22]">Rp 170.412</b>
                                                    <span id="match-chip" class="rounded-md bg-white px-2 py-0.5 text-[9px] font-bold text-[#0B3A22] ring-1 ring-[#0B3A22]/25">Kode Unik: <span data-f="unik">412</span> <span data-f="match">COCOK</span></span>
                                                </p>
                                                <p data-f="match-text" class="mt-1 text-[10px] leading-snug text-[#0B3A22]/80">Nominal mutasi rekening bank terdeteksi sesuai tanpa selisih.</p>
                                            </div>
                                        </div>

                                        {{-- Aksi --}}
                                        <div class="flex items-stretch gap-3 pt-1">
                                            <button type="button" id="btn-reject" class="flex w-[108px] shrink-0 items-center justify-center gap-1.5 rounded-[28px] {{ $lav }} px-3 py-4 text-center text-[11px] font-bold leading-tight text-[#B42318] transition hover:bg-[#cfd9f7] active:scale-95">
                                                {!! $ic($p['x'], 'h-3.5 w-3.5 shrink-0', '2.2') !!} <span id="reject-label">Tolak<br>Pembayaran</span>
                                            </button>
                                            <button type="button" id="btn-approve" class="flex flex-1 items-center justify-center gap-2.5 rounded-[28px] bg-[#0B3A22] px-4 py-4 text-center text-[12px] font-bold leading-tight text-white shadow-[0_12px_24px_-8px_rgba(11,58,34,0.55)] transition hover:bg-[#124c2f] active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-60">
                                                <span id="approve-qr">{!! $ic($p['qr'], 'h-4 w-4 shrink-0', '2') !!}</span>
                                                <svg id="approve-spin" class="hidden h-4 w-4 shrink-0 animate-spin" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 3a9 9 0 019 9"/></svg>
                                                <span id="approve-label">Terima &amp; Terbitkan<br>E-Ticket QR</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="review-empty" class="hidden py-20 text-center">
                                <span class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full {{ $mint }} text-[#0B3A22]">{!! $ic($p['check'], 'h-6 w-6', '2.4') !!}</span>
                                <p class="text-[14px] font-bold text-[#0B3A22]">Semua bukti transfer sudah diproses</p>
                                <p class="mt-1 text-[11px] text-slate-600">Struk baru akan muncul otomatis di sini.</p>
                            </div>
                        </section>

                        {{-- WhatsApp Gateway --}}
                        <section class="flex items-center justify-between gap-4 rounded-2xl {{ $soft }} p-4">
                            <div class="flex items-start gap-3">
                                <span class="mt-0.5 text-[#8B5E34]">{!! $ic($p['bolt'], 'h-5 w-5', '2') !!}</span>
                                <div>
                                    <p class="text-[12px] font-bold text-slate-900">Integrasi WhatsApp Gateway V2 Siap</p>
                                    <p class="mt-0.5 text-[10px] leading-snug text-slate-600">Tiket barcode terenkripsi akan langsung terkirim ke WhatsApp pemesan saat disetujui.</p>
                                </div>
                            </div>
                            <p class="shrink-0 text-[10px] leading-snug text-slate-600">Latensi:<br><b data-latency class="text-[12px] text-slate-900">142ms</b></p>
                        </section>
                    </div>

                    {{-- Right Column: antrean --}}
                    <aside>
                        <div class="mb-3 flex items-start justify-between px-1">
                            <h2 class="text-[11px] font-bold uppercase leading-snug tracking-wide text-slate-900">Antrean Verifikasi<br>Lainnya</h2>
                            <span class="text-[9px] font-medium text-slate-600">Auto-refresh:<br><span data-refresh-in>15</span>d</span>
                        </div>
                        <div id="queue-list" class="space-y-3"></div>
                        <p id="queue-empty" class="hidden rounded-xl bg-white p-6 text-center text-[11px] font-medium text-slate-500 shadow-[0_2px_10px_rgba(15,69,39,0.06)]">Tidak ada antrean yang cocok.</p>
                    </aside>
                </div>

                {{-- Riwayat Transaksi --}}
                <section class="{{ $card }} p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h2 class="text-[18px] font-bold text-[#0B3A22]">Riwayat Transaksi Sukses &amp; E-Ticket Terbit</h2>
                            <p class="mt-1 text-[11px] leading-relaxed text-slate-600">5 aktivitas pemesanan terakhir yang telah lolos verifikasi dan dikirimkan ke gerbang otomatis.</p>
                        </div>
                        <div class="flex shrink-0 items-center gap-3">
                            <button type="button" id="btn-export" class="flex items-center gap-2 rounded-lg {{ $lav }} px-4 py-2 text-[11px] font-semibold text-slate-800 transition hover:bg-[#cfd9f7] active:scale-95">
                                {!! $ic($p['download'], 'h-4 w-4') !!} Unduh XLS
                            </button>
                            <a href="#" class="flex items-center gap-2 rounded-lg bg-[#0B3A22] px-4 py-2 text-[11px] font-bold text-white shadow-sm transition hover:bg-[#124c2f] active:scale-95">
                                {!! $ic($p['history'], 'h-4 w-4') !!} Lihat Semua Arsip
                            </a>
                        </div>
                    </div>

                    <div class="mt-5 overflow-x-auto">
                        <div class="min-w-[760px]">
                            <div class="grid grid-cols-[100px_130px_minmax(0,1.5fr)_90px_120px_150px_130px] items-center gap-2 rounded-xl bg-[#E8ECFB] px-4 py-3 text-[9px] font-bold uppercase leading-snug tracking-wide text-slate-700">
                                <span>Waktu<br>Transaksi</span><span>Kode Booking</span><span>Customer &amp; Unit</span><span>Metode<br>Bayar</span><span>Nominal</span><span>Status &amp; Dispatch</span><span class="text-right">Opsi</span>
                            </div>
                            <div id="history-list"></div>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-between gap-4">
                        <p class="text-[10px] text-slate-600">Menampilkan <span data-shown>5</span> dari <span data-c="verified">86</span> transaksi berhasil hari ini</p>
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
            </div>
        </main>
    </div>

    {{-- Toast --}}
    <div id="toast" role="status" class="pointer-events-none fixed right-6 top-20 z-50 max-w-sm translate-y-[-8px] rounded-xl bg-[#0B3A22] px-4 py-3 text-[12px] font-semibold text-white opacity-0 shadow-[0_10px_24px_-8px_rgba(11,46,34,0.55)] transition duration-300"></div>
    </div>

@endsection

@push('scripts')
<style>
    @keyframes ocr-scan { 0% { transform: translateY(-20%); } 100% { transform: translateY(1000%); } }
    @keyframes pop-in { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: none; } }
    @keyframes row-flash { from { background-color: #D5F0DC; } to { background-color: transparent; } }
    .ocr-line { animation: ocr-scan 2.8s linear infinite; }
    .pop-in { animation: pop-in .4s ease-out both; }
    .row-flash { animation: row-flash 2.4s ease-out both; }
    @media (prefers-reduced-motion: reduce) { .ocr-line, .pop-in, .row-flash { animation: none; } }
</style>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const LIVE_DEMO = true;      // ganti dengan polling / Laravel Echo untuk data asli
        const REFRESH_EVERY = 15;    // detik
        const MAX_QUEUE = 7;

        const $ = (s, r = document) => r.querySelector(s);
        const $$ = (s, r = document) => [...r.querySelectorAll(s)];
        const ICON = @json($p);
        const ic = (n, c = 'h-4 w-4', sw = '1.8') => `<svg class="${c}" fill="none" stroke="currentColor" stroke-width="${sw}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="${ICON[n]}"/></svg>`;
        const rp = (n) => 'Rp ' + Math.round(n).toLocaleString('id-ID');
        const pad = (n) => String(n).padStart(2, '0');
        const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
        const rand = (a, b) => Math.floor(Math.random() * (b - a + 1)) + a;
        const pick = (arr) => arr[rand(0, arr.length - 1)];
        const sleep = (ms) => new Promise((r) => setTimeout(r, ms));
        const wib = (ms) => { const d = new Date(ms + 7 * 3600e3); return pad(d.getUTCHours()) + ':' + pad(d.getUTCMinutes()); };
        const mins = (at) => Math.floor((Date.now() - at) / 60000);
        const ago = (at) => { const m = mins(at); return m < 1 ? 'baru saja' : m < 60 ? m + ' mnt lalu' : Math.floor(m / 60) + ' jam lalu'; };
        const agoLong = (at) => { const m = mins(at); return m < 1 ? 'baru saja' : m < 60 ? m + ' menit lalu' : Math.floor(m / 60) + ' jam lalu'; };

        // ---------- State ----------
        const state = {
            queue: @json($queue).map((i) => ({ ...i, at: Date.now() - i.mins * 60000 })),
            history: @json($history),
            active: @json($first['id']),
            bank: 'all', status: 'waiting', q: '',
            c: { pending: 14, verified: 86, rejected: 2 },
            busy: false, page: 1,
        };
        let seq = 13;

        // ---------- Toast ----------
        let toastTimer;
        const toast = (msg) => {
            const el = $('#toast');
            el.textContent = msg;
            el.classList.remove('opacity-0', 'translate-y-[-8px]');
            el.classList.add('opacity-100', 'translate-y-0');
            clearTimeout(toastTimer);
            toastTimer = setTimeout(() => {
                el.classList.add('opacity-0', 'translate-y-[-8px]');
                el.classList.remove('opacity-100', 'translate-y-0');
            }, 2800);
        };

        // ---------- Counter ----------
        const animate = (el, to, dur = 800) => {
            const from = Number(el.dataset.val ?? to);
            el.dataset.val = to;
            if (reduce || from === to) { el.textContent = Math.round(to).toLocaleString('id-ID'); return; }
            const t0 = performance.now();
            const tick = (now) => {
                const p = Math.min((now - t0) / dur, 1);
                el.textContent = Math.round(from + (to - from) * (1 - Math.pow(1 - p, 3))).toLocaleString('id-ID');
                if (p < 1) requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
        };
        const setCount = (name, v) => { state.c[name] = v; $$(`[data-c="${name}"]`).forEach((el) => animate(el, v)); };
        Object.keys(state.c).forEach((k) => $$(`[data-c="${k}"]`).forEach((el) => (el.dataset.val = 0)));
        Object.entries(state.c).forEach(([k, v]) => setCount(k, v));

        // ---------- Antrean ----------
        const tone = {
            mint: 'bg-[#CDEFD5] text-[#0B3A22]', peach: 'bg-[#FBD9B0] text-[#6B4520]',
            lav: 'bg-[#DDE6FB] text-[#2F3F86]', red: 'bg-[#FBD5D5] text-[#B42318]',
        };
        const visible = () => state.queue.filter((i) =>
            (state.bank === 'all' || i.method === state.bank) &&
            (state.status === 'waiting' || i.problem) &&
            (!state.q || (i.id + ' ' + i.name + ' ' + i.amount + ' ' + i.sender[1]).toLowerCase().includes(state.q)));

        const queueCard = (it) => {
            const on = it.id === state.active;
            let actions = '';
            if (!on && it.act === 'reupload') {
                actions = `<div class="mt-3 flex items-start gap-2 rounded-lg bg-[#FDE8E8] p-2.5 text-[10px] leading-snug text-[#B42318]">${ic('alert', 'mt-0.5 h-3.5 w-3.5 shrink-0', '2')}<span>${esc(it.note)}</span></div>
                    <button type="button" data-do="reupload" data-id="${it.id}" class="mt-2 flex w-full items-center justify-center gap-1.5 rounded-lg bg-[#DDE6FB] py-2 text-[10px] font-bold text-[#0B3A22] transition hover:bg-[#cfd9f7] active:scale-95">${ic('pin', 'h-3.5 w-3.5', '2')} Minta Upload Ulang</button>`;
            } else if (!on && it.act === 'proof') {
                actions = `<button type="button" data-do="proof" data-id="${it.id}" class="mt-3 flex w-full items-center justify-center gap-1.5 rounded-lg bg-[#DDE6FB] py-2 text-[10px] font-bold text-[#0B3A22] transition hover:bg-[#cfd9f7] active:scale-95">${ic('external', 'h-3.5 w-3.5', '2')} Buka Bukti Transfer</button>`;
            } else if (!on) {
                actions = `<div class="mt-3 grid grid-cols-2 gap-2">
                    <button type="button" data-do="preview" data-id="${it.id}" class="flex items-center justify-center gap-1.5 rounded-lg bg-[#DDE6FB] py-2 text-[10px] font-bold text-[#0B3A22] transition hover:bg-[#cfd9f7] active:scale-95">${ic('eye', 'h-3.5 w-3.5', '2')} Pratinjau</button>
                    <button type="button" data-do="quick" data-id="${it.id}" class="flex items-center justify-center gap-1.5 rounded-lg bg-[#0B3A22] py-2 text-[10px] font-bold text-white transition hover:bg-[#124c2f] active:scale-95">${ic('bolt', 'h-3.5 w-3.5', '2')} Cepat Verifikasi</button>
                </div>`;
            }
            const pill = on ? `<span class="shrink-0 rounded-full ${tone.mint} px-2.5 py-1 text-[9px] font-bold">Sedang Ditinjau</span>`
                            : `<span class="shrink-0 rounded-full ${tone[it.tone]} px-2.5 py-1 text-[9px] font-bold">${esc(it.tag)}</span>`;
            const meta = on ? '' : `<span class="h-1.5 w-1.5 rounded-full ${it.dot}"></span><span>${ago(it.at)}</span>`;
            const sub = on ? `${esc(it.unit)} • ${esc(it.pay)}` : esc(it.unit);
            return `<div data-q="${it.id}" class="${it.fresh ? 'pop-in ' : ''}cursor-pointer rounded-xl bg-white p-3.5 shadow-[0_2px_10px_rgba(15,69,39,0.06)] transition hover:shadow-[0_6px_18px_rgba(15,69,39,0.12)] ${on ? 'border-l-4 border-[#0B3A22]' : ''}">
                <div class="flex items-center justify-between gap-2">
                    <p class="flex items-center gap-1.5 text-[9px] font-semibold leading-snug text-slate-600"><span>${it.id}</span>${meta}</p>
                    ${pill}
                </div>
                <div class="mt-2 flex items-end justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-[12px] font-bold text-slate-900">${esc(it.name)}</p>
                        <p class="mt-0.5 text-[10px] leading-snug text-slate-600">${sub}</p>
                    </div>
                    <div class="shrink-0 text-right">
                        <p class="text-[13px] font-bold text-slate-900">${rp(it.amount)}</p>
                        ${on ? '' : `<p class="mt-0.5 text-[9px] leading-snug text-slate-500">${esc(it.pay)}</p>`}
                    </div>
                </div>
                ${actions}
            </div>`;
        };

        const renderQueue = () => {
            const list = visible();
            $('#queue-list').innerHTML = list.map(queueCard).join('');
            $('#queue-empty').classList.toggle('hidden', list.length > 0);
            state.queue.forEach((i) => (i.fresh = false));
        };

        // ---------- Panel utama ----------
        const countUp = (el, to) => {
            if (reduce) { el.textContent = to; return; }
            const t0 = performance.now();
            const tick = (now) => {
                const p = Math.min((now - t0) / 700, 1);
                el.textContent = Math.round(to * (1 - Math.pow(1 - p, 3)));
                if (p < 1) requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
        };
        const toggle = (el, list, on) => list.forEach((c) => el.classList.toggle(c, on));
        const current = () => state.queue.find((i) => i.id === state.active);

        const fillMain = (it) => {
            const set = (k, v, html) => $$(`[data-f="${k}"]`).forEach((el) => (html ? (el.innerHTML = v) : (el.textContent = v)));
            set('id', it.id); set('channel', it.channel); set('file', it.file);
            set('name', it.name); set('email', it.email); set('phone', it.phone);
            set('service', it.service.map(esc).join('<br>'), true);
            set('session', it.session);
            set('sender-name', it.sender[0]); set('sender-acc', it.sender[1]);
            set('dest-name', it.dest[0]); set('dest-acc', it.dest[1]);
            set('amount', rp(it.amount));
            set('unik', pad(it.amount % 1000).padStart(3, '0'));
            $('[data-f="proof"]').src = it.proof;
            countUp($('[data-f="ocr"]'), it.ocr);
            updateUploaded();

            const bad = it.problem;
            set('match', bad ? 'PERIKSA' : 'COCOK');
            set('match-text', bad ? 'Nominal tidak dapat dipastikan oleh OCR. Lakukan audit visual sebelum menerbitkan E-Ticket.' : 'Nominal mutasi rekening bank terdeteksi sesuai tanpa selisih.');
            toggle($('#match-box'), ['bg-[#FDE8E8]'], bad);
            toggle($('#match-box'), ['bg-[#D5F0DC]'], !bad);
            toggle($('#match-icon'), ['bg-[#B42318]'], bad);
            toggle($('#match-icon'), ['bg-[#0B3A22]'], !bad);
            toggle($('[data-f="amount"]'), ['text-[#B42318]'], bad);
            toggle($('[data-f="amount"]'), ['text-[#0B3A22]'], !bad);
            toggle($('#match-chip'), ['text-[#B42318]', 'ring-[#B42318]/25'], bad);
            toggle($('#match-chip'), ['text-[#0B3A22]', 'ring-[#0B3A22]/25'], !bad);
            $('#btn-approve').disabled = bad;
            resetReject();
        };
        const updateUploaded = () => {
            const it = current();
            if (it) $('[data-f="uploaded"]').textContent = `• Diunggah ${agoLong(it.at)} (${wib(it.at)} WIB)`;
        };

        const showMain = (animateIn = true) => {
            const it = current();
            $('#review-body').classList.toggle('hidden', !it);
            $('#review-empty').classList.toggle('hidden', !!it);
            if (!it) return;
            const body = $('#review-body');
            if (animateIn && !reduce) {
                body.classList.add('opacity-0', 'translate-y-1');
                setTimeout(() => { fillMain(it); body.classList.remove('opacity-0', 'translate-y-1'); }, 180);
            } else fillMain(it);
        };

        const setActive = (id) => { state.active = id; renderQueue(); showMain(); };

        // ---------- Riwayat ----------
        const histRow = (h) => `<div class="${h.fresh ? 'row-flash ' : ''}grid grid-cols-[100px_130px_minmax(0,1.5fr)_90px_120px_150px_130px] items-center gap-2 border-b border-slate-100 px-4 py-4 last:border-0">
            <span class="font-mono text-[10px] text-slate-500">${h.t} WIB</span>
            <span class="break-words text-[11px] font-semibold text-slate-900">${h.id}</span>
            <span><b class="block text-[11px] text-slate-900">${esc(h.name)}</b><span class="block text-[10px] leading-snug text-slate-600">${esc(h.unit)}</span></span>
            <span><span class="inline-block rounded-lg bg-[#DDE6FB] px-3 py-1.5 text-[10px] font-bold text-slate-800">${esc(h.method)}</span></span>
            <b class="text-[12px] text-slate-900">${rp(h.amount)}</b>
            <span><span class="inline-flex items-center gap-1.5 rounded-xl bg-[#CDEFD5] px-3 py-1.5 text-[9px] font-bold leading-tight text-[#0B3A22]">${ic('mail', 'h-3 w-3 shrink-0', '2')}<span>Lunas • QR<br>WA &amp; Email</span></span></span>
            <span class="text-right"><button type="button" data-detail="${h.id}" class="rounded-lg bg-[#DDE6FB] px-3 py-2 text-center text-[9px] font-bold leading-tight text-slate-800 transition hover:bg-[#cfd9f7] active:scale-95">Lihat Detail &amp;<br>E-Ticket</button></span>
        </div>`;
        const renderHistory = () => {
            $('#history-list').innerHTML = state.history.map(histRow).join('');
            $('[data-shown]').textContent = state.history.length;
            state.history.forEach((h) => (h.fresh = false));
        };

        // ---------- Aksi: terima / tolak ----------
        const removeItem = (id) => {
            const idx = state.queue.findIndex((i) => i.id === id);
            if (idx < 0) return;
            const was = state.active === id;
            state.queue.splice(idx, 1);
            if (was) state.active = state.queue[0] ? state.queue[0].id : null;
            renderQueue();
            if (was) showMain();
        };

        const setApproveBusy = (busy) => {
            $('#btn-approve').disabled = busy;
            $('#approve-qr').classList.toggle('hidden', busy);
            $('#approve-spin').classList.toggle('hidden', !busy);
            $('#approve-label').innerHTML = busy ? 'Menerbitkan<br>E-Ticket...' : 'Terima &amp; Terbitkan<br>E-Ticket QR';
        };

        const approve = async (id, quick = false) => {
            if (state.busy) return;
            const it = state.queue.find((i) => i.id === id);
            if (!it) return;
            if (it.problem) return toast('Struk buram perlu audit visual sebelum diterima.');
            state.busy = true;
            if (id === state.active) setApproveBusy(true);
            await sleep(quick ? 700 : 1400);

            state.history.unshift({ t: wib(Date.now()), id: it.id, name: it.name, unit: it.unit, method: it.method, amount: it.amount, fresh: true });
            state.history = state.history.slice(0, 5);
            removeItem(id);
            setCount('pending', Math.max(0, state.c.pending - 1));
            setCount('verified', state.c.verified + 1);
            renderHistory();
            setApproveBusy(false);
            if (current()) $('#btn-approve').disabled = current().problem;
            state.busy = false;
            toast(`E-Ticket QR ${it.name} terkirim ke WhatsApp & Email`);
        };

        let rejectTimer, rejectArmed = false;
        const resetReject = () => { rejectArmed = false; clearTimeout(rejectTimer); $('#reject-label').innerHTML = 'Tolak<br>Pembayaran'; };
        const reject = () => {
            const it = current();
            if (!it || state.busy) return;
            if (!rejectArmed) {
                rejectArmed = true;
                $('#reject-label').innerHTML = 'Yakin<br>Tolak?';
                rejectTimer = setTimeout(resetReject, 3000);
                return;
            }
            resetReject();
            removeItem(it.id);
            setCount('pending', Math.max(0, state.c.pending - 1));
            setCount('rejected', state.c.rejected + 1);
            toast(`Pembayaran ${it.name} ditolak. Pemesan diberi tahu via WhatsApp.`);
        };

        $('#btn-approve').addEventListener('click', () => state.active && approve(state.active));
        $('#btn-reject').addEventListener('click', reject);

        $('#queue-list').addEventListener('click', (e) => {
            const btn = e.target.closest('[data-do]');
            const card = e.target.closest('[data-q]');
            if (btn) {
                e.stopPropagation();
                const id = btn.dataset.id, it = state.queue.find((i) => i.id === id);
                if (btn.dataset.do === 'preview') setActive(id);
                if (btn.dataset.do === 'quick') approve(id, true);
                if (btn.dataset.do === 'proof' && it) window.open(it.proof, '_blank', 'noopener');
                if (btn.dataset.do === 'reupload' && it) {
                    removeItem(id);
                    setCount('pending', Math.max(0, state.c.pending - 1));
                    toast(`Permintaan upload ulang dikirim ke ${it.name} via WhatsApp`);
                }
                return;
            }
            if (card && card.dataset.q !== state.active) setActive(card.dataset.q);
        });

        // ---------- Filter ----------
        const on = ['bg-[#0B3A22]', 'text-white', 'shadow-sm'];
        const off = ['text-slate-700', 'hover:text-[#0B3A22]'];
        $$('[data-bank]').forEach((b) => b.addEventListener('click', () => {
            $$('[data-bank]').forEach((t) => { t.classList.remove(...on); t.classList.add(...off); });
            b.classList.remove(...off); b.classList.add(...on);
            state.bank = b.dataset.bank;
            renderQueue();
        }));
        $('#search').addEventListener('input', (e) => { state.q = e.target.value.trim().toLowerCase(); renderQueue(); });

        const menu = $('#status-menu');
        $('#status-btn').addEventListener('click', (e) => { e.stopPropagation(); menu.classList.toggle('hidden'); });
        document.addEventListener('click', () => menu.classList.add('hidden'));
        $$('[data-status]').forEach((b) => b.addEventListener('click', () => {
            state.status = b.dataset.status;
            $('#status-label').textContent = 'Status: ' + b.textContent.trim();
            menu.classList.add('hidden');
            renderQueue();
        }));

        // ---------- Struk masuk (real-time) ----------
        const names = ['Putri Maharani', 'Galih Pramudya', 'Intan Permata', 'Yoga Saputra', 'Melati Kusuma', 'Fikri Ramadhan', 'Anindya Putri', 'Teguh Santoso'];
        const services = [
            { unit: '2x Tiket Premier Onsen', service: ['2× Tiket', 'Premier Onsen'], session: 'Sesi Pagi (08:00 - 11:00)', base: 170000, tag: 'Tiket Reguler', tone: 'lav' },
            { unit: '3x Tiket Classic Hot Spring', service: ['3× Tiket', 'Classic Hot Spring'], session: 'Sesi Siang (13:00 - 16:00)', base: 180000, tag: 'Tiket Reguler', tone: 'lav' },
            { unit: 'Magnolia Cabin Suite (1 Malam)', service: ['1× Cabin', 'Magnolia Suite'], session: 'Check-in 14:00 WIB', base: 1040000, tag: 'Glamping Resort', tone: 'peach' },
            { unit: 'Cabin Shorts (3 Jam)', service: ['1× Cabin', 'Shorts (3 Jam)'], session: 'Mulai 13:00 WIB', base: 340000, tag: 'Glamping Resort', tone: 'peach' },
        ];
        const banks = [
            { m: 'BCA', channel: 'BCA Virtual Account / Manual', pay: 'BCA Transfer' },
            { m: 'BNI', channel: 'BNI Virtual Account', pay: 'BNI Virtual Account' },
            { m: 'Mandiri', channel: 'Mandiri Transfer / Manual', pay: 'Mandiri Transfer' },
            { m: 'QRIS', channel: 'QRIS Dinamis', pay: 'QRIS' },
        ];

        const incoming = () => {
            if (state.queue.length >= MAX_QUEUE) return false;
            const n = pick(names), s = pick(services), b = pick(banks), code = rand(100, 999);
            const bad = Math.random() < 0.15;
            const id = 'JW-20260920-' + String(seq++).padStart(3, '0');
            state.queue.push({
                id, name: n, email: n.toLowerCase().replace(/\s+/g, '.') + '\u0040gmail.com', phone: '+62 8' + rand(11, 78) + '-' + rand(1000, 9999) + '-' + rand(1000, 9999),
                service: s.service, unit: s.unit, session: s.session, amount: s.base + code, method: b.m, channel: b.channel, pay: b.pay,
                sender: [n, b.m + ' • ' + rand(1000, 9999) + '-' + rand(1000, 9999) + '-' + rand(10, 99)],
                dest: ['Jiwanta Wisata Mandiri', b.m + ' • 4370-9008-21'],
                file: `bukti-transfer-${String(seq - 1).padStart(3, '0')}.jpg`, proof: @json($proof), ocr: bad ? rand(52, 68) : rand(94, 99),
                at: Date.now(), tag: bad ? 'Struk Buram' : s.tag, tone: bad ? 'red' : s.tone, dot: bad ? 'bg-red-600' : 'bg-slate-400',
                act: bad ? 'reupload' : pick(['quick', 'proof']), problem: bad, note: bad ? 'OCR gagal membaca 3 digit terakhir. Perlu audit visual staf.' : '', fresh: true,
            });
            if (!state.active) state.active = state.queue[state.queue.length - 1].id;
            setCount('pending', state.c.pending + 1);
            renderQueue();
            showMain(false);
            toast(`Struk baru masuk: ${n} (${rp(s.base + code)})`);
            return true;
        };

        // ---------- Siklus auto-refresh ----------
        let left = REFRESH_EVERY, cycles = 0;
        const cycle = (manual) => {
            cycles++;
            renderQueue();
            updateUploaded();
            if (manual) {
                if (!incoming()) toast('Antrean sudah yang terbaru.');
            } else if (LIVE_DEMO && cycles % 3 === 0) incoming();
        };
        setInterval(() => {
            left--;
            if (left <= 0) { cycle(false); left = REFRESH_EVERY; }
            $$('[data-refresh-in]').forEach((el) => (el.textContent = left));
        }, 1000);

        $('#refresh-btn').addEventListener('click', () => {
            const icon = $('#refresh-icon');
            if (icon.animate && !reduce) icon.animate([{ transform: 'rotate(0)' }, { transform: 'rotate(360deg)' }], { duration: 800, easing: 'ease-in-out' });
            left = REFRESH_EVERY;
            setTimeout(() => cycle(true), 600);
        });

        // Latensi gateway (simulasi)
        setInterval(() => {
            const ms = rand(118, 182);
            $$('[data-latency]').forEach((el) => {
                el.textContent = ms + 'ms';
                el.classList.toggle('text-[#8B5E34]', ms > 165);
                el.classList.toggle('text-slate-900', ms <= 165);
            });
        }, 2500);

        // ---------- Riwayat: ekspor & detail ----------
        $('#btn-export').addEventListener('click', () => {
            const rows = [['Waktu', 'Kode Booking', 'Customer', 'Unit', 'Metode', 'Nominal'], ...state.history.map((h) => [h.t + ' WIB', h.id, h.name, h.unit, h.method, h.amount])];
            const csv = '\ufeff' + rows.map((r) => r.map((v) => `"${String(v).replace(/"/g, '""')}"`).join(',')).join('\n');
            const a = Object.assign(document.createElement('a'), { href: URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8' })), download: 'riwayat-transaksi.csv' });
            document.body.appendChild(a); a.click(); a.remove();
            toast('Riwayat transaksi diunduh');
        });
        $('#history-list').addEventListener('click', (e) => {
            const b = e.target.closest('[data-detail]');
            if (b) toast('Membuka detail & E-Ticket ' + b.dataset.detail);
        });

        // Pagination (tampilan)
        const pgOn = ['bg-[#0B3A22]', 'text-white'];
        const pgOff = ['bg-[#E8ECFB]', 'text-slate-700', 'hover:bg-[#DDE3F8]'];
        const setPage = (p) => {
            state.page = p;
            $$('#pager [data-pg]').forEach((b) => {
                const act = b.dataset.pg === String(p);
                if (isNaN(Number(b.dataset.pg))) return;
                toggle(b, pgOn, act); toggle(b, pgOff, !act);
            });
        };
        $$('#pager [data-pg]').forEach((b) => b.addEventListener('click', () => {
            const v = b.dataset.pg;
            const next = v === 'prev' ? Math.max(1, state.page - 1) : v === 'next' ? Math.min(18, state.page + 1) : Number(v);
            if ([1, 2, 3, 18].includes(next)) setPage(next);
            else toast('Halaman ' + next);
        }));

        // ---------- Mulai ----------
        renderQueue();
        renderHistory();
        showMain(false);
    });
</script>
@endpush
