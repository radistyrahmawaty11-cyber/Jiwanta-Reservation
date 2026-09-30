@extends('layouts.dashboard')

@section('title', 'Dashboard Operasional - Jiwanta')

@section('content')

@php
    // ---------- Helper ----------
    $ic = fn (string $d, string $c = 'h-4 w-4') =>
        '<svg class="'.$c.'" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="'.$d.'"/></svg>';
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
        'wallet'    => 'M3 7h16a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7zM3 7l3-3h11v3M16 13.5h2',
        'bell'      => 'M6 8a6 6 0 0112 0c0 7 3 8 3 8H3s3-1 3-8M10 20a2 2 0 004 0',
        'download'  => 'M12 4v11m-4-4l4 4 4-4M5 20h14',
        'clock'     => 'M12 8v4l2 2M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'logout'    => 'M15 4h4a1 1 0 011 1v14a1 1 0 01-1 1h-4M10 16l-4-4 4-4M6 12h10',
        'snow'      => 'M12 3v18M4.5 7.5l15 9M4.5 16.5l15-9M9 4l3 2 3-2M9 20l3-2 3 2',
        'arrow'     => 'M5 12h14M13 6l6 6-6 6',
        'check'     => 'M5 13l4 4L19 7',
        'trend'     => 'M3 17l6-6 4 4 8-8M15 7h6v6',
        'plus'      => 'M12 5v14M5 12h14',
        'calendar'  => 'M8 3v3M16 3v3M4 9h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z',
        'sliders'   => 'M4 7h9M17 7h3M4 17h3M11 17h9M15 5v4M9 15v4',
        'bed'       => 'M3 18V7M3 14h18v4M21 14v-2a3 3 0 00-3-3h-7v5',
        'door'      => 'M6 21V4h9v17M4 21h16M12 12h.01',
        'idcard'    => 'M4 6h16v12H4zM8.5 11a1.5 1.5 0 100-3 1.5 1.5 0 000 3zM6 15c.5-1.5 4.5-1.5 5 0M14 10h4M14 13h3',
        'tree'      => 'M12 3l4 6h-2.5l3.5 5h-3l3 5H7l3-5H7l3.5-5H8l4-6z',
    ];

    $nav = [
        'Navigasi Utama' => [['Dashboard', 'dashboard', true, null], ['Verifikasi & Transaksi', 'receipt', false, 14]],
        'Manajemen Resort' => [['Kelola Tiket', 'ticket', false, null], ['Kelola Cabin Suite', 'cabin', false, null], ['Kelola Fasilitas Resort', 'pool', false, null]],
        'Administrasi & Sistem' => [['Manajemen Pengguna', 'users', false, null], ['Laporan & Export', 'chart', false, null], ['Pengaturan Profil', 'gear', false, null]],
    ];

    $mint = 'bg-[#CDEFD5]';
    $peach = 'bg-[#FBD9B0]';
    $lav = 'bg-[#DDE6FB]';

    $queue = [
        ['AM', $mint, 'Aris Munandar', 'JW-TKT-089', 'bg-[#DDE2F5]', ['BCA Transfer', 'Manual'], ['3 Tiket Kolam', 'Classic'], 170412, 3],
        ['CK', $peach, 'Citra Kirana', 'JW-CAB-014', 'bg-[#F5C89A]', ['Mandiri', 'VA'], ['Pinus Suite Cabin (1', 'Malam)'], 2450000, 0],
        ['DA', $mint, 'Dini Anggraini', 'JW-TKT-092', 'bg-[#DDE2F5]', ['BCA Transfer', 'Manual'], ['2 Tiket', 'Belerang', 'Premier'], 240000, 2],
    ];

    $status = [
        'Check-in' => [$mint, 'bg-[#0B3A22]'],
        'Lunas'    => [$peach, 'bg-[#C2762B]'],
        'Menunggu' => [$lav, 'bg-slate-500'],
    ];

    $reservations = [
        ['tiket', ['#JW-', '8821'], ['Bambang', 'Wicaksono'], ['+62 812-', '4421-900'], ['Kolam Air', 'Panas', 'Classic'], ['20 Sep', '2026,', '09:00 WIB'], 'Check-in'],
        ['cabin', ['#JW-', '8820'], ['Larasati', 'Dewi'], ['+62 857-', '1190-332'], ['Magnolia', 'Cabin', 'Suite'], ['20 - 21 Sep', '(1 Malam)'], 'Lunas'],
        ['tiket', ['#JW-', '8819'], ['Reza', 'Pratama'], ['+62 821-', '9988-121'], ['Belerang', 'Premier', 'VIP'], ['20 Sep', '2026,', '14:00 WIB'], 'Menunggu'],
        ['cabin', ['#JW-', '8818'], ['Nabila', 'Syakieb'], ['+62 813-', '7722-109'], ['Pinus', 'Cabin', 'Suite'], ['20 - 22', 'Sep (2', 'Malam)'], 'Check-in'],
        ['tiket', ['#JW-', '8817'], ['Fajar', 'Nugraha'], ['+62 878-', '3341-002'], ['Kolam Air', 'Panas', 'Classic'], ['20 Sep', '2026,', '11:00 WIB'], 'Lunas'],
    ];

    $tabs = [['all', 'Semua'], ['tiket', 'Kolam<br>Tiket'], ['cabin', 'Cabin<br>Suite']];

    $card = 'rounded-2xl bg-white shadow-sm border border-slate-200';
    $cap = 'text-[9px] font-bold uppercase leading-snug tracking-wide text-slate-600';
