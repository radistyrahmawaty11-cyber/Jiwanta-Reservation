@extends('layouts.pengunjung')

@section('title', 'Tiket Saya - Jiwanta')
@section('page-label', 'Tiket Saya')
@section('frame-class') relative mx-auto min-h-screen w-full max-w-[390px] overflow-x-clip bg-[#F9F8FF] pb-32 shadow-2xl @endsection

@section('page-content')

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
        'clock'    => 'M12 8v4l2 2M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'star'     => 'M12 3l2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9L12 3z',
        'users'    => 'M17 20v-1a4 4 0 00-4-4H7a4 4 0 00-4 4v1M10 11a3.5 3.5 0 100-7 3.5 3.5 0 000 7zM21 20v-1a4 4 0 00-3-3.9M16 4.1a3.5 3.5 0 010 6.8',
        'scan'     => 'M4 8V5a1 1 0 011-1h3M16 4h3a1 1 0 011 1v3M20 16v3a1 1 0 01-1 1h-3M8 20H5a1 1 0 01-1-1v-3M9 9h2v2H9zM13 9h2v2h-2zM9 13h2v2H9zM14 14h1v1h-1z',
        'download' => 'M12 4v11M7 11l5 5 5-5M5 20h14',
        'image'    => 'M4 5h16v14H4zM4 16l4-4 3 3 4-5 5 6M9 9.5h.01',
    ];

    // Animasi masuk (dipakai oleh script)
    $reveal   = 'opacity-0 translate-y-8 transition duration-700 ease-out';
    $revealSm = 'opacity-0 translate-y-3 transition duration-500 ease-out';

    // ---------- Data tiket: pesanan sesi > transaksi terbaru > kosong ----------
    $order = $order ?? null;
    $transaksis = $transaksis ?? collect();
    $selected = $selected ?? null;
    $user = auth()->user();
    $monShort = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    $phoneOf = fn ($p) => $p && $p->nohp
        ? preg_replace('/(\d{4})(\d{4})(\d+)/', '$1-$2-$3', preg_replace('/\D/', '', $p->nohp))
        : '-';

    $ticket = null;
    $currentTrx = null;

    if ($order) {
        $isCabin = ($order['tipe'] ?? 'tiket') === 'cabin';
        $d = \Illuminate\Support\Carbon::parse($order['tanggal']);

        $ticket = [
            'code' => $order['kode'],
            'name' => $user?->nama_pengguna ?? ($order['pemesan'] ?? '-'),
            'phone' => $phoneOf($user),
            'date' => $d->day.' '.$monShort[$d->month - 1].' '.$d->year,
            'open_at' => $d->format('Y-m-d').'T08:00:00+07:00',
            'close_at' => $d->format('Y-m-d').'T18:00:00+07:00',
            'hours' => $isCabin ? 'Check-in 14.00 WIB' : '08.00 - 18.00 WIB',
            'guests' => (string) ($order['jumlah'] ?? $order['malam'] ?? 2),
            'guest_type' => $isCabin ? ($order['tamu'] ?? 'Tamu Dewasa') : 'Dewasa Reguler',
            'type' => $order['judul'],
            'includes' => $isCabin
                ? 'Termasuk: Akses Kolam Panas & Fasilitas Cabin'
                : 'Termasuk: Tiket '.$order['judul'],
            'total' => (int) $order['total'],
            'method' => ($order['status'] ?? '') === 'terkirim'
                ? 'Transfer Bank • Menunggu Verifikasi'
                : 'Belum Dikirim',
            'status' => ($order['status'] ?? '') === 'terkirim' ? 'terkirim' : 'pending',
            'pass' => $isCabin ? 'CABIN<br>SUITE' : 'TIKET<br>PASS',
            'verified' => false,
            'checked_in' => false,
            'date_raw' => $d->format('Y-m-d'),
        ];

        // Utamakan status asli dari database bila kode pesanan cocok.
        $matched = $transaksis->first(fn ($t) => $t->kode_transaksi === ($order['kode'] ?? null));

        if ($matched) {
            $currentTrx = $matched;
            $ticket['total'] = (int) ($matched->total_amount ?: $ticket['total']);
            $ticket['verified'] = (bool) $matched->verified_at;
            $ticket['checked_in'] = (bool) $matched->checked_in_at;

            if ($matched->payment_type) {
                $ticket['method'] = 'Midtrans '.strtoupper(str_replace('_', ' ', $matched->payment_type));
            } elseif (strcasecmp((string) $matched->metode_pembayaran, 'midtrans') === 0) {
                $ticket['method'] = 'Pembayaran Online';
            } elseif ($matched->metode_pembayaran) {
                $ticket['method'] = 'Transfer via '.$matched->metode_pembayaran;
            }

            if (in_array($matched->status, ['lunas', 'gagal'], true)) {
                $ticket['status'] = $matched->status;
            } else {
                $ticket['status'] = ($order['status'] ?? '') === 'terkirim' || $matched->bukti_transfer
                    ? 'terkirim'
                    : 'pending';
            }
        }
    } elseif ($selected || $transaksis->isNotEmpty()) {
        $t = $selected ?: $transaksis->first();
        $currentTrx = $t;
        $d = \Illuminate\Support\Carbon::parse($t->tanggal_transaksi);
        $detail = $t->detailtransaksis->first();
        $qty = max(1, (int) $t->detailtransaksis->sum('jumlah'));

        $isCabin = (bool) ($detail?->reservasi?->cabin);
        $type = $detail?->tiket?->nama_tiket
            ?? ($detail?->reservasi?->cabin ? 'Cabin '.$detail->reservasi->cabin->nama_cabin : 'Tiket Jiwanta');

        $ticket = [
            'code' => $t->kode_transaksi,
            'name' => $user?->nama_pengguna ?? '-',
            'phone' => $phoneOf($user),
            'date' => $d->day.' '.$monShort[$d->month - 1].' '.$d->year,
            'open_at' => $d->format('Y-m-d').'T08:00:00+07:00',
            'close_at' => $d->format('Y-m-d').'T18:00:00+07:00',
            'hours' => $isCabin ? 'Check-in 14.00 WIB' : '08.00 - 18.00 WIB',
            'guests' => (string) $qty,
            'guest_type' => $isCabin ? $qty.' Tamu Dewasa' : 'Dewasa Reguler',
            'type' => $type,
            'includes' => $isCabin
                ? 'Termasuk: Akses Kolam Panas & Fasilitas Cabin'
                : 'Termasuk: Tiket '.$type,
            'total' => (int) $t->total_amount,
            'method' => $t->payment_type
                ? 'Midtrans '.strtoupper(str_replace('_', ' ', $t->payment_type))
                : 'Transfer via '.$t->metode_pembayaran,
            'status' => in_array($t->status, ['lunas', 'gagal'], true) ? $t->status : 'pending',
            'pass' => $isCabin ? 'CABIN<br>SUITE' : 'TIKET<br>PASS',
            'verified' => (bool) $t->verified_at,
            'checked_in' => (bool) $t->checked_in_at,
            'date_raw' => $d->format('Y-m-d'),
        ];
    }

    $bookingItems = collect();

    if ($currentTrx) {
        $bookingItems = $currentTrx->detailtransaksis->map(function ($d) use ($monShort) {
            $res = $d->reservasi;
            $name = $d->tiket?->nama_tiket
                ?? ($res?->cabin
                    ? 'Cabin '.$res->cabin->nama_cabin
                    : ($res?->renang?->nama_kolam ?? 'Reservasi'));

            $note = '';

            if ($res) {
                $checkin = $res->tanggal_checkin ? \Illuminate\Support\Carbon::parse($res->tanggal_checkin) : null;
                $checkout = $res->tanggal_checkout ? \Illuminate\Support\Carbon::parse($res->tanggal_checkout) : null;
                $note = $checkin
                    ? 'Check-in '.$checkin->day.' '.$monShort[$checkin->month - 1].' '.$checkin->year
                    : '';
                if ($checkout) {
                    $note .= ' • Check-out '.$checkout->day.' '.$monShort[$checkout->month - 1].' '.$checkout->year;
                }
            }

            return [
                'name' => $name ?: 'Pesanan Jiwanta',
                'qty' => max(1, (int) $d->jumlah),
                'subtotal' => (int) $d->subtotal,
                'note' => $note,
            ];
        });
    } elseif ($ticket) {
        $bookingItems = collect([[
            'name' => $ticket['type'],
            'qty' => max(1, (int) preg_replace('/\D/', '', (string) $ticket['guests'])),
            'subtotal' => (int) $ticket['total'],
            'note' => $ticket['date'],
        ]]);
    }

    $visitDate = $ticket['date_raw'] ?? null;
    $isPastVisit = $visitDate !== null && $visitDate < now()->toDateString();
    $isCheckedIn = ! empty($ticket['checked_in']);
    $isSuccessDone = $ticket !== null
        && ($ticket['status'] ?? '') === 'lunas'
        && ($isCheckedIn || $isPastVisit);
    $isDone = $ticket !== null
        && (($ticket['status'] ?? '') === 'gagal' || $isSuccessDone);

    $statusLabel = match (true) {
        ($ticket['status'] ?? '') === 'lunas' && $isCheckedIn => 'LUNAS & SUDAH CHECK-IN',
        ($ticket['status'] ?? '') === 'lunas' && ! empty($ticket['verified']) => 'LUNAS & DIKONFIRMASI',
        ($ticket['status'] ?? '') === 'lunas' => 'LUNAS',
        ($ticket['status'] ?? '') === 'terkirim' => 'MENUNGGU VERIFIKASI',
        ($ticket['status'] ?? '') === 'gagal' => 'PEMBAYARAN GAGAL',
        default => 'MENUNGGU PEMBAYARAN',
    };
    $statusKey = $ticket['status'] ?? 'pending';
    $gatePill = match (true) {
        $statusKey === 'lunas' && $isCheckedIn => 'Sudah Check-in',
        $statusKey === 'lunas' && ! empty($ticket['verified']) => 'Terkonfirmasi',
        $statusKey === 'lunas' => 'Menunggu Konfirmasi Petugas',
        $statusKey === 'terkirim' => 'Menunggu Verifikasi',
        $statusKey === 'gagal' => 'Pembayaran Gagal',
        default => 'Menunggu Pembayaran',
    };
    $statusColor = match ($statusKey) {
        'lunas' => 'text-[#B5F0BE]',
        'gagal' => 'text-[#F7B0B0]',
        default => 'text-[#FDD9B0]',
    };
    $statusIcon = $statusKey === 'lunas' ? $p['check'] : $p['clock'];
    $gateOpen = $ticket['open_at'] ?? '';
    $gateClose = $ticket['close_at'] ?? '';
    $gateForce = $statusKey === 'lunas';

    // QR & barcode gate hanya diterbitkan setelah pembayaran lunas/terverifikasi.
    $qrReady = $statusKey === 'lunas';

    // ---------- Barcode Code 128 (SVG asli, bisa discan petugas) ----------
    $bookingCode = $ticket['code'] ?? 'JW-00000000-000';
    $barcodePayload = \App\Support\Barcode::payload($bookingCode);
    $barcodeSvg = \App\Support\Barcode::code128($barcodePayload);

    $shadow = 'shadow-[0_4px_18px_rgba(15,69,39,0.08)]';
