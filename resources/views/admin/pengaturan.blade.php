@extends('layouts.admin')

@section('title', 'Pengaturan Profil & Keamanan Akun - Jiwanta')
@section('container-class') max-w-6xl space-y-5 @endsection

@section('page-content')

<style>
    @keyframes jw-rise{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}
    @keyframes jw-wiggle{0%,100%{transform:rotate(0)}20%{transform:rotate(-14deg)}40%{transform:rotate(12deg)}60%{transform:rotate(-8deg)}80%{transform:rotate(5deg)}}
    @keyframes jw-shrink{from{width:100%}to{width:0}}
    @keyframes jw-ripple{to{transform:scale(4);opacity:0}}
    @keyframes jw-flash{0%{box-shadow:0 0 0 0 rgba(11,58,34,.35)}100%{box-shadow:0 0 0 14px rgba(11,58,34,0)}}
    @keyframes jw-pop{0%{transform:scale(.6);opacity:.4}70%{transform:scale(1.08)}100%{transform:scale(1);opacity:1}}
    @keyframes jw-shake{0%,100%{transform:translateX(0)}25%{transform:translateX(-5px)}75%{transform:translateX(5px)}}
    [data-reveal]{opacity:0;transform:translateY(18px);transition:opacity .6s ease,transform .7s cubic-bezier(.2,.8,.2,1)}
    [data-reveal].in{opacity:1;transform:none}
    .jw-wiggle{animation:jw-wiggle .7s}
    .jw-flash{animation:jw-flash .9s}
    .jw-pop{animation:jw-pop .4s}
    .jw-shake{animation:jw-shake .35s}
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
        'shield'    => 'M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6l7-3z',
        'shieldck'  => 'M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6l7-3zM9 12l2 2 4-4',
        'user'      => 'M12 12a4 4 0 100-8 4 4 0 000 8zM5 20v-1a5 5 0 015-5h4a5 5 0 015 5v1',
        'idcard'    => 'M3 6h18v12H3zM8 12a2 2 0 100-4 2 2 0 000 4zM6 16c0-1.5 1-2 2-2s2 .5 2 2M14 10h4M14 14h4',
        'lock'      => 'M6 11h12v9H6zM8 11V8a4 4 0 018 0v3',
        'bank'      => 'M3 10l9-6 9 6M5 10v8M9 10v8M15 10v8M19 10v8M3 20h18',
        'wallet'    => 'M3 7h16a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7zM3 7l12-3v3M16 14h2',
        'laptop'    => 'M5 6h14v10H5zM2 19h20',
        'cam'       => 'M4 8h3l2-3h6l2 3h3v11H4zM12 17a3.5 3.5 0 100-7 3.5 3.5 0 000 7z',
        'checkc'    => 'M21 12a9 9 0 11-18 0 9 9 0 0118 0zM8.5 12.5l2.5 2.5 4.5-5',
        'check'     => 'M5 13l4 4L19 7',
        'right'     => 'M9 6l6 6-6 6',
        'copy'      => 'M8 8h11v12H8zM5 16V4h11',
        'pencil'    => 'M4 20h4L19 9l-4-4L4 16v4zM13 7l4 4',
        'plus'      => 'M12 5v14M5 12h14',
        'save'      => 'M5 4h12l3 3v13H5zM8 4v5h8V4M8 20v-6h8v6',
        'x'         => 'M6 6l12 12M18 6L6 18',
        'power'     => 'M12 3v8M7 6.5a8 8 0 1010 0',
        'radar'     => 'M12 3l8 4.5v9L12 21l-8-4.5v-9L12 3zM12 9v6M9 12h6',
        'headset'   => 'M4 14v-2a8 8 0 0116 0v2M4 14h3v5H5a1 1 0 01-1-1v-4zM20 14h-3v5h2a1 1 0 001-1v-4z',
    ];

    $nav = [
        'Navigasi Utama' => [['Dashboard', 'dashboard', false, null], ['Verifikasi & Transaksi', 'receipt', false, 14]],
        'Manajemen Resort' => [['Kelola Tiket', 'ticket', false, null], ['Kelola Cabin Suite', 'cabin', false, null]],
        'Administrasi & Sistem' => [['Manajemen Pengguna', 'users', false, null], ['Laporan & Export', 'chart', false, null], ['Pengaturan Profil', 'gear', true, null]],
    ];

    $mint = 'bg-[#CDEFD5]';
    $peach = 'bg-[#FBD9B0]';
    $lav = 'bg-[#DDE6FB]';
    $card = 'rounded-2xl bg-white shadow-sm border border-slate-100';
    $field = 'mt-1.5 w-full rounded-xl bg-[#EEF2FC] px-4 py-3.5 text-[12px] font-medium text-slate-800 placeholder-slate-400 border border-transparent focus:border-[#0B3A22]/40 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0B3A22]/15 transition';
    $lbl = 'block text-[10px] font-semibold text-slate-700';

    $menu = [
        ['info', 'Informasi Pribadi & Kontak', 'idcard', null],
        ['sandi', 'Keamanan & Sandi', 'lock', ['2FA Aktif', 'bg-[#CDEFD5] text-[#0B3A22]']],
        ['bank', 'Rekening Bank Resort', 'bank', ['2 Bank', 'bg-[#DDE6FB] text-slate-700']],
        ['notif', 'Notifikasi & WhatsApp Gateway', 'bell', 'dot'],
        ['sesi', 'Sesi Login & Riwayat', 'laptop', ['2 Aktif', 'text-slate-600']],
    ];

    $notifs = [
        ['n1', 'Notifikasi WhatsApp instan saat ada reservasi kabin baru', 'Kirim detail nama pemesan, tipe cabin suite, dan tanggal check-in langsung ke grup operasional.'],
        ['n2', 'Peringatan jika sisa kuota tiket kolam di bawah 20 pax', 'Membantu loket gate turnstile mengatur antrean pengunjung walk-in pada jam sibuk kabut sore.'],
        ['n3', 'Notifikasi peringatan sensor suhu air belerang jika melebihi 42°C', 'Sistem IoT kolam belerang Ciwidey akan memicu sirine preventif dan WhatsApp darurat ke tim maintenance.'],
    ];
