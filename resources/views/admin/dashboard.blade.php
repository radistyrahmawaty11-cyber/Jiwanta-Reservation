@extends('layouts.admin')

@section('title', 'Dashboard Operasional - Jiwanta')

@section('page-content')

@php
    $ic = fn (string $d, string $c = 'h-4 w-4') =>
        '<svg class="'.$c.'" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="'.$d.'"/></svg>';
    $br = fn (array $lines) => implode('<br>', array_map('e', $lines));

    $p = [
        'dashboard' => 'M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z',
        'receipt'   => 'M6 3h12v18l-3-2-3 2-3-2-3 2V3zM9 8h6M9 12h6',
        'ticket'    => 'M4 7h16v3a2 2 0 000 4v3H4v-3a2 2 0 000-4V7zM14 7v10',
        'cabin'     => 'M3 11l9-8 9 8M5 10v10h14V10M10 20v-5h4v5',
        'pool'      => 'M3 19c1.5 0 1.5-1 3-1s1.5 1 3 1 1.5-1 3-1 1.5 1 3 1 1.5-1 3-1 1.5 1 3 1M8 15V6M14 15V6M8 8h6M8 12h6',
        'users'     => 'M17 20v-1a4 4 0 00-4-4H7a4 4 0 00-4 4v1M10 11a4 4 0 100-8 4 4 0 000 8zM21 20v-1a4 4 0 00-3-3.9M16 3.1a4 4 0 010 7.8',
        'chart'     => 'M5 20V10M12 20V4M19 20v-7',
        'gear'      => 'M12 15a3 3 0 100-6 3 3 0 000 6zM12 2v3M12 19v3M2 12h3M19 12h3M5 5l2 2M17 17l2 2M19 5l-2 2M7 17l-2 2',
        'wallet'    => 'M3 7h16a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V7zM3 7l3-3h11v3M16 13.5h2',
        'bell'      => 'M6 8a6 6 0 0112 0c0 7 3 8 3 8H3s3-1 3-8M10 20a2 2 0 004 0',
        'download'  => 'M12 4v11m-4-4l4 4 4-4M5 20h14',
        'clock'     => 'M12 8v4l2 2M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'logout'    => 'M15 4h4a1 1 0 011 1v14a1 1 0 01-1 1h-4M10 16l-4-4 4-4M6 12h10',
        'snow'      => 'M12 3v18M4.5 7.5l15 9M4.5 16.5l15-9M9 4l3 2 3-2M9 20l3-2 3 2',
        'arrow'     => 'M5 12h14M13 6l6 6-6 6',
        'check'     => 'M5 13l4 4L19 7',
        'trend'     => 'M3 17l6-6 4 4 8-8M15 7h6v6',
        'plus'      => 'M12 5v14M5 12h14',
        'calendar'  => 'M8 3v3M16 3v3M4 9h16M5 5h14a1 1 0 011 1v13a1 1 0 01-1 1H5a1 1 0 01-1-1V6a1 1 0 011-1z',
        'sliders'   => 'M4 7h9M17 7h3M4 17h3M11 17h9M15 5v4M9 15v4',
        'bed'       => 'M3 18V7M3 14h18v4M21 14v-2a3 3 0 00-3-3h-7v5',
        'door'      => 'M6 21V4h9v17M4 21h16M12 12h.01',
        'idcard'    => 'M4 6h16v12H4zM8.5 11a1.5 1.5 0 100-3 1.5 1.5 0 000 3zM6 15c.5-1.5 4.5-1.5 5 0M14 10h4M14 13h3',
        'tree'      => 'M12 3l4 6h-2.5l3.5 5h-3l3 5H7l3-5H7l3.5-5H8l4-6z',
        'x'         => 'M6 6l12 12M18 6L6 18',
    ];

    $mint = 'bg-[#CDEFD5]';
    $peach = 'bg-[#FBD9B0]';
    $lav = 'bg-[#DDE6FB]';

    $status = [
        'Check-in' => [$mint, 'bg-[#0B3A22]'],
        'Lunas'    => [$peach, 'bg-[#C2762B]'],
        'Menunggu' => [$lav, 'bg-slate-500'],
        'Gagal'    => ['bg-[#FBD5D5]', 'bg-red-500'],
    ];

    $tabs = [['all', 'Semua'], ['tiket', 'Tiket<br>Renang'], ['cabin', 'Cabin']];

    $card = 'rounded-2xl bg-white shadow-sm border border-slate-200';
    $cap = 'text-[9px] font-bold uppercase leading-snug tracking-wide text-slate-600';

    $inp = 'w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-[12px] text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-[#0B3A22] focus:ring-2 focus:ring-[#0B3A22]/15';
    $lbl = 'mb-1 block text-[11px] font-semibold text-slate-700';
    $err = 'mt-1 hidden text-[10px] font-medium text-red-600';
    $btnP = 'inline-flex items-center justify-center gap-2 rounded-lg bg-[#0B3A22] px-5 py-2.5 text-[12px] font-bold text-white transition hover:bg-[#124c2f] active:scale-95 disabled:cursor-wait disabled:opacity-60';
    $btnG = 'inline-flex items-center justify-center gap-2 rounded-lg bg-slate-100 px-5 py-2.5 text-[12px] font-semibold text-slate-700 transition hover:bg-slate-200 active:scale-95';
