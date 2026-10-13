@extends('layouts.admin')

@section('title', 'Transaksi - Jiwanta')
@section('page-class') bg-[#F9F8FF] font-['Plus_Jakarta_Sans',ui-sans-serif,system-ui,sans-serif] text-slate-900 antialiased @endsection

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    @keyframes pop-in { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: none; } }
    .pop-in { animation: pop-in .35s ease-out both; }
    @media (prefers-reduced-motion: reduce) { .pop-in { animation: none; } }
</style>
@endpush

@section('page-content')

@php
    $ic = fn (string $d, string $c = 'h-4 w-4', string $sw = '1.8') =>
        '<svg class="'.$c.'" fill="none" stroke="currentColor" stroke-width="'.$sw.'" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="'.$d.'"/></svg>';

    $p = [
        'dashboard' => 'M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z',
        'receipt'   => 'M6 3h12v18l-3-2-3 2-3-2-3 2V3zM9 8h6M9 12h6',
        'ticket'    => 'M4 7h16v3a2 2 0 000 4v3H4v-3a2 2 0 000-4V7zM14 7v10',
        'cabin'     => 'M3 11l9-8 9 8M5 10v10h14V10M10 20v-5h4v5',
        'pool'      => 'M3 19c1.5 0 1.5-1 3-1s1.5 1 3 1 1.5-1 3-1 1.5 1 3 1 1.5-1 3-1 1.5 1 3 1M8 15V6M14 15V6M8 8h6M8 12h6',
        'users'     => 'M17 20v-1a4 4 0 00-4-4H7a4 4 0 00-4 4v1M10 11a4 4 0 100-8 4 4 0 000 8zM21 20v-1a4 4 0 00-3-3.9M16 3.1a4 4 0 010 7.8',
        'chart'     => 'M5 20V10M12 20V4M19 20v-7',
        'gear'      => 'M12 15a3 3 0 100-6 3 3 0 000 6zM2 12h3M19 12h3M5 5l2 2M17 17l2 2M19 5l-2 2M7 17l-2 2',
        'bell'      => 'M6 8a6 6 0 0112 0c0 7 3 8 3 8H3s3-1 3-8M10 20a2 2 0 004 0',
        'download'  => 'M12 4v11m-4-4l4 4 4-4M5 20h14',
        'clock'     => 'M12 8v4l2 2M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'logout'    => 'M15 4h4a1 1 0 011 1v14a1 1 0 01-1 1h-4M10 16l-4-4 4-4M6 12h10',
        'snow'      => 'M12 3v18M4.5 7.5l15 9M4.5 16.5l15-9M9 4l3 2 3-2M9 20l3-2 3 2',
        'tree'      => 'M12 3l4 6h-2.5l3.5 5h-3l3 5H7l3-5H7l3.5-5H8l4-6z',
        'search'    => 'M21 21l-4.3-4.3M17 11a6 6 0 11-12 0 6 6 0 0112 0z',
        'sliders'   => 'M4 7h9M17 7h3M4 17h3M11 17h9M15 5v4M9 15v4',
        'chevron'   => 'M6 9l6 6 6-6',
        'chevl'     => 'M15 18l-6-6 6-6',
        'chevr'     => 'M9 6l6 6-6 6',
        'refresh'   => 'M4 4v6h6M20 20v-6h-6M20 9A8 8 0 006 6L4 10M4 15a8 8 0 0014 3l2-4',
        'check'     => 'M5 13l4 4L19 7',
        'checkc'    => 'M9 12l2 2 4-4M12 21a9 9 0 100-18 9 9 0 000 18z',
        'x'         => 'M6 6l12 12M18 6L6 18',
        'eye'       => 'M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12zM12 15a3 3 0 100-6 3 3 0 000 6z',
        'alert'     => 'M12 4l9 16H3L12 4zM12 10v4M12 17h.01',
        'history'   => 'M3 12a9 9 0 109-9 9 9 0 00-6.4 2.6L3 8M3 3v5h5M12 8v4l3 2',
        'plus'      => 'M12 5v14M5 12h14',
        'edit'      => 'M12 20h9M16.5 3.5a2.1 2.1 0 013 3L7 21l-4 1 1-4L16.5 3.5z',
        'trash'     => 'M4 7h16M9 7V5a1 1 0 011-1h4a1 1 0 011 1v2M6 7l1 13a1 1 0 001 1h8a1 1 0 001-1l1-13M10 11v6M14 11v6',
        'save'      => 'M5 4h11l3 3v13H5zM8 4v5h6V4M8 20v-6h8v6',
    ];

    $mint  = 'bg-[#CDEFD5]';
    $peach = 'bg-[#FBD9B0]';
    $lav   = 'bg-[#DDE6FB]';
    $soft  = 'bg-[#EEF1FC]';
    $card  = 'rounded-2xl bg-white shadow-[0_4px_18px_rgba(15,69,39,0.08)]';

    $banks = [['all', 'Semua'], ['BCA', 'BCA'], ['Mandiri', 'Mandiri']];
    $statuses = [['all', 'Semua Status'], ['lunas', 'Lunas'], ['pending', 'Menunggu Verifikasi'], ['gagal', 'Gagal']];
