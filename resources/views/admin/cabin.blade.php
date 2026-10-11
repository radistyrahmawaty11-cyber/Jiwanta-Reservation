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
        'edit'      => 'M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z',
        'trash'     => 'M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2M10 11v6M14 11v6',
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

    // Convert database cabins to units array
    $units = $cabins->map(function($cabin) {
        $amenities = [];
        if ($cabin->fasilitas) {
            $amenities = array_map('trim', explode(',', $cabin->fasilitas));
        }

        return [
            'id' => $cabin->id,
            'key' => 'cabin-'.$cabin->id,
            'no' => 'Unit '.str_pad($cabin->id, 2, '0', STR_PAD_LEFT),
            'img' => $cabin->gambar ? asset('storage/'.$cabin->gambar) : 'https://via.placeholder.com/400x300/1d3b2a/ffffff?text='.urlencode($cabin->nama_cabin),
            'title' => $cabin->nama_cabin,
            'badge' => [$cabin->status === 'terisi' ? 'Sedang Terisi' : 'Tersedia',
                       $cabin->status === 'terisi' ? 'bg-[#FBD9B0] text-[#6B4520]' : 'bg-[#CDEFD5] text-[#0B3A22]',
                       $cabin->status === 'terisi' ? 'bg-[#7A5230]' : 'bg-[#0B3A22]'],
            'capIcon' => 'users',
            'cap' => 'Kapasitas: '.$cabin->kapasitas.' orang',
            'amen' => array_map(function($amen) {
                $icons = ['bed', 'tv', 'wifi', 'fire', 'food', 'sofa', 'kitchen', 'deck'];
                $icon = $icons[array_rand($icons)];
                return [$icon, $amen];
            }, $amenities),
            'normal' => $cabin->harga_per_malam,
            'weekend' => round($cabin->harga_per_malam * 1.2),
            'guest' => null,
            'primary' => 'booking',
        ];
    })->toArray();

    $totalCabin = $cabins->count();
    $cabinTerisi = $cabins->where('status', 'terisi')->count();
    $cabinTersedia = $totalCabin - $cabinTerisi;
@endphp

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
                        <a href="{{ route('admin.cabin.create') }}" class="flex items-center gap-2 rounded-full bg-[#0B3A22] px-5 py-2.5 text-[11px] font-bold text-white shadow-sm transition hover:bg-[#124c2f]">
                            {!! $ic($p['plus'], 'h-4 w-4') !!} Tambah Unit Kabin
                        </a>
                    </div>
                </div>

                {{-- Stats Cards --}}
                <div class="grid grid-cols-3 gap-4 items-start">
                    <div data-reveal class="{{ $card }} p-5 hover:shadow-md">
                        <div class="flex items-start justify-between">
                            <p class="{{ $cap }}">Inventaris Unit</p>
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $mint }}">{!! $ic($p['cabin'], 'h-5 w-5') !!}</span>
                        </div>
                        <p class="text-[34px] font-bold leading-tight text-[#0B3A22]"><span data-inv>{{ $totalCabin }}</span> <span class="text-[18px] font-medium text-slate-600">Kabin</span></p>
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
                            <p class="text-[34px] font-bold leading-[1.1] text-[#0B3A22]"><span data-occ>{{ $cabinTerisi }}</span><br>Unit</p>
                            <span data-occ-pct class="mt-4 rounded-full {{ $mint }} px-3 py-1 text-[9px] font-bold leading-tight text-[#0B3A22]">{{ $totalCabin > 0 ? round(($cabinTerisi / $totalCabin) * 100) : 0 }}%<br>Okupansi</span>
                        </div>
                        <p class="mt-3 flex items-start gap-2 text-[10px] leading-snug text-slate-600"><span class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-[#0B3A22]"></span><span>{{ $cabinTerisi }} Tamu Menginap, {{ $cabinTersedia }} Unit Tersedia</span></p>
                    </div>

                    <div data-reveal class="{{ $card }} p-5 hover:shadow-md">
                        <div class="flex items-start justify-between">
                            <p class="{{ $cap }}">Status Housekeeping</p>
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $mint }}">{!! $ic($p['spark'], 'h-5 w-5') !!}</span>
                        </div>
                        <p class="text-[34px] font-bold leading-tight text-[#0B3A22]"><span data-hk-ready>{{ $cabinTersedia }}</span> Siap <span class="text-[18px] font-medium text-slate-600">/ <span data-hk-service>{{ $cabinTerisi }}</span> Servis</span></p>
                        <p class="mt-3 flex items-center gap-2 text-[10px] text-slate-600"><span class="text-[#C2762B]">{!! $ic($p['fire'], 'h-3.5 w-3.5') !!}</span> {{ $cabinTerisi }} Unit Pembersihan Rutin &amp; Filter Kolam</p>
                    </div>
                </div>