@endphp

        <main class="px-4 pt-2">

        @if ($ticket)

            {{-- ========== STATUS RESERVASI ========== --}}
            <section data-reveal class="{{ $reveal }} flex items-center justify-between rounded-2xl bg-[#0B3A22] px-4 py-3.5 text-white">
                <div class="flex items-center gap-3">
                    <span id="status-icon" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/15 {!! $statusColor !!}">{!! $ic($statusIcon, 'h-6 w-6', '2') !!}</span>
                    <div>
                        <p class="text-[9px] font-semibold uppercase tracking-wider text-white/70">Status Reservasi</p>
                        <p id="status-label" class="text-[17px] font-bold leading-tight">{{ $statusLabel }}</p>
                    </div>
                </div>
                <span id="gate-pill" class="ml-2 shrink-0 whitespace-nowrap rounded-full bg-white/15 px-2.5 py-1.5 text-[10px] font-semibold text-white transition-colors duration-500">{{ $gatePill }}</span>
            </section>

            {{-- ========== KARTU TIKET ========== --}}
            <section data-reveal class="{{ $reveal }} delay-200 relative mt-4 overflow-hidden rounded-2xl bg-white shadow-[0_8px_28px_rgba(15,69,39,0.14)] {{ $isDone ? 'grayscale opacity-60' : '' }}">

                {{-- Header kartu --}}
                <div class="flex items-center justify-between bg-[#1B4A32] px-4 py-4 text-white">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/15">{!! $ic($p['tree'], 'h-5 w-5', '1.6') !!}</span>
                        <div>
                            <h2 class="text-[17px] font-bold leading-tight">Jiwanta Ciwidey</h2>
                            <p class="mt-0.5 text-[10px] leading-tight text-white/75">Eco Luxury Hot Springs &amp;<br>Forest</p>
                        </div>
                    </div>
                    <span class="rounded-full bg-[#FDD9B0] px-3.5 py-2 text-center text-[9px] font-extrabold leading-tight tracking-wide text-[#8B5E34]">{!! $ticket['pass'] !!}</span>
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

                {{-- Daftar booking --}}
                <div class="px-4 pt-5">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Daftar Booking</p>
                        <span class="rounded-full bg-[#EEF0FB] px-2.5 py-1 text-[9px] font-bold text-[#0B3A22]">{{ $bookingItems->count() }} Item</span>
                    </div>
                    <div class="mt-2.5 space-y-2">
                        @forelse ($bookingItems as $item)
                            <div class="flex items-center gap-3 rounded-xl bg-[#EEF0FB] px-3.5 py-3">
                                <span class="shrink-0 text-[#8B5E34]">{!! $ic($p['star'], 'h-5 w-5', '1.8') !!}</span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-[13.5px] font-bold text-[#0B3A22]">{{ $item['name'] }}</p>
                                    <p class="mt-0.5 text-[10px] text-slate-600">{{ $item['qty'] }} pax{{ $item['note'] ? ' • '.$item['note'] : '' }}</p>
                                </div>
                                <p class="shrink-0 text-[12px] font-extrabold text-[#0B3A22]">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                            </div>
                        @empty
                            <div class="rounded-xl bg-[#EEF0FB] px-3.5 py-3 text-[11px] text-slate-600">Detail booking belum tersedia.</div>
                        @endforelse
                    </div>
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
                            <p class="mt-0.5 text-[9.5px] text-slate-600">{{ $isCheckedIn ? 'Sudah Check-in di Gerbang' : ($statusKey === 'lunas' ? (! empty($ticket['verified']) ? 'Terkonfirmasi Petugas' : 'Menunggu Konfirmasi Petugas') : ($statusKey === 'terkirim' ? 'Menunggu Verifikasi' : 'Belum Dibayar')) }}</p>
                        </div>
                    </div>

                    {{-- Barcode (hanya setelah lunas) --}}
                    @if($qrReady)
                    <div class="mt-5 px-3">
                        <div class="relative overflow-hidden">
                            {!! $barcodeSvg !!}
                            @if($isSuccessDone)
                                <span class="absolute inset-0 flex items-center justify-center">
                                    <span class="-rotate-[10deg] rounded-md border-[3px] border-[#0B3A22] bg-white/80 px-3 py-1 text-[14px] font-extrabold uppercase tracking-[0.22em] text-[#0B3A22]">Berhasil</span>
                                </span>
                            @endif
                        </div>
                        <p class="mt-1.5 text-center text-[8px] font-bold uppercase tracking-[0.2em] text-slate-800">Secure Token Authenticated</p>
                    </div>
                    @endif
                </div>
            </section>

            {{-- ========== PETUNJUK MASUK ========== --}}
            <section data-reveal class="{{ $reveal }} mt-4 flex items-start gap-3 rounded-2xl bg-white p-4 {{ $shadow }}">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#FDD9B0] text-[#8B5E34]">{!! $ic($p['scan'], 'h-5 w-5', '1.8') !!}</span>
                <div>
                    <h3 class="text-[16px] font-bold text-[#0B3A22]">Petunjuk Masuk Wisata</h3>
                    <p class="mt-1 text-[12.5px] leading-relaxed text-slate-700">
                        @if($qrReady)
                            Tunjukkan barcode di bawah ini kepada petugas gerbang depan Jiwanta Ciwidey. Petugas akan memindai barcode tersebut. Anda akan langsung menerima gelang akses pengunjung dan voucher welcome drink.
                        @else
                            Barcode akan muncul setelah pembayaran Anda lunas dan diverifikasi admin. Simpan halaman ini, lalu buka kembali menu Tiket Saya.
                        @endif
                    </p>
                </div>
            </section>

            {{-- ========== AKSI ========== --}}
            <section data-reveal class="{{ $reveal }} mt-6 space-y-2.5">
                <a href="{{ route('dashboard') }}" class="block py-3 text-center text-[13px] font-medium text-slate-600 transition hover:text-[#0B3A22]">Kembali ke Beranda</a>
            </section>

        @else

            {{-- ========== KOSONG ========== --}}
            <section data-reveal class="{{ $reveal }} rounded-2xl bg-white p-6 text-center {{ $shadow }}">
                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-[#FDD9B0] text-[#8B5E34]">{!! $ic($p['image'], 'h-6 w-6', '1.8') !!}</span>
                <h2 class="mt-3 text-[18px] font-bold text-[#0B3A22]">Belum Ada Tiket</h2>
                <p class="mt-1 text-[12.5px] leading-relaxed text-slate-600">Anda belum memiliki pesanan aktif. Buat reservasi untuk mendapatkan e-ticket QR.</p>
                <a href="{{ route('reservasi') }}" class="mt-4 inline-flex items-center justify-center gap-2 rounded-full bg-[#0B3A22] px-5 py-3 text-[13px] font-bold text-white transition hover:bg-[#0F4527] active:scale-95">Buat Reservasi Sekarang</a>
            </section>

        @endif

        {{-- ========== RIWAYAT TRANSAKSI ========== --}}
        @if ($transaksis->isNotEmpty())
            <section data-reveal class="{{ $reveal }} mt-6">
                <div class="mb-3 flex items-end justify-between">
                    <h2 class="text-[16px] font-bold text-[#0B3A22]">Riwayat Transaksi</h2>
                    <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">{{ $transaksis->count() }} Pesanan</span>
                </div>
                <div class="space-y-3">
                    @foreach ($transaksis as $trx)
                        @php
                            $dtrx = \Illuminate\Support\Carbon::parse($trx->tanggal_transaksi);
                            $dFirst = $trx->detailtransaksis->first();
                            $jenis = $dFirst?->tiket?->nama_tiket
                                ?? ($dFirst?->reservasi?->cabin ? 'Cabin '.$dFirst->reservasi->cabin->nama_cabin : 'Pesanan Jiwanta');
                            $qtyTrx = max(1, (int) $trx->detailtransaksis->sum('jumlah'));
                            $badge = match (true) {
                                $trx->checked_in_at => ['Dipakai', 'bg-[#DDE6FB] text-[#0B3A22]'],
                                $trx->status === 'lunas' && $trx->verified_at => ['Dikonfirmasi', 'bg-[#CDEFD5] text-[#0B3A22]'],
                                $trx->status === 'lunas' => ['Lunas', 'bg-[#CDEFD5] text-[#0B3A22]'],
                                $trx->status === 'gagal' => ['Gagal', 'bg-[#FBD5D5] text-[#8B1F1F]'],
                                default => ['Menunggu', 'bg-[#FBD9B0] text-[#8B5E34]'],
                            };
                            $isActive = $currentTrx && (int) $currentTrx->id === (int) $trx->id;
                        @endphp
                        <a href="{{ route('tiket', ['trx' => $trx->id]) }}"
                           @if($isActive) aria-current="true" @endif
                           class="flex items-center gap-3 rounded-2xl bg-white p-3.5 transition {{ $shadow }} {{ $isActive ? 'ring-2 ring-[#0B3A22]' : 'hover:bg-[#f7f8ff] active:scale-[0.99]' }}">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-[#EEF0FB] text-[#0B3A22]">{!! $ic($p['check'], 'h-5 w-5', '1.8') !!}</span>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <p class="truncate text-[14px] font-bold text-[#0B3A22]">{{ $jenis }}</p>
                                    <span data-trx-badge="{{ $trx->kode_transaksi }}" class="shrink-0 rounded-full px-2 py-0.5 text-[9px] font-bold {{ $badge[1] }}">{{ $badge[0] }}</span>
                                </div>
                                <p class="mt-0.5 truncate text-[10.5px] text-slate-500">{{ $trx->kode_transaksi }} • {{ $dtrx->day.' '.$monShort[$dtrx->month - 1].' '.$dtrx->year }} • {{ $qtyTrx }} pax</p>
                            </div>
                            <div class="flex shrink-0 flex-col items-end gap-1">
                                <p class="text-[14px] font-extrabold text-[#0B3A22]">Rp {{ number_format($trx->total_amount, 0, ',', '.') }}</p>
                                <span class="text-[9px] font-semibold text-slate-500">Lihat detail</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
        </main>

        {{-- Toast (muncul saat kode disalin) --}}
        <div id="toast" class="pointer-events-none fixed bottom-24 left-1/2 z-50 -translate-x-1/2 translate-y-4 whitespace-nowrap rounded-full bg-[#0B3A22] px-4 py-2 text-[12px] font-semibold text-white opacity-0 shadow-lg transition duration-300">
            Kode booking tersalin
        </div>

@endsection

@push('scripts')
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

        // Tanpa tiket aktif: cukup animasi muncul, hentikan Fitur tiket.
        if (!document.getElementById('booking-code')) return;

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
        const openAt  = new Date(@json($gateOpen)).getTime();
        const closeAt = new Date(@json($gateClose)).getTime();
        const QR_READY = {{ $qrReady ? 'true' : 'false' }};
        const TICKET_STATUS = @json($statusKey);
        let VERIFIED = {{ ! empty($ticket['verified']) ? 'true' : 'false' }};
        let CHECKED_IN = {{ $isCheckedIn ? 'true' : 'false' }};
        let FORCE_ACTIVE = {{ $gateForce ? 'true' : 'false' }}; // tiket lunas -> selalu "Gate Siap Masuk"; pesanan session -> ikut jam buka
        const pad = (n) => String(n).padStart(2, '0');

        const updateGate = () => {
            const now = Date.now();
            let text = 'Gate Siap Masuk', warn = false;

            // Barcode baru terbit setelah lunas: pill mengikuti status pembayaran.
            if (!QR_READY) {
                text = TICKET_STATUS === 'terkirim'
                    ? 'Menunggu Verifikasi'
                    : (TICKET_STATUS === 'gagal' ? 'Pembayaran Gagal' : 'Menunggu Pembayaran');
                warn = TICKET_STATUS === 'gagal';
            } else if (!VERIFIED) {
                text = 'Menunggu Konfirmasi Petugas';
            } else if (!FORCE_ACTIVE) {
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

        // 5b. Status realtime (Reverb) — update tanpa refresh
        const IC_CHECK = `{!! $ic($p['check'], 'h-6 w-6', '2') !!}`;
        const IC_CLOCK = `{!! $ic($p['clock'], 'h-6 w-6', '2') !!}`;
        const STATUS_META = {
            lunas: { label: 'LUNAS & DIKONFIRMASI', color: 'text-[#B5F0BE]' },
            terkirim: { label: 'MENUNGGU VERIFIKASI', color: 'text-[#FDD9B0]' },
            pending: { label: 'MENUNGGU PEMBAYARAN', color: 'text-[#FDD9B0]' },
            gagal: { label: 'PEMBAYARAN GAGAL', color: 'text-[#F7B0B0]' },
        };
        const BADGE_META = {
            lunas: ['Lunas', 'bg-[#CDEFD5] text-[#0B3A22]'],
            gagal: ['Gagal', 'bg-[#FBD5D5] text-[#8B1F1F]'],
            pending: ['Menunggu', 'bg-[#FBD9B0] text-[#8B5E34]'],
        };
        const bookingCode = document.getElementById('booking-code').textContent.trim();
        const applyBadge = (code, status) => {
            const badge = document.querySelector('[data-trx-badge="' + code + '"]');
            if (!badge) return;
            const meta = BADGE_META[status] || BADGE_META.pending;
            badge.textContent = meta[0];
            badge.className = 'shrink-0 rounded-full px-2 py-0.5 text-[9px] font-bold ' + meta[1];
        };
        const applyStatus = (status) => {
            const meta = STATUS_META[status] || STATUS_META.pending;
            const label = document.getElementById('status-label');
            const icon = document.getElementById('status-icon');
            if (label) {
                label.textContent = status === 'lunas' && !VERIFIED ? 'LUNAS' : meta.label;
            }
            if (icon) {
                icon.className = 'flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/15 ' + meta.color;
                icon.innerHTML = status === 'lunas' ? IC_CHECK : IC_CLOCK;
            }
            if (status === 'lunas') {
                // Muat ulang agar QR/barcode dan status konfirmasi petugas ikut terbarui.
                if (!QR_READY || !VERIFIED) { window.location.reload(); return; }
                FORCE_ACTIVE = true;
                updateGate();
            }
        };
        if (window.JiwantaRealtime && window.JiwantaRealtime.userChannel) {
            window.JiwantaRealtime.subscribe(window.JiwantaRealtime.userChannel, 'order.status', (e) => {
                const code = (e && e.kode) || '';
                const status = (e && e.status) || 'pending';
                // Tiket sudah dipakai di gerbang -> muat ulang agar tampil stempel abu-abu.
                if (e && e.checked_in && code === bookingCode) { window.location.reload(); return; }
                applyBadge(code, status);
                if (code && code === bookingCode) applyStatus(status);
            });
        }

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

        // 9. Scroll halus
        document.documentElement.style.scrollBehavior = reduce ? 'auto' : 'smooth';
    });
</script>
@endpush
