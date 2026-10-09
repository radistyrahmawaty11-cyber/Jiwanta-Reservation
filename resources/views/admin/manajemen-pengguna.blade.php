@extends('layouts.admin')

@section('title', 'Manajemen Pengguna & Hak Akses - Jiwanta')

@section('page-content')

<style>
    @keyframes jw-rise{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}
    @keyframes jw-wiggle{0%,100%{transform:rotate(0)}20%{transform:rotate(-14deg)}40%{transform:rotate(12deg)}60%{transform:rotate(-8deg)}80%{transform:rotate(5deg)}}
    @keyframes jw-shrink{from{width:100%}to{width:0}}
    @keyframes jw-ripple{to{transform:scale(4);opacity:0}}
    @keyframes jw-flash{0%{box-shadow:0 0 0 0 rgba(11,58,34,.35)}100%{box-shadow:0 0 0 14px rgba(11,58,34,0)}}
    @keyframes jw-pop{0%{transform:scale(.6);opacity:.4}70%{transform:scale(1.08)}100%{transform:scale(1);opacity:1}}
    @keyframes jw-fill{from{width:0}}
    [data-reveal]{opacity:0;transform:translateY(18px);transition:opacity .6s ease,transform .7s cubic-bezier(.2,.8,.2,1)}
    [data-reveal].in{opacity:1;transform:none}
    .jw-wiggle{animation:jw-wiggle .7s}
    .jw-flash{animation:jw-flash .9s}
    .jw-pop{animation:jw-pop .4s}
    .jw-edit [data-perm]:not([data-locked]){cursor:pointer;border-radius:6px}
    .jw-edit [data-perm]:not([data-locked]):hover{background:#f1f5f9}
    @media (prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}[data-reveal]{opacity:1;transform:none}}
</style>

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
        'tree'      => 'M12 3l4 6h-2.5l3.5 5h-3l3 5H7l3-5H7l3.5-5H8l4-6z',
        'shield'    => 'M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6l7-3z',
        'shieldck'  => 'M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6l7-3zM9 12l2 2 4-4',
        'briefcase' => 'M4 8h16v11H4zM9 8V6a1 1 0 011-1h4a1 1 0 011 1v2M4 13h16',
        'trend'     => 'M3 17l6-6 4 4 8-8M15 7h6v6',
        'cloudup'   => 'M7 18a4 4 0 01-.5-8 5.5 5.5 0 0110.8 1.2A3.5 3.5 0 0117 18M12 12v7M9 14.5l3-3 3 3',
        'userplus'  => 'M15 20v-1a4 4 0 00-4-4H6a4 4 0 00-4 4v1M8.5 11a4 4 0 100-8 4 4 0 000 8zM19 8v6M16 11h6',
        'search'    => 'M11 18a7 7 0 100-14 7 7 0 000 14zM21 21l-4.3-4.3',
        'sliders'   => 'M4 7h9M17 7h3M4 17h3M11 17h9M15 5v4M9 15v4',
        'checkc'    => 'M21 12a9 9 0 11-18 0 9 9 0 0118 0zM8.5 12.5l2.5 2.5 4.5-5',
        'xc'        => 'M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9 9l6 6M15 9l-6 6',
        'lock'      => 'M6 11h12v9H6zM8 11V8a4 4 0 018 0v3',
        'star'      => 'M12 3l2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9L12 3z',
        'gate'      => 'M5 5v14M19 5v14M5 12h14M9 8l6 8',
        'left'      => 'M15 6l-6 6 6 6',
        'right'     => 'M9 6l6 6-6 6',
        'x'         => 'M6 6l12 12M18 6L6 18',
        'check'     => 'M5 13l4 4L19 7',
    ];

    $nav = [
        'Navigasi Utama' => [['Dashboard', 'dashboard', false, null], ['Verifikasi & Transaksi', 'receipt', false, 14]],
        'Manajemen Resort' => [['Kelola Tiket', 'ticket', false, null], ['Kelola Cabin Suite', 'cabin', false, null]],
        'Administrasi & Sistem' => [['Manajemen Pengguna', 'users', true, null], ['Laporan & Export', 'chart', false, null], ['Pengaturan Profil', 'gear', false, null]],
    ];

    $mint = 'bg-[#CDEFD5]';
    $peach = 'bg-[#FBD9B0]';
    $lav = 'bg-[#DDE6FB]';

    $card = 'rounded-2xl bg-white shadow-sm border border-slate-100';

    $pills = [['all', 'Semua Akun', 'total'], ['super', 'Super Admin', 'super'], ['admin', 'Admin Operasional', 'admin'], ['gate', 'Petugas Gate & Loket', 'gate'], ['customer', 'Customer Terdaftar', 'customer']];

    $roles = [
        ['key' => 'super', 'label' => 'Super Admin', 'icon' => 'shield', 'pill' => 'bg-[#0B3A22] text-white', 'cnt' => 'super', 'locked' => true,
         'desc' => 'Otoritas tertinggi tanpa batas terhadap semua modul sistem resort.',
         'perms' => [['Dashboard & Rekap Keuangan', 'on'], ['Master Tiket & Cabin Suite', 'on'], ['Manajemen User & Hak Akses', 'on'], ['Laporan Lengkap & Export Audit', 'on']],
         'level' => '100% Modul Dibuka'],
        ['key' => 'gate', 'label' => 'Petugas Gate', 'icon' => 'gate', 'pill' => 'bg-[#FBD9B0] text-[#6B4520]', 'cnt' => 'cgate', 'locked' => false,
         'desc' => 'Operasional lapangan gerbang masuk & turnstile otomatis.',
         'perms' => [['Scanner QR E-Ticket Cepat', 'on'], ['Monitor Antrean Turnstile', 'on'], ['Override Buka Palang Darurat', 'lock'], ['Laporan Finansial', 'off']],
         'level' => 'Validasi & Entry Only'],
        ['key' => 'kasir', 'label' => 'Kasir Loket', 'icon' => 'ticket', 'pill' => 'bg-[#FBD9B0] text-[#6B4520]', 'cnt' => 'ckasir', 'locked' => false,
         'desc' => 'Pelayanan tamu on-the-spot dan penjualan langsung.',
         'perms' => [['Kasir Cepat POS Walk-in', 'on'], ['Cetak Tiket Fisik & Struk QR', 'on'], ['Rekonsiliasi Kas Harian Shift', 'on'], ['Konfigurasi Kuota Master', 'off']],
         'level' => 'POS Kasir & Struk'],
        ['key' => 'customer', 'label' => 'Customer', 'icon' => 'search', 'pill' => 'bg-[#DDE6FB] text-slate-800', 'cnt' => 'customer', 'locked' => false,
         'desc' => 'Akses publik untuk reservasi mandiri dan keanggotaan resort.',
         'perms' => [['Pemesanan Tiket & Cabin Online', 'on'], ['Upload Bukti Bayar / QRIS', 'on'], ['Unduh E-Ticket & Simpan Barcode', 'on'], ['Akumulasi Poin Keanggotaan', 'on']],
         'level' => 'Portal Tamu Eksternal'],
    ];
    $permStyle = [
        'on'   => ['checkc', 'text-[#0B3A22]', 'text-slate-800'],
        'lock' => ['lock', 'text-[#C2762B]', 'text-slate-800'],
        'off'  => ['xc', 'text-slate-400', 'text-slate-400 line-through'],
    ];
