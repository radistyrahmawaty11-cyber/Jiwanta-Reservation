@extends('layouts.admin')

@section('title', 'Kelola Cabin Suite & Shorts - Jiwanta')

@section('page-content')

<style>
    @keyframes jw-grow{from{opacity:0;transform:scaleX(.2)}to{opacity:1;transform:scaleX(1)}}
    @keyframes jw-rise{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}
    @keyframes jw-wiggle{0%,100%{transform:rotate(0)}20%{transform:rotate(-14deg)}40%{transform:rotate(12deg)}60%{transform:rotate(-8deg)}80%{transform:rotate(5deg)}}
    @keyframes jw-shrink{from{width:100%}to{width:0}}
    @keyframes jw-ripple{to{transform:scale(4);opacity:0}}
    @keyframes jw-flash{0%{box-shadow:0 0 0 0 rgba(11,58,34,.35)}100%{box-shadow:0 0 0 14px rgba(11,58,34,0)}}
    @keyframes jw-pop{0%{transform:scale(.6);opacity:.4}70%{transform:scale(1.08)}100%{transform:scale(1);opacity:1}}
    [data-reveal]{opacity:0;transform:translateY(18px);transition:opacity .6s ease,transform .7s cubic-bezier(.2,.8,.2,1)}
    [data-reveal].in{opacity:1;transform:none}
    #tl-rows>div{animation:jw-rise .5s cubic-bezier(.2,.8,.2,1) backwards}
    #tl-rows>div:nth-child(2){animation-delay:.1s}
    #tl-rows .contents>*{transform-origin:left center;animation:jw-grow .6s cubic-bezier(.2,.8,.2,1) backwards;animation-delay:.25s}
    #tl-rows .contents>*:nth-child(2){animation-delay:.35s}
    #tl-rows .contents>*:nth-child(3){animation-delay:.45s}
    .jw-wiggle{animation:jw-wiggle .7s}
    .jw-flash{animation:jw-flash .9s}
    .jw-pop{animation:jw-pop .4s}
    @media (prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}[data-reveal]{opacity:1;transform:none}}
</style>

@php
    // ---------- Helper ----------
    $ic = fn (string $d, string $c = 'h-4 w-4') =>
        '<svg class="'.$c.'" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="'.$d.'"/></svg>';
    $br = fn (array $lines) => implode('<br>', array_map('e', $lines));
    $rp = fn (int $n) => 'Rp '.number_format($n, 0, ',', '.');

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
        'plus'      => 'M12 5v14M5 12h14',
        'calendar'  => 'M8 3v3M16 3v3M4 9h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z',
        'bed'       => 'M3 18V7M3 14h18v4M21 14v-2a3 3 0 00-3-3h-7v5',
        'tree'      => 'M12 3l4 6h-2.5l3.5 5h-3l3 5H7l3-5H7l3.5-5H8l4-6z',
        'sync'      => 'M4 12a8 8 0 0113.7-5.6L20 8M20 4v4h-4M20 12a8 8 0 01-13.7 5.6L4 16M4 20v-4h4',
        'left'      => 'M15 6l-6 6 6 6',
        'right'     => 'M9 6l6 6-6 6',
        'lock'      => 'M6 11h12v9H6zM8 11V8a4 4 0 018 0v3',
        'tv'        => 'M4 5h16v11H4zM9 20h6M12 16v4',
        'wifi'      => 'M2 9a15 15 0 0120 0M5 12.5a10 10 0 0114 0M8.5 16a5 5 0 017 0M12 19.5h.01',
        'fire'      => 'M12 3c1 4 5 5 5 10a5 5 0 01-10 0c0-2 1-3 2-4 0 2 1 3 2 3 0-3-1-5 1-9z',
        'food'      => 'M7 3v8M5 3v5a2 2 0 004 0V3M7 11v10M17 3c-2 2-3 5-3 8h3v10',
        'sofa'      => 'M5 11V8a2 2 0 012-2h10a2 2 0 012 2v3M3 13a2 2 0 014 0v2h10v-2a2 2 0 014 0v5H3z',
        'kitchen'   => 'M5 3h14v18H5zM5 9h14M9 5.5h.01M9 12v3',
        'deck'      => 'M3 10l9-6 9 6M5 10v10M19 10v10M3 20h18M9 14h6',
        'spark'     => 'M12 3l2 5 5 2-5 2-2 5-2-5-5-2 5-2z',
        'x'         => 'M6 6l12 12M18 6L6 18',
        'copy'      => 'M9 9h10v11H9zM5 15V4h10',
        'trash'     => 'M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16',
    ];

    $nav = [
        'Navigasi Utama' => [['Dashboard', 'dashboard', false, null], ['Verifikasi & Transaksi', 'receipt', false, 14]],
        'Manajemen Resort' => [['Kelola Tiket', 'ticket', false, null], ['Kelola Cabin', 'cabin', true, null]],
        'Administrasi & Sistem' => [['Manajemen Pengguna', 'users', false, null], ['Laporan & Export', 'chart', false, null], ['Pengaturan Profil', 'gear', false, null]],
    ];

    $mint = 'bg-[#CDEFD5]';
    $peach = 'bg-[#FBD9B0]';
    $lav = 'bg-[#DDE6FB]';

    $card = 'rounded-2xl bg-white shadow-sm border border-slate-200';
    $cap = 'text-[9px] font-bold uppercase leading-snug tracking-wide text-slate-600';
@endphp

