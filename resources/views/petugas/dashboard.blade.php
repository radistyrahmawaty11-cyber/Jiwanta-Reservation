@extends('layouts.petugas')

@section('title', 'Gate Portal - Jiwanta')

@section('page-content')

<style>
    @keyframes jw-rise{from{opacity:0;transform:translateY(12px)}to{opacity:1;transform:none}}
    @keyframes jw-wiggle{0%,100%{transform:rotate(0)}20%{transform:rotate(-14deg)}40%{transform:rotate(12deg)}60%{transform:rotate(-8deg)}80%{transform:rotate(5deg)}}
    @keyframes jw-shrink{from{width:100%}to{width:0}}
    @keyframes jw-flash{0%{box-shadow:0 0 0 0 rgba(11,58,34,.35)}100%{box-shadow:0 0 0 14px rgba(11,58,34,0)}}
    @keyframes jw-pop{0%{transform:scale(.94);opacity:.4}70%{transform:scale(1.02)}100%{transform:scale(1);opacity:1}}
    @keyframes jw-scan{0%{top:6%}100%{top:94%}}
    .jw-rise{animation:jw-rise .6s cubic-bezier(.2,.8,.2,1) backwards}
    .jw-wiggle{animation:jw-wiggle .7s}
    .jw-flash{animation:jw-flash .9s}
    .jw-pop{animation:jw-pop .35s}
    .jw-scanline{animation:jw-scan 2.2s ease-in-out infinite alternate}
    @media (prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}}
</style>

@php
    $ic = fn (string $d, string $c = 'h-4 w-4') =>
        '<svg class="'.$c.'" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="'.$d.'"/></svg>';

    $p = [
        'grid'    => 'M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z',
        'scan'    => 'M4 8V5a1 1 0 011-1h3M16 4h3a1 1 0 011 1v3M20 16v3a1 1 0 01-1 1h-3M8 20H5a1 1 0 01-1-1v-3M4 12h16',
        'receipt' => 'M6 3h12v18l-3-2-3 2-3-2-3 2V3zM9 8h6M9 12h6',
        'bell'    => 'M6 8a6 6 0 0112 0c0 7 3 8 3 8H3s3-1 3-8M10 20a2 2 0 004 0',
        'logout'  => 'M15 4h4a1 1 0 011 1v14a1 1 0 01-1 1h-4M10 16l-4-4 4-4M6 12h10',
        'tree'    => 'M12 3l4 6h-2.5l3.5 5h-3l3 5H7l3-5H7l3.5-5H8l4-6z',
        'users'   => 'M17 20v-1a4 4 0 00-4-4H7a4 4 0 00-4 4v1M10 11a4 4 0 100-8 4 4 0 000 8zM21 20v-1a4 4 0 00-3-3.9M16 3.1a4 4 0 010 7.8',
        'checkc'  => 'M9 12l2 2 4-4M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'camera'  => 'M4 8h3l2-3h6l2 3h3v11H4zM12 17a3.5 3.5 0 100-7 3.5 3.5 0 000 7z',
        'volume'  => 'M11 5L6 9H3v6h3l5 4V5zM15.5 8.5a5 5 0 010 7',
        'search'  => 'M11 18a7 7 0 100-14 7 7 0 000 14zM21 21l-4.5-4.5',
        'x'       => 'M6 6l12 12M18 6L6 18',
        'cloud'   => 'M7 18a4 4 0 010-8 5 5 0 019.6-1A4.5 4.5 0 0117 18H7z',
    ];

    $mint = 'bg-[#CDEFD5]'; $peach = 'bg-[#FBD9B0]'; $lav = 'bg-[#DDE6FB]'; $soft = 'bg-[#EEF1FD]';
    $card = 'rounded-2xl bg-white shadow-[0_2px_14px_rgba(15,69,39,0.05)]';
    $cap  = 'text-[10px] font-bold uppercase leading-snug tracking-wide text-slate-600';