@endphp

<div class="flex items-start justify-between gap-6">
    <div class="max-w-xl">
        <p class="mb-2 flex items-center gap-2">
            <span class="rounded-md {{ $mint }} px-2.5 py-1 text-[9px] font-bold uppercase tracking-wide text-[#0B3A22]">Finance Ledger</span>
            <span class="text-[10px] text-slate-600">Rekonsiliasi Otomatis Aktif</span>
        </p>
        <h1 class="text-[32px] font-bold leading-tight text-[#0B3A22]">Riwayat Transaksi</h1>
        <p class="mt-2 text-[13px] leading-relaxed text-slate-600">Seluruh pemesanan yang tercatat beserta metode pembayaran, nominal, dan status penyelesaiannya dalam satu daftar.</p>
    </div>

    <div class="grid w-[540px] shrink-0 grid-cols-3 gap-3">
        <div class="{{ $card }} flex items-center gap-3 px-4 py-3.5">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $mint }} text-[#0B3A22]">{!! $ic($p['receipt'], 'h-5 w-5') !!}</span>
            <div>
                <p class="text-[22px] font-bold leading-tight text-slate-900" data-stat-today>{{ $stats['today'] }}</p>
                <p class="text-[10px] leading-tight text-slate-600">Transaksi Hari Ini</p>
            </div>
        </div>
        <div class="{{ $card }} flex items-center gap-3 px-4 py-3.5">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $lav }} text-[#2F3F86]">{!! $ic($p['chart'], 'h-5 w-5') !!}</span>
            <div>
                <p class="text-[18px] font-bold leading-tight text-slate-900" data-stat-omzet>Rp {{ number_format($stats['omzet'], 0, ',', '.') }}</p>
                <p class="text-[10px] leading-tight text-slate-600">Omzet Hari Ini</p>
            </div>
        </div>
        <div class="{{ $card }} flex items-center gap-3 px-4 py-3.5">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $peach }} text-[#0B3A22]">{!! $ic($p['clock'], 'h-5 w-5') !!}</span>
            <div>
                <p class="text-[22px] font-bold leading-tight text-slate-900" data-stat-pending>{{ $stats['pending'] }}</p>
                <p class="text-[10px] leading-tight text-slate-600">Menunggu Verifikasi</p>
            </div>
        </div>
    </div>
</div>

<div class="{{ $card }} mt-6 p-5">
    <div class="flex flex-wrap items-center gap-3">
        <div class="relative min-w-[220px] flex-1">
            <span class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400">{!! $ic($p['search'], 'h-4 w-4') !!}</span>
            <input id="search" type="text" placeholder="Cari kode transaksi, pengguna, atau unit..." class="w-full rounded-xl border border-slate-200 bg-[#F7F8FC] py-2.5 pl-10 pr-4 text-[12px] text-slate-800 outline-none transition focus:border-[#0B3A22] focus:bg-white">
        </div>

        <div class="flex items-center gap-1 rounded-xl {{ $soft }} p-1">
            @foreach ($banks as [$val, $label])
                <button type="button" data-bank="{{ $val }}" class="rounded-lg px-3.5 py-2 text-[11px] font-semibold text-slate-600 transition {{ $val === 'all' ? 'bg-white text-[#0B3A22] shadow-sm' : 'hover:text-[#0B3A22]' }}">{{ $label }}</button>
            @endforeach
        </div>

        <div class="relative">
            <button type="button" id="status-btn" class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-[11px] font-semibold text-slate-700 transition hover:border-[#0B3A22]">
                {!! $ic($p['sliders'], 'h-3.5 w-3.5') !!}<span id="status-label">Semua Status</span>{!! $ic($p['chevron'], 'h-3.5 w-3.5') !!}
            </button>
            <div id="status-menu" class="hidden absolute right-0 top-full z-40 mt-2 w-44 overflow-hidden rounded-xl border border-slate-100 bg-white py-1 shadow-lg">
                @foreach ($statuses as [$val, $label])
                    <button type="button" data-status="{{ $val }}" class="block w-full px-4 py-2.5 text-left text-[11px] font-semibold text-slate-600 transition hover:bg-[#F3F5FC] hover:text-[#0B3A22]">{{ $label }}</button>
                @endforeach
            </div>
        </div>

        <a href="{{ route('admin.transaksi.export') }}" class="flex items-center gap-2 rounded-xl {{ $lav }} px-4 py-2.5 text-[11px] font-bold text-slate-800 transition hover:bg-[#cfd9f7] active:scale-95">
            {!! $ic($p['download'], 'h-4 w-4', '2.2') !!} Unduh XLS
        </a>
    </div>
</div>

<div class="{{ $card }} mt-6 overflow-hidden">
    <div class="overflow-x-auto">
        <div class="min-w-[900px]">
            <div class="grid grid-cols-[90px_140px_minmax(0,1.4fr)_100px_130px_150px_160px] items-center gap-3 border-b border-slate-100 bg-[#F3F5FC] px-5 py-3 text-[9px] font-bold uppercase tracking-wide text-slate-500">
                <span>Waktu</span>
                <span>Kode Booking</span>
                <span>Customer &amp; Unit</span>
                <span>Metode</span>
                <span>Nominal</span>
                <span>Status</span>
                <span class="text-right">Opsi</span>
            </div>
            <div id="trx-list"></div>
            {{-- Riwayat transaksi bersifat read-only: laporan dari pemesanan pengunjung. --}}
        </div>
    </div>
    <div class="flex items-center justify-between gap-4 border-t border-slate-100 px-5 py-4">
        <p class="text-[10px] text-slate-600">Menampilkan <span data-shown class="font-bold text-slate-800">0</span> dari <span data-total class="font-bold text-slate-800">0</span> transaksi</p>
        <div id="pager" class="flex items-center gap-1.5 text-[11px] font-semibold"></div>
    </div>
</div>
@endsection

@section('overlays')
    <div id="trx-detail" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/40 p-4 backdrop-blur-sm">
        <div class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                <h3 class="text-[15px] font-bold text-[#0B3A22]">Detail Transaksi</h3>
                <button type="button" data-close="trx-detail" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">{!! $ic($p['x'], 'h-4 w-4') !!}</button>
            </div>
            <div id="dt-body" class="max-h-[60vh] space-y-3 overflow-y-auto px-6 py-5"></div>
            <div class="flex justify-end gap-2 border-t border-slate-100 px-6 py-4">
                <button type="button" data-close="trx-detail" class="rounded-xl bg-[#0B3A22] px-4 py-2 text-[11px] font-bold text-white transition hover:bg-[#0e4a2c]">Tutup</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        const $ = (s, r = document) => r.querySelector(s);
        const $$ = (s, r = document) => [...r.querySelectorAll(s)];
        const ICON = @json($p);
        const ic = (n, c = 'h-4 w-4', sw = '1.8') => `<svg class="${c}" fill="none" stroke="currentColor" stroke-width="${sw}" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="${ICON[n]}"/></svg>`;
        const rp = (n) => 'Rp ' + Math.round(n).toLocaleString('id-ID');
        const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

        const state = { rows: @json($transaksis), q: '', bank: 'all', status: 'all', page: 1, per: 8, viewing: null };

        const openModal = (id) => { const m = $('#' + id); m.classList.remove('hidden'); m.classList.add('flex'); };
        const closeModal = (id) => { const m = $('#' + id); m.classList.add('hidden'); m.classList.remove('flex'); };
        $$('[data-close]').forEach((b) => b.addEventListener('click', () => closeModal(b.dataset.close)));
        ['trx-detail'].forEach((id) => {
            const m = $('#' + id);
            m.addEventListener('click', (e) => { if (e.target === m) closeModal(id); });
        });
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') ['trx-detail'].forEach(closeModal);
        });

        const PILL = {
            lunas:  ['bg-[#CDEFD5] text-[#0B3A22]', 'checkc', 'Lunas'],
            pending: ['bg-[#FBD9B0] text-[#6B4520]', 'clock', 'Menunggu'],
            gagal:  ['bg-[#FBD5D5] text-[#B42318]', 'alert', 'Gagal'],
        };
        const pill = (st) => {
            const [cls, icon, label] = PILL[st] || PILL.pending;
            return `<span class="inline-flex items-center gap-1.5 rounded-xl ${cls} px-3 py-1.5 text-[9px] font-bold leading-tight">${ic(icon, 'h-3 w-3 shrink-0', '2')} ${label}</span>`;
        };

        const row = (h, i) => `<div class="${reduce ? '' : 'pop-in '}grid grid-cols-[90px_140px_minmax(0,1.4fr)_100px_130px_150px_160px] items-center gap-3 border-b border-slate-100 px-5 py-4 last:border-0" style="animation-delay:${i * 40}ms">
            <span class="font-mono text-[10px] text-slate-500">${esc(h.t)} WIB</span>
            <span class="break-words text-[11px] font-semibold text-slate-900">${esc(h.kode)}</span>
            <span><b class="block text-[11px] text-slate-900">${esc(h.name)}</b><span class="block text-[10px] leading-snug text-slate-600">${esc(h.unit)}</span></span>
            <span><span class="inline-block rounded-lg bg-[#DDE6FB] px-3 py-1.5 text-[10px] font-bold text-slate-800">${esc(h.metode)}</span></span>
            <b class="text-[12px] text-slate-900">${rp(h.amount)}</b>
            <span>${pill(h.status)}</span>
            <span class="flex justify-end">
                <button type="button" data-view="${h.id}" class="rounded-lg bg-[#DDE6FB] px-3 py-2 text-[9px] font-bold text-slate-800 transition hover:bg-[#cfd9f7] active:scale-95">Detail</button>
            </span>
        </div>`;

        const visible = () => state.rows.filter((h) =>
            (state.bank === 'all' || h.metode === state.bank) &&
            (state.status === 'all' || h.status === state.status) &&
            (!state.q || (h.kode + ' ' + h.name + ' ' + h.unit).toLowerCase().includes(state.q)));

        const renderStats = () => {
            const today = state.rows.filter((h) => h.today);
            $('[data-stat-today]').textContent = today.length;
            $('[data-stat-omzet]').textContent = rp(today.reduce((a, h) => a + h.amount, 0));
            $('[data-stat-pending]').textContent = state.rows.filter((h) => h.status === 'pending').length;
        };

        const renderPager = (pages) => {
            const pager = $('#pager');
            if (pages <= 1) { pager.innerHTML = ''; return; }
            let html = `<button type="button" data-pg="prev" class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-600 transition hover:bg-slate-100">${ic('chevl', 'h-4 w-4', '2')}</button>`;
            for (let p = 1; p <= pages; p++) {
                html += `<button type="button" data-pg="${p}" class="flex h-8 w-8 items-center justify-center rounded-lg transition ${p === state.page ? 'bg-[#0B3A22] text-white' : 'border border-slate-200 text-slate-600 hover:bg-slate-100'}">${p}</button>`;
            }
            html += `<button type="button" data-pg="next" class="flex h-8 w-8 items-center justify-center rounded-lg border border-slate-200 text-slate-600 transition hover:bg-slate-100">${ic('chevr', 'h-4 w-4', '2')}</button>`;
            $('#pager').innerHTML = html;
        };

        const render = () => {
            const list = visible();
            const pages = Math.max(1, Math.ceil(list.length / state.per));
            if (state.page > pages) state.page = pages;
            const start = (state.page - 1) * state.per;
            const slice = list.slice(start, start + state.per);
            $('#trx-list').innerHTML = slice.length
                ? slice.map(row).join('')
                : `<p class="px-5 py-10 text-center text-[11px] font-medium text-slate-500">Tidak ada transaksi yang cocok dengan filter.</p>`;
            $('[data-shown]').textContent = slice.length;
            $('[data-total]').textContent = list.length;
            renderPager(Math.max(1, Math.ceil(list.length / state.per)));
            renderStats();
        };

        const on = ['bg-white', 'text-[#0B3A22]', 'shadow-sm'];
        const off = ['text-slate-600', 'hover:text-[#0B3A22]'];
        $$('[data-bank]').forEach((b) => b.addEventListener('click', () => {
            $$('[data-bank]').forEach((t) => { t.classList.remove(...on); t.classList.add(...off); });
            b.classList.remove(...off); b.classList.add(...on);
            state.bank = b.dataset.bank;
            state.page = 1;
            render();
        }));

        $('#search').addEventListener('input', (e) => { state.q = e.target.value.trim().toLowerCase(); state.page = 1; render(); });

        const menu = $('#status-menu');
        $('#status-btn').addEventListener('click', (e) => { e.stopPropagation(); menu.classList.toggle('hidden'); });
        document.addEventListener('click', () => menu.classList.add('hidden'));
        $$('[data-status]').forEach((b) => b.addEventListener('click', () => {
            state.status = b.dataset.status;
            $('#status-label').textContent = b.textContent.trim();
            menu.classList.add('hidden');
            state.page = 1;
            render();
        }));

        $('#pager').addEventListener('click', (e) => {
            const b = e.target.closest('[data-pg]');
            if (!b) return;
            const pages = Math.max(1, Math.ceil(visible().length / state.per));
            const v = b.dataset.pg;
            if (v === 'prev') state.page = Math.max(1, state.page - 1);
            else if (v === 'next') state.page = Math.min(pages, state.page + 1);
            else state.page = Number(v);
            render();
        });

        const detailLine = (label, val) => `<div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-2.5 last:border-0"><span class="shrink-0 text-[11px] text-slate-500">${label}</span><span class="text-right text-[12px] font-semibold text-slate-900">${val}</span></div>`;
        const openDetail = (h) => {
            state.viewing = h.id;
            $('#dt-body').innerHTML =
                detailLine('Kode Transaksi', esc(h.kode)) +
                detailLine('Pengguna', esc(h.name)) +
                detailLine('Unit', esc(h.unit)) +
                detailLine('Waktu', esc(h.date) + ' • ' + esc(h.t) + ' WIB') +
                detailLine('Metode', esc(h.metode)) +
                detailLine('Total Bayar', rp(h.total_bayar)) +
                detailLine('Jumlah Bayar', rp(h.jumlah_bayar)) +
                detailLine('Kembalian', rp(h.kembalian)) +
                detailLine('Status', pill(h.status)) +
                detailLine('Bukti', esc(h.bukti || '-'));
            openModal('trx-detail');
        };
        const byId = (id) => state.rows.find((r) => String(r.id) === String(id));

        $('#trx-list').addEventListener('click', (e) => {
            const v = e.target.closest('[data-view]');
            if (v) { const h = byId(v.dataset.view); if (h) openDetail(h); }
        });

        const FEED_URL = @json(route('admin.feed'));
        const refreshFeed = async () => {
            try {
                const res = await fetch(FEED_URL, { headers: { 'Accept': 'application/json' } });
                const json = await res.json();
                const data = json.data || json;
                if (Array.isArray(data.rows)) {
                    state.rows = data.rows;
                    if (state.viewing && !byId(state.viewing)) closeModal('trx-detail');
                    render();
                }
            } catch (e) { /* abaikan gangguan jaringan */ }
        };
        window.addEventListener('jiwanta:realtime', () => refreshFeed());

        render();
    });
</script>
@endpush