@endphp

                {{-- Header Section --}}
                <div class="flex items-start justify-between">
                    <div class="max-w-xl">
                        <p class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-wide text-[#8B5E34] mb-1">{!! $ic($p['tree'], 'h-3 w-3') !!} Jiwanta Highland Sanctuary &amp; Hot Spring</p>
                        <h1 class="text-[32px] font-bold leading-tight text-slate-900">Dashboard Operasional Utama</h1>
                        <p class="mt-2 text-[13px] leading-relaxed text-slate-600">Pantau pendapatan harian, reservasi aktif, dan status gerbang Ciwidey secara langsung.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="flex items-center gap-2 rounded-lg bg-white border border-slate-200 px-4 py-2 text-[11px] font-medium shadow-sm">
                            {!! $ic($p['calendar'], 'h-4 w-4') !!} Hari ini: {{ $todayLabel ?? now()->format('d M Y') }} <span class="h-1.5 w-1.5 rounded-full bg-[#C2762B]"></span>
                        </span>
                        <button type="button" data-open="report" class="flex items-center gap-2 rounded-lg bg-slate-100 px-4 py-2 text-[11px] font-medium transition hover:bg-slate-200">
                            {!! $ic($p['download'], 'h-4 w-4') !!} Unduh Laporan Ringkas
                        </button>
                    </div>
                </div>

                {{-- Stats Cards --}}
                <div class="grid grid-cols-4 gap-4">
                    <div class="{{ $card }} p-5">
                        <div class="flex items-start justify-between mb-3">
                            <p class="{{ $cap }}">Pendapatan Hari Ini</p>
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $mint }}">{!! $ic($p['wallet'], 'h-5 w-5') !!}</span>
                        </div>
                        <p class="text-[26px] font-bold leading-tight text-slate-900">Rp <span data-revenue>{{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</span></p>
                        <p class="mt-2 flex items-center gap-2 text-[11px]">
                            <b class="inline-flex items-center gap-1 rounded-full {{ ($growthPct ?? 0) >= 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700' }} px-2 py-0.5">{!! $ic($p['trend'], 'h-3 w-3') !!}{{ ($growthPct ?? 0) >= 0 ? '+' : '' }}{{ $growthPct ?? 0 }}%</b>
                            <span class="text-slate-600">dibandingkan kemarin</span>
                        </p>
                    </div>

                    <div class="{{ $card }} p-5">
                        <div class="flex items-start justify-between mb-3">
                            <p class="{{ $cap }}">Tiket Terjual Hari Ini</p>
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $lav }}">{!! $ic($p['ticket'], 'h-5 w-5') !!}</span>
                        </div>
                        <p class="text-[26px] font-bold leading-tight text-slate-900"><span data-sold>{{ $tiketTerjual ?? 0 }}</span> <span class="text-[15px] font-normal text-slate-500">Tiket Terjual</span></p>
                        <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-100">
                            <div data-sold-bar style="width: 61%" class="h-full rounded-full bg-[#0B3A22] transition-all duration-1000"></div>
                        </div>
                        <p class="mt-2 flex justify-between text-[10px] font-bold"><span data-sold-pct>61% Terisi</span><span data-sold-left class="text-slate-600">Sisa 115 Slot</span></p>
                    </div>

                    <div class="{{ $card }} p-5">
                        <div class="flex items-start justify-between mb-3">
                            <p class="{{ $cap }}">Okupansi Kabin</p>
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $peach }} text-[#8B5E34]">{!! $ic($p['cabin'], 'h-5 w-5') !!}</span>
                        </div>
                        <p class="text-[26px] font-bold leading-tight text-slate-900">{{ $totalCabinTerisi ?? 0 }} / {{ $totalCabin ?? 0 }} <span class="text-[15px] font-normal text-slate-500">Terisi</span></p>
                        <p class="mt-3 inline-flex items-center gap-2 rounded-lg {{ $peach }} px-3 py-1.5 text-[10px] font-bold text-[#6B4520]">
                            <span class="h-1.5 w-1.5 rounded-full bg-[#6B4520]"></span>100% Penuh • High Demand
                        </p>
                    </div>

                    <div class="{{ $card }} p-5">
                        <div class="flex items-start justify-between mb-3">
                            <p class="{{ $cap }}">Perlu Verifikasi Cepat</p>
                            <span class="flex h-10 w-10 items-center justify-center rounded-xl {{ $mint }}">{!! $ic($p['receipt'], 'h-5 w-5') !!}</span>
                        </div>
                        <p class="text-[26px] font-bold leading-tight"><span data-batch class="text-[#8B5E34]">{{ $perluVerifikasi ?? 0 }}</span> <span class="text-[15px] font-normal text-slate-500">Struk Baru</span></p>
                        <p class="mt-2 flex items-center gap-2 text-[11px] text-slate-600">{!! $ic($p['clock'], 'h-3.5 w-3.5') !!} Rata-rata respons: &lt; 5 menit</p>
                    </div>
                </div>

                {{-- Main Content Grid --}}
                <div class="grid grid-cols-[1fr_320px] gap-6">

                    {{-- Left Column --}}
                    <div class="space-y-6">

                        {{-- Reservations Table --}}
                        <section class="{{ $card }}">
                            <div class="p-5 border-b border-slate-100">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h2 class="text-[16px] font-bold text-slate-900">Tabel Reservasi Terbaru</h2>
                                        <p class="mt-1 text-[11px] leading-relaxed text-slate-600">Daftar booking terpusat untuk kunjungan perairan &amp; menginap</p>
                                    </div>
                                    <div class="flex items-center rounded-lg bg-slate-100 p-1">
                                        @foreach ($tabs as [$key, $label])
                                            <button type="button" data-filter="{{ $key }}"
                                                    class="rounded-md px-4 py-1.5 text-center text-[10px] font-semibold transition {{ $loop->first ? 'bg-[#0B3A22] text-white shadow-sm' : 'text-slate-700 hover:text-[#0B3A22]' }}">{!! $label !!}</button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-[60px_120px_100px_100px_90px_1fr] bg-slate-50 px-5 py-3 text-[9px] font-bold uppercase tracking-wide text-slate-600 border-b border-slate-200">
                                <span>Kode</span><span>Nama Tamu</span><span>Tipe Layanan</span><span>Tanggal &amp; Sesi</span><span>Status</span><span class="text-center">Aksi</span>
                            </div>

                            <div id="reservations">
                                @forelse ($reservations ?? [] as [$type, $code, $guest, $phone, $service, $date, $st])
                                    <div data-type="{{ $type }}" class="grid grid-cols-[60px_120px_100px_100px_90px_1fr] items-center px-5 py-4 transition hover:bg-slate-50 border-b border-slate-100 last:border-0">
                                        <span class="text-[10px] font-bold">{!! $br($code) !!}</span>
                                        <span>
                                            <span class="block text-[11px] font-semibold">{!! $br($guest) !!}</span>
                                            <span class="mt-0.5 block text-[10px] text-slate-500">{!! $br($phone) !!}</span>
                                        </span>
                                        <span class="text-[11px] leading-relaxed">{!! $br($service) !!}</span>
                                        <span class="text-[10px] leading-relaxed">{!! $br($date) !!}</span>
                                        <span><span class="inline-flex items-center gap-1.5 rounded-full {{ $status[$st][0] ?? 'bg-slate-100' }} px-2.5 py-1 text-[9px] font-bold"><span class="h-1.5 w-1.5 rounded-full {{ $status[$st][1] ?? 'bg-slate-400' }}"></span>{{ $st }}</span></span>
                                        <button type="button" data-resv-action class="justify-self-center text-slate-400 hover:text-slate-600" aria-label="Aksi">
                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="5" r="1.5"/><circle cx="12" cy="12" r="1.5"/><circle cx="12" cy="19" r="1.5"/></svg>
                                        </button>
                                    </div>
                                @empty
                                    <p class="p-8 text-center text-[12px] font-medium text-slate-500">Belum ada reservasi.</p>
                                @endforelse
                            </div>
                        </section>

                        {{-- Cabin Availability & Staff --}}
                        <div class="grid grid-cols-2 gap-6">

                            {{-- Cabin Availability --}}
                            <section class="{{ $card }} p-5">
                                <div class="mb-4 flex items-start justify-between">
                                    <h2 class="flex items-start gap-2 text-[15px] font-bold text-slate-900"><span class="mt-0.5">{!! $ic($p['cabin'], 'h-5 w-5') !!}</span> <span>Ketersediaan Kabin</span></h2>
                                    <span class="rounded-lg {{ $mint }} px-3 py-1.5 text-center text-[10px] font-bold">{{ $totalCabin ?? 0 }} Total Unit</span>
                                </div>
                                <div class="space-y-2">
                                    @forelse ($cabins ?? [] as [$n, $sub, $st, $busy, $icon])
                                        <div class="flex items-center gap-3 rounded-lg bg-slate-50 p-3">
                                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $busy ? $lav : $mint }}">{!! $ic($p[$icon], 'h-4 w-4') !!}</span>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-[11px] font-bold text-slate-900">{{ $n }}</p>
                                                <p class="text-[10px] text-slate-600">{{ $sub }}</p>
                                            </div>
                                            <span class="shrink-0 rounded-full {{ $busy ? $peach : $mint }} px-2.5 py-1 text-[9px] font-bold">{{ $st }}</span>
                                        </div>
                                    @empty
                                        <p class="py-4 text-center text-[11px] text-slate-500">Belum ada data cabin.</p>
                                    @endforelse
                                </div>
                            </section>

                            {{-- Staff Section --}}
                            <section class="{{ $card }} p-5">
                                <div class="mb-4 flex items-center justify-between">
                                    <h2 class="flex items-center gap-2 text-[15px] font-bold text-slate-900">{!! $ic($p['idcard'], 'h-5 w-5') !!} Petugas Lapangan</h2>
                                    <span class="relative flex h-2 w-2"><span class="absolute h-full w-full animate-ping rounded-full bg-[#0B3A22] opacity-60"></span><span class="relative h-2 w-2 rounded-full bg-[#0B3A22]"></span></span>
                                </div>
                                <div class="space-y-3">
                                    @forelse ($staff ?? [] as [$ini, $bg, $n, $sub])
                                        <div class="flex items-center gap-3">
                                            <span class="relative flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $bg }} text-[11px] font-bold">
                                                {{ $ini }}<span class="absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full border-2 border-white bg-[#0B3A22]"></span>
                                            </span>
                                            <div class="min-w-0 flex-1">
                                                <p class="text-[11px] font-bold text-slate-900">{{ $n }}</p>
                                                <p class="text-[10px] text-slate-600">{!! $br($sub) !!}</p>
                                            </div>
                                            <span class="rounded-full {{ $mint }} px-2.5 py-1 text-[9px] font-bold">Online</span>
                                        </div>
                                    @empty
                                        <p class="py-4 text-center text-[11px] text-slate-500">Belum ada petugas terdaftar.</p>
                                    @endforelse
                                </div>
                            </section>
                        </div>
                    </div>

                    {{-- Right Column --}}
                    <div class="space-y-6">

                        {{-- Quota Section --}}
                        <section class="{{ $card }} p-5">
                            <div class="mb-4 flex items-center justify-between">
                                <h2 class="flex items-center gap-2 text-[15px] font-bold text-slate-900">{!! $ic($p['ticket'], 'h-5 w-5') !!} Kuota Tiket Hari Ini</h2>
                                <span class="flex items-center gap-1.5 text-[9px] text-emerald-600 font-medium">
                                    <span class="relative flex h-2 w-2"><span class="absolute h-full w-full animate-ping rounded-full bg-emerald-500 opacity-70"></span><span class="relative h-2 w-2 rounded-full bg-emerald-600"></span></span>Real-time
                                </span>
                            </div>

                            <div class="space-y-4">
                                @forelse ($tiketKuota ?? [] as $t)
                                    @php
                                        $cap = max(0, $t['kapasitas']);
                                        $used = $t['terjual'];
                                        $total = $cap + $used;
                                        $sisa = $cap;
                                        $pct = $total > 0 ? min(100, (int) round(($used / $total) * 100)) : 0;
                                    @endphp
                                    <div data-quota-item data-quota-id="{{ $t['id'] }}" data-used="{{ $used }}" data-cap="{{ $cap }}">
                                        <div class="flex justify-between text-[11px] mb-1"><span class="font-semibold text-slate-700">{{ $t['nama'] }}</span><b class="text-[11px]"><span data-q-used>{{ $used }}</span> terjual</b></div>
                                        <div class="h-2 overflow-hidden rounded-full bg-slate-100"><div data-q-bar style="width: {{ $pct }}%" class="h-full rounded-full bg-[#0B3A22] transition-all duration-1000"></div></div>
                                        <p data-q-text class="mt-1.5 text-right text-[10px] text-slate-600">Sisa {{ $sisa }} tiket ({{ $pct }}% terpakai)</p>
                                    </div>
                                @empty
                                    <p class="py-4 text-center text-[11px] text-slate-500">Belum ada data tiket.</p>
                                @endforelse

                                <button type="button" data-open="quota" class="mt-2 flex w-full items-center justify-center gap-2 rounded-lg bg-slate-100 py-2.5 text-[11px] font-bold transition hover:bg-slate-200">
                                    {!! $ic($p['sliders'], 'h-4 w-4') !!} + Atur Kuota
                                </button>
                            </div>
                        </section>

                    </div>
                </div>
