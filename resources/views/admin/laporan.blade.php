@extends('layouts.dashboard')

@section('title', 'Laporan Operasional, Keuangan & Export Data - Jiwanta')

@section('content')

<style>
    @keyframes jw-rise{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}
    @keyframes jw-wiggle{0%,100%{transform:rotate(0)}20%{transform:rotate(-14deg)}40%{transform:rotate(12deg)}60%{transform:rotate(-8deg)}80%{transform:rotate(5deg)}}
    @keyframes jw-shrink{from{width:100%}to{width:0}}
    @keyframes jw-ripple{to{transform:scale(4);opacity:0}}
    @keyframes jw-flash{0%{box-shadow:0 0 0 0 rgba(11,58,34,.35)}100%{box-shadow:0 0 0 14px rgba(11,58,34,0)}}
    @keyframes jw-pop{0%{transform:scale(.6);opacity:.4}70%{transform:scale(1.08)}100%{transform:scale(1);opacity:1}}
    @keyframes jw-grow{from{transform:scaleY(0)}to{transform:scaleY(1)}}
    @keyframes jw-fill{from{width:0}}
    [data-reveal]{opacity:0;transform:translateY(18px);transition:opacity .6s ease,transform .7s cubic-bezier(.2,.8,.2,1)}
    [data-reveal].in{opacity:1;transform:none}
    .jw-wiggle{animation:jw-wiggle .7s}
    .jw-flash{animation:jw-flash .9s}
    .jw-pop{animation:jw-pop .4s}
    .jw-bar{transform-origin:bottom;transform-box:fill-box;animation:jw-grow .8s cubic-bezier(.2,.8,.2,1) backwards}
    @media (prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}[data-reveal]{opacity:1;transform:none}}
</style>

@php
    $ic = fn (string $d, string $c = 'h-4 w-4') =>
        '<svg class="'.$c.'" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="'.$d.'"/></svg>';

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
        'clock'     => 'M12 8v4l2 2M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'logout'    => 'M15 4h4a1 1 0 011 1v14a1 1 0 01-1 1h-4M10 16l-4-4 4-4M6 12h10',
        'snow'      => 'M12 3v18M4.5 7.5l15 9M4.5 16.5l15-9M9 4l3 2 3-2M9 20l3-2 3 2',
        'tree'      => 'M12 3l4 6h-2.5l3.5 5h-3l3 5H7l3-5H7l3.5-5H8l4-6z',
        'mail'      => 'M3 6h18v12H3zM3 7l9 6 9-6',
        'file'      => 'M7 3h7l5 5v13H7zM14 3v5h5M10 13h6M10 17h6',
        'sheet'     => 'M4 4h16v16H4zM4 10h16M4 15h16M10 4v16',
        'cal'       => 'M4 6h16v14H4zM4 10h16M8 3v4M16 3v4',
        'cash'      => 'M3 7h18v10H3zM12 14a2 2 0 100-4 2 2 0 000 4zM6 10v.01M18 14v.01',
        'bank'      => 'M3 10l9-6 9 6M5 10v8M9 10v8M15 10v8M19 10v8M3 20h18',
        'scan'      => 'M4 8V5a1 1 0 011-1h3M16 4h3a1 1 0 011 1v3M20 16v3a1 1 0 01-1 1h-3M8 20H5a1 1 0 01-1-1v-3M4 12h16',
        'receipt2'  => 'M6 3h12v18l-3-2-3 2-3-2-3 2V3zM9 8h6M9 12h6M9 16h3',
        'leaf'      => 'M5 19c0-9 5-14 14-14 0 9-5 14-14 14zM5 19l7-7',
        'download'  => 'M12 4v11M8 11l4 4 4-4M5 20h14',
        'check'     => 'M5 13l4 4L19 7',
        'star'      => 'M12 3l2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9L12 3z',
        'house'     => 'M3 11l9-8 9 8M5 10v10h14V10M9 20v-6h6v6',
        'chev'      => 'M6 9l6 6 6-6',
        'up'        => 'M7 17L17 7M9 7h8v8',
    ];

    $nav = [
        'Navigasi Utama' => [['Dashboard', 'dashboard', false, null], ['Verifikasi & Transaksi', 'receipt', false, 14]],
        'Manajemen Resort' => [['Kelola Tiket', 'ticket', false, null], ['Kelola Cabin Suite', 'cabin', false, null]],
        'Administrasi & Sistem' => [['Manajemen Pengguna', 'users', false, null], ['Laporan & Export', 'chart', true, null], ['Pengaturan Profil', 'gear', false, null]],
    ];

    $mint = 'bg-[#CDEFD5]';
    $peach = 'bg-[#FBD9B0]';
    $lav = 'bg-[#DDE6FB]';
    $card = 'rounded-2xl bg-white shadow-sm border border-slate-100';

    $quick = [['today', 'Hari Ini'], ['7d', '7 Hari Terakhir'], ['month', 'Bulan Ini'], ['quarter', 'Kuartal Ini']];

    // Dokumen arsip
    $docs = [
        ['tag' => 'Pajak Daerah', 'size' => 'File: 2.4 MB', 'icon' => 'bank', 'ibg' => 'bg-[#FBD5D5] text-red-700', 'title' => 'Laporan Rekapitulasi Pajak Pariwisata Kab. Bandung',
         'desc' => 'Bulan September 2026. Dasar pengenaan pajak hiburan 10% atas tiket kolam hangat & PPN penginapan kabin glamping.', 'gen' => 'Generated: 20 Sep 2026, 06:00 WIB', 'btn' => 'Unduh PDF', 'bcls' => 'bg-[#0B3A22]', 'bic' => 'file', 'kind' => 'pdf'],
        ['tag' => 'Operasional Gerbang', 'size' => 'File: 580 KB', 'icon' => 'scan', 'ibg' => 'bg-[#CDEFD5] text-[#0B3A22]', 'title' => 'Laporan Harian Kasir & Gate Scanner',
         'desc' => 'Shift Pagi (07:00 - 15:00) & Sore (15:00 - 23:00) 20 September 2026. Pencocokan tiket fisik turnstile vs kas cash drawer.', 'gen' => 'Generated: Real-time Snapshot', 'btn' => 'Unduh Excel', 'bcls' => 'bg-[#0B3A22]', 'bic' => 'sheet', 'kind' => 'csv'],
        ['tag' => 'Log Verifikasi', 'size' => 'File: 14.8 MB (ZIP)', 'icon' => 'receipt2', 'ibg' => 'bg-[#FBD9B0] text-[#8B5E34]', 'title' => 'Log Audit Pembayaran & Bukti Struk Manual',
         'desc' => '14 Hari Terakhir. Berisi arsip kompresi resolusi tinggi bukti transfer manual, ID mutasi bank BCA/Mandiri, dan catatan…', 'gen' => 'Generated: 19 Sep 2026, 23:59 WIB', 'btn' => 'Unduh ZIP/CSV', 'bcls' => 'bg-[#7A5230]', 'bic' => 'download', 'kind' => 'zip'],
        ['tag' => 'Fasilitas & Room', 'size' => 'File: 3.1 MB', 'icon' => 'house', 'ibg' => 'bg-[#CDEFD5] text-[#0B3A22]', 'title' => 'Laporan Okupansi & Pemeliharaan Cabin Suite',
         'desc' => 'Jadwal deep-cleaning geothermal tub, konsumsi amenities, rating kepuasan tamu, dan siklus perawatan kayu pinus…', 'gen' => 'Generated: 18 Sep 2026, 17:00 WIB', 'btn' => 'Unduh PDF', 'bcls' => 'bg-[#0B3A22]', 'bic' => 'file', 'kind' => 'pdf'],
    ];
