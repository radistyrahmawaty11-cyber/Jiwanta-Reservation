@extends('layouts.pengunjung')

@section('title', 'Ulasan - Jiwanta')
@section('page-label', 'Ulasan')

@section('page-content')

<section class="px-5 pt-4">
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#0B3A22] via-[#0F4527] to-[#17653c] p-5 text-white shadow-[0_18px_40px_rgba(11,58,34,0.25)]">
        <div class="pointer-events-none absolute -right-10 -top-10 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>

        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-white/60">Rating Layanan</p>

        <div class="mt-3 flex items-end gap-3">
            <p class="text-5xl font-extrabold leading-none">{{ number_format($ringkasan['rata_rata'], 1, ',', '.') }}<span class="text-xl font-bold text-white/50">/5</span></p>
            <div class="pb-1">
                @include('pengunjung.ulasan._stars', ['rating' => (int) round($ringkasan['rata_rata']), 'size' => 'h-4 w-4'])
                <p class="mt-1 text-[11px] text-white/70">dari {{ $ringkasan['jumlah_ulasan'] }} ulasan pengunjung</p>
            </div>
        </div>
    </div>
</section>

<section class="px-5 pb-8 pt-4">
    <div class="rounded-3xl bg-white p-5 text-center shadow-[0_4px_18px_rgba(15,69,39,0.08)]">
        <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-[#FFF4D6] text-[#8B5E34]">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                <rect x="5" y="10" width="14" height="10" rx="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M8 10V7a4 4 0 118 0v3" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>

        <h2 class="mt-3 text-[18px] font-bold text-[#0B3A22]">Rating Belum Terbuka</h2>
        <p class="mx-auto mt-1 max-w-[280px] text-[12.5px] leading-relaxed text-slate-600">
            Halaman rating tersedia setelah transaksi Anda diverifikasi oleh petugas.
            Selesaikan pembayaran, lalu tunggu konfirmasi petugas.
        </p>

        <div class="mt-5 space-y-2 text-left">
            <div class="flex items-center gap-3 rounded-xl bg-[#F9F8FF] px-3.5 py-3">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[#0B3A22] text-[11px] font-bold text-white">1</span>
                <p class="text-[11.5px] font-medium text-slate-700">Selesaikan pembayaran reservasi.</p>
            </div>
            <div class="flex items-center gap-3 rounded-xl bg-[#F9F8FF] px-3.5 py-3">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[#0B3A22] text-[11px] font-bold text-white">2</span>
                <p class="text-[11.5px] font-medium text-slate-700">Tunggu petugas memverifikasi transaksi.</p>
            </div>
            <div class="flex items-center gap-3 rounded-xl bg-[#F9F8FF] px-3.5 py-3">
                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[#0B3A22] text-[11px] font-bold text-white">3</span>
                <p class="text-[11.5px] font-medium text-slate-700">Menu Ulasan akan muncul otomatis.</p>
            </div>
        </div>

        <a href="{{ route('tiket') }}" class="mt-5 flex w-full items-center justify-center rounded-full bg-[#0B3A22] px-5 py-3 text-[13px] font-bold text-white transition hover:bg-[#0F4527] active:scale-[0.99]">
            Lihat Tiket Saya
        </a>
        <a href="{{ route('dashboard') }}" class="mt-2 block py-2 text-[12px] font-medium text-slate-500 transition hover:text-[#0B3A22]">
            Kembali ke Beranda
        </a>
    </div>
</section>

@endsection
