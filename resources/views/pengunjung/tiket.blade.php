@extends('layouts.dashboard')

@section('title', 'Tiket Saya - Jiwanta')

@section('content')

@php
    // ---------- Helper ----------
    $ic = fn (string $d, string $c = 'h-4 w-4', string $sw = '1.8') =>
        '<svg class="'.$c.'" fill="none" stroke="currentColor" stroke-width="'.$sw.'" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="'.$d.'"/></svg>';

    $p = [
        'check'    => 'M9 12l2 2 4-4M12 21a9 9 0 100-18 9 9 0 000 18z',
        'tree'     => 'M12 3l4 6h-2.5l3.5 5h-3l3 5H7l3-5H7l3.5-5H8l4-6zM11 19h2v3h-2z',
        'copy'     => 'M9 9h10v11H9zM6 15H5a1 1 0 01-1-1V4a1 1 0 011-1h9a1 1 0 011 1v1',
        'copied'   => 'M5 13l4 4L19 7',
        'signal'   => 'M12 12h.01M8.5 8.5a5 5 0 000 7M15.5 8.5a5 5 0 010 7M5.5 5.5a9 9 0 000 13M18.5 5.5a9 9 0 010 13',
        'star'     => 'M12 3l2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9L12 3z',
        'users'    => 'M17 20v-1a4 4 0 00-4-4H7a4 4 0 00-4 4v1M10 11a3.5 3.5 0 100-7 3.5 3.5 0 000 7zM21 20v-1a4 4 0 00-3-3.9M16 4.1a3.5 3.5 0 010 6.8',
        'scan'     => 'M4 8V5a1 1 0 011-1h3M16 4h3a1 1 0 011 1v3M20 16v3a1 1 0 01-1 1h-3M8 20H5a1 1 0 01-1-1v-3M9 9h2v2H9zM13 9h2v2h-2zM9 13h2v2H9zM14 14h1v1h-1z',
        'download' => 'M12 4v11M7 11l5 5 5-5M5 20h14',
        'image'    => 'M4 5h16v14H4zM4 16l4-4 3 3 4-5 5 6M9 9.5h.01',
    ];

    // Animasi masuk (dipakai oleh script)
    $reveal   = 'opacity-0 translate-y-8 transition duration-700 ease-out';
    $revealSm = 'opacity-0 translate-y-3 transition duration-500 ease-out';

    // ---------- Data tiket (ganti dengan data dari controller) ----------
    $ticket = [
        'code'     => 'JW-20260920-001',
        'name'     => 'Ahmad Fadillah',
        'phone'    => '0812-3456-7890',
        'date'     => '20 Sep 2026',
        'open_at'  => '2026-09-20T08:00:00+07:00',
        'close_at' => '2026-09-20T18:00:00+07:00',
        'hours'    => '08.00 - 18.00 WIB',
        'guests'   => 2,
        'guest_type' => 'Dewasa Reguler',
        'type'     => 'Tiket Premier',
        'includes' => 'Termasuk: Semua Fasilitas Area Classic',
        'total'    => 150000,
        'method'   => 'Lunas via Transfer BCA',
    ];

    // ---------- QR (placeholder visual, deterministik dari kode booking) ----------
    // Produksi: ganti dengan QR asli, mis. simplesoftwareio/simple-qrcode
    $n = 15;
    mt_srand(crc32($ticket['code']));
    $finders = [[0, 0], [$n - 5, 0], [0, $n - 5]];
    $cells = '';
    for ($y = 0; $y < $n; $y++) {
        for ($x = 0; $x < $n; $x++) {
            $on = null;
            foreach ($finders as [$fx, $fy]) {
                if ($x >= $fx && $x < $fx + 5 && $y >= $fy && $y < $fy + 5) {
                    $lx = $x - $fx; $ly = $y - $fy;
                    $on = ($lx === 0 || $lx === 4 || $ly === 0 || $ly === 4) || ($lx === 2 && $ly === 2);
                } elseif ($x >= $fx - 1 && $x <= $fx + 5 && $y >= $fy - 1 && $y <= $fy + 5) {
                    $on = $on ?? false; // area kosong di sekeliling finder
                }
            }
            if ($on === null) {
                $center = $x >= 6 && $x <= 8 && $y >= 6 && $y <= 8;
                $on = $center ? false : mt_rand(0, 99) < 46;
            }
            if ($on) $cells .= '<rect x="'.$x.'" y="'.$y.'" width="1" height="1"/>';
        }
    }

    // ---------- Barcode (kelas lebar ditulis utuh agar terbaca Tailwind) ----------
    mt_srand(crc32($ticket['code'].'bar'));
    $barW = ['w-[1px]', 'w-[2px]', 'w-[3px]', 'w-[4px]'];
    $bars = [];
    for ($i = 0; $i < 46; $i++) $bars[] = $barW[mt_rand(0, 3)];

    $shadow = 'shadow-[0_4px_18px_rgba(15,69,39,0.08)]';