@endsection

@section('overlays')
{{-- Modal laporan --}}
<div data-modal="report" class="fixed inset-0 z-50 hidden items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="rp-title">
    <div data-close class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
    <form id="report-form" data-panel class="relative w-full max-w-md scale-95 overflow-hidden rounded-2xl bg-white opacity-0 shadow-xl transition duration-200">
        <div class="flex items-start justify-between border-b border-slate-100 px-6 py-4">
            <div>
                <h3 id="rp-title" class="text-[16px] font-bold text-slate-900">Unduh Laporan Ringkas</h3>
                <p class="mt-0.5 text-[11px] text-slate-500">Hari ini, 20 Sep 2026. Pilih format dan isi laporan.</p>
            </div>
            <button type="button" data-close class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700" aria-label="Tutup">{!! $ic($p['x'], 'h-5 w-5') !!}</button>
        </div>
        <div class="space-y-4 px-6 py-5">
            <div>
                <p class="{{ $lbl }}">Format</p>
                <div class="grid grid-cols-2 gap-2">
                    @foreach ([['csv', 'CSV (Excel)', 'Data tabel, bisa dibuka di Excel'], ['pdf', 'PDF / Cetak', 'Tampilan siap cetak']] as [$v, $t, $d])
                        <label class="cursor-pointer">
                            <input type="radio" name="format" value="{{ $v }}" class="peer sr-only" {{ $loop->first ? 'checked' : '' }}>
                            <span class="block rounded-xl border border-slate-200 p-3 transition peer-checked:border-[#0B3A22] peer-checked:bg-[#0B3A22]/5 peer-checked:ring-2 peer-checked:ring-[#0B3A22]/15">
                                <b class="block text-[12px] text-slate-900">{{ $t }}</b><span class="text-[10px] text-slate-500">{{ $d }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div>
                <p class="{{ $lbl }}">Isi laporan</p>
                <div class="space-y-2">
                    @foreach ([['summary', 'Ringkasan pendapatan & tiket'], ['reservations', 'Daftar reservasi'], ['quota', 'Kuota tiket & okupansi kabin']] as [$v, $t])
                        <label class="flex cursor-pointer items-center gap-2.5 text-[12px] text-slate-700">
                            <input type="checkbox" name="sections" value="{{ $v }}" checked class="h-4 w-4 rounded border-slate-300 accent-[#0B3A22]"> {{ $t }}
                        </label>
                    @endforeach
                </div>
            </div>
            <p data-rp-error class="hidden rounded-lg bg-red-50 px-3 py-2 text-[11px] font-medium text-red-700"></p>
        </div>
        <div class="flex justify-end gap-2 border-t border-slate-100 bg-slate-50 px-6 py-4">
            <button type="button" data-close class="{{ $btnG }}">Batal</button>
            <button type="submit" class="{{ $btnP }}">{!! $ic($p['download'], 'h-4 w-4') !!} Unduh Laporan</button>
        </div>
    </form>
</div>

<div data-modal="quota" class="fixed inset-0 z-50 hidden items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="quota-title">
    <div data-close class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
    <div data-panel class="relative flex max-h-[90vh] w-full max-w-md scale-95 flex-col overflow-hidden rounded-2xl bg-white opacity-0 shadow-xl transition duration-200">
        <div class="mb-4 flex items-start justify-between border-b border-slate-100 px-6 py-4">
            <div>
                <h3 id="quota-title" class="text-[16px] font-bold text-slate-900">Atur Kuota</h3>
                <p class="mt-1 text-[11px] leading-relaxed text-slate-600">Sesuaikan kuota harian tiket renang dan kapasitas tiap kabin. Isi <b>0</b> untuk menutup penjualan/pemesanan.</p>
            </div>
            <button type="button" data-close class="text-slate-400 transition hover:text-slate-600" aria-label="Tutup">
                {!! $ic($p['x'], 'h-5 w-5') !!}
            </button>
        </div>
        <div class="flex-1 overflow-y-auto px-6 py-5">
            <div class="space-y-5">
                <div>
                    <p class="{{ $lbl }}">Kuota Tiket Renang</p>
                    <div class="space-y-2">
                        @forelse ($tiketKuota ?? [] as $t)
                            <div class="flex items-center gap-3">
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-[11px] font-semibold text-slate-800">{{ $t['nama'] }}</p>
                                    <p class="text-[10px] text-slate-500">Terjual {{ $t['terjual'] }} tiket</p>
                                </div>
                                <div class="w-24 shrink-0">
                                    <input type="number" min="0" max="100000" step="1" value="{{ $t['kapasitas'] }}"
                                           data-quota-id="{{ $t['id'] }}"
                                           data-quota-url="/admin/tiket/{{ $t['id'] }}/kuota"
                                           data-quota-nama="{{ $t['nama'] }}"
                                           data-quota-terjual="{{ $t['terjual'] }}"
                                           data-current="{{ $t['kapasitas'] }}"
                                           aria-label="Kuota {{ $t['nama'] }}" class="{{ $inp }} text-right">
                                </div>
                            </div>
                        @empty
                            <p class="py-2 text-center text-[11px] text-slate-500">Belum ada data tiket.</p>
                        @endforelse
                    </div>
                </div>

                <div>
                    <p class="{{ $lbl }}">Kapasitas Kabin</p>
                    <div class="space-y-2">
                        @forelse ($cabinQuota ?? [] as $c)
                            <div class="flex items-center gap-3">
                                <p class="min-w-0 flex-1 truncate text-[11px] font-semibold text-slate-800">{{ $c['nama'] }}</p>
                                <div class="w-24 shrink-0">
                                    <input type="number" min="0" max="100" step="1" value="{{ $c['kapasitas'] }}"
                                           data-cabin-id="{{ $c['id'] }}"
                                           data-cabin-url="/admin/cabin/{{ $c['id'] }}/kapasitas"
                                           data-cabin-nama="{{ $c['nama'] }}"
                                           data-current="{{ $c['kapasitas'] }}"
                                           aria-label="Kapasitas {{ $c['nama'] }}" class="{{ $inp }} text-right">
                                </div>
                            </div>
                        @empty
                            <p class="py-2 text-center text-[11px] text-slate-500">Belum ada data kabin.</p>
                        @endforelse
                    </div>
                </div>

                <p id="quota-error" class="{{ $err }}"></p>
            </div>
        </div>
        <div class="flex justify-end gap-2 border-t border-slate-100 px-6 py-4">
            <button type="button" data-close class="{{ $btnG }}">Batal</button>
            <button type="button" id="quota-save" class="{{ $btnP }}">Simpan Kuota</button>
        </div>
    </div>
</div>

<div data-modal="resv" class="fixed inset-0 z-50 hidden items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="resv-title">
    <div data-close class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
    <div data-panel class="relative w-full max-w-md scale-95 rounded-2xl bg-white p-6 opacity-0 shadow-xl transition duration-200">
        <div class="mb-4 flex items-start justify-between">
            <div>
                <h3 id="resv-title" class="text-[16px] font-bold text-slate-900">Detail Reservasi</h3>
                <p class="mt-1 text-[11px] leading-relaxed text-slate-600">Rincian booking pada tabel reservasi terbaru.</p>
            </div>
            <button type="button" data-close class="text-slate-400 transition hover:text-slate-600" aria-label="Tutup">
                {!! $ic($p['x'], 'h-5 w-5') !!}
            </button>
        </div>
        <dl class="divide-y divide-slate-100 text-[12px]">
            <div class="flex items-center justify-between gap-4 py-2.5"><dt class="text-slate-500">Kode</dt><dd data-resv="code" class="text-right font-semibold text-slate-900">-</dd></div>
            <div class="flex items-center justify-between gap-4 py-2.5"><dt class="text-slate-500">Nama Tamu</dt><dd data-resv="guest" class="text-right font-semibold text-slate-900">-</dd></div>
            <div class="flex items-center justify-between gap-4 py-2.5"><dt class="text-slate-500">No. WhatsApp</dt><dd data-resv="phone" class="text-right text-slate-900">-</dd></div>
            <div class="flex items-center justify-between gap-4 py-2.5"><dt class="text-slate-500">Tipe Layanan</dt><dd data-resv="service" class="text-right text-slate-900">-</dd></div>
            <div class="flex items-center justify-between gap-4 py-2.5"><dt class="text-slate-500">Jadwal</dt><dd data-resv="date" class="text-right text-slate-900">-</dd></div>
            <div class="flex items-center justify-between gap-4 py-2.5"><dt class="text-slate-500">Status</dt><dd data-resv="status" class="text-right font-semibold text-slate-900">-</dd></div>
        </dl>
        <div class="mt-5 flex justify-end">
            <button type="button" data-close class="{{ $btnP }}">Tutup</button>
        </div>
    </div>
</div>

<div id="toast" class="pointer-events-none fixed bottom-6 right-6 z-[60] flex translate-y-4 items-center gap-2 rounded-xl bg-slate-900 px-4 py-3 text-[12px] font-medium text-white opacity-0 shadow-xl transition duration-300" role="status" aria-live="polite"></div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const $ = (s, root = document) => root.querySelector(s);
        const $$ = (s, root = document) => Array.from(root.querySelectorAll(s));
        const fmt = (n) => Math.round(n).toLocaleString('id-ID');

        const animate = (el, to, dur = 900) => {
            if (!el) return;
            const from = Number(el.dataset.val ?? 0);
            el.dataset.val = to;
            if (reduce) { el.textContent = fmt(to); return; }
            const t0 = performance.now();
            const tick = (now) => {
                const p = Math.min((now - t0) / dur, 1);
                el.textContent = fmt(from + (to - from) * (1 - Math.pow(1 - p, 3)));
                if (p < 1) requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
        };

        // Data asli dari database
        const S = {
            revenue: {{ $totalPendapatan ?? 0 }},
            sold: {{ $tiketTerjual ?? 0 }},
        };
        const QUOTA = @json($tiketKuota);
        let CAP = {{ \App\Models\Tiket::sum('stok') ?: 1 }};
        const quotaRow = (re) => QUOTA.find((t) => re.test(t.nama || ''));
        let Q1_CAP = quotaRow(/classic/i)?.kapasitas ?? CAP;
        let Q2_CAP = quotaRow(/premier/i)?.kapasitas ?? CAP;
        S.q1 = quotaRow(/classic/i)?.terjual ?? 0;
        S.q2 = quotaRow(/premier/i)?.terjual ?? 0;
        const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        const renderQuota = () => {
            $$('[data-quota-item]').forEach((el) => {
                const used = Number(el.dataset.used);
                const cap = Math.max(0, Number(el.dataset.cap));
                const total = used + cap;
                const pct = total > 0 ? Math.min(100, Math.round((used / total) * 100)) : 0;
                const bar = $('[data-q-bar]', el), txt = $('[data-q-text]', el);
                if (bar) bar.style.width = pct + '%';
                if (txt) txt.textContent = 'Sisa ' + cap + ' tiket (' + pct + '% terpakai)';
                animate($('[data-q-used]', el), used, 900);
            });
        };

        const render = () => {
            animate($('[data-revenue]'), S.revenue, 1200);
            animate($('[data-sold]'), S.sold);
            const pool = CAP + S.sold;
            const pct = pool > 0 ? Math.min(100, Math.round((S.sold / pool) * 100)) : 0;
            $('[data-sold-bar]').style.width = pct + '%';
            $('[data-sold-pct]').textContent = pct + '% Terisi';
            $('[data-sold-left]').textContent = 'Sisa ' + Math.max(0, CAP) + ' Slot';
            renderQuota();
        };

        ['[data-revenue]', '[data-sold]'].forEach((s) => ($(s).dataset.val = 0));
        $$('[data-q-used]').forEach((el) => (el.dataset.val = 0));
        $$('[data-q-bar], [data-sold-bar]').forEach((bar) => {
            const w = bar.style.width;
            bar.style.width = '0%';
            requestAnimationFrame(() => requestAnimationFrame(() => (bar.style.width = w)));
        });
        render();

        const setAll = (sel, v) => $$(sel).forEach((el) => (el.textContent = v));
        let total = Number($('[data-total]')?.textContent || 0);
        let batch = Number($('[data-batch]').textContent);

        $$('[data-action]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const row = btn.closest('[data-queue-row]');
                if (!row || row.dataset.done) return;
                row.dataset.done = '1';

                if (btn.dataset.action === 'verify') {
                    S.revenue += Number(row.dataset.amount);
                    S.sold = Math.min(CAP, S.sold + Number(row.dataset.tickets));
                    render();
                }

                row.style.transition = 'all 0.3s ease';
                row.style.opacity = '0';
                row.style.transform = 'translateX(-10px)';
                setTimeout(() => {
                    row.remove();
                    if (!$('[data-queue-row]')) $('#queue-empty').classList.remove('hidden');
                }, 300);

                total = Math.max(0, total - 1);
                batch = Math.max(0, batch - 1);
                setAll('[data-total]', total);
                setAll('[data-batch]', batch);
            });
        });

        const on = ['bg-[#0B3A22]', 'text-white', 'shadow-sm'];
        const off = ['text-slate-700', 'hover:text-[#0B3A22]'];
        $$('[data-filter]').forEach((tab) => {
            tab.addEventListener('click', () => {
                $$('[data-filter]').forEach((t) => { t.classList.remove(...on); t.classList.add(...off); });
                tab.classList.remove(...off);
                tab.classList.add(...on);
                $$('#reservations [data-type]').forEach((row) => {
                    row.classList.toggle('hidden', tab.dataset.filter !== 'all' && row.dataset.type !== tab.dataset.filter);
                });
            });
        });

        const rp = (n) => 'Rp ' + fmt(n);
        const esc = (v) => String(v ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
        const TODAY = '2026-09-20';

        let toastTimer;
        const toast = (msg) => {
            const t = $('#toast');
            t.textContent = msg;
            t.classList.remove('opacity-0', 'translate-y-4');
            clearTimeout(toastTimer);
            toastTimer = setTimeout(() => t.classList.add('opacity-0', 'translate-y-4'), 2600);
        };

        let openName = null, lastFocus = null;
        const openModal = (name) => {
            const m = $(`[data-modal="${name}"]`);
            if (openName) closeModal(true);
            lastFocus = document.activeElement;
            openName = name;
            m.classList.remove('hidden'); m.classList.add('flex');
            document.body.classList.add('overflow-hidden');
            const panel = m.querySelector('[data-panel]');
            requestAnimationFrame(() => requestAnimationFrame(() => panel.classList.remove('opacity-0', 'scale-95')));
            setTimeout(() => (panel.querySelector('input:not([type=hidden]), select') || panel).focus(), 60);
        };
        const closeModal = (instant = false) => {
            if (!openName) return;
            const m = $(`[data-modal="${openName}"]`);
            m.querySelector('[data-panel]').classList.add('opacity-0', 'scale-95');
            const done = () => { m.classList.add('hidden'); m.classList.remove('flex'); };
            (instant || reduce) ? done() : setTimeout(done, 180);
            openName = null;
            document.body.classList.remove('overflow-hidden');
            if (lastFocus && lastFocus.focus) lastFocus.focus();
        };
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });
        document.addEventListener('click', (e) => {
            if (e.target.closest('[data-close]')) closeModal();
            const opener = e.target.closest('[data-open]');
            if (!opener) return;
            if (opener.dataset.open === 'report') openModal('report');
            if (opener.dataset.open === 'quota') { syncQuotaInputs(); openModal('quota'); }
        });

        const quotaError = $('#quota-error');

        const syncQuotaInputs = () => {
            $$('[data-current]').forEach((el) => { el.value = el.dataset.current; });
            quotaError.classList.add('hidden');
        };

        const jsonPut = async (url, body) => {
            const res = await fetch(url, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(body),
            });
            if (!res.ok) {
                const err = await res.json().catch(() => ({}));
                const first = Object.values(err.errors || {}).flat()[0];
                throw new Error(first || err.message || `Gagal menyimpan (${res.status}).`);
            }
            return res.json().catch(() => ({}));
        };

        $('#quota-save').addEventListener('click', async () => {
            quotaError.classList.add('hidden');
            const jobs = [];
            let invalid = null;

            $$('input[data-quota-id]').forEach((el) => {
                const val = Number(el.value);
                if (!Number.isInteger(val) || val < 0 || val > 100000) {
                    invalid = `${el.dataset.quotaNama}: isi sisa kuota 0 - 100000 (0 = tutup penjualan).`;
                    return;
                }
                jobs.push({ el, url: el.dataset.quotaUrl, body: { cap: val }, value: val, type: 'tiket' });
            });

            if (!invalid) {
                $$('input[data-cabin-id]').forEach((el) => {
                    const val = Number(el.value);
                    if (!Number.isInteger(val) || val < 0 || val > 100) {
                        invalid = `${el.dataset.cabinNama}: isi 0 untuk menutup pemesanan, atau 1 - 100.`;
                        return;
                    }
                    jobs.push({ el, url: el.dataset.cabinUrl, body: { kapasitas: val }, value: val, type: 'cabin' });
                });
            }

            if (invalid) {
                quotaError.textContent = invalid;
                quotaError.classList.remove('hidden');
                return;
            }
            if (!jobs.length) return;

            const btn = $('#quota-save');
            const label = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Menyimpan...';
            try {
                await Promise.all(jobs.map((j) => jsonPut(j.url, j.body)));
                jobs.forEach((j) => { j.el.dataset.current = String(j.value); });
                jobs.filter((j) => j.type === 'tiket').forEach((j) => {
                    const item = $(`[data-quota-item][data-quota-id="${j.el.dataset.quotaId}"]`);
                    if (item) item.dataset.cap = String(j.value);
                    const row = QUOTA.find((q) => String(q.id) === String(j.el.dataset.quotaId));
                    if (row) row.kapasitas = j.value;
                });

                CAP = Math.max(1, $$('[data-quota-item]').reduce((sum, el) => sum + Number(el.dataset.cap || 0), 0));
                Q1_CAP = quotaRow(/classic/i)?.kapasitas ?? CAP;
                Q2_CAP = quotaRow(/premier/i)?.kapasitas ?? CAP;
                render();
                closeModal();
                toast('Kuota berhasil diperbarui');
            } catch (err) {
                quotaError.textContent = err.message;
                quotaError.classList.remove('hidden');
            } finally {
                btn.disabled = false;
                btn.textContent = label;
            }
        });

        const resvCellText = (el) => el
            ? el.innerHTML.split(/<br\s*\/?>/i).map((s) => s.replace(/<[^>]+>/g, '').replace(/&amp;/g, '&').trim()).filter(Boolean).join(' ')
            : '-';

        document.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-resv-action]');
            if (!btn) return;
            const row = btn.closest('[data-type]');
            if (!row) return;
            const c = row.children;
            const fill = (key, value) => { const el = $(`[data-resv="${key}"]`); if (el) el.textContent = value; };
            fill('code', resvCellText(c[0]));
            fill('guest', resvCellText(c[1] && c[1].children[0]));
            fill('phone', resvCellText(c[1] && c[1].children[1]));
            fill('service', resvCellText(c[2]));
            fill('date', resvCellText(c[3]));
            fill('status', resvCellText(c[4]));
            openModal('resv');
        });

        const rForm = $('#report-form');

        const cellText = (el, sep) => el.innerHTML.split(/<br\s*\/?>/i).map((s) => s.replace(/<[^>]+>/g, '').replace(/&amp;/g, '&').trim()).join(sep).replace(/\s+/g, ' ').trim();
        const readReservations = () => [...$$('#reservations [data-type]')].map((r) => {
            const c = r.children, who = c[1].children;
            return [cellText(c[0], ''), cellText(who[0], ' '), cellText(who[1], ''), cellText(c[2], ' '), cellText(c[3], ' '), cellText(c[4], ' ')];
        });

        const buildReport = (sections) => {
            const out = { title: 'Laporan Ringkas Operasional - Jiwanta Ciwidey', period: 'Hari ini, 20 Sep 2026', blocks: [] };
            if (sections.includes('summary')) out.blocks.push({ name: 'Ringkasan', head: ['Indikator', 'Nilai'], rows: [
                ['Pendapatan hari ini', rp(S.revenue)], ['Tiket terjual', `${S.sold} tiket`], ['Okupansi cabin', '{{ $totalCabinTerisi ?? 0 }} / {{ $totalCabin ?? 0 }} (100%)'], ['Struk perlu verifikasi', $('[data-batch]').textContent],
            ] });
            if (sections.includes('reservations')) out.blocks.push({ name: 'Daftar Reservasi', head: ['Kode', 'Nama Tamu', 'No. WhatsApp', 'Layanan', 'Jadwal', 'Status'], rows: readReservations() });
            if (sections.includes('quota')) out.blocks.push({ name: 'Kuota & Okupansi', head: ['Kolam / Unit', 'Terjual', 'Kapasitas', 'Sisa'], rows: [
                ['Tiket Renang Classic', S.q1, S.q1 + Q1_CAP, Q1_CAP], ['Tiket Renang Premier', S.q2, S.q2 + Q2_CAP, Q2_CAP], ['Cabin Suite & Cabin Short ({{ $totalCabin ?? 0 }} unit)', {{ $totalCabinTerisi ?? 0 }}, {{ $totalCabin ?? 0 }}, {{ ($totalCabin ?? 0) - ($totalCabinTerisi ?? 0) }}],
            ] });
            return out;
        };

        const downloadCSV = (rep) => {
            const cell = (v) => `"${String(v).replace(/"/g, '""')}"`;
            const rows = [[rep.title], ['Periode', rep.period], ['Dibuat', new Date().toLocaleString('id-ID')], []];
            rep.blocks.forEach((b) => rows.push([b.name.toUpperCase()], b.head, ...b.rows, []));
            const csv = '\uFEFF' + rows.map((r) => r.map(cell).join(',')).join('\r\n');
            const a = document.createElement('a');
            a.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8' }));
            a.download = `laporan-ringkas-${TODAY}.csv`;
            document.body.appendChild(a); a.click(); a.remove();
            setTimeout(() => URL.revokeObjectURL(a.href), 1000);
        };

        const printPDF = (rep) => {
            const w = window.open('', '_blank');
            if (!w) throw new Error('Pop-up diblokir browser. Izinkan pop-up lalu coba lagi.');
            const table = (b) => `<h2>${esc(b.name)}</h2><table><thead><tr>${b.head.map((h) => `<th>${esc(h)}</th>`).join('')}</tr></thead><tbody>${b.rows.map((r) => `<tr>${r.map((c) => `<td>${esc(c)}</td>`).join('')}</tr>`).join('')}</tbody></table>`;
            w.document.write(`<!doctype html><html lang="id"><head><meta charset="utf-8"><title>${esc(rep.title)}</title><style>
                body{font:13px/1.5 system-ui,sans-serif;color:#0b3a22;padding:32px}h1{font-size:20px;margin:0}p{margin:2px 0 18px;color:#555}
                h2{font-size:14px;margin:22px 0 8px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #d8dde8;padding:6px 8px;text-align:left;font-size:12px}
                th{background:#eef1fd}</style></head><body>
                <h1>${esc(rep.title)}</h1><p>Periode: ${esc(rep.period)} &bull; Dibuat: ${esc(new Date().toLocaleString('id-ID'))}</p>${rep.blocks.map(table).join('')}</body></html>`);
            w.document.close(); w.focus();
            setTimeout(() => w.print(), 300);
        };

        rForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const fd = new FormData(rForm);
            const sections = fd.getAll('sections'), format = fd.get('format');
            const box = $('[data-rp-error]');
            box.classList.add('hidden');
            if (!sections.length) { box.textContent = 'Pilih minimal satu isi laporan.'; box.classList.remove('hidden'); return; }
            try {
                const rep = buildReport(sections);
                format === 'pdf' ? printPDF(rep) : downloadCSV(rep);
                closeModal();
                toast(format === 'pdf' ? 'Laporan dibuka untuk dicetak' : 'Laporan CSV diunduh');
            } catch (err) {
                box.textContent = err.message; box.classList.remove('hidden');
            }
        });

        // ---------- Realtime: sinkronkan angka dari feed saat ada pesanan/pembayaran ----------
        const FEED_URL = @json(route('admin.feed'));
        const syncLive = async () => {
            try {
                const res = await fetch(FEED_URL, { headers: { 'Accept': 'application/json' } });
                const json = await res.json();
                const st = (json.data || json).stats || {};
                if (typeof st.revenue === 'number') S.revenue = st.revenue;
                if (typeof st.sold === 'number') S.sold = st.sold;
                render();
                if (typeof st.pending === 'number') { batch = st.pending; setAll('[data-batch]', st.pending); setAll('[data-total]', st.pending); }
            } catch (e) { /* abaikan gangguan jaringan */ }
        };
        window.addEventListener('jiwanta:realtime', syncLive);
        setInterval(syncLive, 15000);
    });
</script>
@endpush
