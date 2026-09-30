@extends('layouts.dashboard')

@section('title', 'Transaksi - Jiwanta')

@section('content')

<div class="min-h-screen w-full bg-[#eceefa] font-sans text-[#0b2e22]">
    <div class="relative mx-auto min-h-screen w-full max-w-[420px] bg-[#f8f8ff] pb-28">

        {{-- ========== HEADER ========== --}}
        <header class="flex items-center justify-between px-5 pt-5 pb-3 rounded-b-2xl bg-[#cdeedb]">
            <h1 class="text-lg font-bold tracking-tight text-[#0b2e22]">Jiwanta</h1>
            <div class="flex items-center gap-3">
                <span class="text-xs font-medium text-[#0b2e22]">Transaksi</span>
                <img src="{{ asset('images/profil.jpg') }}" alt="Profil"
                     class="h-9 w-9 rounded-full bg-[#c9d3c4] object-cover">
            </div>
        </header>

        <main class="space-y-4 px-4 pt-1">

            {{-- ========== STEPPER ========== --}}
            <section class="rounded-2xl bg-white px-5 pt-4 pb-3 shadow-sm">
                <div class="flex items-center">
                    {{-- Step 1 --}}
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#cdeedb] text-[#0b2e22]">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="mx-2 h-[2px] flex-1 bg-[#0b2e22]"></div>
                    {{-- Step 2 --}}
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#0b2e22] text-sm font-bold text-white">2</div>
                    <div class="mx-2 h-[2px] flex-1 bg-[#d9dcf3]"></div>
                    {{-- Step 3 --}}
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#dfe2f8] text-sm font-semibold text-[#0b2e22]">3</div>
                </div>
                <div class="mt-2 flex items-start justify-between text-center">
                    <div class="w-20">
                        <p class="text-[11px] font-bold leading-tight text-[#0b2e22]">1. Reservasi</p>
                        <p class="text-[9px] text-gray-400">Selesai</p>
                    </div>
                    <div class="w-20">
                        <p class="text-[11px] font-bold leading-tight text-[#0b2e22]">2. Checkout</p>
                        <p class="text-[9px] font-semibold text-[#8a4b0f]">Aktif</p>
                    </div>
                    <div class="w-20">
                        <p class="text-[11px] font-bold leading-tight text-gray-500">3. E-Ticket</p>
                        <p class="text-[9px] text-gray-400">Selanjutnya</p>
                    </div>
                </div>
            </section>

            {{-- ========== TIMER ========== --}}
            <section class="flex items-center justify-between rounded-2xl bg-[#ffd7a8] px-4 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-[#7a4a1a] text-white">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase leading-tight tracking-wide text-[#8a4b0f]">Sisa Waktu Pembayaran</p>
                        <p id="countdown" class="text-2xl font-extrabold leading-tight text-[#0b2e22]">01:59:44</p>
                    </div>
                </div>
                <span class="rounded-full bg-[#fff1dd] px-3 py-1 text-[10px] font-bold text-[#7a4a1a]">Batas 2 Jam</span>
            </section>

            {{-- ========== DETAIL TIKET ========== --}}
            <section class="rounded-2xl bg-white p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/kolam jiwanta.jpg') }}" alt="Kolam Jiwanta"
                         class="h-14 w-14 shrink-0 rounded-lg bg-[#8fa89a] object-cover">
                    <div class="min-w-0">
                        <span class="inline-block rounded-md bg-[#cdeedb] px-2 py-0.5 text-[10px] font-semibold text-[#0b4a35]">Tiket Masuk &amp; Kolam</span>
                        <h2 class="text-lg font-bold leading-tight text-[#0b2e22]">Tiket Renang Premier</h2>
                        <p class="mt-0.5 flex items-center gap-1 text-xs text-gray-500">
                            <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                            </svg>
                            2 Pengunjung Dewasa
                        </p>
                    </div>
                </div>

                <div class="mt-4 space-y-2 rounded-xl bg-[#eef0ff] p-3">
                    <div class="flex items-center gap-2">
                        <svg class="h-4 w-4 shrink-0 text-[#0b2e22]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-xs font-bold text-[#0b2e22]">Minggu, 20 September 2026</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-[#0b2e22]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span class="text-xs leading-snug text-gray-600">Jiwanta Ciwidey Resort, Jl. Raya Ciwidey - Patengan KM 11</span>
                    </div>
                </div>

                <div class="mt-4 space-y-1.5 text-xs text-gray-600">
                    <div class="flex justify-between">
                        <span>Harga Satuan (x2)</span>
                        <span>Rp 75.000 x 2</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Subtotal Tiket</span>
                        <span>Rp 150.000</span>
                    </div>
                </div>

                <div class="my-3 h-px bg-[#dfe2f8]"></div>

                <div class="flex items-end justify-between">
                    <div class="max-w-[45%]">
                        <p class="text-xs font-semibold text-[#0b2e22]">Total Jumlah Transfer</p>
                        <p class="mt-1 text-[10px] leading-relaxed text-gray-400">Harus transfer persis hingga digit terakhir</p>
                    </div>
                    <div class="text-right leading-none text-[#0b2e22]">
                        <p class="text-3xl font-extrabold">Rp</p>
                        <p class="text-[32px] font-extrabold tracking-tight">150.000</p>
                    </div>
                </div>
            </section>

            {{-- ========== DATA PEMESAN ========== --}}
            <section class="rounded-2xl bg-white p-4 shadow-sm">
                <div class="mb-3 flex items-center justify-between">
                    <h3 class="flex items-center gap-2 text-lg font-bold text-[#0b2e22]">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Data Pemesan
                    </h3>
                    <span class="rounded-full bg-[#cdeedb] px-2.5 py-0.5 text-[10px] font-semibold text-[#0b4a35]">Terdaftar</span>
                </div>

                <div class="space-y-2.5">
                    <div class="rounded-xl bg-[#eef0ff] px-3 py-2">
                        <p class="text-[10px] font-semibold text-gray-500">Nama Lengkap</p>
                        <p class="text-sm font-semibold text-[#0b2e22]">Ahmad Fadillah</p>
                    </div>
                    <div class="rounded-xl bg-[#eef0ff] px-3 py-2">
                        <p class="text-[10px] font-semibold text-gray-500">No. WhatsApp</p>
                        <p class="text-sm font-semibold text-[#0b2e22]">0812-3456-7890</p>
                    </div>
                    <div class="rounded-xl bg-[#eef0ff] px-3 py-2">
                        <p class="text-[10px] font-semibold text-gray-500">Email Konfirmasi</p>
                        <p class="text-sm font-semibold text-[#0b2e22]">ahmad.fadillah@example.com</p>
                    </div>
                </div>
            </section>

            {{-- ========== TRANSFER BANK MANUAL ========== --}}
            <section class="rounded-2xl bg-white p-4 shadow-sm">
                <h3 class="flex items-center gap-2 text-lg font-bold text-[#0b2e22]">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10l9-6 9 6M5 10v8m4-8v8m6-8v8m4-8v8M3 21h18"/>
                    </svg>
                    Transfer Bank Manual
                </h3>
                <p class="mt-0.5 mb-3 text-xs text-gray-500">Pilih salah satu rekening resmi Jiwanta di bawah ini:</p>

                {{-- BCA --}}
                <div class="mb-3 rounded-2xl bg-[#eef0ff] p-3">
                    <div class="flex items-center justify-between">
                        <span class="rounded-md bg-[#dbe3fb] px-3 py-1 text-lg font-extrabold text-[#0b2e5a]">BCA</span>
                        <span class="text-[10px] text-gray-400">Bank Central Asia</span>
                    </div>
                    <div class="mt-3 flex items-center justify-between rounded-xl bg-white px-3 py-2.5">
                        <div>
                            <p class="text-[10px] font-semibold text-gray-500">Nomor Rekening</p>
                            <p class="text-xl font-bold tracking-[0.15em] text-[#0b2e22]">8405123499</p>
                        </div>
                        <button type="button" data-copy="8405123499"
                                class="copy-btn flex items-center gap-1.5 rounded-full bg-[#0b2e22] px-3 py-1.5 text-[11px] font-semibold text-white transition hover:bg-[#12442f] active:scale-95">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <span class="copy-label">Salin</span>
                        </button>
                    </div>
                    <p class="mt-2 text-xs text-gray-600">a.n Jiwanta Thermall Springs</p>
                </div>

                {{-- MANDIRI --}}
                <div class="rounded-2xl bg-[#eef0ff] p-3">
                    <div class="flex items-center justify-between">
                        <span class="rounded-md bg-[#e3e3f3] px-3 py-1 text-lg font-extrabold text-[#7a5a2e]">MANDIRI</span>
                        <span class="text-[10px] text-gray-400">Bank Mandiri</span>
                    </div>
                    <div class="mt-3 flex items-center justify-between rounded-xl bg-white px-3 py-2.5">
                        <div>
                            <p class="text-[10px] font-semibold text-gray-500">Nomor Rekening</p>
                            <p class="text-xl font-bold tracking-[0.15em] text-[#0b2e22]">1310098765432</p>
                        </div>
                        <button type="button" data-copy="1310098765432"
                                class="copy-btn flex items-center gap-1.5 rounded-full bg-[#0b2e22] px-3 py-1.5 text-[11px] font-semibold text-white transition hover:bg-[#12442f] active:scale-95">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            <span class="copy-label">Salin</span>
                        </button>
                    </div>
                    <p class="mt-2 text-xs text-gray-600">a.n Jiwanta Thermall Springs</p>
                </div>
            </section>

            {{-- ========== BUKTI PEMBAYARAN ========== --}}
            <section class="rounded-2xl bg-white p-4 shadow-sm">
                <div class="mb-3 flex items-start justify-between gap-3">
                    <h3 class="flex items-center gap-2 text-lg font-bold leading-tight text-[#0b2e22]">
                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Bukti<br>Pembayaran</span>
                    </h3>
                    <span class="flex items-center gap-1.5 rounded-2xl bg-[#ffd7a8] px-3 py-1.5 text-[10px] font-bold leading-tight text-[#7a4a1a]">
                        <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-[#7a4a1a]"></span>
                        Menunggu Verifikasi Admin
                    </span>
                </div>

                {{-- File terunggah --}}
                <div id="fileRow" class="flex items-center gap-3 rounded-xl bg-[#eef0ff] p-2.5">
                    <div class="relative shrink-0">
                        <img id="fileThumb" src="{{ asset('images/bukti transfer.jpg') }}" alt="Bukti transfer"
                             class="h-12 w-12 rounded-lg bg-gray-300 object-cover">
                        <span class="absolute -right-1 -bottom-1 flex h-4 w-4 items-center justify-center rounded-full bg-[#0b2e22] text-white ring-2 ring-[#eef0ff]">
                            <svg class="h-2.5 w-2.5" fill="none" stroke="currentColor" stroke-width="3.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                            </svg>
                        </span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p id="fileName" class="truncate text-xs font-bold text-[#0b2e22]">bukti_transfer_bca_ahmad.jpg</p>
                        <p id="fileMeta" class="text-[10px] text-gray-500">2.4 MB • 20 Sep 2026 14:30 WIB</p>
                        <p class="mt-0.5 flex items-center gap-1 text-[10px] font-semibold text-[#0b2e22]">
                            <svg class="h-3 w-3 text-[#0b7a4a]" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Struk Berhasil Diunggah
                        </p>
                    </div>
                    <button type="button" id="editFileBtn" aria-label="Ganti file"
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#dbe0fa] text-[#0b2e22] transition hover:bg-[#cdd4f6]">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 15.83a2 2 0 01-.879.513L7 17l.657-3.95A2 2 0 018.17 12.17L9 13z"/>
                        </svg>
                    </button>
                </div>

                {{-- Area upload --}}
                <label for="fileInput"
                       class="mt-3 flex cursor-pointer flex-col items-center rounded-xl bg-[#eef0ff] px-4 py-6 text-center transition hover:bg-[#e5e8fb]">
                    <input type="file" id="fileInput" accept="image/png,image/jpeg" class="hidden">
                    <div class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-[#cdeedb] text-[#0b2e22]">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <p class="text-xs font-bold text-[#0b2e22]">Upload Foto Struk / Screenshot Bukti Transfer</p>
                    <p class="mt-1 text-[10px] text-gray-500">Format JPG, PNG (Maksimal 5MB)</p>
                </label>
            </section>

            {{-- ========== TOMBOL KIRIM ========== --}}
            <div class="pt-1">
                <button type="button" id="submitBtn"
                        class="flex w-full items-center justify-center gap-2 rounded-full bg-[#0b2e22] py-4 text-sm font-bold text-white shadow-lg transition hover:bg-[#12442f] active:scale-[0.99] disabled:opacity-70">
                    <span id="submitLabel">Kirim &amp; Cek Status Tiket</span>
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </button>

                <a href="https://wa.me/6281234567890" target="_blank" rel="noopener"
                   class="mt-4 flex items-center justify-center gap-1.5 text-xs font-semibold text-[#7a4a1a] hover:underline">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/>
                    </svg>
                    Butuh Bantuan? Hubungi WhatsApp Admin
                </a>
            </div>
        </main>

        {{-- ========== BOTTOM NAVIGATION ========== --}}
        <nav class="fixed bottom-0 left-1/2 z-30 flex w-full max-w-[420px] -translate-x-1/2 items-center justify-around bg-white px-2 pt-3 pb-4 shadow-[0_-4px_16px_rgba(11,46,34,0.06)]">
            <a href="{{ route('dashboard') }}" class="flex flex-1 flex-col items-center gap-1 text-[#0b2e22]">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 20l5-9 3 5 2-3 5 7H3z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v3"/>
                </svg>
                <span class="text-[11px] font-semibold">Beranda</span>
            </a>
            <a href="#" class="flex flex-1 flex-col items-center gap-1 text-[#0b2e22]">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 11l9-7 9 7v9a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-9z"/>
                </svg>
                <span class="text-[11px] font-semibold">Reservasi</span>
            </a>
            <a href="{{ route('transaksi') }}" class="flex flex-1 flex-col items-center gap-1 text-[#0b2e22]">
                <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v17l2-1.2 2 1.2 2-1.2 2 1.2 2-1.2 2 1.2 2-1.2V4a2 2 0 00-2-2H6zm2 5h8v2H8V7zm0 4h8v2H8v-2zm0 4h5v2H8v-2z" clip-rule="evenodd"/>
                </svg>
                <span class="text-[11px] font-extrabold">Transaksi</span>
            </a>
            <a href="#" class="flex flex-1 flex-col items-center gap-1 text-[#0b2e22]">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                </svg>
                <span class="text-[11px] font-semibold">Tiket Saya</span>
            </a>
        </nav>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // === COUNTDOWN (mulai 01:59:44) ===
    (function () {
        let timeLeft = 1 * 3600 + 59 * 60 + 44;
        const el = document.getElementById('countdown');
        const pad = (n) => String(n).padStart(2, '0');

        function tick() {
            const h = Math.floor(timeLeft / 3600);
            const m = Math.floor((timeLeft % 3600) / 60);
            const s = timeLeft % 60;
            el.textContent = `${pad(h)}:${pad(m)}:${pad(s)}`;
            if (timeLeft > 0) timeLeft--;
        }
        tick();
        setInterval(tick, 1000);
    })();

    // === SALIN NOMOR REKENING ===
    document.querySelectorAll('.copy-btn').forEach((btn) => {
        btn.addEventListener('click', () => {
            const label = btn.querySelector('.copy-label');
            navigator.clipboard.writeText(btn.dataset.copy).then(() => {
                label.textContent = 'Tersalin';
                setTimeout(() => (label.textContent = 'Salin'), 2000);
            });
        });
    });

    // === UPLOAD BUKTI TRANSFER ===
    const fileInput = document.getElementById('fileInput');
    document.getElementById('editFileBtn').addEventListener('click', () => fileInput.click());

    fileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;

        if (!['image/jpeg', 'image/png'].includes(file.type)) {
            alert('Format file harus JPG atau PNG.');
            fileInput.value = '';
            return;
        }
        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran file maksimal 5MB.');
            fileInput.value = '';
            return;
        }

        const now = new Date();
        const tanggal = now.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
        const jam = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', hour12: false }).replace('.', ':');

        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileMeta').textContent =
            `${(file.size / 1024 / 1024).toFixed(1)} MB • ${tanggal} ${jam} WIB`;
        document.getElementById('fileThumb').src = URL.createObjectURL(file);
    });

    // === KIRIM ===
    document.getElementById('submitBtn').addEventListener('click', function () {
        const label = document.getElementById('submitLabel');
        const original = label.textContent;
        this.disabled = true;
        label.textContent = 'Mengirim...';

        // TODO: ganti dengan request ke backend (fetch/axios/form submit)
        setTimeout(() => {
            label.textContent = original;
            this.disabled = false;
            window.location.href = "{{ route('transaksi') }}";
        }, 1500);
    });
</script>
@endpush