@endphp


            {{-- Judul --}}
            <section class="jw-rise flex flex-wrap items-end justify-between gap-4">
                <div>
                    <h1 class="text-[26px] font-bold leading-tight">Dashboard Petugas Gate</h1>
                    <p class="mt-1 max-w-xl text-[13px] leading-relaxed text-slate-600">Scan QR e-ticket pengunjung dan verifikasi bukti pembayaran transfer.</p>
                </div>
                <span class="flex items-center gap-2 rounded-full {{ $mint }} px-4 py-2 text-[11px] font-semibold">
                    {!! $ic($p['cloud'], 'h-4 w-4') !!} Realtime • Sinkron <span data-sync>2</span> dtk lalu
                </span>
            </section>

            {{-- Ringkasan --}}
            <section class="jw-rise grid gap-4 sm:grid-cols-3" style="animation-delay:.06s">
                <div class="{{ $card }} p-5">
                    <div class="flex items-start justify-between"><p class="{{ $cap }}">Tamu Masuk Hari Ini</p><span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $soft }}">{!! $ic($p['users'], 'h-5 w-5') !!}</span></div>
                    <p class="mt-1 text-[32px] font-bold leading-none"><span data-s-gate>0</span> <span class="text-[14px] font-normal text-slate-600">/ 250 pax</span></p>
                    <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-[#E3E6F3]"><div data-s-bar style="width:0%" class="h-full rounded-full bg-[#0B3A22] transition-[width] duration-1000 ease-out"></div></div>
                </div>
                <div class="{{ $card }} p-5">
                    <div class="flex items-start justify-between"><p class="{{ $cap }}">Menunggu Verifikasi</p><span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $peach }} text-[#8B5E34]">{!! $ic($p['receipt'], 'h-5 w-5') !!}</span></div>
                    <p class="mt-1 text-[32px] font-bold leading-none text-[#8B5E34]"><span data-s-pending>0</span> <span class="text-[14px] font-normal text-slate-600">pembayaran</span></p>
                    <p class="mt-3 text-[11px] text-slate-600">Periksa bukti transfer pengunjung</p>
                </div>
                <div class="{{ $card }} p-5">
                    <div class="flex items-start justify-between"><p class="{{ $cap }}">Pembayaran Terverifikasi</p><span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $mint }}">{!! $ic($p['checkc'], 'h-5 w-5') !!}</span></div>
                    <p class="mt-1 text-[32px] font-bold leading-none"><span data-s-ok>0</span> <span class="text-[14px] font-normal text-slate-600">hari ini</span></p>
                    <p class="mt-3 text-[11px] text-slate-600">E-ticket otomatis diterbitkan</p>
                </div>
            </section>

            {{-- Konten utama --}}
            <div class="grid items-start gap-5 xl:grid-cols-[minmax(0,1.15fr)_minmax(0,1fr)]">

                {{-- ========== KIRI: SCAN ========== --}}
                <div class="space-y-5">

                    <section class="jw-rise {{ $card }} p-5 lg:p-6" style="animation-delay:.1s">
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#0B3A22] text-white">{!! $ic($p['scan'], 'h-5 w-5') !!}</span>
                                <div>
                                    <h2 class="text-[18px] font-bold leading-tight">Scan QR E-Ticket</h2>
                                    <p class="text-[11px] text-slate-600">Arahkan kamera ke QR tiket pengunjung</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" id="beeper" class="flex items-center gap-1.5 rounded-full bg-[#E4E8FB] px-3.5 py-1.5 text-[11px] font-semibold transition hover:bg-[#d6dcf7] active:scale-95">{!! $ic($p['volume'], 'h-3.5 w-3.5') !!} Beep: <span data-beeper-state>On</span></button>
                                <span id="cam-pill" class="rounded-full {{ $lav }} px-3 py-1.5 text-[10px] font-bold">Kamera Mati</span>
                            </div>
                        </div>

                        <div class="relative mt-5 h-[280px] overflow-hidden rounded-2xl bg-gradient-to-br from-[#0f3a24] via-[#1c4a30] to-[#0a2a1a]">
                            <video id="cam" playsinline muted class="absolute inset-0 hidden h-full w-full object-cover"></video>

                            <div id="cam-off" class="absolute inset-0 flex flex-col items-center justify-center gap-3 px-6 text-center text-white">
                                <span class="flex h-14 w-14 items-center justify-center rounded-full bg-white/10">{!! $ic($p['camera'], 'h-7 w-7') !!}</span>
                                <p class="text-[13px] font-semibold">Kamera belum aktif</p>
                                <button type="button" id="cam-on" class="rounded-full bg-white px-5 py-2.5 text-[12px] font-bold text-[#0B3A22] transition hover:bg-[#CDEFD5] active:scale-95">Aktifkan Kamera</button>
                            </div>

                            <div id="frame" class="pointer-events-none absolute left-1/2 top-1/2 hidden h-[62%] w-[62%] -translate-x-1/2 -translate-y-1/2">
                                <span class="absolute left-0 top-0 h-6 w-6 rounded-tl-lg border-l-2 border-t-2 border-white/90"></span>
                                <span class="absolute right-0 top-0 h-6 w-6 rounded-tr-lg border-r-2 border-t-2 border-white/90"></span>
                                <span class="absolute bottom-0 left-0 h-6 w-6 rounded-bl-lg border-b-2 border-l-2 border-white/90"></span>
                                <span class="absolute bottom-0 right-0 h-6 w-6 rounded-br-lg border-b-2 border-r-2 border-white/90"></span>
                                <span class="jw-scanline absolute left-[-10%] right-[-10%] h-0.5 rounded-full bg-white shadow-[0_0_14px_4px_rgba(255,255,255,0.75)]"></span>
                            </div>

                            <button type="button" id="cam-stop" class="absolute right-3 top-3 hidden rounded-full bg-black/45 px-3 py-1.5 text-[10px] font-bold text-white transition hover:bg-black/60">Matikan Kamera</button>
                            <p id="cam-msg" class="absolute bottom-3 left-3 right-3 hidden rounded-lg bg-black/55 px-3 py-2 text-[11px] leading-snug text-white"></p>
                        </div>

                        <div class="mt-4 rounded-2xl {{ $soft }} p-4">
                            <p class="text-[10px] font-bold uppercase tracking-wide">Atau masukkan kode booking</p>
                            <div class="mt-2 flex flex-wrap items-center gap-2">
                                <label data-input-wrap class="flex min-w-[200px] flex-1 items-center gap-2 rounded-xl bg-white px-3 py-2.5 ring-2 ring-transparent transition focus-within:ring-[#0B3A22]/30">
                                    {!! $ic($p['search'], 'h-4 w-4 shrink-0 text-slate-500') !!}
                                    <input id="code-input" type="text" autocomplete="off" placeholder="JW-20260920-001" class="w-full min-w-0 bg-transparent text-[12px] font-semibold uppercase outline-none placeholder:normal-case placeholder:text-slate-400">
                                </label>
                                <button type="button" id="validate" class="flex items-center gap-2 rounded-xl bg-[#0B3A22] px-5 py-3 text-[12px] font-bold text-white transition hover:bg-[#124c2f] active:scale-95">{!! $ic($p['checkc'], 'h-4 w-4') !!} Validasi</button>
                            </div>
                            <p class="mt-2 flex flex-wrap items-center justify-between gap-2 text-[10px] text-slate-600">
                                <span>Tekan <b>F2</b> untuk fokus • <b>Enter</b> untuk validasi</span>
                                <button type="button" id="simulate" class="font-semibold text-[#8B5E34] underline-offset-2 transition hover:underline">Simulasi scan (demo)</button>
                            </p>
                        </div>
                    </section>

                    {{-- Hasil scan --}}
                    <div id="result" aria-live="polite"></div>

                    {{-- Riwayat singkat --}}
                    <section class="jw-rise {{ $card }} p-5 lg:p-6" style="animation-delay:.14s">
                        <div class="flex items-center justify-between">
                            <h2 class="text-[16px] font-bold">Scan Terakhir</h2>
                            <span class="text-[10px] text-slate-600">5 catatan terbaru</span>
                        </div>
                        <ul id="hist" class="mt-3"></ul>
                    </section>
                </div>

                {{-- ========== KANAN: VERIFIKASI ========== --}}
                <section id="verifikasi" class="jw-rise {{ $card }} p-5 lg:p-6" style="animation-delay:.12s">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="flex h-11 w-11 items-center justify-center rounded-xl {{ $peach }} text-[#8B5E34]">{!! $ic($p['receipt'], 'h-5 w-5') !!}</span>
                            <div>
                                <h2 class="text-[18px] font-bold leading-tight">Verifikasi Pembayaran</h2>
                                <p class="text-[11px] text-slate-600">Bukti transfer manual dari pengunjung</p>
                            </div>
                        </div>
                        <span class="rounded-full {{ $peach }} px-3 py-1.5 text-[10px] font-bold text-[#6B4520]"><span id="pay-count">0</span> Menunggu</span>
                    </div>

                    <div id="pay-list" class="mt-5 space-y-3"></div>
                    <p id="pay-empty" class="mt-5 hidden rounded-2xl {{ $soft }} px-4 py-10 text-center text-[12px] font-medium text-slate-600">Tidak ada pembayaran yang menunggu verifikasi.</p>
                </section>
            </div>
@endsection
@section('overlays')
{{-- Modal verifikasi --}}
<div id="modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/40 p-4">
    <div class="max-h-[92vh] w-full max-w-xl overflow-y-auto rounded-2xl bg-white p-6 shadow-xl">
        <div class="flex items-start justify-between">
            <div>
                <h3 class="text-[17px] font-bold">Periksa Bukti Pembayaran</h3>
                <p class="mt-0.5 text-[11px] text-slate-600">Cocokkan bukti transfer dengan tagihan sebelum memverifikasi.</p>
            </div>
            <button type="button" data-m-close class="rounded-lg p-1 text-slate-500 hover:bg-slate-100" aria-label="Tutup">{!! $ic($p['x'], 'h-5 w-5') !!}</button>
        </div>
        <div id="modal-body" class="mt-5"></div>
    </div>
</div>

<div id="toasts" class="fixed bottom-6 right-6 z-[60] space-y-2"></div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const LIVE_DEMO = true; // ganti dengan polling / Laravel Echo untuk data asli
        const $ = (s, r = document) => r.querySelector(s);
        const $$ = (s, r = document) => [...r.querySelectorAll(s)];
        const fmt = (n) => Math.round(n).toLocaleString('id-ID');
        const rp = (n) => 'Rp ' + fmt(n);
        const pad = (n) => String(n).padStart(2, '0');
        const esc = (s) => String(s).replace(/[&<>"]/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));
        const clockStr = (d = new Date()) => d.toLocaleTimeString('id-ID', { hour12: false, timeZone: 'Asia/Jakarta' }).replace(/\./g, ':');
        const P = {
            checkc: 'M9 12l2 2 4-4M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            xc: 'M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9 9l6 6M15 9l-6 6',
            alert: 'M12 9v4M12 17h.01M10.3 4l-8 14A2 2 0 004 21h16a2 2 0 001.7-3l-8-14a2 2 0 00-3.4 0z',
            scan: 'M4 8V5a1 1 0 011-1h3M16 4h3a1 1 0 011 1v3M20 16v3a1 1 0 01-1 1h-3M8 20H5a1 1 0 01-1-1v-3M4 12h16',
            receipt: 'M6 3h12v18l-3-2-3 2-3-2-3 2V3zM9 8h6M9 12h6',
        };
        const ic = (d, c = 'h-4 w-4') => `<svg class="${c}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="${d}"/></svg>`;
        const anim = (el, c) => { if (!el) return; el.classList.remove(c); void el.offsetWidth; el.classList.add(c); };

        // ---------- Toast & beep ----------
        const toast = (msg) => {
            const t = document.createElement('div');
            t.className = 'relative flex max-w-[340px] items-center gap-2 overflow-hidden rounded-xl bg-[#0B3A22] px-4 py-3 text-[11px] font-semibold text-white shadow-lg opacity-0 translate-x-6 transition-all duration-300';
            t.innerHTML = '<span class="h-2 w-2 shrink-0 animate-pulse rounded-full bg-[#B5F0BE]"></span><span>' + msg + '</span><span class="absolute bottom-0 left-0 h-0.5 bg-[#B5F0BE]" style="animation:jw-shrink 3.2s linear forwards"></span>';
            $('#toasts').appendChild(t);
            requestAnimationFrame(() => t.classList.remove('opacity-0', 'translate-x-6'));
            setTimeout(() => { t.classList.add('opacity-0', 'translate-x-6'); setTimeout(() => t.remove(), 300); }, 3200);
        };
        let beeper = true, audio = null;
        const beep = (ok = true) => {
            if (!beeper) return;
            try {
                audio = audio || new (window.AudioContext || window.webkitAudioContext)();
                if (audio.state === 'suspended') audio.resume();
                const o = audio.createOscillator(), g = audio.createGain();
                o.type = 'sine'; o.frequency.value = ok ? 880 : 220; g.gain.value = 0.08;
                o.connect(g); g.connect(audio.destination);
                o.start(); o.stop(audio.currentTime + (ok ? 0.12 : 0.35));
            } catch (e) {}
        };
        $('#beeper').addEventListener('click', () => { beeper = !beeper; $('[data-beeper-state]').textContent = beeper ? 'On' : 'Off'; if (beeper) beep(true); });

        // ---------- Data demo (ganti dengan API) ----------
        const PRICE = { classic: 45000, premier: 75000 };
        const PKG = { classic: 'Tiket Renang Classic', premier: 'Tiket Renang Premier' };
        const REK = { BCA: '8405123499', Mandiri: '1310098765432' };
        const NOW = Date.now();
        const B = {};
        let seq = 8;
        const mk = (n, name, pax, pkg, status, extra = {}) => {
            const b = { code: 'JW-20260920-' + String(n).padStart(3, '0'), name, pax, pkg, total: PRICE[pkg] * pax, status, ...extra };
            B[b.code] = b; return b;
        };
        mk(1, 'Ahmad Fadillah', 2, 'premier', 'pending', { bank: 'BCA', file: 'bukti_transfer_bca_ahmad.jpg', paid: 150000, uploaded: NOW - 4 * 60e3 });
        mk(2, 'Bima Sakti', 2, 'premier', 'used');
        mk(3, 'Rangga Pratama', 1, 'classic', 'used');
        mk(4, 'Siti Rahmawati', 4, 'premier', 'paid');
        mk(5, 'Maya Lestari', 3, 'premier', 'pending', { bank: 'Mandiri', file: 'bukti_mandiri_maya.png', paid: 225000, uploaded: NOW - 11 * 60e3 });
        mk(6, 'Budi Santoso', 2, 'classic', 'pending', { bank: 'BCA', file: 'bukti_bca_budi.jpg', paid: 75000, uploaded: NOW - 2 * 60e3 });
        mk(7, 'Dewi Maharani', 1, 'classic', 'paid');
        mk(8, 'Aris Munandar', 2, 'classic', 'paid');
        B['JW-20260919-014'] = { code: 'JW-20260919-014', name: 'Rendi Pratama', pax: 1, pkg: 'classic', total: 45000, status: 'expired' };

        const S = { gate: 182, cap: 250, verified: 24 };
        const hist = [
            { t: '10:14:15', name: 'Bima Sakti', sub: '2 Dewasa • Premier', ok: true },
            { t: '10:09:04', name: 'Rangga Pratama', sub: '1 Dewasa • Classic', ok: true },
            { t: '10:01:22', name: 'Rendi Pratama', sub: 'Tiket kedaluwarsa (sesi kemarin)', ok: false },
        ];
        const pending = () => Object.values(B).filter((b) => b.status === 'pending').sort((a, b) => b.uploaded - a.uploaded);

        // ---------- Sinkron, jam & umur unggahan ----------
        let syncAge = 2;
        const touch = () => { syncAge = 0; $('[data-sync]').textContent = 0; };
        const ago = (t) => {
            const s = Math.max(0, Math.floor((Date.now() - t) / 1000));
            return s < 10 ? 'baru saja' : s < 60 ? s + ' detik lalu' : s < 3600 ? Math.floor(s / 60) + ' menit lalu' : Math.floor(s / 3600) + ' jam lalu';
        };
        const tickClock = () => { $('[data-clock]').textContent = clockStr(); };
        const tickAgo = () => $$('[data-ago]').forEach((el) => (el.textContent = ago(Number(el.dataset.t))));
        tickClock();
        setInterval(() => { syncAge++; $('[data-sync]').textContent = syncAge; tickClock(); tickAgo(); }, 1000);

        // ---------- Statistik ----------
        const setNum = (sel, v) => { const el = $(sel); if (el.textContent !== String(v)) { el.textContent = v; anim(el, 'jw-pop'); } };
        const renderStats = () => {
            const n = pending().length;
            setNum('[data-s-gate]', S.gate);
            setNum('[data-s-pending]', n);
            setNum('[data-s-ok]', S.verified);
            $('[data-s-bar]').style.width = Math.min(100, (S.gate / S.cap) * 100) + '%';
            $('#pay-count').textContent = n;
            const bc = $('[data-bell-count]'); bc.textContent = n; bc.classList.toggle('hidden', n === 0);
        };

        // ---------- Bukti transfer (ilustrasi SVG) ----------
        const proofSVG = (b) => {
            const c = b.bank === 'BCA' ? '#0A4AA8' : '#B8832F';
            const t = clockStr(new Date(b.uploaded)).slice(0, 5);
            const s = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 180 250" font-family="Arial,sans-serif"><rect width="180" height="250" rx="10" fill="#F4F7F5"/><rect width="180" height="38" rx="10" fill="${c}"/><rect y="20" width="180" height="18" fill="${c}"/><text x="14" y="25" font-size="13" font-weight="700" fill="#fff">${b.bank}</text><circle cx="90" cy="68" r="14" fill="#22C55E"/><path d="M83 68l5 5 9-10" stroke="#fff" stroke-width="3" fill="none" stroke-linecap="round" stroke-linejoin="round"/><text x="90" y="98" font-size="10" text-anchor="middle" fill="#334155">Transfer Berhasil</text><text x="90" y="122" font-size="17" font-weight="700" text-anchor="middle" fill="#0B3A22">Rp ${fmt(b.paid)}</text><line x1="16" x2="164" y1="136" y2="136" stroke="#D5DCD8"/><text x="16" y="154" font-size="8" fill="#64748B">Ke rekening</text><text x="16" y="165" font-size="9.5" font-weight="700" fill="#1F2937">${REK[b.bank]}</text><text x="16" y="182" font-size="8" fill="#64748B">Atas nama</text><text x="16" y="193" font-size="9.5" font-weight="700" fill="#1F2937">Jiwanta Thermall Springs</text><text x="16" y="210" font-size="8" fill="#64748B">Pengirim</text><text x="16" y="221" font-size="9.5" font-weight="700" fill="#1F2937">${esc(b.name)}</text><text x="164" y="240" font-size="8" text-anchor="end" fill="#94A3B8">20 Sep 2026 • ${t} WIB</text></svg>`;
            return 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(s);
        };

        // ---------- Daftar verifikasi ----------
        const bankChip = (bank) => `<span class="rounded-md ${bank === 'BCA' ? 'bg-[#DBE4FF] text-[#0A4AA8]' : 'bg-[#F3E6CF] text-[#8A5A1C]'} px-2 py-0.5 text-[9px] font-bold">${bank}</span>`;
        const payCard = (b) => `<article data-pay="${b.code}" class="jw-rise flex items-center gap-4 rounded-2xl bg-[#EEF1FD] p-4 transition duration-300">
            <img src="${proofSVG(b)}" alt="Bukti transfer ${esc(b.name)}" class="h-[64px] w-[52px] shrink-0 rounded-lg bg-white object-cover shadow-sm">
            <div class="min-w-0 flex-1">
                <p class="flex flex-wrap items-center gap-2"><b class="truncate text-[13px]">${esc(b.name)}</b>${bankChip(b.bank)}${b.paid !== b.total ? '<span class="rounded-md bg-[#FBD9B0] px-2 py-0.5 text-[9px] font-bold text-[#6B4520]">Nominal berbeda</span>' : ''}</p>
                <p class="mt-0.5 text-[11px] text-slate-600">${b.code} • ${b.pax} Dewasa</p>
                <p class="mt-1 text-[12px]"><b>${rp(b.total)}</b> <span class="text-slate-500">• <span data-ago data-t="${b.uploaded}">${ago(b.uploaded)}</span></span></p>
            </div>
            <button type="button" data-open="${b.code}" class="shrink-0 rounded-xl bg-[#0B3A22] px-4 py-2.5 text-[11px] font-bold text-white transition hover:bg-[#124c2f] active:scale-95">Periksa</button>
        </article>`;
        const renderPay = () => {
            const list = pending();
            $('#pay-list').innerHTML = list.map(payCard).join('');
            $('#pay-empty').classList.toggle('hidden', list.length > 0);
            renderStats();
        };
        $('#pay-list').addEventListener('click', (e) => { const b = e.target.closest('[data-open]'); if (b) openPay(b.dataset.open); });
        $('#bell').addEventListener('click', () => $('#verifikasi').scrollIntoView({ behavior: 'smooth', block: 'start' }));

        // ---------- Modal verifikasi ----------
        const modal = $('#modal');
        let payCode = null;
        const closeModal = () => { modal.classList.add('hidden'); modal.classList.remove('flex'); payCode = null; };
        $$('[data-m-close]').forEach((x) => x.addEventListener('click', closeModal));
        modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });

        function openPay(code) {
            const b = B[code];
            if (!b || b.status !== 'pending') return;
            payCode = code;
            const diff = b.paid !== b.total;
            $('#modal-body').innerHTML = `
                <div class="grid gap-5 sm:grid-cols-[170px_1fr]">
                    <img src="${proofSVG(b)}" alt="Bukti transfer" class="w-full max-w-[170px] rounded-xl bg-[#F4F7F5] object-cover shadow-sm sm:max-w-none">
                    <dl class="space-y-2.5 text-[12px]">
                        <div class="flex justify-between gap-3"><dt class="text-slate-600">Pemesan</dt><dd class="text-right font-bold">${esc(b.name)}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-slate-600">Kode booking</dt><dd class="text-right font-bold">${b.code}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-slate-600">Paket</dt><dd class="text-right font-bold">${PKG[b.pkg]} • ${b.pax} pax</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-slate-600">Tagihan</dt><dd class="text-right font-bold">${rp(b.total)}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-slate-600">Nominal di bukti</dt><dd class="text-right font-bold ${diff ? 'text-red-700' : 'text-[#0B3A22]'}">${rp(b.paid)}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-slate-600">Rekening tujuan</dt><dd class="text-right font-bold">${b.bank} ${REK[b.bank]}</dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-slate-600">Diunggah</dt><dd class="text-right font-bold"><span data-ago data-t="${b.uploaded}">${ago(b.uploaded)}</span></dd></div>
                        <div class="flex justify-between gap-3"><dt class="text-slate-600">File</dt><dd class="max-w-[160px] truncate text-right font-medium">${esc(b.file)}</dd></div>
                    </dl>
                </div>
                ${diff ? `<p class="mt-4 flex items-start gap-2 rounded-xl bg-[#FBD9B0] px-4 py-3 text-[11px] font-semibold leading-snug text-[#6B4520]">${ic(P.alert, 'mt-px h-4 w-4 shrink-0')} Nominal pada bukti berbeda dari tagihan. Disarankan menolak dan minta pengunjung mengunggah ulang.</p>` : ''}
                <div class="mt-4 space-y-2 rounded-2xl bg-[#EEF1FD] p-4">
                    <label class="flex cursor-pointer items-start gap-3 text-[12px] font-medium"><input type="checkbox" data-chk class="mt-0.5 h-4 w-4 accent-[#0B3A22]"> Nominal transfer sesuai tagihan (${rp(b.total)})</label>
                    <label class="flex cursor-pointer items-start gap-3 text-[12px] font-medium"><input type="checkbox" data-chk class="mt-0.5 h-4 w-4 accent-[#0B3A22]"> Rekening tujuan adalah rekening resmi Jiwanta</label>
                </div>
                <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <select id="rej-reason" class="rounded-xl bg-[#DDE6FB] px-3 py-2.5 text-[11px] font-semibold outline-none">
                            <option>Nominal tidak sesuai</option><option>Bukti tidak jelas</option><option>Rekening tujuan salah</option>
                        </select>
                        <button type="button" data-m-reject class="rounded-xl bg-[#DDE6FB] px-4 py-2.5 text-[11px] font-bold transition hover:bg-[#cfdaf7] active:scale-95">Tolak</button>
                    </div>
                    <button type="button" data-m-verify disabled class="flex items-center gap-2 rounded-xl bg-[#0B3A22] px-5 py-3 text-[12px] font-bold text-white transition hover:bg-[#124c2f] active:scale-95 disabled:cursor-not-allowed disabled:opacity-40">${ic(P.checkc, 'h-4 w-4')} Verifikasi Pembayaran</button>
                </div>`;
            modal.classList.remove('hidden'); modal.classList.add('flex');
        }
        modal.addEventListener('change', (e) => {
            if (!e.target.matches('[data-chk]')) return;
            $('[data-m-verify]').disabled = !$$('[data-chk]', modal).every((c) => c.checked);
        });
        const fadeOut = (code) => {
            const el = $(`[data-pay="${code}"]`);
            if (el) el.classList.add('opacity-0', 'scale-95');
            setTimeout(renderPay, 280);
        };
        modal.addEventListener('click', (e) => {
            if (e.target.closest('[data-m-verify]')) {
                const b = B[payCode]; if (!b) return;
                b.status = 'paid'; S.verified++;
                const code = payCode; closeModal(); fadeOut(code); touch(); beep(true);
                toast('Pembayaran ' + esc(b.name) + ' terverifikasi. E-ticket diterbitkan.');
                if (cur && cur.code === code) showValid(b);
            }
            if (e.target.closest('[data-m-reject]')) {
                const b = B[payCode]; if (!b) return;
                const why = $('#rej-reason').value;
                b.status = 'rejected'; b.why = why;
                const code = payCode; closeModal(); fadeOut(code); touch(); beep(false);
                toast('Pembayaran ' + esc(b.name) + ' ditolak: ' + why + '. Pengunjung diberi tahu.');
                if (cur && cur.code === code) showBad(b, 'Pembayaran ditolak', why + '. Pengunjung perlu mengunggah ulang bukti transfer.');
            }
        });

        // ---------- Riwayat ----------
        const renderHist = () => {
            $('#hist').innerHTML = hist.slice(0, 5).map((h, i) => `<li class="flex items-center gap-3 py-3 ${i ? 'border-t border-slate-100' : ''}">
                <span class="w-[62px] shrink-0 font-mono text-[11px] font-bold ${h.ok ? '' : 'text-red-700'}">${h.t}</span>
                <div class="min-w-0 flex-1"><p class="truncate text-[12px] font-bold">${esc(h.name)}</p><p class="truncate text-[10px] ${h.ok ? 'text-slate-600' : 'font-semibold text-red-700'}">${esc(h.sub)}</p></div>
                <span class="shrink-0 rounded-full ${h.ok ? 'bg-[#CDEFD5] text-[#0B3A22]' : 'bg-[#FAD4D4] text-red-700'} px-3 py-1 text-[10px] font-bold">${h.ok ? 'Masuk' : 'Ditolak'}</span>
            </li>`).join('');
            const first = $('#hist li'); if (first) anim(first, 'jw-pop');
        };

        // ---------- Hasil scan ----------
        let cur = null, idleTimer = null;
        const shell = 'jw-pop overflow-hidden rounded-2xl bg-white p-5 shadow-[0_2px_14px_rgba(15,69,39,0.05)] lg:p-6';
        const setResult = (html) => { $('#result').innerHTML = html; };
        const idleHTML = () => `<div class="flex items-center gap-4 rounded-2xl border-2 border-dashed border-[#D5DBF3] bg-white/60 px-6 py-6">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#EEF1FD] text-slate-500">${ic(P.scan, 'h-6 w-6')}</span>
            <div><p class="text-[14px] font-bold">Menunggu scan tiket</p><p class="mt-0.5 text-[12px] text-slate-600">Hasil validasi QR pengunjung akan muncul di sini.</p></div></div>`;
        const info = (b) => `<div class="mt-4 grid gap-3 rounded-2xl bg-[#EEF1FD] p-4 text-[11px] sm:grid-cols-3">
            <div><p class="text-slate-600">Paket</p><p class="mt-0.5 font-bold">${PKG[b.pkg]}</p></div>
            <div><p class="text-slate-600">Pengunjung</p><p class="mt-0.5 font-bold">${b.pax} Dewasa</p></div>
            <div><p class="text-slate-600">Tanggal Kunjungan</p><p class="mt-0.5 font-bold">Minggu, 20 Sep 2026</p></div></div>`;

        function showValid(b) {
            cur = { code: b.code };
            setResult(`<div class="${shell} border-t-[5px] border-[#0B3A22]">
                <div class="flex items-start gap-4">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#CDEFD5] text-[#0B3A22]">${ic(P.checkc, 'h-6 w-6')}</span>
                    <div>
                        <p class="flex flex-wrap items-center gap-2.5"><span class="text-[22px] font-bold leading-tight">${esc(b.name)}</span><span class="rounded-md bg-[#0B3A22] px-2.5 py-1 text-[10px] font-bold text-white">VALID • ${b.pax} PAX</span></p>
                        <p class="mt-1 text-[12px] text-slate-700">Booking ID: <b>${b.code}</b> • Pembayaran lunas</p>
                    </div>
                </div>
                ${info(b)}
                <div class="mt-4 flex justify-end gap-3">
                    <button type="button" data-act="clear" class="rounded-xl bg-[#DDE6FB] px-5 py-3 text-[12px] font-semibold transition hover:bg-[#cfdaf7] active:scale-95">Batal</button>
                    <button type="button" data-act="allow" data-code="${b.code}" class="flex items-center gap-2 rounded-xl bg-[#0B3A22] px-6 py-3 text-[12px] font-bold text-white transition hover:bg-[#124c2f] active:scale-95">${ic(P.checkc, 'h-4 w-4')} Izinkan Masuk</button>
                </div></div>`);
        }
        function showBad(b, title, desc, withPay) {
            cur = b ? { code: b.code } : null;
            setResult(`<div class="${shell} border-t-[5px] border-red-600">
                <div class="flex items-start gap-4">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#FAD4D4] text-red-700">${ic(P.xc, 'h-6 w-6')}</span>
                    <div>
                        <p class="flex flex-wrap items-center gap-2.5"><span class="text-[20px] font-bold leading-tight">${esc(title)}</span><span class="rounded-md bg-red-600 px-2.5 py-1 text-[10px] font-bold text-white">DITOLAK</span></p>
                        <p class="mt-1 text-[12px] leading-snug text-slate-700">${esc(desc)}</p>
                        ${b ? `<p class="mt-1 text-[11px] text-slate-600">${esc(b.name)} • ${b.code}</p>` : ''}
                    </div>
                </div>
                <div class="mt-4 flex justify-end gap-3">
                    <button type="button" data-act="clear" class="rounded-xl bg-[#DDE6FB] px-5 py-3 text-[12px] font-semibold transition hover:bg-[#cfdaf7] active:scale-95">Tutup</button>
                    ${withPay ? `<button type="button" data-act="pay" data-code="${b.code}" class="flex items-center gap-2 rounded-xl bg-[#0B3A22] px-5 py-3 text-[12px] font-bold text-white transition hover:bg-[#124c2f] active:scale-95">${ic(P.receipt, 'h-4 w-4')} Periksa Pembayaran</button>` : ''}
                </div></div>`);
        }
        const reject = (b, title, desc, withPay) => {
            showBad(b, title, desc, withPay);
            hist.unshift({ t: clockStr(), name: b ? b.name : 'Tidak dikenal', sub: title, ok: false });
            renderHist(); beep(false); touch();
        };
        const done = (b) => {
            cur = null;
            setResult(`<div class="${shell} border-t-[5px] border-[#0B3A22]"><div class="flex items-center gap-4">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-[#0B3A22] text-white">${ic(P.checkc, 'h-6 w-6')}</span>
                <div><p class="text-[18px] font-bold leading-tight">Check-in berhasil</p><p class="mt-0.5 text-[12px] text-slate-700">${esc(b.name)} • ${b.pax} pax masuk pukul ${clockStr()} WIB</p></div></div></div>`);
        };
        const reset = () => { cur = null; clearTimeout(idleTimer); setResult(idleHTML()); };

        const normalize = (raw) => {
            const s = String(raw || '').toUpperCase().trim();
            const m = s.match(/JW-\d{8}-\d{3}/);
            return m ? m[0] : s;
        };
        function handleScan(raw) {
            const code = normalize(raw);
            if (!code) return;
            clearTimeout(idleTimer);
            const b = B[code];
            if (!b) return reject(null, 'Kode tidak ditemukan', 'Pastikan QR e-ticket asli atau ketik ulang kode booking.');
            if (b.status === 'used') return reject(b, 'Tiket sudah digunakan', 'Tiket ini sudah dipakai untuk masuk' + (b.usedAt ? ' pukul ' + clockStr(b.usedAt) + ' WIB.' : '.'));
            if (b.status === 'expired') return reject(b, 'Tiket kedaluwarsa', 'Tanggal kunjungan tiket ini sudah lewat.');
            if (b.status === 'pending') return reject(b, 'Pembayaran belum terverifikasi', 'Verifikasi bukti transfer pengunjung terlebih dahulu.', true);
            if (b.status === 'rejected') return reject(b, 'Pembayaran ditolak', (b.why || 'Bukti tidak sesuai') + '. Pengunjung perlu mengunggah ulang bukti transfer.');
            showValid(b); beep(true); touch();
        }
        function allow(code) {
            const b = B[code];
            if (!b || b.status !== 'paid') return;
            b.status = 'used'; b.usedAt = new Date();
            S.gate = Math.min(S.cap, S.gate + b.pax);
            hist.unshift({ t: clockStr(), name: b.name, sub: b.pax + ' Dewasa • ' + (b.pkg === 'premier' ? 'Premier' : 'Classic'), ok: true });
            renderHist(); renderStats(); done(b); beep(true); touch();
            idleTimer = setTimeout(reset, 4000);
        }
        $('#result').addEventListener('click', (e) => {
            const a = e.target.closest('[data-act]'); if (!a) return;
            if (a.dataset.act === 'allow') allow(a.dataset.code);
            if (a.dataset.act === 'clear') reset();
            if (a.dataset.act === 'pay') openPay(a.dataset.code);
        });

        // ---------- Input manual ----------
        const input = $('#code-input');
        const validate = () => {
            if (!input.value.trim()) {
                const w = $('[data-input-wrap]'); w.classList.add('ring-red-400');
                setTimeout(() => w.classList.remove('ring-red-400'), 900); return;
            }
            handleScan(input.value); input.select();
        };
        $('#validate').addEventListener('click', validate);
        input.addEventListener('keydown', (e) => { if (e.key === 'Enter') validate(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'F2') { e.preventDefault(); input.focus(); input.select(); } });
        $('#simulate').addEventListener('click', () => {
            const all = Object.values(B), paid = all.filter((b) => b.status === 'paid'), other = all.filter((b) => b.status !== 'paid');
            const pool = (Math.random() < 0.65 && paid.length) || !other.length ? paid : other;
            if (!pool.length) return toast('Tidak ada data tiket untuk disimulasikan.');
            const b = pool[Math.floor(Math.random() * pool.length)];
            input.value = b.code; handleScan(b.code);
        });

        // ---------- Kamera & pembaca QR ----------
        const video = $('#cam');
        const detector = 'BarcodeDetector' in window ? new BarcodeDetector({ formats: ['qr_code'] }) : null;
        let stream = null, loopTimer = null, lastRaw = '', lastAt = 0;
        const camMsg = (t) => { const m = $('#cam-msg'); m.textContent = t || ''; m.classList.toggle('hidden', !t); };
        const camUI = (on) => {
            video.classList.toggle('hidden', !on);
            $('#cam-off').classList.toggle('hidden', on);
            $('#frame').classList.toggle('hidden', !on);
            $('#cam-stop').classList.toggle('hidden', !on);
            const pill = $('#cam-pill');
            pill.textContent = on ? 'Siap Scan' : 'Kamera Mati';
            pill.className = 'rounded-full px-3 py-1.5 text-[10px] font-bold ' + (on ? 'bg-[#CDEFD5] text-[#0B3A22]' : 'bg-[#DDE6FB]');
        };
        const loop = async () => {
            if (!stream || !detector) return;
            try {
                const codes = await detector.detect(video);
                if (codes.length) {
                    const raw = codes[0].rawValue, now = Date.now();
                    if (raw !== lastRaw || now - lastAt > 3000) { lastRaw = raw; lastAt = now; handleScan(raw); }
                }
            } catch (e) {}
            loopTimer = setTimeout(loop, 250);
        };
        async function startCam() {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                camUI(false); return toast('Kamera butuh koneksi HTTPS. Gunakan input kode booking.');
            }
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: { ideal: 'environment' } }, audio: false });
                video.srcObject = stream; await video.play();
                camUI(true); camMsg('');
                if (detector) loop(); else camMsg('Browser belum mendukung pembacaan QR otomatis. Gunakan input kode booking.');
            } catch (e) {
                camUI(false); toast('Kamera tidak dapat diakses. Periksa izin kamera di browser.');
            }
        }
        function stopCam() {
            clearTimeout(loopTimer);
            if (stream) stream.getTracks().forEach((t) => t.stop());
            stream = null; video.srcObject = null; camUI(false); camMsg('');
        }
        $('#cam-on').addEventListener('click', startCam);
        $('#cam-stop').addEventListener('click', stopCam);
        window.addEventListener('pagehide', stopCam);

        // ---------- Pembayaran masuk real-time (demo) ----------
        if (LIVE_DEMO) {
            const NAMES = ['Fikri Ramadhan', 'Laras Wulandari', 'Anisa Putri', 'Hendra Gunawan', 'Nadia Safitri', 'Rizky Aditya', 'Salsabila Putri', 'Bayu Anggoro', 'Yoga Prasetyo', 'Citra Kirana'];
            let ni = 0;
            setInterval(() => {
                if (pending().length >= 8) return;
                const pkg = Math.random() < 0.5 ? 'classic' : 'premier', pax = 1 + Math.floor(Math.random() * 4);
                const bank = Math.random() < 0.6 ? 'BCA' : 'Mandiri', name = NAMES[ni++ % NAMES.length];
                const b = mk(++seq, name, pax, pkg, 'pending', { bank, file: 'bukti_transfer_' + bank.toLowerCase() + '_' + name.split(' ')[0].toLowerCase() + '.jpg', uploaded: Date.now() });
                b.paid = Math.random() < 0.2 ? b.total - 15000 : b.total;
                renderPay(); touch(); beep(true);
                anim($('#bell'), 'jw-wiggle'); anim($(`[data-pay="${b.code}"]`), 'jw-flash');
                toast('Bukti transfer baru dari ' + esc(name) + ' (' + rp(b.total) + ')');
            }, 17000);
        }

        // ---------- Init ----------
        renderPay();
        renderHist();
        reset();
        requestAnimationFrame(() => requestAnimationFrame(renderStats));
    });
</script>
@endpush