@endphp


                {{-- Header --}}
                <div class="flex items-start justify-between gap-6">
                    <div class="max-w-2xl">
                        <h1 class="text-[28px] font-bold leading-tight text-[#0B3A22]">Pengaturan Profil &amp; Keamanan Akun</h1>
                        <p class="mt-1.5 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 rounded-full {{ $mint }} px-2.5 py-1 text-[9px] font-bold text-[#0B3A22]">{!! $ic($p['checkc'], 'h-3 w-3') !!} Akun Super Admin Terverifikasi (Role Level 1)</span>
                            <span class="flex items-center gap-1.5 text-[9px] font-medium text-slate-500"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span><span data-live>Disimpan: 2 Menit Lalu</span></span>
                        </p>
                        <p class="mt-2 text-[12px] leading-relaxed text-slate-600">Kelola informasi identitas akun super admin, preferensi sistem, rekening bank penerima, dan keamanan login resort secara terintegrasi.</p>
                    </div>
                    <div class="flex shrink-0 items-center gap-3 pt-2">
                        <button type="button" data-reset class="rounded-full {{ $lav }} px-5 py-2.5 text-[11px] font-semibold text-slate-800 transition hover:brightness-95">Batal / Reset</button>
                        <button type="button" data-save class="relative flex items-center gap-2 rounded-full bg-[#0B3A22] px-6 py-3 text-[11px] font-bold text-white shadow-sm transition hover:bg-[#124c2f]">
                            {!! $ic($p['save'], 'h-4 w-4') !!} Simpan Perubahan
                            <span data-dirty class="absolute -right-0.5 -top-0.5 hidden h-3 w-3 rounded-full border-2 border-white bg-[#C2762B]"></span>
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-[250px_1fr] items-start gap-5">

                    {{-- ===== Kolom Kiri ===== --}}
                    <div class="sticky top-0 space-y-4">
                        <div data-reveal class="{{ $card }} relative overflow-hidden p-5">
                            <div class="pointer-events-none absolute -right-10 -top-10 h-28 w-28 rounded-full bg-[#CDEFD5]/60 blur-2xl"></div>
                            <div class="pointer-events-none absolute -bottom-10 -left-10 h-24 w-24 rounded-full bg-[#FBD9B0]/60 blur-2xl"></div>
                            <div class="relative mx-auto h-[84px] w-[84px]">
                                <img data-avatar src="{{ asset('images/profil.jpg') }}" alt="Bagas Dananjaya" class="h-full w-full rounded-full bg-[#C9B99A] object-cover border-4 border-white shadow-md">
                                <button type="button" data-cam class="absolute -bottom-1 -right-1 flex h-7 w-7 items-center justify-center rounded-full border-2 border-white bg-[#0B3A22] text-white transition hover:bg-[#124c2f]" aria-label="Ganti foto">{!! $ic($p['cam'], 'h-3.5 w-3.5') !!}</button>
                                <input data-file type="file" accept="image/*" class="hidden">
                            </div>
                            <div class="relative mt-3 text-center">
                                <p data-name-card class="text-[16px] font-bold text-slate-900">Bagas Dananjaya</p>
                                <p class="mt-0.5 text-[10px] font-medium text-[#8B5E34]">Super Admin Resort &amp; Wisata Jiwanta</p>
                                <p class="mt-2 text-[9px] font-medium text-slate-600">ID: JW-ADM-001 • Bergabung: Maret 2024</p>
                            </div>
                            <div class="relative mt-3 flex items-center justify-between rounded-xl bg-[#EEF2FC] px-3 py-3">
                                <span class="flex items-center gap-1.5 text-[9px] font-semibold text-slate-700">{!! $ic($p['shield'], 'h-3.5 w-3.5') !!} Status Akses</span>
                                <span class="rounded-md {{ $mint }} px-2 py-1 text-[9px] font-bold text-[#0B3A22]">Tier 1 • Penuh</span>
                            </div>
                        </div>

                        <div data-reveal class="{{ $card }} p-2">
                            <ul class="space-y-1">
                                @foreach ($menu as [$k, $t, $i, $b])
                                    <li>
                                        <button type="button" data-menu="{{ $k }}" class="flex w-full items-center gap-2.5 rounded-lg px-3 py-2.5 text-left text-[11px] font-medium transition {{ $loop->first ? 'bg-[#0B3A22] text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                                            {!! $ic($p[$i], 'h-4 w-4 shrink-0') !!}
                                            <span class="flex-1">{{ $t }}</span>
                                            @if ($k === 'info')
                                                <span data-chev>{!! $ic($p['right'], 'h-3.5 w-3.5') !!}</span>
                                            @elseif ($b === 'dot')
                                                <span class="h-2 w-2 rounded-full bg-[#0B3A22]"></span>
                                            @else
                                                <span @if ($k === 'bank') data-bank-badge @elseif ($k === 'sesi') data-sesi-badge @elseif ($k === 'sandi') data-2fa-badge @endif class="rounded-md px-2 py-0.5 text-[9px] font-bold {{ $b[1] }}">{{ $b[0] }}</span>
                                            @endif
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        <div data-reveal class="rounded-2xl {{ $lav }}/50 bg-[#E9EEFB] p-4">
                            <p class="flex items-center gap-1.5 text-[9px] font-bold uppercase tracking-wide text-[#8B5E34]">{!! $ic($p['headset'], 'h-3.5 w-3.5') !!} Bantuan Teknis IT Resort</p>
                            <p class="mt-2 text-[10px] leading-relaxed text-slate-700">Pembaruan hak akses atau pergantian gateway pembayaran resmi memerlukan verifikasi token fisik IT Jiwanta.</p>
                        </div>
                    </div>

                    {{-- ===== Kolom Kanan ===== --}}
                    <div class="space-y-5">

                        {{-- Informasi Pribadi --}}
                        <section id="sec-info" data-sec="info" data-reveal class="{{ $card }} p-6">
                            <div class="flex items-start gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#E3E9FA] text-slate-700">{!! $ic($p['idcard'], 'h-4 w-4') !!}</span>
                                <div>
                                    <h2 class="text-[16px] font-bold text-slate-900">Informasi Pribadi &amp; Kontak</h2>
                                    <p class="text-[10px] text-slate-600">Data administratif yang terhubung ke dokumen legal dan tanda tangan persetujuan sistem.</p>
                                </div>
                            </div>
                            <div class="mt-5 grid grid-cols-2 gap-x-4 gap-y-4">
                                <label class="{{ $lbl }}">Nama Lengkap<input data-f="name" value="Bagas Dananjaya, S.Par" class="{{ $field }}"></label>
                                <label class="{{ $lbl }}">Email Resmi Resort<input data-f="email" type="email" value="bagas.dananjaya@jiwanta.com" class="{{ $field }}"></label>
                                <label class="{{ $lbl }}">No. WhatsApp Operasional
                                    <span class="relative block">
                                        <input data-f="wa" value="+62 812-4455-9011" class="{{ $field }} pr-10">
                                        <span data-wa-ok class="absolute right-4 top-1/2 -translate-y-1/2 text-[#0B3A22]">{!! $ic($p['checkc'], 'h-4 w-4') !!}</span>
                                    </span>
                                </label>
                                <label class="{{ $lbl }}">Posisi / Jabatan<input data-f="job" value="Super Admin & Manajer Operasional Wisata Ciwidey" class="{{ $field }}"></label>
                            </div>
                        </section>

                        {{-- Rekening Bank --}}
                        <section id="sec-bank" data-sec="bank" data-reveal class="{{ $card }} p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-start gap-3">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#E3E9FA] text-slate-700">{!! $ic($p['wallet'], 'h-4 w-4') !!}</span>
                                    <div>
                                        <h2 class="text-[16px] font-bold leading-tight text-slate-900">Rekening Bank Penampung Transfer Manual</h2>
                                        <p class="mt-0.5 max-w-md text-[10px] leading-snug text-slate-600">Daftar rekening tujuan untuk verifikasi pembayaran tiket dan sewa kabin via transfer manual.</p>
                                    </div>
                                </div>
                                <button type="button" data-bank-add class="flex shrink-0 items-center gap-2 rounded-full {{ $lav }} px-5 py-2.5 text-center text-[11px] font-bold leading-tight text-slate-800 transition hover:brightness-95">{!! $ic($p['plus'], 'h-3.5 w-3.5') !!} <span>Tambah<br>Rekening Bank</span></button>
                            </div>
                            <div id="banks" class="mt-5 space-y-3"></div>
                        </section>

                        {{-- Keamanan --}}
                        <section id="sec-sandi" data-sec="sandi" data-reveal class="{{ $card }} p-6">
                            <div class="flex items-start gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#E3E9FA] text-slate-700">{!! $ic($p['shieldck'], 'h-4 w-4') !!}</span>
                                <div>
                                    <h2 class="text-[16px] font-bold text-slate-900">Keamanan Akun &amp; Kredensial</h2>
                                    <p class="max-w-lg text-[10px] leading-snug text-slate-600">Pastikan kata sandi berkala diperbarui untuk mencegah akses tidak sah pada operasional reservasi.</p>
                                </div>
                            </div>
                            <div class="mt-5 grid grid-cols-3 gap-4">
                                <label class="{{ $lbl }}">Kata Sandi Lama<input data-f="pw0" type="password" value="Jiwanta2026!" class="{{ $field }}"></label>
                                <label class="{{ $lbl }}">Kata Sandi Baru<input data-f="pw1" type="password" placeholder="Minimal 8 karakter unik" class="{{ $field }}"></label>
                                <label class="{{ $lbl }}">Konfirmasi Kata Sandi<input data-f="pw2" type="password" placeholder="Ulangi kata sandi baru" class="{{ $field }}"></label>
                            </div>
                            <div data-strength class="mt-3 hidden items-center gap-3">
                                <div class="h-1.5 flex-1 overflow-hidden rounded-full bg-slate-100"><div data-strength-bar class="h-full w-0 rounded-full transition-all duration-500"></div></div>
                                <span data-strength-text class="w-28 text-right text-[10px] font-bold"></span>
                            </div>
                            <div class="mt-5 flex items-center justify-between gap-4 rounded-2xl bg-[#EEF2FC] p-4">
                                <div class="flex items-start gap-3">
                                    <span class="mt-0.5 text-slate-700">{!! $ic($p['shield'], 'h-4 w-4') !!}</span>
                                    <div>
                                        <p class="flex items-center gap-2 text-[12px] font-bold text-slate-900">Two-Factor Authentication (2FA) <span data-2fa-pill class="rounded-full {{ $mint }} px-2 py-0.5 text-[9px] font-bold text-[#0B3A22]">Aktif</span></p>
                                        <p class="mt-1 max-w-xs text-[10px] leading-snug text-slate-600">Verifikasi kode OTP melalui WhatsApp Gateway (<span data-wa-mask>+62 812-****-9011</span>) &amp; Google Authenticator.</p>
                                    </div>
                                </div>
                                <button type="button" data-2fa class="shrink-0 rounded-full {{ $lav }} px-5 py-2.5 text-[11px] font-bold text-slate-800 transition hover:brightness-95">Konfigurasi Ulang 2FA</button>
                            </div>
                            <div class="mt-5 text-right">
                                <button type="button" data-logout-all class="inline-flex items-center gap-1.5 text-[11px] font-bold text-red-700 transition hover:underline">{!! $ic($p['power'], 'h-3.5 w-3.5') !!} Keluar dari Semua Perangkat Lain</button>
                            </div>
                        </section>

                        {{-- Notifikasi --}}
                        <section id="sec-notif" data-sec="notif" data-reveal class="{{ $card }} p-6">
                            <div class="flex items-start gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-[#E3E9FA] text-slate-700">{!! $ic($p['radar'], 'h-4 w-4') !!}</span>
                                <div>
                                    <h2 class="text-[16px] font-bold text-slate-900">Pengaturan Notifikasi Darurat Lapangan</h2>
                                    <p class="max-w-lg text-[10px] leading-snug text-slate-600">Kustomisasi ambang batas alarm dan pengiriman pesan bot WhatsApp real-time untuk kondisi kritis resort.</p>
                                </div>
                            </div>
                            <div class="mt-5 space-y-3">
                                @foreach ($notifs as [$id, $t, $d])
                                    <button type="button" role="switch" aria-checked="true" data-notif="{{ $id }}" class="flex w-full items-start gap-3 rounded-2xl bg-[#EEF2FC] px-4 py-4 text-left transition hover:brightness-[.98]">
                                        <span data-box class="mt-0.5 flex h-4 w-4 shrink-0 items-center justify-center rounded-[4px] bg-[#0B3A22] text-white transition">{!! $ic($p['check'], 'h-3 w-3') !!}</span>
                                        <span class="block">
                                            <span data-nt class="block text-[12px] font-bold text-slate-900">{{ $t }}</span>
                                            <span class="mt-1 block max-w-xl text-[10px] leading-snug text-slate-600">{{ $d }}</span>
                                        </span>
                                    </button>
                                @endforeach
                            </div>
                        </section>
                    </div>
                </div>
