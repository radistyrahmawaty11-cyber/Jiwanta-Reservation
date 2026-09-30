@extends('layouts.dashboard')

@section('title', 'Gate Portal - Jiwanta')

@section('content')

@php
    // ---------- Helper ----------
    $ic = fn (string $d, string $c = 'h-4 w-4') =>
        '<svg class="'.$c.'" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="'.$d.'"/></svg>';
    $br = fn (array $lines) => implode('<br>', array_map('e', $lines));

    $p = [
        'cloud'   => 'M7 18a4 4 0 010-8 5 5 0 019.6-1A4.5 4.5 0 0117 18H7z',
        'bell'    => 'M6 8a6 6 0 0112 0c0 7 3 8 3 8H3s3-1 3-8M10 20a2 2 0 004 0',
        'logout'  => 'M15 4h4a1 1 0 011 1v14a1 1 0 01-1 1h-4M10 16l-4-4 4-4M6 12h10',
        'volume'  => 'M11 5L6 9H3v6h3l5 4V5zM15.5 8.5a5 5 0 010 7',
        'headset' => 'M4 14v-2a8 8 0 0116 0v2M4 14h3v5H4zM17 14h3v5h-3zM20 19a3 3 0 01-3 3h-3',
        'swap'    => 'M7 7h13M16 3l4 4-4 4M17 17H4M8 13l-4 4 4 4',
        'lock'    => 'M7 11V8a5 5 0 0110 0v3M5 11h14v9H5z',
        'usercheck'=> 'M16 20v-1a4 4 0 00-4-4H8a4 4 0 00-4 4v1M10 11a4 4 0 100-8 4 4 0 000 8zM17 11l2 2 4-4',
        'qr'      => 'M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h2v2h-2zM18 18h2v2h-2zM14 18h2M18 14h2',
        'tools'   => 'M14.7 6.3a4 4 0 00-5.4 5.4L3 18l3 3 6.3-6.3a4 4 0 005.4-5.4l-2.5 2.5-2.5-2.5 2.5-2.5z',
        'building'=> 'M4 20V9l8-5 8 5v11M9 20v-6h6v6',
        'door'    => 'M6 21V4h9v17M4 21h16M12 12h.01',
        'scan'    => 'M4 8V5a1 1 0 011-1h3M16 4h3a1 1 0 011 1v3M20 16v3a1 1 0 01-1 1h-3M8 20H5a1 1 0 01-1-1v-3M4 12h16',
        'search'  => 'M11 18a7 7 0 100-14 7 7 0 000 14zM21 21l-4.5-4.5',
        'refresh' => 'M4 4v6h6M20 20v-6h-6M20 9A8 8 0 006 6L4 10M4 15a8 8 0 0014 3l2-4',
        'check'   => 'M5 13l4 4L19 7',
        'checkc'  => 'M9 12l2 2 4-4M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'phone'   => 'M5 4h4l2 5-2.5 1.5a11 11 0 005 5L15 13l5 2v4a2 2 0 01-2 2A16 16 0 013 6a2 2 0 012-2z',
        'card'    => 'M3 6h18v12H3zM3 10h18',
        'pin'     => 'M12 21s-7-6-7-11a7 7 0 0114 0c0 5-7 11-7 11zM12 12a2 2 0 100-4 2 2 0 000 4z',
        'printer' => 'M7 8V3h10v5M7 17H4v-7h16v7h-3M7 14h10v7H7z',
        'key'     => 'M15 7a4 4 0 11-3.5 6L3 21v-3l7-7',
        'id'      => 'M4 6h16v12H4zM9 11a2 2 0 100-4 2 2 0 000 4zM6 16c.5-2 5.5-2 6 0M14 10h4M14 13h3',
        'split'   => 'M6 3v6a4 4 0 004 4h4a4 4 0 014 4v4M6 3L3 6M6 3l3 3',
        'register'=> 'M6 3h12v5H6zM4 8h16v13H4zM8 12h2M12 12h2M8 16h8',
        'thermo'  => 'M14 14V5a2 2 0 00-4 0v9a4 4 0 104 0z',
        'download'=> 'M12 4v11m-4-4l4 4 4-4M5 20h14',
        'cash'    => 'M3 7h18v10H3zM12 14a2 2 0 100-4 2 2 0 000 4z',
        'tree'    => 'M12 3l4 6h-2.5l3.5 5h-3l3 5H7l3-5H7l3.5-5H8l4-6z',
    ];

    $mint = 'bg-[#CDEFD5]'; $peach = 'bg-[#FBD9B0]'; $lav = 'bg-[#DDE6FB]'; $soft = 'bg-[#EEF1FD]';
    $card = 'rounded-2xl bg-white shadow-[0_2px_14px_rgba(15,69,39,0.05)]';
    $cap = 'text-[10px] font-bold uppercase leading-snug tracking-wide text-slate-600';

    $nav = [
        'Operasional Masuk' => ['Dashboard Petugas & Gate Scan', 'Monitor Turnstile & Arus Tamu'],
        'Kendala & Log Lapangan' => ['Masalah & Insiden Tiket', 'Shift & Log Aktivitas'],
    ];

    $ok = [$mint.' text-[#0B3A22]', 'bg-[#0B3A22]'];
    $bad = ['bg-[#FAD4D4] text-red-700', 'bg-red-600'];
    $early = [$peach.' text-[#6B4520]', 'bg-[#B26A1F]'];

    $history = [
        ['10:14:15', ['JW-TKT-', '9921'], 'Bima Sakti', '2 Dewasa', ['Tiket', 'Kolam', 'Premier'], 'Gate 1', $lav, ['BERHASIL', 'MASUK'], $ok, false],
        ['10:09:04', ['JW-TKT-', '9919'], 'Rangga Pratama', '1 Dewasa', ['Tiket', 'Kolam', 'Classic'], 'Gate 1', $lav, ['BERHASIL', 'MASUK'], $ok, false],
        ['10:01:22', ['JW-TKT-', '8849'], 'Rendi Pratama', '1 Pax (Sesi Kemarin)', ['Classic', 'Day-Pass'], 'Gate 1', $lav, ['DITOLAK:', 'EXPIRED'], $bad, true],
        ['09:58:10', ['JW-CAB-', '004'], 'Siti Rahmawati', '4 Tamu Resor', ['Pinus', 'Cabin A', '(Glamping)'], 'Gate 2 VIP', $peach, ['EARLY', 'ACC', '(SPV)'], $early, false],
        ['09:50:05', ['JW-TKT-', '9915'], 'Maya Lestari', '2 Dewasa', ['Tiket', 'Kolam', 'Premier'], 'Gate 2 VIP', $peach, ['BERHASIL', 'MASUK'], $ok, false],
    ];

    $issues = [
        ['Siti Rahmawati (JW-CAB-004)', ['Early', 'Check-in'], $peach.' text-[#6B4520]', 'Glamping Pinus Cabin A • 4 Pax',
         'Tamu datang 45 menit sebelum jam 14:00. Room Boy mengabarkan kabin sudah siap dan bersih.', 'Beri Akses Kabin', 'key', true],
        ['Rendi Pratama (Offline WA)', ['Layar', 'Rusak'], $lav, 'HP Layar Pecah • No: 0812-3456-990',
         'QR tidak terbaca scanner. Verifikasi NIK KTP manual cocok dengan invoice Tokopedia Travel.', 'Validasi Identitas KTP', 'id', false],
        ['Budi Santoso (JW-GRP-102)', ['Split', 'Pax'], $mint, 'Rombongan 5 Pax (Baru tiba 3 Pax)',
         '2 orang tertinggal di parkiran bus. Tamu minta izin 3 orang masuk terlebih dahulu.', 'Check-in Parsial (3 Pax)', 'split', false],
    ];

    $checklist = [
        ['2× Gelang RFID Biru', 'Akses Gerbang Onsen'],
        ['2× Voucher Welcome Drink', 'Ambil di Gazebo Resto'],
        ['Handuk & Loker #24, #25', 'Kunci Silinder Onsen'],
    ];