<<<<<<< HEAD
=======
<<<<<<< HEAD
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
                                <a href="{{ route($active ? 'cabin' : 'manajemen-pengguna') }}" class="flex h-[36px] items-center gap-3 rounded-lg px-3 text-[13px] font-medium transition {{ $active ? 'bg-[#0B3A22] text-white' : 'text-slate-700 hover:bg-slate-50' }}">
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

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-lg {{ $lav }} py-2.5 text-[11px] font-bold text-red-700 transition hover:brightness-95">
                    {!! $ic($p['logout'], 'h-4 w-4') !!} Keluar Sistem
                </button>
            </form>
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
                    <span data-notif class="absolute right-2 top-2 h-2.5 w-2.5 rounded-full bg-red-600 border-2 border-white"></span>
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
=======
>>>>>>> 593d6fbd28a469abbe6a7d9a1a3b10fd1a150a7a
>>>>>>> d0c70c8d31d52cecb4d10594c5c6910a3c2cf03b

                {{-- Header Section --}}
                <div class="flex items-start justify-between">
                    <div class="max-w-xl">
                        <p class="mb-2 flex items-center gap-2 text-[9px] font-bold uppercase tracking-wide">
                            <span class="rounded-md {{ $mint }} px-2 py-1 text-[#0B3A22]">Highland Inventory Master</span>
                            <span class="text-slate-400">•</span>
                            <span class="font-medium normal-case text-slate-600">Ciwidey Valley Sanctuary</span>
                        </p>
                        <h1 class="text-[32px] font-bold leading-tight text-[#0B3A22]">Manajemen Unit Cabin Suite &amp; Shorts</h1>
                        <p class="mt-2 text-[13px] leading-relaxed text-slate-600">Kelola unit kabin, kalender ketersediaan real-time, status kebersihan (housekeeping), dan tarif musiman.</p>
                    </div>
                    <div class="flex flex-wrap items-center justify-end gap-3 max-w-[420px]">
                        <button type="button" data-sync class="flex items-center gap-2 rounded-full {{ $lav }} px-4 py-2 text-[11px] font-semibold text-[#0B3A22] transition hover:brightness-95">
                            <span data-sync-icon class="inline-flex">{!! $ic($p['sync'], 'h-4 w-4') !!}</span> <span data-sync-text>Sinkronisasi PMS / Smart Lock</span>
                        </button>
                        <button type="button" data-datepick class="flex items-center gap-2 rounded-full {{ $lav }} px-4 py-2 text-[11px] font-semibold text-[#0B3A22] transition hover:brightness-95">
                            {!! $ic($p['calendar'], 'h-4 w-4') !!} Kalender Okupansi
                        </button>
                        <button type="button" data-add-unit class="flex items-center gap-2 rounded-full bg-[#0B3A22] px-5 py-2.5 text-[11px] font-bold text-white shadow-sm transition hover:bg-[#124c2f]">
                            {!! $ic($p['plus'], 'h-4 w-4') !!} Tambah Unit Kabin
                        </button>
                    </div>
                </div>

                {{-- Stats Cards --}}
                <div class="grid grid-cols-3 gap-4 items-start">
                    <div data-reveal class="{{ $card }} p-5 hover:shadow-md">
                        <div class="flex items-start justify-between">
                            <p class="{{ $cap }}">Inventaris Unit</p>
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $mint }}">{!! $ic($p['cabin'], 'h-5 w-5') !!}</span>
                        </div>
                        <p class="text-[34px] font-bold leading-tight text-[#0B3A22]"><span data-inv>{{ $cabins->count() }}</span> <span class="text-[18px] font-medium text-slate-600">Kabin</span></p>
                        <div class="mt-3 flex items-end justify-between text-[10px] text-slate-600">
                            <span class="max-w-[110px] leading-snug">Tipe Suite Eksklusif Ciwidey</span>
                            <b class="max-w-[100px] text-right text-[10px] leading-snug text-slate-900">100% Terdaftar PMS</b>
                        </div>
                    </div>

                    <div data-reveal class="{{ $card }} p-5 hover:shadow-md">
                        <div class="flex items-start justify-between">
                            <p class="{{ $cap }}">Terisi Malam Ini</p>
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $peach }} text-[#8B5E34]">{!! $ic($p['bed'], 'h-5 w-5') !!}</span>
                        </div>
                        <div class="flex items-start gap-2">
                            <p class="text-[34px] font-bold leading-[1.1] text-[#0B3A22]"><span data-occ>0</span><br>Unit</p>
                            <span data-occ-pct class="mt-4 rounded-full {{ $mint }} px-3 py-1 text-[9px] font-bold leading-tight text-[#0B3A22]">0%<br>Okupansi</span>
                        </div>
                        <p class="mt-3 flex items-start gap-2 text-[10px] leading-snug text-slate-600"><span class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-[#0B3A22]"></span><span>Data real-time dari sistem</span></p>
                    </div>

                    <div data-reveal class="{{ $card }} p-5 hover:shadow-md">
                        <div class="flex items-start justify-between">
                            <p class="{{ $cap }}">Status Housekeeping</p>
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $mint }}">{!! $ic($p['spark'], 'h-5 w-5') !!}</span>
                        </div>
                        <p class="text-[34px] font-bold leading-tight text-[#0B3A22]"><span data-hk-ready>{{ $cabins->count() }}</span> Siap</p>
                        <p class="mt-3 flex items-center gap-2 text-[10px] text-slate-600"><span class="text-[#C2762B]">{!! $ic($p['fire'], 'h-3.5 w-3.5') !!}</span> Semua unit dalam kondisi baik</p>
                    </div>
                </div>

                {{-- Interactive Timeline --}}
                <section data-reveal class="{{ $card }} p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-[#0B3A22] text-white">{!! $ic($p['calendar'], 'h-5 w-5') !!}</span>
                            <div>
                                <h2 class="text-[16px] font-bold leading-tight text-slate-900">Interactive Visual Timeline<br>Kalender</h2>
                                <p class="relative mt-1 text-[10px] leading-snug text-slate-600">Periode aktif mingguan: <button type="button" data-range-btn title="Klik untuk pilih tanggal" class="inline-flex items-center gap-1 rounded font-semibold text-[#0B3A22] underline decoration-dotted underline-offset-2 transition hover:text-[#7A5230]"><span data-range>20 Sep 2026 - 26 Sep 2026</span>{!! $ic($p['calendar'], 'h-3 w-3') !!}</button><input type="date" data-date-pick tabindex="-1" aria-label="Pilih tanggal awal" class="pointer-events-none absolute left-0 top-full h-0 w-0 opacity-0"></p>
                            </div>
                        </div>
                        <div class="flex items-center gap-5 text-[10px] text-slate-700">
                            <span class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-[#0B3A22]"></span>Terisi<br>Penuh</span>
                            <span class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-[#7A5230]"></span>Check-in<br>Hari Ini</span>
                            <span class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full {{ $lav }}"></span>Maintenance /<br>Sanitasi</span>
                            <span class="flex items-center gap-2"><span class="h-2.5 w-2.5 rounded-full bg-slate-100 border border-slate-200"></span>Available</span>
                            <div class="flex items-center rounded-xl {{ $lav }} px-2 py-2">
                                <button type="button" data-week="-1" class="rounded-md p-1 text-slate-600 transition hover:bg-white/60" aria-label="Minggu sebelumnya">{!! $ic($p['left'], 'h-3.5 w-3.5') !!}</button>
                                <button type="button" data-week="0" class="px-3 text-center text-[10px] font-bold leading-tight text-slate-900">Minggu<br>Ini</button>
                                <button type="button" data-week="1" class="rounded-md p-1 text-slate-600 transition hover:bg-white/60" aria-label="Minggu berikutnya">{!! $ic($p['right'], 'h-3.5 w-3.5') !!}</button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 overflow-x-auto">
                        <div class="min-w-[760px]">
                            <div id="tl-head" class="grid grid-cols-[190px_repeat(7,minmax(0,1fr))_110px] items-center gap-2 px-3 text-[9px] font-bold uppercase tracking-wide text-slate-600"></div>
                            <div id="tl-rows" class="mt-3 space-y-3"></div>
                        </div>
                    </div>
                </section>

                {{-- Master Data --}}
                <section data-reveal>
                    <div class="flex items-end justify-between">
                        <div>
                            <h2 class="text-[16px] font-bold text-slate-900">Master Data Unit Cabin Suite &amp; Shorts</h2>
                            <p class="mt-0.5 text-[11px] text-slate-600">Spesifikasi fasilitas kamar, dan tarif acuan</p>
                        </div>
                        <div id="filters" class="flex flex-wrap items-center justify-end gap-2 text-[10px] font-medium text-slate-600">
                            Filter Tipe:
                            @php
                                $totalCabin = $cabins->count();
                                $suiteCount = $cabins->where('jenis_cabin', 'suite')->count();
                                $shortsCount = $cabins->where('jenis_cabin', 'shorts')->count();
                            @endphp
                            <button type="button" data-filter="all" class="rounded-full px-3 py-1 text-[10px] font-bold transition bg-[#0B3A22] text-white">Semua ({{ $totalCabin }})</button>
                            <button type="button" data-filter="suite" class="rounded-full px-3 py-1 text-[10px] font-bold transition bg-[#DDE6FB] text-slate-800 hover:brightness-95">Suite ({{ $suiteCount }})</button>
                            <button type="button" data-filter="shorts" class="rounded-full px-3 py-1 text-[10px] font-bold transition bg-[#DDE6FB] text-slate-800 hover:brightness-95">Shorts ({{ $shortsCount }})</button>
                        </div>
                    </div>

                    <div id="units" class="mt-5 flex flex-wrap justify-center gap-4">
<<<<<<< HEAD
=======
<<<<<<< HEAD
                        @foreach ($cabins as $cabin)
                            <article data-unit="{{ $cabin->jenis_cabin }}" data-reveal class="group relative w-[260px] overflow-hidden rounded-2xl bg-white shadow-md border border-slate-100 hover:shadow-xl">
                                <span data-shine class="pointer-events-none absolute inset-0 z-20 opacity-0 transition-opacity duration-300 bg-[radial-gradient(220px_circle_at_var(--mx,50%)_var(--my,50%),rgba(255,255,255,0.28),transparent_65%)]"></span>

                                <div class="relative h-[158px] overflow-hidden bg-[#1d3b2a]">
                                    @php
                                        $imgPath = $cabin->jenis_cabin === 'suite' ? 'images/cabin-suite.jpg' : 'images/cabin-shorts.jpg';
                                    @endphp
                                    <img src="{{ asset($imgPath) }}" alt="{{ $cabin->nama_cabin }}" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110">
                                    <div class="absolute inset-0 bg-gradient-to-t from-[#0B3A22]/80 via-transparent to-black/10"></div>

                                    <span data-badge class="absolute left-3 top-3 z-10 inline-flex cursor-pointer select-none items-center gap-1.5 rounded-full bg-[#FBD9B0] text-[#6B4520] px-2.5 py-1 text-[9px] font-bold transition hover:brightness-95">
                                        <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-[#7A5230]"></span>
                                        <span data-badge-text>{{ ucfirst($cabin->status) }}</span>
                                    </span>
                                    <span class="absolute right-3 top-3 rounded-md bg-[#0B3A22] px-2 py-1 text-[9px] font-bold text-white">Unit {{ $cabin->id }}</span>
                                    <h3 class="absolute bottom-3 left-3 text-[16px] font-bold text-white">{{ $cabin->nama_cabin }}</h3>
=======
>>>>>>> d0c70c8d31d52cecb4d10594c5c6910a3c2cf03b
                        @foreach ($units as $u)
                            <article data-unit="{{ $u['key'] }}" data-reveal class="group relative w-[260px] overflow-hidden rounded-2xl bg-white shadow-md border border-slate-100 hover:shadow-xl">
                                <span data-shine class="pointer-events-none absolute inset-0 z-20 opacity-0 transition-opacity duration-300 bg-[radial-gradient(220px_circle_at_var(--mx,50%)_var(--my,50%),rgba(255,255,255,0.28),transparent_65%)]"></span>
                                <div class="relative h-[158px] overflow-hidden bg-[#1d3b2a]">
                                    <img data-img src="{{ asset($u['img']) }}" alt="{{ $u['title'] }}" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110">
                                    <div class="absolute inset-0 bg-gradient-to-t from-[#0B3A22]/80 via-transparent to-black/10"></div>
                                    <span data-badge class="absolute left-3 top-3 z-10 inline-flex cursor-pointer select-none items-center gap-1.5 rounded-full {{ $u['badge'][1] }} px-2.5 py-1 text-[9px] font-bold transition hover:brightness-95">
                                        <span class="h-1.5 w-1.5 animate-pulse rounded-full {{ $u['badge'][2] }}"></span><span data-badge-text>{{ $u['badge'][0] }}</span>
                                    </span>
                                    <span class="absolute right-3 top-3 rounded-md bg-[#0B3A22] px-2 py-1 text-[9px] font-bold text-white">{{ $u['no'] }}</span>
                                    <h3 data-title class="absolute bottom-3 left-3 text-[16px] font-bold text-white">{{ $u['title'] }}</h3>
<<<<<<< HEAD
=======
>>>>>>> 593d6fbd28a469abbe6a7d9a1a3b10fd1a150a7a
>>>>>>> d0c70c8d31d52cecb4d10594c5c6910a3c2cf03b
                                </div>

                                <div class="p-4">
                                    <p class="flex items-center gap-2 text-[10px] font-bold text-slate-800">
                                        <span class="text-[#0B3A22]">{!! $ic('users', 'h-3.5 w-3.5') !!}</span>
                                        Kapasitas: {{ $cabin->kapasitas }} orang
                                    </p>

                                    <div class="mt-3 flex flex-wrap gap-1.5">
                                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-[#DDE6FB] px-2 py-1 text-[9px] font-medium text-slate-800 transition hover:-translate-y-0.5 hover:bg-white hover:shadow-sm">
                                            {!! $ic('wifi', 'h-3 w-3') !!} High-Speed WiFi
                                        </span>
                                    </div>

                                    <div class="mt-4 space-y-1.5 rounded-xl bg-slate-100 p-3 text-[10px] text-slate-600">
                                        <div class="flex items-start justify-between gap-2">
                                            <span class="leading-snug">Tarif per Malam</span>
                                            <b data-rate-normal class="text-right text-[11px] leading-snug text-slate-900">
                                                Rp {{ number_format($cabin->harga_per_malam, 0, ',', '.') }}
                                            </b>
                                        </div>
                                    </div>

                                    {{-- TOMBOL AKSI (EDIT & HAPUS) --}}
                                    <div class="mt-4 flex items-center gap-2">
                                        <button type="button" class="flex-1 rounded-lg bg-[#DDE6FB] px-3 py-2 text-[10px] font-bold text-slate-800 transition hover:brightness-95">Edit</button>

                                        <form method="POST" action="{{ route('cabin.destroy', $cabin->id) }}" onsubmit="return confirm('Yakin ingin menghapus cabin {{ $cabin->nama_cabin }}?')" class="flex-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-full rounded-lg bg-red-50 px-3 py-2 text-[10px] font-bold text-red-600 transition hover:bg-red-100 flex items-center justify-center gap-1">
                                                {!! $ic('trash', 'h-3.5 w-3.5') !!} Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
<<<<<<< HEAD
=======
<<<<<<< HEAD
            </div>
        </main>
    </div>
    </div>

    {{-- Modal --}}
=======
>>>>>>> d0c70c8d31d52cecb4d10594c5c6910a3c2cf03b
@endsection
@section('overlays')
    {{-- Modal (Atur Tarif / Detail Booking / Walk-in / Edit Unit / Tambah Unit) --}}
>>>>>>> 593d6fbd28a469abbe6a7d9a1a3b10fd1a150a7a
    <div id="modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/40 p-4">
        <div data-m-box class="max-h-[90vh] w-full max-w-sm overflow-y-auto rounded-2xl bg-white p-6 shadow-xl">
            <div class="flex items-start justify-between">
                <div>
                    <h3 data-m-title class="text-[16px] font-bold text-slate-900">Atur Tarif</h3>
                    <p data-m-sub class="mt-0.5 text-[11px] text-slate-600"></p>
                </div>
                <button type="button" data-m-close class="rounded-lg p-1 text-slate-500 hover:bg-slate-100" aria-label="Tutup">{!! $ic($p['x'], 'h-5 w-5') !!}</button>
            </div>
            <div data-m-body class="mt-5 space-y-4"></div>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" data-m-close class="rounded-lg {{ $lav }} px-4 py-2 text-[11px] font-bold">Batal</button>
                <button type="button" data-m-save class="rounded-lg bg-[#0B3A22] px-5 py-2 text-[11px] font-bold text-white transition hover:bg-[#124c2f]">Simpan</button>
            </div>
        </div>
    </div>

    {{-- Toast --}}
    <div id="toasts" class="fixed bottom-6 right-6 z-[60] space-y-2"></div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Variabel dari Laravel untuk menembak Database
        const STORE_URL = "{{ route('cabin.store') }}";
        const CSRF_TOKEN = "{{ csrf_token() }}";

        const LIVE_DEMO = true;
        const $ = (s, r = document) => r.querySelector(s);
        const $$ = (s, r = document) => [...r.querySelectorAll(s)];
        const fmt = (n) => Math.round(n).toLocaleString('id-ID');
        const rp = (n) => 'Rp ' + fmt(n);
        const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

        const ICON = (d, c = 'h-4 w-4') => `<svg class="${c}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="${d}"/></svg>`;
        const LOCK = 'M6 11h12v9H6zM8 11V8a4 4 0 018 0v3';
        const CLOCK = 'M12 8v4l2 2M21 12a9 9 0 11-18 0 9 9 0 0118 0z';

        const IC = {
            bed: 'M3 18V7M3 14h18v4M21 14v-2a3 3 0 00-3-3h-7v5',
            tv: 'M4 5h16v11H4zM9 20h6M12 16v4',
            wifi: 'M2 9a15 15 0 0120 0M5 12.5a10 10 0 0114 0M8.5 16a5 5 0 017 0M12 19.5h.01',
            fire: 'M12 3c1 4 5 5 5 10a5 5 0 01-10 0c0-2 1-3 2-4 0 2 1 3 2 3 0-3-1-5 1-9z',
            food: 'M7 3v8M5 3v5a2 2 0 004 0V3M7 11v10M17 3c-2 2-3 5-3 8h3v10',
            sofa: 'M5 11V8a2 2 0 012-2h10a2 2 0 012 2v3M3 13a2 2 0 014 0v2h10v-2a2 2 0 014 0v5H3z',
            kitchen: 'M5 3h14v18H5zM5 9h14M9 5.5h.01M9 12v3',
            deck: 'M3 10l9-6 9 6M5 10v10M19 10v10M3 20h18M9 14h6',
            spark: 'M12 3l2 5 5 2-5 2-2 5-2-5-5-2 5-2z',
            users: 'M17 20v-1a4 4 0 00-4-4H7a4 4 0 00-4 4v1M10 11a4 4 0 100-8 4 4 0 000 8zM21 20v-1a4 4 0 00-3-3.9M16 3.1a4 4 0 010 7.8',
        };
        const amenIcon = (label) => {
            const l = label.toLowerCase();
            const map = [[/bed|kasur/, 'bed'], [/tv/, 'tv'], [/wifi|internet/, 'wifi'], [/spring|kolam|pool|onsen|air panas/, 'fire'], [/sarapan|breakfast|makan|food/, 'food'], [/living|sofa|ruang/, 'sofa'], [/dapur|kitchen/, 'kitchen'], [/deck|teras|balkon|view/, 'deck']];
            const f = map.find(([r]) => r.test(l));
            return IC[f ? f[1] : 'spark'];
        };
        const chip = (l) => `<span class="inline-flex items-center gap-1.5 rounded-lg bg-[#DDE6FB] px-2 py-1 text-[9px] font-medium text-slate-800 transition hover:-translate-y-0.5 hover:bg-white hover:shadow-sm">${ICON(amenIcon(l), 'h-3 w-3')}${esc(l)}</span>`;

        const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const toast = (msg) => {
            const t = document.createElement('div');
            t.className = 'relative flex items-center gap-2 overflow-hidden rounded-xl bg-[#0B3A22] px-4 py-3 text-[11px] font-semibold text-white shadow-lg opacity-0 translate-x-6 transition-all duration-300';
            t.innerHTML = '<span class="h-2 w-2 animate-pulse rounded-full bg-[#B5F0BE]"></span>' + esc(msg) + '<span class="absolute bottom-0 left-0 h-0.5 bg-[#B5F0BE]" style="animation:jw-shrink 3.2s linear forwards"></span>';
            $('#toasts').appendChild(t);
            requestAnimationFrame(() => t.classList.remove('opacity-0', 'translate-x-6'));
            setTimeout(() => { t.classList.add('opacity-0', 'translate-x-6'); setTimeout(() => t.remove(), 300); }, 3200);
        };
        const flash = (el) => { if (!el) return; el.classList.remove('jw-flash'); void el.offsetWidth; el.classList.add('jw-flash'); };
        const bell = () => { const b = $('[data-bell]'); b.classList.remove('jw-wiggle'); void b.offsetWidth; b.classList.add('jw-wiggle'); };
        const tween = (el, to, dur = 800) => {
            if (!el) return;
            const from = Number(el.dataset.v ?? 0);
            el.dataset.v = to;
            if (reduce) { el.textContent = to; return; }
            const t0 = performance.now();
            const tick = (now) => {
                const p = Math.min((now - t0) / dur, 1);
                el.textContent = Math.round(from + (to - from) * (1 - Math.pow(1 - p, 3)));
                if (p < 1) requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
        };
        $$('[data-toast]').forEach((b) => b.addEventListener('click', () => toast(b.dataset.toast)));

        const DAYS = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        const MON = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const utc = (y, m, d) => new Date(Date.UTC(y, m, d));
        const iso = (d) => d.toISOString().slice(0, 10);
        const TODAY = utc(2026, 8, 20);
        const addDays = (d, n) => new Date(d.getTime() + n * 864e5);
        let WS = TODAY;

        const UNITS = [
            { id: 'A', key: 'suite', name: 'Cabin Suite', sub: 'King Bed • 2 Tamu + 1 Anak', action: { label: 'Detail Slot', cls: 'bg-[#DDE6FB] text-slate-800', type: 'slot' },
              segs: [{ from: '2026-09-20', to: '2026-09-22', type: 'occupied', title: 'Hendra Setiawan (2 Malam)', sub: 'Booked: 20 - 22 Sep • Terisi' }] },
            { id: 'B', key: 'shorts', name: 'Cabin Shorts', sub: 'Family Suite • 4-5 Tamu', action: { label: 'Kirim Smart PIN', cls: 'bg-[#FBD9B0] text-[#6B4520]', type: 'pin' },
              segs: [
                { from: '2026-09-20', to: '2026-09-20', type: 'checkin', title: 'Ibu Mariana', sub: 'Check-in 14:00 WIB' },
                { from: '2026-09-21', to: '2026-09-21', type: 'checkout', title: 'Check-out 21 Sep' },
                { from: '2026-09-25', to: '2026-09-26', type: 'none' },
              ] },
        ];

        const weekStart = () => WS;

        const bar = (seg, span, startCol, ukey) => {
            const col = `style="grid-column:${startCol} / span ${span}${seg.walkin ? ';cursor:pointer' : ''}"` + (seg.walkin ? ` data-cancel="${seg.from}" data-ukey="${ukey}" title="Klik untuk batalkan"` : '') + (seg.type === 'checkin' ? ' title="Check-in 14:00 WIB"' : '');
            if (seg.type === 'occupied')
                return `<div ${col} class="flex h-12 items-center gap-2 rounded-xl bg-[#0B3A22] px-3 text-white shadow-sm">
                    <span class="shrink-0">${ICON(LOCK, 'h-3.5 w-3.5')}</span>
                    <div class="min-w-0 flex-1"><p class="truncate text-[10px] font-bold">${esc(seg.title)}</p><p class="truncate text-[9px] text-white/80">${esc(seg.sub)}</p></div>
                    <span class="relative flex h-2 w-2 shrink-0"><span class="absolute h-full w-full animate-ping rounded-full bg-[#B5F0BE] opacity-70"></span><span class="relative h-2 w-2 rounded-full bg-[#B5F0BE]"></span></span></div>`;
            if (seg.type === 'checkin')
                return `<div ${col} class="flex h-12 items-center gap-2 rounded-xl bg-[#7A5230] px-3 text-white shadow-sm">
                    <span class="shrink-0">${ICON(CLOCK, 'h-3.5 w-3.5')}</span>
                    <div class="min-w-0"><p class="truncate text-[10px] font-bold">${esc(seg.title)}</p><p class="truncate text-[9px] text-white/80">${esc(seg.sub)}</p></div></div>`;
            return `<div ${col} class="flex h-12 items-center justify-center rounded-xl bg-[#DDE6FB] px-3 text-[10px] font-bold text-slate-800">${esc(seg.title)}</div>`;
        };

        const slot = (a, b, startCol, span, ukey) => {
            const range = a.getTime() === b.getTime() ? `${a.getUTCDate()} ${MON[a.getUTCMonth()]}` : `${a.getUTCDate()} - ${b.getUTCDate()} ${MON[b.getUTCMonth()]}`;
            return `<button type="button" data-slot="${iso(a)}" data-end="${iso(b)}" data-ukey="${ukey}" style="grid-column:${startCol} / span ${span}" class="group flex h-12 items-center justify-center rounded-xl bg-white text-[10px] font-bold text-slate-800 shadow-sm transition hover:-translate-y-0.5 hover:bg-[#CDEFD5] hover:shadow-md"><span class="group-hover:hidden">Available Slot (${range})</span><span class="hidden group-hover:inline">+ Booking Walk-in (${a.getUTCDate()} ${MON[a.getUTCMonth()]})</span></button>`;
        };

        const renderTimeline = () => {
            const ws = weekStart();
            const we = addDays(WS, 6);
            $('[data-range]').textContent = `${ws.getUTCDate()} ${MON[ws.getUTCMonth()]} ${ws.getUTCFullYear()} - ${we.getUTCDate()} ${MON[we.getUTCMonth()]} ${we.getUTCFullYear()}`;

            $('#tl-head').innerHTML = '<span>Unit Kabin</span>' + [0, 1, 2, 3, 4, 5, 6].map((i) => {
                const d = addDays(WS, i);
                const n = DAYS[d.getUTCDay()];
                const isT = iso(d) === iso(TODAY);
                return `<span class="relative flex flex-col items-center justify-center rounded-xl py-2 text-center ${isT ? 'bg-[#CDEFD5] text-[#0B3A22]' : ''}">${isT ? '<span class="absolute right-2 top-2 flex h-1.5 w-1.5"><span class="absolute h-full w-full animate-ping rounded-full bg-[#0B3A22] opacity-60"></span><span class="relative h-1.5 w-1.5 rounded-full bg-[#0B3A22]"></span></span>' : ''}
                    <span class="text-[9px]">${n}</span><b class="text-[18px] leading-tight text-slate-900">${d.getUTCDate()}</b>
                    <span class="text-[8px] leading-tight">${MON[d.getUTCMonth()].toUpperCase()}${isT ? ' (HARI INI)' : ''}</span></span>`;
            }).join('') + '<span class="text-right">Aksi Jadwal</span>';

            $('#tl-rows').innerHTML = UNITS.filter((u) => filter === 'all' || u.key === filter).map((u) => {
                let cells = '';
                let i = 0;
                while (i < 7) {
                    const d = addDays(WS, i);
                    const k = iso(d);
                    const seg = u.segs.find((s) => k >= s.from && k <= s.to);
                    if (seg) {
                        let span = 1;
                        while (i + span < 7 && iso(addDays(WS, i + span)) <= seg.to) span++;
                        if (seg.type !== 'none') cells += bar(seg, span, i + 2, u.key);
                        i += span;
                    } else if (k < iso(TODAY)) {
                        i++;
                    } else {
                        let span = 1;
                        while (i + span < 7) {
                            const kk = iso(addDays(WS, i + span));
                            if (u.segs.some((s) => kk >= s.from && kk <= s.to)) break;
                            span++;
                        }
                        cells += slot(d, addDays(WS, i + span - 1), i + 2, span, u.key);
                        i += span;
                    }
                }
                return `<div class="grid grid-cols-[190px_repeat(7,minmax(0,1fr))_110px] items-center gap-2 rounded-xl bg-[#E8EEFC] p-3">
                    <div class="flex items-center gap-3" style="grid-column:1;grid-row:1">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-[#0B3A22] text-[13px] font-bold text-white">${u.id}</span>
                        <div><p class="text-[13px] font-bold text-slate-900">${esc(u.name)}</p><p class="text-[10px] text-slate-600">${esc(u.sub)}</p></div></div>
                    <div class="contents [&>*]:row-start-1">${cells}</div>
                    <button type="button" data-tl-action="${u.action.type}" data-unit-id="${u.key}" style="grid-column:9;grid-row:1" class="rounded-lg ${u.action.cls} px-3 py-2 text-[10px] font-bold transition hover:brightness-95">${u.action.label}</button></div>`;
            }).join('');
        };

        let filter = 'all';

        const HK = { ready: 2, service: 1, total: 3 };
        const updateStats = () => {
            const tk = iso(TODAY);
            const occ = UNITS.filter((u) => u.segs.some((s) => tk >= s.from && tk <= s.to && (s.type === 'occupied' || s.type === 'checkin'))).length;
            tween($('[data-inv]'), UNITS.length);
            tween($('[data-occ]'), occ);
            $('[data-occ-pct]').innerHTML = Math.round((occ / UNITS.length) * 100) + '%<br>Okupansi';
            tween($('[data-hk-ready]'), HK.ready);
            tween($('[data-hk-service]'), HK.service);
        };

        $$('[data-week]').forEach((b) => b.addEventListener('click', () => {
            const v = Number(b.dataset.week);
            WS = v === 0 ? TODAY : addDays(WS, v * 7);
            renderTimeline();
        }));

        const picker = $('[data-date-pick]');
        const openPicker = () => {
            picker.value = iso(WS);
            try { picker.showPicker(); } catch (_) { picker.focus(); picker.click(); }
        };
        $$('[data-range-btn], [data-datepick]').forEach((b) => b.addEventListener('click', openPicker));
        picker.addEventListener('change', () => {
            if (!picker.value) return;
            WS = new Date(picker.value + 'T00:00:00Z');
            renderTimeline();
            toast('Timeline dimulai ' + fmtD(picker.value));
        });

        $('#tl-rows').addEventListener('click', (e) => {
            const slotBtn = e.target.closest('[data-slot]');
            if (slotBtn) return openModal(slotBtn.dataset.ukey, 'book', { from: slotBtn.dataset.slot, end: slotBtn.dataset.end });
            const cx = e.target.closest('[data-cancel]');
            if (cx) {
                const u = UNITS.find((x) => x.key === cx.dataset.ukey);
                u.segs = u.segs.filter((s) => !(s.walkin && s.from === cx.dataset.cancel));
                renderTimeline(); updateStats();
                return toast('Booking walk-in dibatalkan.');
            }
            const a = e.target.closest('[data-tl-action]');
            if (!a) return;
            if (a.dataset.tlAction === 'pin') {
                const pin = String(Math.floor(100000 + Math.random() * 900000));
                a.disabled = true;
                a.textContent = 'Mengirim...';
                setTimeout(() => { a.disabled = false; a.textContent = 'Kirim Smart PIN'; toast('Smart PIN ' + pin + ' terkirim ke Ibu Mariana'); }, 900);
            } else toast('Detail slot ' + (a.dataset.unitId === 'suite' ? 'Cabin Suite' : 'Cabin Shorts') + ' dibuka.');
        });

        const onC = ['bg-[#0B3A22]', 'text-white'];
        const offC = ['bg-[#DDE6FB]', 'text-slate-800', 'hover:brightness-95'];
        const bindFilter = (tab) => tab.addEventListener('click', () => {
            filter = tab.dataset.filter;
            $$('[data-filter]').forEach((t) => { t.classList.remove(...onC); t.classList.add(...offC); });
            tab.classList.remove(...offC);
            tab.classList.add(...onC);
            $$('[data-unit]').forEach((c) => c.classList.toggle('hidden', filter !== 'all' && c.dataset.unit !== filter));
            renderTimeline();
        });
        $$('[data-filter]').forEach(bindFilter);

        const RATES = { suite: { normal: 1250000, weekend: 1500000 }, shorts: { normal: 2100000, weekend: 2450000 } };
        const NAMES = { suite: 'Cabin Suite', shorts: 'Cabin Shorts' };
        const modal = $('#modal');
        let current = null;
        const dInp = 'w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-[12px] font-semibold focus:outline-none focus:ring-2 focus:ring-[#0B3A22]/30';
        const lbl = 'block text-[10px] font-bold uppercase text-slate-600';
        const dayDiff = (x, y) => Math.round((new Date(y + 'T00:00:00Z') - new Date(x + 'T00:00:00Z')) / 864e5);
        const fmtD = (k) => Number(k.slice(8)) + ' ' + MON[Number(k.slice(5, 7)) - 1];
        const rangeTxt = (x, y) => (x === y ? fmtD(x) : Number(x.slice(8)) + ' - ' + fmtD(y));
        const clash = (u, x, y, skip) => u.segs.find((sg) => sg !== skip && x <= sg.to && y >= sg.from);
        const unitCard = (key) => $(`[data-unit="${key}"]`);

        const unitForm = (v, withRates) => `
            <label class="${lbl}">Nama Unit<input data-in="name" type="text" value="${esc(v.name)}" placeholder="mis. Cabin Pinus" class="${dInp} mt-1"></label>
            <div>
                <span class="${lbl}">Foto Unit</span>
                <div class="mt-1 flex items-center gap-3">
                    <div class="h-16 w-24 shrink-0 overflow-hidden rounded-lg bg-[#1d3b2a]"><img data-prev ${v.img ? `src="${esc(v.img)}"` : ''} alt="Preview" class="h-full w-full object-cover ${v.img ? '' : 'hidden'}"></div>
                    <div class="min-w-0 flex-1 space-y-2">
                        <input data-in="file" type="file" accept="image/*" class="block w-full text-[10px] text-slate-600 file:mr-2 file:cursor-pointer file:rounded-lg file:border-0 file:bg-[#DDE6FB] file:px-3 file:py-1.5 file:text-[10px] file:font-bold file:text-slate-800">
                        <input data-in="imgurl" type="url" placeholder="atau tempel URL foto" class="${dInp} !py-1.5 !text-[10px]">
                    </div>
                </div>
            </div>
            <label class="${lbl}">Kapasitas<input data-in="cap" type="text" value="${esc(v.cap)}" placeholder="mis. 4-5 orang" class="${dInp} mt-1"></label>
            <label class="${lbl}">Deskripsi Singkat (di timeline)<input data-in="sub" type="text" value="${esc(v.sub)}" placeholder="mis. King Bed • 2 Tamu + 1 Anak" class="${dInp} mt-1"></label>
            <label class="${lbl}">Fasilitas (pisahkan dengan koma)<textarea data-in="amen" rows="3" placeholder="King Bed, Smart TV, Private Hot Spring" class="${dInp} mt-1">${esc(v.amen)}</textarea></label>
            ${withRates ? `<div class="grid grid-cols-2 gap-3">
                <label class="${lbl}">Tarif Normal<input data-in="normal" type="number" step="10000" min="0" placeholder="1250000" class="${dInp} mt-1"></label>
                <label class="${lbl}">Tarif Weekend<input data-in="weekend" type="number" step="10000" min="0" placeholder="1500000" class="${dInp} mt-1"></label></div>` : ''}`;

        const openModal = (key, mode, ex = {}) => {
            const u = key ? UNITS.find((q) => q.key === key) : null;
            current = { key, mode, list: u ? u.segs.filter((sg) => sg.type !== 'none') : [] };
            const wide = mode === 'add' || mode === 'edit';
            $('[data-m-box]').className = 'max-h-[90vh] w-full overflow-y-auto rounded-2xl bg-white p-6 shadow-xl ' + (wide ? 'max-w-md' : 'max-w-sm');
            $('[data-m-title]').textContent = { tarif: 'Atur Tarif', booking: 'Detail Booking', book: 'Booking Walk-in', edit: 'Edit Unit', add: 'Tambah Unit Kabin' }[mode];
            $('[data-m-sub]').textContent = mode === 'add' ? 'Unit baru langsung muncul di timeline & master data' : (key ? NAMES[key] : '');
            $('[data-m-save]').textContent = mode === 'add' ? 'Tambah Unit' : 'Simpan';
            let html = '';
            if (mode === 'tarif') {
                html = `<label class="${lbl}">Tarif Normal (Weekday)<input data-in="normal" type="number" step="10000" min="0" value="${RATES[key].normal}" class="${dInp} mt-1"></label>
                        <label class="${lbl}">Tarif Weekend (Jum-Sab)<input data-in="weekend" type="number" step="10000" min="0" value="${RATES[key].weekend}" class="${dInp} mt-1"></label>`;
            } else if (mode === 'add') {
                html = unitForm({ name: '', img: '', cap: '', sub: '', amen: '' }, true);
            } else if (mode === 'edit') {
                const card = unitCard(key);
                const im = $('[data-img]', card);
                html = unitForm({
                    name: u ? u.name : '',
                    img: im && !im.classList.contains('hidden') ? (im.getAttribute('src') || '') : '',
                    cap: $('[data-cap]', card) ? $('[data-cap]', card).textContent.replace(/^Kapasitas:\s*/, '') : '',
                    sub: u ? u.sub : '',
                    amen: card ? $$('[data-amen] > span', card).map((s) => s.textContent.trim()).join(', ') : '',
                }, false);
            } else if (mode === 'book') {
                const to = dayDiff(ex.from, ex.end) >= 1 ? iso(addDays(new Date(ex.from + 'T00:00:00Z'), 1)) : ex.from;
                html = `<label class="${lbl}">Nama Tamu<input data-in="guest" type="text" placeholder="Nama tamu" class="${dInp} mt-1"></label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="${lbl}">Check-in<input data-in="from" type="date" min="${iso(TODAY)}" value="${ex.from}" class="${dInp} mt-1"></label>
                            <label class="${lbl}">Check-out<input data-in="to" type="date" min="${iso(TODAY)}" value="${to}" class="${dInp} mt-1"></label>
                        </div>
                        <p data-nights class="text-[11px] font-semibold text-[#7A5230]"></p>`;
            } else {
                html = current.list.map((sg, i) => `<div class="rounded-lg bg-slate-50 p-3">
                        <p class="text-[12px] font-bold text-slate-900">${esc(sg.title)}</p>
                        <div class="mt-2 grid grid-cols-2 gap-3">
                            <label class="${lbl}">Mulai<input data-ef="${i}" type="date" value="${sg.from}" class="${dInp} mt-1"></label>
                            <label class="${lbl}">Selesai<input data-et="${i}" type="date" value="${sg.to}" class="${dInp} mt-1"></label>
                        </div></div>`).join('') || '<p class="text-[12px] text-slate-600">Belum ada booking.</p>';
            }
            $('[data-m-body]').innerHTML = html;

            if (wide) {
                const fileIn = $('[data-in="file"]'), urlIn = $('[data-in="imgurl"]'), pv = $('[data-prev]');
                fileIn.addEventListener('change', () => {
                    const f = fileIn.files[0];
                    if (!f) return;
                    if (f.size > 5 * 1024 * 1024) { fileIn.value = ''; return toast('Ukuran foto maksimal 5 MB.'); }
                    pv.src = URL.createObjectURL(f);
                    pv.classList.remove('hidden');
                });
                urlIn.addEventListener('input', () => {
                    if (!urlIn.value.trim()) return;
                    pv.src = urlIn.value.trim();
                    pv.classList.remove('hidden');
                });
            }
            if (mode === 'book') {
                const fi = $('[data-in="from"]'), ti = $('[data-in="to"]');
                const upd = () => {
                    const n = dayDiff(fi.value, ti.value);
                    $('[data-nights]').textContent = fi.value && ti.value && n >= 0
                        ? Math.max(n, 1) + ' malam • ' + rangeTxt(fi.value, ti.value)
                        : 'Tanggal check-out harus sama atau setelah check-in';
                };
                fi.addEventListener('input', () => { if (ti.value < fi.value) ti.value = iso(addDays(new Date(fi.value + 'T00:00:00Z'), 1)); upd(); });
                ti.addEventListener('input', upd);
                upd();
            }
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        };
        const closeModal = () => { modal.classList.add('hidden'); modal.classList.remove('flex'); };
        $$('[data-m-close]').forEach((b) => b.addEventListener('click', closeModal));
        modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });

        const pickImage = (old = '') => {
            const f = $('[data-in="file"]').files[0];
            const url = $('[data-in="imgurl"]').value.trim();
            return f ? URL.createObjectURL(f) : (url || old);
        };
        const readList = () => $('[data-in="amen"]').value.split(',').map((s) => s.trim()).filter(Boolean).slice(0, 12);

        const cardHTML = (u) => `
            <span data-shine class="pointer-events-none absolute inset-0 z-20 opacity-0 transition-opacity duration-300 bg-[radial-gradient(220px_circle_at_var(--mx,50%)_var(--my,50%),rgba(255,255,255,0.28),transparent_65%)]"></span>
            <div class="relative h-[158px] overflow-hidden bg-[#1d3b2a]">
                <img data-img ${u.img ? `src="${esc(u.img)}"` : ''} alt="${esc(u.name)}" class="${u.img ? '' : 'hidden '}h-full w-full object-cover transition-transform duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-gradient-to-t from-[#0B3A22]/80 via-transparent to-black/10"></div>
                <span data-badge class="absolute left-3 top-3 z-10 inline-flex cursor-pointer select-none items-center gap-1.5 rounded-full bg-[#CDEFD5] text-[#0B3A22] px-2.5 py-1 text-[9px] font-bold transition hover:brightness-95">
                    <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-[#0B3A22]"></span><span data-badge-text>Siap Reservasi</span>
                </span>
                <span class="absolute right-3 top-3 rounded-md bg-[#0B3A22] px-2 py-1 text-[9px] font-bold text-white">${esc(u.no)}</span>
                <h3 data-title class="absolute bottom-3 left-3 text-[16px] font-bold text-white">${esc(u.name)}</h3>
            </div>
            <div class="p-4">
                <p class="flex items-center gap-2 text-[10px] font-bold text-slate-800"><span class="text-[#0B3A22]">${ICON(IC.users, 'h-3.5 w-3.5')}</span><span data-cap>Kapasitas: ${esc(u.cap)}</span></p>
                <div data-amen class="mt-3 flex flex-wrap gap-1.5">${u.amen.map(chip).join('')}</div>
                <div class="mt-4 space-y-1.5 rounded-xl bg-slate-100 p-3 text-[10px] text-slate-600">
                    <div class="flex items-start justify-between gap-2"><span class="leading-snug">Tarif Normal<br>(Weekday)</span><b data-rate-normal class="text-right text-[11px] leading-snug text-slate-900">${rp(u.normal)} /<br>malam</b></div>
                    <div class="flex items-start justify-between gap-2"><span class="leading-snug">Tarif Weekend<br>(Jum-Sab)</span><b data-rate-weekend class="text-right text-[11px] leading-snug text-[#7A5230]">${rp(u.weekend)} /<br>malam</b></div>
                </div>
                <div class="mt-4 flex items-center gap-1.5">
                    <button type="button" data-edit class="rounded-lg bg-[#DDE6FB] px-3 py-2 text-[10px] font-bold text-slate-800 transition hover:brightness-95">Edit Unit</button>
                    <button type="button" data-tarif class="rounded-lg bg-[#DDE6FB] px-3 py-2 text-[10px] font-bold text-slate-800 transition hover:brightness-95">Atur Tarif</button>
                    <button type="button" data-booking class="rounded-lg bg-[#0B3A22] px-3 py-2 text-[10px] font-bold text-white transition hover:brightness-95">Detail Booking</button>
                </div>
            </div>`;

        const syncFilterCount = () => { $('[data-filter="all"]').textContent = 'Semua (' + UNITS.length + ')'; };

        // ==========================================
        // FUNGSI TAMBAH UNIT (LANGSUNG KE DATABASE)
        // ==========================================
        const addUnit = () => {
            const name = $('[data-in="name"]').value.trim();
            const cap = $('[data-in="cap"]').value.trim();
            const normal = $('[data-in="normal"]').value;

            if (!name) return toast('Nama unit wajib diisi.');
            if (!normal) return toast('Tarif wajib diisi.');

            toast('Menyimpan ke database...');

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = STORE_URL;

            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = CSRF_TOKEN;
            form.appendChild(csrfInput);

            const addField = (name, value) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = name;
                input.value = value;
                form.appendChild(input);
            };

            addField('nama_cabin', name);
            addField('jenis_cabin', 'suite');
            addField('kapasitas', parseInt(cap) || 2);
            addField('harga_per_malam', normal);
            addField('status', 'tersedia');
            addField('deskripsi', 'Ditambahkan via sistem');

            document.body.appendChild(form);
            form.submit();
        };

        const saveEdit = () => {
            const u = UNITS.find((q) => q.key === current.key);
            const card = unitCard(current.key);
            const name = $('[data-in="name"]').value.trim();
            if (!name) return toast('Nama unit wajib diisi.');
            const cap = $('[data-in="cap"]').value.trim();
            const sub = $('[data-in="sub"]').value.trim();
            const amen = readList();
            const im = $('[data-img]', card);
            const old = im && !im.classList.contains('hidden') ? (im.getAttribute('src') || '') : '';
            const img = pickImage(old);

            u.name = name;
            if (sub) u.sub = sub;
            NAMES[current.key] = name;
            $('[data-title]', card).textContent = name;
            if (cap) $('[data-cap]', card).textContent = 'Kapasitas: ' + cap;
            $('[data-amen]', card).innerHTML = amen.map(chip).join('');
            if (img) { im.src = img; im.alt = name; im.classList.remove('hidden'); }

            closeModal();
            renderTimeline();
            flash(card);
            toast('Unit ' + name + ' diperbarui.');
        };

        $('[data-m-save]').addEventListener('click', () => {
            if (current.mode === 'add') return addUnit();
            if (current.mode === 'edit') return saveEdit();
            const u = UNITS.find((q) => q.key === current.key);
            if (current.mode === 'tarif') {
                const n = Number($('[data-in="normal"]').value), w = Number($('[data-in="weekend"]').value);
                if (!(n > 0) || !(w > 0)) return toast('Tarif harus lebih dari 0.');
                RATES[current.key] = { normal: n, weekend: w };
                const card = unitCard(current.key);
                $('[data-rate-normal]', card).innerHTML = rp(n) + ' /<br>malam';
                $('[data-rate-weekend]', card).innerHTML = rp(w) + ' /<br>malam';
                closeModal();
                return toast('Tarif ' + NAMES[current.key] + ' diperbarui.');
            }
            if (current.mode === 'book') {
                const g = ($('[data-in="guest"]').value || '').trim() || 'Tamu Walk-in';
                const x = $('[data-in="from"]').value, y = $('[data-in="to"]').value;
                if (!x || !y || dayDiff(x, y) < 0) return toast('Tanggal tidak valid.');
                if (x < iso(TODAY)) return toast('Check-in tidak boleh sebelum hari ini.');
                if (clash(u, x, y)) return toast('Tanggal bentrok dengan booking / blok lain.');
                const n = Math.max(dayDiff(x, y), 1);
                u.segs.push({ from: x, to: y, type: 'occupied', walkin: true, title: `${g} (${n} Malam)`, sub: 'Booked: ' + rangeTxt(x, y) + ' • Terisi' });
                if (x < iso(WS) || x > iso(addDays(WS, 6))) WS = new Date(x + 'T00:00:00Z');
                closeModal(); renderTimeline(); updateStats();
                $$('[data-occ]').forEach((el) => flash(el.closest('[data-reveal]')));
                return toast('Booking ' + g + ' • ' + rangeTxt(x, y) + ' tersimpan');
            }
            const backup = u.segs.map((sg) => ({ ...sg }));
            for (let i = 0; i < current.list.length; i++) {
                const sg = current.list[i];
                const x = $(`[data-ef="${i}"]`).value, y = $(`[data-et="${i}"]`).value;
                if (!x || !y || y < x) return toast('Tanggal "' + sg.title + '" tidak valid.');
                if (clash(u, x, y, sg)) { u.segs.forEach((o, j) => Object.assign(o, backup[j])); return toast('Tanggal "' + sg.title + '" bentrok dengan booking lain.'); }
                sg.from = x; sg.to = y;
                if (sg.type === 'occupied') {
                    const n = Math.max(dayDiff(x, y), 1);
                    sg.title = sg.title.replace(/\s*\(\d+ Malam\)$/, '') + ` (${n} Malam)`;
                    sg.sub = 'Booked: ' + rangeTxt(x, y) + ' • Terisi';
                }
                if (sg.type === 'checkout') sg.title = 'Check-out ' + fmtD(y);
            }
            closeModal(); renderTimeline(); updateStats();
            toast('Tanggal booking diperbarui.');
        });

        $('[data-add-unit]').addEventListener('click', () => openModal(null, 'add'));

        let syncing = false;
        $('[data-sync]').addEventListener('click', () => {
            if (syncing) return;
            syncing = true;
            const ic = $('[data-sync-icon]'), txt = $('[data-sync-text]');
            const steps = ['Menghubungkan PMS...', 'Mengecek Smart Lock...', 'Memperbarui kalender...'];
            ic.classList.add('animate-spin');
            steps.forEach((m, i) => setTimeout(() => (txt.textContent = m), i * 700));
            setTimeout(() => {
                ic.classList.remove('animate-spin');
                txt.textContent = 'Sinkronisasi PMS / Smart Lock';
                syncing = false;
                renderTimeline();
                $$('[data-inv], [data-occ], [data-hk-ready]').forEach((el) => flash(el.closest('[data-reveal]')));
                toast('PMS & Smart Lock tersinkron ' + new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }));
            }, steps.length * 700);
        });

        if (LIVE_DEMO) {
            const events = [
                'Smart Lock Unit 01: pintu terkunci',
                'Smart Lock Unit 02: baterai 92%, normal',
                'Housekeeping: Unit 01 selesai dibersihkan',
                'PMS: data reservasi tersinkron',
            ];
            let n = 0;
            setInterval(() => {
                toast(events[n++ % events.length]); bell();
                if (n % 3 === 0) { HK.service = HK.service === 1 ? 0 : 1; HK.ready = HK.total - HK.service; updateStats(); flash(hkCard); }
            }, 12000);
        }

        const io = new IntersectionObserver((es) => es.forEach((en) => {
            if (!en.isIntersecting) return;
            en.target.classList.add('in');
            setTimeout(() => (en.target.style.transitionDelay = ''), 900);
            io.unobserve(en.target);
        }), { threshold: 0.08 });
        $$('[data-reveal]').forEach((el, i) => { el.style.transitionDelay = (i % 4) * 90 + 'ms'; io.observe(el); });

        const bindCard = (card) => {
            const k = card.dataset.unit;
            $('[data-tarif]', card).addEventListener('click', () => openModal(k, 'tarif'));
            $('[data-booking]', card).addEventListener('click', () => openModal(k, 'booking'));
            $('[data-edit]', card).addEventListener('click', () => openModal(k, 'edit'));

            const shine = $('[data-shine]', card);
            card.addEventListener('mousemove', (e) => {
                const r = card.getBoundingClientRect();
                const x = (e.clientX - r.left) / r.width, y = (e.clientY - r.top) / r.height;
                card.style.transition = 'transform .1s ease-out';
                card.style.transform = `perspective(800px) rotateX(${(0.5 - y) * 8}deg) rotateY(${(x - 0.5) * 10}deg) translateY(-4px)`;
                card.style.setProperty('--mx', x * 100 + '%');
                card.style.setProperty('--my', y * 100 + '%');
                shine.style.opacity = 1;
            });
            card.addEventListener('mouseleave', () => {
                card.style.transition = 'transform .5s cubic-bezier(.2,.8,.2,1)';
                card.style.transform = '';
                shine.style.opacity = 0;
            });

            const b = $('[data-badge]', card);
            const txt = $('[data-badge-text]', b), orig = txt.textContent, cls = b.className;
            let busy = false;
            b.addEventListener('click', () => {
                if (busy) return;
                busy = true;
                txt.textContent = 'Membuka kunci...';
                setTimeout(() => {
                    txt.textContent = 'Pintu Terbuka';
                    b.className = cls.replace(/bg-white\/95 text-slate-800|bg-\[#FBD9B0\] text-\[#6B4520\]/, 'bg-[#CDEFD5] text-[#0B3A22]');
                    b.classList.add('jw-pop');
                    toast('Pintu ' + NAMES[k] + ' terbuka, auto-kunci 5 detik');
                }, 800);
                setTimeout(() => { txt.textContent = orig; b.className = cls; busy = false; }, 5800);
            });
        };
        $$('[data-unit]').forEach(bindCard);

        const hkCard = $('[data-hk-ready]').closest('[data-reveal]');
        hkCard.classList.add('cursor-pointer', 'select-none');
        hkCard.title = 'Klik untuk ubah status housekeeping';
        hkCard.addEventListener('click', () => {
            HK.service = HK.service ? 0 : 1;
            HK.ready = HK.total - HK.service;
            updateStats();
            flash(hkCard);
            toast(HK.service ? 'Unit dijadwalkan servis' : 'Semua unit siap');
        });

        const fmtWib = new Intl.DateTimeFormat('en-GB', { timeZone: 'Asia/Jakarta', hourCycle: 'h23', hour: '2-digit', minute: '2-digit', second: '2-digit' });
        const tickCd = () => {
            const g = (t) => Number(fmtWib.formatToParts(new Date()).find((p) => p.type === t).value);
            const left = 14 * 3600 - (g('hour') * 3600 + g('minute') * 60 + g('second'));
            $$('[data-cd]').forEach((el) => {
                el.textContent = left <= 0 ? 'Sudah tiba' : `${Math.floor(left / 3600)}j ${Math.floor((left % 3600) / 60)}m ${String(left % 60).padStart(2, '0')}d lagi`;
            });
        };
        tickCd();
        setInterval(tickCd, 1000);

        document.addEventListener('pointerdown', (e) => {
            const b = e.target.closest('main button, aside button, #modal button');
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

        renderTimeline();
        updateStats();
    });
</script>
@endpush
