@extends('layouts.pengunjung')

@section('title', 'Reservasi - Jiwanta')
@section('page-label', 'Reservasi')
@section('frame-class') relative mx-auto min-h-screen w-full max-w-[390px] overflow-x-clip bg-[#F9F8FF] pb-[calc(env(safe-area-inset-bottom,0px)_+_192px)] shadow-2xl @endsection

@section('page-content')

@php
    // Tab awal: /reservasi (tiket) atau /reservasi?tab=cabin
    $tab = request('tab') === 'cabin' ? 'cabin' : 'tiket';
    $home = \Illuminate\Support\Facades\Route::has('dashboard') ? route('dashboard') : url('/');

    // ---------- Helper ikon ----------
    $paths = [
        'sauna' => 'M4 20c1.5 0 1.5-1 3-1s1.5 1 3 1 1.5-1 3-1 1.5 1 3 1 1.5-1 3-1M8 15V9M12 15V6M16 15V9M6 6c0-1 1-1 1-2M12 3c0-1 1-1 1-2',
        'home' => 'M3 11l9-8 9 8M5 10v10h14V10M10 20v-5h4v5',
        'tree' => 'M12 3l4 6h-2.5l3.5 5h-3l3 5H7l3-5H7l3.5-5H8l4-6zM11 19h2v3h-2z',
        'receipt' => 'M6 3h12v18l-3-2-3 2-3-2-3 2V3zM9 8h6M9 12h6',
        'ticket' => 'M4 7h16v3a2 2 0 000 4v3H4v-3a2 2 0 000-4V7zM14 7v10',
        'swim' => 'M3 19c1.5 0 1.5-1 3-1s1.5 1 3 1 1.5-1 3-1 1.5 1 3 1 1.5-1 3-1 1.5 1 3 1M3 15c1.5 0 1.5-1 3-1s1.5 1 3 1 1.5-1 3-1 1.5 1 3 1 1.5-1 3-1 1.5 1 3 1M15 6a1.5 1.5 0 100-3 1.5 1.5 0 000 3zM6 11l5-3 4 3',
        'leaf' => 'M5 19c0-8 5-13 14-14 0 9-5 14-14 14zM5 19c3-4 6-6 9-8',
        'calendar' => 'M8 3v3M16 3v3M4 9h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z',
        'calendarcheck' => 'M8 3v3M16 3v3M4 9h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1zM9 15l2 2 4-4',
        'clock' => 'M12 8v4l2 2M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'calc' => 'M6 3h12v18H6zM9 7h6M9 12h.01M12 12h.01M15 12h.01M9 16h.01M12 16h.01M15 16h.01',
        'minus' => 'M5 12h14',
        'plus' => 'M12 5v14M5 12h14',
        'users' => 'M17 20v-1a4 4 0 00-4-4H7a4 4 0 00-4 4v1M10 11a4 4 0 100-8 4 4 0 000 8zM21 20v-1a4 4 0 00-3-3.9M16 3.1a4 4 0 010 7.8',
        'refresh' => 'M4 4v6h6M20 20v-6h-6M20 9A8 8 0 006 6L4 10M4 15a8 8 0 0014 3l2-4',
        'bath' => 'M4 12h16v3a4 4 0 01-4 4H8a4 4 0 01-4-4v-3zM6 12V7a2 2 0 014 0',
        'mountain' => 'M3 19l6-10 4 6 3-4 5 8H3z',
        'info' => 'M12 8h.01M11 12h1v5h1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'login' => 'M14 4h5a1 1 0 011 1v14a1 1 0 01-1 1h-5M10 16l4-4-4-4M14 12H4',
        'star' => 'M12 3l2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9L12 3z',
        'arrow' => 'M5 12h14M13 6l6 6-6 6',
        'check' => 'M5 13l4 4L19 7',
        'chevron' => 'M6 9l6 6 6-6',
    ];
    $ic = fn (string $n, string $size = 'h-4 w-4', string $sw = '1.8') =>
        '<svg class="'.$size.'" fill="none" stroke="currentColor" stroke-width="'.$sw.'" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="'.($paths[$n] ?? '').'"/></svg>';

    // ---------- Data ----------
    $tabs = [['tiket', 'Tiket Renang', 'sauna'], ['cabin', 'Cabin Suite', 'home']];

    $nav = [
        ['Beranda', $home, false, 'tree'],
        ['Reservasi', url('/reservasi'), true, 'home'],
        ['Transaksi', '#', false, 'receipt'],
        ['Tiket Saya', '#', false, 'ticket'],
    ];

    // [key, nama, harga, ikon, warna ikon, badge]
    $cats = [
        ['classic', 'Tiket Classic', 60000, 'swim', 'bg-[#DDE6FB]', null],
        ['premier', 'Tiket Premier', 75000, 'leaf', 'bg-[#CDEFD5]', 'Paling Diminati'],
    ];

    $units = [
        ['key' => 'shorts', 'name' => 'Shorts', 'image' => asset('images/cabin-shorts.jpg'), 'badge' => 'Unit Favorit Keluarga',
         'capacity' => 'Kapasitas 4 orang', 'feature' => ['Private Jacuzzi', 'bath'], 'price' => 340000, 'label' => 'Harga Sewa',
         'per' => 'sewa', 'guests' => '4 Tamu Dewasa'],
        ['key' => 'suite', 'name' => 'Suite', 'image' => asset('images/cabin-suite.jpg'), 'badge' => null,
         'capacity' => 'Kapasitas 4-5 orang', 'feature' => ['Valley View', 'mountain'], 'price' => 1040000, 'label' => 'Harga per Malam',
         'per' => 'malam', 'guests' => '4-5 Tamu Dewasa'],
    ];

    // ---------- Tanggal bebas (hari ini s/d 365 hari ke depan) ----------
    $today = now('Asia/Jakarta')->startOfDay();
    try {
        $selected = request('tanggal') ? \Illuminate\Support\Carbon::parse(request('tanggal'), 'Asia/Jakarta')->startOfDay() : $today->copy();
    } catch (\Throwable $e) {
        $selected = $today->copy();
    }
    if ($selected->lt($today) || $selected->gt($today->copy()->addDays(365))) {
        $selected = $today->copy();
    }
    $dayShort = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
    $dayFull = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $monShort = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    // Judul kartu tanggal: ketuk untuk membuka kalender bebas
    $dateTitle = fn (string $t) => '<label class="relative flex cursor-pointer items-center gap-2 text-[14px] font-bold text-[#0B3A22]">'
        .$ic('calendar').' '.e($t).' '.$ic('chevron', 'h-3.5 w-3.5 text-slate-500')
        .'<input type="date" data-date-input aria-label="Pilih tanggal" class="absolute inset-0 h-full w-full cursor-pointer opacity-0"></label>';

    // 5 chip: 2 hari sebelum, tanggal terpilih (tengah), 2 hari sesudah
    $chips = function () use ($selected, $today, $dayShort, $dayFull, $monShort) {
        $out = '';
        foreach ([-2, -1, 0, 1, 2] as $off) {
            $d = $selected->copy()->addDays($off);
            $on = $off === 0;
            $past = $d->lt($today) || $d->gt($today->copy()->addDays(365));
            $cls = $past ? 'h-[58px] w-[46px] cursor-not-allowed bg-[#EEF0FB] text-slate-400'
                : ($on ? 'h-[72px] w-[60px] bg-[#0B3A22] text-white shadow-[0_8px_18px_rgba(11,58,34,0.35)]' : 'h-[58px] w-[46px] bg-[#DDE6FB] text-[#0B3A22] active:scale-95');
            $out .= '<button type="button" data-date-chip data-offset="'.$off.'"'.($past ? ' disabled' : '').' class="flex flex-col items-center justify-center rounded-xl transition-all duration-300 ease-out '.$cls.'">'
                .'<span data-l class="text-[9px] font-semibold leading-tight">'.($on ? $dayFull[$d->dayOfWeek] : $dayShort[$d->dayOfWeek]).'</span>'
                .'<span data-n class="text-[22px] font-bold leading-tight">'.$d->day.'</span>'
                .'<span data-m class="text-[9px] font-semibold leading-tight '.($on ? '' : 'hidden').'">'.$monShort[$d->month - 1].'</span></button>';
        }
        return $out;
    };

    $soft = 'shadow-[0_2px_10px_rgba(15,69,39,0.06)]';
@endphp

        <form id="reservasi-form" method="POST" action="{{ url('/reservasi/checkout') }}">
            @csrf
            <input type="hidden" name="tipe" value="{{ $tab }}">

            <main class="px-4 pt-1">

                {{-- ################ PANEL: TIKET RENANG ################ --}}
                <div data-panel="tiket" class="space-y-3 {{ $tab === 'tiket' ? '' : 'hidden' }}">

                    <section data-reveal class="pt-1">
                        <div class="flex items-end justify-between">
                            <h2 class="text-[17px] font-bold text-[#0B3A22]">Pilih Kategori Tiket</h2>
                            <span class="text-[9px] font-bold uppercase tracking-wider text-[#0B3A22]">Akses Pemandian</span>
                        </div>
                        <p class="mt-1 text-[12px] leading-snug text-slate-600">Nikmati kolam sulfur alami berkhasiat di kelilingi pepohonan<br>pinus berkabut.</p>
                    </section>

                    <div class="space-y-3.5 pt-1">
                        @foreach ($cats as [$key, $name, $price, $icon, $bg, $badge])
                            <div data-reveal class="relative">
                                @if ($badge)
                                    <span class="absolute -top-2 right-4 z-10 inline-flex items-center gap-1 rounded-t-lg bg-[#8B5E34] px-2.5 py-0.5 text-[9px] font-semibold text-white">{!! $ic('star', 'h-2.5 w-2.5') !!} {{ $badge }}</span>
                                @endif
                                <button type="button" data-cat="{{ $key }}" data-name="{{ $name }}" data-price="{{ $price }}"
                                        class="flex w-full items-center gap-3 rounded-2xl bg-white p-3.5 text-left {{ $soft }} transition duration-300 active:scale-[0.98]">
                                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full {{ $bg }} text-[#0B3A22]">{!! $ic($icon, 'h-5 w-5') !!}</span>
                                    <span class="min-w-0 flex-1">
                                        <b class="block text-[17px] leading-tight text-[#0B3A22]">{{ $name }}</b>
                                        <span class="mt-0.5 block"><b class="text-[17px] text-[#0B3A22]">Rp {{ number_format($price, 0, ',', '.') }}</b> <span class="text-[11px] text-slate-600">/ orang</span></span>
                                    </span>
                                    <span data-ind class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-[#DDE6FB] text-white transition duration-300">
                                        <span data-tick class="opacity-0 transition duration-300">{!! $ic('check', 'h-4 w-4', '3') !!}</span>
                                    </span>
                                </button>
                            </div>
                        @endforeach
                    </div>

                    {{-- Tanggal kunjungan --}}
                    <section data-reveal class="rounded-2xl bg-white p-4 {{ $soft }}">
                        <div class="flex items-center justify-between">
                            <h3 class="text-[14px] font-bold text-[#0B3A22]">{!! $dateTitle('Tanggal Kunjungan') !!}</h3>
                            <span data-date-badge class="rounded-full bg-[#FBD9B0] px-2.5 py-1 text-[9px] font-bold text-[#8B5E34] transition">Akhir Pekan</span>
                        </div>
                        <div data-chips class="mt-3 flex items-center justify-between transition-opacity duration-150">{!! $chips() !!}</div>
                        <p class="mt-3 flex items-start gap-2 text-[11px] leading-snug text-slate-600">{!! $ic('clock', 'mt-0.5 h-3.5 w-3.5 shrink-0') !!} <span>Jam Buka: 07:00 - 20:00 WIB • Akses Berlaku<br>Sepanjang Hari</span></p>
                    </section>

                    {{-- Jumlah pengunjung --}}
                    <section data-reveal class="rounded-2xl bg-white p-4 {{ $soft }}">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-[14px] font-bold text-[#0B3A22]">Jumlah Pengunjung</h3>
                                <p class="mt-0.5 text-[11px] text-slate-600">Usia 3 tahun ke atas wajib tiket</p>
                            </div>
                            <div class="flex items-center gap-3 rounded-full bg-[#DDE6FB] p-1.5">
                                <button type="button" data-step="-1" aria-label="Kurangi" class="flex h-9 w-9 items-center justify-center rounded-full bg-white text-[#0B3A22] shadow-sm transition hover:bg-[#f3f4fd] active:scale-90">{!! $ic('minus', 'h-4 w-4', '2.4') !!}</button>
                                <span data-qty class="w-4 text-center text-[18px] font-bold text-[#0B3A22]">2</span>
                                <button type="button" data-step="1" aria-label="Tambah" class="flex h-9 w-9 items-center justify-center rounded-full bg-[#0B3A22] text-white shadow-sm transition hover:bg-[#124c2f] active:scale-90">{!! $ic('plus', 'h-4 w-4', '2.4') !!}</button>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center justify-between rounded-xl bg-[#E8ECFB] px-3 py-3">
                            <span class="flex items-center gap-2 text-[12px] text-slate-700">{!! $ic('calc') !!} <span data-line>2 x Rp 75.000</span></span>
                            <b data-sub class="text-[18px] text-[#0B3A22]">Rp 150.000</b>
                        </div>
                    </section>
                </div>

                {{-- ################ PANEL: CABIN SUITE ################ --}}
                <div data-panel="cabin" class="space-y-3 {{ $tab === 'cabin' ? '' : 'hidden' }}">

                    {{-- Tanggal check-in --}}
                    <section data-reveal class="mt-1 rounded-2xl bg-white p-4 {{ $soft }}">
                        <div class="flex items-center justify-between">
                            <h3 class="text-[14px] font-bold text-[#0B3A22]">{!! $dateTitle('Tanggal Check-in') !!}</h3>
                            <span data-date-badge class="rounded-full bg-[#FBD9B0] px-2.5 py-1 text-[9px] font-bold text-[#8B5E34] transition">Akhir Pekan</span>
                        </div>
                        <div data-chips class="mt-3 flex items-center justify-between transition-opacity duration-150">{!! $chips() !!}</div>
                        <p class="mt-3 flex items-start gap-2 text-[11px] leading-snug text-slate-600">{!! $ic('clock', 'mt-0.5 h-3.5 w-3.5 shrink-0') !!} <span>Check-in mulai 14.00 WIB • Check-out maks<br>12.00 WIB</span></p>
                    </section>

                    {{-- Periode menginap --}}
                    <section data-reveal class="rounded-2xl bg-white p-4 {{ $soft }}">
                        <div class="flex items-center justify-between">
                            <h3 class="flex items-center gap-2 text-[14px] font-bold text-[#0B3A22]">{!! $ic('calendarcheck') !!} Periode Menginap</h3>
                            <button type="button" data-nights title="Ketuk untuk mengubah durasi" class="rounded-full bg-[#CDEFD5] px-3 py-1 text-[10px] font-bold text-[#0B3A22] transition hover:bg-[#bfe8c9] active:scale-95">Durasi: <span data-nights-text>2 Malam</span></button>
                        </div>
                        <div class="mt-3 grid grid-cols-2 gap-2.5">
                            <div class="rounded-xl bg-[#E8ECFB] p-3">
                                <p class="text-[10px] font-medium text-slate-700">Check-in</p>
                                <p data-in class="mt-1 text-[13px] font-bold leading-tight text-[#0B3A22]">Sabtu, 20 Sep 2026</p>
                                <p class="text-[11px] text-slate-600">Mulai 14.00 WIB</p>
                            </div>
                            <div class="rounded-xl bg-[#E8ECFB] p-3">
                                <p class="text-[10px] font-medium text-slate-700">Check-out</p>
                                <p data-out class="mt-1 text-[13px] font-bold leading-tight text-[#0B3A22]">Senin, 22 Sep 2026</p>
                                <p class="text-[11px] text-slate-600">Maks 12.00 WIB</p>
                            </div>
                        </div>
                        <div class="mt-2.5 flex items-center justify-between rounded-xl bg-[#E8ECFB] px-3 py-3">
                            <span class="flex items-center gap-2 text-[12px] text-slate-700">{!! $ic('users') !!} Kapasitas Tamu Terpilih</span>
                            <b data-guests class="text-[13px] text-[#0B3A22]">4 Tamu Dewasa</b>
                        </div>
                    </section>

                    {{-- Ketersediaan --}}
                    <section data-reveal class="pt-3">
                        <div class="flex items-start justify-between">
                            <div>
                                <h2 class="text-[17px] font-bold text-[#0B3A22]">Cek Ketersediaan Real-time</h2>
                                <p class="mt-0.5 text-[11px] text-slate-600">Unit Cabin Suite Ciwidey Highland</p>
                            </div>
                            <button type="button" data-refresh aria-label="Segarkan ketersediaan" class="mt-1 text-[#0B3A22] transition active:scale-90"><span data-refresh-icon class="block">{!! $ic('refresh', 'h-5 w-5', '2.2') !!}</span></button>
                        </div>
                    </section>

                    @foreach ($units as $u)
                        <article data-reveal data-unit="{{ $u['key'] }}" data-name="{{ $u['name'] }}" data-price="{{ $u['price'] }}" data-per="{{ $u['per'] }}" data-guests="{{ $u['guests'] }}"
                                 class="rounded-2xl bg-white p-3 {{ $soft }} ring-2 ring-transparent transition duration-300">
                            <div class="relative h-[158px] overflow-hidden rounded-xl bg-slate-300">
                                <img src="{{ $u['image'] }}" alt="Cabin {{ $u['name'] }}" class="h-full w-full object-cover">
                                <span data-status class="absolute right-2.5 top-2.5 inline-flex items-center gap-1.5 rounded-full bg-[#D5F5DC] px-2.5 py-1 text-[10px] font-semibold text-[#0B3A22] transition">
                                    <span data-dot class="h-1.5 w-1.5 rounded-full bg-[#0B3A22]"></span><span data-status-text>Tersedia</span>
                                </span>
                                @if ($u['badge'])
                                    <span class="absolute bottom-2.5 left-2.5 rounded-full bg-white/90 px-2.5 py-1 text-[10px] font-semibold text-slate-800">{{ $u['badge'] }}</span>
                                @endif
                            </div>
                            <h3 class="mt-3 text-[20px] font-bold leading-tight text-[#0B3A22]">{{ $u['name'] }}</h3>
                            <p class="mt-1 flex items-center gap-2 text-[12px] text-slate-600">
                                {!! $ic('users', 'h-3.5 w-3.5') !!} {{ $u['capacity'] }}
                                <span class="h-1 w-1 rounded-full bg-slate-500"></span>
                                {!! $ic($u['feature'][1], 'h-3.5 w-3.5') !!} {{ $u['feature'][0] }}
                            </p>
                            <div class="mt-3 flex items-end justify-between">
                                <div>
                                    <p class="text-[10px] text-slate-600">{{ $u['label'] }}</p>
                                    <p class="text-[20px] font-bold leading-tight text-[#0B3A22]">Rp {{ number_format($u['price'], 0, ',', '.') }}</p>
                                </div>
                                <button type="button" data-pick class="flex items-center gap-1.5 rounded-full bg-[#0B3A22] px-5 py-2.5 text-[12px] font-bold text-white transition duration-300 hover:bg-[#124c2f] active:scale-95">
                                    <span data-pick-icon class="hidden">{!! $ic('check', 'h-3.5 w-3.5', '3') !!}</span><span data-pick-text>Pilih Unit</span>
                                </button>
                            </div>
                        </article>
                    @endforeach

                    {{-- Kebijakan --}}
                    <section data-reveal class="rounded-2xl bg-[#E4E8FB] p-4">
                        <h3 class="flex items-center gap-2 text-[13px] font-bold text-[#0B3A22]">{!! $ic('info') !!} Kebijakan Menginap &amp; Cabin</h3>
                        <p class="mt-2.5 flex items-start gap-2.5 text-[12px] leading-snug text-slate-700">{!! $ic('login', 'mt-0.5 h-3.5 w-3.5 shrink-0') !!} <span>Waktu Check-in: <b class="text-[#0B3A22]">14.00 WIB</b> • Check-out: <b class="text-[#0B3A22]">12.00 WIB</b></span></p>
                        <p class="mt-2 flex items-start gap-2.5 text-[12px] leading-snug text-slate-700">{!! $ic('leaf', 'mt-0.5 h-3.5 w-3.5 shrink-0') !!} <span>Kawasan konservasi hening: ketenangan malam mulai pukul 22.00 WIB</span></p>
                    </section>
                </div>
            </main>

            {{-- ========== CHECKOUT BAR ========== --}}
            <div class="fixed bottom-[calc(env(safe-area-inset-bottom,0px)_+_78px)] left-1/2 z-40 w-full max-w-[390px] -translate-x-1/2 px-4">
                <div class="flex items-center justify-between gap-3 rounded-2xl bg-white p-3.5 shadow-[0_8px_28px_rgba(15,69,39,0.16)]">
                    <div class="min-w-0">
                        <p class="text-[9px] font-bold uppercase leading-tight tracking-wide text-slate-600">Total<br>Pembayaran</p>
                        <p data-total class="whitespace-nowrap text-[26px] font-bold leading-tight text-[#0B3A22]">Rp 150.000</p>
                        <p data-total-sub class="truncate text-[10px] text-slate-600">2× Tiket Premier • 20 Sep</p>
                    </div>
                    <button type="submit" id="checkout-btn" class="flex shrink-0 cursor-pointer items-center gap-2 rounded-full bg-[#0B3A22] px-5 py-4 text-[13px] font-bold text-white shadow-lg transition hover:bg-[#124c2f] active:scale-95 disabled:cursor-not-allowed disabled:opacity-40">
                    <span data-btn-label>Checkout</span> {!! $ic('arrow', 'h-4 w-4', '2.2') !!}
                    </button>
                </div>
            </div>
        </form>

@endsection
@section('subheader')
            <div class="px-4 pb-3">
                <div class="flex rounded-full bg-[#DDE6FB] p-1" role="tablist" aria-label="Jenis reservasi">
                    @foreach ($tabs as [$key, $label, $icon])
                        <button type="button" role="tab" data-tab-btn="{{ $key }}" aria-selected="{{ $tab === $key ? 'true' : 'false' }}"
                                class="flex flex-1 items-center justify-center gap-2 rounded-full py-3 text-[13px] font-semibold transition duration-300 active:scale-95 {{ $tab === $key ? 'bg-[#0B3A22] text-white shadow-md' : 'text-[#0B3A22] hover:bg-white/50' }}">
                            {!! $ic($icon) !!} {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const LIVE_DEMO = true; // ganti dengan polling / Laravel Echo untuk ketersediaan asli
        const $ = (s, r = document) => r.querySelector(s);
        const $$ = (s, r = document) => [...r.querySelectorAll(s)];
        const rp = (n) => 'Rp ' + Math.round(n).toLocaleString('id-ID');

        // ---------- Tanggal bebas ----------
        const dayShort = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
        const dayFull = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        const monShort = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const parseISO = (t) => { const [y, m, d] = t.split('-').map(Number); return new Date(y, m - 1, d); };
        const iso = (dt) => `${dt.getFullYear()}-${String(dt.getMonth() + 1).padStart(2, '0')}-${String(dt.getDate()).padStart(2, '0')}`;
        const addDays = (dt, n) => new Date(dt.getFullYear(), dt.getMonth(), dt.getDate() + n);
        const shortDate = (dt) => `${dt.getDate()} ${monShort[dt.getMonth()]}`;
        const fmtDate = (dt) => `${dayFull[dt.getDay()]}, ${dt.getDate()} ${monShort[dt.getMonth()]} ${dt.getFullYear()}`;
        const TODAY = parseISO(@json($today->toDateString()));
        const MAX_DATE = addDays(TODAY, 365);
        const inRange = (dt) => dt >= TODAY && dt <= MAX_DATE;

        const state = { tab: @json($tab), cat: 'premier', qty: 2, date: parseISO(@json($selected->toDateString())), nights: 2, unit: null };
        const MAX_NIGHTS = 5, MAX_QTY = 20;

        // ---------- Util ----------
        const animate = (el, to, dur = 700) => {
            if (!el) return;
            const from = Number(el.dataset.val ?? 0);
            el.dataset.val = to;
            if (reduce) { el.textContent = rp(to); return; }
            const t0 = performance.now();
            const tick = (now) => {
                const p = Math.min((now - t0) / dur, 1);
                el.textContent = rp(from + (to - from) * (1 - Math.pow(1 - p, 3)));
                if (p < 1) requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
        };
        const field = (name, value) => {
            const form = $('#reservasi-form');
            let input = $(`input[name="${name}"]`, form);
            if (!input) { input = document.createElement('input'); input.type = 'hidden'; input.name = name; form.appendChild(input); }
            input.value = value;
        };
        const toggleAll = (el, list, on) => list.forEach((c) => el.classList.toggle(c, on));

        // ---------- Muncul bertahap saat panel tampil ----------
        const hiddenCls = ['opacity-0', 'translate-y-8'];
        const playIn = (panel) => {
            if (reduce) return;
            const els = $$('[data-reveal]', panel);
            els.forEach((el, i) => {
                el.style.transitionDelay = '0ms';
                el.classList.remove('transition', 'duration-700', 'ease-out');
                el.classList.add(...hiddenCls);
            });
            void panel.offsetHeight;
            requestAnimationFrame(() => els.forEach((el, i) => {
                el.classList.add('transition', 'duration-700', 'ease-out');
                el.style.transitionDelay = (i * 70) + 'ms';
                el.classList.remove(...hiddenCls);
            }));
        };

        // ---------- Tanggal (dipakai bersama tab tiket & cabin) ----------
        const SEL = ['h-[72px]', 'w-[60px]', 'bg-[#0B3A22]', 'text-white', 'shadow-[0_8px_18px_rgba(11,58,34,0.35)]'];
        const UN = ['h-[58px]', 'w-[46px]', 'bg-[#DDE6FB]', 'text-[#0B3A22]', 'active:scale-95'];
        const PAST = ['h-[58px]', 'w-[46px]', 'bg-[#EEF0FB]', 'text-slate-400', 'cursor-not-allowed'];
        const ALL = [...new Set([...SEL, ...UN, ...PAST])];
        const WK = ['bg-[#FBD9B0]', 'text-[#8B5E34]'];
        const WD = ['bg-[#CDEFD5]', 'text-[#0B3A22]'];

        const renderDates = () => {
            $$('[data-date-chip]').forEach((c) => {
                const off = Number(c.dataset.offset);
                const dt = addDays(state.date, off);
                const on = off === 0, ok = inRange(dt);
                c.classList.remove(...ALL);
                c.classList.add(...(on ? SEL : ok ? UN : PAST));
                c.disabled = !ok;
                $('[data-l]', c).textContent = on ? dayFull[dt.getDay()] : dayShort[dt.getDay()];
                $('[data-n]', c).textContent = dt.getDate();
                const m = $('[data-m]', c);
                m.textContent = monShort[dt.getMonth()];
                m.classList.toggle('hidden', !on);
                c.setAttribute('aria-pressed', on);
            });
            const weekend = [0, 6].includes(state.date.getDay());
            $$('[data-date-badge]').forEach((b) => {
                b.textContent = weekend ? 'Akhir Pekan' : 'Hari Kerja';
                b.classList.remove(...WK, ...WD);
                b.classList.add(...(weekend ? WK : WD));
            });
            $$('[data-date-input]').forEach((i) => { i.min = iso(TODAY); i.max = iso(MAX_DATE); i.value = iso(state.date); });
            field('tanggal', iso(state.date));
        };

        // ---------- Tab tiket ----------
        const cats = $$('[data-cat]');
        const ON = ['shadow-[0_10px_26px_rgba(15,69,39,0.18)]'];
        const OFF = ['shadow-[0_2px_10px_rgba(15,69,39,0.06)]'];
        const currentCat = () => cats.find((b) => b.dataset.cat === state.cat);

        const renderTiket = () => {
            const cur = currentCat(), price = Number(cur.dataset.price);
            cats.forEach((b) => {
                const on = b === cur;
                b.classList.remove(...(on ? OFF : ON));
                b.classList.add(...(on ? ON : OFF));
                const ind = $('[data-ind]', b);
                ind.classList.toggle('bg-[#0B3A22]', on);
                ind.classList.toggle('bg-[#DDE6FB]', !on);
                $('[data-tick]', b).classList.toggle('opacity-0', !on);
                b.setAttribute('aria-pressed', on);
            });
            $('[data-qty]').textContent = state.qty;
            $('[data-line]').textContent = `${state.qty} x ${rp(price)}`;
            animate($('[data-sub]'), price * state.qty);
            field('kategori', state.cat);
            field('jumlah', state.qty);
        };

        // ---------- Tab cabin ----------
        const units = $$('[data-unit]');
        const currentUnit = () => units.find((u) => u.dataset.unit === state.unit);
        const ringOn = ['ring-[#0B3A22]', 'shadow-[0_10px_26px_rgba(15,69,39,0.16)]'];

        const renderCabin = () => {
            $('[data-in]').textContent = fmtDate(state.date);
            $('[data-out]').textContent = fmtDate(addDays(state.date, state.nights));
            $('[data-nights-text]').textContent = state.nights + ' Malam';
            const chosen = currentUnit();

            units.forEach((u) => {
                const on = u === chosen;
                toggleAll(u, ringOn, on);
                u.classList.toggle('ring-transparent', !on);
                const btn = $('[data-pick]', u);
                btn.classList.toggle('bg-[#0B3A22]', !on);
                btn.classList.toggle('text-white', !on);
                btn.classList.toggle('bg-[#CDEFD5]', on);
                btn.classList.toggle('text-[#0B3A22]', on);
                $('[data-pick-icon]', u).classList.toggle('hidden', !on);
                $('[data-pick-text]', u).textContent = on ? 'Terpilih' : 'Pilih Unit';
            });
            $('[data-guests]').textContent = chosen ? chosen.dataset.guests : '4 Tamu Dewasa';
            field('unit', state.unit ?? '');
            field('malam', state.nights);
        };

        // ---------- Total & checkout ----------
        const renderTotal = () => {
            let total = 0, sub = '', enabled = true;
            if (state.tab === 'tiket') {
                const cur = currentCat();
                total = Number(cur.dataset.price) * state.qty;
                sub = `${state.qty}× ${cur.dataset.name} • ${shortDate(state.date)}`;
            } else {
                const chosen = currentUnit();
                if (!chosen) { sub = 'Pilih unit cabin terlebih dahulu'; enabled = false; }
                else {
                    const price = Number(chosen.dataset.price), perSewa = chosen.dataset.per === 'sewa';
                    total = perSewa ? price : price * state.nights;
                    sub = `${chosen.dataset.name} • ${perSewa ? 'Sewa 3 jam' : state.nights + ' Malam'} • ${shortDate(state.date)}`;
                }
            }
            animate($('[data-total]'), total);
            $('[data-total-sub]').textContent = sub;
            $('#checkout-btn').disabled = !enabled;
            field('total', total);
            field('tipe', state.tab);
        };

        const renderAll = () => { renderDates(); renderTiket(); renderCabin(); renderTotal(); };

        // ---------- Tab switch ----------
        const ACT = ['bg-[#0B3A22]', 'text-white', 'shadow-md'];
        const INACT = ['text-[#0B3A22]', 'hover:bg-white/50'];
        const showTab = (tab, animateIn = true) => {
            state.tab = tab;
            $$('[data-tab-btn]').forEach((b) => {
                const on = b.dataset.tabBtn === tab;
                b.classList.remove(...(on ? INACT : ACT));
                b.classList.add(...(on ? ACT : INACT));
                b.setAttribute('aria-selected', on);
            });
            $$('[data-panel]').forEach((p) => p.classList.toggle('hidden', p.dataset.panel !== tab));
            const url = new URL(window.location);
            tab === 'cabin' ? url.searchParams.set('tab', 'cabin') : url.searchParams.delete('tab');
            history.replaceState(null, '', url);
            renderTotal();
            if (animateIn) { window.scrollTo({ top: 0, behavior: reduce ? 'auto' : 'smooth' }); playIn($(`[data-panel="${tab}"]`)); }
        };
        $$('[data-tab-btn]').forEach((b) => b.addEventListener('click', () => { if (b.dataset.tabBtn !== state.tab) showTab(b.dataset.tabBtn); }));

        // ---------- Event ----------
        const setDate = (dt) => {
            const wraps = $$('[data-chips]');
            wraps.forEach((w) => w.classList.add('opacity-40'));
            state.date = dt;
            renderAll();
            setTimeout(() => wraps.forEach((w) => w.classList.remove('opacity-40')), 150);
        };
        // Ketuk chip di kiri/kanan -> tanggal bergeser, chip tengah selalu tanggal terpilih
        $$('[data-date-chip]').forEach((c) => c.addEventListener('click', () => {
            const off = Number(c.dataset.offset);
            if (!off || c.disabled) return;
            setDate(addDays(state.date, off));
        }));
        // Panah kiri/kanan pada keyboard
        $$('[data-chips]').forEach((w) => w.addEventListener('keydown', (e) => {
            if (e.key !== 'ArrowLeft' && e.key !== 'ArrowRight') return;
            const dt = addDays(state.date, e.key === 'ArrowRight' ? 1 : -1);
            if (inRange(dt)) { e.preventDefault(); setDate(dt); }
        }));
        // Kalender bebas (ketuk judul tanggal)
        $$('[data-date-input]').forEach((i) => {
            i.addEventListener('click', () => { try { i.showPicker(); } catch (e) { /* browser tanpa showPicker */ } });
            i.addEventListener('change', () => {
                if (!i.value) return;
                const dt = parseISO(i.value);
                inRange(dt) ? setDate(dt) : renderDates();
            });
        });
        cats.forEach((b) => b.addEventListener('click', () => { state.cat = b.dataset.cat; renderAll(); }));
        $$('[data-step]').forEach((b) => b.addEventListener('click', () => {
            state.qty = Math.max(1, Math.min(MAX_QTY, state.qty + Number(b.dataset.step)));
            renderAll();
        }));
        $('[data-nights]').addEventListener('click', () => { state.nights = state.nights >= MAX_NIGHTS ? 1 : state.nights + 1; renderAll(); });
        units.forEach((u) => $('[data-pick]', u).addEventListener('click', () => {
            state.unit = state.unit === u.dataset.unit ? null : u.dataset.unit;
            renderAll();
        }));

        // Ketersediaan real-time (simulasi)
        const setStatus = (u, low) => {
            const badge = $('[data-status]', u);
            $('[data-status-text]', u).textContent = low ? 'Tersisa 1 Unit' : 'Tersedia';
            toggleAll(badge, ['bg-[#FBD9B0]', 'text-[#6B4520]'], low);
            toggleAll(badge, ['bg-[#D5F5DC]', 'text-[#0B3A22]'], !low);
            $('[data-dot]', u).classList.toggle('bg-[#8B5E34]', low);
            $('[data-dot]', u).classList.toggle('bg-[#0B3A22]', !low);
        };
        const refresh = () => units.forEach((u) => setStatus(u, Math.random() < 0.3));
        $('[data-refresh]').addEventListener('click', () => {
            const icon = $('[data-refresh-icon]');
            if (icon.animate && !reduce) icon.animate([{ transform: 'rotate(0)' }, { transform: 'rotate(360deg)' }], { duration: 800, easing: 'ease-in-out' });
            setTimeout(refresh, 600);
        });
        if (LIVE_DEMO) setInterval(refresh, 20000);

        // Bayangan header saat scroll
        const head = $('#top-header');
        const onScroll = () => head.classList.toggle('shadow-[0_6px_16px_rgba(15,69,39,0.08)]', window.scrollY > 8);
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();

        // Status memproses saat submit
        $('#reservasi-form').addEventListener('submit', () => {
            const btn = $('#checkout-btn');
            $('[data-btn-label]', btn).textContent = 'Memproses...';
            btn.classList.add('pointer-events-none', 'opacity-80');
        });

        // ---------- Mulai ----------
        renderAll();
        showTab(state.tab, false);
        playIn($(`[data-panel="${state.tab}"]`));
    });
</script>
@endpush