@endphp

    <div class="min-w-full bg-slate-50 flex">

    {{-- ================= SIDEBAR ================= --}}
    <aside class="w-[240px] shrink-0 bg-white border-r border-slate-200 flex flex-col">
        <div class="px-5 pt-6 pb-4">
            <div class="flex items-center gap-3">
                <span class="flex h-[26px] w-[26px] shrink-0 items-center justify-center rounded-lg bg-[#0B3A22] text-[#B5F0BE]">{!! $ic($p['tree'], 'h-4 w-4') !!}</span>
                <div>
                    <p class="text-[10px] font-bold leading-tight tracking-wide">JIWANTA</p>
                    <p class="text-[6px] font-semibold uppercase leading-tight tracking-wider text-slate-600">Ciwidey Resort</p>
                </div>
                <div class="pl-1">
                    <p class="text-[14px] font-bold leading-tight text-[#0B3A22]">Jiwanta</p>
                    <p class="text-[8px] font-bold uppercase leading-tight tracking-wider text-[#8B5E34]">Admin Panel</p>
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
                                <a href="{{ route('pengaturan') }}" class="flex h-[36px] items-center gap-3 rounded-lg px-3 text-[13px] font-medium transition {{ $active ? 'bg-[#0B3A22] text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                                    {!! $ic($p[$icon], 'h-4 w-4 shrink-0') !!}
                                    <span class="flex-1">{{ $text }}</span>
                                    @if ($badge)
                                        <span data-pending-badge class="rounded-full {{ $peach }} px-2 py-0.5 text-[10px] font-bold text-[#6B4520]">{{ $badge }}</span>
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
            <button type="button" class="flex w-full items-center justify-center gap-2 rounded-lg {{ $lav }} py-2.5 text-[11px] font-bold text-red-700 transition hover:brightness-95">
                {!! $ic($p['logout'], 'h-4 w-4') !!} Keluar Sistem
            </button>
        </div>
    </aside>

    {{-- ================= MAIN ================= --}}
    <div class="flex-1 flex flex-col min-w-0">

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
                <button type="button" data-bell class="relative h-10 w-10 flex items-center justify-center rounded-full bg-slate-100 transition hover:bg-slate-200" aria-label="Notifikasi">
                    {!! $ic($p['bell'], 'h-5 w-5') !!}
                    <span class="absolute right-2 top-2 flex h-2.5 w-2.5"><span class="absolute h-full w-full animate-ping rounded-full bg-red-500 opacity-70"></span><span class="relative h-2.5 w-2.5 rounded-full border-2 border-white bg-red-600"></span></span>
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

        <main class="flex-1 p-6 overflow-y-auto">
            <div class="max-w-7xl mx-auto space-y-6">

                {{-- Header Card --}}
                <div class="{{ $card }} p-6">
                    <div class="flex items-start justify-between gap-6">
                        <div class="max-w-xl">
                            <p class="mb-3 flex items-center gap-2 text-[9px] font-bold uppercase tracking-[.2em] text-[#8B5E34]">
                                <span class="h-1.5 w-1.5 rounded-full bg-[#8B5E34]"></span> Audit Finansial &amp; Ledger Resmi
                                <span class="ml-2 flex items-center gap-1.5 normal-case tracking-normal font-medium text-slate-500"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span><span data-live>Realtime: 2 Menit Lalu</span></span>
                            </p>
                            <h1 class="text-[30px] font-bold leading-tight text-[#0B3A22]">Laporan Operasional, Keuangan &amp; Export Data</h1>
                            <p class="mt-2 text-[13px] leading-relaxed text-slate-600">Analisis performa pendapatan tiket, okupansi kabin glamping, volume kunjungan, dan ekspor laporan berkala.</p>
                        </div>
                        <div class="flex shrink-0 flex-col items-end gap-3">
                            <div class="flex items-center gap-3">
                                <button type="button" data-mail class="flex items-center gap-2 rounded-full {{ $lav }} px-4 py-2.5 text-[11px] font-semibold text-[#0B3A22] transition hover:brightness-95">{!! $ic($p['mail'], 'h-4 w-4') !!} Jadwalkan via Email</button>
                                <button type="button" data-print class="flex items-center gap-2 rounded-full {{ $lav }} px-4 py-2.5 text-[11px] font-semibold text-red-700 transition hover:brightness-95">{!! $ic($p['file'], 'h-4 w-4') !!} Export ke PDF (.pdf)</button>
                            </div>
                            <button type="button" data-export class="flex items-center gap-2 rounded-full bg-[#0B3A22] px-6 py-3 text-[11px] font-bold text-white shadow-sm transition hover:bg-[#124c2f]">{!! $ic($p['sheet'], 'h-4 w-4') !!} Export ke Excel (.xlsx)</button>
                        </div>
                    </div>
                    <div class="mt-5 flex items-center justify-between gap-4">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="mr-2 text-[10px] font-bold uppercase tracking-wide text-slate-600">Pilihan Cepat:</span>
                            @foreach ($quick as [$k, $l])
                                <button type="button" data-quick="{{ $k }}" class="rounded-full px-4 py-2 text-[11px] font-semibold transition {{ $k === 'month' ? 'bg-[#0B3A22] text-white shadow-sm' : $lav.' text-slate-800 hover:brightness-95' }}">{{ $l }}</button>
                            @endforeach
                        </div>
                        <span class="flex items-center gap-2 rounded-full bg-[#F1F4FC] px-4 py-2 text-[10px] text-slate-600">{!! $ic($p['cal'], 'h-3.5 w-3.5') !!} Periode Aktif: <b data-period class="text-slate-900">01 Sep 2026 – 20 Sep 2026</b> {!! $ic($p['chev'], 'h-3 w-3') !!}</span>
                    </div>
                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-3 gap-4">
                    <div data-reveal class="{{ $card }} p-5 hover:shadow-md">
                        <div class="flex items-start justify-between">
                            <p class="text-[10px] font-bold uppercase leading-snug text-slate-600">Total Omzet<br>Terverifikasi<br><span class="normal-case text-[#0B3A22]">Bulan September 2026</span></p>
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl {{ $mint }} text-[#0B3A22]">{!! $ic($p['cash'], 'h-4 w-4') !!}</span>
                        </div>
                        <p class="mt-2 text-[12px] font-bold text-[#0B3A22]">Rp</p>
                        <p data-omzet class="text-[32px] font-bold leading-tight text-[#0B3A22]">0</p>
                        <div class="mt-2 flex items-center gap-2">
                            <span class="inline-flex items-center gap-1 rounded-full {{ $mint }} px-2.5 py-1 text-[9px] font-bold text-[#0B3A22]">{!! $ic($p['trend'] ?? $p['up'], 'h-3 w-3') !!} +16.4% YoY</span>
                            <span class="text-[10px] text-slate-600">vs periode lalu</span>
                        </div>
                        <svg data-spark class="mt-3 h-8 w-full" viewBox="0 0 160 32" preserveAspectRatio="none"></svg>
                    </div>

                    <div data-reveal class="{{ $card }} p-5 hover:shadow-md">
                        <div class="flex items-start justify-between">
                            <p class="text-[10px] font-bold uppercase leading-snug text-slate-600">Volume Kunjungan<br><span class="normal-case text-[#8B5E34]">Tiket Kolam Air Hangat</span></p>
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl {{ $peach }} text-[#8B5E34]">{!! $ic($p['pool'], 'h-4 w-4') !!}</span>
                        </div>
                        <p class="mt-3 text-[32px] font-bold leading-tight text-[#0B3A22]"><span data-pax>0</span> <span class="text-[13px] font-medium text-slate-600">Pax</span></p>
                        <div class="mt-2 flex items-center gap-2">
                            <span class="inline-flex items-center gap-1 rounded-full {{ $peach }} px-2.5 py-1 text-[9px] font-bold text-[#6B4520]">{!! $ic($p['up'], 'h-3 w-3') !!} +8.2% Pax</span>
                            <span class="text-[10px] text-slate-600">Weekend spike</span>
                        </div>
                        <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3 text-[10px] font-semibold text-slate-700">
                            <span>Classic: <span data-cls>0</span></span><span class="text-slate-400">•</span><span>Premier: <span data-prem>0</span></span>
                        </div>
                    </div>

                    <div data-reveal class="{{ $card }} p-5 hover:shadow-md">
                        <div class="flex items-start justify-between">
                            <p class="text-[10px] font-bold uppercase leading-snug text-slate-600">Okupansi Cabin<br><span class="normal-case text-slate-800">12 Unit Villa</span></p>
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl {{ $mint }} text-[#0B3A22]">{!! $ic($p['house'], 'h-4 w-4') !!}</span>
                        </div>
                        <p class="mt-3 text-[32px] font-bold leading-tight text-[#0B3A22]"><span data-occ>0</span>%</p>
                        <div class="mt-2 flex items-center gap-2">
                            <span class="rounded-full {{ $mint }} px-2.5 py-1 text-[9px] font-bold text-[#0B3A22]">Status: Sangat Tinggi</span>
                            <span class="text-[10px] text-slate-600">Sisa <span data-sisa>9</span> room-night</span>
                        </div>
                        <div class="mt-5 h-1.5 overflow-hidden rounded-full bg-slate-100"><div data-occ-bar style="width:92.5%;animation:jw-fill 1.4s cubic-bezier(.2,.8,.2,1)" class="h-full rounded-full bg-[#0B3A22]"></div></div>
                    </div>
                </div>

                {{-- Chart + Highlight --}}
                <div class="grid grid-cols-[1.75fr_1fr] items-start gap-4">
                    <div data-reveal class="{{ $card }} p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2">
                                    <h2 class="text-[16px] font-bold leading-tight text-[#0B3A22]">Grafik Komparasi<br>Pendapatan &amp; Kunjungan</h2>
                                    <span class="flex items-center gap-1.5 rounded-full {{ $mint }} px-2 py-1 text-[8px] font-bold text-[#0B3A22]"><span class="relative flex h-1.5 w-1.5"><span class="absolute h-full w-full animate-ping rounded-full bg-[#0B3A22] opacity-60"></span><span class="relative h-1.5 w-1.5 rounded-full bg-[#0B3A22]"></span></span>Live Feed</span>
                                </div>
                                <p class="mt-1 text-[10px] leading-snug text-slate-600">Korelasi harian tiket kolam air panas dan okupansi kabin 15–20 Sep 2026</p>
                            </div>
                            <div class="flex items-center gap-3 text-[10px] font-semibold text-slate-700">
                                <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-sm bg-[#0B3A22]"></span>Kabin</span>
                                <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-sm bg-[#8B6B43]"></span>Tiket Kolam</span>
                                <span class="flex items-center gap-1.5"><span class="h-0.5 w-3 bg-[#C0272D]"></span>Pax Kunjungan</span>
                            </div>
                        </div>
                        <svg data-chart viewBox="0 0 540 260" class="mt-3 w-full"></svg>
                        <div class="mt-2 flex items-center justify-between gap-4 rounded-xl bg-[#EEF2FC] px-4 py-3">
                            <p class="flex items-center gap-2 text-[10px] leading-snug text-slate-800"><span class="text-[#8B5E34]">{!! $ic($p['star'], 'h-4 w-4') !!}</span><span><b>Puncak Terverifikasi:</b> Sabtu 19 Sep menghasilkan omzet harian <b>Rp 42.150.000</b> dengan 890 tiket scan valid.</span></p>
                            <p class="shrink-0 text-right text-[9px] leading-snug text-slate-600">Tingkat akurasi<br>audit: <b class="text-[#0B3A22]">99.8%</b></p>
                        </div>
                    </div>

                    <div data-reveal class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#0B3A22] via-[#0F4A2C] to-[#14563A] p-6 text-white shadow-sm">
                        <div class="flex items-center justify-between">
                            <span class="rounded-full bg-white/15 px-3 py-1.5 text-[8px] font-bold uppercase tracking-wider">Laporan Finansial Unggulan</span>
                            <span class="text-[#B5F0BE]">{!! $ic($p['leaf'], 'h-4 w-4') !!}</span>
                        </div>
                        <h3 class="mt-4 text-[19px] font-bold leading-snug">Efisiensi Reservasi Mandiri Menghemat Biaya Front Desk Sebesar 32%</h3>
                        <p class="mt-3 text-[10px] leading-relaxed text-white/80">Penerapan gate turnstile QR-code terintegrasi telah memangkas antrian tiket fisik kolam hingga 8 menit per rombongan saat peak weekend kabut Ciwidey.</p>
                        <div class="mt-4 space-y-2 rounded-xl bg-white/10 p-3 text-[10px]">
                            <p class="flex items-start justify-between gap-3"><span class="font-semibold text-white/85">Rata-rata Durasi Kolam:</span><b>2 Jam 45 Menit</b></p>
                            <p class="flex items-start justify-between gap-3"><span class="font-semibold text-white/85">RevPAR Glamping Kabin:</span><b class="text-right leading-snug">Rp 1.480.000 / malam</b></p>
                        </div>
                        <button type="button" data-summary class="mt-4 flex w-full items-center justify-center gap-2 rounded-full bg-[#8B6B43] px-4 py-3 text-[11px] font-bold text-white transition hover:brightness-110">{!! $ic($p['download'], 'h-4 w-4') !!} Unduh Executive Summary (1-Pager)</button>
                    </div>
                </div>

                {{-- Tabel Rekap --}}
                <section data-reveal>
                    <div class="mb-3 flex items-end justify-between">
                        <div>
                            <h2 class="text-[18px] font-bold text-[#0B3A22]">Tabel Rekap Laporan Finansial Harian (Periode Terpilih)</h2>
                            <p class="mt-0.5 text-[11px] text-slate-600">Rincian breakdown pemasukan per unit bisnis dari 15 September sampai dengan 20 September 2026.</p>
                        </div>
                        <p class="text-[10px] text-slate-600">Total 6 hari transaksi terverifikasi</p>
                    </div>
                    <div class="{{ $card }} overflow-hidden shadow-md">
                        <div class="grid grid-cols-[1.1fr_1.3fr_.9fr_1fr_1fr_1.1fr_.9fr] items-center gap-3 bg-[#E9EEFB] px-5 py-4 text-[10px] font-bold uppercase text-slate-700">
                            <span>Tanggal</span><span class="leading-tight">Tiket Terjual<br>(Cls/Prem)</span><span>Cabin Terisi</span><span class="text-right">Pendapatan<br>Tiket</span><span class="text-right">Pendapatan<br>Cabin</span><span class="text-right">Total Omzet<br>Bersih</span><span class="text-center">Status Audit</span>
                        </div>
                        <div id="rows"></div>
                        <div id="total" class="grid grid-cols-[1.1fr_1.3fr_.9fr_1fr_1fr_1.1fr_.9fr] items-center gap-3 bg-[#0B3A22] px-5 py-5 text-white"></div>
                    </div>
                </section>

                {{-- Arsip --}}
                <section data-reveal>
                    <div class="mb-3 flex items-end justify-between">
                        <div>
                            <h2 class="text-[18px] font-bold text-[#0B3A22]">Modul Arsip &amp; Dokumen Laporan Siap Unduh</h2>
                            <p class="mt-0.5 text-[11px] text-slate-600">Dokumen resmi tervalidasi bertanda tangan digital direksi resort dan sistem akuntansi.</p>
                        </div>
                        <p class="text-[10px] text-slate-600">Format resmi dinas &amp; perbankan</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach ($docs as $d)
                            <article class="{{ $card }} p-4 transition hover:-translate-y-1 hover:shadow-lg">
                                <div class="flex items-start gap-4">
                                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl {{ $d['ibg'] }}">{!! $ic($p[$d['icon']], 'h-5 w-5') !!}</span>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center justify-between">
                                            <span class="rounded-md bg-[#E3E9FA] px-2 py-1 text-[8px] font-bold uppercase tracking-wide text-slate-700">{{ $d['tag'] }}</span>
                                            <span class="text-[9px] text-slate-600">{{ $d['size'] }}</span>
                                        </div>
                                        <h3 class="mt-2 text-[15px] font-bold leading-snug text-slate-900">{{ $d['title'] }}</h3>
                                        <p class="mt-1 text-[10px] leading-snug text-slate-600">{{ $d['desc'] }}</p>
                                    </div>
                                </div>
                                <div class="mt-3 flex items-center justify-between rounded-xl bg-[#F1F4FC] px-3 py-2.5">
                                    <span class="text-[9px] text-slate-600">{{ $d['gen'] }}</span>
                                    <button type="button" data-doc="{{ $d['kind'] }}" data-title="{{ $d['title'] }}" class="flex items-center gap-1.5 rounded-lg {{ $d['bcls'] }} px-3 py-1.5 text-[10px] font-bold text-white transition hover:brightness-110">{!! $ic($p[$d['bic']], 'h-3.5 w-3.5') !!} {{ $d['btn'] }}</button>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            </div>
        </main>
    </div>
    </div>

    <div id="toasts" class="fixed bottom-6 right-6 z-[60] space-y-2"></div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const LIVE_DEMO = true;
        const $ = (s, r = document) => r.querySelector(s);
        const $$ = (s, r = document) => [...r.querySelectorAll(s)];
        const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const fmt = (n) => Math.round(n).toLocaleString('id-ID');
        const rp = (n) => 'Rp ' + fmt(n);
        const ic = (d, c = 'h-4 w-4') => `<svg class="${c}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="${d}"/></svg>`;
        const I = { checkc: 'M21 12a9 9 0 11-18 0 9 9 0 0118 0zM8.5 12.5l2.5 2.5 4.5-5', clock: 'M12 8v4l2 2M21 12a9 9 0 11-18 0 9 9 0 0118 0z', match: 'M5 13l4 4L19 7' };

        // ---------- Util ----------
        const toast = (msg) => {
            const t = document.createElement('div');
            t.className = 'relative flex items-center gap-2 overflow-hidden rounded-xl bg-[#0B3A22] px-4 py-3 text-[11px] font-semibold text-white shadow-lg opacity-0 translate-x-6 transition-all duration-300';
            t.innerHTML = '<span class="h-2 w-2 animate-pulse rounded-full bg-[#B5F0BE]"></span>' + msg + '<span class="absolute bottom-0 left-0 h-0.5 bg-[#B5F0BE]" style="animation:jw-shrink 3.2s linear forwards"></span>';
            $('#toasts').appendChild(t);
            requestAnimationFrame(() => t.classList.remove('opacity-0', 'translate-x-6'));
            setTimeout(() => { t.classList.add('opacity-0', 'translate-x-6'); setTimeout(() => t.remove(), 300); }, 3200);
        };
        const flash = (el) => { if (!el) return; el.classList.remove('jw-flash'); void el.offsetWidth; el.classList.add('jw-flash'); };
        const bell = () => { const b = $('[data-bell]'); b.classList.remove('jw-wiggle'); void b.offsetWidth; b.classList.add('jw-wiggle'); };
        const tween = (el, to, dur = 900, f = fmt, dec = 0) => {
            if (!el) return;
            const from = Number(el.dataset.v ?? 0);
            el.dataset.v = to;
            if (reduce || from === to) { el.textContent = f(to); return; }
            const t0 = performance.now();
            const tick = (now) => {
                const p = Math.min((now - t0) / dur, 1);
                el.textContent = f(from + (to - from) * (1 - Math.pow(1 - p, 3)));
                if (p < 1) requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
        };
        const download = (name, content, type) => {
            const a = document.createElement('a');
            a.href = URL.createObjectURL(new Blob(['\ufeff' + content], { type }));
            a.download = name; a.click();
            setTimeout(() => URL.revokeObjectURL(a.href), 1000);
        };

        // ---------- Data ----------
        const PRICE = { cls: 35000, prem: 55000 };
        const DAYS = [
            { d: 'Selasa, 15 Sep 2026', s: 'Sel (15)', cls: 170, prem: 40, cab: 8, tk: 7850000, cb: 12800000 },
            { d: 'Rabu, 16 Sep 2026', s: 'Rab (16)', cls: 195, prem: 50, cab: 9, tk: 9150000, cb: 14400000 },
            { d: 'Kamis, 17 Sep 2026', s: 'Kam (17)', cls: 220, prem: 70, cab: 10, tk: 11200000, cb: 16000000 },
            { d: 'Jumat, 18 Sep 2026', s: 'Jum (18)', cls: 360, prem: 120, cab: 12, tk: 18600000, cb: 20400000 },
            { d: 'Sabtu, 19 Sep 2026', s: 'Sab (19) ✦', cls: 680, prem: 210, cab: 12, tk: 34550000, cb: 21600000, peak: true, audit: 'ok' },
            { d: 'Minggu, 20 Sep 2026', s: 'Min (20) ✦', cls: 590, prem: 190, cab: 12, tk: 30200000, cb: 21600000, peak: true, audit: 'run' },
        ];
        DAYS.forEach((x) => x.audit = x.audit || 'ok');
        const BASE = { omzet: 184250000, pax: 3120, cls: 2380, prem: 740 };
        const live = { omzet: 0, cls: 0, prem: 0 };
        const SP = [4, 6, 5, 8, 7, 10, 9, 12, 11, 14, 13, 17];

        const pax = (x) => x.cls + x.prem;
        const net = (x) => x.tk + x.cb;

        // ---------- Tabel ----------
        const COLS = 'grid grid-cols-[1.1fr_1.3fr_.9fr_1fr_1fr_1.1fr_.9fr]';
        const rowHTML = (x, i, anim) => {
            const full = x.cab === 12;
            const cab = full
                ? `<span class="inline-block rounded-full bg-[#CDEFD5] px-2.5 py-1 text-center text-[9px] font-bold leading-tight text-[#0B3A22]">12 / 12<br>(Penuh)</span>`
                : `<span class="inline-block rounded-full bg-[#DDE6FB] px-2.5 py-1 text-[9px] font-bold text-slate-700">${x.cab} / 12 Unit</span>`;
            const au = x.audit === 'ok'
                ? `<span class="inline-flex items-center gap-1 rounded-full bg-[#CDEFD5] px-2.5 py-1 text-[9px] font-bold text-[#0B3A22]">${ic(I.checkc, 'h-3 w-3')} Reconciled</span>`
                : `<span class="inline-flex items-center gap-1 rounded-full bg-[#FBD9B0] px-2.5 py-1 text-center text-[9px] font-bold leading-tight text-[#6B4520]">${ic(I.clock, 'h-3 w-3')} Audit<br>Berjalan</span>`;
            const [hari, ...rest] = x.d.split(', ');
            return `<div data-day="${i}" class="${COLS} items-center gap-3 border-b border-slate-100 px-5 py-4 transition-colors hover:bg-slate-50 ${x.peak ? 'bg-[#F7F9FE]' : ''}" style="${anim ? `animation:jw-rise .5s cubic-bezier(.2,.8,.2,1) ${i * 70}ms backwards` : ''}">
                <p class="text-[11px] font-bold leading-snug text-slate-900">${hari}, ${rest.join(', ')}${x.peak ? ' <span class="text-[#8B5E34]">(Peak)</span>' : ''}</p>
                <div><p class="text-[13px] font-bold text-slate-900">${fmt(pax(x))} Pax</p><p class="text-[9px] leading-snug text-slate-600">${fmt(x.cls)} Cls • ${fmt(x.prem)} Prem</p></div>
                <div>${cab}</div>
                <p class="text-right text-[12px] font-semibold text-slate-800">${rp(x.tk)}</p>
                <p class="text-right text-[12px] font-semibold text-slate-800">${rp(x.cb)}</p>
                <p class="text-right text-[13px] font-bold text-slate-900">${rp(net(x))}</p>
                <div class="text-center">${au}</div>
            </div>`;
        };
        const renderTotal = () => {
            const s = (k) => DAYS.reduce((a, x) => a + (typeof k === 'function' ? k(x) : x[k]), 0);
            const cab = s('cab');
            $('#total').innerHTML = `
                <p class="text-[10px] font-bold uppercase leading-snug">Total<br>Periode (6<br>Hari)</p>
                <p class="text-[12px] font-bold leading-snug">${fmt(s(pax))} Pax<br><span class="text-[9px] font-medium text-white/80">Terverifikasi</span></p>
                <p class="text-[11px] font-bold leading-snug">${cab} / 72 Unit<br>Night (${(cab / 72 * 100).toFixed(1)}%)</p>
                <p class="text-right text-[12px] font-bold">${rp(s('tk'))}</p>
                <p class="text-right text-[12px] font-bold">${rp(s('cb'))}</p>
                <p class="text-right text-[18px] font-bold leading-tight text-[#F3C98B]">Rp<br>${fmt(s(net))}</p>
                <div class="text-center"><span class="inline-flex items-center gap-1 rounded-full bg-white/15 px-2.5 py-1 text-[9px] font-bold leading-tight">${ic(I.match, 'h-3 w-3')} Ledger<br>Match</span></div>`;
        };
        const renderRows = (anim = true) => {
            $('#rows').innerHTML = DAYS.map((x, i) => rowHTML(x, i, anim)).join('');
            renderTotal();
        };

        // ---------- Chart ----------
        const STOPS = [0, 5, 15, 30, 45].map((v) => v * 1e6);
        const yPos = (v, top, h) => {
            v = Math.min(v, STOPS[4]);
            for (let i = 1; i < 5; i++) if (v <= STOPS[i]) {
                const t = (v - STOPS[i - 1]) / (STOPS[i] - STOPS[i - 1]);
                return top + h - ((i - 1 + t) / 4) * h;
            }
            return top;
        };
        const drawChart = (animate) => {
            const W = 540, L = 50, R = 10, T = 10, H = 200, BASEY = T + H;
            const gw = (W - L - R) / DAYS.length;
            let g = '';
            STOPS.forEach((v, i) => {
                const y = T + H - (i / 4) * H;
                g += `<line x1="${L}" x2="${W - R}" y1="${y}" y2="${y}" stroke="#E5E7EB" stroke-dasharray="${i ? '3 3' : ''}"/><text x="${L - 8}" y="${y + 3}" text-anchor="end" font-size="9" fill="#64748B">${v ? 'Rp ' + v / 1e6 + 'M' : '0'}</text>`;
            });
            let bars = '', pts = [];
            DAYS.forEach((x, i) => {
                const cx = L + gw * i + gw / 2, bw = 15;
                const y1 = yPos(x.cb, T, H), y2 = yPos(x.tk, T, H);
                const dl = animate ? `animation-delay:${i * 80}ms` : 'animation:none';
                bars += `<rect class="jw-bar" style="${dl}" x="${cx - bw - 1}" y="${y1}" width="${bw}" height="${BASEY - y1}" rx="2" fill="#0B3A22"/>`;
                bars += `<rect class="jw-bar" style="${dl}" x="${cx + 1}" y="${y2}" width="${bw}" height="${BASEY - y2}" rx="2" fill="#8B6B43"/>`;
                pts.push([cx, yPos(pax(x) * 47000, T, H)]);
                g += `<text x="${cx}" y="${BASEY + 18}" text-anchor="middle" font-size="9.5" font-weight="${x.peak ? 700 : 500}" fill="#334155">${x.s}</text>`;
            });
            const line = pts.map((p, i) => (i ? 'L' : 'M') + p[0].toFixed(1) + ' ' + p[1].toFixed(1)).join(' ');
            const dots = pts.map((p) => `<circle cx="${p[0]}" cy="${p[1]}" r="3.2" fill="#fff" stroke="#C0272D" stroke-width="1.8"/>`).join('');
            $('[data-chart]').innerHTML = g + bars + `<path d="${line}" fill="none" stroke="#C0272D" stroke-width="1.8" stroke-linejoin="round" stroke-linecap="round"/>` + dots;
        };
        const drawSpark = (animate) => {
            const sv = $('[data-spark]');
            const mx = Math.max(...SP), mn = Math.min(...SP);
            const w = 160 / SP.length;
            sv.innerHTML = SP.map((v, i) => {
                const h = 4 + ((v - mn) / (mx - mn || 1)) * 24;
                const last = i === SP.length - 1;
                return `<rect x="${i * w + 1}" y="${32 - h}" width="${w - 3}" height="${h}" rx="1.5" fill="${last ? '#0B3A22' : '#CDEFD5'}" class="jw-bar" style="${animate ? `animation-delay:${i * 40}ms` : 'animation:none'}"/>`;
            }).join('');
        };

        // ---------- Stats ----------
        const updateStats = (animate = true) => {
            const cabN = DAYS.reduce((a, x) => a + x.cab, 0);
            tween($('[data-omzet]'), BASE.omzet + live.omzet, animate ? 1100 : 0);
            tween($('[data-pax]'), BASE.pax + live.cls + live.prem, animate ? 1100 : 0);
            tween($('[data-cls]'), BASE.cls + live.cls, animate ? 900 : 0);
            tween($('[data-prem]'), BASE.prem + live.prem, animate ? 900 : 0);
            tween($('[data-occ]'), 92.5, animate ? 1100 : 0, (v) => v.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 1 }));
        };

        // ---------- Realtime label ----------
        let lastUpdate = Date.now() - 120000;
        const touch = () => { lastUpdate = Date.now(); };
        setInterval(() => {
            const s = Math.floor((Date.now() - lastUpdate) / 1000);
            $('[data-live]').textContent = 'Realtime: ' + (s < 10 ? 'Baru saja' : s < 60 ? s + ' Detik Lalu' : Math.floor(s / 60) + ' Menit Lalu');
        }, 1000);

        // ---------- Aksi ----------
        const PER = { today: ['20 Sep 2026', '20 Sep 2026'], '7d': ['14 Sep 2026', '20 Sep 2026'], month: ['01 Sep 2026', '20 Sep 2026'], quarter: ['01 Jul 2026', '20 Sep 2026'] };
        $$('[data-quick]').forEach((b) => b.addEventListener('click', () => {
            $$('[data-quick]').forEach((x) => { x.className = 'rounded-full px-4 py-2 text-[11px] font-semibold transition ' + (x === b ? 'bg-[#0B3A22] text-white shadow-sm' : 'bg-[#DDE6FB] text-slate-800 hover:brightness-95'); });
            const [a, z] = PER[b.dataset.quick];
            $('[data-period]').textContent = a + ' – ' + z;
            flash($('[data-period]').parentElement); touch();
            toast('Periode diubah: ' + b.textContent.trim());
        }));

        const csvRows = () => ['Tanggal,Cls,Prem,Total Pax,Cabin Terisi,Pendapatan Tiket,Pendapatan Cabin,Total Omzet,Status Audit']
            .concat(DAYS.map((x) => [x.d, x.cls, x.prem, pax(x), x.cab + '/12', x.tk, x.cb, net(x), x.audit === 'ok' ? 'Reconciled' : 'Audit Berjalan'].map((v) => '"' + String(v).replace(/"/g, '""') + '"').join(','))).join('\n');

        $('[data-export]').addEventListener('click', () => { download('laporan-keuangan-jiwanta.csv', csvRows(), 'text/csv;charset=utf-8'); toast('Laporan diekspor (format CSV, terbuka di Excel).'); });
        $('[data-print]').addEventListener('click', () => { toast('Menyiapkan dokumen PDF...'); setTimeout(() => window.print(), 600); });
        $('[data-mail]').addEventListener('click', () => { touch(); toast('Laporan terjadwal tiap hari 06:00 WIB ke email admin.'); });
        $('[data-summary]').addEventListener('click', () => {
            download('executive-summary-jiwanta.txt', 'EXECUTIVE SUMMARY JIWANTA\nTotal omzet: ' + rp(BASE.omzet + live.omzet) + '\nTotal pax: ' + fmt(BASE.pax + live.cls + live.prem) + '\nOkupansi cabin: 92,5%\nRevPAR: Rp 1.480.000/malam', 'text/plain;charset=utf-8');
            toast('Executive Summary diunduh.');
        });
        $$('[data-doc]').forEach((b) => b.addEventListener('click', () => {
            const k = b.dataset.doc;
            if (k === 'pdf') { toast('Menyiapkan: ' + b.dataset.title); setTimeout(() => window.print(), 600); }
            else { download('arsip-jiwanta.csv', csvRows(), 'text/csv;charset=utf-8'); toast('Unduhan dimulai: ' + b.dataset.title); }
        }));

        // ---------- Efek global ----------
        const io = new IntersectionObserver((es) => es.forEach((en) => {
            if (!en.isIntersecting) return;
            en.target.classList.add('in');
            setTimeout(() => (en.target.style.transitionDelay = ''), 900);
            io.unobserve(en.target);
        }), { threshold: 0.06 });
        $$('[data-reveal]').forEach((el, i) => { el.style.transitionDelay = (i % 3) * 90 + 'ms'; io.observe(el); });

        document.addEventListener('pointerdown', (e) => {
            const b = e.target.closest('main button, aside button');
            if (!b || b.disabled || reduce) return;
            if (getComputedStyle(b).position === 'static') b.style.position = 'relative';
            b.style.overflow = 'hidden';
            const r = b.getBoundingClientRect(), sz = Math.max(r.width, r.height);
            const rip = document.createElement('span');
            rip.className = 'pointer-events-none absolute rounded-full bg-white/40';
            rip.style.cssText = `width:${sz}px;height:${sz}px;left:${e.clientX - r.left - sz / 2}px;top:${e.clientY - r.top - sz / 2}px;animation:jw-ripple .6s ease-out forwards`;
            b.appendChild(rip);
            setTimeout(() => rip.remove(), 600);
        });

        // ---------- Live demo: tiket scan masuk real-time ----------
        if (LIVE_DEMO) {
            setInterval(() => {
                const today = DAYS[DAYS.length - 1];
                const nc = 1 + Math.floor(Math.random() * 4), np = Math.random() < 0.5 ? 1 + Math.floor(Math.random() * 2) : 0;
                const add = nc * PRICE.cls + np * PRICE.prem;
                today.cls += nc; today.prem += np; today.tk += add;
                live.cls += nc; live.prem += np; live.omzet += add;
                SP[SP.length - 1] += 0.3;
                renderRows(false);
                flash($('[data-day="' + (DAYS.length - 1) + '"]'));
                drawChart(false); drawSpark(false); updateStats();
                touch(); bell();
                toast('Scan valid: +' + (nc + np) + ' tiket kolam (' + rp(add) + ')');
            }, 6000);
        }

        // ---------- Init ----------
        renderRows(true);
        drawChart(true);
        drawSpark(true);
        updateStats();
    });
</script>
@endpush
