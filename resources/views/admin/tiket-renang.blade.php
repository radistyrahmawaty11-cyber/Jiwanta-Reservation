@extends('layouts.admin')

@section('title', 'Kelola Tiket - Jiwanta')

@section('page-content')

@php
    // ---------- Helper ----------
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
        'check'     => 'M5 13l4 4L19 7',
        'checkc'    => 'M9 12l2 2 4-4M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'plus'      => 'M12 5v14M5 12h14',
        'minus'     => 'M5 12h14',
        'calendar'  => 'M8 3v3M16 3v3M4 9h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z',
        'sliders'   => 'M4 7h9M17 7h3M4 17h3M11 17h9M15 5v4M9 15v4',
        'tree'      => 'M12 3l4 6h-2.5l3.5 5h-3l3 5H7l3-5H7l3.5-5H8l4-6z',
        'x'         => 'M6 6l12 12M18 6L6 18',
        'warn'      => 'M12 9v4M12 17h.01M10.3 3.9L2.4 18a2 2 0 001.7 3h15.8a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z',
        'history'   => 'M3 12a9 9 0 109-9M3 4v5h5M12 8v4l3 2',
        'power'     => 'M12 3v9M6.3 6.3a8 8 0 1011.4 0',
        'sync'      => 'M4 4v6h6M20 20v-6h-6M20 9A8 8 0 006 6L4 10M4 15a8 8 0 0014 3l2-4',
        'cash'      => 'M3 7h18v10H3zM12 14a2 2 0 100-4 2 2 0 000 4z',
        'waves'     => 'M3 17c1.5 0 1.5-1 3-1s1.5 1 3 1 1.5-1 3-1 1.5 1 3 1 1.5-1 3-1 1.5 1 3 1M3 12c1.5 0 1.5-1 3-1s1.5 1 3 1 1.5-1 3-1 1.5 1 3 1 1.5-1 3-1 1.5 1 3 1M15 5a1.5 1.5 0 100-3 1.5 1.5 0 000 3z',
        'drop'      => 'M12 3s6 7 6 11a6 6 0 01-12 0c0-4 6-11 6-11z',
        'cloudup'   => 'M7 18a4 4 0 010-8 5 5 0 019.6-1A4.5 4.5 0 0117 18M12 12v6M9 15l3-3 3 3',
        'thermo'    => 'M14 14V5a2 2 0 00-4 0v9a4 4 0 104 0z',
        'star'      => 'M12 3l2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9L12 3z',
        'edit'      => 'M12 20h9M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4 12.5-12.5z',
        'trash'     => 'M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3',
        'chevron'   => 'M6 9l6 6 6-6',
        'bolt'      => 'M13 3L5 14h6l-1 7 8-11h-6l1-7z',
    ];

    $nav = [
        'Navigasi Utama' => [['Dashboard', 'dashboard', false, null], ['Verifikasi & Transaksi', 'receipt', false, 14]],
        'Manajemen Resort' => [['Kelola Tiket', 'ticket', true, null], ['Kelola Cabin Suite', 'cabin', false, null]],
        'Administrasi & Sistem' => [['Manajemen Pengguna', 'users', false, null], ['Laporan & Export', 'chart', false, null], ['Pengaturan Profil', 'gear', false, null]],
    ];

    $mint = 'bg-[#CDEFD5]';
    $peach = 'bg-[#FBD9B0]';
    $lav = 'bg-[#DDE6FB]';

    // Data katalog tiket (diisi dari database via TiketController)
    $tickets = $tickets ?? [];

    // Distribusi sesi kedatangan hari ini
    $sessions = [
        ['no' => '01', 'name' => 'Pagi (08:00 - 12:00)', 'note' => 'Kondisi Udara 17°C Sejuk Berkabut', 'used' => 85, 'cap' => 90, 'vipOnly' => false],
        ['no' => '02', 'name' => 'Siang (12:00 - 15:00)', 'note' => 'Waktu Bersih Kolam 12:00 - 12:45', 'used' => 45, 'cap' => 90, 'vipOnly' => false],
        ['no' => '03', 'name' => 'Sore (15:00 - 18:00)', 'note' => 'Golden Hour Kabut Pinus', 'used' => 68, 'cap' => 90, 'vipOnly' => false],
        ['no' => '04', 'name' => 'Malam Onsen (18:00 - 21:00)', 'note' => 'Khusus Tiket Premier / VIP', 'used' => 27, 'cap' => 80, 'vipOnly' => true],
    ];

    // Riwayat log awal
    $log = [
        ['who' => 'Bagas Dananjaya (Super Admin)', 'time' => '09:42 WIB', 'text' => 'Menambah kuota darurat +25 pax untuk Tiket Classic via Fast Turnstile.', 'tone' => 'green', 'ticket' => 'classic'],
        ['who' => 'Siti Rahma (Duty Manager)', 'time' => '07:05 WIB', 'text' => 'Membuka loket online Premier Onsen Sesi Pagi & Malam.', 'tone' => 'brown', 'ticket' => 'premier'],
        ['who' => 'Sistem Otomatis (Nightly Batch)', 'time' => '00:01 WIB', 'text' => 'Reset kuota harian kembali ke default operasional (350 pax).', 'tone' => 'gray', 'ticket' => ''],
    ];

    $card = 'rounded-2xl bg-white shadow-sm border border-slate-200';
    $cap = 'text-[9px] font-bold uppercase leading-snug tracking-wide text-slate-600';

    // Gaya form & tombol (dipakai semua modal)
    $inp = 'w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-[12px] text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-[#0B3A22] focus:ring-2 focus:ring-[#0B3A22]/15';
    $lbl = 'mb-1 block text-[11px] font-semibold text-slate-700';
    $err = 'mt-1 hidden text-[10px] font-medium text-red-600';
    $btnP = 'inline-flex items-center justify-center gap-2 rounded-lg bg-[#0B3A22] px-5 py-2.5 text-[12px] font-bold text-white transition hover:bg-[#124c2f] active:scale-95 disabled:cursor-wait disabled:opacity-60';
    $btnG = 'inline-flex items-center justify-center gap-2 rounded-lg bg-slate-100 px-5 py-2.5 text-[12px] font-semibold text-slate-700 transition hover:bg-slate-200 active:scale-95';