@endphp

<div class="flex min-h-screen w-full bg-[#F8F7FF] text-[#0B3A22]">

    {{-- ================= SIDEBAR (menempel penuh di kiri) ================= --}}
    <aside class="sticky top-0 flex h-screen w-[280px] shrink-0 flex-col border-r border-slate-100 bg-white">
        <div class="pl-[120px] pr-4 pt-7">
            <p class="text-[20px] font-bold leading-tight">Jiwanta Gate<br>Portal</p>
            <p class="mt-1 text-[10px] font-bold uppercase tracking-wide text-slate-700">Petugas &amp; Akses Masuk</p>
        </div>

        <div class="mx-5 mt-4 flex items-center justify-between rounded-full {{ $soft }} px-4 py-2">
            <span class="flex items-center gap-2 text-[12px] font-medium"><span class="h-2 w-2 rounded-full bg-[#0B3A22]"></span> Turnstile Gate</span>
            <span class="rounded-full {{ $mint }} px-2.5 py-0.5 text-[10px] font-bold">Ready</span>
        </div>

        <nav class="mt-8 flex-1 space-y-6 px-5">
            @foreach ($nav as $label => $items)
                <div>
                    <p class="mb-2 px-1 text-[10px] font-bold uppercase tracking-wider text-slate-600">{{ $label }}</p>
                    <ul class="space-y-1">
                        @foreach ($items as $text)
                            <li><a href="#" class="block rounded-xl px-2.5 py-3 text-[14px] font-medium transition hover:bg-[#F3F4FD]">{{ $text }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </nav>

        <div class="m-5 flex items-center justify-between rounded-2xl bg-[#E8ECFB] px-4 py-4">
            <div>
                <p class="text-[12px] font-bold leading-tight">Shift Pagi Gate</p>
                <p class="mt-0.5 text-[12px] leading-tight text-slate-700">07:00 - 15:00 WIB</p>
            </div>
            <button type="button" class="transition hover:text-red-600" aria-label="Keluar">{!! $ic($p['logout'], 'h-5 w-5') !!}</button>
        </div>
    </aside>

    {{-- ================= MAIN ================= --}}
    <div class="min-w-0 flex-1">

        {{-- Topbar --}}
        <header class="sticky top-0 z-20 flex h-16 items-center justify-between bg-[#F8F7FF]/95 px-8 backdrop-blur">
            <div class="flex items-center gap-3">
                <span class="flex items-center gap-2 rounded-full bg-[#E4E8FB] px-4 py-2 text-[12px]">{!! $ic($p['cloud'], 'h-4 w-4') !!} <b class="font-semibold">Ciwidey 18°C</b> <span class="text-slate-600">Kabut Sejuk</span></span>
                <span class="flex items-center gap-2 rounded-full bg-[#E4E8FB] px-4 py-2 text-[12px]">
                    <span class="relative flex h-2 w-2"><span class="absolute h-full w-full animate-ping rounded-full bg-[#0B3A22] opacity-60"></span><span class="relative h-2 w-2 rounded-full bg-[#0B3A22]"></span></span>
                    <b class="font-semibold">Gate Turnstile</b>
                    <span class="rounded-full {{ $mint }} px-2 py-0.5 text-[10px] font-bold">Online</span>
                </span>
            </div>
            <div class="flex items-center gap-5">
                <button type="button" class="relative" aria-label="Notifikasi">{!! $ic($p['bell'], 'h-5 w-5') !!}<span class="absolute -right-0.5 -top-0.5 h-2 w-2 rounded-full bg-red-600"></span></button>
                <span class="h-8 w-px bg-slate-200"></span>
                <div class="text-right">
                    <p class="text-[13px] font-bold leading-tight">Rian Hidayat</p>
                    <p class="text-[10px] font-semibold uppercase leading-tight tracking-wide text-[#8B5E34]">Staf Gate Scanner STF-042</p>
                </div>
            </div>
        </header>

        <main class="space-y-5 px-8 pb-10 pt-2">

            {{-- Header pos --}}
            <section class="{{ $card }} flex items-start justify-between gap-6 p-6">
                <div>
                    <p class="flex items-center gap-3">
                        <span class="inline-flex items-center gap-2 rounded-full bg-[#0B3A22] px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-wide text-white">
                            <span class="h-1.5 w-1.5 rounded-full bg-[#B5F0BE]"></span> Pos Scanner Aktif
                        </span>
                        <span class="text-[13px] text-slate-700">STF-042 • Rian Hidayat •</span>
                    </p>
                    <p class="mt-3 text-[13px] font-semibold">Pos Turnstile Utama Gerbang A &amp; B</p>
                    <div class="mt-1 flex items-center gap-4">
                        <h1 class="text-[26px] font-bold leading-tight">Dashboard Validasi &amp; Kontrol<br>Pintu Masuk</h1>
                        <span class="flex items-center gap-1.5 rounded-xl {{ $mint }} px-3 py-1.5 text-[10px] font-semibold leading-tight">
                            {!! $ic($p['cloud'], 'h-4 w-4 shrink-0') !!}<span>Cloud Sync: <span data-sync>2</span><br>dtk lalu</span>
                        </span>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3 pt-1">
                    <button type="button" data-beeper class="flex items-center justify-center gap-2 rounded-full bg-[#E4E8FB] px-5 py-2.5 text-[12px] font-semibold transition hover:bg-[#d6dcf7] active:scale-95">{!! $ic($p['volume'], 'h-4 w-4') !!}<span>Beeper Audio: <span data-beeper-state>On</span></span></button>
                    <button type="button" class="flex items-center justify-center gap-2 rounded-full bg-[#E4E8FB] px-5 py-2.5 text-[12px] font-semibold transition hover:bg-[#d6dcf7] active:scale-95">{!! $ic($p['headset'], 'h-4 w-4') !!} Panggil SPV (Radio)</button>
                    <button type="button" class="flex items-center justify-center gap-2 rounded-full bg-[#E4E8FB] px-5 py-2.5 text-[12px] font-semibold transition hover:bg-[#d6dcf7] active:scale-95">{!! $ic($p['swap'], 'h-4 w-4') !!} Ganti Pos Gate</button>
                    <button type="button" class="flex items-center justify-center gap-2 rounded-full bg-[#C0121F] px-5 py-2.5 text-[12px] font-semibold text-white transition hover:bg-[#a30f1a] active:scale-95">{!! $ic($p['lock'], 'h-4 w-4') !!} Buka Gate Darurat</button>
                </div>
            </section>

            {{-- Statistik --}}
            <div class="grid grid-cols-5 items-start gap-4">
                <div class="{{ $card }} p-5">
                    <div class="flex items-start justify-between"><p class="{{ $cap }}">Total Gate-In</p><span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $soft }}">{!! $ic($p['usercheck'], 'h-5 w-5') !!}</span></div>
                    <p class="mt-1 text-[32px] font-bold leading-none"><span data-gatein>182</span> <span class="text-[14px] font-normal text-slate-600">/ 250</span></p>
                    <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-[#E3E6F3]"><div data-gatein-bar style="width: 72.8%" class="h-full rounded-full bg-[#0B3A22] transition-[width] duration-1000 ease-out"></div></div>
                    <p class="mt-1.5 flex justify-between text-[10px]"><span class="leading-tight text-slate-700">Throughput Sesi<br>Pagi</span><b data-gatein-pct>72.8%</b></p>
                </div>

                <div class="{{ $card }} p-5">
                    <div class="flex items-start justify-between"><p class="{{ $cap }}">QR Valid<br>Terverifikasi</p><span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $mint }}">{!! $ic($p['qr'], 'h-5 w-5') !!}</span></div>
                    <p class="mt-1 text-[32px] font-bold leading-none" data-qr>126</p>
                    <p class="mt-3 flex items-start justify-between gap-1 text-[10px] leading-tight text-slate-700">
                        <span class="flex items-start gap-1">{!! $ic($p['checkc'], 'h-3.5 w-3.5 shrink-0') !!} 100% Validasi<br>Otomatis</span><span class="text-right">Avg:<br>0.6 dtk</span>
                    </p>
                </div>

                <div class="{{ $card }} p-5">
                    <div class="flex items-start justify-between"><p class="{{ $cap }}">Insiden /<br>Verifikasi Manual</p><span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $peach }} text-[#8B5E34]">{!! $ic($p['tools'], 'h-5 w-5') !!}</span></div>
                    <p class="mt-1 text-[32px] font-bold leading-none"><span data-cases class="text-[#8B5E34]">4</span> <span class="text-[14px] font-normal text-slate-600">Kasus</span></p>
                    <p class="mt-3 text-[10px] leading-snug text-slate-700">2 Layar Rusak • 1 Early • 1<br>Unpaid</p>
                </div>

                <div class="{{ $card }} p-5">
                    <div class="flex items-start justify-between"><p class="{{ $cap }}">Sisa Kuota<br>Onsen</p><span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $lav }}">{!! $ic($p['building'], 'h-5 w-5') !!}</span></div>
                    <p class="mt-1 text-[32px] font-bold leading-none"><span data-kuota>45</span> <span class="text-[14px] font-normal text-slate-600">Pax</span></p>
                    <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-[#E3E6F3]"><div data-kuota-bar style="width: 85%" class="h-full rounded-full bg-[#7A5230] transition-[width] duration-1000 ease-out"></div></div>
                    <p class="mt-1.5 flex justify-between text-[10px] leading-tight"><span class="text-slate-700">Kapasitas Kolam<br>300</span><b data-kuota-left class="text-right text-[#8B5E34]">Tersisa<br>15%</b></p>
                </div>

                <div class="{{ $card }} p-5">
                    <div class="flex items-start justify-between"><p class="{{ $cap }}">Status Hardware<br>Turnstile</p><span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $soft }}">{!! $ic($p['door'], 'h-5 w-5') !!}</span></div>
                    <p class="mt-1 flex items-start gap-2 text-[20px] font-bold leading-tight"><span class="mt-2 h-2 w-2 shrink-0 rounded-full bg-[#0B3A22]"></span>G1 &amp; G2<br>Normal</p>
                    <div class="mt-3 grid grid-cols-2 gap-2 text-[10px] leading-tight text-slate-700"><span>G1 Tripod:<br>142 Scan</span><span>G2 Flap VIP:<br>40 Scan</span></div>
                </div>
            </div>

            {{-- Grid utama --}}
            <div class="grid grid-cols-[minmax(0,2fr)_minmax(0,1fr)] items-start gap-5">

                {{-- ========== KIRI ========== --}}
                <div class="space-y-5">

                    {{-- Scanner --}}
                    <section class="{{ $card }} p-6">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <div class="flex items-center gap-3">
                                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#0B3A22] text-white">{!! $ic($p['scan'], 'h-5 w-5') !!}</span>
                                <div>
                                    <h2 class="text-[19px] font-bold leading-tight">Live Optical Scanner Pos A</h2>
                                    <p class="text-[11px] text-slate-600">Sensor CMOS 60FPS • Sudut Deteksi 120°</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="rounded-lg {{ $lav }} px-3 py-1.5 text-[10px] font-semibold">Lampu Bantu: Nyala</span>
                                <span class="rounded-full {{ $mint }} px-3 py-1.5 text-[10px] font-bold">Siap Deteksi</span>
                            </div>
                        </div>

                        <div class="mt-5 grid grid-cols-2 gap-5">
                            {{-- Kamera --}}
                            <div class="relative h-[270px] overflow-hidden rounded-2xl bg-gradient-to-br from-[#0f3a24] via-[#1c4a30] to-[#0a2a1a]">
                                <img src="{{ asset('images/scanner-feed.jpg') }}" alt="" onerror="this.remove()" class="absolute inset-0 h-full w-full object-cover opacity-60">
                                <span class="absolute left-3 top-3 flex items-center gap-1.5 rounded-full bg-black/40 px-3 py-1 text-[10px] font-bold text-white"><span class="h-2 w-2 animate-pulse rounded-full bg-red-500"></span> REC FEED #01</span>
                                <span class="absolute right-3 top-3 rounded-full bg-black/40 px-3 py-1 text-[10px] font-semibold text-white">Turnstile A1 - Tripod</span>

                                <div class="absolute left-1/2 top-1/2 h-[46%] w-[46%] -translate-x-1/2 -translate-y-1/2">
                                    <span class="absolute left-0 top-0 h-5 w-5 rounded-tl-lg border-l-2 border-t-2 border-white/90"></span>
                                    <span class="absolute right-0 top-0 h-5 w-5 rounded-tr-lg border-r-2 border-t-2 border-white/90"></span>
                                    <span class="absolute bottom-0 left-0 h-5 w-5 rounded-bl-lg border-b-2 border-l-2 border-white/90"></span>
                                    <span class="absolute bottom-0 right-0 h-5 w-5 rounded-br-lg border-b-2 border-r-2 border-white/90"></span>
                                    <span data-scanline class="absolute left-[-14%] right-[-14%] top-1/2 h-0.5 rounded-full bg-white shadow-[0_0_14px_4px_rgba(255,255,255,0.75)]"></span>
                                </div>

                                <p class="absolute bottom-3 left-3 text-[10px] font-semibold leading-tight text-white">Arahkan QR Tiket ke<br>Kamera</p>
                                <p class="absolute bottom-3 right-3 text-right text-[10px] font-semibold leading-tight text-white">Autofokus<br>Terkunci</p>
                            </div>

                            {{-- Input alternatif --}}
                            <div class="rounded-2xl {{ $soft }} p-5">
                                <div class="flex items-start justify-between">
                                    <p class="text-[13px] font-bold">Input Alternatif Tiket</p>
                                    <span class="rounded-md bg-white px-2 py-0.5 text-[9px] font-bold text-slate-600">Hotkey: F2</span>
                                </div>
                                <p class="mt-1.5 text-[11px] leading-snug text-slate-600">Gunakan nomor transaksi jika QR handphone pengunjung pecah, buram, atau baterai habis.</p>
                                <p class="mt-3 text-[9px] font-bold uppercase tracking-wide">Kode Booking / No. WhatsApp</p>
                                <div class="mt-1.5 flex items-center gap-2 rounded-xl bg-white px-3 py-2.5 ring-2 ring-transparent transition focus-within:ring-[#0B3A22]/30" data-input-wrap>
                                    {!! $ic($p['search'], 'h-4 w-4 shrink-0 text-slate-500') !!}
                                    <input id="code-input" type="text" value="JW-20260920-001" class="w-full min-w-0 bg-transparent text-[12px] font-semibold outline-none placeholder:text-slate-400" placeholder="JW-20260920-000">
                                    <button type="button" id="code-reset" class="text-[10px] font-semibold text-slate-500 transition hover:text-[#0B3A22]">Reset</button>
                                </div>
                                <div class="mt-2.5 flex flex-wrap gap-2">
                                    @foreach ([['JW-001', 'JW-001 (Fadillah)'], ['JW-9921', 'JW-9921 (Bima)'], ['JW-004', 'JW-004 (Cabin A)']] as [$val, $lbl])
                                        <button type="button" data-chip="{{ $val }}" class="rounded-lg bg-white px-2.5 py-1.5 text-[10px] font-medium transition hover:bg-[#f3f4fd] active:scale-95">{{ $lbl }}</button>
                                    @endforeach
                                </div>
                                <div class="mt-4 flex items-center gap-2">
                                    <button type="button" id="validate" class="flex flex-1 items-center justify-center gap-2 rounded-xl bg-[#0B3A22] px-4 py-3 text-[12px] font-bold text-white transition hover:bg-[#124c2f] active:scale-95">{!! $ic($p['checkc'], 'h-4 w-4') !!} Validasi Kode (Enter)</button>
                                    <button type="button" id="code-clear" class="flex h-11 w-11 items-center justify-center rounded-xl {{ $lav }} transition hover:bg-[#cfdaf7] active:scale-95" aria-label="Muat ulang">{!! $ic($p['refresh'], 'h-4 w-4') !!}</button>
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- Tamu valid --}}
                    <section class="overflow-hidden rounded-2xl border-t-[5px] border-[#0B3A22] bg-white p-6 shadow-[0_2px_14px_rgba(15,69,39,0.05)]">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-start gap-4">
                                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl {{ $mint }}">{!! $ic($p['checkc'], 'h-6 w-6') !!}</span>
                                <div>
                                    <p class="flex items-center gap-3"><span class="text-[24px] font-bold leading-tight">Ahmad Fadillah</span><span class="rounded-md bg-[#0B3A22] px-2.5 py-1 text-[10px] font-bold text-white">VALID • 2 PAX</span></p>
                                    <p class="mt-1 max-w-[380px] text-[12px] leading-snug text-slate-700">Booking ID: <b>JW-20260920-001</b> • Check-in: 10:14:22 WIB di Gerbang 1</p>
                                </div>
                            </div>
                            <span class="rounded-xl {{ $lav }} px-4 py-2.5 text-[12px] font-medium leading-tight">Tiket Premier<br>Sanctuary</span>
                        </div>

                        <div class="mt-5 rounded-2xl {{ $soft }} p-4">
                            <div class="flex items-center justify-between">
                                <p class="flex items-center gap-2 text-[12px] font-bold">{!! $ic($p['check'], 'h-4 w-4') !!} Checklist Fisik Petugas (Serahkan Langsung ke Pengunjung):</p>
                                <span class="text-[10px] font-bold text-[#8B5E34]">Wajib Sebelum Tamu Masuk</span>
                            </div>
                            <div class="mt-3 grid grid-cols-3 gap-3">
                                @foreach ($checklist as [$t, $s])
                                    <label class="flex cursor-pointer items-start gap-3 rounded-xl bg-white p-3.5 transition hover:shadow-sm">
                                        <input type="checkbox" checked data-check class="peer sr-only">
                                        <span class="mt-0.5 flex h-4 w-4 shrink-0 items-center justify-center rounded border-2 border-slate-300 bg-white text-transparent transition peer-checked:border-[#0B3A22] peer-checked:bg-[#0B3A22] peer-checked:text-white peer-focus-visible:ring-2 peer-focus-visible:ring-[#0B3A22]/40">{!! $ic($p['check'], 'h-3 w-3') !!}</span>
                                        <span><b class="block text-[12px] leading-tight">{{ $t }}</b><span class="mt-0.5 block text-[11px] leading-tight text-slate-600">{{ $s }}</span></span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-5 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-6 text-[11px] leading-tight text-slate-700">
                                <span class="flex items-center gap-2">{!! $ic($p['phone'], 'h-4 w-4') !!}<span>+62 812-<br>9844-xxxx</span></span>
                                <span class="flex items-center gap-2">{!! $ic($p['card'], 'h-4 w-4') !!}<span>Lunas<br>via<br>QRIS</span></span>
                                <span class="flex items-center gap-2">{!! $ic($p['pin'], 'h-4 w-4') !!}<b class="text-[#0B3A22]">Parkir<br>A-12</b></span>
                            </div>
                            <div class="flex items-stretch gap-3">
                                <button type="button" class="flex items-center gap-2 rounded-2xl {{ $lav }} px-5 py-3 text-left text-[12px] font-semibold leading-tight transition hover:bg-[#cfdaf7] active:scale-95">{!! $ic($p['printer'], 'h-4 w-4') !!}<span>Cetak<br>Slip<br>Fisik</span></button>
                                <button type="button" class="rounded-2xl {{ $lav }} px-5 py-3 text-center text-[12px] font-semibold leading-tight transition hover:bg-[#cfdaf7] active:scale-95">Batalkan<br>Check-in</button>
                                <button type="button" id="open-gate" class="flex items-center gap-2 rounded-2xl bg-[#0B3A22] px-6 py-3 text-left text-[12px] font-bold leading-tight text-white transition hover:bg-[#124c2f] active:scale-95 disabled:cursor-not-allowed disabled:opacity-40">{!! $ic($p['checkc'], 'h-4 w-4') !!}<span data-gate-label>Buka<br>Palang<br>Gate 1</span></button>
                            </div>
                        </div>
                    </section>

                    {{-- Riwayat --}}
                    <section class="{{ $card }} p-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <h2 class="text-[19px] font-bold leading-tight">Riwayat Scan Gate Real-Time</h2>
                                <p class="mt-1 text-[11px] text-slate-600">Pencatatan otomatis dari seluruh turnstile aktif sesi saat ini</p>
                            </div>
                            <div class="flex items-center gap-3 text-[10px] leading-tight text-slate-600">
                                <span>Filter<br>Gerbang:</span>
                                <span class="rounded-lg {{ $soft }} px-4 py-2 text-[11px] font-bold text-[#0B3A22]">Semua Pos Gate (A &amp; B)</span>
                            </div>
                        </div>

                        <div class="mt-5 grid grid-cols-[76px_82px_minmax(0,1.2fr)_minmax(0,1fr)_84px_120px_84px] gap-2 rounded-lg {{ $soft }} px-4 py-3.5 text-[10px] font-bold uppercase leading-snug tracking-wide text-slate-600">
                            <span>Waktu</span><span>Kode<br>Booking</span><span>Nama<br>Tamu &amp; Pax</span><span>Paket<br>Layanan</span><span>Pos</span><span>Status<br>Validasi</span><span class="text-center">Aksi</span>
                        </div>

                        <div id="history">
                            @foreach ($history as [$time, $code, $name, $pax, $pkg, $pos, $posBg, $st, $stc, $rej])
                                <div data-scan-row class="grid grid-cols-[76px_82px_minmax(0,1.2fr)_minmax(0,1fr)_84px_120px_84px] items-center gap-2 border-b border-[#E8ECFB] px-4 py-4 transition last:border-0 {{ $rej ? 'bg-[#FEF1EF]' : 'hover:bg-[#F8F9FE]' }}">
                                    <span data-s="time" class="font-mono text-[12px] font-bold {{ $rej ? 'text-red-700' : ($time === '09:58:10' ? 'text-[#B26A1F]' : '') }}">{{ $time }}</span>
                                    <span data-s="code" class="font-mono text-[11px] font-bold leading-snug {{ $rej ? 'text-red-700' : '' }}">{!! $br($code) !!}</span>
                                    <span><b data-s="name" class="block text-[12px] leading-tight {{ $rej ? '' : '' }}">{{ $name }}</b><span data-s="pax" class="mt-0.5 block text-[10px] leading-tight {{ $rej ? 'font-semibold text-red-700' : 'text-slate-600' }}">{{ $pax }}</span></span>
                                    <span data-s="pkg" class="text-[12px] leading-snug text-slate-800">{!! $br($pkg) !!}</span>
                                    <span><span class="inline-block rounded-md {{ $posBg }} px-2 py-1 text-[10px] font-bold">{{ $pos }}</span></span>
                                    <span><span class="inline-flex items-center gap-1.5 rounded-full {{ $stc[0] }} px-3 py-1.5 text-[10px] font-bold leading-tight"><span class="h-1.5 w-1.5 shrink-0 rounded-full {{ $stc[1] }}"></span>{!! $br($st) !!}</span></span>
                                    @if ($rej)
                                        <button type="button" class="justify-self-center rounded-lg {{ $lav }} px-3 py-1.5 text-[10px] font-bold transition hover:bg-[#cfdaf7] active:scale-95">Investigasi</button>
                                    @else
                                        <button type="button" class="justify-self-center text-slate-600" aria-label="Aksi"><svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg></button>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </section>
                </div>

                {{-- ========== KANAN ========== --}}
                <div class="space-y-5">

                    {{-- Antrean kendala --}}
                    <section class="{{ $card }} p-5">
                        <div class="mb-4 flex items-start justify-between gap-2">
                            <h2 class="flex items-start gap-2 text-[19px] font-bold leading-tight"><span class="mt-2 h-2 w-2 shrink-0 rounded-full bg-[#7A5230]"></span>Antrean<br>Kendala Gate</h2>
                            <span class="rounded-xl {{ $peach }} px-3 py-1.5 text-center text-[10px] font-bold leading-tight text-[#6B4520]"><span data-issues>3</span> Butuh<br>Penanganan</span>
                        </div>
                        <div class="space-y-3">
                            @foreach ($issues as [$name, $tag, $tagBg, $sub, $desc, $btn, $icon, $twin])
                                <div data-issue class="overflow-hidden rounded-2xl {{ $soft }} p-4 transition-all duration-500 ease-out">
                                    <div class="flex items-start justify-between gap-2">
                                        <p class="text-[12px] font-bold leading-snug">{{ $name }}</p>
                                        <span class="shrink-0 rounded-lg {{ $tagBg }} px-2 py-1 text-center text-[9px] font-bold leading-tight">{!! $br($tag) !!}</span>
                                    </div>
                                    <p class="mt-1 text-[11px] text-slate-700">{{ $sub }}</p>
                                    <p class="mt-2 text-[11px] leading-snug text-slate-600">{{ $desc }}</p>
                                    <div class="mt-3 flex items-center gap-2">
                                        <button type="button" data-resolve class="flex flex-1 items-center justify-center gap-1.5 rounded-xl {{ $twin ? 'bg-[#0B3A22] text-white hover:bg-[#124c2f]' : $lav.' hover:bg-[#cfdaf7]' }} px-3 py-2.5 text-[11px] font-bold transition active:scale-95">{!! $ic($p[$icon], 'h-3.5 w-3.5') !!} {{ $btn }}</button>
                                        @if ($twin)
                                            <button type="button" data-resolve class="rounded-xl {{ $lav }} px-4 py-2.5 text-[11px] font-semibold transition hover:bg-[#cfdaf7] active:scale-95">Tolak</button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <p id="issues-empty" class="mt-3 hidden rounded-xl {{ $soft }} py-6 text-center text-[11px] font-medium text-slate-600">Semua kendala sudah ditangani.</p>
                    </section>

                    {{-- Kasir --}}
                    <section class="{{ $card }} p-5">
                        <div class="mb-4 flex items-start justify-between">
                            <h2 class="flex items-start gap-2 text-[19px] font-bold leading-tight"><span class="mt-1">{!! $ic($p['register'], 'h-5 w-5') !!}</span>Kasir Cepat<br>Walk-In</h2>
                            <span class="text-right text-[10px] leading-tight text-slate-600">Beli di Loket<br>Gate</span>
                        </div>
                        <div class="space-y-2">
                            @foreach ([['Tiket Kolam Classic', 'Rp 45.000 / pax', 45000, 1], ['Tiket Premier Onsen', 'Rp 85.000 / pax<br>(Voucher Minum)', 85000, 0]] as $i => [$n, $price, $val, $init])
                                <div class="flex items-center justify-between rounded-2xl {{ $soft }} px-4 py-3" data-line data-price="{{ $val }}">
                                    <div><p class="text-[12px] font-bold leading-tight">{{ $n }}</p><p class="mt-0.5 text-[10px] leading-tight text-slate-600">{!! $price !!}</p></div>
                                    <div class="flex items-center gap-3">
                                        <button type="button" data-step="-1" class="flex h-7 w-7 items-center justify-center rounded-full bg-white text-[14px] font-bold shadow-sm transition hover:bg-[#f3f4fd] active:scale-90">−</button>
                                        <span data-qty class="w-3 text-center text-[13px] font-bold">{{ $init }}</span>
                                        <button type="button" data-step="1" class="flex h-7 w-7 items-center justify-center rounded-full bg-white text-[14px] font-bold shadow-sm transition hover:bg-[#f3f4fd] active:scale-90">+</button>
                                    </div>
                                </div>
                            @endforeach
                            <div class="flex items-center justify-between rounded-2xl {{ $soft }} px-4 py-3.5">
                                <span class="text-[12px] text-slate-700">Total Tagihan:</span>
                                <b data-total class="text-[19px]">Rp 45.000</b>
                            </div>
                        </div>
                        <div class="mt-3 grid grid-cols-2 gap-3">
                            <button type="button" data-pay class="flex items-center justify-center gap-2 rounded-2xl {{ $lav }} px-3 py-3.5 text-[11px] font-bold transition hover:bg-[#cfdaf7] active:scale-95">{!! $ic($p['qr'], 'h-4 w-4') !!} QRIS Instan</button>
                            <button type="button" data-pay class="flex items-center justify-center gap-2 rounded-2xl bg-[#0B3A22] px-3 py-3.5 text-left text-[11px] font-bold leading-tight text-white transition hover:bg-[#124c2f] active:scale-95">{!! $ic($p['cash'], 'h-4 w-4 shrink-0') !!}<span>Tunai &amp; Buka<br>Gate</span></button>
                        </div>
                    </section>

                    {{-- Kondisi --}}
                    <section class="{{ $card }} p-5">
                        <div class="mb-4 flex items-start justify-between">
                            <h2 class="flex items-start gap-2 text-[19px] font-bold leading-tight"><span class="mt-1">{!! $ic($p['thermo'], 'h-5 w-5') !!}</span>Kondisi Kolam &amp;<br>Cuaca</h2>
                            <span class="text-right text-[10px] leading-tight text-slate-600">Sensor IoT<br>Aktif</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="rounded-2xl {{ $soft }} p-4"><p class="text-[10px] font-medium leading-tight text-slate-700">Suhu Air Alami</p><p class="mt-1 text-[24px] font-bold leading-tight"><span data-temp>39.5</span>°C</p><p class="text-[10px] font-semibold text-emerald-700">Sangat Nyaman</p></div>
                            <div class="rounded-2xl {{ $soft }} p-4"><p class="text-[10px] font-medium leading-tight text-slate-700">Kadar Belerang<br>(pH)</p><p class="mt-1 text-[24px] font-bold leading-tight"><span data-ph>6.8</span> pH</p><p class="text-[10px] font-semibold text-emerald-700">Aman &amp; Seimbang</p></div>
                            <div class="rounded-2xl {{ $soft }} p-4"><p class="text-[10px] font-medium leading-tight text-slate-700">Suhu Udara Luar</p><p class="mt-1 text-[24px] font-bold leading-tight">18°C</p><p class="text-[10px] leading-tight text-slate-600">Kabut Lembut<br>Ciwidey</p></div>
                            <div class="rounded-2xl {{ $soft }} p-4"><p class="text-[10px] font-medium leading-tight text-slate-700">Kepadatan Area</p><p class="mt-1 text-[24px] font-bold leading-tight"><span data-density>60.6</span>%</p><p class="text-[10px] leading-tight text-slate-600"><span data-density-text>182</span> dari 300 Tamu</p></div>
                        </div>
                        <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-4 text-[10px] leading-tight">
                            <span class="flex items-center gap-2 text-slate-700"><span class="h-1.5 w-1.5 rounded-full bg-[#0B3A22]"></span>Mode Cadangan Offline:<br>Siap</span>
                            <button type="button" class="flex items-center gap-1.5 font-semibold transition hover:text-[#8B5E34]">{!! $ic($p['download'], 'h-3.5 w-3.5') !!}<span class="text-left">Unduh Log<br>Shift</span></button>
                        </div>
                    </section>
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
        const LIVE_DEMO = true; // ganti dengan polling / Laravel Echo untuk data asli
        const $ = (s, r = document) => r.querySelector(s);
        const $$ = (s, r = document) => r.querySelectorAll(s);
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

        // ---------- State & render ----------
        const S = { gatein: 182, qr: 126, kuota: 45 };
        const GATE_CAP = 250, POOL_CAP = 300;

        const render = () => {
            animate($('[data-gatein]'), S.gatein);
            animate($('[data-qr]'), S.qr);
            animate($('[data-kuota]'), S.kuota);
            const pct = (S.gatein / GATE_CAP) * 100;
            $('[data-gatein-bar]').style.width = Math.min(pct, 100) + '%';
            $('[data-gatein-pct]').textContent = pct.toFixed(1) + '%';
            $('[data-kuota-bar]').style.width = (100 - (S.kuota / POOL_CAP) * 100) + '%';
            $('[data-kuota-left]').innerHTML = 'Tersisa<br>' + Math.round((S.kuota / POOL_CAP) * 100) + '%';
            $('[data-density]').textContent = (Math.floor((S.gatein / POOL_CAP) * 1000) / 10).toFixed(1);
            $('[data-density-text]').textContent = S.gatein;
        };

        ['[data-gatein]', '[data-qr]', '[data-kuota]'].forEach((s) => ($(s).dataset.val = 0));
        $$('[data-gatein-bar], [data-kuota-bar]').forEach((bar) => {
            const w = bar.style.width;
            bar.style.width = '0%';
            requestAnimationFrame(() => requestAnimationFrame(() => (bar.style.width = w)));
        });
        render();

        // ---------- Garis scan kamera ----------
        const line = $('[data-scanline]');
        if (line && !reduce && line.animate) {
            line.animate([{ top: '6%' }, { top: '94%' }], { duration: 2200, iterations: Infinity, direction: 'alternate', easing: 'ease-in-out' });
        }

        // ---------- Cloud sync & sensor IoT ----------
        let sync = 2;
        setInterval(() => { sync = sync >= 6 ? 1 : sync + 1; $('[data-sync]').textContent = sync; }, 1000);
        setInterval(() => {
            $('[data-temp]').textContent = (39.5 + (Math.random() - 0.5) * 0.4).toFixed(1);
            $('[data-ph]').textContent = (6.8 + (Math.random() - 0.5) * 0.2).toFixed(1);
        }, 3500);

        // ---------- Beeper ----------
        let beeper = true;
        $('[data-beeper]').addEventListener('click', () => {
            beeper = !beeper;
            $('[data-beeper-state]').textContent = beeper ? 'On' : 'Off';
        });

        // ---------- Riwayat scan ----------
        const history = $('#history');
        const template = $('[data-scan-row]').cloneNode(true);
        const nowWIB = () => new Date().toLocaleTimeString('id-ID', { hour12: false, timeZone: 'Asia/Jakarta' }).replace(/\./g, ':');

        const addScan = (code, name, pax, pkg) => {
            const row = template.cloneNode(true);
            row.querySelector('[data-s=time]').textContent = nowWIB();
            row.querySelector('[data-s=code]').innerHTML = code;
            row.querySelector('[data-s=name]').textContent = name;
            row.querySelector('[data-s=pax]').textContent = pax;
            row.querySelector('[data-s=pkg]').innerHTML = pkg;
            if (!reduce) { row.classList.add('opacity-0', '-translate-y-2'); }
            history.prepend(row);
            requestAnimationFrame(() => requestAnimationFrame(() => row.classList.remove('opacity-0', '-translate-y-2')));
            while (history.children.length > 6) history.lastElementChild.remove();
            S.gatein = Math.min(GATE_CAP, S.gatein + (parseInt(pax) || 1));
            S.qr += 1;
            render();
        };

        // ---------- Input alternatif ----------
        const input = $('#code-input');
        const names = { 'JW-001': ['Ahmad Fadillah', '2 Dewasa'], 'JW-9921': ['Bima Sakti', '2 Dewasa'], 'JW-004': ['Siti Rahmawati', '4 Tamu Resor'] };
        $$('[data-chip]').forEach((c) => c.addEventListener('click', () => { input.value = c.dataset.chip; input.focus(); }));
        $('#code-reset').addEventListener('click', () => { input.value = ''; input.focus(); });
        $('#code-clear').addEventListener('click', () => { input.value = 'JW-20260920-001'; });

        const validate = () => {
            const v = input.value.trim().toUpperCase();
            const wrap = $('[data-input-wrap]');
            if (!v) {
                wrap.classList.add('ring-red-400');
                setTimeout(() => wrap.classList.remove('ring-red-400'), 1000);
                return;
            }
            const [name, pax] = names[v] || ['Tamu Terverifikasi', '1 Dewasa'];
            addScan(v.replace(/(JW-[A-Z]+-?)/, '$1<br>'), name, pax, 'Tiket<br>Kolam<br>Classic');
        };
        $('#validate').addEventListener('click', validate);
        input.addEventListener('keydown', (e) => { if (e.key === 'Enter') validate(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'F2') { e.preventDefault(); input.focus(); input.select(); } });

        // ---------- Checklist fisik -> buka palang ----------
        const gateBtn = $('#open-gate');
        const checks = $$('[data-check]');
        const syncGate = () => { gateBtn.disabled = ![...checks].every((c) => c.checked); };
        checks.forEach((c) => c.addEventListener('change', syncGate));
        syncGate();
        gateBtn.addEventListener('click', () => {
            const label = $('[data-gate-label]');
            const old = label.innerHTML;
            label.innerHTML = 'Gate 1<br>Terbuka<br>&nbsp;';
            gateBtn.disabled = true;
            addScan('JW-TKT-<br>9922', 'Ahmad Fadillah', '2 Dewasa', 'Tiket<br>Kolam<br>Premier');
            setTimeout(() => { label.innerHTML = old; syncGate(); }, 2200);
        });

        // ---------- Antrean kendala ----------
        let issues = 3, cases = 4;
        $$('[data-resolve]').forEach((btn) => btn.addEventListener('click', () => {
            const card = btn.closest('[data-issue]');
            if (!card || card.dataset.done) return;
            card.dataset.done = '1';
            card.style.maxHeight = card.offsetHeight + 'px';
            requestAnimationFrame(() => { card.classList.add('opacity-0', 'scale-95', '!p-0'); card.style.maxHeight = '0px'; });
            setTimeout(() => {
                card.remove();
                if (!$('[data-issue]')) $('#issues-empty').classList.remove('hidden');
            }, 500);
            issues = Math.max(0, issues - 1);
            cases = Math.max(0, cases - 1);
            $('[data-issues]').textContent = issues;
            $('[data-cases]').textContent = cases;
        }));

        // ---------- Kasir ----------
        const lines = [...$$('[data-line]')];
        const total = () => lines.reduce((sum, l) => sum + Number(l.dataset.price) * Number($('[data-qty]', l).textContent), 0);
        const showTotal = () => ($('[data-total]').textContent = 'Rp ' + fmt(total()));
        lines.forEach((l) => $$('[data-step]', l).forEach((b) => b.addEventListener('click', () => {
            const q = $('[data-qty]', l);
            q.textContent = Math.max(0, Math.min(20, Number(q.textContent) + Number(b.dataset.step)));
            showTotal();
        })));
        $$('[data-pay]').forEach((b) => b.addEventListener('click', () => {
            const [classic, premier] = lines.map((l) => Number($('[data-qty]', l).textContent));
            if (classic + premier === 0) return;
            S.gatein = Math.min(GATE_CAP, S.gatein + classic + premier);
            S.kuota = Math.max(0, S.kuota - premier);
            render();
            addScan('JW-WLK-<br>' + Math.floor(1000 + Math.random() * 9000), 'Walk-in Loket', (classic + premier) + ' Dewasa', premier ? 'Tiket<br>Premier<br>Onsen' : 'Tiket<br>Kolam<br>Classic');
            lines.forEach((l, i) => ($('[data-qty]', l).textContent = i === 0 ? 1 : 0));
            showTotal();
        }));

        // ---------- Simulasi scan masuk otomatis ----------
        if (LIVE_DEMO) {
            const guests = ['Dewi Anggraeni', 'Fikri Ramadhan', 'Laras Wulandari', 'Yoga Prasetyo', 'Anisa Putri'];
            setInterval(() => {
                if (S.gatein >= GATE_CAP) return;
                addScan('JW-TKT-<br>' + Math.floor(9000 + Math.random() * 999), guests[Math.floor(Math.random() * guests.length)], '1 Dewasa', 'Tiket<br>Kolam<br>Classic');
            }, 12000);
        }
    });
</script>
@endpush
