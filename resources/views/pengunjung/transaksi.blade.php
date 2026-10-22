@extends('layouts.pengunjung')

@section('title', 'Transaksi - Jiwanta')
@section('page-label', 'Transaksi')
@section('outer-class') bg-[#f1f1fa] @endsection
@section('page-class') font-['Plus_Jakarta_Sans',ui-sans-serif,system-ui,sans-serif] text-[#0b2e22] antialiased @endsection
@section('frame-class') relative mx-auto min-h-screen w-full max-w-[390px] bg-[#f8f8ff] pb-[120px] @endsection

{{-- Font desain: Plus Jakarta Sans (aman jika layout belum punya @stack('styles'), hanya tidak terpakai) --}}
@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@endpush

@section('page-content')

@php
    // Pesanan dari session (POST /reservasi/checkout). Tanpa pesanan -> data demo lama.
    $order = $order ?? null;

    $hari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    if ($order) {
        $judul = $order['judul'];
        $badge = $order['badge'];
        $tamu = $order['tamu'];
        $hargaLabel = $order['harga_label'];
        $hargaValue = $order['harga_value'];
        $subtotalLabel = $order['subtotal_label'];
        $subtotalValue = 'Rp '.number_format($order['total'], 0, ',', '.');
        $totalFormatted = number_format($order['total'], 0, ',', '.');

        $d = \Illuminate\Support\Carbon::parse($order['tanggal']);
        $tanggalLong = $hari[$d->dayOfWeek].', '.$d->day.' '.$bulan[$d->month - 1].' '.$d->year;

        $gambar = ($order['tipe'] === 'cabin')
            ? asset('images/cabin-suite.jpg')
            : asset('images/kolam jiwanta.jpg');
        $gambarAlt = $order['tipe'] === 'cabin' ? $judul : 'Kolam Jiwanta';
    } else {
        $judul = '-';
        $badge = '-';
        $tamu = '-';
        $tanggalLong = '-';
        $hargaLabel = '-';
        $hargaValue = '-';
        $subtotalLabel = '-';
        $subtotalValue = 'Rp 0';
        $totalFormatted = '0';
        $gambar = asset('images/kolam jiwanta.jpg');
        $gambarAlt = 'Jiwanta';
    }

    // Status kiriman bukti (POST /transaksi/kirim) -> tampil konsisten saat halaman dibuka ulang
    $user = $user ?? null;
    $pemesanNama = $user?->nama_pengguna ?? '-';
    $pemesanWa = $user?->nohp
        ? preg_replace('/(\d{4})(\d{4})(\d+)/', '$1-$2-$3', preg_replace('/\D/', '', $user->nohp))
        : '-';
    $pemesanEmail = $user?->email ?? '-';

    $sudahKirim = $order && ($order['status'] ?? '') === 'terkirim';
    $monT = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    $namaBukti = ($sudahKirim && !empty($order['bukti_nama'])) ? $order['bukti_nama'] : '-';
    if ($sudahKirim && !empty($order['dikirim_at'])) {
        $t = \Illuminate\Support\Carbon::parse($order['dikirim_at']);
        $metaBukti = 'Terunggah '.$t->day.' '.$monT[$t->month - 1].' '.$t->year.' '.sprintf('%02d:%02d', $t->hour, $t->minute).' WIB';
    } else {
        $metaBukti = '-';
    }

    // Pembayaran online via Midtrans (BCA VA, Mandiri Bill, QRIS).
    $transaksi = $transaksi ?? null;
    $midtrans = $midtrans ?? ['enabled' => false, 'client_key' => '', 'sandbox' => true, 'charge_url' => ''];
    $isPaid = ($transaksi?->status ?? '') === 'lunas';
    $paidVia = $transaksi?->payment_type ? strtoupper(str_replace('_', ' ', $transaksi->payment_type)) : '';

    $paymentStatus = $transaksi?->status ?? 'pending';
    $paymentFailed = $paymentStatus === 'gagal';
    $buktiAda = ($transaksi?->bukti_transfer) || !empty($order['bukti_url']);
    $waitingVerify = $order && ! $isPaid && ! $paymentFailed && ($sudahKirim || $buktiAda);

    $selectedMetode = strtolower((string) ($order['metode'] ?? $transaksi?->metode_pembayaran ?? ''));
    $selectedMetode = match (true) {
        str_contains($selectedMetode, 'bca') => 'bca',
        str_contains($selectedMetode, 'mandiri') => 'mandiri',
        str_contains($selectedMetode, 'qris') => 'qris',
        str_contains($selectedMetode, 'midtrans') => 'qris',
        default => '',
    };
@endphp

        <main class="space-y-4 px-3.5">
@if($order)

            {{-- ========== STEPPER ========== --}}
            <section class="rounded-2xl bg-white px-4 pt-3.5 pb-3 shadow-[0_4px_24px_-6px_rgba(30,41,90,0.10)]" aria-label="Langkah pemesanan">
                <div class="relative flex items-start">
                    {{-- garis penghubung --}}
                    <div class="absolute left-[16.66%] right-[16.66%] top-[15px] flex h-[2px]">
                        <div class="h-full w-1/2 bg-[#0b2e22]"></div>
                        <div class="h-full w-1/2 bg-[#d9dcf3]"></div>
                    </div>

                    {{-- Step 1 --}}
                    <div class="relative z-10 flex w-1/3 flex-col items-center text-center">
                        <div class="flex h-[32px] w-[32px] items-center justify-center rounded-full bg-[#cdeedb] text-[#0b2e22]">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="mt-1.5 text-[10.5px] font-bold leading-tight text-[#0b2e22]">1. Reservasi</p>
                        <p class="text-[9.5px] text-gray-400">Selesai</p>
                    </div>
                    {{-- Step 2 --}}
                    <div class="relative z-10 flex w-1/3 flex-col items-center text-center">
                        <div class="flex h-[32px] w-[32px] items-center justify-center rounded-full bg-[#0b2e22] text-[13px] font-bold text-white">2</div>
                        <p class="mt-1.5 text-[10.5px] font-bold leading-tight text-[#0b2e22]">2. Checkout</p>
                        <p class="text-[9.5px] font-semibold text-[#8a4b0f]">Aktif</p>
                    </div>
                    {{-- Step 3 --}}
                    <div class="relative z-10 flex w-1/3 flex-col items-center text-center">
                        <div class="flex h-[32px] w-[32px] items-center justify-center rounded-full bg-[#dfe2f8] text-[13px] font-semibold text-[#0b2e22]/70">3</div>
                        <p class="mt-1.5 text-[10.5px] font-bold leading-tight text-[#0b2e22]/80">3. E-Ticket</p>
                        <p class="text-[9.5px] text-gray-400">Selanjutnya</p>
                    </div>
                </div>
            </section>

            {{-- ========== TIMER (real-time) ========== --}}
            <section id="timerCard"
                     data-expired-at="{{ isset($expiredAt) ? $expiredAt->toIso8601String() : '' }}"
                     class="flex items-center justify-between rounded-2xl bg-[#ffd7a8] px-4 py-3.5 shadow-[0_4px_24px_-6px_rgba(30,41,90,0.10)]"
                     aria-live="off">
                <div class="flex items-center gap-3">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[#7a4a1a] text-white">
                        <svg class="h-[18px] w-[18px]" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="leading-tight">
                        <p id="countdownLabel" class="text-[9.5px] font-bold uppercase tracking-wider text-[#8a4b0f]">Sisa Waktu Pembayaran</p>
                        <p id="countdown" class="text-[21px] font-extrabold tabular-nums text-[#0b2e22]">01:59:44</p>
                    </div>
                </div>
                <span class="rounded-full bg-[#fff1dd] px-3 py-1 text-[10.5px] font-bold text-[#7a4a1a]">Batas 2 Jam</span>
            </section>

            {{-- ========== DETAIL TIKET ========== --}}
            <section class="rounded-2xl bg-white p-4 shadow-[0_4px_24px_-6px_rgba(30,41,90,0.10)]">
                <div class="flex items-center gap-3">
                    <img src="{{ $gambar }}" alt="{{ $gambarAlt }}"
                         class="h-[52px] w-[52px] shrink-0 rounded-lg bg-[#8fa89a] object-cover">
                    <div class="min-w-0">
                        <span class="inline-block rounded-md bg-[#cdeedb] px-2 py-[3px] text-[9.5px] font-bold text-[#0b4a35]">{{ $badge }}</span>
                        <h2 class="mt-0.5 text-[17px] font-extrabold leading-tight text-[#0b2e22]">{{ $judul }}</h2>
                        <p class="mt-0.5 flex items-center gap-1.5 text-[11.5px] font-medium text-gray-500">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $tamu }}
                        </p>
                    </div>
                </div>

                <div class="mt-3.5 space-y-2 rounded-xl bg-[#eef0ff] px-3 py-3">
                    <div class="flex items-center gap-2.5">
                        <svg class="h-4 w-4 shrink-0 text-[#0b2e22]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-[12.5px] font-bold text-[#0b2e22]">{{ $tanggalLong }}</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-[#0b2e22]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="text-[12px] font-medium leading-snug text-[#0b2e22]/85">Jiwanta Ciwidey Resort, Jl. Raya Ciwidey - Patengan KM 11</span>
                    </div>
                </div>

                @if (!empty($order['items']))
                    <div class="mt-3 space-y-2 rounded-xl bg-[#eef0ff] px-3 py-3">
                        @foreach ($order['items'] as $item)
                            <div class="flex items-start justify-between gap-3 text-[12px]">
                                <span class="min-w-0">
                                    <span class="block font-bold text-[#0b2e22]">{{ $item['nama'] }}</span>
                                    <span class="text-gray-500">{{ $item['keterangan'] }} × Rp {{ number_format($item['satuan'], 0, ',', '.') }}</span>
                                </span>
                                <span class="shrink-0 font-semibold tabular-nums text-[#0b2e22]">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="mt-4 space-y-1.5 text-[12px] text-[#0b2e22]/80">
                    <div class="flex justify-between">
                        <span>{{ $hargaLabel }}</span>
                        <span class="tabular-nums">{{ $hargaValue }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>{{ $subtotalLabel }}</span>
                        <span class="tabular-nums">{{ $subtotalValue }}</span>
                    </div>
                </div>

                <div class="my-3.5 h-px bg-slate-200"></div>

                <div class="flex items-end justify-between">
                    <div class="max-w-[125px]">
                        <p class="text-[12px] font-bold text-[#0b2e22]">Total Jumlah Transfer</p>
                        <p class="mt-1 text-[9.5px] leading-snug text-gray-400">Harus transfer persis hingga digit terakhir</p>
                    </div>
                    <div class="text-right leading-none text-[#0b2e22]">
                        <p class="text-[22px] font-extrabold">Rp</p>
                        <p class="mt-0.5 text-[30px] font-extrabold tabular-nums tracking-tight">{{ $totalFormatted }}</p>
                    </div>
                </div>
            </section>

            {{-- ========== DATA PEMESAN ========== --}}
            <section class="rounded-2xl bg-white p-4 shadow-[0_4px_24px_-6px_rgba(30,41,90,0.10)]">
                <div class="flex items-center justify-between">
                    <h3 class="flex items-center gap-2 text-[16.5px] font-extrabold text-[#0b2e22]">
                        <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <circle cx="12" cy="8" r="3.6" stroke-linecap="round" stroke-linejoin="round"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 20.5c0-4 3.4-6.5 7.5-6.5s7.5 2.5 7.5 6.5z"/>
                        </svg>
                        Data Pemesan
                    </h3>
                    <span class="rounded-md bg-[#cdeedb] px-2 py-[3px] text-[10px] font-bold text-[#0b4a35]">Terdaftar</span>
                </div>

                <div class="mt-3 space-y-2">
                    <div class="rounded-xl bg-[#eef0ff] px-3.5 py-2.5">
                        <p class="text-[10px] font-semibold text-gray-500">Nama Lengkap</p>
                        <p class="text-[14.5px] font-bold text-[#0b2e22]">{{ $pemesanNama }}</p>
                    </div>
                    <div class="rounded-xl bg-[#eef0ff] px-3.5 py-2.5">
                        <p class="text-[10px] font-semibold text-gray-500">No. WhatsApp</p>
                        <p class="text-[14.5px] font-bold tabular-nums text-[#0b2e22]">{{ $pemesanWa }}</p>
                    </div>
                    <div class="rounded-xl bg-[#eef0ff] px-3.5 py-2.5">
                        <p class="text-[10px] font-semibold text-gray-500">Email Konfirmasi</p>
                        <p class="break-all text-[14.5px] font-bold text-[#0b2e22]">{{ $pemesanEmail }}</p>
                    </div>
                </div>
            </section>

            @if($order && ! $isPaid)
            {{-- ========== METODE PEMBAYARAN ========== --}}
            <section class="rounded-2xl bg-white p-4 shadow-[0_4px_24px_-6px_rgba(30,41,90,0.10)]">
                <div class="flex items-start justify-between gap-2">
                    <h3 class="flex items-center gap-2 text-[16.5px] font-extrabold text-[#0b2e22]">
                        <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <rect x="2.5" y="5.5" width="19" height="13" rx="2.5"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.5 10h19"/>
                        </svg>
                        Metode Pembayaran
                    </h3>

                    @if($waitingVerify)
                        <span class="flex shrink-0 items-center gap-1.5 rounded-2xl bg-[#ffd7a8] px-3 py-1.5 text-[10.5px] font-bold leading-tight text-[#7a4a1a]">
                            <span class="h-1.5 w-1.5 shrink-0 animate-pulse rounded-full bg-[#7a4a1a]"></span>
                            Menunggu Verifikasi
                        </span>
                    @elseif($paymentFailed)
                        <span class="shrink-0 rounded-2xl bg-[#f7c9c9] px-3 py-1.5 text-[10.5px] font-bold text-[#8b1f1f]">Pembayaran Gagal</span>
                    @else
                        <span id="payStatus" class="shrink-0 rounded-2xl bg-[#ffd7a8] px-3 py-1.5 text-[10.5px] font-bold text-[#7a4a1a]">Belum Dibayar</span>
                    @endif
                </div>

                @if($waitingVerify)
                    <div class="mt-3 rounded-2xl bg-[#eef0ff] p-4 text-center">
                        <div class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-[#cdeedb] text-[#0b4a35]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="mt-2 text-[13px] font-extrabold text-[#0b2e22]">Bukti pembayaran terkirim</p>
                        <p class="mt-1 text-[11.5px] leading-relaxed text-slate-600">
                            Pembayaran via <span class="font-bold">{{ strtoupper($selectedMetode) }}</span> sedang diverifikasi admin.
                            QR gate dibuat otomatis setelah pembayaran lunas.
                        </p>
                        <a href="{{ route('tiket') }}"
                           class="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-full bg-[#0b2e22] px-6 py-[14px] text-[13px] font-bold text-white transition hover:bg-[#12442f] active:scale-[0.98]">
                            Cek Status Tiket
                        </a>
                    </div>
                @else
                    @if($paymentFailed)
                        <p class="mt-2 rounded-xl bg-[#fde7e7] px-3 py-2 text-[11.5px] font-semibold text-[#8b1f1f]">
                            Pembayaran sebelumnya ditolak. Silakan pilih metode lagi.
                        </p>
                    @endif

                    <p class="mt-1 text-[11.5px] text-[#0b2e22]/75">Pilih metode di bawah ini, lalu selesaikan pembayaran.</p>

                    @if($midtrans['enabled'])
                    <div class="mt-3 grid grid-cols-2 gap-2" id="methodList">
                        <button type="button"
                                class="method-btn flex flex-col items-start gap-1 rounded-xl border-2 px-3 py-2.5 text-left transition active:scale-[0.98] {{ $selectedMetode === 'bca' ? 'border-[#0b2e22] bg-[#eef0ff]' : 'border-transparent bg-[#eef0ff] hover:border-[#c9d2ee]' }}"
                                data-method="bca"
                                aria-pressed="{{ $selectedMetode === 'bca' ? 'true' : 'false' }}">
                            <span class="rounded-md bg-[#dbe4ff] px-2 py-0.5 text-[12px] font-extrabold tracking-wide text-[#0b2e5a]">BCA</span>
                            <span class="text-[10px] font-semibold text-slate-500">Virtual Account</span>
                        </button>

                        <button type="button"
                                class="method-btn flex flex-col items-start gap-1 rounded-xl border-2 px-3 py-2.5 text-left transition active:scale-[0.98] {{ $selectedMetode === 'mandiri' ? 'border-[#0b2e22] bg-[#eef0ff]' : 'border-transparent bg-[#eef0ff] hover:border-[#c9d2ee]' }}"
                                data-method="mandiri"
                                aria-pressed="{{ $selectedMetode === 'mandiri' ? 'true' : 'false' }}">
                            <span class="rounded-md bg-[#e3e5f7] px-2 py-0.5 text-[12px] font-extrabold tracking-wide text-[#7a5a2e]">MANDIRI</span>
                            <span class="text-[10px] font-semibold text-slate-500">Bill Payment</span>
                        </button>

                        <button type="button"
                                class="method-btn col-span-2 flex flex-col items-start gap-1 rounded-xl border-2 px-3 py-2.5 text-left transition active:scale-[0.98] {{ $selectedMetode === 'qris' ? 'border-[#0b2e22] bg-[#eef0ff]' : 'border-transparent bg-[#eef0ff] hover:border-[#c9d2ee]' }}"
                                data-method="qris"
                                aria-pressed="{{ $selectedMetode === 'qris' ? 'true' : 'false' }}">
                            <span class="rounded-md bg-[#0b2e22] px-2 py-0.5 text-[11px] font-extrabold tracking-wide text-white">QRIS</span>
                            <span class="text-[10px] font-semibold text-slate-500">QR Code</span>
                        </button>
                    </div>

                    {{-- BCA Virtual Account --}}
                    <div data-panel="bca" class="method-panel {{ $selectedMetode === 'bca' ? '' : 'hidden' }} mt-3 rounded-2xl bg-[#eef0ff] p-3">
                        <div class="flex items-center justify-between">
                            <span class="rounded-md bg-[#dbe4ff] px-3 py-1 text-[15px] font-extrabold tracking-wide text-[#0b2e5a]">BCA</span>
                            <span class="text-[10.5px] text-gray-400">Virtual Account</span>
                        </div>
                        <div id="bcaBox" class="mt-2.5 hidden rounded-xl bg-white px-3.5 py-3 shadow-sm">
                            <p class="text-[10px] font-semibold text-gray-500">Nomor Virtual Account</p>
                            <div class="mt-0.5 flex flex-wrap items-center justify-between gap-2">
                                <p id="bcaVa" class="min-w-0 flex-1 break-all text-[17px] font-extrabold leading-tight tabular-nums tracking-[0.06em] text-[#0b2e22]">-</p>
                                <button type="button" id="bcaCopy" data-copy=""
                                        class="copy-btn flex max-w-full shrink-0 items-center gap-1.5 rounded-full bg-[#0b2e22] px-3 py-2 text-[11px] font-bold text-white transition hover:bg-[#12442f] active:scale-95">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="copy-label">Salin</span>
                                </button>
                            </div>
                            <p id="bcaExpiry" class="mt-1 text-[10px] text-slate-500"></p>
                        </div>
                        <p id="state-bca" class="mt-2 flex items-center gap-1.5 text-[11px] font-bold text-[#7a4a1a]">
                            <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-current"></span>
                            Pilih BCA untuk menampilkan nomor Virtual Account.
                        </p>
                        <p class="mt-1.5 text-[10.5px] leading-snug text-slate-500">Bayar lewat m-BCA, ATM, atau internet banking dengan nomor VA di atas.</p>
                    </div>

                    {{-- Mandiri Bill Payment --}}
                    <div data-panel="mandiri" class="method-panel {{ $selectedMetode === 'mandiri' ? '' : 'hidden' }} mt-3 rounded-2xl bg-[#eef0ff] p-3">
                        <div class="flex items-center justify-between">
                            <span class="rounded-md bg-[#e3e5f7] px-3 py-1 text-[15px] font-extrabold tracking-wide text-[#7a5a2e]">MANDIRI</span>
                            <span class="text-[10.5px] text-gray-400">Bill Payment</span>
                        </div>
                        <div id="mandiriBox" class="mt-2.5 hidden rounded-xl bg-white px-3.5 py-3 shadow-sm">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0 flex-1">
                                    <p class="text-[10px] font-semibold text-gray-500">Biller Code</p>
                                    <p id="mandiriBiller" class="break-all text-[16px] font-extrabold tabular-nums text-[#0b2e22]">-</p>
                                </div>
                                <div class="min-w-0 flex-1 text-right">
                                    <p class="text-[10px] font-semibold text-gray-500">Bill Key</p>
                                    <p id="mandiriKey" class="break-all text-[16px] font-extrabold tabular-nums text-[#0b2e22]">-</p>
                                </div>
                            </div>
                            <div class="mt-2 flex flex-wrap justify-end">
                                <button type="button" id="mandiriCopy" data-copy=""
                                        class="copy-btn flex max-w-full shrink-0 items-center gap-1.5 rounded-full bg-[#0b2e22] px-3 py-2 text-[11px] font-bold text-white transition hover:bg-[#12442f] active:scale-95">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="copy-label">Salin Bill Key</span>
                                </button>
                            </div>
                            <p id="mandiriExpiry" class="mt-1 text-[10px] text-slate-500"></p>
                        </div>
                        <p id="state-mandiri" class="mt-2 flex items-center gap-1.5 text-[11px] font-bold text-[#7a4a1a]">
                            <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-current"></span>
                            Pilih Mandiri untuk menampilkan Biller Code dan Bill Key.
                        </p>
                        <p class="mt-1.5 text-[10.5px] leading-snug text-slate-500">Bayar lewat ATM Mandiri atau Mandiri Online dengan Biller Code dan Bill Key di atas.</p>
                    </div>

                    {{-- QRIS --}}
                    <div data-panel="qris" class="method-panel {{ $selectedMetode === 'qris' ? '' : 'hidden' }} mt-3 rounded-2xl bg-[#eef0ff] p-3">
                        <div class="flex items-center justify-between">
                            <span class="rounded-md bg-[#0b2e22] px-3 py-1 text-[13px] font-extrabold tracking-wide text-white">QRIS</span>
                            <span class="text-[10.5px] text-gray-400">QR Code</span>
                        </div>
                        <div id="qrisBox" class="mt-2.5 hidden rounded-2xl bg-white p-3 text-center shadow-sm">
                            <p class="text-[10.5px] font-bold text-slate-600">Scan QRIS dengan aplikasi bank / e-wallet</p>
                            <div class="mt-2 flex justify-center">
                                <img id="qrisImage" alt="Kode QRIS pembayaran" class="h-[190px] w-[190px] rounded-lg bg-white object-contain">
                            </div>
                            <p id="qrisExpiry" class="mt-1 text-[10px] text-slate-500"></p>
                        </div>
                        <p id="state-qris" class="mt-2 flex items-center gap-1.5 text-[11px] font-bold text-[#7a4a1a]">
                            <span class="h-1.5 w-1.5 animate-pulse rounded-full bg-current"></span>
                            Pilih QRIS untuk menampilkan kode QR.
                        </p>
                        <p class="mt-1.5 text-[10.5px] leading-snug text-slate-500">Pembayaran diverifikasi otomatis. Tiket dan QR gate terbit setelah lunas.</p>
                    </div>
                    @else
                    <p class="mt-3 rounded-xl bg-[#eef0ff] px-3 py-3 text-center text-[11.5px] text-[#0b2e22]/70">Pembayaran online belum aktif.</p>
                    @endif

                    <div class="mt-3 flex items-center justify-between rounded-xl bg-[#f6f7ff] px-3.5 py-2.5">
                        <p class="text-[10px] font-semibold text-gray-500">Total Pembayaran</p>
                        <p class="text-[19px] font-extrabold tabular-nums tracking-[0.04em] text-[#0b2e22]">Rp {{ $totalFormatted }}</p>
                    </div>
                @endif
            </section>
            @endif

            @if($order && ! $isPaid)
            <div class="pt-1">
                <a href="https://wa.me/6281234567890" target="_blank" rel="noopener"
                   class="mt-3.5 flex items-center justify-center gap-1.5 text-[12px] font-semibold text-[#7a4a1a] hover:underline">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M4 4h16a1 1 0 011 1v11a1 1 0 01-1 1H9l-4.2 3.4A.6.6 0 013.8 20V17H4a1 1 0 01-1-1V5a1 1 0 011-1zm4 4v1.6h8V8zm0 3.4V13h5v-1.6z"/>
                    </svg>
                    Butuh Bantuan? Hubungi WhatsApp Admin
                </a>
            </div>
            @endif

            {{-- ========== SUDAH LUNAS (MIDTRANS) ========== --}}
            @if($order && $isPaid)
            <section class="rounded-2xl bg-white p-4 text-center shadow-[0_4px_24px_-6px_rgba(30,41,90,0.10)]">
                <p class="text-[12.5px] font-extrabold text-[#0b2e22]">Pembayaran {{ $paidVia !== '' ? $paidVia : 'Online' }} Lunas</p>
                <p class="mt-1 text-[11.5px] leading-relaxed text-gray-500">Bukti transfer manual tidak diperlukan lagi. Buka Tiket Saya untuk melihat QR code masuk Anda.</p>
                <a href="{{ route('tiket') }}"
                   class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-full bg-[#0b2e22] px-6 py-[15px] text-[13.5px] font-bold text-white transition hover:bg-[#12442f] active:scale-[0.98]">
                    Buka Tiket Saya
                </a>
            </section>
            @endif

@else
            {{-- ========== EMPTY STATE (belum ada pesanan aktif) ========== --}}
            <section class="mt-10 rounded-2xl bg-white p-6 text-center shadow-[0_4px_24px_-6px_rgba(30,41,90,0.10)]">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-[#DFE2F8] text-[#0b2e22]">
                    <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 3h12v18l-3-2-3 2-3-2-3 2V3zM9 8h6M9 12h6"/>
                    </svg>
                </div>
                <h2 class="mt-4 text-[16px] font-bold text-[#0b2e22]">Belum Ada Pesanan Aktif</h2>
                <p class="mt-1.5 text-[12px] leading-relaxed text-gray-500">Silakan pilih tiket atau cabin terlebih dahulu untuk melanjutkan proses pembayaran.</p>
                <a href="{{ route('reservasi') }}"
                   class="mt-5 inline-flex items-center justify-center gap-2 rounded-xl bg-[#0b2e22] px-6 py-3 text-[12.5px] font-bold text-white transition hover:bg-[#124c2f] active:scale-95">
                    Mulai Reservasi
                </a>
            </section>
@endif
        </main>

@endsection
@section('overlays')
        {{-- ========== TOAST ========== --}}
        <div id="toast" role="status"
             class="pointer-events-none fixed left-1/2 top-4 z-50 w-[calc(100%-32px)] max-w-[358px] -translate-x-1/2 -translate-y-3 rounded-xl bg-[#0b2e22] px-4 py-3 text-center text-[12.5px] font-semibold text-white opacity-0 shadow-[0_10px_24px_-8px_rgba(11,46,34,0.55)] transition duration-300"></div>
@endsection

@push('scripts')
<script>
(function () {
    const $   = (id) => document.getElementById(id);
    const pad = (n) => String(n).padStart(2, '0');

    /* ---------- Toast ---------- */
    let toastTimer;
    function toast(msg) {
        const el = $('toast');
        el.textContent = msg;
        el.classList.remove('opacity-0', '-translate-y-3');
        el.classList.add('opacity-100', 'translate-y-0');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => {
            el.classList.add('opacity-0', '-translate-y-3');
            el.classList.remove('opacity-100', 'translate-y-0');
        }, 2400);
    }

    /* =====================================================
     * COUNTDOWN REAL-TIME
     * - Jika server mengirim $expiredAt (Carbon) -> pakai itu.
     * - Jika tidak -> mulai dari 01:59:44 dan disimpan di
     *   localStorage supaya tidak reset saat halaman di-refresh.
     * ===================================================== */
    const card   = $('timerCard');
    const elTime = $('countdown');
    const STORE  = 'jiwanta_deadline_pembayaran';
    let deadline = card.dataset.expiredAt ? new Date(card.dataset.expiredAt).getTime() : NaN;

    if (isNaN(deadline)) {
        try {
            const saved = Number(localStorage.getItem(STORE));
            if (saved && saved > Date.now()) deadline = saved;
        } catch (e) {}
    }
    if (isNaN(deadline)) {
        deadline = Date.now() + (1 * 3600 + 59 * 60 + 44) * 1000;
        try { localStorage.setItem(STORE, String(deadline)); } catch (e) {}
    }

    let expired = false;
    function tick() {
        const left = Math.max(0, Math.round((deadline - Date.now()) / 1000));
        const h = Math.floor(left / 3600), m = Math.floor((left % 3600) / 60), s = left % 60;
        elTime.textContent = `${pad(h)}:${pad(m)}:${pad(s)}`;

        // < 10 menit: beri penekanan warna
        elTime.classList.toggle('text-[#b42318]', left > 0 && left < 600);
        elTime.classList.toggle('text-[#0b2e22]', !(left > 0 && left < 600));

        if (left === 0 && !expired) {
            expired = true;
            $('countdownLabel').textContent = 'Waktu Pembayaran Habis';
            const submitBtn = $('submitBtn');
            if (submitBtn) submitBtn.disabled = true;
            toast('Waktu pembayaran habis. Silakan buat reservasi baru.');
        }
    }
    tick();
    setInterval(tick, 1000);
    document.addEventListener('visibilitychange', tick);

    /* ---------- Salin nomor rekening ---------- */
    async function copyText(text) {
        try {
            await navigator.clipboard.writeText(text);
        } catch (e) {
            const ta = document.createElement('textarea');
            ta.value = text; ta.style.cssText = 'position:fixed;opacity:0';
            document.body.appendChild(ta); ta.select();
            document.execCommand('copy'); ta.remove();
        }
    }
    document.querySelectorAll('.copy-btn').forEach((btn) => {
        btn.addEventListener('click', async () => {
            await copyText(btn.dataset.copy);
            const label = btn.querySelector('.copy-label');
            label.textContent = 'Tersalin!';
            btn.classList.replace('bg-[#0b2e22]', 'bg-[#0b7a4a]');
            toast(`${btn.dataset.copy} disalin`);
            setTimeout(() => {
                label.textContent = 'Salin';
                btn.classList.replace('bg-[#0b7a4a]', 'bg-[#0b2e22]');
            }, 1800);
        });
    });

})();
</script>
@endpush

@push('scripts')
@if($order && !empty($midtrans['enabled']) && ! $isPaid)
<script>
(function () {
    const $ = (id) => document.getElementById(id);
    const CHARGE_URL = @json($midtrans['charge_url'] ?? '');
    const STATUS_URL = @json($midtrans['status_url'] ?? '');
    const TIKET_URL = @json(route('tiket', absolute: false));
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const LABEL = { bca: 'BCA Virtual Account', mandiri: 'Mandiri Bill', qris: 'QRIS' };
    const busy = {};
    let pollTimer = null;

    let toastTimer;
    function toast(msg) {
        const el = $('toast');
        if (!el) return;
        el.textContent = msg;
        el.classList.remove('opacity-0', '-translate-y-3');
        el.classList.add('opacity-100', 'translate-y-0');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => {
            el.classList.add('opacity-0', '-translate-y-3');
            el.classList.remove('opacity-100', 'translate-y-0');
        }, 2600);
    }

    function setActive(method) {
        document.querySelectorAll('.method-btn').forEach((b) => {
            const on = b.dataset.method === method;
            b.classList.toggle('border-[#0b2e22]', on);
            b.classList.toggle('border-transparent', !on);
            b.setAttribute('aria-pressed', on ? 'true' : 'false');
        });
        document.querySelectorAll('.method-panel').forEach((p) => {
            p.classList.toggle('hidden', p.dataset.panel !== method);
        });
    }

    function setState(method, text, tone) {
        const el = $('state-' + method);
        if (!el) return;
        const color = tone === 'error'
            ? 'text-[#8b1f1f]'
            : (tone === 'success' ? 'text-[#0b5a3a]' : 'text-[#7a4a1a]');
        el.className = 'mt-2 flex items-center gap-1.5 text-[11px] font-bold ' + color;
        el.innerHTML = '<span class="h-1.5 w-1.5 animate-pulse rounded-full bg-current"></span>' + text;
    }

    function expiryText(value) {
        if (!value) return '';
        const parsed = new Date(String(value).replace(' ', 'T'));
        if (isNaN(parsed.getTime())) return 'Berlaku sampai ' + value;

        return 'Berlaku sampai ' + parsed.toLocaleString('id-ID', {
            day: '2-digit',
            month: 'short',
            hour: '2-digit',
            minute: '2-digit',
        });
    }

    function render(method, data) {
        if (method === 'qris') {
            $('qrisImage').src = data.qr_url;
            $('qrisBox').classList.remove('hidden');
            $('qrisExpiry').textContent = expiryText(data.expiry_time);
        } else if (method === 'bca') {
            $('bcaVa').textContent = data.va_number;
            $('bcaCopy').dataset.copy = data.va_number;
            $('bcaBox').classList.remove('hidden');
            $('bcaExpiry').textContent = expiryText(data.expiry_time);
        } else if (method === 'mandiri') {
            $('mandiriBiller').textContent = data.biller_code;
            $('mandiriKey').textContent = data.bill_key;
            $('mandiriCopy').dataset.copy = data.bill_key;
            $('mandiriBox').classList.remove('hidden');
            $('mandiriExpiry').textContent = expiryText(data.expiry_time);
        }

        setState(method, 'Menunggu pembayaran...', 'waiting');
        startPolling();
    }

    async function charge(method) {
        if (!CHARGE_URL || busy[method]) return;

        busy[method] = true;
        setState(method, 'Menyiapkan ' + LABEL[method] + '...', 'waiting');

        try {
            const res = await fetch(CHARGE_URL, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ metode: method }),
            });
            const data = await res.json().catch(function () { return null; });

            if (!res.ok || !data || !data.ok) {
                const message = (data && data.message) || 'Gagal membuat pembayaran.';
                setState(method, message, 'error');
                toast(message);
                return;
            }

            render(method, data);
        } catch (e) {
            setState(method, 'Gagal menghubungi Midtrans. Coba lagi.', 'error');
            toast('Gagal menghubungi Midtrans.');
        } finally {
            busy[method] = false;
        }
    }

    document.querySelectorAll('.method-btn').forEach((btn) => {
        btn.addEventListener('click', function () {
            const method = btn.dataset.method;
            setActive(method);
            charge(method);
        });
    });

    function startPolling() {
        if (pollTimer || !STATUS_URL) return;

        pollTimer = setInterval(async function () {
            try {
                const res = await fetch(STATUS_URL + '?sync=1', { headers: { 'Accept': 'application/json' } });
                const data = await res.json();

                if (data.status === 'lunas') {
                    clearInterval(pollTimer);
                    pollTimer = null;
                    toast('Pembayaran berhasil. Membuka Tiket Saya...');
                    setTimeout(function () { window.location.href = TIKET_URL; }, 800);
                } else if (data.status === 'gagal') {
                    clearInterval(pollTimer);
                    pollTimer = null;
                    toast('Pembayaran gagal atau kedaluwarsa. Silakan pilih metode lagi.');
                }
            } catch (e) { /* coba lagi pada interval berikutnya */ }
        }, 4000);
    }

    const initial = @json($selectedMetode);
    if (initial) {
        setActive(initial);
        charge(initial);
    }
})();
</script>
@endif
@endpush