@endphp


                {{-- Header Section --}}
                <div class="flex items-start justify-between gap-6">
                    <div class="max-w-xl">
                        <p class="flex items-center gap-3 mb-2">
                            <span class="rounded-md {{ $peach }} px-2.5 py-1 text-[9px] font-bold uppercase tracking-wide text-[#8B5E34]">Katalog Resort &amp; Rekreasi</span>
                            <span class="flex items-center gap-1.5 text-[10px] text-slate-500"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span> Real-time Gate Sync</span>
                        </p>
                        <h1 class="text-[32px] font-bold leading-tight text-slate-900">Master Data &amp; Pengaturan Tiket</h1>
                        <p class="mt-2 text-[13px] leading-relaxed text-slate-600">Kelola jenis tiket kolam air panas Ciwidey, kuota harian, tarif sesi, dan fasilitas inklusi. Penyesuaian kuota langsung tersinkronisasi ke turnstile pintu masuk.</p>
                    </div>
                    <div class="flex w-[300px] shrink-0 flex-col items-start gap-3 pt-6">
                        <div class="relative">
                            <button type="button" id="period-btn" aria-haspopup="menu" aria-expanded="false" class="flex items-center gap-2.5 rounded-xl bg-white border border-slate-200 px-5 py-3 text-[12px] font-medium shadow-sm transition hover:bg-slate-50 active:scale-95">
                                {!! $ic($p['calendar'], 'h-4 w-4') !!} Periode: <span data-period>Hari Ini &amp; Besok</span> {!! $ic($p['chevron'], 'h-3.5 w-3.5 text-slate-400') !!}
                            </button>
                            <div id="period-menu" role="menu" class="absolute left-0 top-full z-20 mt-1 hidden w-52 overflow-hidden rounded-xl border border-slate-200 bg-white py-1 shadow-lg">
                                @foreach (['Hari Ini', 'Hari Ini & Besok', '7 Hari ke Depan'] as $opt)
                                    <button type="button" role="menuitem" data-period-opt="{{ $opt }}" class="block w-full px-4 py-2 text-left text-[11px] font-medium text-slate-700 transition hover:bg-slate-50">{{ $opt }}</button>
                                @endforeach
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <button type="button" id="open-all" class="flex items-center gap-2 rounded-xl {{ $lav }} px-5 py-2.5 text-[11px] font-semibold text-slate-800 transition hover:bg-[#cfdaf7] active:scale-95">
                                {!! $ic($p['power'], 'h-4 w-4') !!} Buka Seluruh Penjualan
                            </button>
                            <button type="button" id="add-ticket" class="flex items-center gap-2 rounded-xl bg-[#0B3A22] px-5 py-2.5 text-[11px] font-bold text-white shadow-sm transition hover:bg-[#124c2f] active:scale-95">
                                {!! $ic($p['plus'], 'h-4 w-4') !!} Tambah Kategori Tiket
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Stats Cards --}}
                <div class="grid grid-cols-3 gap-5">
                    <div class="{{ $card }} relative overflow-hidden p-6">
                        <span class="pointer-events-none absolute -bottom-10 -right-8 h-36 w-36 rounded-full bg-[#DDF2E1]"></span>
                        <div class="relative flex items-start justify-between">
                            <p class="{{ $cap }}">Status Portofolio</p>
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl {{ $mint }} text-[#0B3A22]">{!! $ic($p['ticket'], 'h-5 w-5') !!}</span>
                        </div>
                        <p class="relative mt-4 flex items-baseline gap-2"><span data-portfolio-count class="text-[40px] font-bold leading-none text-slate-900">2</span><span class="text-[13px] font-medium text-slate-700">Kategori Utama</span></p>
                        <p data-portfolio-text class="relative mt-2 text-[11px] leading-relaxed text-slate-600">Classic Thermal Springs &amp; Premier Onsen Private aktif dijual.</p>
                        <p class="relative mt-4 flex items-center gap-2 text-[10px] font-semibold text-slate-800"><span class="h-1.5 w-1.5 rounded-full bg-[#0B3A22]"></span><span data-draft-text>Tidak ada paket dalam draf</span></p>
                    </div>

                    <div class="{{ $card }} relative overflow-hidden p-6">
                        <span class="pointer-events-none absolute -bottom-10 -right-8 h-36 w-36 rounded-full bg-[#FDEBD6]"></span>
                        <div class="relative flex items-start justify-between">
                            <p class="{{ $cap }}">Kapasitas Kolam Harian</p>
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl {{ $peach }} text-[#8B5E34]">{!! $ic($p['waves'], 'h-5 w-5') !!}</span>
                        </div>
                        <div class="relative mt-4 flex items-end justify-between">
                            <p class="flex items-baseline gap-1.5"><span data-sold-total class="text-[40px] font-bold leading-none text-slate-900">225</span><span class="text-[13px] font-medium text-slate-500">/ <span data-cap-total>300</span> pax</span></p>
                            <b data-pct class="text-[13px] text-[#8B5E34]">75.0%</b>
                        </div>
                        <div class="relative mt-3 h-2 overflow-hidden rounded-full bg-slate-100"><div data-cap-bar style="width: 75%" class="h-full rounded-full bg-[#0B3A22] transition-all duration-1000"></div></div>
                        <p class="relative mt-2 flex justify-between text-[10px]"><b data-left class="text-slate-800">Tersisa 75 kuota gerbang</b><span class="font-medium text-[#8B5E34]">Beban Puncak 14:00</span></p>
                    </div>

                    <div class="relative overflow-hidden rounded-2xl bg-[#0F4527] p-6 text-white shadow-sm">
                        <span class="pointer-events-none absolute -bottom-12 -right-10 h-40 w-40 rounded-full bg-white/5"></span>
                        <div class="relative flex items-start justify-between">
                            <p class="text-[9px] font-bold uppercase tracking-wide text-white/70">Pendapatan Tiket Hari Ini</p>
                            <span class="text-white/70">{!! $ic($p['cash'], 'h-5 w-5') !!}</span>
                        </div>
                        <p class="relative mt-4 flex items-baseline gap-2"><span class="text-[13px] text-white/70">Rp</span><span data-revenue class="text-[38px] font-bold leading-none">12.400.000</span></p>
                        <p class="relative mt-2 text-[11px] leading-relaxed text-[#B5F0BE]">+18.5% dibanding rerata hari kerja minggu lalu.</p>
                        <p class="relative mt-4 flex items-center gap-2 text-[10px] font-semibold">
                            <span class="inline-flex items-center gap-1.5">{!! $ic($p['bolt'], 'h-3.5 w-3.5') !!} 88% Bayar via QRIS</span>
                            <span class="rounded-md bg-white/10 px-2 py-1 text-[9px] font-bold">Audit Otomatis</span>
                        </p>
                    </div>
                </div>

                {{-- Main Grid --}}
                <div class="grid grid-cols-[minmax(0,1fr)_340px] items-start gap-6">

                    {{-- Katalog --}}
                    <section>
                        <div class="mb-4 flex items-center justify-between">
                            <h2 class="flex items-center gap-3 text-[16px] font-bold text-slate-900">Katalog Tiket &amp; Varian <span data-count class="rounded-md {{ $lav }} px-2 py-0.5 text-[9px] font-bold text-slate-700">2 Terdata</span></h2>
                            <span class="flex items-center gap-1.5 text-[10px] text-slate-500"><span data-sync-icon class="inline-block">{!! $ic($p['sync'], 'h-3.5 w-3.5') !!}</span> Auto-sync POS Kiosk</span>
                        </div>
                        <div id="catalog" class="space-y-5"></div>
                        <p id="catalog-empty" class="hidden rounded-2xl bg-white p-10 text-center text-[12px] font-medium text-slate-500 border border-slate-200">Belum ada kategori tiket. Klik "Tambah Kategori Tiket" untuk membuat.</p>
                    </section>

                    {{-- Kolom kanan --}}
                    <div class="space-y-5">

                        {{-- Manajer kuota --}}
                        <section class="{{ $card }} p-5">
                            <div class="flex items-start justify-between gap-2">
                                <div class="flex items-start gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $mint }} text-[#0B3A22]">{!! $ic($p['sliders'], 'h-5 w-5') !!}</span>
                                    <div>
                                        <h2 class="text-[16px] font-bold leading-tight text-slate-900">Manajer Kuota Gerbang</h2>
                                        <p class="mt-0.5 text-[10px] text-slate-500">Penyesuaian cepat kapasitas hari ini</p>
                                    </div>
                                </div>
                                <span class="flex items-center gap-1.5 rounded-md {{ $peach }} px-2 py-1 text-[8px] font-bold uppercase tracking-wide text-[#8B5E34]"><span class="h-1.5 w-1.5 animate-pulse rounded-full bg-[#C2762B]"></span>Live Sync</span>
                            </div>

                            <div class="mt-5 flex items-center justify-between gap-2">
                                <p class="text-[9px] font-bold uppercase tracking-wide text-slate-600">Pilih Varian Tiket</p>
                                <button type="button" id="qm-add" class="inline-flex items-center gap-1.5 rounded-lg bg-[#0B3A22] px-2.5 py-1.5 text-[10px] font-bold text-white transition hover:bg-[#124c2f] active:scale-95">{!! $ic($p['plus'], 'h-3.5 w-3.5') !!} Tambah Tipe</button>
                            </div>
                            <div id="variant-tabs" class="mt-2 grid grid-cols-1 gap-2"></div>
                            <div id="variant-actions" class="mt-2 hidden items-center gap-2">
                                <button type="button" id="qm-edit" class="flex flex-1 items-center justify-center gap-1.5 rounded-lg bg-[#DDE6FB] py-2 text-[10px] font-bold text-slate-700 transition hover:bg-[#cfd9f7] active:scale-95">{!! $ic($p['edit'], 'h-3.5 w-3.5') !!} Edit Tipe</button>
                                <button type="button" id="qm-del" class="flex items-center justify-center gap-1.5 rounded-lg bg-red-50 px-3 py-2 text-[10px] font-bold text-red-600 transition hover:bg-red-100 active:scale-95">{!! $ic($p['trash'], 'h-3.5 w-3.5') !!} Hapus</button>
                            </div>

                            <div class="mt-4 rounded-2xl bg-[#EEF1FD] p-4">
                                <div class="flex items-center justify-between">
                                    <p class="text-[12px] font-bold text-slate-900">Penyesuaian Kuota Darurat</p>
                                    <span data-qm-safe class="rounded-md {{ $peach }} px-2 py-1 text-[9px] font-bold text-[#8B5E34]">Batas Aman 250 pax</span>
                                </div>
                                <div class="mt-4 flex items-center justify-between">
                                    <button type="button" id="qm-minus" aria-label="Kurangi 25" class="flex h-12 w-12 items-center justify-center rounded-xl bg-white text-slate-800 shadow-sm transition hover:bg-slate-50 active:scale-90 disabled:cursor-not-allowed disabled:opacity-40">{!! $ic($p['minus'], 'h-5 w-5') !!}</button>
                                    <div class="text-center">
                                        <p class="flex items-baseline justify-center gap-1"><span data-qm-value class="text-[48px] font-bold leading-none text-slate-900">200</span><span class="text-[11px] text-slate-500">pax</span></p>
                                        <p data-qm-note class="mt-1 text-[10px] font-medium text-slate-600">Kapasitas Standar</p>
                                    </div>
                                    <button type="button" id="qm-plus" aria-label="Tambah 25" class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#0B3A22] text-white shadow-sm transition hover:bg-[#124c2f] active:scale-90 disabled:cursor-not-allowed disabled:opacity-40">{!! $ic($p['plus'], 'h-5 w-5') !!}</button>
                                </div>
                                <p class="mt-4 text-center text-[10px] leading-relaxed text-slate-600">Klik +/- 25 kuota untuk merespons kepadatan kolam secara bertahap.</p>
                            </div>

                            <button type="button" id="qm-apply" class="mt-4 flex w-full items-center justify-center gap-2 rounded-xl bg-[#0B3A22] py-3 text-[12px] font-bold text-white shadow-sm transition hover:bg-[#124c2f] active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-50">
                                {!! $ic($p['cloudup'], 'h-4 w-4') !!} <span data-qm-apply-label>Terapkan Perubahan ke Gate Kiosk</span>
                            </button>
                        </section>

                        {{-- Distribusi sesi --}}
                        <section class="{{ $card }} p-5">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h2 class="text-[16px] font-bold text-slate-900">Distribusi Sesi Kedatangan</h2>
                                    <p class="mt-0.5 text-[10px] text-slate-500">Mencegah kepadatan air belerang</p>
                                </div>
                                <span class="text-slate-500">{!! $ic($p['drop'], 'h-5 w-5') !!}</span>
                            </div>
                            <div id="sessions" class="mt-4 space-y-2"></div>
                        </section>

                        {{-- Riwayat log --}}
                        <section class="{{ $card }} p-5">
                            <div class="flex items-center justify-between">
                                <h2 class="text-[16px] font-bold text-slate-900">Riwayat Log Penyesuaian</h2>
                                <span class="text-[10px] text-slate-500">Hari ini</span>
                            </div>
                            <ol id="log" class="mt-4"></ol>
                        </section>
                    </div>
                </div>
@endsection
@section('overlays')
    {{-- ================= MODAL ================= --}}
    <div id="modal-root">

        {{-- Form tiket: tambah & edit --}}
        <div data-modal="ticket" class="fixed inset-0 z-50 hidden items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="tf-title">
            <div data-close class="absolute inset-0 bg-slate-900/50 backdrop-blur-[2px]"></div>
            <form id="ticket-form" novalidate data-panel class="relative flex max-h-[92vh] w-full max-w-xl scale-95 flex-col overflow-hidden rounded-2xl bg-white opacity-0 shadow-2xl transition duration-200">
                <input type="hidden" name="id">
                <div class="flex items-start justify-between border-b border-slate-100 px-6 py-4">
                    <div>
                        <h3 id="tf-title" class="text-[16px] font-bold text-slate-900">Tambah Kategori Tiket</h3>
                        <p id="tf-sub" class="mt-0.5 text-[11px] text-slate-500">Isi data katalog tiket baru.</p>
                    </div>
                    <button type="button" data-close class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700" aria-label="Tutup">{!! $ic($p['x'], 'h-5 w-5') !!}</button>
                </div>

                <div class="grid grid-cols-2 gap-4 overflow-y-auto px-6 py-5">
                    <div class="col-span-2">
                        <label class="{{ $lbl }}" for="tf-name">Nama Tiket</label>
                        <input id="tf-name" name="name" type="text" maxlength="70" placeholder="Contoh: Tiket Kolam Keluarga" class="{{ $inp }}">
                        <p data-err="name" class="{{ $err }}"></p>
                    </div>
                    <div>
                        <label class="{{ $lbl }}" for="tf-price">Harga (Rp)</label>
                        <input id="tf-price" name="price" type="number" min="0" step="1000" placeholder="45000" class="{{ $inp }}">
                        <p data-err="price" class="{{ $err }}"></p>
                    </div>
                    <div>
                        <label class="{{ $lbl }}" for="tf-unit">Satuan</label>
                        <select id="tf-unit" name="unit" class="{{ $inp }}"><option value="orang">per orang</option><option value="paket">per paket</option></select>
                    </div>
                    <div>
                        <label class="{{ $lbl }}" for="tf-cap">Kuota per hari</label>
                        <input id="tf-cap" name="cap" type="number" min="1" max="2000" placeholder="100" class="{{ $inp }}">
                        <p data-err="cap" class="{{ $err }}"></p>
                    </div>
                    <div>
                        <label class="{{ $lbl }}" for="tf-accent">Kategori tampilan</label>
                        <select id="tf-accent" name="accent" class="{{ $inp }}"><option value="public">Publik</option><option value="vip">VIP / Premier</option></select>
                    </div>
                    <div>
                        <label class="{{ $lbl }}" for="tf-temp">Suhu air (opsional)</label>
                        <input id="tf-temp" name="temp" type="text" placeholder="39° - 41°C" class="{{ $inp }}">
                    </div>
                    <div>
                        <label class="{{ $lbl }}" for="tf-status">Status</label>
                        <select id="tf-status" name="status" class="{{ $inp }}"><option value="active">Aktif</option><option value="draft">Draf Rilis</option><option value="inactive">Nonaktif</option></select>
                    </div>
                    <div class="col-span-2">
                        <label class="{{ $lbl }}" for="tf-hours">Jam operasional</label>
                        <input id="tf-hours" name="hours" type="text" placeholder="07:00 - 18:00 WIB (Fleksibel Tanpa Sesi Kaku)" class="{{ $inp }}">
                    </div>
                    <div class="col-span-2">
                        <label class="{{ $lbl }}" for="tf-sessions">Sesi terjadwal <span class="font-normal text-slate-400">(pisahkan koma, kosongkan jika fleksibel)</span></label>
                        <input id="tf-sessions" name="sessions" type="text" placeholder="Pagi 08-12, Sore 13-17, Malam 18-21" class="{{ $inp }}">
                    </div>
                    <div class="col-span-2">
                        <label class="{{ $lbl }}" for="tf-fac">Fasilitas inklusi <span class="font-normal text-slate-400">(pisahkan koma)</span></label>
                        <input id="tf-fac" name="facilities" type="text" placeholder="Gazebo Publik, Kamar Bilas Standar" class="{{ $inp }}">
                    </div>
                    <div class="col-span-2">
                        <label class="{{ $lbl }}" for="tf-desc">Deskripsi singkat</label>
                        <textarea id="tf-desc" name="desc" rows="2" maxlength="200" placeholder="Tampil pada kartu tiket yang belum aktif." class="{{ $inp }} resize-none"></textarea>
                    </div>
                    <p data-form-error class="col-span-2 hidden rounded-lg bg-red-50 px-3 py-2 text-[11px] font-medium text-red-700"></p>
                </div>

                <div class="flex items-center justify-between gap-2 border-t border-slate-100 bg-slate-50 px-6 py-4">
                    <button type="button" id="tf-delete" class="hidden items-center gap-2 rounded-lg px-3 py-2.5 text-[12px] font-semibold text-red-600 transition hover:bg-red-50">{!! $ic($p['trash'], 'h-4 w-4') !!} Hapus Tiket</button>
                    <span class="flex-1"></span>
                    <button type="button" data-close class="{{ $btnG }}">Batal</button>
                    <button type="submit" data-submit class="{{ $btnP }}">{!! $ic($p['check'], 'h-4 w-4') !!} <span data-submit-label>Simpan Tiket</span></button>
                </div>
            </form>
        </div>

        {{-- Jadwal --}}
        <div data-modal="schedule" class="fixed inset-0 z-50 hidden items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="sc-title">
            <div data-close class="absolute inset-0 bg-slate-900/50 backdrop-blur-[2px]"></div>
            <div data-panel class="relative w-full max-w-md scale-95 overflow-hidden rounded-2xl bg-white opacity-0 shadow-2xl transition duration-200">
                <div class="flex items-start justify-between border-b border-slate-100 px-6 py-4">
                    <div>
                        <h3 id="sc-title" class="text-[16px] font-bold text-slate-900">Jadwal Tiket</h3>
                        <p data-sc-sub class="mt-0.5 text-[11px] text-slate-500"></p>
                    </div>
                    <button type="button" data-close class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700" aria-label="Tutup">{!! $ic($p['x'], 'h-5 w-5') !!}</button>
                </div>
                <div data-sc-body class="space-y-2 px-6 py-5"></div>
                <div class="flex justify-end border-t border-slate-100 bg-slate-50 px-6 py-4"><button type="button" data-close class="{{ $btnG }}">Tutup</button></div>
            </div>
        </div>

        {{-- Riwayat per tiket --}}
        <div data-modal="history" class="fixed inset-0 z-50 hidden items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="hs-title">
            <div data-close class="absolute inset-0 bg-slate-900/50 backdrop-blur-[2px]"></div>
            <div data-panel class="relative flex max-h-[85vh] w-full max-w-md scale-95 flex-col overflow-hidden rounded-2xl bg-white opacity-0 shadow-2xl transition duration-200">
                <div class="flex items-start justify-between border-b border-slate-100 px-6 py-4">
                    <div>
                        <h3 id="hs-title" class="text-[16px] font-bold text-slate-900">Riwayat Perubahan</h3>
                        <p data-hs-sub class="mt-0.5 text-[11px] text-slate-500"></p>
                    </div>
                    <button type="button" data-close class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700" aria-label="Tutup">{!! $ic($p['x'], 'h-5 w-5') !!}</button>
                </div>
                <ol data-hs-body class="overflow-y-auto px-6 py-5"></ol>
                <div class="flex justify-end border-t border-slate-100 bg-slate-50 px-6 py-4"><button type="button" data-close class="{{ $btnG }}">Tutup</button></div>
            </div>
        </div>

        {{-- Konfirmasi --}}
        <div data-modal="confirm" class="fixed inset-0 z-50 hidden items-center justify-center p-4" role="alertdialog" aria-modal="true" aria-labelledby="cf-title">
            <div data-close class="absolute inset-0 bg-slate-900/50 backdrop-blur-[2px]"></div>
            <div data-panel class="relative w-full max-w-sm scale-95 overflow-hidden rounded-2xl bg-white p-6 text-center opacity-0 shadow-2xl transition duration-200">
                <span data-cf-icon class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-50 text-red-600">{!! $ic($p['warn'], 'h-6 w-6') !!}</span>
                <h3 id="cf-title" class="mt-3 text-[16px] font-bold text-slate-900"></h3>
                <p data-cf-text class="mt-1.5 text-[12px] leading-relaxed text-slate-600"></p>
                <div class="mt-5 flex justify-center gap-2">
                    <button type="button" data-close class="{{ $btnG }}">Batal</button>
                    <button type="button" data-cf-ok class="{{ $btnP }}"><span data-cf-label>Ya</span></button>
                </div>
            </div>
        </div>
    </div>

    {{-- Notifikasi singkat --}}
    <div id="toast" class="pointer-events-none fixed bottom-6 right-6 z-[60] flex translate-y-4 items-center gap-2 rounded-xl bg-slate-900 px-4 py-3 text-[12px] font-medium text-white opacity-0 shadow-xl transition duration-300" role="status" aria-live="polite"></div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const $ = (s, r = document) => r.querySelector(s);
        const $$ = (s, r = document) => [...r.querySelectorAll(s)];
        const fmt = (n) => Math.round(n).toLocaleString('id-ID');
        const rp = (n) => 'Rp ' + fmt(n);
        const esc = (v) => String(v ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

        // =====================================================================
        //  KONFIGURASI — ubah saat database sudah siap
        // =====================================================================
        const CONFIG = {
            // false = data hanya di browser (tampilan saja). true = memanggil endpoint di bawah.
            USE_API: true,
            csrf: @json(csrf_token()),
            urls: {
                tickets:  @json(url('/admin/tiket')),            // POST / PUT {id} / DELETE {id}
                quota:    @json(url('/admin/tiket/{id}/kuota')), // PUT  body: { cap }
                openAll:  @json(url('/admin/tiket/buka-semua')), // POST
            },
        };
        const LIVE_DEMO = false; // simulasi tiket terjual & sinkronisasi; matikan saat memakai data asli
        const USER = 'Bagas Dananjaya (Super Admin)';
        const STEP = 25;

        // Pembungkus fetch (server membalas JSON record yang tersimpan)
        const http = async (method, url, body) => {
            const res = await fetch(url, {
                method,
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CONFIG.csrf },
                body: body ? JSON.stringify(body) : undefined,
            });
            if (!res.ok) {
                const err = await res.json().catch(() => ({}));
                throw new Error(err.message || 'Terjadi kesalahan pada server.');
            }
            if (res.status === 204) return true;
            const json = await res.json();
            // Endpoint tiket membalas { ok, data: {...} }; kembalikan record-nya langsung.
            return json && typeof json === 'object' && json.data && typeof json.data === 'object' ? json.data : json;
        };

        // =====================================================================
        //  STATE
        // =====================================================================
        const state = {
            tickets: @json($tickets).map((t) => ({ ...t, updatedAt: Date.now() - t.updatedMin * 60000 })),
            sessions: @json($sessions),
            log: @json($log),
            revenue: 12400000,
            variant: 'classic',
            draftCap: null,
        };
        const byId = (id) => state.tickets.find((t) => t.id === id);

        const ICONS = {
            users: @json($ic($p['users'], 'h-3.5 w-3.5')),
            warn: @json($ic($p['warn'], 'h-3.5 w-3.5')),
            clock: @json($ic($p['clock'], 'h-4 w-4')),
            calendar: @json($ic($p['calendar'], 'h-4 w-4')),
            checkc: @json($ic($p['checkc'], 'h-3.5 w-3.5')),
            history: @json($ic($p['history'], 'h-4 w-4')),
            thermo: @json($ic($p['thermo'], 'h-3 w-3')),
            star: @json($ic($p['star'], 'h-3 w-3')),
            warnS: @json($ic($p['warn'], 'h-3 w-3')),
        };

        // =====================================================================
        //  UTIL
        // =====================================================================
        const animate = (el, to, { dur = 900, format = fmt } = {}) => {
            if (!el) return;
            const from = Number(el.dataset.val ?? 0);
            el.dataset.val = to;
            if (reduce) { el.textContent = format(to); return; }
            const t0 = performance.now();
            const tick = (now) => {
                const p = Math.min((now - t0) / dur, 1);
                el.textContent = format(from + (to - from) * (1 - Math.pow(1 - p, 3)));
                if (p < 1) requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
        };

        let toastTimer;
        const toast = (msg, type = 'ok') => {
            const t = $('#toast');
            t.textContent = msg;
            t.classList.toggle('bg-red-600', type === 'err');
            t.classList.toggle('bg-slate-900', type !== 'err');
            t.classList.remove('opacity-0', 'translate-y-4');
            clearTimeout(toastTimer);
            toastTimer = setTimeout(() => t.classList.add('opacity-0', 'translate-y-4'), 2600);
        };

        const ago = (ts) => {
            const m = Math.floor((Date.now() - ts) / 60000);
            if (m < 1) return 'baru saja';
            if (m < 60) return `${m}m lalu`;
            return `${Math.floor(m / 60)}j lalu`;
        };
        const nowWIB = () => new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false, timeZone: 'Asia/Jakarta' }).replace('.', ':') + ' WIB';

        const addLog = (text, ticket = '', tone = 'green') => {
            state.log.unshift({ who: USER, time: nowWIB(), text, tone, ticket });
            state.log = state.log.slice(0, 40);
            renderLog();
        };

        // =====================================================================
        //  MODAL
        // =====================================================================
        let openName = null, lastFocus = null;
        const openModal = (name) => {
            const m = $(`[data-modal="${name}"]`);
            if (openName) closeModal(true);
            lastFocus = document.activeElement;
            openName = name;
            m.classList.remove('hidden'); m.classList.add('flex');
            document.body.classList.add('overflow-hidden');
            const panel = $('[data-panel]', m);
            requestAnimationFrame(() => requestAnimationFrame(() => panel.classList.remove('opacity-0', 'scale-95')));
            setTimeout(() => ($('input:not([type=hidden]), select, textarea, button[data-close]', panel) || panel).focus(), 60);
        };
        const closeModal = (instant = false) => {
            if (!openName) return;
            const m = $(`[data-modal="${openName}"]`);
            $('[data-panel]', m).classList.add('opacity-0', 'scale-95');
            const done = () => { m.classList.add('hidden'); m.classList.remove('flex'); };
            (instant || reduce) ? done() : setTimeout(done, 180);
            openName = null;
            document.body.classList.remove('overflow-hidden');
            if (lastFocus && lastFocus.focus) lastFocus.focus();
        };
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });
        document.addEventListener('click', (e) => { if (e.target.closest('[data-close]')) closeModal(); });

        const setBusy = (formEl, busy, label) => {
            const btn = $('[data-submit]', formEl);
            btn.disabled = busy;
            $('[data-submit-label]', btn).textContent = busy ? 'Menyimpan...' : label;
        };

        // Konfirmasi berbasis Promise
        let confirmResolve = null;
        const askConfirm = ({ title, text, label, tone = 'danger' }) => new Promise((resolve) => {
            confirmResolve = resolve;
            $('#cf-title').textContent = title;
            $('[data-cf-text]').innerHTML = text;
            $('[data-cf-label]').textContent = label;
            const danger = tone === 'danger';
            $('[data-cf-icon]').className = `mx-auto flex h-12 w-12 items-center justify-center rounded-full ${danger ? 'bg-red-50 text-red-600' : 'bg-emerald-50 text-emerald-700'}`;
            $('[data-cf-ok]').className = `inline-flex items-center justify-center gap-2 rounded-lg px-5 py-2.5 text-[12px] font-bold text-white transition active:scale-95 ${danger ? 'bg-red-600 hover:bg-red-700' : 'bg-[#0B3A22] hover:bg-[#124c2f]'}`;
            openModal('confirm');
        });
        $('[data-cf-ok]').addEventListener('click', () => { const r = confirmResolve; confirmResolve = null; closeModal(); r && r(true); });
        document.addEventListener('click', (e) => { if (e.target.closest('[data-close]') && confirmResolve) { confirmResolve(false); confirmResolve = null; } });

        // =====================================================================
        //  STATISTIK
        // =====================================================================
        const renderStats = () => {
            const cap = state.tickets.reduce((a, t) => a + t.cap, 0); // termasuk kuota rencana paket draf
            const sold = state.tickets.reduce((a, t) => a + t.sold, 0);
            const pct = cap ? (sold / cap) * 100 : 0;
            animate($('[data-sold-total]'), sold);
            $('[data-cap-total]').textContent = fmt(cap);
            $('[data-pct]').textContent = pct.toFixed(1) + '%';
            $('[data-cap-bar]').style.width = Math.min(100, pct) + '%';
            $('[data-left]').textContent = `Tersisa ${fmt(Math.max(0, cap - sold))} kuota gerbang`;
            animate($('[data-revenue]'), state.revenue, { dur: 1200 });

            const active = state.tickets.filter((t) => t.status === 'active');
            const drafts = state.tickets.filter((t) => t.status === 'draft');
            $('[data-portfolio-count]').textContent = active.length;
            $('[data-portfolio-text]').textContent = active.length ? `${active.map((t) => t.portfolio).join(' & ')} aktif dijual.` : 'Belum ada kategori tiket yang aktif dijual.';
            $('[data-draft-text]').textContent = drafts.length === 0 ? 'Tidak ada paket dalam draf'
                : drafts.length === 1 ? `1 Paket ${drafts[0].short} dalam draf` : `${drafts.length} paket dalam draf`;
            $('[data-count]').textContent = `${state.tickets.length} Terdata`;
        };

        // =====================================================================
        //  KATALOG (READ)
        // =====================================================================
        const BTN = 'rounded-lg px-3.5 py-2 text-[11px] font-semibold transition active:scale-95';
        const chip = 'inline-flex items-center gap-1.5 rounded-lg bg-[#E4E8FB] px-2.5 py-1 text-[10px] font-semibold text-slate-700';

        const cardHTML = (t) => {
            const left = Math.max(0, t.cap - t.sold);
            const isActive = t.status === 'active';
            const vip = t.accent === 'vip';
            const low = isActive && left <= Math.max(15, t.cap * 0.15);
            const badge = vip ? 'KAPASITAS TERBATAS' : 'KATEGORI PUBLIK';
            const imgChip = vip ? `${ICONS.star} VIP Private` : (t.temp ? `${ICONS.thermo} ${esc(t.temp)}` : '');

            const ribbon = low ? `<span class="absolute right-0 top-0 inline-flex items-center gap-1.5 rounded-bl-xl rounded-tr-2xl bg-[#7A5230] px-3 py-1.5 text-[10px] font-bold text-white">${ICONS.warnS} ${left ? `Sisa ${left} Tiket - Hampir Habis` : 'Habis Terjual'}</span>` : '';

            const statusChip = !isActive
                ? (t.status === 'draft'
                    ? `<span class="shrink-0 rounded-2xl ${'bg-[#DDE6FB]'} px-2.5 py-1.5 text-center text-[9px] font-bold leading-tight text-slate-700">Draf<br>Rilis</span>`
                    : `<span class="shrink-0 rounded-2xl bg-slate-200 px-2.5 py-1.5 text-center text-[9px] font-bold leading-tight text-slate-600">Non-<br>aktif</span>`)
                : (low ? '' : `<span class="shrink-0 rounded-2xl bg-[#CDEFD5] px-2.5 py-1.5 text-center text-[10px] font-bold leading-tight text-[#0B3A22]">Aktif /<br><span class="inline-flex items-center gap-1"><span class="h-1.5 w-1.5 rounded-full bg-[#0B3A22]"></span>Buka</span></span>`);

            const media = `
                <div class="relative h-[140px] w-[140px] shrink-0 overflow-hidden rounded-xl bg-gradient-to-br from-[#2d5a4a] to-[#0B3A22] ${isActive ? '' : 'grayscale'}">
                    <img src="${esc(t.image)}" alt="" onerror="this.remove()" class="absolute inset-0 h-full w-full object-cover">
                    <span class="absolute inset-0 bg-gradient-to-t from-black/45 via-transparent to-transparent"></span>
                    ${imgChip ? `<span class="absolute left-2 top-2 inline-flex items-center gap-1 rounded-full bg-white/95 px-2 py-1 text-[10px] font-semibold text-slate-800">${imgChip}</span>` : ''}
                    ${isActive ? `<span class="absolute bottom-2 left-2 text-[9px] font-bold uppercase tracking-wide text-white">${badge}</span>` : `<span class="absolute inset-0 flex items-center justify-center"><span class="rounded-full bg-white px-3 py-1.5 text-[10px] font-bold tracking-wide text-slate-700 shadow">NONAKTIF</span></span>`}
                </div>`;

            const price = isActive
                ? `<p class="mt-2 text-[24px] font-bold leading-none text-slate-900">${rp(t.price)}<span class="ml-1 text-[11px] font-normal text-slate-600">/ ${esc(t.unit)}</span></p>`
                : `<p class="mt-2 text-[22px] font-bold leading-none text-slate-800">${rp(t.price)}<span class="ml-1 text-[11px] font-normal text-slate-600">/ ${esc(t.unit)} (Rencana kuota: ${t.cap} pax)</span></p>`;

            const quota = isActive ? `<p class="mt-2.5"><span class="inline-flex items-center gap-1.5 rounded-lg ${low ? 'bg-[#FBD9B0] text-[#6B4520]' : 'bg-[#E4E8FB] text-slate-700'} px-2.5 py-1 text-[10px] font-semibold">${low ? ICONS.warn : ICONS.users} ${t.cap} Kuota/hari (Sisa: ${left} tiket)</span></p>` : '';

            const schedule = !isActive ? '' : (t.sessions.length
                ? `<p class="mt-2.5 flex flex-wrap items-center gap-1.5 text-[11px]"><span class="flex items-center gap-1.5 font-semibold text-slate-700"><span class="text-[#8B5E34]">${ICONS.calendar}</span> ${t.sessions.length} Sesi Terjadwal:</span>${t.sessions.map((s) => `<span class="rounded-lg bg-[#E4E8FB] px-2 py-1 text-[10px] font-semibold text-slate-700">${esc(s)}</span>`).join('')}</p>`
                : (t.hours ? `<p class="mt-2.5 flex items-start gap-2 text-[11px] leading-snug text-slate-700"><span class="mt-0.5 text-[#8B5E34]">${ICONS.clock}</span> ${esc(t.hours)}</p>` : ''));

            const facilities = isActive && t.facilities.length ? `<div class="mt-2.5 flex flex-wrap gap-1.5">${t.facilities.map((f) => `<span class="${chip}">${ICONS.checkc} ${esc(f)}</span>`).join('')}</div>` : '';
            const desc = !isActive && t.desc ? `<p class="mt-2 text-[11px] leading-relaxed text-slate-600">${esc(t.desc)}</p>` : '';

            const actions = isActive
                ? `<button type="button" data-act="edit" class="${BTN} ${vip ? 'bg-[#7A5230] text-white hover:bg-[#6a4628]' : 'bg-[#DDE6FB] text-slate-800 hover:bg-[#cfdaf7]'}">Ubah Harga &amp; Kuota</button>
                   <button type="button" data-act="schedule" class="${BTN} bg-[#DDE6FB] text-slate-800 hover:bg-[#cfdaf7]">Lihat Jadwal</button>
                   <button type="button" data-act="history" class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-800" aria-label="Riwayat ${esc(t.name)}">${ICONS.history}</button>`
                : `<button type="button" data-act="activate" class="${BTN} bg-[#2d5a4a] text-white hover:bg-[#0B3A22]">Aktifkan Penjualan</button>
                   <button type="button" data-act="edit" class="${BTN} bg-[#DDE6FB] text-slate-800 hover:bg-[#cfdaf7]">Edit Detail</button>`;

            const note = isActive ? (low ? '<p class="mt-2 text-[10px] font-semibold text-[#8B5E34]">Tinggi Peminat</p>' : `<p class="mt-2 text-[10px] text-slate-500">Update ${ago(t.updatedAt)}</p>`) : '';

            return `
            <article data-ticket="${esc(t.id)}" class="relative rounded-2xl p-5 transition ${isActive ? 'bg-white border border-slate-200 shadow-sm hover:shadow-md' : 'bg-[#EEF1FD] border border-transparent'}">
                ${ribbon}
                <div class="flex gap-5">
                    ${media}
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 ${low ? 'pr-2 pt-5' : ''}">
                                <h3 class="text-[16px] font-bold leading-snug ${isActive ? 'text-slate-900' : 'text-slate-600'}">${esc(t.name)}</h3>
                                ${t.sku && isActive ? `<p class="mt-0.5 text-[10px] text-slate-500">SKU: ${esc(t.sku)}</p>` : ''}
                            </div>
                            ${statusChip}
                        </div>
                        ${price}${quota}${schedule}${facilities}${desc}
                        <div class="mt-3.5 flex flex-wrap items-center gap-2">${actions}</div>
                        ${note}
                    </div>
                </div>
            </article>`;
        };

        const renderCatalog = () => {
            $('#catalog').innerHTML = state.tickets.map(cardHTML).join('');
            $('#catalog-empty').classList.toggle('hidden', state.tickets.length > 0);
        };

        // =====================================================================
        //  MANAJER KUOTA GERBANG
        // =====================================================================
        const ON = ['bg-[#0B3A22]', 'text-white', 'shadow-sm'];
        const OFF = ['bg-[#DDE6FB]', 'text-slate-700', 'hover:bg-[#cfdaf7]'];

        const renderQuota = () => {
            const actives = state.tickets.filter((t) => t.status === 'active');
            if (!actives.find((t) => t.id === state.variant)) { state.variant = actives[0]?.id; state.draftCap = null; }
            const t = byId(state.variant);
            $('#variant-tabs').innerHTML = actives.map((a) =>
                `<button type="button" data-variant="${esc(a.id)}" class="flex items-center justify-between gap-2 rounded-xl px-3 py-2.5 text-left text-[11px] font-semibold transition active:scale-95 ${a.id === state.variant ? ON.join(' ') : OFF.join(' ')}"><span class="truncate">${esc(a.name)}</span><span class="shrink-0 rounded-md ${a.id === state.variant ? 'bg-white/20 text-white' : 'bg-white text-slate-700'} px-1.5 py-0.5 text-[10px] font-bold">${a.cap}</span></button>`).join('');
            $('#variant-actions').classList.toggle('hidden', !t);
            $('#variant-actions').classList.toggle('flex', !!t);

            const minus = $('#qm-minus'), plus = $('#qm-plus'), apply = $('#qm-apply');
            if (!t) {
                [minus, plus, apply].forEach((b) => (b.disabled = true));
                $('[data-qm-value]').textContent = '–';
                $('[data-qm-note]').textContent = 'Belum ada tiket aktif';
                return;
            }
            if (state.draftCap === null) state.draftCap = t.cap;
            const diff = state.draftCap - t.cap;
            animate($('[data-qm-value]'), state.draftCap, { dur: 350 });
            $('[data-qm-safe]').textContent = `Batas Aman ${t.safeMax} pax`;
            $('[data-qm-note]').textContent = diff === 0 ? 'Kapasitas Standar' : `${diff > 0 ? '+' : ''}${diff} dari standar`;
            $('[data-qm-note]').className = `mt-1 text-[10px] font-medium ${diff === 0 ? 'text-slate-600' : 'text-[#8B5E34]'}`;
            minus.disabled = state.draftCap - STEP < t.sold;
            plus.disabled = state.draftCap + STEP > t.safeMax;
            apply.disabled = diff === 0;
        };

        $('#variant-tabs').addEventListener('click', (e) => {
            const b = e.target.closest('[data-variant]');
            if (!b) return;
            state.variant = b.dataset.variant; state.draftCap = null; renderQuota();
        });
        $('#qm-minus').addEventListener('click', () => { state.draftCap -= STEP; renderQuota(); });
        $('#qm-plus').addEventListener('click', () => { state.draftCap += STEP; renderQuota(); });

        // CRUD tipe tiket langsung dari panel Manajer Kuota Gerbang
        $('#qm-add').addEventListener('click', () => openForm('create'));
        $('#qm-edit').addEventListener('click', () => { const t = byId(state.variant); if (t) openForm('edit', t, 'name'); });
        $('#qm-del').addEventListener('click', () => removeTicket(byId(state.variant)));

        $('#qm-apply').addEventListener('click', async () => {
            const t = byId(state.variant), diff = state.draftCap - t.cap;
            if (!t || diff === 0) return;
            const btn = $('#qm-apply'), label = $('[data-qm-apply-label]');
            btn.disabled = true; label.textContent = 'Menyinkronkan ke Gate Kiosk...';
            try {
                // UPDATE — PUT /admin/tiket/{id}/kuota  body: { cap }
                if (CONFIG.USE_API) await http('PUT', CONFIG.urls.quota.replace('{id}', t.id), { cap: state.draftCap });
                else await new Promise((r) => setTimeout(r, reduce ? 0 : 900));
                t.cap = state.draftCap; t.updatedAt = Date.now();
                addLog(`${diff > 0 ? 'Menambah' : 'Mengurangi'} kuota darurat ${diff > 0 ? '+' : ''}${diff} pax untuk Tiket ${t.short} via Fast Turnstile.`, t.id, 'green');
                state.draftCap = null;
                renderAll();
                toast(`Kuota ${t.short} diterapkan ke Gate Kiosk`);
            } catch (err) {
                toast(err.message, 'err');
            } finally {
                label.textContent = 'Terapkan Perubahan ke Gate Kiosk';
                renderQuota();
            }
        });

        // =====================================================================
        //  DISTRIBUSI SESI & LOG
        // =====================================================================
        const renderSessions = () => {
            $('#sessions').innerHTML = state.sessions.map((s) => {
                const r = s.used / s.cap;
                const dot = r >= 0.9 ? 'bg-red-600' : r >= 0.7 ? 'bg-[#7A5230]' : 'bg-[#0B3A22]';
                const num = r >= 0.9 ? 'text-slate-900' : r >= 0.7 ? 'text-[#8B5E34]' : 'text-slate-900';
                return `<div class="flex items-center gap-3 rounded-xl bg-[#EEF1FD] px-3 py-3">
                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-white text-[10px] font-bold text-slate-700">${esc(s.no)}</span>
                    <div class="min-w-0 flex-1"><p class="text-[11px] font-bold leading-tight text-slate-900">${esc(s.name)}</p><p class="text-[9px] leading-tight text-slate-500">${esc(s.note)}</p></div>
                    <p class="text-[11px] ${num}"><b class="text-[13px]">${s.used}</b><span class="text-slate-400"> / ${s.cap}</span></p>
                    <span class="h-2 w-2 shrink-0 rounded-full ${dot}"></span>
                </div>`;
            }).join('');
        };

        const TONE = { green: 'bg-[#0B3A22]', brown: 'bg-[#7A5230]', gray: 'bg-slate-300' };
        const logHTML = (list) => list.map((l, i) => `
            <li class="relative pl-6 ${i === list.length - 1 ? '' : 'pb-5'}">
                ${i === list.length - 1 ? '' : '<span class="absolute left-[4px] top-3 h-full w-px bg-slate-200"></span>'}
                <span class="absolute left-0 top-1 h-[9px] w-[9px] rounded-full ${TONE[l.tone] || TONE.gray}"></span>
                <p class="flex justify-between gap-2 text-[10px]"><b class="text-slate-900">${esc(l.who)}</b><span class="shrink-0 text-slate-500">${esc(l.time)}</span></p>
                <p class="mt-0.5 text-[10px] leading-relaxed text-slate-600">${esc(l.text)}</p>
            </li>`).join('');
        const renderLog = () => { $('#log').innerHTML = logHTML(state.log.slice(0, 5)); };

        // =====================================================================
        //  FORM TIKET (CREATE & UPDATE & DELETE)
        // =====================================================================
        const form = $('#ticket-form');
        let formMode = 'create';
        const f = (n) => form.elements[n];
        const list = (v) => v.split(',').map((s) => s.trim()).filter(Boolean);

        const clearErrors = () => {
            $$('[data-err]', form).forEach((el) => { el.textContent = ''; el.classList.add('hidden'); });
            $$('input, select, textarea', form).forEach((el) => el.classList.remove('border-red-400'));
            $('[data-form-error]').classList.add('hidden');
        };

        const openForm = (mode, t = null, focus = '') => {
            formMode = mode; clearErrors(); form.reset();
            $('#tf-title').textContent = mode === 'create' ? 'Tambah Kategori Tiket' : (t.status === 'active' ? 'Ubah Harga & Kuota' : 'Edit Detail Tiket');
            $('#tf-sub').textContent = mode === 'create' ? 'Isi data katalog tiket baru.' : `${t.name} • SKU ${t.sku}`;
            f('id').value = t ? t.id : '';
            f('name').value = t ? t.name : '';
            f('price').value = t ? t.price : '';
            f('unit').value = t ? t.unit : 'orang';
            f('cap').value = t ? t.cap : '';
            f('accent').value = t ? t.accent : 'public';
            f('temp').value = t ? t.temp : '';
            f('status').value = t ? t.status : 'active';
            f('hours').value = t ? t.hours : '';
            f('sessions').value = t ? t.sessions.join(', ') : '';
            f('facilities').value = t ? t.facilities.join(', ') : '';
            f('desc').value = t ? t.desc : '';
            $('#tf-delete').classList.toggle('hidden', mode === 'create');
            $('#tf-delete').classList.toggle('inline-flex', mode !== 'create');
            $('[data-submit-label]', form).textContent = mode === 'create' ? 'Simpan Tiket' : 'Simpan Perubahan';
            openModal('ticket');
            if (focus) setTimeout(() => f(focus).focus(), 80);
        };

        const makeSku = (name) => {
            const abbr = name.replace(/^tiket\s+/i, '').replace(/[^A-Za-z]/g, '').slice(0, 3).toUpperCase().padEnd(3, 'X');
            return `JWN-TKT-${abbr}-${String(state.tickets.length + 1).padStart(2, '0')}`;
        };

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            clearErrors();
            const id = f('id').value, old = byId(id);
            const d = {
                name: f('name').value.trim(), price: Number(f('price').value), unit: f('unit').value, cap: Number(f('cap').value),
                accent: f('accent').value, temp: f('temp').value.trim(), status: f('status').value, hours: f('hours').value.trim(),
                sessions: list(f('sessions').value), facilities: list(f('facilities').value), desc: f('desc').value.trim(),
                kategori: (old && old.kategori) || 'renang',
            };
            const errs = {};
            if (d.name.length < 3) errs.name = 'Nama tiket minimal 3 karakter.';
            if (!(d.price >= 1000)) errs.price = 'Harga minimal Rp 1.000.';
            if (!(d.cap >= 1)) errs.cap = 'Kuota minimal 1.';
            else if (old && d.cap < old.sold) errs.cap = `Tidak boleh di bawah tiket terjual (${old.sold}).`;
            if (Object.keys(errs).length) {
                Object.entries(errs).forEach(([k, msg]) => { const el = $(`[data-err="${k}"]`, form); el.textContent = msg; el.classList.remove('hidden'); f(k).classList.add('border-red-400'); });
                return;
            }

            const label = formMode === 'create' ? 'Simpan Tiket' : 'Simpan Perubahan';
            setBusy(form, true, label);
            try {
                let saved;
                if (formMode === 'create') {
                    // CREATE — POST /admin/tiket
                    const short = d.name.replace(/^tiket\s+(kolam\s+)?/i, '').split(' ').slice(0, 2).join(' ');
                    saved = CONFIG.USE_API
                        ? await http('POST', CONFIG.urls.tickets, d)
                        : { ...d, id: 'tk-' + Date.now(), sku: makeSku(d.name), short, portfolio: d.name, sold: 0, safeMax: Math.max(d.cap + 50, Math.round(d.cap * 1.25)), image: '', updatedAt: Date.now() };
                    saved.updatedAt = Date.now();
                    state.tickets.push(saved);
                    addLog(`Menambahkan kategori tiket baru "${saved.name}" (${rp(saved.price)}, kuota ${saved.cap}).`, saved.id, 'green');
                } else {
                    // UPDATE — PUT /admin/tiket/{id}
                    saved = CONFIG.USE_API ? await http('PUT', `${CONFIG.urls.tickets}/${id}`, d) : { ...old, ...d };
                    saved.updatedAt = Date.now();
                    const parts = [];
                    if (saved.price !== old.price) parts.push(`harga ${rp(old.price)} → ${rp(saved.price)}`);
                    if (saved.cap !== old.cap) parts.push(`kuota ${old.cap} → ${saved.cap} pax`);
                    if (saved.status !== old.status) parts.push(`status ${old.status} → ${saved.status}`);
                    state.tickets[state.tickets.indexOf(old)] = saved;
                    addLog(`Memperbarui Tiket ${saved.short}${parts.length ? ': ' + parts.join(', ') : ''}.`, saved.id, 'brown');
                }
                state.draftCap = null;
                renderAll();
                closeModal();
                toast(formMode === 'create' ? `Tiket "${saved.name}" ditambahkan` : `Tiket "${saved.name}" diperbarui`);
            } catch (err) {
                const box = $('[data-form-error]'); box.textContent = err.message; box.classList.remove('hidden');
            } finally {
                setBusy(form, false, label);
            }
        });

        // DELETE — DELETE /admin/tiket/{id}
        const removeTicket = async (t) => {
            if (!t) return;
            if (t.sold > 0) {
                if (openName === 'ticket') {
                    const box = $('[data-form-error]');
                    box.textContent = `Tiket tidak bisa dihapus karena sudah terjual ${t.sold} hari ini. Ubah status menjadi Nonaktif saja.`;
                    box.classList.remove('hidden');
                } else {
                    toast(`Tidak bisa dihapus: ${t.name} sudah terjual ${t.sold} hari ini.`, 'err');
                }
                return;
            }
            const ok = await askConfirm({ title: 'Hapus Kategori Tiket?', text: `<b>${esc(t.name)}</b> akan dihapus dari katalog dan tidak dapat dikembalikan.`, label: 'Ya, Hapus' });
            if (!ok) return;
            try {
                if (CONFIG.USE_API) await http('DELETE', `${CONFIG.urls.tickets}/${t.id}`);
                state.tickets = state.tickets.filter((x) => x.id !== t.id);
                if (state.variant === t.id) state.variant = null;
                addLog(`Menghapus kategori tiket "${t.name}" dari katalog.`, '', 'gray');
                state.draftCap = null;
                renderAll();
                toast(`Tiket "${t.name}" dihapus`);
            } catch (err) { toast(err.message, 'err'); }
        };

        $('#tf-delete').addEventListener('click', () => {
            const t = byId(f('id').value);
            if (!t) return;
            if (t.sold > 0) { removeTicket(t); return; }
            closeModal(true);
            removeTicket(t);
        });

        // =====================================================================
        //  JADWAL & RIWAYAT PER TIKET
        // =====================================================================
        const openSchedule = (t) => {
            $('#sc-title').textContent = 'Jadwal ' + t.short;
            $('[data-sc-sub]').textContent = t.name;
            const rows = t.sessions.length
                ? t.sessions.map((s, i) => `<div class="flex items-center justify-between rounded-xl bg-[#EEF1FD] px-4 py-3"><span class="flex items-center gap-3 text-[12px] font-semibold text-slate-900"><span class="flex h-7 w-7 items-center justify-center rounded-lg bg-white text-[10px] font-bold">${String(i + 1).padStart(2, '0')}</span>${esc(s)}</span><span class="rounded-md bg-[#CDEFD5] px-2 py-1 text-[9px] font-bold">Dibuka</span></div>`).join('')
                : `<div class="rounded-xl bg-[#EEF1FD] px-4 py-3 text-[12px] leading-relaxed text-slate-700"><b class="block text-slate-900">Fleksibel tanpa sesi kaku</b>${esc(t.hours || 'Jam operasional belum diatur.')}</div>`;
            $('[data-sc-body]').innerHTML = rows + `<p class="pt-1 text-[10px] text-slate-500">Kuota ${t.cap} tiket/hari • terjual ${t.sold} • sisa ${Math.max(0, t.cap - t.sold)}.</p>`;
            openModal('schedule');
        };

        const openHistory = (t) => {
            const entries = state.log.filter((l) => l.ticket === t.id);
            $('[data-hs-sub]').textContent = t.name;
            $('[data-hs-body]').innerHTML = entries.length ? logHTML(entries) : '<li class="py-6 text-center text-[12px] text-slate-500">Belum ada riwayat perubahan untuk tiket ini.</li>';
            openModal('history');
        };

        // =====================================================================
        //  AKSI KARTU KATALOG
        // =====================================================================
        $('#catalog').addEventListener('click', async (e) => {
            const btn = e.target.closest('[data-act]');
            if (!btn) return;
            const t = byId(btn.closest('[data-ticket]').dataset.ticket);
            const act = btn.dataset.act;
            if (act === 'edit') openForm('edit', t, t.status === 'active' ? 'price' : 'name');
            if (act === 'schedule') openSchedule(t);
            if (act === 'history') openHistory(t);
            if (act === 'activate') {
                const ok = await askConfirm({ title: 'Aktifkan Penjualan?', text: `<b>${esc(t.name)}</b> akan tampil di loket online dan gate kiosk.`, label: 'Aktifkan', tone: 'ok' });
                if (!ok) return;
                try {
                    // UPDATE — PUT /admin/tiket/{id}  body: { status: 'active' }
                    if (CONFIG.USE_API) await http('PUT', `${CONFIG.urls.tickets}/${t.id}`, { ...t, status: 'active' });
                    t.status = 'active'; t.updatedAt = Date.now();
                    addLog(`Mengaktifkan penjualan Tiket ${t.short} (kuota ${t.cap} pax).`, t.id, 'green');
                    renderAll(); toast(`${t.short} kini aktif dijual`);
                } catch (err) { toast(err.message, 'err'); }
            }
        });

        $('#add-ticket').addEventListener('click', () => openForm('create'));

        // Buka seluruh penjualan: aktifkan semua tiket berstatus Nonaktif
        $('#open-all').addEventListener('click', async () => {
            const paused = state.tickets.filter((t) => t.status === 'inactive');
            if (!paused.length) { toast('Semua penjualan sudah terbuka. Paket draf perlu diaktifkan manual.'); return; }
            const ok = await askConfirm({ title: 'Buka Seluruh Penjualan?', text: `${paused.length} tiket nonaktif akan dibuka: <b>${paused.map((t) => esc(t.short)).join(', ')}</b>.`, label: 'Buka Semua', tone: 'ok' });
            if (!ok) return;
            try {
                // POST /admin/tiket/buka-semua
                if (CONFIG.USE_API) await http('POST', CONFIG.urls.openAll);
                paused.forEach((t) => { t.status = 'active'; t.updatedAt = Date.now(); });
                addLog(`Membuka seluruh penjualan tiket (${paused.length} kategori).`, '', 'green');
                renderAll(); toast('Seluruh penjualan dibuka');
            } catch (err) { toast(err.message, 'err'); }
        });

        // Periode
        const pbtn = $('#period-btn'), pmenu = $('#period-menu');
        const togglePeriod = (open) => { pmenu.classList.toggle('hidden', !open); pbtn.setAttribute('aria-expanded', String(open)); };
        pbtn.addEventListener('click', (e) => { e.stopPropagation(); togglePeriod(pmenu.classList.contains('hidden')); });
        $$('[data-period-opt]').forEach((o) => o.addEventListener('click', () => { $('[data-period]').textContent = o.dataset.periodOpt; togglePeriod(false); toast('Periode: ' + o.dataset.periodOpt); }));
        document.addEventListener('click', (e) => { if (!e.target.closest('#period-menu')) togglePeriod(false); });

        // =====================================================================
        //  RENDER & REAL-TIME
        // =====================================================================
        const renderAll = () => { renderStats(); renderCatalog(); renderQuota(); renderSessions(); renderLog(); };

        // Mulai: angka naik dari 0, bar terisi bertahap
        ['[data-sold-total]', '[data-revenue]', '[data-qm-value]'].forEach((s) => ($(s).dataset.val = 0));
        $('[data-cap-bar]').style.width = '0%';
        renderAll();
        requestAnimationFrame(() => requestAnimationFrame(() => renderStats()));

        // "Update Xm lalu" ikut berjalan
        setInterval(() => { if (!openName) renderCatalog(); }, 30000);

        // Simulasi penjualan masuk dari POS / gate kiosk
        if (LIVE_DEMO) {
            setInterval(() => {
                if (openName) return;
                const sellable = state.tickets.filter((t) => t.status === 'active' && t.sold < t.cap);
                if (!sellable.length) return;
                const t = sellable[Math.floor(Math.random() * sellable.length)];
                t.sold += 1;
                state.revenue += t.price;
                const slots = state.sessions.filter((s) => s.used < s.cap && (t.accent === 'vip' || !s.vipOnly));
                if (slots.length) slots[Math.floor(Math.random() * slots.length)].used += 1;
                const icon = $('[data-sync-icon]');
                if (icon.animate && !reduce) icon.animate([{ transform: 'rotate(0)' }, { transform: 'rotate(360deg)' }], { duration: 700, easing: 'ease-in-out' });
                renderStats(); renderCatalog(); renderQuota(); renderSessions();
            }, 6000);
        }
    });
</script>
@endpush
