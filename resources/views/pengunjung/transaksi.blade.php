@extends('layouts.dashboard')

@section('title', 'Transaksi - Jiwanta')

{{-- Font desain: Plus Jakarta Sans (aman jika layout belum punya @stack('styles'), hanya tidak terpakai) --}}
@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@endpush

@section('content')

<div class="min-h-screen w-full bg-[#f1f1fa] font-['Plus_Jakarta_Sans',ui-sans-serif,system-ui,sans-serif] text-[#0b2e22] antialiased">
    <div class="relative mx-auto min-h-screen w-full max-w-[390px] bg-[#f8f8ff] pb-[120px]">

        {{-- ========== HEADER ========== --}}
       <header id="top-header" class="sticky top-0 z-40 flex items-center justify-between bg-[#F9F8FF]/85 px-5 py-3 backdrop-blur transition-shadow duration-300">
            <span class="text-[19px] font-bold tracking-tight text-[#0B3A22]">Jiwanta</span>
            <div class="flex items-center gap-3">
                <span class="text-[13px] font-medium text-slate-600">Transaksi</span>
                <img src="{{ asset('images/profil.jpg') }}" alt="Profil" class="h-9 w-9 rounded-full bg-[#C9B99A] object-cover">
            </div>
        </header>

        <main class="space-y-4 px-3.5">

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
                    <img src="{{ asset('images/kolam jiwanta.jpg') }}" alt="Kolam Jiwanta"
                         class="h-[52px] w-[52px] shrink-0 rounded-lg bg-[#8fa89a] object-cover">
                    <div class="min-w-0">
                        <span class="inline-block rounded-md bg-[#cdeedb] px-2 py-[3px] text-[9.5px] font-bold text-[#0b4a35]">Tiket Masuk &amp; Kolam</span>
                        <h2 class="mt-0.5 text-[17px] font-extrabold leading-tight text-[#0b2e22]">Tiket Renang Premier</h2>
                        <p class="mt-0.5 flex items-center gap-1.5 text-[11.5px] font-medium text-gray-500">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            2 Pengunjung Dewasa
                        </p>
                    </div>
                </div>

                <div class="mt-3.5 space-y-2 rounded-xl bg-[#eef0ff] px-3 py-3">
                    <div class="flex items-center gap-2.5">
                        <svg class="h-4 w-4 shrink-0 text-[#0b2e22]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-[12.5px] font-bold text-[#0b2e22]">Minggu, 20 September 2026</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-[#0b2e22]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="text-[12px] font-medium leading-snug text-[#0b2e22]/85">Jiwanta Ciwidey Resort, Jl. Raya Ciwidey - Patengan KM 11</span>
                    </div>
                </div>

                <div class="mt-4 space-y-1.5 text-[12px] text-[#0b2e22]/80">
                    <div class="flex justify-between">
                        <span>Harga Satuan (x2)</span>
                        <span class="tabular-nums">Rp 75.000 x 2</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Subtotal Tiket</span>
                        <span class="tabular-nums">Rp 150.000</span>
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
                        <p class="mt-0.5 text-[30px] font-extrabold tabular-nums tracking-tight">150.000</p>
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
                        <p class="text-[14.5px] font-bold text-[#0b2e22]">Ahmad Fadillah</p>
                    </div>
                    <div class="rounded-xl bg-[#eef0ff] px-3.5 py-2.5">
                        <p class="text-[10px] font-semibold text-gray-500">No. WhatsApp</p>
                        <p class="text-[14.5px] font-bold tabular-nums text-[#0b2e22]">0812-3456-7890</p>
                    </div>
                    <div class="rounded-xl bg-[#eef0ff] px-3.5 py-2.5">
                        <p class="text-[10px] font-semibold text-gray-500">Email Konfirmasi</p>
                        <p class="break-all text-[14.5px] font-bold text-[#0b2e22]">ahmad.fadillah@example.com</p>
                    </div>
                </div>
            </section>

            {{-- ========== TRANSFER BANK MANUAL ========== --}}
            <section class="rounded-2xl bg-white p-4 shadow-[0_4px_24px_-6px_rgba(30,41,90,0.10)]">
                <h3 class="flex items-center gap-2 text-[16.5px] font-extrabold text-[#0b2e22]">
                    <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9.5L12 4l9 5.5M5 10v8M9.5 10v8M14.5 10v8M19 10v8M3 20.5h18"/>
                    </svg>
                    Transfer Bank Manual
                </h3>
                <p class="mt-1 text-[11.5px] text-[#0b2e22]/75">Pilih salah satu rekening resmi Jiwanta di bawah ini:</p>

                {{-- BCA --}}
                <div class="mt-3 rounded-2xl bg-[#eef0ff] p-3">
                    <div class="flex items-center justify-between">
                        <span class="rounded-md bg-[#dbe4ff] px-3 py-1 text-[17px] font-extrabold tracking-wide text-[#0b2e5a]">BCA</span>
                        <span class="text-[10.5px] text-gray-400">Bank Central Asia</span>
                    </div>
                    <div class="mt-2.5 flex items-center justify-between rounded-xl bg-white px-3.5 py-2.5 shadow-sm">
                        <div>
                            <p class="text-[10px] font-semibold text-gray-500">Nomor Rekening</p>
                            <p class="text-[19px] font-extrabold tabular-nums tracking-[0.12em] text-[#0b2e22]">8405123499</p>
                        </div>
                        <button type="button" data-copy="8405123499"
                                class="copy-btn flex items-center gap-1.5 rounded-full bg-[#0b2e22] px-3 py-2 text-[11px] font-bold text-white transition hover:bg-[#12442f] active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0b2e22] focus-visible:ring-offset-2">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <span class="copy-label">Salin</span>
                        </button>
                    </div>
                    <p class="mt-2 text-[11.5px] text-[#0b2e22]/80">a.n <span class="font-semibold">Jiwanta Thermall Springs</span></p>
                </div>

                {{-- MANDIRI --}}
                <div class="mt-4 rounded-2xl bg-[#eef0ff] p-3">
                    <div class="flex items-center justify-between">
                        <span class="rounded-md bg-[#e3e5f7] px-3 py-1 text-[15px] font-extrabold tracking-wide text-[#7a5a2e]">MANDIRI</span>
                        <span class="text-[10.5px] text-gray-400">Bank Mandiri</span>
                    </div>
                    <div class="mt-2.5 flex items-center justify-between rounded-xl bg-white px-3.5 py-2.5 shadow-sm">
                        <div>
                            <p class="text-[10px] font-semibold text-gray-500">Nomor Rekening</p>
                            <p class="text-[19px] font-extrabold tabular-nums tracking-[0.12em] text-[#0b2e22]">1310098765432</p>
                        </div>
                        <button type="button" data-copy="1310098765432"
                                class="copy-btn flex items-center gap-1.5 rounded-full bg-[#0b2e22] px-3 py-2 text-[11px] font-bold text-white transition hover:bg-[#12442f] active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0b2e22] focus-visible:ring-offset-2">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <span class="copy-label">Salin</span>
                        </button>
                    </div>
                    <p class="mt-2 text-[11.5px] text-[#0b2e22]/80">a.n <span class="font-semibold">Jiwanta Thermall Springs</span></p>
                </div>
            </section>

            {{-- ========== BUKTI PEMBAYARAN ========== --}}
            <section class="rounded-2xl bg-white p-4 shadow-[0_4px_24px_-6px_rgba(30,41,90,0.10)]">
                <div class="flex items-start justify-between gap-2">
                    <h3 class="flex items-start gap-2 text-[16.5px] font-extrabold leading-tight text-[#0b2e22]">
                        <svg class="mt-0.5 h-[18px] w-[18px] shrink-0" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 3h12v18l-3-2-3 2-3-2-3 2zM9 8h6M9 12h6"/>
                        </svg>
                        <span>Bukti<br>Pembayaran</span>
                    </h3>

                    {{-- Status: menunggu --}}
                    <span id="statusWaiting" class="flex items-center gap-1.5 rounded-2xl bg-[#ffd7a8] px-3 py-1.5 text-[10.5px] font-bold leading-tight text-[#7a4a1a]">
                        <span class="h-1.5 w-1.5 shrink-0 animate-pulse rounded-full bg-[#7a4a1a]"></span>
                        <span>Menunggu Verifikasi<br>Admin</span>
                    </span>
                    {{-- Status: sedang diverifikasi (muncul setelah kirim) --}}
                    <span id="statusVerifying" class="hidden items-center gap-1.5 rounded-2xl bg-[#cdeedb] px-3 py-1.5 text-[10.5px] font-bold leading-tight text-[#0b4a35]">
                        <span class="h-1.5 w-1.5 shrink-0 animate-pulse rounded-full bg-[#0b4a35]"></span>
                        <span>Sedang Diverifikasi<br>Admin</span>
                    </span>
                </div>

                {{-- File terunggah --}}
                <div id="fileRow" class="mt-3 flex items-center gap-3 rounded-xl bg-[#eef0ff] p-2.5">
                    <div class="relative shrink-0">
                        <img id="fileThumb" src="{{ asset('images/bukti transfer.jpg') }}" alt="Bukti transfer"
                             class="h-[46px] w-[46px] rounded-lg bg-gray-300 object-cover">
                        <span class="absolute -right-1 -bottom-1 flex h-4 w-4 items-center justify-center rounded-[5px] bg-white text-[#0b2e22] shadow ring-1 ring-[#eef0ff]">
                            <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" stroke-width="4" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p id="fileName" class="truncate text-[12.5px] font-extrabold text-[#0b2e22]">bukti_transfer_bca_ahmad.jpg</p>
                        <p id="fileMeta" class="mt-0.5 text-[10.5px] text-gray-500">2.4 MB • 20 Sep 2026 14:30 WIB</p>
                        <p class="mt-0.5 flex items-center gap-1 text-[10.5px] font-bold text-[#0b2e22]">
                            <svg class="h-3.5 w-3.5 text-[#0b2e22]" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l2.4 1.7 2.9-.1 1 2.7 2.4 1.7-.9 2.8.9 2.8-2.4 1.7-1 2.7-2.9-.1L12 22l-2.4-1.7-2.9.1-1-2.7L3.3 16l.9-2.8-.9-2.8 2.4-1.7 1-2.7 2.9.1z"/>
                                <path d="M8.5 12.2l2.4 2.4 4.6-4.8" stroke="#fff" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Struk Berhasil Diunggah
                        </p>
                    </div>
                    <button type="button" id="editFileBtn" aria-label="Ganti file"
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#dbe0fa] text-[#0b2e22] transition hover:bg-[#cdd4f6] active:scale-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0b2e22]">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.3" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 20h4L19 9a2.8 2.8 0 00-4-4L4 16zM13.5 6.5l4 4"/>
                        </svg>
                    </button>
                </div>

                {{-- Area upload --}}
                <label for="fileInput" id="dropZone"
                       class="mt-3 flex cursor-pointer flex-col items-center rounded-xl bg-[#eef0ff] px-4 py-5 text-center transition hover:bg-[#e5e8fb]">
                    <input type="file" id="fileInput" accept="image/png,image/jpeg" class="hidden">
                    <div class="mb-2.5 flex h-11 w-11 items-center justify-center rounded-full bg-[#cdeedb] text-[#0b2e22]">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 8.5A2.5 2.5 0 016.5 6h1.3l1.2-1.8A1.5 1.5 0 0110.2 3.5h3.6a1.5 1.5 0 011.2.7L16.2 6h1.3A2.5 2.5 0 0120 8.5v9A2.5 2.5 0 0117.5 20h-11A2.5 2.5 0 014 17.5z"/>
                            <circle cx="12" cy="12.7" r="3.3"/>
                        </svg>
                    </div>
                    <p class="text-[12.5px] font-extrabold text-[#0b2e22]">Upload Foto Struk / Screenshot Bukti Transfer</p>
                    <p class="mt-1 text-[10px] font-medium text-gray-500">Format JPG, PNG (Maksimal 5MB)</p>
                </label>
                <p id="uploadError" class="mt-2 hidden text-center text-[11px] font-semibold text-red-600" role="alert"></p>
            </section>

            {{-- ========== TOMBOL KIRIM ========== --}}
            <div class="pt-1">
                <button type="button" id="submitBtn"
                        class="flex w-full items-center justify-center gap-2 rounded-full bg-[#0b2e22] py-[17px] text-[14px] font-bold text-white shadow-[0_10px_24px_-8px_rgba(11,46,34,0.55)] transition hover:bg-[#12442f] active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-60 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#0b2e22] focus-visible:ring-offset-2">
                    <span id="submitLabel">Kirim &amp; Cek Status Tiket</span>
                    <svg id="submitArrow" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.6" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6"/>
                    </svg>
                    <svg id="submitSpin" class="hidden h-4 w-4 animate-spin" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                        <path stroke-linecap="round" d="M12 3a9 9 0 019 9"/>
                    </svg>
                </button>

                <a href="https://wa.me/6281234567890" target="_blank" rel="noopener"
                   class="mt-3.5 flex items-center justify-center gap-1.5 text-[12px] font-semibold text-[#7a4a1a] hover:underline">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M4 4h16a1 1 0 011 1v11a1 1 0 01-1 1H9l-4.2 3.4A.6.6 0 013.8 20V17H4a1 1 0 01-1-1V5a1 1 0 011-1zm4 4v1.6h8V8zm0 3.4V13h5v-1.6z"/>
                    </svg>
                    Butuh Bantuan? Hubungi WhatsApp Admin
                </a>
            </div>
        </main>

        {{-- ========== BOTTOM NAVIGATION ========== --}}
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
                <a href="#" class="flex flex-1 flex-col items-center gap-1 text-[#0b2e22]">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 11l9-7 9 7v9a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-9z"/>
                    </svg>
                    <span class="text-[10.5px] font-bold">Reservasi</span>
                </a>
                <a href="{{ route('transaksi') }}" aria-current="page" class="flex flex-1 flex-col items-center gap-1 text-[#0b2e22]">
                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v17l2-1.2 2 1.2 2-1.2 2 1.2 2-1.2 2 1.2 2-1.2V4a2 2 0 00-2-2H6zm2 5h8v2H8V7zm0 4h8v2H8v-2zm0 4h5v2H8v-2z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-[10.5px] font-extrabold">Transaksi</span>
                </a>
                <a href="#" class="flex flex-1 flex-col items-center gap-1 text-[#0b2e22]">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                    </svg>
                    <span class="text-[10.5px] font-bold">Tiket Saya</span>
                </a>
            </div>
        </nav>

        {{-- ========== TOAST ========== --}}
        <div id="toast" role="status"
             class="pointer-events-none fixed left-1/2 top-4 z-50 w-[calc(100%-32px)] max-w-[358px] -translate-x-1/2 -translate-y-3 rounded-xl bg-[#0b2e22] px-4 py-3 text-center text-[12.5px] font-semibold text-white opacity-0 shadow-[0_10px_24px_-8px_rgba(11,46,34,0.55)] transition duration-300"></div>
    </div>
</div>

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
            $('submitBtn').disabled = true;
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
            toast(`Nomor rekening ${btn.dataset.copy} disalin`);
            setTimeout(() => {
                label.textContent = 'Salin';
                btn.classList.replace('bg-[#0b7a4a]', 'bg-[#0b2e22]');
            }, 1800);
        });
    });

    /* ---------- Upload bukti transfer ---------- */
    const input  = $('fileInput');
    const zone   = $('dropZone');
    const MAX    = 5 * 1024 * 1024;
    const BULAN  = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    let uploaded = null;

    const showError = (msg) => {
        const el = $('uploadError');
        el.textContent = msg || '';
        el.classList.toggle('hidden', !msg);
    };
    const fmtSize = (b) => b >= 1048576 ? (b / 1048576).toFixed(1) + ' MB' : Math.max(1, Math.round(b / 1024)) + ' KB';
    const fmtWIB  = (d) => {
        const w = new Date(d.getTime() + (7 * 60 + d.getTimezoneOffset()) * 60000);
        return `${pad(w.getDate())} ${BULAN[w.getMonth()]} ${w.getFullYear()} ${pad(w.getHours())}:${pad(w.getMinutes())} WIB`;
    };

    function handleFile(file) {
        showError('');
        if (!file) return;
        if (!['image/jpeg', 'image/png'].includes(file.type)) return showError('Format file harus JPG atau PNG.');
        if (file.size > MAX) return showError('Ukuran file maksimal 5MB.');

        uploaded = file;
        $('fileName').textContent = file.name;
        $('fileMeta').textContent = `${fmtSize(file.size)} • ${fmtWIB(new Date())}`;

        const url = URL.createObjectURL(file);
        const img = $('fileThumb');
        img.onload = () => URL.revokeObjectURL(url);
        img.src = url;

        showStatus('waiting');
        toast('Bukti transfer berhasil diunggah');
    }

    $('editFileBtn').addEventListener('click', () => input.click());
    input.addEventListener('change', (e) => { handleFile(e.target.files[0]); input.value = ''; });

    ['dragenter', 'dragover'].forEach((ev) => zone.addEventListener(ev, (e) => {
        e.preventDefault(); zone.classList.add('ring-2', 'ring-[#0b2e22]');
    }));
    ['dragleave', 'drop'].forEach((ev) => zone.addEventListener(ev, (e) => {
        e.preventDefault(); zone.classList.remove('ring-2', 'ring-[#0b2e22]');
    }));
    zone.addEventListener('drop', (e) => handleFile(e.dataTransfer.files[0]));

    /* ---------- Status verifikasi ---------- */
    function showStatus(state) {
        const waiting = $('statusWaiting'), verifying = $('statusVerifying');
        const v = state === 'verifying';
        waiting.classList.toggle('hidden', v);
        waiting.classList.toggle('flex', !v);
        verifying.classList.toggle('hidden', !v);
        verifying.classList.toggle('flex', v);
    }

    /* ---------- Kirim & cek status ---------- */
    $('submitBtn').addEventListener('click', async function () {
        if (expired) return;
        const btn = this;
        btn.disabled = true;
        $('submitLabel').textContent = 'Mengirim...';
        $('submitArrow').classList.add('hidden');
        $('submitSpin').classList.remove('hidden');

        /* TODO: ganti dengan request ke backend, contoh:
         *
         * const fd = new FormData();
         * if (uploaded) fd.append('bukti', uploaded);
         * await fetch("{{ url('/transaksi/bukti') }}", {
         *     method: 'POST',
         *     headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
         *     body: fd,
         * });
         */
        await new Promise((r) => setTimeout(r, 1400));

        showStatus('verifying');
        toast('Bukti terkirim. Admin sedang memverifikasi pembayaran Anda.');
        $('submitLabel').textContent = 'Kirim & Cek Status Tiket';
        $('submitArrow').classList.remove('hidden');
        $('submitSpin').classList.add('hidden');
        btn.disabled = false;

        // Jika ingin pindah halaman setelah sukses:
        // window.location.href = "{{ route('transaksi') }}";
    });
})();
</script>
@endpush