@endphp

<div class="min-h-screen w-full bg-slate-200">
    <div class="relative mx-auto min-h-screen w-full max-w-[390px] overflow-x-clip bg-[#F9F8FF] pb-32 shadow-2xl">

        {{-- ========== HEADER (menempel saat scroll) ========== --}}
        <header id="top-header" class="sticky top-0 z-40 flex items-center justify-between bg-[#F9F8FF]/85 px-5 py-3 backdrop-blur transition-shadow duration-300">
            <span class="text-[19px] font-bold tracking-tight text-[#0B3A22]">Jiwanta</span>
            <div class="flex items-center gap-3">
                <span class="text-[13px] font-medium text-slate-600">Tiket Saya</span>
                <img src="{{ asset('images/profil.jpg') }}" alt="Profil" class="h-9 w-9 rounded-full bg-[#C9B99A] object-cover">
            </div>
        </header>

        <main class="px-4 pt-2">

            {{-- ========== STATUS RESERVASI ========== --}}
            <section data-reveal class="{{ $reveal }} flex items-center justify-between rounded-2xl bg-[#0B3A22] px-4 py-3.5 text-white">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/15 text-[#B5F0BE]">{!! $ic($p['check'], 'h-6 w-6', '2') !!}</span>
                    <div>
                        <p class="text-[9px] font-semibold uppercase tracking-wider text-white/70">Status Reservasi</p>
                        <p class="text-[17px] font-bold leading-tight">LUNAS &amp; DIKONFIRMASI</p>
                    </div>
                </div>
                <span id="gate-pill" class="ml-2 shrink-0 whitespace-nowrap rounded-full bg-white/15 px-2.5 py-1.5 text-[10px] font-semibold text-white transition-colors duration-500">Gate Siap Masuk</span>
            </section>

            {{-- ========== KARTU TIKET ========== --}}
            <section data-reveal class="{{ $reveal }} delay-200 relative mt-4 overflow-hidden rounded-2xl bg-white shadow-[0_8px_28px_rgba(15,69,39,0.14)]">

                {{-- Header kartu --}}
                <div class="flex items-center justify-between bg-[#1B4A32] px-4 py-4 text-white">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/15">{!! $ic($p['tree'], 'h-5 w-5', '1.6') !!}</span>
                        <div>
                            <h2 class="text-[17px] font-bold leading-tight">Jiwanta Ciwidey</h2>
                            <p class="mt-0.5 text-[10px] leading-tight text-white/75">Eco Luxury Hot Springs &amp;<br>Forest</p>
                        </div>
                    </div>
                    <span class="rounded-full bg-[#FDD9B0] px-3.5 py-2 text-center text-[9px] font-extrabold leading-tight tracking-wide text-[#8B5E34]">PREMIER<br>PASS</span>
                </div>

                {{-- Kode booking --}}
                <div class="relative overflow-hidden bg-[#EEF0FB] px-4 py-3.5">
                    <div class="pointer-events-none absolute -right-4 bottom-0 h-10 w-40 bg-[#E3E6F7] [clip-path:polygon(0_0,100%_0,60%_100%)]"></div>
                    <div class="relative flex items-center justify-between">
                        <div>
                            <p class="text-[9px] font-bold uppercase tracking-wider text-slate-700">Kode Booking Tiket</p>
                            <p id="booking-code" class="mt-0.5 text-[20px] font-bold tracking-[0.12em] text-[#0B3A22]">{{ $ticket['code'] }}</p>
                        </div>
                        <button type="button" id="copy-btn" class="inline-flex items-center gap-1.5 rounded-lg bg-white px-3 py-1.5 text-[11px] font-semibold text-slate-700 shadow-sm transition active:scale-95">
                            <span id="copy-icon" class="text-slate-700">{!! $ic($p['copy'], 'h-3.5 w-3.5', '2') !!}</span>
                            <span id="copy-label">Salin</span>
                        </button>
                    </div>
                </div>

                {{-- QR Code --}}
                <div class="flex flex-col items-center px-4 pt-5">
                    <div data-tilt class="rounded-[22px] bg-[#E6E9FB] p-3 transition-transform duration-150 ease-out will-change-transform">
                        <div class="relative flex h-[160px] w-[160px] items-center justify-center overflow-hidden rounded-xl bg-white p-3">
                            <svg viewBox="0 0 {{ $n }} {{ $n }}" class="h-full w-full fill-[#0B3A22]" shape-rendering="crispEdges" role="img" aria-label="QR Code tiket {{ $ticket['code'] }}">{!! $cells !!}</svg>

                            {{-- Logo tengah --}}
                            <span class="absolute left-1/2 top-1/2 flex h-8 w-8 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full bg-[#0B3A22] text-white ring-4 ring-white">
                                {!! $ic($p['tree'], 'h-4 w-4', '1.8') !!}
                            </span>

                            {{-- Garis pindai (real-time) --}}
                            <span class="scan-line pointer-events-none absolute inset-x-0 top-0 h-8 bg-gradient-to-b from-transparent via-emerald-400/25 to-transparent"></span>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center gap-2 text-center text-[11px] font-semibold leading-snug text-slate-800">
                        <span class="text-[#0B3A22]">{!! $ic($p['signal'], 'h-3.5 w-3.5', '2') !!}</span>
                        <span>Siap di-scan pada turnstile gate gerbang<br>depan</span>
                    </div>
                    <p class="mt-1 text-center text-[10.5px] text-slate-600">Kecerahan layar ponsel Anda disarankan maksimal</p>
                </div>

                {{-- Garis sobekan tiket --}}
                <div class="relative mt-5">
                    <span class="absolute -left-3 top-1/2 h-6 w-6 -translate-y-1/2 rounded-full bg-[#F9F8FF]"></span>
                    <span class="absolute -right-3 top-1/2 h-6 w-6 -translate-y-1/2 rounded-full bg-[#F9F8FF]"></span>
                    <div class="mx-6 border-t-2 border-dashed border-slate-200"></div>
                </div>

                {{-- Detail pengunjung --}}
                <div class="px-4 pb-5 pt-5">
                    <div class="grid grid-cols-2 gap-x-4 gap-y-5">
                        <div>
                            <p class="text-[9px] font-semibold text-slate-600">Nama Pengunjung</p>
                            <p class="mt-1 text-[17px] font-bold leading-tight text-[#0B3A22]">{{ $ticket['name'] }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] font-semibold text-slate-600">Nomor WhatsApp</p>
                            <p class="mt-1 text-[17px] font-bold leading-tight text-[#0B3A22]">{{ $ticket['phone'] }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] font-semibold text-slate-600">Tanggal Kunjungan</p>
                            <p class="mt-1 text-[17px] font-bold leading-tight text-[#0B3A22]">{{ $ticket['date'] }}</p>
                            <p class="mt-1 text-[9.5px] font-medium text-slate-600">{{ $ticket['hours'] }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] font-semibold text-slate-600">Jumlah Pengunjung</p>
                            <p class="mt-1 flex items-center gap-1.5 text-[17px] font-bold leading-tight text-[#0B3A22]">
                                {!! $ic($p['users'], 'h-4 w-4', '2') !!} {{ $ticket['guests'] }} Orang
                            </p>
                            <p class="mt-1 text-[9.5px] font-medium text-slate-600">{{ $ticket['guest_type'] }}</p>
                        </div>
                    </div>

                    {{-- Jenis tiket --}}
                    <div class="mt-5 flex items-center gap-3 rounded-2xl bg-[#EEF0FB] px-4 py-3.5">
                        <span class="text-[#8B5E34]">{!! $ic($p['star'], 'h-6 w-6', '1.8') !!}</span>
                        <div>
                            <p class="text-[16px] font-bold text-[#0B3A22]">{{ $ticket['type'] }}</p>
                            <p class="mt-0.5 text-[10px] text-slate-600">{{ $ticket['includes'] }}</p>
                        </div>
                    </div>

                    {{-- Total pembayaran --}}
                    <div class="mt-3 flex items-center justify-between rounded-2xl bg-[#EEF0FB] px-4 py-3.5">
                        <div>
                            <p class="text-[9px] font-bold text-slate-700">Total Pembayaran</p>
                            <p class="mt-1.5 text-[11px] font-semibold text-slate-800">{{ $ticket['method'] }}</p>
                        </div>
                        <div class="text-right">
                            <p data-count="{{ $ticket['total'] }}" data-prefix="Rp " class="text-[24px] font-extrabold leading-tight text-[#0B3A22]">Rp {{ number_format($ticket['total'], 0, ',', '.') }}</p>
                            <p class="mt-0.5 text-[9.5px] text-slate-600">Verifikasi Otomatis</p>
                        </div>
                    </div>

                    {{-- Barcode --}}
                    <div class="mt-5 px-3">
                        <div class="relative h-12 overflow-hidden">
                            <div class="flex h-full items-stretch justify-between gap-[2px]">
                                @foreach ($bars as $w)
                                    <span class="{{ $w }} shrink-0 bg-[#0B3A22]"></span>
                                @endforeach
                            </div>
                            <span class="shimmer pointer-events-none absolute inset-y-0 left-0 w-1/3 bg-gradient-to-r from-transparent via-white/70 to-transparent"></span>
                        </div>
                        <p class="mt-1.5 text-center text-[8px] font-bold uppercase tracking-[0.2em] text-slate-800">Secure Token Authenticated</p>
                    </div>
                </div>
            </section>

            {{-- ========== PETUNJUK MASUK ========== --}}
            <section data-reveal class="{{ $reveal }} mt-4 flex items-start gap-3 rounded-2xl bg-white p-4 {{ $shadow }}">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#FDD9B0] text-[#8B5E34]">{!! $ic($p['scan'], 'h-5 w-5', '1.8') !!}</span>
                <div>
                    <h3 class="text-[16px] font-bold text-[#0B3A22]">Petunjuk Masuk Wisata</h3>
                    <p class="mt-1 text-[12.5px] leading-relaxed text-slate-700">
                        Tunjukkan QR Code ini kepada petugas gerbang depan Jiwanta Ciwidey. Anda akan langsung menerima gelang akses pengunjung dan voucher welcome drink.
                    </p>
                </div>
            </section>

            {{-- ========== AKSI ========== --}}
            <section data-reveal class="{{ $reveal }} mt-6 space-y-2.5">
                <a href="#" id="btn-download" class="flex w-full items-center justify-center gap-2.5 rounded-full bg-[#0B3A22] px-5 py-4 text-[15px] font-bold text-white transition hover:bg-[#0F4527] active:scale-[0.98]">
                    {!! $ic($p['download'], 'h-4 w-4', '2.2') !!} Download E-Ticket (PDF)
                </a>
                <button type="button" id="btn-save-image" class="flex w-full items-center justify-center gap-2.5 rounded-full bg-[#FDD9B0] px-5 py-3.5 text-[15px] font-bold text-[#0B3A22] transition hover:bg-[#fbcb9a] active:scale-[0.98]">
                    {!! $ic($p['image'], 'h-4 w-4', '2.2') !!} Simpan Gambar ke Galeri
                </button>
                <a href="{{ route('dashboard') }}" class="block py-3 text-center text-[13px] font-medium text-slate-600 transition hover:text-[#0B3A22]">Kembali ke Beranda</a>
            </section>
        </main>

        {{-- Toast (muncul saat kode disalin) --}}
        <div id="toast" class="pointer-events-none fixed bottom-24 left-1/2 z-50 -translate-x-1/2 translate-y-4 whitespace-nowrap rounded-full bg-[#0B3A22] px-4 py-2 text-[12px] font-semibold text-white opacity-0 shadow-lg transition duration-300">
            Kode booking tersalin
        </div>

        {{-- ========== BOTTOM NAV ========== --}}
        <nav class="fixed bottom-0 left-1/2 z-30 w-full max-w-[390px] -translate-x-1/2 bg-white pb-[env(safe-area-inset-bottom,0px)] shadow-[0_-4px_16px_rgba(11,46,34,0.06)]" aria-label="Navigasi utama">
            <div class="flex items-center justify-around px-2 pt-3 pb-3.5">
                <a href="{{ route('dashboard') }}" class="flex flex-1 flex-col items-center gap-1 text-[#0b2e22]">
                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M8 3l4.2 6.2h-2l3.3 4.6h-2.3L15 18.5H1l3.8-4.7H2.6L6 9.2H4z"/>
                        <path d="M17 8l3.6 5.2h-1.8L22 17.5h-4.5v3H16v-3h-1.4z" opacity=".85"/>
                        <path d="M7 18.5h2V21H7z"/>
                    </svg>
                    <span class="text-[10.5px] font-bold">Beranda</span>
                </a>
                <a href="{{ route('reservasi') }}" class="flex flex-1 flex-col items-center gap-1 text-[#0b2e22]">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 11l9-7 9 7v9a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-9z"/>
                    </svg>
                    <span class="text-[10.5px] font-bold">Reservasi</span>
                </a>
                <a href="{{ route('transaksi') }}" class="flex flex-1 flex-col items-center gap-1 text-[#0b2e22]">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 3h12v18l-3-2-3 2-3-2-3 2V3zM9 8h6M9 12h6"/>
                    </svg>
                    <span class="text-[10.5px] font-bold">Transaksi</span>
                </a>
                <a href="{{ route('tiket') }}" aria-current="page" class="flex flex-1 flex-col items-center gap-1 text-[#0b2e22]">
                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M4 6a2 2 0 012-2h12a2 2 0 012 2v3a2.5 2.5 0 000 5v3a2 2 0 01-2 2H6a2 2 0 01-2-2v-3a2.5 2.5 0 000-5V6zm9 1v2h2V7h-2zm0 4v2h2v-2h-2zm0 4v2h2v-2h-2z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-[10.5px] font-extrabold">Tiket Saya</span>
                </a>
            </div>
        </nav>
    </div>
</div>

@endsection

@push('scripts')
<style>
    @keyframes scan-move { 0% { transform: translateY(-100%); } 100% { transform: translateY(560%); } }
    @keyframes shimmer-move { 0% { transform: translateX(-120%); } 100% { transform: translateX(420%); } }
    .scan-line { animation: scan-move 2.6s ease-in-out infinite; }
    .shimmer   { animation: shimmer-move 3.2s ease-in-out infinite; }
    @media (prefers-reduced-motion: reduce) { .scan-line, .shimmer { animation: none; } }
</style>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const hidden = ['opacity-0', 'translate-y-8', 'translate-y-3'];
        const $$ = (s) => document.querySelectorAll(s);

        // 1. Muncul bertahap saat masuk layar
        const io = new IntersectionObserver((entries) => {
            entries.forEach((e) => {
                if (!e.isIntersecting) return;
                e.target.classList.remove(...hidden);
                io.unobserve(e.target);
            });
        }, { threshold: 0.1 });
        $$('[data-reveal]').forEach((el) => (reduce ? el.classList.remove(...hidden) : io.observe(el)));

        // 2. Total pembayaran menghitung naik saat terlihat
        if (!reduce) {
            const countIO = new IntersectionObserver((entries) => {
                entries.forEach((e) => {
                    if (!e.isIntersecting) return;
                    const el = e.target, end = Number(el.dataset.count), pre = el.dataset.prefix || '', t0 = performance.now();
                    const tick = (now) => {
                        const p = Math.min((now - t0) / 1200, 1);
                        el.textContent = pre + Math.round(end * (1 - Math.pow(1 - p, 3))).toLocaleString('id-ID');
                        if (p < 1) requestAnimationFrame(tick);
                    };
                    requestAnimationFrame(tick);
                    countIO.unobserve(el);
                });
            }, { threshold: 0.6 });
            $$('[data-count]').forEach((el) => countIO.observe(el));
        }

        // 3. Tilt QR mengikuti kursor / sentuhan
        if (!reduce) {
            $$('[data-tilt]').forEach((card) => {
                card.addEventListener('pointermove', (e) => {
                    const r = card.getBoundingClientRect();
                    const x = (e.clientX - r.left) / r.width - 0.5;
                    const y = (e.clientY - r.top) / r.height - 0.5;
                    card.style.transform = `perspective(600px) rotateX(${-y * 12}deg) rotateY(${x * 12}deg) scale(1.03)`;
                });
                ['pointerleave', 'pointerup', 'pointercancel'].forEach((ev) =>
                    card.addEventListener(ev, () => (card.style.transform = '')));
            });
        }

        // 4. Bayangan header saat scroll
        const header = document.getElementById('top-header');
        window.addEventListener('scroll', () => {
            header.classList.toggle('shadow-[0_4px_16px_rgba(15,69,39,0.08)]', window.scrollY > 10);
        }, { passive: true });

        // 5. Status gate real-time (dihitung ulang tiap detik)
        const pill = document.getElementById('gate-pill');
        const openAt  = new Date('{{ $ticket['open_at'] }}').getTime();
        const closeAt = new Date('{{ $ticket['close_at'] }}').getTime();
        const FORCE_ACTIVE = true; // <- set false di produksi; true = selalu tampil "Gate Siap Masuk" untuk demo
        const pad = (n) => String(n).padStart(2, '0');

        const updateGate = () => {
            const now = Date.now();
            let text = 'Gate Siap Masuk', warn = false;

            if (!FORCE_ACTIVE) {
                if (now < openAt) {
                    const diff = openAt - now, d = Math.floor(diff / 864e5);
                    if (d >= 1) text = 'H-' + d;
                    else {
                        const h = Math.floor(diff / 36e5), m = Math.floor(diff % 36e5 / 6e4), s = Math.floor(diff % 6e4 / 1e3);
                        text = 'Buka ' + pad(h) + ':' + pad(m) + ':' + pad(s);
                    }
                } else if (now > closeAt) {
                    text = 'Kedaluwarsa'; warn = true;
                }
            }
            pill.textContent = text;
            pill.classList.toggle('bg-white/15', !warn);
            pill.classList.toggle('bg-red-400/40', warn);
        };
        updateGate();
        setInterval(updateGate, 1000);

        // 6. Salin kode booking
        const copyBtn = document.getElementById('copy-btn');
        const copyLabel = document.getElementById('copy-label');
        const copyIcon = document.getElementById('copy-icon');
        const toast = document.getElementById('toast');
        const iconCopy = copyIcon.innerHTML;
        const iconDone = `{!! $ic($p['copied'], 'h-3.5 w-3.5', '2.6') !!}`;
        let timer;

        copyBtn.addEventListener('click', async () => {
            const code = document.getElementById('booking-code').textContent.trim();
            try {
                await navigator.clipboard.writeText(code);
            } catch (_) {
                const ta = Object.assign(document.createElement('textarea'), { value: code });
                document.body.appendChild(ta); ta.select(); document.execCommand('copy'); ta.remove();
            }
            if (navigator.vibrate) navigator.vibrate(30);

            copyLabel.textContent = 'Tersalin';
            copyIcon.innerHTML = iconDone;
            copyIcon.classList.add('text-[#0B3A22]');
            toast.classList.remove('opacity-0', 'translate-y-4');

            clearTimeout(timer);
            timer = setTimeout(() => {
                copyLabel.textContent = 'Salin';
                copyIcon.innerHTML = iconCopy;
                copyIcon.classList.remove('text-[#0B3A22]');
                toast.classList.add('opacity-0', 'translate-y-4');
            }, 2000);
        });

        // 7. Jaga layar tetap menyala agar QR mudah di-scan
        let wakeLock = null;
        const keepAwake = async () => {
            try { if ('wakeLock' in navigator) wakeLock = await navigator.wakeLock.request('screen'); } catch (_) {}
        };
        keepAwake();
        document.addEventListener('visibilitychange', () => { if (document.visibilityState === 'visible') keepAwake(); });

        // 8. Tombol unduh (sambungkan ke route Anda)
        // document.getElementById('btn-download').href = "{{ url('/tiket-saya/'.$ticket['code'].'/pdf') }}";
        // document.getElementById('btn-save-image').addEventListener('click', () => { /* html-to-image / endpoint gambar */ });

        // 9. Scroll halus
        document.documentElement.style.scrollBehavior = reduce ? 'auto' : 'smooth';
    });
</script>
@endpush