@if(session('success'))
<div id="success-toast" class="fixed bottom-6 right-6 z-[70] bg-[#0B3A22] text-white px-6 py-3 rounded-xl shadow-2xl flex items-center gap-3 animate-bounce">
    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    <span class="font-semibold">{{ session('success') }}</span>
</div>
<script>
setTimeout(() => {
    const toast = document.getElementById('success-toast');
    if (toast) {
        toast.style.transition = 'opacity 0.5s';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 500);
    }
}, 3000);
</script>
@endif

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
                            <button type="button" data-filter="all" class="rounded-full px-3 py-1 text-[10px] font-bold transition bg-[#0B3A22] text-white">Semua ({{ $totalCabin }})</button>
                            @foreach($cabins->groupBy('nama_cabin') as $nama => $group)
                                <button type="button" data-filter="cabin-{{ $group->first()->id }}" class="rounded-full px-3 py-1 text-[10px] font-bold transition bg-[#DDE6FB] text-slate-800 hover:brightness-95">{{ $nama }}</button>
                            @endforeach
                        </div>
                    </div>

                    <div id="units" class="mt-5 flex flex-wrap justify-center gap-4">
                        @foreach($cabins as $cabin)
                            <article data-unit="cabin-{{ $cabin->id }}" data-reveal class="group relative w-[260px] overflow-hidden rounded-2xl bg-white shadow-md border border-slate-100 hover:shadow-xl">
                                <span data-shine class="pointer-events-none absolute inset-0 z-20 opacity-0 transition-opacity duration-300 bg-[radial-gradient(220px_circle_at_var(--mx,50%)_var(--my,50%),rgba(255,255,255,0.28),transparent_65%)]"></span>
                                <div class="relative h-[158px] overflow-hidden bg-[#1d3b2a]">
                                    <img data-img src="{{ $cabin->gambar ? asset('storage/'.$cabin->gambar) : 'https://via.placeholder.com/400x300/1d3b2a/ffffff?text='.urlencode($cabin->nama_cabin) }}" alt="{{ $cabin->nama_cabin }}" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110">
                                    <div class="absolute inset-0 bg-gradient-to-t from-[#0B3A22]/80 via-transparent to-black/10"></div>
                                    <span data-badge class="absolute left-3 top-3 z-10 inline-flex cursor-pointer select-none items-center gap-1.5 rounded-full {{ $cabin->status === 'terisi' ? 'bg-[#FBD9B0] text-[#6B4520]' : 'bg-[#CDEFD5] text-[#0B3A22]' }} px-2.5 py-1 text-[9px] font-bold transition hover:brightness-95">
                                        <span class="h-1.5 w-1.5 animate-pulse rounded-full {{ $cabin->status === 'terisi' ? 'bg-[#7A5230]' : 'bg-[#0B3A22]' }}"></span><span data-badge-text>{{ $cabin->status === 'terisi' ? 'Sedang Terisi' : 'Tersedia' }}</span>
                                    </span>
                                    <span class="absolute right-3 top-3 rounded-md bg-[#0B3A22] px-2 py-1 text-[9px] font-bold text-white">Unit {{ str_pad($cabin->id, 2, '0', STR_PAD_LEFT) }}</span>
                                    <h3 data-title class="absolute bottom-3 left-3 text-[16px] font-bold text-white">{{ $cabin->nama_cabin }}</h3>
                                </div>

                                <div class="p-4">
                                    <p class="flex items-center gap-2 text-[10px] font-bold text-slate-800"><span class="text-[#0B3A22]">{!! $ic($p['users'], 'h-3.5 w-3.5') !!}</span><span data-cap>Kapasitas: {{ $cabin->kapasitas }} orang</span></p>
                                    <div data-amen class="mt-3 flex flex-wrap gap-1.5">
                                        @if($cabin->fasilitas)
                                            @foreach(array_map('trim', explode(',', $cabin->fasilitas)) as $fasilitas)
                                                <span class="inline-flex items-center gap-1.5 rounded-lg {{ $lav }} px-2 py-1 text-[9px] font-medium text-slate-800 transition hover:-translate-y-0.5 hover:bg-white hover:shadow-sm">{!! $ic($p['spark'], 'h-3 w-3') !!}{{ $fasilitas }}</span>
                                            @endforeach
                                        @endif
                                    </div>

                                    <div class="mt-4 space-y-1.5 rounded-xl bg-slate-100 p-3 text-[10px] text-slate-600">
                                        <div class="flex items-start justify-between gap-2"><span class="leading-snug">Tarif Normal<br>(Weekday)</span><b data-rate-normal class="text-right text-[11px] leading-snug text-slate-900">{{ $rp($cabin->harga_per_malam) }} /<br>malam</b></div>
                                        <div class="flex items-start justify-between gap-2"><span class="leading-snug">Tarif Weekend<br>(Jum-Sab)</span><b data-rate-weekend class="text-right text-[11px] leading-snug text-[#7A5230]">{{ $rp(round($cabin->harga_per_malam * 1.2)) }} /<br>malam</b></div>
                                    </div>

                                    <div class="mt-4 flex items-center gap-1.5">
                                        <a href="{{ route('admin.cabin.edit', $cabin->id) }}" class="rounded-lg {{ $lav }} px-3 py-2 text-[10px] font-bold text-slate-800 transition hover:brightness-95">Edit Unit</a>
                                        <button type="button" onclick="openTarifModal('cabin-{{ $cabin->id }}', '{{ $cabin->nama_cabin }}', {{ $cabin->harga_per_malam }}, {{ round($cabin->harga_per_malam * 1.2) }})" class="rounded-lg px-3 py-2 text-[10px] font-bold transition hover:brightness-95 {{ $lav }} text-slate-800">Atur Tarif</button>

                                        <form action="{{ route('admin.cabin.destroy', $cabin->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus cabin {{ $cabin->nama_cabin }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg bg-red-100 px-3 py-2 text-[10px] font-bold text-red-700 transition hover:bg-red-200">Hapus</button>
                                        </form>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