@endphp


                {{-- Header Section --}}
                <div class="flex items-start justify-between gap-6">
                    <div class="max-w-xl">
                        <p class="mb-2 flex items-center gap-2 text-[9px] font-bold uppercase tracking-wide">
                            <span class="rounded-md {{ $mint }} px-2 py-1 text-[#0B3A22]">Otorisasi &amp; RBAC</span>
                            <span class="flex items-center gap-1.5 font-medium normal-case text-slate-600"><span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span><span data-live>Pembaruan Realtime: 2 Menit Lalu</span></span>
                        </p>
                        <h1 class="text-[30px] font-bold leading-tight text-[#0B3A22]">Manajemen Pengguna &amp; Hak Akses</h1>
                        <p class="mt-2 text-[13px] leading-relaxed text-slate-600">Kelola akun staf internal resort, petugas gate scanner, kasir tiket, dan data pelanggan terdaftar dalam satu otoritas terpadu.</p>
                    </div>
                    <div class="flex flex-col items-end gap-3">
                        <button type="button" data-export class="flex items-center gap-2 rounded-full {{ $lav }} px-5 py-2.5 text-[11px] font-semibold text-[#0B3A22] transition hover:brightness-95">
                            {!! $ic($p['cloudup'], 'h-4 w-4') !!} Export Data Pengguna
                        </button>
                        <button type="button" data-add class="flex items-center gap-2 rounded-full bg-[#0B3A22] px-6 py-3 text-[11px] font-bold text-white shadow-sm transition hover:bg-[#124c2f]">
                            {!! $ic($p['userplus'], 'h-4 w-4') !!} + Tambah Akun Pengguna
                        </button>
                    </div>
                </div>

                {{-- Filter Pills --}}
                <div class="flex flex-wrap items-center gap-2">
                    @foreach ($pills as [$k, $l, $c])
                        <button type="button" data-role="{{ $k }}" class="rounded-full px-4 py-2 text-[11px] font-semibold transition {{ $loop->first ? 'bg-[#0B3A22] text-white shadow-sm' : $lav.' text-slate-800 hover:brightness-95' }}">{{ $l }} (<span data-c="{{ $c }}">0</span>)</button>
                    @endforeach
                </div>

                {{-- Stats Cards --}}
                <div class="grid grid-cols-4 gap-4">
                    <div data-reveal class="{{ $card }} p-5 hover:shadow-md">
                        <div class="flex items-start justify-between">
                            <p class="text-[11px] text-slate-600">Total Akun Terdaftar</p>
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl {{ $lav }}">{!! $ic($p['users'], 'h-4 w-4') !!}</span>
                        </div>
                        <p class="mt-1 text-[34px] font-bold leading-tight text-[#0B3A22]"><span data-c="total">0</span> <span class="text-[13px] font-medium text-slate-600">Akun</span></p>
                        <div class="mt-2 flex items-center gap-2">
                            <span class="rounded-md bg-[#E3E9FA] px-2 py-1 text-[9px] font-bold text-slate-700">Internal: <span data-c="internal">0</span></span>
                            <span class="rounded-md bg-[#E3E9FA] px-2 py-1 text-[9px] font-bold text-slate-700">Customer: <span data-c="customer">0</span></span>
                        </div>
                        <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-slate-100"><div style="width:90%;animation:jw-fill 1.4s cubic-bezier(.2,.8,.2,1)" class="h-full rounded-full bg-[#0B3A22]"></div></div>
                    </div>

                    <div data-reveal class="{{ $card }} p-5 hover:shadow-md">
                        <div class="flex items-start justify-between">
                            <p class="text-[11px] text-slate-600">Staf On-Duty Shift Ini</p>
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl {{ $mint }}">{!! $ic($p['briefcase'], 'h-4 w-4') !!}</span>
                        </div>
                        <p class="mt-1 text-[34px] font-bold leading-tight text-[#0B3A22]"><span data-duty>0</span> <span class="text-[13px] font-medium text-slate-600">Petugas Aktif</span></p>
                        <p class="mt-2 flex items-start gap-2 text-[10px] font-bold leading-snug text-slate-800"><span class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-[#0B3A22]"></span><span>Shift Pagi: Gate, Hot Spring, Tiketing</span></p>
                        <div data-dots class="mt-2 flex items-center gap-1.5"></div>
                    </div>

                    <div data-reveal class="{{ $card }} p-5 hover:shadow-md">
                        <div class="flex items-start justify-between">
                            <p class="text-[11px] text-slate-600">Customer Baru (Bulan Ini)</p>
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl {{ $peach }} text-[#8B5E34]">{!! $ic($p['trend'], 'h-4 w-4') !!}</span>
                        </div>
                        <p class="mt-1 text-[34px] font-bold leading-tight text-[#7A5230]">+<span data-c="newm">0</span> <span class="text-[13px] font-medium text-slate-600">Pengguna</span></p>
                        <p class="mt-2 text-[10px] leading-snug text-slate-800">Meningkat 24% dari periode bulan lalu</p>
                        <svg data-spark class="mt-2 h-8 w-full" viewBox="0 0 160 32" preserveAspectRatio="none" fill="none"><path stroke="#7A5230" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d=""/></svg>
                    </div>

                    <div data-reveal class="rounded-2xl bg-[#0B3A22] p-5 text-white shadow-sm hover:shadow-md">
                        <div class="flex items-start justify-between">
                            <p class="text-[11px] text-white/85">Status Keamanan Sistem</p>
                            <span class="text-white/90">{!! $ic($p['shieldck'], 'h-4 w-4') !!}</span>
                        </div>
                        <p class="mt-1 text-[34px] font-bold leading-tight">100% <span class="text-[11px] font-medium text-white/85">2FA Aktif</span></p>
                        <p class="mt-2 text-[10px] leading-snug text-white/85">Seluruh <span data-c="internal">0</span> staf resort terikat kunci FIDO2/OTP</p>
                        <p class="mt-3 flex items-center gap-2 rounded-lg bg-white/10 px-3 py-2 text-[10px] font-bold">{!! $ic($p['lock'], 'h-3.5 w-3.5') !!} Enkripsi AES-256 Aktif</p>
                    </div>
                </div>

                {{-- Search & Filter --}}
                <div data-reveal class="{{ $card }} p-4">
                    <div class="flex items-center gap-3">
                        <label class="flex flex-1 items-center gap-3 rounded-full bg-[#F1F4FC] px-4 py-3 text-slate-500">
                            {!! $ic($p['search'], 'h-4 w-4 shrink-0') !!}
                            <input data-q type="text" placeholder="Cari berdasarkan nama, email, atau no. WhatsApp staf / tamu..." class="w-full bg-transparent text-[11px] text-slate-800 placeholder-slate-500 focus:outline-none">
                        </label>
                        <label class="flex items-center gap-2 rounded-full bg-[#F1F4FC] px-4 py-3 text-[11px] text-slate-600">Status:
                            <select data-status class="cursor-pointer bg-transparent text-[11px] font-semibold text-slate-900 focus:outline-none">
                                <option value="all">Semua Status</option><option value="aktif">Aktif</option><option value="online">Online</option><option value="offline">Offline</option><option value="pending">Menunggu Verifikasi</option>
                            </select>
                        </label>
                        <label class="flex items-center gap-2 rounded-full bg-[#F1F4FC] px-4 py-3 text-[11px] text-slate-600">Departemen:
                            <select data-dept class="cursor-pointer bg-transparent text-[11px] font-semibold text-slate-900 focus:outline-none">
                                <option value="all">Semua Departemen</option><option>Manajemen</option><option>Gate &amp; Turnstile</option><option>Keamanan &amp; Kolam</option><option>Loket Tiket</option><option>Customer</option>
                            </select>
                        </label>
                        <button type="button" data-reset class="flex h-10 w-10 items-center justify-center rounded-full {{ $lav }} text-slate-700 transition hover:brightness-95" aria-label="Reset filter">{!! $ic($p['sliders'], 'h-4 w-4') !!}</button>
                    </div>
                </div>

                {{-- Users Table --}}
                <section data-reveal class="{{ $card }} overflow-hidden shadow-md">
                    <div class="grid grid-cols-[minmax(210px,2.1fr)_88px_minmax(150px,1.5fr)_minmax(100px,1fr)_minmax(130px,1.2fr)_minmax(150px,1.3fr)] items-center gap-3 bg-[#E9EEFB] px-5 py-4 text-[10px] font-semibold text-slate-700">
                        <span>Pengguna</span><span class="leading-tight">No.<br>WhatsApp</span><span class="leading-tight">Role &amp;<br>Departemen</span><span>Status Akun</span><span class="leading-tight">Shift /<br>Penugasan</span><span class="text-right">Aksi</span>
                    </div>
                    <div id="user-rows"></div>
                    <div class="flex items-center justify-between border-t border-slate-100 bg-slate-50/60 px-5 py-4">
                        <p data-showing class="text-[10px] text-slate-600">Menampilkan 6 dari 1.428 pengguna terdaftar</p>
                        <div id="pager" class="flex items-center gap-1.5"></div>
                    </div>
                </section>

                {{-- RBAC Matrix --}}
                <section id="rbac" data-reveal class="pt-2">
                    <div class="flex items-start justify-between gap-6">
                        <div>
                            <h2 class="text-[22px] font-bold text-[#0B3A22]">Matriks Otoritas &amp; Hak Akses (RBAC)</h2>
                            <p class="mt-1 text-[12px] text-slate-600">Ringkasan hak istimewa setiap role untuk menjaga integritas operasional dan keamanan finansial resort.</p>
                        </div>
                        <button type="button" data-rbac-btn class="flex shrink-0 items-center gap-2 rounded-full {{ $lav }} px-5 py-2.5 text-[11px] font-semibold text-[#0B3A22] transition hover:brightness-95">
                            <span data-rbac-icon>{!! $ic($p['sliders'], 'h-4 w-4') !!}</span> <span data-rbac-text>Konfigurasi Perizinan Role</span>
                        </button>
                    </div>

                    <div class="mt-5 grid grid-cols-4 items-start gap-4">
                        @foreach ($roles as $r)
                            <article data-rbac-card @if ($r['locked']) data-locked @endif class="rounded-2xl bg-white p-4 shadow-md border border-slate-100 transition hover:-translate-y-1 hover:shadow-lg">
                                <div class="flex items-center justify-between">
                                    <span class="inline-flex items-center gap-1.5 rounded-full {{ $r['pill'] }} px-3 py-1 text-[10px] font-bold">{!! $ic($p[$r['icon']], 'h-3 w-3') !!}{{ $r['label'] }}</span>
                                    <span class="text-[10px] text-slate-600"><span data-c="{{ $r['cnt'] }}">0</span> Akun</span>
                                </div>
                                <p class="mt-3 text-[11px] leading-relaxed text-slate-600">{{ $r['desc'] }}</p>
                                <ul class="mt-3 space-y-2">
                                    @foreach ($r['perms'] as [$pl, $ps])
                                        <li data-perm data-state="{{ $ps }}" @if ($r['locked']) data-locked @endif class="flex items-start gap-2 px-1 py-0.5 text-[11px] leading-snug">
                                            <span data-ico class="mt-px shrink-0 {{ $permStyle[$ps][1] }}">{!! $ic($p[$permStyle[$ps][0]], 'h-4 w-4') !!}</span>
                                            <span data-label class="{{ $permStyle[$ps][2] }}">{{ $pl }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="mt-4 flex items-start justify-between gap-3 rounded-xl bg-slate-50 px-3 py-2.5 text-[10px]">
                                    <span class="leading-tight text-slate-600">Tingkat<br>Akses:</span>
                                    <b class="max-w-[100px] text-right leading-snug text-[#0B3A22]">{{ $r['level'] }}</b>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
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
            <div data-m-body class="mt-5 space-y-4"></div>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" data-m-close class="rounded-lg {{ $lav }} px-4 py-2 text-[11px] font-bold">Tutup</button>
                <button type="button" data-m-ok class="rounded-lg bg-[#0B3A22] px-5 py-2 text-[11px] font-bold text-white transition hover:bg-[#124c2f]">Simpan</button>
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
        const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const fmt = (n) => Math.round(n).toLocaleString('id-ID');
        const IMG = "{{ asset('images/users') }}/";
        const P = {
            checkc: 'M21 12a9 9 0 11-18 0 9 9 0 0118 0zM8.5 12.5l2.5 2.5 4.5-5',
            xc: 'M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9 9l6 6M15 9l-6 6',
            lock: 'M6 11h12v9H6zM8 11V8a4 4 0 018 0v3',
            shield: 'M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6l7-3z',
            gear: 'M12 15a3 3 0 100-6 3 3 0 000 6zM12 2v3M12 19v3M2 12h3M19 12h3M5 5l2 2M17 17l2 2M19 5l-2 2M7 17l-2 2',
            gate: 'M5 5v14M19 5v14M5 12h14M9 8l6 8',
            pool: 'M3 19c1.5 0 1.5-1 3-1s1.5 1 3 1 1.5-1 3-1 1.5 1 3 1 1.5-1 3-1 1.5 1 3 1M8 15V6M14 15V6M8 8h6M8 12h6',
            ticket: 'M4 7h16v3a2 2 0 000 4v3H4v-3a2 2 0 000-4V7zM14 7v10',
            star: 'M12 3l2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9L12 3z',
            left: 'M15 6l-6 6 6 6',
            right: 'M9 6l6 6-6 6',
            sliders: 'M4 7h9M17 7h3M4 17h3M11 17h9M15 5v4M9 15v4',
            check: 'M5 13l4 4L19 7',
        };
        const ic = (d, c = 'h-4 w-4') => `<svg class="${c}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="${d}"/></svg>`;

        // ---------- Util: toast, flash, bell, tween ----------
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
        const tween = (el, to, dur = 900) => {
            if (!el) return;
            const from = Number(el.dataset.v ?? 0);
            el.dataset.v = to;
            if (reduce || from === to) { el.textContent = fmt(to); return; }
            const t0 = performance.now();
            const tick = (now) => {
                const p = Math.min((now - t0) / dur, 1);
                el.textContent = fmt(from + (to - from) * (1 - Math.pow(1 - p, 3)));
                if (p < 1) requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
        };

        // ---------- Data ----------
        const SH = { pagi: 'Shift Pagi (07:00 - 15:00)', siang: 'Shift Siang (15:00 - 23:00)', malam: 'Shift Malam (23:00 - 07:00)' };
        const ROLE = {
            super:    { label: 'Super Admin', cls: 'bg-[#0B3A22] text-white', icon: 'shield', group: 'super', sub: 'Akses Penuh Semua Modul' },
            admin:    { label: 'Admin Operasional', cls: 'bg-[#CDEFD5] text-[#0B3A22]', icon: 'gear', group: 'admin', sub: 'Kelola Tiket, Cabin & Fasilitas' },
            gate:     { label: 'Petugas Gate', cls: 'bg-[#FBD9B0] text-[#6B4520]', icon: 'gate', group: 'gate', sub: 'Turnstile Scanner & Validasi' },
            kolam:    { label: 'Petugas Keamanan & Kolam', cls: 'bg-[#DDE6FB] text-slate-800', icon: 'pool', group: 'gate', sub: 'Pengawas Pemandian & Kapasitas' },
            kasir:    { label: 'Kasir Loket Tiket', cls: 'bg-[#FBD9B0] text-[#6B4520]', icon: 'ticket', group: 'gate', sub: 'Penjualan Tiket Walk-in & EDC' },
            vip:      { label: 'Customer VIP', cls: 'bg-[#DDE6FB] text-slate-800', icon: 'star', group: 'customer', sub: 'Reservasi Cabin Suite' },
            customer: { label: 'Customer', cls: 'bg-[#DDE6FB] text-slate-800', icon: null, group: 'customer', sub: 'Pengunjung Regular' },
        };
        const ST = {
            aktif2fa: { t: 'Aktif (2FA)', bg: 'bg-[#CDEFD5]', dot: 'bg-[#0B3A22]' },
            online:   { t: 'Online / Aktif', bg: 'bg-[#CDEFD5]', dot: 'bg-[#0B3A22]', live: true },
            aktif:    { t: 'Aktif', bg: 'bg-[#CDEFD5]', dot: 'bg-[#0B3A22]' },
            pending:  { t: 'Menunggu Verifikasi', bg: 'bg-[#FBD9B0]', dot: 'bg-[#C2762B]' },
            offline:  { t: 'Offline', bg: 'bg-slate-100', dot: 'bg-slate-400' },
        };
        const LAV = 'bg-[#DDE6FB] text-slate-800';
        const ACT = {
            edit: ['Edit', LAV], log: ['Log Aktivitas', LAV], shift: ['Ganti Shift', LAV],
            resetpin: ['Reset PIN', 'bg-[#FBD5D5] text-red-700'], tutup: ['Tutup Kasir', LAV],
            riwayat: ['Riwayat Booking', LAV], resetpw: ['Reset Password', 'bg-slate-100 text-slate-400'],
            verif: ['Verifikasi Struk', LAV], detail: ['Detail', 'bg-slate-100 text-slate-500'],
        };
        const AV = ['bg-[#DDE6FB]', 'bg-[#FBD9B0]', 'bg-[#CDEFD5]'];

        let uid = 0;
        const U = (o) => ({ id: ++uid, status: 'aktif', actions: ['edit'], ...o });
        const USERS = [
            U({ name: 'Bagas Dananjaya', email: 'bagas.dananjaya@jiwanta.com', wa: '0812-4455-9011', role: 'super', dept: 'Manajemen', status: 'aktif2fa', cell: { t: 'Kantor Pusat Resort', s: 'Shift Manajemen Terpadu' }, actions: ['edit', 'log'], img: 'bagas.jpg' }),
            U({ name: 'Rian Hidayat', email: 'rian.h@jiwanta.com', wa: '0813-8822-4411', role: 'gate', dept: 'Gate & Turnstile', status: 'online', cell: { t: 'Pos Gate Utama', s: SH.pagi }, actions: ['edit', 'shift', 'resetpin'], img: 'rian.jpg' }),
            U({ name: 'Hendra Gunawan', email: 'hendra.g@jiwanta.com', wa: '0815-9922-1133', role: 'kolam', dept: 'Keamanan & Kolam', status: 'online', cell: { t: 'Area Hot Spring', s: SH.pagi }, actions: ['edit', 'shift'], img: 'hendra.jpg' }),
            U({ name: 'Siti Rahayu', email: 'siti.rahayu@jiwanta.com', wa: '0819-2233-4455', role: 'kasir', dept: 'Loket Tiket', status: 'online', cell: { t: 'Loket Depan Resepsionis', s: SH.pagi }, actions: ['edit', 'tutup'], img: 'siti.jpg' }),
            U({ name: 'Dewi Maharani', email: 'dewi.maharani@gmail.com', wa: '0811-9988-7766', role: 'vip', rsub: '8x Reservasi Cabin Suite', vip: true, dept: 'Customer', cell: { t: 'Member Sejak Jan 2026', s: 'Loyalty Poin: 2.450 pts', sa: true }, actions: ['riwayat', 'resetpw'], img: 'dewi.jpg' }),
            U({ name: 'Aris Munandar', email: 'aris.munandar@gmail.com', wa: '0812-5544-3322', role: 'customer', dept: 'Customer', status: 'pending', cell: { t: '1 Tiket Hot Spring Classic', s: 'Invoice: #INV-2026-0891' }, actions: ['verif', 'detail'] }),
            U({ name: 'Maya Kartika', email: 'maya.k@jiwanta.com', wa: '0821-7788-1200', role: 'super', dept: 'Manajemen', status: 'aktif2fa', cell: { t: 'Kantor Pusat Resort', s: 'Shift Manajemen Terpadu' }, actions: ['edit', 'log'] }),
            ...[['Dimas Prakoso', 'dimas.p', '0822-4410-9087'], ['Nurul Fadilah', 'nurul.f', '0857-3321-7740'], ['Tomi Hartono', 'tomi.h', '0878-9012-3345'], ['Rina Oktaviani', 'rina.o', '0813-6677-2290']].map(([n, e, w]) =>
                U({ name: n, email: e + '@jiwanta.com', wa: w, role: 'admin', dept: 'Manajemen', cell: { t: 'Kantor Operasional', s: 'Shift Manajemen Terpadu' }, actions: ['edit', 'log'] })),
            U({ name: 'Yusuf Ramdani', email: 'yusuf.r@jiwanta.com', wa: '0856-1122-9034', role: 'gate', dept: 'Gate & Turnstile', status: 'online', cell: { t: 'Pos Gate Samping', s: SH.pagi }, actions: ['edit', 'shift', 'resetpin'] }),
            U({ name: 'Lina Marlina', email: 'lina.m@jiwanta.com', wa: '0838-5521-7766', role: 'kasir', dept: 'Loket Tiket', status: 'online', cell: { t: 'Loket Cabin Suite', s: SH.pagi }, actions: ['edit', 'tutup'] }),
            U({ name: 'Eko Prasetyo', email: 'eko.p@jiwanta.com', wa: '0877-3300-1845', role: 'kasir', dept: 'Loket Tiket', status: 'offline', cell: { t: 'Loket Hot Spring', s: SH.malam }, actions: ['edit', 'tutup'] }),
        ];
        const COUNT = { customerBase: 1416, newm: 184 };
        const FN = ['Andi', 'Budi', 'Citra', 'Dedi', 'Eka', 'Fitri', 'Gilang', 'Hana', 'Irfan', 'Jihan', 'Kevin', 'Lestari', 'Mira', 'Naufal', 'Olivia', 'Putra', 'Rani', 'Satria', 'Tania', 'Umar', 'Vina', 'Wahyu', 'Yuni', 'Zaki'];
        const LN = ['Saputra', 'Wijaya', 'Lestari', 'Kurniawan', 'Permata', 'Santoso', 'Hidayat', 'Maulana', 'Pratiwi', 'Firmansyah'];
        const MO = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'];
        const CACHE = {};
        const gen = (i) => CACHE[i] || (CACHE[i] = (() => {
            const name = FN[i % FN.length] + ' ' + LN[(i * 7 + 3) % LN.length];
            const vip = i % 5 === 0, pend = i % 7 === 0, m = MO[i % 6];
            return {
                id: 'g' + i, name, email: name.toLowerCase().replace(/ /g, '.') + '@gmail.com',
                wa: `08${11 + (i % 9)}-${1000 + ((i * 37) % 9000)}-${1000 + ((i * 91) % 9000)}`,
                role: vip ? 'vip' : 'customer', rsub: vip ? `${3 + (i % 9)}x Reservasi Cabin Suite` : undefined, vip,
                dept: 'Customer', status: pend ? 'pending' : 'aktif',
                cell: pend ? { t: '1 Tiket Hot Spring Classic', s: 'Invoice: #INV-2026-' + String(900 + i).padStart(4, '0') }
                    : vip ? { t: 'Member Sejak ' + m + ' 2026', s: 'Loyalty Poin: ' + fmt(500 + ((i * 173) % 3000)) + ' pts', sa: true }
                    : { t: 'Member Reguler', s: 'Terdaftar ' + m + ' 2026' },
                actions: pend ? ['verif', 'detail'] : vip ? ['riwayat', 'resetpw'] : ['riwayat', 'detail'],
            };
        })());
        const S = (i) => (i < USERS.length ? USERS[i] : gen(i));
        const findUser = (id) => USERS.find((u) => String(u.id) === String(id)) || Object.values(CACHE).find((u) => String(u.id) === String(id));
        const grp = (u) => ROLE[u.role].group;

        // ---------- State & filter ----------
        const state = { role: 'all', q: '', status: 'all', dept: 'all', page: 1 };
        const STMAP = { aktif: ['aktif2fa', 'aktif', 'online'], online: ['online'], offline: ['offline'], pending: ['pending'] };
        const matchU = (u) => {
            const q = state.q.trim().toLowerCase();
            return (!q || (u.name + ' ' + u.email + ' ' + u.wa).toLowerCase().includes(q))
                && (state.status === 'all' || STMAP[state.status].includes(u.status))
                && (state.dept === 'all' || u.dept === state.dept)
                && (state.role === 'all' || grp(u) === state.role);
        };
        const counts = () => {
            const staff = USERS.filter((u) => grp(u) !== 'customer');
            const customer = COUNT.customerBase + USERS.filter((u) => grp(u) === 'customer' && u.added).length;
            const by = (r) => USERS.filter((u) => u.role === r).length;
            return {
                internal: staff.length, customer, total: staff.length + customer, newm: COUNT.newm,
                super: by('super'), admin: by('admin'), gate: USERS.filter((u) => grp(u) === 'gate').length,
                cgate: by('gate') + by('kolam'), ckasir: by('kasir'),
            };
        };
        const paginate = (list) => {
            const pages = Math.max(1, Math.ceil(list.length / 6));
            state.page = Math.min(state.page, pages);
            return { rows: list.slice((state.page - 1) * 6, state.page * 6), total: list.length, pages, f: true };
        };
        const getView = () => {
            const f = state.q.trim() || state.status !== 'all' || state.dept !== 'all';
            const c = counts();
            if (!f && (state.role === 'all' || state.role === 'customer')) {
                const total = state.role === 'all' ? c.total : c.customer;
                const start = (state.page - 1) * 6, rows = [];
                const cl = USERS.filter((u) => grp(u) === 'customer');
                for (let k = 0; k < 6; k++) rows.push(state.role === 'all' ? S(start + k) : (cl[start + k] || gen(start + k + 200)));
                return { rows, total, pages: Math.ceil(total / 20), f: false };
            }
            if (!f) return paginate(USERS.filter(matchU));
            return paginate([...USERS, ...Array.from({ length: 60 }, (_, i) => gen(i))].filter(matchU));
        };

        // ---------- Render rows ----------
        const COLS = 'grid grid-cols-[minmax(210px,2.1fr)_88px_minmax(150px,1.5fr)_minmax(100px,1fr)_minmax(130px,1.2fr)_minmax(150px,1.3fr)]';
        const statusHTML = (u) => {
            const s = ST[u.status];
            const dot = s.live
                ? `<span class="relative flex h-1.5 w-1.5 shrink-0"><span class="absolute h-full w-full animate-ping rounded-full ${s.dot} opacity-60"></span><span class="relative h-1.5 w-1.5 rounded-full ${s.dot}"></span></span>`
                : `<span class="h-1.5 w-1.5 shrink-0 rounded-full ${s.dot}"></span>`;
            return `<span class="inline-flex max-w-[92px] items-center gap-1.5 rounded-full ${s.bg} px-2.5 py-1.5 text-[9px] font-bold leading-tight text-slate-900">${dot}<span>${s.t}</span></span>`;
        };
        const actionsHTML = (u) => u.actions.map((a) => {
            let [label, cls] = ACT[a];
            let dis = '';
            if (a === 'tutup' && u.closed) { label = 'Kasir Ditutup'; cls = 'bg-slate-100 text-slate-400'; dis = 'disabled'; }
            return `<button type="button" ${dis} data-act="${a}" data-id="${u.id}" class="rounded-lg ${cls} px-2.5 py-1.5 text-center text-[9px] font-bold leading-tight transition hover:brightness-95">${label}</button>`;
        }).join('');
        const rowHTML = (u, i, anim, hi) => {
            const r = ROLE[u.role];
            const ini = u.name.split(' ').map((w) => w[0]).slice(0, 2).join('').toUpperCase();
            const bg = AV[(String(u.id).length + u.name.length) % 3];
            const style = anim ? `animation:jw-rise .5s cubic-bezier(.2,.8,.2,1) ${i * 70}ms backwards`
                : hi ? 'animation:jw-rise .5s cubic-bezier(.2,.8,.2,1),jw-flash 1s' : '';
            return `<div data-row="${u.id}" class="${COLS} items-center gap-3 border-b border-slate-100 px-5 py-5 transition-colors last:border-0 hover:bg-slate-50 ${hi ? 'bg-[#EAF6EE]' : ''}" style="${style}">
                <div class="flex min-w-0 items-center gap-3">
                    <span class="relative flex h-11 w-11 shrink-0 items-center justify-center overflow-hidden rounded-full ${bg} text-[13px] font-bold text-slate-800">${ini}${u.img ? `<img src="${IMG + u.img}" alt="" class="absolute inset-0 h-full w-full object-cover" onerror="this.remove()">` : ''}</span>
                    <div class="min-w-0">
                        <p class="flex flex-wrap items-center gap-x-2 text-[15px] font-bold leading-tight text-slate-900">${u.name}${u.vip ? '<span class="rounded-md bg-[#FBD9B0] px-1.5 py-0.5 text-[8px] font-bold text-[#6B4520]">VIP</span>' : ''}</p>
                        <p class="mt-0.5 break-all text-[10px] text-slate-600">${u.email}</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold leading-snug text-slate-800">${u.wa.replace(/^(\d+)-/, '$1-<br>')}</span>
                <div>
                    <span class="inline-flex max-w-[118px] items-start gap-1 rounded-[14px] ${r.cls} px-2.5 py-1 text-[9px] font-bold leading-tight">${r.icon ? `<span class="mt-px shrink-0">${ic(P[r.icon], 'h-3 w-3')}</span>` : ''}<span>${r.label}</span></span>
                    <p class="mt-1.5 text-[10px] leading-snug text-slate-600">${u.rsub || r.sub}</p>
                </div>
                <div data-st="${u.id}">${statusHTML(u)}</div>
                <div>
                    <p class="text-[11px] font-bold leading-snug text-slate-900">${u.cell.t}</p>
                    <p class="mt-0.5 text-[10px] leading-snug ${u.cell.sa ? 'font-bold text-[#7A5230]' : 'text-slate-600'}">${u.cell.s}</p>
                </div>
                <div data-acts="${u.id}" class="flex flex-wrap items-center justify-end gap-1.5">${actionsHTML(u)}</div>
            </div>`;
        };
        const pagerBtn = 'flex h-8 min-w-8 items-center justify-center rounded-lg px-2 text-[11px] font-bold transition';
        const renderPager = (v) => {
            const pg = state.page, n = v.pages;
            let list = [];
            if (n <= 5) list = Array.from({ length: n }, (_, i) => i + 1);
            else if (pg <= 3) list = [1, 2, 3, '…', n];
            else if (pg >= n - 2) list = [1, '…', n - 2, n - 1, n];
            else list = [1, '…', pg - 1, pg, pg + 1, '…', n];
            $('#pager').innerHTML =
                `<button type="button" data-pg="${pg - 1}" ${pg <= 1 ? 'disabled' : ''} class="${pagerBtn} bg-white text-slate-600 shadow-sm hover:bg-slate-100 disabled:opacity-40">${ic(P.left, 'h-3.5 w-3.5')}</button>` +
                list.map((x) => x === '…' ? '<span class="px-1 text-[11px] text-slate-500">...</span>'
                    : `<button type="button" data-pg="${x}" class="${pagerBtn} ${x === pg ? 'bg-[#0B3A22] text-white shadow-sm' : 'bg-white text-slate-700 shadow-sm hover:bg-slate-100'}">${x}</button>`).join('') +
                `<button type="button" data-pg="${pg + 1}" ${pg >= n ? 'disabled' : ''} class="${pagerBtn} bg-white text-slate-600 shadow-sm hover:bg-slate-100 disabled:opacity-40">${ic(P.right, 'h-3.5 w-3.5')}</button>`;
        };
        const renderRows = (anim = true, hi = null) => {
            const v = getView();
            $('#user-rows').innerHTML = v.rows.length
                ? v.rows.map((u, i) => rowHTML(u, i, anim, hi !== null && String(u.id) === String(hi))).join('')
                : '<p class="p-10 text-center text-[12px] font-medium text-slate-500">Tidak ada pengguna yang cocok dengan filter.</p>';
            $('[data-showing]').textContent = `Menampilkan ${v.rows.length} dari ${fmt(v.total)} pengguna ${v.f ? 'ditemukan' : 'terdaftar'}`;
            renderPager(v);
        };

        // ---------- Counters, duty, sparkline ----------
        const updateCounts = () => {
            const c = counts();
            $$('[data-c]').forEach((el) => tween(el, c[el.dataset.c]));
        };
        const renderDuty = () => {
            const staff = USERS.filter((u) => grp(u) === 'gate');
            const on = staff.filter((u) => u.status === 'online').length;
            tween($('[data-duty]'), on);
            $('[data-dots]').innerHTML = staff.map((u) => `<span class="h-2 w-2 rounded-full transition-colors ${u.status === 'online' ? 'bg-[#0B3A22]' : 'bg-slate-300'}"></span>`).join('');
        };
        const SP = [4, 6, 5, 8, 7, 10, 9, 12, 11, 14, 13, 17];
        const drawSpark = (animate) => {
            const path = $('[data-spark] path'), mx = Math.max(...SP), mn = Math.min(...SP);
            const pts = SP.map((v, i) => [(i / (SP.length - 1)) * 160, 28 - ((v - mn) / (mx - mn || 1)) * 24]);
            path.setAttribute('d', pts.map((p, i) => (i ? 'L' : 'M') + p[0].toFixed(1) + ' ' + p[1].toFixed(1)).join(' '));
            if (!animate || reduce) return;
            const len = path.getTotalLength();
            path.style.transition = 'none';
            path.style.strokeDasharray = len;
            path.style.strokeDashoffset = len;
            path.getBoundingClientRect();
            path.style.transition = 'stroke-dashoffset 1.4s ease';
            path.style.strokeDashoffset = 0;
        };

        // ---------- Realtime label ----------
        let lastUpdate = Date.now() - 120000;
        const touch = () => { lastUpdate = Date.now(); };
        setInterval(() => {
            const s = Math.floor((Date.now() - lastUpdate) / 1000);
            $('[data-live]').textContent = 'Pembaruan Realtime: ' + (s < 10 ? 'Baru saja' : s < 60 ? s + ' Detik Lalu' : Math.floor(s / 60) + ' Menit Lalu');
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
            b.textContent = ok;
            b.classList.toggle('hidden', hideOk);
            onOk = cb;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            const f = $('[data-m-body] input, [data-m-body] select');
            if (f) f.focus();
        };
        const closeModal = () => { modal.classList.add('hidden'); modal.classList.remove('flex'); };
        $$('[data-m-close]').forEach((b) => b.addEventListener('click', closeModal));
        modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });
        $('[data-m-ok]').addEventListener('click', () => { if (onOk && onOk() === false) return; closeModal(); });
        const val = (k) => ($(`[data-f="${k}"]`).value || '').trim();
        const list = (items) => items.map((t) => `<p class="rounded-lg bg-slate-50 p-3 text-[11px] text-slate-700">${t}</p>`).join('');

        // ---------- Aksi per baris ----------
        const refreshRow = (u) => {
            const row = $(`[data-row="${u.id}"]`);
            if (!row) return;
            const hi = renderRowOnly(u, row);
            return hi;
        };
        const renderRowOnly = (u, row) => {
            const tmp = document.createElement('div');
            tmp.innerHTML = rowHTML(u, 0, false, true);
            row.replaceWith(tmp.firstElementChild);
        };
        const act = (id, a) => {
            const u = findUser(id);
            if (!u) return;
            if (a === 'edit') {
                openModal({ title: 'Edit Akun', sub: u.name, body:
                    `<label class="${LBL}">Nama<input data-f="name" value="${u.name}" class="${INP}"></label>
                     <label class="${LBL}">Email<input data-f="email" type="email" value="${u.email}" class="${INP}"></label>
                     <label class="${LBL}">No. WhatsApp<input data-f="wa" value="${u.wa}" class="${INP}"></label>` }, () => {
                    if (!val('name') || !val('email').includes('@')) { toast('Nama dan email valid wajib diisi.'); return false; }
                    Object.assign(u, { name: val('name'), email: val('email'), wa: val('wa') || u.wa });
                    refreshRow(u); touch(); toast('Akun ' + u.name + ' diperbarui.');
                });
            } else if (a === 'shift') {
                openModal({ title: 'Ganti Shift', sub: u.name, body:
                    `<label class="${LBL}">Pilih shift<select data-f="shift" class="${INP}">${Object.values(SH).map((s) => `<option ${u.cell.s === s ? 'selected' : ''}>${s}</option>`).join('')}</select></label>` }, () => {
                    u.cell.s = val('shift'); refreshRow(u); touch(); toast('Shift ' + u.name + ' diubah.');
                });
            } else if (a === 'resetpin') {
                openModal({ title: 'Reset PIN', sub: u.name, body: '<p class="text-[12px] text-slate-700">PIN baru akan dibuat dan dikirim ke WhatsApp petugas. PIN lama langsung tidak berlaku.</p>', ok: 'Reset PIN' }, () => {
                    toast('PIN baru ' + u.name + ': ' + String(Math.floor(100000 + Math.random() * 900000))); touch();
                });
            } else if (a === 'resetpw') {
                toast('Tautan reset password dikirim ke ' + u.email); touch();
            } else if (a === 'tutup') {
                u.closed = true; refreshRow(u); touch(); toast('Kasir ' + u.name + ' ditutup, rekonsiliasi kas dimulai.');
            } else if (a === 'verif') {
                u.status = 'aktif'; u.cell = { t: '1 Tiket Hot Spring Classic', s: 'Invoice lunas • terverifikasi' }; u.actions = ['riwayat', 'detail'];
                refreshRow(u); touch(); bell();
                const b = $('[data-pending-badge]'); b.textContent = Math.max(0, Number(b.textContent) - 1); b.classList.remove('jw-pop'); void b.offsetWidth; b.classList.add('jw-pop');
                toast('Struk ' + u.name + ' terverifikasi.');
            } else if (a === 'log') {
                openModal({ title: 'Log Aktivitas', sub: u.name, hideOk: true, body: list(['07:02 • Login berhasil (2FA)', '07:15 • Mengubah tarif Cabin Suite', '08:40 • Verifikasi 3 struk transfer', '09:05 • Export laporan harian', '10:12 • Ganti shift petugas gate']) });
            } else if (a === 'riwayat') {
                openModal({ title: 'Riwayat Booking', sub: u.name, hideOk: true, body: list(['#JW-8820 • Magnolia Cabin Suite • 20 - 21 Sep', '#JW-8412 • Pinus Cabin Suite • 02 - 04 Agu', '#JW-8107 • Kolam Air Panas Classic • 12 Jul']) });
            } else if (a === 'detail') {
                openModal({ title: 'Detail Pengguna', sub: u.name, hideOk: true, body: list([`Email: ${u.email}`, `WhatsApp: ${u.wa}`, `Role: ${ROLE[u.role].label}`, `Status: ${ST[u.status].t}`, `${u.cell.t} • ${u.cell.s}`]) });
            }
        };
        $('#user-rows').addEventListener('click', (e) => {
            const b = e.target.closest('[data-act]');
            if (b && !b.disabled) act(b.dataset.id, b.dataset.act);
        });

        // ---------- Filter, search, pager ----------
        const setRoleUI = () => $$('[data-role]').forEach((b) => {
            const on = b.dataset.role === state.role;
            b.className = 'rounded-full px-4 py-2 text-[11px] font-semibold transition ' + (on ? 'bg-[#0B3A22] text-white shadow-sm' : 'bg-[#DDE6FB] text-slate-800 hover:brightness-95');
        });
        const refilter = () => { state.page = 1; renderRows(true); };
        $$('[data-role]').forEach((b) => b.addEventListener('click', () => { state.role = b.dataset.role; setRoleUI(); refilter(); }));
        let tq;
        $('[data-q]').addEventListener('input', (e) => { clearTimeout(tq); tq = setTimeout(() => { state.q = e.target.value; refilter(); }, 150); });
        $('[data-status]').addEventListener('change', (e) => { state.status = e.target.value; refilter(); });
        $('[data-dept]').addEventListener('change', (e) => { state.dept = e.target.value; refilter(); });
        $('[data-reset]').addEventListener('click', () => {
            Object.assign(state, { role: 'all', q: '', status: 'all', dept: 'all', page: 1 });
            $('[data-q]').value = ''; $('[data-status]').value = 'all'; $('[data-dept]').value = 'all';
            setRoleUI(); renderRows(true); toast('Filter direset.');
        });
        $('#pager').addEventListener('click', (e) => {
            const b = e.target.closest('[data-pg]');
            if (!b || b.disabled) return;
            state.page = Number(b.dataset.pg);
            renderRows(true);
        });

        // ---------- Tambah akun ----------
        const DEF = {
            customer: { role: 'customer', dept: 'Customer', cell: { t: 'Member Reguler', s: 'Terdaftar baru' }, actions: ['riwayat', 'detail'], added: true },
            gate:     { role: 'gate', dept: 'Gate & Turnstile', cell: { t: 'Pos Gate Utama', s: SH.pagi }, actions: ['edit', 'shift', 'resetpin'] },
            kasir:    { role: 'kasir', dept: 'Loket Tiket', cell: { t: 'Loket Depan Resepsionis', s: SH.pagi }, actions: ['edit', 'tutup'] },
            admin:    { role: 'admin', dept: 'Manajemen', cell: { t: 'Kantor Operasional', s: 'Shift Manajemen Terpadu' }, actions: ['edit', 'log'] },
            super:    { role: 'super', dept: 'Manajemen', cell: { t: 'Kantor Pusat Resort', s: 'Shift Manajemen Terpadu' }, actions: ['edit', 'log'], status: 'aktif2fa' },
        };
        const addUser = (o) => {
            const u = U(o);
            USERS.unshift(u);
            Object.assign(state, { role: 'all', q: '', status: 'all', dept: 'all', page: 1 });
            $('[data-q]').value = ''; $('[data-status]').value = 'all'; $('[data-dept]').value = 'all';
            setRoleUI(); renderRows(false, u.id); updateCounts(); renderDuty(); touch();
            return u;
        };
        $('[data-add]').addEventListener('click', () => {
            openModal({ title: 'Tambah Akun Pengguna', sub: 'Akun baru langsung tampil di tabel', body:
                `<label class="${LBL}">Nama Lengkap<input data-f="name" placeholder="Nama pengguna" class="${INP}"></label>
                 <label class="${LBL}">Email<input data-f="email" type="email" placeholder="nama@email.com" class="${INP}"></label>
                 <label class="${LBL}">No. WhatsApp<input data-f="wa" placeholder="0812-0000-0000" class="${INP}"></label>
                 <label class="${LBL}">Role<select data-f="role" class="${INP}"><option value="customer">Customer</option><option value="gate">Petugas Gate</option><option value="kasir">Kasir Loket</option><option value="admin">Admin Operasional</option><option value="super">Super Admin</option></select></label>`,
                ok: 'Buat Akun' }, () => {
                if (!val('name') || !val('email').includes('@')) { toast('Nama dan email valid wajib diisi.'); return false; }
                const u = addUser({ ...DEF[val('role')], name: val('name'), email: val('email'), wa: val('wa') || '0812-0000-0000', status: DEF[val('role')].status || 'aktif' });
                if (DEF[val('role')].added) COUNT.newm++, updateCounts();
                toast('Akun ' + u.name + ' dibuat.');
            });
        });

        // ---------- Export CSV ----------
        $('[data-export]').addEventListener('click', () => {
            const rows = USERS.filter(matchU);
            const csv = ['Nama,Email,WhatsApp,Role,Status,Departemen'].concat(rows.map((u) => [u.name, u.email, u.wa, ROLE[u.role].label, ST[u.status].t, u.dept].map((x) => '"' + String(x).replace(/"/g, '""') + '"').join(','))).join('\n');
            const a = document.createElement('a');
            a.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8' }));
            a.download = 'pengguna-jiwanta.csv';
            a.click();
            setTimeout(() => URL.revokeObjectURL(a.href), 1000);
            toast(rows.length + ' pengguna diekspor ke CSV.');
        });

        // ---------- RBAC ----------
        const PS = { on: ['checkc', 'text-[#0B3A22]', 'text-slate-800'], lock: ['lock', 'text-[#C2762B]', 'text-slate-800'], off: ['xc', 'text-slate-400', 'text-slate-400 line-through'] };
        const setPerm = (li, st) => {
            li.dataset.state = st;
            const [i, c1, c2] = PS[st];
            $('[data-ico]', li).className = 'mt-px shrink-0 ' + c1;
            $('[data-ico]', li).innerHTML = ic(P[i]);
            $('[data-label]', li).className = c2;
            li.classList.remove('jw-pop'); void li.offsetWidth; li.classList.add('jw-pop');
        };
        let rbacEdit = false;
        const rbacBtn = $('[data-rbac-btn]');
        rbacBtn.addEventListener('click', () => {
            rbacEdit = !rbacEdit;
            $('#rbac').classList.toggle('jw-edit', rbacEdit);
            $('[data-rbac-text]').textContent = rbacEdit ? 'Selesai Konfigurasi' : 'Konfigurasi Perizinan Role';
            $('[data-rbac-icon]').innerHTML = ic(rbacEdit ? P.check : P.sliders);
            rbacBtn.className = 'flex shrink-0 items-center gap-2 rounded-full px-5 py-2.5 text-[11px] font-semibold transition hover:brightness-95 ' + (rbacEdit ? 'bg-[#0B3A22] text-white' : 'bg-[#DDE6FB] text-[#0B3A22]');
            toast(rbacEdit ? 'Mode konfigurasi aktif: klik izin untuk mengubah.' : 'Perubahan izin disimpan.');
            touch();
        });
        $$('[data-perm]').forEach((li) => li.addEventListener('click', () => {
            if (!rbacEdit) return;
            if (li.hasAttribute('data-locked')) return toast('Super Admin selalu memiliki akses penuh.');
            const off = li.dataset.state !== 'off';
            setPerm(li, off ? 'off' : 'on');
            toast($('[data-label]', li).textContent + (off ? ' dinonaktifkan' : ' diaktifkan') + ' untuk role ' + $('span', li.closest('[data-rbac-card]')).textContent.trim());
        }));

        // ---------- Efek global ----------
        const io = new IntersectionObserver((es) => es.forEach((en) => {
            if (!en.isIntersecting) return;
            en.target.classList.add('in');
            setTimeout(() => (en.target.style.transitionDelay = ''), 900);
            io.unobserve(en.target);
        }), { threshold: 0.06 });
        $$('[data-reveal]').forEach((el, i) => { el.style.transitionDelay = (i % 4) * 90 + 'ms'; io.observe(el); });

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

        // ---------- Live demo ----------
        if (LIVE_DEMO) {
            const NEWS = ['Rizky Aditya', 'Salsabila Putri', 'Bayu Anggoro', 'Mega Ayu', 'Fikri Ramadhan', 'Nadia Safitri'];
            let n = 0;
            // presence petugas
            setInterval(() => {
                const staff = USERS.filter((u) => grp(u) === 'gate');
                const u = staff[Math.floor(Math.random() * staff.length)];
                u.status = u.status === 'online' ? 'offline' : 'online';
                const cell = $(`[data-st="${u.id}"]`);
                if (cell) { cell.innerHTML = statusHTML(u); cell.classList.remove('jw-pop'); void cell.offsetWidth; cell.classList.add('jw-pop'); }
                renderDuty(); touch(); bell();
                toast(u.name + (u.status === 'online' ? ' masuk shift (Online)' : ' keluar (Offline)'));
            }, 11000);
            // customer baru mendaftar
            setInterval(() => {
                const name = NEWS[n++ % NEWS.length];
                const u = U({ ...DEF.customer, name, email: name.toLowerCase().replace(/ /g, '.') + '@gmail.com', wa: '0812-' + (1000 + Math.floor(Math.random() * 9000)) + '-' + (1000 + Math.floor(Math.random() * 9000)), status: 'aktif' });
                USERS.unshift(u);
                COUNT.newm++;
                SP.shift(); SP.push(SP[SP.length - 1] + Math.round(Math.random() * 3 - 1));
                updateCounts(); drawSpark(true); touch(); bell();
                if (state.role === 'all' && state.page === 1 && !state.q && state.status === 'all' && state.dept === 'all') renderRows(false, u.id);
                toast('Customer baru mendaftar: ' + name);
            }, 17000);
        }

        // ---------- Init ----------
        setRoleUI();
        renderRows(true);
        updateCounts();
        renderDuty();
        drawSpark(true);
    });
</script>
@endpush