@endsection
@section('overlays')
    {{-- Modal --}}
    <div id="modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/40 p-4">
        <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl">
            <div class="flex items-start justify-between">
                <div>
                    <h3 data-m-title class="text-[16px] font-bold text-slate-900"></h3>
                    <p data-m-sub class="mt-0.5 text-[11px] text-slate-600"></p>
                </div>
                <button type="button" data-m-close class="rounded-lg p-1 text-slate-500 hover:bg-slate-100" aria-label="Tutup">{!! $ic($p['x'], 'h-5 w-5') !!}</button>
            </div>
            <div data-m-body class="mt-5 space-y-3"></div>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" data-m-close class="rounded-lg {{ $lav }} px-4 py-2 text-[11px] font-bold">Tutup</button>
                <button type="button" data-m-ok class="rounded-lg bg-[#0B3A22] px-5 py-2 text-[11px] font-bold text-white transition hover:bg-[#124c2f]">Simpan</button>
            </div>
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
        const P = {
            copy: 'M8 8h11v12H8zM5 16V4h11', pencil: 'M4 20h4L19 9l-4-4L4 16v4zM13 7l4 4', check: 'M5 13l4 4L19 7',
            x: 'M6 6l12 12M18 6L6 18', checkc: 'M21 12a9 9 0 11-18 0 9 9 0 0118 0zM8.5 12.5l2.5 2.5 4.5-5',
            xc: 'M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9 9l6 6M15 9l-6 6', trash: 'M5 7h14M10 7V4h4v3M7 7l1 13h8l1-13',
        };
        const ic = (d, c = 'h-4 w-4') => `<svg class="${c}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="${d}"/></svg>`;

        // ---------- Util ----------
        const toast = (msg) => {
            const t = document.createElement('div');
            t.className = 'relative flex items-center gap-2 overflow-hidden rounded-xl bg-[#0B3A22] px-4 py-3 text-[11px] font-semibold text-white shadow-lg opacity-0 translate-x-6 transition-all duration-300';
            t.innerHTML = '<span class="h-2 w-2 animate-pulse rounded-full bg-[#B5F0BE]"></span>' + msg + '<span class="absolute bottom-0 left-0 h-0.5 bg-[#B5F0BE]" style="animation:jw-shrink 3.2s linear forwards"></span>';
            $('#toasts').appendChild(t);
            requestAnimationFrame(() => t.classList.remove('opacity-0', 'translate-x-6'));
            setTimeout(() => { t.classList.add('opacity-0', 'translate-x-6'); setTimeout(() => t.remove(), 300); }, 3200);
        };
        const anim = (el, c) => { if (!el) return; el.classList.remove(c); void el.offsetWidth; el.classList.add(c); };
        const bell = () => anim($('[data-bell]'), 'jw-wiggle');
        const esc = (s) => String(s).replace(/[&<>"]/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));

        let lastUpdate = Date.now() - 120000;
        const touch = () => { lastUpdate = Date.now(); };
        setInterval(() => {
            const s = Math.floor((Date.now() - lastUpdate) / 1000);
            $('[data-live]').textContent = 'Disimpan: ' + (s < 10 ? 'Baru saja' : s < 60 ? s + ' Detik Lalu' : Math.floor(s / 60) + ' Menit Lalu');
        }, 1000);

        // ---------- Modal ----------
        const modal = $('#modal');
        let onOk = null;
        const INP = 'mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-[12px] font-semibold focus:outline-none focus:ring-2 focus:ring-[#0B3A22]/30';
        const LBL = 'block text-[10px] font-bold uppercase text-slate-600';
        const openModal = ({ title, sub = '', body = '', ok = 'Simpan', hideOk = false }, cb) => {
            $('[data-m-title]').textContent = title;
            $('[data-m-sub]').textContent = sub;
            $('[data-m-body]').innerHTML = body;
            const b = $('[data-m-ok]');
            b.textContent = ok; b.classList.toggle('hidden', hideOk);
            onOk = cb;
            modal.classList.remove('hidden'); modal.classList.add('flex');
            const f = $('[data-m-body] input, [data-m-body] select'); if (f) f.focus();
        };
        const closeModal = () => { modal.classList.add('hidden'); modal.classList.remove('flex'); };
        $$('[data-m-close]').forEach((b) => b.addEventListener('click', closeModal));
        modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });
        $('[data-m-ok]').addEventListener('click', () => { if (onOk && onOk() === false) return; closeModal(); });
        const mv = (k) => ($(`[data-mf="${k}"]`).value || '').trim();

        // ---------- State awal & dirty tracking ----------
        const INIT = { name: 'Bagas Dananjaya, S.Par', email: 'bagas.dananjaya@jiwanta.com', wa: '+62 812-4455-9011', job: 'Super Admin & Manajer Operasional Wisata Ciwidey' };
        const F = (k) => $(`[data-f="${k}"]`);
        const dirty = () => Object.keys(INIT).some((k) => F(k).value !== INIT[k]) || F('pw1').value || F('pw2').value || notifDirty() || avatarDirty;
        const syncDirty = () => $('[data-dirty]').classList.toggle('hidden', !dirty());

        // ---------- Form info ----------
        const waOk = () => /^\+?62\s?8\d{2}[-\s]?\d{3,4}[-\s]?\d{3,4}$/.test(F('wa').value.trim());
        const syncWa = () => {
            const ok = waOk(), el = $('[data-wa-ok]');
            el.className = 'absolute right-4 top-1/2 -translate-y-1/2 ' + (ok ? 'text-[#0B3A22]' : 'text-red-600');
            el.innerHTML = ic(ok ? P.checkc : P.xc);
            const d = F('wa').value.replace(/\D/g, '');
            if (ok) $('[data-wa-mask]').textContent = '+62 ' + d.slice(2, 5) + '-****-' + d.slice(-4);
        };
        ['name', 'email', 'wa', 'job'].forEach((k) => F(k).addEventListener('input', () => {
            if (k === 'wa') syncWa();
            syncDirty();
        }));

        // ---------- Avatar ----------
        let avatarDirty = false, avatarOld = null;
        $('[data-cam]').addEventListener('click', () => $('[data-file]').click());
        $('[data-file]').addEventListener('change', (e) => {
            const f = e.target.files[0];
            if (!f) return;
            if (f.size > 2 * 1024 * 1024) return toast('Ukuran foto maksimal 2 MB.');
            const r = new FileReader();
            r.onload = () => {
                avatarOld = avatarOld || $('[data-avatar]').src;
                $('[data-avatar]').src = r.result; $('[data-avatar-mini]').src = r.result;
                avatarDirty = true; anim($('[data-avatar]'), 'jw-pop'); syncDirty();
                toast('Foto baru dipilih. Klik Simpan Perubahan.');
            };
            r.readAsDataURL(f);
        });

        // ---------- Password ----------
        const score = (v) => [v.length >= 8, /[A-Z]/.test(v) && /[a-z]/.test(v), /\d/.test(v), /[^A-Za-z0-9]/.test(v), v.length >= 12].filter(Boolean).length;
        F('pw1').addEventListener('input', () => {
            const v = F('pw1').value, box = $('[data-strength]');
            box.classList.toggle('hidden', !v); box.classList.toggle('flex', !!v);
            const s = score(v);
            const [t, c, w] = s <= 2 ? ['Lemah', 'bg-red-500', 20 + s * 15] : s <= 3 ? ['Cukup', 'bg-[#C2762B]', 65] : s === 4 ? ['Kuat', 'bg-[#0B3A22]', 85] : ['Sangat Kuat', 'bg-[#0B3A22]', 100];
            const bar = $('[data-strength-bar]');
            bar.className = 'h-full rounded-full transition-all duration-500 ' + c; bar.style.width = w + '%';
            $('[data-strength-text]').textContent = t;
            syncDirty();
        });
        F('pw2').addEventListener('input', syncDirty);

        // ---------- Rekening bank ----------
        const LOGO = { BCA: 'text-[#0B3A22]', MDR: 'text-[#8B5E34]', BNI: 'text-[#0B3A22]' };
        const BANKS_INIT = [
            { id: 1, code: 'BCA', no: '4370-9008-21', an: 'PT Jiwanta Wisata Mandiri', br: 'KCU Kopo Bandung', main: true },
            { id: 3, code: 'BNI', no: '0891-2334-11', an: 'PT Jiwanta Wisata Mandiri', br: 'KCP Soreang' },
        ];
        let BANKS = JSON.parse(JSON.stringify(BANKS_INIT)), bid = 3;
        const bankHTML = (b, i, a) => `<div data-bank="${b.id}" class="flex items-center gap-4 rounded-2xl bg-[#EEF2FC] px-4 py-4 transition" style="${a ? `animation:jw-rise .5s cubic-bezier(.2,.8,.2,1) ${i * 70}ms backwards` : ''}">
            <span class="flex h-12 w-14 shrink-0 items-center justify-center rounded-xl bg-white text-[13px] font-extrabold shadow-sm ${LOGO[b.code] || 'text-slate-800'}">${esc(b.code)}</span>
            <div class="min-w-0 flex-1">
                <p class="flex items-center gap-2 text-[17px] font-bold leading-tight text-slate-900">${esc(b.no)}<span class="rounded-full ${b.main ? 'bg-[#CDEFD5] text-[#0B3A22]' : 'bg-[#DDE6FB] text-slate-700'} px-2 py-0.5 text-[9px] font-bold">${b.main ? 'Aktif Utama' : 'Aktif'}</span></p>
                <p class="mt-0.5 text-[10px] text-slate-600">a/n ${esc(b.an)} • ${esc(b.br)}</p>
            </div>
            <button type="button" data-bcopy="${b.id}" class="rounded-lg p-2 text-slate-600 transition hover:bg-white" aria-label="Salin">${ic(P.copy)}</button>
            <button type="button" data-bedit="${b.id}" class="rounded-lg p-2 text-slate-600 transition hover:bg-white" aria-label="Edit">${ic(P.pencil)}</button>
        </div>`;
        const renderBanks = (a = true) => {
            $('#banks').innerHTML = BANKS.map((b, i) => bankHTML(b, i, a)).join('');
            $('[data-bank-badge]').textContent = BANKS.length + ' Bank';
        };
        $('#banks').addEventListener('click', (e) => {
            const c = e.target.closest('[data-bcopy]'), d = e.target.closest('[data-bedit]');
            if (c) {
                const b = BANKS.find((x) => x.id == c.dataset.bcopy);
                (navigator.clipboard ? navigator.clipboard.writeText(b.no) : Promise.reject()).then(() => 0, () => 0);
                c.innerHTML = ic(P.check, 'h-4 w-4 text-[#0B3A22]'); anim(c, 'jw-pop');
                setTimeout(() => (c.innerHTML = ic(P.copy)), 1400);
                toast('Rekening ' + b.code + ' ' + b.no + ' disalin.');
            }
            if (d) bankModal(BANKS.find((x) => x.id == d.dataset.bedit));
        });
        const bankModal = (b) => {
            const isNew = !b;
            b = b || { code: 'BCA', no: '', an: 'PT Jiwanta Wisata Mandiri', br: '' };
            openModal({ title: isNew ? 'Tambah Rekening Bank' : 'Edit Rekening Bank', sub: isNew ? 'Rekening baru langsung aktif' : b.code, ok: isNew ? 'Tambah' : 'Simpan', body:
                `<label class="${LBL}">Bank<select data-mf="code" class="${INP}">${['BCA', 'MDR', 'BNI', 'BRI'].map((c) => `<option ${c === b.code ? 'selected' : ''}>${c}</option>`).join('')}</select></label>
                 <label class="${LBL}">No. Rekening<input data-mf="no" value="${esc(b.no)}" placeholder="0000-0000-00" class="${INP}"></label>
                 <label class="${LBL}">Atas Nama<input data-mf="an" value="${esc(b.an)}" class="${INP}"></label>
                 <label class="${LBL}">Cabang<input data-mf="br" value="${esc(b.br)}" placeholder="KCU / KC / KCP" class="${INP}"></label>
                 ${isNew ? '' : `<label class="flex items-center gap-2 text-[11px] font-semibold text-slate-700"><input data-mf="main" type="checkbox" ${b.main ? 'checked' : ''}> Jadikan rekening utama</label>`}` }, () => {
                if (!/^[\d-]{6,}$/.test(mv('no')) || !mv('an') || !mv('br')) { toast('Lengkapi nomor rekening, nama, dan cabang.'); return false; }
                if (isNew) { BANKS.push({ id: ++bid, code: mv('code'), no: mv('no'), an: mv('an'), br: mv('br') }); }
                else {
                    Object.assign(b, { code: mv('code'), no: mv('no'), an: mv('an'), br: mv('br') });
                    if ($('[data-mf="main"]').checked) BANKS.forEach((x) => (x.main = x.id === b.id));
                }
                renderBanks(false); touch(); bell();
                const el = $(`[data-bank="${isNew ? bid : b.id}"]`); anim(el, 'jw-flash');
                toast(isNew ? 'Rekening ' + mv('code') + ' ditambahkan.' : 'Rekening ' + mv('code') + ' diperbarui.');
            });
        };
        $('[data-bank-add]').addEventListener('click', () => bankModal(null));

        // ---------- 2FA ----------
        $('[data-2fa]').addEventListener('click', () => {
            const otp = String(Math.floor(100000 + Math.random() * 900000));
            openModal({ title: 'Konfigurasi Ulang 2FA', sub: 'Kode OTP dikirim ke WhatsApp operasional', ok: 'Verifikasi', body:
                `<p class="rounded-lg bg-slate-50 p-3 text-[11px] text-slate-700">Kode demo: <b class="tracking-widest text-[#0B3A22]">${otp}</b></p>
                 <label class="${LBL}">Masukkan kode OTP<input data-mf="otp" inputmode="numeric" maxlength="6" placeholder="6 digit" class="${INP} tracking-[.4em]"></label>` }, () => {
                if (mv('otp') !== otp) { anim($('[data-m-body]'), 'jw-shake'); toast('Kode OTP tidak sesuai.'); return false; }
                touch(); bell(); anim($('[data-2fa-pill]'), 'jw-pop'); toast('2FA berhasil dikonfigurasi ulang.');
            });
        });

        // ---------- Sesi ----------
        let sesi = [['Chrome • Windows 11', 'Bandung, ID • Perangkat ini', true], ['Safari • iPhone 15', 'Ciwidey, ID • 12 menit lalu', false]];
        const syncSesi = () => ($('[data-sesi-badge]').textContent = sesi.length + ' Aktif');
        const sesiModal = () => openModal({ title: 'Sesi Login & Riwayat', sub: sesi.length + ' sesi aktif', hideOk: true, body: sesi.map((s) =>
            `<p class="rounded-lg bg-slate-50 p-3 text-[11px] text-slate-700"><b class="text-slate-900">${s[0]}</b>${s[2] ? ' <span class="ml-1 rounded bg-[#CDEFD5] px-1.5 py-0.5 text-[9px] font-bold text-[#0B3A22]">Saat ini</span>' : ''}<br>${s[1]}</p>`).join('') });
        $('[data-logout-all]').addEventListener('click', () => openModal({ title: 'Keluar dari Semua Perangkat Lain', sub: 'Sesi lain akan diakhiri', ok: 'Ya, Keluarkan', body: '<p class="text-[12px] text-slate-700">Semua perangkat selain perangkat ini harus login ulang menggunakan kata sandi dan 2FA.</p>' }, () => {
            sesi = sesi.filter((s) => s[2]); syncSesi(); touch(); bell(); anim($('[data-sesi-badge]'), 'jw-pop');
            toast('Semua perangkat lain telah dikeluarkan.');
        }));

        // ---------- Notifikasi ----------
        const NS = { n1: true, n2: true, n3: true };
        const notifDirty = () => $$('[data-notif]').some((b) => (b.getAttribute('aria-checked') === 'true') !== NS[b.dataset.notif]);
        $$('[data-notif]').forEach((b) => b.addEventListener('click', () => {
            const on = b.getAttribute('aria-checked') !== 'true';
            b.setAttribute('aria-checked', on);
            const box = $('[data-box]', b);
            box.className = 'mt-0.5 flex h-4 w-4 shrink-0 items-center justify-center rounded-[4px] transition ' + (on ? 'bg-[#0B3A22] text-white' : 'border-2 border-slate-400 bg-white text-transparent');
            anim(box, 'jw-pop'); syncDirty();
            toast($('[data-nt]', b).textContent.slice(0, 44) + '… ' + (on ? 'diaktifkan' : 'dinonaktifkan'));
        }));

        // ---------- Simpan & Reset ----------
        $('[data-save]').addEventListener('click', () => {
            const bad = (m, el) => { toast(m); anim(el, 'jw-shake'); el.focus(); };
            if (!F('name').value.trim()) return bad('Nama lengkap wajib diisi.', F('name'));
            if (!/^\S+@\S+\.\S+$/.test(F('email').value)) return bad('Format email tidak valid.', F('email'));
            if (!waOk()) return bad('Nomor WhatsApp tidak valid.', F('wa'));
            if (F('pw1').value || F('pw2').value) {
                if (!F('pw0').value) return bad('Isi kata sandi lama.', F('pw0'));
                if (score(F('pw1').value) < 3 || F('pw1').value.length < 8) return bad('Kata sandi baru terlalu lemah (min. 8 karakter, huruf besar/kecil & angka).', F('pw1'));
                if (F('pw1').value !== F('pw2').value) return bad('Konfirmasi kata sandi tidak sama.', F('pw2'));
                F('pw0').value = F('pw1').value; F('pw1').value = F('pw2').value = '';
                $('[data-strength]').classList.add('hidden'); $('[data-strength]').classList.remove('flex');
            }
            ['name', 'email', 'wa', 'job'].forEach((k) => (INIT[k] = F(k).value));
            $$('[data-notif]').forEach((b) => (NS[b.dataset.notif] = b.getAttribute('aria-checked') === 'true'));
            avatarDirty = false; avatarOld = null;
            $('[data-name-mini]').textContent = $('[data-name-card]').textContent = INIT.name.split(',')[0];
            syncDirty(); touch(); bell(); anim($('[data-save]'), 'jw-flash');
            toast('Perubahan profil berhasil disimpan.');
        });
        $('[data-reset]').addEventListener('click', () => {
            Object.keys(INIT).forEach((k) => (F(k).value = INIT[k]));
            F('pw1').value = F('pw2').value = '';
            $('[data-strength]').classList.add('hidden'); $('[data-strength]').classList.remove('flex');
            $$('[data-notif]').forEach((b) => { if ((b.getAttribute('aria-checked') === 'true') !== NS[b.dataset.notif]) b.click(); });
            if (avatarOld) { $('[data-avatar]').src = avatarOld; $('[data-avatar-mini]').src = avatarOld; avatarOld = null; }
            avatarDirty = false; syncWa(); syncDirty(); toast('Perubahan dibatalkan.');
        });
        window.addEventListener('beforeunload', (e) => { if (dirty()) { e.preventDefault(); e.returnValue = ''; } });

        // ---------- Menu & scrollspy ----------
        const setMenu = (k) => $$('[data-menu]').forEach((b) => {
            const on = b.dataset.menu === k;
            b.classList.toggle('bg-[#0B3A22]', on); b.classList.toggle('text-white', on);
            b.classList.toggle('text-slate-700', !on); b.classList.toggle('hover:bg-slate-50', !on);
            const c = $('[data-chev]', b); if (c) c.classList.toggle('hidden', !on);
        });
        $$('[data-menu]').forEach((b) => b.addEventListener('click', () => {
            const k = b.dataset.menu;
            if (k === 'sesi') return sesiModal();
            const el = $('#sec-' + k);
            el.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'start' });
            setMenu(k); anim(el, 'jw-flash');
        }));
        const spy = new IntersectionObserver((es) => es.forEach((en) => { if (en.isIntersecting) setMenu(en.target.dataset.sec); }), { root: $('#scroller'), rootMargin: '-10% 0px -70% 0px' });
        $$('[data-sec]').forEach((s) => spy.observe(s));

        // ---------- Efek global ----------
        const io = new IntersectionObserver((es) => es.forEach((en) => {
            if (!en.isIntersecting) return;
            en.target.classList.add('in');
            setTimeout(() => (en.target.style.transitionDelay = ''), 900);
            io.unobserve(en.target);
        }), { threshold: 0.06 });
        $$('[data-reveal]').forEach((el, i) => { el.style.transitionDelay = (i % 3) * 90 + 'ms'; io.observe(el); });

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

        // ---------- Live demo: peristiwa keamanan masuk real-time ----------
        if (LIVE_DEMO) {
            const EV = ['Login baru terdeteksi: Chrome • Android (Soreang)', 'Webhook WhatsApp Gateway terhubung ulang', 'Sensor suhu air belerang: 38°C (normal)', 'Kuota tiket kolam diperbarui: sisa 142 pax'];
            let n = 0;
            setInterval(() => {
                const e = EV[n++ % EV.length];
                if (n % 4 === 1 && sesi.length < 3) { sesi.push(['Chrome • Android', 'Soreang, ID • Baru saja', false]); syncSesi(); anim($('[data-sesi-badge]'), 'jw-pop'); }
                bell(); toast(e);
            }, 14000);
        }

        // ---------- Init ----------
        renderBanks(true);
        setMenu('info');
        syncWa();
    });
</script>
@endpush