@endsection

@section('overlays')
    {{-- Modal --}}
    <div id="modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/40 p-4">
        <div data-m-box class="max-h-[90vh] w-full max-w-sm overflow-y-auto rounded-2xl bg-white p-6 shadow-xl">
            <div class="flex items-start justify-between">
                <div>
                    <h3 data-m-title class="text-[16px] font-bold text-slate-900">Atur Tarif</h3>
                    <p data-m-sub class="mt-0.5 text-[11px] text-slate-600"></p>
                </div>
                <button type="button" onclick="closeModal()" class="rounded-lg p-1 text-slate-500 hover:bg-slate-100" aria-label="Tutup">{!! $ic($p['x'], 'h-5 w-5') !!}</button>
            </div>
            <div data-m-body class="mt-5 space-y-4"></div>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" onclick="closeModal()" class="rounded-lg {{ $lav }} px-4 py-2 text-[11px] font-bold">Batal</button>
                <button type="button" id="btn-save-modal" class="rounded-lg bg-[#0B3A22] px-5 py-2 text-[11px] font-bold text-white transition hover:bg-[#124c2f]">Simpan</button>
            </div>
        </div>
    </div>

    {{-- Toast --}}
    <div id="toasts" class="fixed bottom-6 right-6 z-[60] space-y-2"></div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
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
    const bell = () => { const b = $('[data-bell]'); if (b) { b.classList.remove('jw-wiggle'); void b.offsetWidth; b.classList.add('jw-wiggle'); } };
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

    const DAYS = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
    const MON = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    const utc = (y, m, d) => new Date(Date.UTC(y, m, d));
    const iso = (d) => d.toISOString().slice(0, 10);
    const TODAY = utc(2026, 8, 20);
    const addDays = (d, n) => new Date(d.getTime() + n * 864e5);
    let WS = TODAY;

    const UNITS = @json($units);

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
                    <div><p class="text-[13px] font-bold text-slate-900">${esc(u.title)}</p><p class="text-[10px] text-slate-600">${esc(u.cap)}</p></div></div>
                <div class="contents [&>*]:row-start-1">${cells}</div>
                <button type="button" data-tl-action="${u.primary}" data-unit-id="${u.key}" style="grid-column:9;grid-row:1" class="rounded-lg ${u.primary === 'tarif' ? 'bg-[#FBD9B0] text-[#6B4520]' : 'bg-[#DDE6FB] text-slate-800'} px-3 py-2 text-[10px] font-bold transition hover:brightness-95">${u.primary === 'tarif' ? 'Atur Tarif' : 'Detail Booking'}</button></div>`;
        }).join('');
    };

    let filter = 'all';

    const HK = { ready: {{ $cabinTersedia }}, service: {{ $cabinTerisi }}, total: {{ $totalCabin }} };
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

    const RATES = {};
    UNITS.forEach(u => { RATES[u.key] = { normal: u.normal, weekend: u.weekend }; });
    const NAMES = {};
    UNITS.forEach(u => { NAMES[u.key] = u.title; });

    const modal = $('#modal');
    const btnSaveModal = $('#btn-save-modal');
    const dInp = 'w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-[12px] font-semibold focus:outline-none focus:ring-2 focus:ring-[#0B3A22]/30';
    const lbl = 'block text-[10px] font-bold uppercase text-slate-600';
    const dayDiff = (x, y) => Math.round((new Date(y + 'T00:00:00Z') - new Date(x + 'T00:00:00Z')) / 864e5);
    const fmtD = (k) => Number(k.slice(8)) + ' ' + MON[Number(k.slice(5, 7)) - 1];
    const rangeTxt = (x, y) => (x === y ? fmtD(x) : Number(x.slice(8)) + ' - ' + fmtD(y));
    const clash = (u, x, y, skip) => u.segs.find((sg) => sg !== skip && x <= sg.to && y >= sg.from);
    const unitCard = (key) => $(`[data-unit="${key}"]`);

    let current = null;

    // ========== FUNGSI OPEN/CLOSE MODAL ==========
    window.openTarifModal = function(unitKey, unitName, normalRate, weekendRate) {
        const titleEl = document.querySelector('[data-m-title]');
        const subEl = document.querySelector('[data-m-sub]');
        const bodyEl = document.querySelector('[data-m-body]');

        titleEl.textContent = 'Atur Tarif';
        subEl.textContent = unitName;
        btnSaveModal.textContent = 'Simpan Tarif';

        bodyEl.innerHTML = `
            <div class="space-y-4">
                <div>
                    <label class="${lbl}">Tarif Normal (Weekday)</label>
                    <div class="relative mt-1">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[12px] text-slate-500 font-semibold">Rp</span>
                        <input id="tarif-normal-input" type="number" step="10000" min="0" value="${normalRate}" class="${dInp} pl-10 font-bold text-[#0B3A22]">
                    </div>
                    <p class="mt-1 text-[10px] text-slate-500">Harga untuk Senin - Jumat</p>
                </div>
                <div>
                    <label class="${lbl}">Tarif Weekend (Jumat - Sabtu)</label>
                    <div class="relative mt-1">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-[12px] text-slate-500 font-semibold">Rp</span>
                        <input id="tarif-weekend-input" type="number" step="10000" min="0" value="${weekendRate}" class="${dInp} pl-10 font-bold text-[#7A5230]">
                    </div>
                    <p class="mt-1 text-[10px] text-slate-500">Harga untuk Jumat malam - Minggu</p>
                </div>
                <div class="rounded-lg bg-[#FBD9B0]/30 border border-[#FBD9B0] p-3">
                    <p class="text-[10px] text-[#6B4520]"><span class="font-bold">💡 Tips:</span> Tarif weekend biasanya 15-20% lebih tinggi dari weekday</p>
                </div>
            </div>
        `;

        btnSaveModal.dataset.unitKey = unitKey;
        btnSaveModal.dataset.unitName = unitName;
        btnSaveModal.dataset.mode = 'tarif';

        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };

    window.closeModal = function() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };

    // ========== FUNGSI SIMPAN TARIF ==========
    function saveTarif() {
        const unitKey = btnSaveModal.dataset.unitKey;
        const unitName = btnSaveModal.dataset.unitName;
        const normal = Number(document.getElementById('tarif-normal-input').value);
        const weekend = Number(document.getElementById('tarif-weekend-input').value);

        if (!normal || !weekend || normal <= 0 || weekend <= 0) {
            toast('Tarif harus lebih dari 0!');
            return;
        }

        // Update tampilan di card
        const card = document.querySelector(`[data-unit="${unitKey}"]`);
        if (card) {
            const normalEl = card.querySelector('[data-rate-normal]');
            const weekendEl = card.querySelector('[data-rate-weekend]');
            if (normalEl) normalEl.innerHTML = 'Rp ' + Math.round(normal).toLocaleString('id-ID') + ' /<br>malam';
            if (weekendEl) weekendEl.innerHTML = 'Rp ' + Math.round(weekend).toLocaleString('id-ID') + ' /<br>malam';
        }

        // Update memory
        if (RATES[unitKey]) {
            RATES[unitKey] = { normal, weekend };
        }

        closeModal();
        toast('Tarif ' + unitName + ' berhasil diperbarui!');
        if (card) flash(card);
    }

    // Event listener tombol simpan
    btnSaveModal.addEventListener('click', function() {
        if (this.dataset.mode === 'tarif') {
            saveTarif();
        }
    });

    // Klik backdrop untuk tutup modal
    modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });

    // ========== Sinkronisasi PMS ==========
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

    // Live demo
    if (LIVE_DEMO) {
        const events = ['Smart Lock Unit 01: pintu terkunci', 'Smart Lock Unit 02: baterai 92%, normal', 'Housekeeping: Unit 01 selesai dibersihkan', 'PMS: data reservasi tersinkron'];
        let n = 0;
        setInterval(() => {
            toast(events[n++ % events.length]); bell();
            if (n % 3 === 0) { HK.service = HK.service === 1 ? 0 : 1; HK.ready = HK.total - HK.service; updateStats(); }
        }, 12000);
    }

    // Scroll reveal
    const io = new IntersectionObserver((es) => es.forEach((en) => {
        if (!en.isIntersecting) return;
        en.target.classList.add('in');
        setTimeout(() => (en.target.style.transitionDelay = ''), 900);
        io.unobserve(en.target);
    }), { threshold: 0.08 });
    $$('[data-reveal]').forEach((el, i) => { el.style.transitionDelay = (i % 4) * 90 + 'ms'; io.observe(el); });

    // Card interactions
    const bindCard = (card) => {
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
        if (b) {
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
                    toast('Pintu ' + NAMES[card.dataset.unit] + ' terbuka, auto-kunci 5 detik');
                }, 800);
                setTimeout(() => { txt.textContent = orig; b.className = cls; busy = false; }, 5800);
            });
        }
    };
    $$('[data-unit]').forEach(bindCard);

    const hkCard = $('[data-hk-ready]') ? $('[data-hk-ready]').closest('[data-reveal]') : null;
    if (hkCard) {
        hkCard.classList.add('cursor-pointer', 'select-none');
        hkCard.title = 'Klik untuk ubah status housekeeping';
        hkCard.addEventListener('click', () => {
            HK.service = HK.service ? 0 : 1;
            HK.ready = HK.total - HK.service;
            updateStats();
            flash(hkCard);
            toast(HK.service ? 'Unit dijadwalkan servis' : 'Semua unit siap');
        });
    }

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