@endphp

    {{-- ================= LAYOUT CONTAINER ================= --}}
    <div class="min-w-full bg-slate-50 flex">

    {{-- ================= SIDEBAR ================= --}}
    <aside class="w-[240px] shrink-0 bg-white border-r border-slate-200 flex flex-col">
        <div class="px-5 pt-6 pb-4">
            <div class="flex items-center gap-3">
                <span class="flex h-[26px] w-[26px] items-center justify-center rounded-lg bg-[#0B3A22] text-[#B5F0BE]">{!! $ic($p['tree'], 'h-4 w-4') !!}</span>
                <div>
                    <p class="text-[13px] font-bold leading-tight tracking-wide">JIWANTA</p>
                    <p class="text-[8px] font-semibold uppercase leading-tight tracking-wider text-slate-600">Ciwidey Resort</p>
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
                                <a href="#" class="flex h-[36px] items-center gap-3 rounded-lg px-3 text-[13px] font-medium transition {{ $active ? 'bg-[#0B3A22] text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                                    {!! $ic($p[$icon], 'h-4 w-4 shrink-0') !!}
                                    <span class="flex-1">{{ $text }}</span>
                                    @if ($badge)
                                        <span class="rounded-full {{ $peach }} px-2 py-0.5 text-[10px] font-bold text-[#6B4520]">{{ $badge }}</span>
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
            <button type="button" class="flex w-full items-center justify-center gap-2 rounded-lg bg-red-50 py-2.5 text-[11px] font-bold text-red-700 transition hover:bg-red-100">
                {!! $ic($p['logout'], 'h-4 w-4') !!} Keluar Sistem
            </button>
        </div>
    </aside>

    {{-- ================= MAIN CONTENT ================= --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Topbar --}}
        <header class="h-[60px] bg-white border-b border-slate-200 px-6 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <p class="text-[11px] text-slate-600">Sistem Jiwanta</p>
                <span class="text-slate-400">/</span>
                <b class="text-[11px] font-semibold text-[#0B3A22]">Panel Kendali Utama</b>
            </div>
            <div class="flex items-center gap-3">
                <span class="flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1.5 text-[10px] font-medium">{!! $ic($p['snow'], 'h-3.5 w-3.5') !!} Ciwidey 18°C Kabut Sejuk</span>
                <span class="flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-[10px] font-medium text-emerald-700">
                    <span class="relative flex h-2 w-2"><span class="absolute h-full w-full animate-ping rounded-full bg-emerald-600 opacity-60"></span><span class="relative h-2 w-2 rounded-full bg-emerald-600"></span></span>
                    Gate Turnstile Online
                </span>
                <button type="button" class="relative h-10 w-10 flex items-center justify-center rounded-full bg-slate-100 transition hover:bg-slate-200" aria-label="Notifikasi">
                    {!! $ic($p['bell'], 'h-5 w-5') !!}
                    <span class="absolute right-2 top-2 h-2.5 w-2.5 rounded-full bg-red-600 border-2 border-white"></span>
                </button>
                <div class="flex items-center gap-3 pl-3 border-l border-slate-200">
                    <img src="{{ asset('images/avatar.jpg') }}" alt="Profil" class="h-10 w-10 rounded-full bg-[#C9B99A] object-cover border-2 border-white shadow-sm">
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
                <div class="flex items-start justify-between">
                    <div class="max-w-xl">
                        <p class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-wide text-[#8B5E34] mb-1">{!! $ic($p['tree'], 'h-3 w-3') !!} Jiwanta Highland Sanctuary &amp; Hot Spring</p>
                        <h1 class="text-[32px] font-bold leading-tight text-slate-900">Dashboard Operasional Utama</h1>
                        <p class="mt-2 text-[13px] leading-relaxed text-slate-600">Pantau pendapatan harian, reservasi aktif, dan status gerbang Ciwidey secara langsung.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="flex items-center gap-2 rounded-lg bg-white border border-slate-200 px-4 py-2 text-[11px] font-medium shadow-sm">
                            {!! $ic($p['calendar'], 'h-4 w-4') !!} Hari ini: 20 Sep 2026 <span class="h-1.5 w-1.5 rounded-full bg-[#C2762B]"></span>
                        </span>
                        <button type="button" class="flex items-center gap-2 rounded-lg bg-slate-100 px-4 py-2 text-[11px] font-medium transition hover:bg-slate-200">
                            {!! $ic($p['download'], 'h-4 w-4') !!} Unduh Laporan Ringkas
                        </button>
                        <button type="button" class="flex items-center gap-2 rounded-lg bg-[#0B3A22] px-5 py-2 text-[11px] font-bold text-white transition hover:bg-[#124c2f] shadow-sm">
                            {!! $ic($p['plus'], 'h-4 w-4') !!} Reservasi Walk-in
                        </button>
                    </div>
                </div>

                {{-- Stats Cards --}}
                <div class="grid grid-cols-4 gap-4">
                    <div class="{{ $card }} p-5">
                        <div class="flex items-start justify-between mb-3">
                            <p class="{{ $cap }}">Pendapatan Hari Ini</p>
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $mint }}">{!! $ic($p['wallet'], 'h-5 w-5') !!}</span>
                        </div>
                        <p class="text-[26px] font-bold leading-tight text-slate-900">Rp <span data-revenue>14.850.000</span></p>
                        <p class="mt-2 flex items-center gap-2 text-[11px]">
                            <b class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-0.5 text-emerald-700">{!! $ic($p['trend'], 'h-3 w-3') !!}+8.5%</b>
                            <span class="text-slate-600">dibandingkan kemarin</span>
                        </p>
                    </div>

                    <div class="{{ $card }} p-5">
                        <div class="flex items-start justify-between mb-3">
                            <p class="{{ $cap }}">Tiket Terjual Hari Ini</p>
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $lav }}">{!! $ic($p['ticket'], 'h-5 w-5') !!}</span>
                        </div>
                        <p class="text-[26px] font-bold leading-tight text-slate-900"><span data-sold>185</span> <span class="text-[15px] font-normal text-slate-500">/ 300 Tiket</span></p>
                        <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-100">
                            <div data-sold-bar style="width: 61%" class="h-full rounded-full bg-[#0B3A22] transition-all duration-1000"></div>
                        </div>
                        <p class="mt-2 flex justify-between text-[10px] font-bold"><span data-sold-pct>61% Terisi</span><span data-sold-left class="text-slate-600">Sisa 115 Slot</span></p>
                    </div>

                    <div class="{{ $card }} p-5">
                        <div class="flex items-start justify-between mb-3">
                            <p class="{{ $cap }}">Okupansi Kabin</p>
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $peach }} text-[#8B5E34]">{!! $ic($p['cabin'], 'h-5 w-5') !!}</span>
                        </div>
                        <p class="text-[26px] font-bold leading-tight text-slate-900">3 / 3 <span class="text-[15px] font-normal text-slate-500">Terisi</span></p>
                        <p class="mt-3 inline-flex items-center gap-2 rounded-lg {{ $peach }} px-3 py-1.5 text-[10px] font-bold text-[#6B4520]">
                            <span class="h-1.5 w-1.5 rounded-full bg-[#6B4520]"></span>100% Penuh • High Demand
                        </p>
                    </div>

                    <div class="{{ $card }} p-5">
                        <div class="flex items-start justify-between mb-3">
                            <p class="{{ $cap }}">Perlu Verifikasi Cepat</p>
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $mint }}">{!! $ic($p['receipt'], 'h-5 w-5') !!}</span>
                        </div>
                        <p class="text-[26px] font-bold leading-tight"><span data-batch class="text-[#8B5E34]">4</span> <span class="text-[15px] font-normal text-slate-500">Struk Baru</span></p>
                        <p class="mt-2 flex items-center gap-2 text-[11px] text-slate-600">{!! $ic($p['clock'], 'h-3.5 w-3.5') !!} Rata-rata respons: &lt; 5 menit</p>
                    </div>
                </div>

                {{-- Main Content Grid --}}
                <div class="grid grid-cols-[1fr_320px] gap-6">

                    {{-- Left Column --}}
                    <div class="space-y-6">

                        {{-- Queue Section --}}
                        <section class="{{ $card }}">
                            <div class="p-5 border-b border-slate-100">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start gap-3">
                                        <span class="mt-1 h-5 w-1.5 shrink-0 rounded-full bg-[#7A5230]"></span>
                                        <div>
                                            <h2 class="text-[16px] font-bold text-slate-900">Antrean Verifikasi Cepat</h2>
                                            <p class="mt-1 text-[11px] leading-relaxed text-slate-600">Struk transfer manual masuk yang membutuhkan validasi admin</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <span class="rounded-lg {{ $peach }} px-3 py-1.5 text-center text-[10px] font-bold text-[#6B4520]"><span data-batch>4</span> Menunggu</span>
                                        <a href="#" class="text-[11px] font-semibold text-slate-700 hover:text-[#0B3A22]">Lihat Semua (<span data-total>14</span>)</a>
                                        <a href="#" aria-label="Lihat semua" class="text-slate-400 hover:text-slate-600">{!! $ic($p['arrow'], 'h-4 w-4') !!}</a>
                                    </div>
                                </div>
                            </div>

                            <div id="queue" class="p-5 space-y-3">
                                @foreach ($queue as [$ini, $avBg, $name, $code, $codeBg, $method, $item, $amount, $tix])
                                    <div data-queue-row data-amount="{{ $amount }}" data-tickets="{{ $tix }}" class="flex items-center gap-4 rounded-xl bg-slate-50 p-4 transition-all hover:bg-slate-100">
                                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg {{ $avBg }} text-[14px] font-bold">{{ $ini }}</span>
                                        <div class="min-w-0 flex-1">
                                            <p class="flex items-center gap-2 text-[13px] font-bold">
                                                {{ $name }}
                                                <span class="rounded-md {{ $codeBg }} px-2 py-0.5 text-[9px] font-bold">{{ $code }}</span>
                                            </p>
                                            <p class="mt-1 flex items-center gap-3 text-[11px] text-slate-600">
                                                <span>{!! $br($method) !!}</span>
                                                <span class="h-1 w-1 shrink-0 rounded-full bg-slate-400"></span>
                                                <span>{!! $br($item) !!}</span>
                                                <span class="h-1 w-1 shrink-0 rounded-full bg-slate-400"></span>
                                                <b class="text-slate-900">Rp {{ number_format($amount, 0, ',', '.') }}</b>
                                            </p>
                                        </div>
                                        <button type="button" data-action="reject" class="rounded-lg bg-slate-200 px-4 py-2 text-[11px] font-semibold transition hover:bg-slate-300">Tolak</button>
                                        <button type="button" data-action="verify" class="flex items-center gap-2 rounded-lg bg-[#0B3A22] px-4 py-2 text-[11px] font-bold text-white transition hover:bg-[#124c2f]">
                                            {!! $ic($p['check'], 'h-4 w-4') !!} Verifikasi
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                            <p id="queue-empty" class="hidden p-8 text-center text-[12px] font-medium text-slate-500">Semua struk transfer sudah diproses.</p>
                        </section>

                        {{-- Reservations Table --}}
                        <section class="{{ $card }}">
                            <div class="p-5 border-b border-slate-100">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h2 class="text-[16px] font-bold text-slate-900">Tabel Reservasi Terbaru</h2>
                                        <p class="mt-1 text-[11px] leading-relaxed text-slate-600">Daftar booking terpusat untuk kunjungan perairan &amp; menginap</p>
                                    </div>
                                    <div class="flex items-center rounded-lg bg-slate-100 p-1">
                                        @foreach ($tabs as [$key, $label])
                                            <button type="button" data-filter="{{ $key }}"
                                                    class="rounded-md px-4 py-1.5 text-center text-[10px] font-semibold transition {{ $loop->first ? 'bg-[#0B3A22] text-white shadow-sm' : 'text-slate-700 hover:text-[#0B3A22]' }}">{!! $label !!}</button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-[60px_120px_100px_100px_90px_1fr] bg-slate-50 px-5 py-3 text-[9px] font-bold uppercase tracking-wide text-slate-600 border-b border-slate-200">
                                <span>Kode</span><span>Nama Tamu</span><span>Tipe Layanan</span><span>Tanggal &amp; Sesi</span><span>Status</span><span class="text-center">Aksi</span>
                            </div>

                            <div id="reservations">
                                @foreach ($reservations as [$type, $code, $guest, $phone, $service, $date, $st])
                                    <div data-type="{{ $type }}" class="grid grid-cols-[60px_120px_100px_100px_90px_1fr] items-center px-5 py-4 transition hover:bg-slate-50 border-b border-slate-100 last:border-0">
                                        <span class="text-[10px] font-bold">{!! $br($code) !!}</span>
                                        <span>
                                            <span class="block text-[11px] font-semibold">{!! $br($guest) !!}</span>
                                            <span class="mt-0.5 block text-[10px] text-slate-500">{!! $br($phone) !!}</span>
                                        </span>
                                        <span class="text-[11px] leading-relaxed">{!! $br($service) !!}</span>
                                        <span class="text-[10px] leading-relaxed">{!! $br($date) !!}</span>
                                        <span><span class="inline-flex items-center gap-1.5 rounded-full {{ $status[$st][0] }} px-2.5 py-1 text-[9px] font-bold"><span class="h-1.5 w-1.5 rounded-full {{ $status[$st][1] }}"></span>{{ $st }}</span></span>
                                        <button type="button" class="justify-self-center text-slate-400 hover:text-slate-600" aria-label="Aksi">
                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    </div>

                    {{-- Right Column --}}
                    <div class="space-y-6">

                        {{-- Quota Section --}}
                        <section class="{{ $card }} p-5">
                            <div class="mb-4 flex items-center justify-between">
                                <h2 class="flex items-center gap-2 text-[15px] font-bold text-slate-900">{!! $ic($p['ticket'], 'h-5 w-5') !!} Kuota Tiket Hari Ini</h2>
                                <span class="flex items-center gap-1.5 text-[9px] text-emerald-600 font-medium">
                                    <span class="relative flex h-2 w-2"><span class="absolute h-full w-full animate-ping rounded-full bg-emerald-500 opacity-70"></span><span class="relative h-2 w-2 rounded-full bg-emerald-600"></span></span>Real-time
                                </span>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <div class="flex justify-between text-[11px] mb-1"><span class="font-semibold text-slate-700">Kolam Air Panas Classic</span><b class="text-[11px]"><span data-q1-used>120</span> / 200</b></div>
                                    <div class="h-2 overflow-hidden rounded-full bg-slate-100"><div data-q1-bar style="width: 60%" class="h-full rounded-full bg-[#0B3A22] transition-all duration-1000"></div></div>
                                    <p data-q1-text class="mt-1.5 text-right text-[10px] text-slate-600">Sisa 80 tiket (60%)</p>
                                </div>

                                <div>
                                    <div class="flex justify-between text-[11px] mb-1"><span class="font-semibold text-slate-700">Kolam Belerang Premier</span><b class="text-[11px] text-[#7A5230]">65 / 100</b></div>
                                    <div class="h-2 overflow-hidden rounded-full bg-slate-100"><div data-bar style="width: 65%" class="h-full rounded-full bg-[#7A5230] transition-all duration-1000"></div></div>
                                    <p class="mt-1.5 text-right text-[10px] text-slate-600">Sisa 35 tiket (65%)</p>
                                </div>

                                <button type="button" class="mt-2 flex w-full items-center justify-center gap-2 rounded-lg bg-slate-100 py-2.5 text-[11px] font-bold transition hover:bg-slate-200">
                                    {!! $ic($p['sliders'], 'h-4 w-4') !!} + Atur Kuota
                                </button>
                            </div>
                        </section>

                        {{-- Cabin Availability --}}
                        <section class="{{ $card }} p-5">
                            <div class="mb-4 flex items-start justify-between">
                                <h2 class="flex items-start gap-2 text-[15px] font-bold text-slate-900"><span class="mt-0.5">{!! $ic($p['cabin'], 'h-5 w-5') !!}</span> <span>Ketersediaan Kabin</span></h2>
                                <span class="rounded-lg {{ $mint }} px-3 py-1.5 text-center text-[10px] font-bold">3 Total Unit</span>
                            </div>
                            <div class="space-y-2">
                                @foreach ([['Pinus Cabin A', 'Tamu: Hendra S.', 'Terisi', true, 'bed'], ['Magnolia Cabin B', 'Check-in 14:00 WIB', 'Terisi', true, 'bed'], ['Eukaliptus Cabin C', 'Siap Reservasi', 'Tersedia', false, 'door']] as [$n, $sub, $st, $busy, $icon])
                                    <div class="flex items-center gap-3 rounded-lg bg-slate-50 p-3">
                                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $busy ? $lav : $mint }}">{!! $ic($p[$icon], 'h-4 w-4') !!}</span>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-[11px] font-bold text-slate-900">{{ $n }}</p>
                                            <p class="text-[10px] text-slate-600">{{ $sub }}</p>
                                        </div>
                                        <span class="shrink-0 rounded-full {{ $busy ? $peach : $mint }} px-2.5 py-1 text-[9px] font-bold">{{ $st }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </section>

                        {{-- Staff Section --}}
                        <section class="{{ $card }} p-5">
                            <div class="mb-4 flex items-center justify-between">
                                <h2 class="flex items-center gap-2 text-[15px] font-bold text-slate-900">{!! $ic($p['idcard'], 'h-5 w-5') !!} Petugas Lapangan</h2>
                                <span class="relative flex h-2 w-2"><span class="absolute h-full w-full animate-ping rounded-full bg-[#0B3A22] opacity-60"></span><span class="relative h-2 w-2 rounded-full bg-[#0B3A22]"></span></span>
                            </div>
                            <div class="space-y-3">
                                @foreach ([['RH', $lav, 'Rian H.', ['Gate 1 & Scanner', 'Turnstile']], ['HG', $peach, 'Hendra G.', ['Area Kolam & Life', 'Safety']]] as [$ini, $bg, $n, $sub])
                                    <div class="flex items-center gap-3">
                                        <span class="relative flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $bg }} text-[11px] font-bold">
                                            {{ $ini }}<span class="absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full border-2 border-white bg-[#0B3A22]"></span>
                                        </span>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-[11px] font-bold text-slate-900">{{ $n }}</p>
                                            <p class="text-[10px] text-slate-600">{!! $br($sub) !!}</p>
                                        </div>
                                        <span class="rounded-full {{ $mint }} px-2.5 py-1 text-[9px] font-bold">Online</span>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </main>
    </div>
    </div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const LIVE_DEMO = true;
        const $ = (s) => document.querySelector(s);
        const $$ = (s) => document.querySelectorAll(s);
        const fmt = (n) => Math.round(n).toLocaleString('id-ID');

        const animate = (el, to, dur = 900) => {
            if (!el) return;
            const from = Number(el.dataset.val ?? 0);
            el.dataset.val = to;
            if (reduce) { el.textContent = fmt(to); return; }
            const t0 = performance.now();
            const tick = (now) => {
                const p = Math.min((now - t0) / dur, 1);
                el.textContent = fmt(from + (to - from) * (1 - Math.pow(1 - p, 3)));
                if (p < 1) requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
        };

        const S = { revenue: 14850000, sold: 185, q1: 120 };
        const CAP = 300, Q1_CAP = 200;

        const render = () => {
            animate($('[data-revenue]'), S.revenue, 1200);
            animate($('[data-sold]'), S.sold);
            animate($('[data-q1-used]'), S.q1);
            const pct = Math.round((S.sold / CAP) * 100);
            $('[data-sold-bar]').style.width = pct + '%';
            $('[data-sold-pct]').textContent = pct + '% Terisi';
            $('[data-sold-left]').textContent = 'Sisa ' + (CAP - S.sold) + ' Slot';
            const q1pct = Math.round((S.q1 / Q1_CAP) * 100);
            $('[data-q1-bar]').style.width = q1pct + '%';
            $('[data-q1-text]').textContent = 'Sisa ' + (Q1_CAP - S.q1) + ' tiket (' + q1pct + '%)';
        };

        ['[data-revenue]', '[data-sold]', '[data-q1-used]'].forEach((s) => ($(s).dataset.val = 0));
        $$('[data-bar], [data-sold-bar], [data-q1-bar]').forEach((bar) => {
            const w = bar.style.width;
            bar.style.width = '0%';
            requestAnimationFrame(() => requestAnimationFrame(() => (bar.style.width = w)));
        });
        render();

        const setAll = (sel, v) => $$(sel).forEach((el) => (el.textContent = v));
        let total = Number($('[data-total]').textContent);
        let batch = Number($('[data-batch]').textContent);

        $$('[data-action]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const row = btn.closest('[data-queue-row]');
                if (!row || row.dataset.done) return;
                row.dataset.done = '1';

                if (btn.dataset.action === 'verify') {
                    S.revenue += Number(row.dataset.amount);
                    S.sold = Math.min(CAP, S.sold + Number(row.dataset.tickets));
                    render();
                }

                row.style.transition = 'all 0.3s ease';
                row.style.opacity = '0';
                row.style.transform = 'translateX(-10px)';
                setTimeout(() => {
                    row.remove();
                    if (!$('[data-queue-row]')) $('#queue-empty').classList.remove('hidden');
                }, 300);

                total = Math.max(0, total - 1);
                batch = Math.max(0, batch - 1);
                setAll('[data-total]', total);
                setAll('[data-batch]', batch);
            });
        });

        const on = ['bg-[#0B3A22]', 'text-white', 'shadow-sm'];
        const off = ['text-slate-700', 'hover:text-[#0B3A22]'];
        $$('[data-filter]').forEach((tab) => {
            tab.addEventListener('click', () => {
                $$('[data-filter]').forEach((t) => { t.classList.remove(...on); t.classList.add(...off); });
                tab.classList.remove(...off);
                tab.classList.add(...on);
                $$('#reservations [data-type]').forEach((row) => {
                    row.classList.toggle('hidden', tab.dataset.filter !== 'all' && row.dataset.type !== tab.dataset.filter);
                });
            });
        });

        if (LIVE_DEMO) {
            setInterval(() => {
                if (S.q1 >= Q1_CAP || S.sold >= CAP) return;
                S.q1 += 1;
                S.sold += 1;
                S.revenue += 45000;
                render();
            }, 8000);
        }
    });
</script>
@endpush
