@extends('layouts.admin')

@section('title', 'Manajemen Pengguna - Jiwanta')

@section('page-content')

<style>
    @keyframes jw-rise{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}
    @keyframes jw-flash{0%{box-shadow:0 0 0 0 rgba(11,58,34,.35)}100%{box-shadow:0 0 0 14px rgba(11,58,34,0)}}
    @keyframes jw-pop{0%{transform:scale(.6);opacity:.4}70%{transform:scale(1.08)}100%{transform:scale(1);opacity:1}}
    [data-reveal]{opacity:0;transform:translateY(18px);transition:opacity .6s ease,transform .7s cubic-bezier(.2,.8,.2,1)}
    [data-reveal].in{opacity:1;transform:none}
    .jw-flash{animation:jw-flash .9s}
    .jw-pop{animation:jw-pop .4s}
    @media (prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}[data-reveal]{opacity:1;transform:none}}
</style>

@php
    $ic = fn (string $d, string $c = 'h-4 w-4') =>
        '<svg class="'.$c.'" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="'.$d.'"/></svg>';

    $p = [
        'users'    => 'M17 20v-1a4 4 0 00-4-4H7a4 4 0 00-4 4v1M10 11a4 4 0 100-8 4 4 0 000 8zM21 20v-1a4 4 0 00-3-3.9M16 3.1a4 4 0 010 7.8',
        'shield'   => 'M12 3l7 3v5c0 4.5-3 8-7 10-4-2-7-5.5-7-10V6l7-3z',
        'gate'     => 'M5 5v14M19 5v14M5 12h14M9 8l6 8',
        'search'   => 'M11 18a7 7 0 100-14 7 7 0 000 14zM21 21l-4.3-4.3',
        'userplus' => 'M15 20v-1a4 4 0 00-4-4H6a4 4 0 00-4 4v1M8.5 11a4 4 0 100-8 4 4 0 000 8zM19 8v6M16 11h6',
        'cloudup'  => 'M7 18a4 4 0 01-.5-8 5.5 5.5 0 0110.8 1.2A3.5 3.5 0 0117 18M12 12v7M9 14.5l3-3 3 3',
        'trend'    => 'M3 17l6-6 4 4 8-8M15 7h6v6',
        'sliders'  => 'M4 7h9M17 7h3M4 17h3M11 17h9M15 5v4M9 15v4',
        'x'        => 'M6 6l12 12M18 6L6 18',
        'check'    => 'M5 13l4 4L19 7',
        'trash'    => 'M5 7h14M10 7V4h4v3M7 7l1 13h8l1-13',
        'pencil'   => 'M4 20h4L19 9l-4-4L4 16v4zM13 7l4 4',
        'left'     => 'M15 6l-6 6 6 6',
        'right'    => 'M9 6l6 6-6 6',
    ];

    $mint = 'bg-[#CDEFD5]';
    $peach = 'bg-[#FBD9B0]';
    $lav = 'bg-[#DDE6FB]';
    $card = 'rounded-2xl bg-white shadow-sm border border-slate-100';

    $roleStyle = [
        'admin'    => ['bg-[#0B3A22] text-white', 'shield', 'Administrator Sistem'],
        'petugas'  => ['bg-[#FBD9B0] text-[#6B4520]', 'gate', 'Petugas Operasional'],
        'pengunjung' => ['bg-[#DDE6FB] text-slate-800', 'users', 'Pengunjung / Tamu'],
    ];
@endphp

                {{-- Header --}}
                <div class="flex items-start justify-between gap-6">
                    <div class="max-w-xl">
                        <p class="mb-2 flex items-center gap-2 text-[9px] font-bold uppercase tracking-wide">
                            <span class="rounded-md {{ $mint }} px-2 py-1 text-[#0B3A22]">Akun &amp; Hak Akses</span>
                        </p>
                        <h1 class="text-[30px] font-bold leading-tight text-[#0B3A22]">Manajemen Pengguna</h1>
                        <p class="mt-2 text-[13px] leading-relaxed text-slate-600">Kelola akun admin, petugas, dan pengunjung yang terdaftar pada sistem reservasi Jiwanta.</p>
                    </div>
                    <div class="flex flex-col items-end gap-3">
                        <button type="button" data-export class="flex items-center gap-2 rounded-full {{ $lav }} px-5 py-2.5 text-[11px] font-semibold text-[#0B3A22] transition hover:brightness-95">
                            {!! $ic($p['cloudup'], 'h-4 w-4') !!} Export CSV
                        </button>
                        <button type="button" data-add class="flex items-center gap-2 rounded-full bg-[#0B3A22] px-6 py-3 text-[11px] font-bold text-white shadow-sm transition hover:bg-[#124c2f]">
                            {!! $ic($p['userplus'], 'h-4 w-4') !!} + Tambah Pengguna
                        </button>
                    </div>
                </div>

                {{-- Filter Pills --}}
                <div class="flex flex-wrap items-center gap-2">
                    @php $pills = [['all', 'Semua', 'total'], ['admin', 'Admin', 'admin'], ['petugas', 'Petugas', 'petugas'], ['pengunjung', 'Pengunjung', 'pengunjung']]; @endphp
                    @foreach ($pills as [$k, $l, $c])
                        <button type="button" data-role="{{ $k }}" class="rounded-full px-4 py-2 text-[11px] font-semibold transition {{ $loop->first ? 'bg-[#0B3A22] text-white shadow-sm' : $lav.' text-slate-800 hover:brightness-95' }}">{{ $l }} (<span>{{ $stats[$c] }}</span>)</button>
                    @endforeach
                </div>

                {{-- Stats Cards --}}
                <div class="grid grid-cols-4 gap-4">
                    <div data-reveal class="{{ $card }} p-5 hover:shadow-md">
                        <div class="flex items-start justify-between">
                            <p class="text-[11px] text-slate-600">Total Akun Terdaftar</p>
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl {{ $lav }}">{!! $ic($p['users'], 'h-4 w-4') !!}</span>
                        </div>
                        <p class="mt-1 text-[34px] font-bold leading-tight text-[#0B3A22]">{{ $stats['total'] }} <span class="text-[13px] font-medium text-slate-600">Akun</span></p>
                    </div>

                    <div data-reveal class="{{ $card }} p-5 hover:shadow-md">
                        <div class="flex items-start justify-between">
                            <p class="text-[11px] text-slate-600">Akun Admin</p>
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl {{ $mint }}">{!! $ic($p['shield'], 'h-4 w-4') !!}</span>
                        </div>
                        <p class="mt-1 text-[34px] font-bold leading-tight text-[#0B3A22]">{{ $stats['admin'] }} <span class="text-[13px] font-medium text-slate-600">Admin</span></p>
                    </div>

                    <div data-reveal class="{{ $card }} p-5 hover:shadow-md">
                        <div class="flex items-start justify-between">
                            <p class="text-[11px] text-slate-600">Akun Petugas</p>
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl {{ $peach }} text-[#8B5E34]">{!! $ic($p['gate'], 'h-4 w-4') !!}</span>
                        </div>
                        <p class="mt-1 text-[34px] font-bold leading-tight text-[#7A5230]">{{ $stats['petugas'] }} <span class="text-[13px] font-medium text-slate-600">Petugas</span></p>
                    </div>

                    <div data-reveal class="{{ $card }} p-5 hover:shadow-md">
                        <div class="flex items-start justify-between">
                            <p class="text-[11px] text-slate-600">Pengunjung Terdaftar</p>
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl {{ $lav }}">{!! $ic($p['trend'], 'h-4 w-4') !!}</span>
                        </div>
                        <p class="mt-1 text-[34px] font-bold leading-tight text-[#0B3A22]">{{ $stats['pengunjung'] }} <span class="text-[13px] font-medium text-slate-600">Pengunjung</span></p>
                        <p class="mt-2 text-[10px] leading-snug text-slate-800">+{{ $stats['bulan_ini'] }} akun baru bulan ini</p>
                    </div>
                </div>

                {{-- Search --}}
                <div data-reveal class="{{ $card }} p-4">
                    <div class="flex items-center gap-3">
                        <label class="flex flex-1 items-center gap-3 rounded-full bg-[#F1F4FC] px-4 py-3 text-slate-500">
                            {!! $ic($p['search'], 'h-4 w-4 shrink-0') !!}
                            <input data-q type="text" placeholder="Cari nama, email, atau no. HP..." class="w-full bg-transparent text-[11px] text-slate-800 placeholder-slate-500 focus:outline-none">
                        </label>
                        <button type="button" data-reset class="flex h-10 w-10 items-center justify-center rounded-full {{ $lav }} text-slate-700 transition hover:brightness-95" aria-label="Reset filter">{!! $ic($p['sliders'], 'h-4 w-4') !!}</button>
                    </div>
                </div>

                {{-- Users Table --}}
                <section data-reveal class="{{ $card }} overflow-hidden shadow-md">
                    <div class="grid grid-cols-[minmax(210px,2.1fr)_120px_minmax(140px,1.2fr)_minmax(130px,1.1fr)_minmax(120px,1fr)] items-center gap-3 bg-[#E9EEFB] px-5 py-4 text-[10px] font-semibold text-slate-700">
                        <span>Pengguna</span><span class="leading-tight">No. HP</span><span>Role</span><span class="leading-tight">Terdaftar</span><span class="text-right">Aksi</span>
                    </div>
                    <div id="user-rows"></div>
                    <div class="flex items-center justify-between border-t border-slate-100 bg-slate-50/60 px-5 py-4">
                        <p data-showing class="text-[10px] text-slate-600"></p>
                        <div id="pager" class="flex items-center gap-1.5"></div>
                    </div>
                </section>
@endsection

@section('overlays')
    {{-- Modal Tambah/Edit --}}
    <div id="modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/40 p-4">
        <div class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-xl">
            <div class="flex items-start justify-between">
                <div>
                    <h3 data-m-title class="text-[16px] font-bold text-slate-900"></h3>
                    <p data-m-sub class="mt-0.5 text-[11px] text-slate-600"></p>
                </div>
                <button type="button" data-m-close class="rounded-lg p-1 text-slate-500 hover:bg-slate-100" aria-label="Tutup">{!! $ic($p['x'], 'h-5 w-5') !!}</button>
            </div>
            <div class="mt-5 space-y-4">
                <label class="block text-[10px] font-bold uppercase text-slate-600">Nama Lengkap
                    <input data-f="nama_pengguna" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-[12px] font-semibold focus:outline-none focus:ring-2 focus:ring-[#0B3A22]/30">
                </label>
                <label class="block text-[10px] font-bold uppercase text-slate-600">Email
                    <input data-f="email" type="email" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-[12px] font-semibold focus:outline-none focus:ring-2 focus:ring-[#0B3A22]/30">
                </label>
                <label class="block text-[10px] font-bold uppercase text-slate-600">No. HP
                    <input data-f="nohp" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-[12px] font-semibold focus:outline-none focus:ring-2 focus:ring-[#0B3A22]/30">
                </label>
                <label class="block text-[10px] font-bold uppercase text-slate-600">Role
                    <select data-f="role" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-[12px] font-semibold focus:outline-none focus:ring-2 focus:ring-[#0B3A22]/30">
                        <option value="pengunjung">Pengunjung</option>
                        <option value="petugas">Petugas</option>
                        <option value="admin">Admin</option>
                    </select>
                </label>
                <label class="block text-[10px] font-bold uppercase text-slate-600"><span data-pw-label>Password</span>
                    <input data-f="password" type="password" placeholder="Kosongkan jika tidak diubah" class="mt-1 w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-[12px] font-semibold focus:outline-none focus:ring-2 focus:ring-[#0B3A22]/30">
                </label>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button type="button" data-m-close class="rounded-lg {{ $lav }} px-4 py-2 text-[11px] font-bold">Batal</button>
                <button type="button" data-m-ok class="rounded-lg bg-[#0B3A22] px-5 py-2 text-[11px] font-bold text-white transition hover:bg-[#124c2f]">Simpan</button>
            </div>
        </div>
    </div>

    <div id="toasts" class="fixed bottom-6 right-6 z-[60] space-y-2"></div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const $ = (s, r = document) => r.querySelector(s);
        const $$ = (s, r = document) => [...r.querySelectorAll(s)];
        const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const fmt = (n) => Number(n).toLocaleString('id-ID');
        const esc = (v) => String(v ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

        const P = {
            left: 'M15 6l-6 6 6 6', right: 'M9 6l6 6-6 6',
            trash: 'M5 7h14M10 7V4h4v3M7 7l1 13h8l1-13',
            pencil: 'M4 20h4L19 9l-4-4L4 16v4zM13 7l4 4',
        };
        const ic = (d, c = 'h-4 w-4') => `<svg class="${c}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="${d}"/></svg>`;

        // Data asli dari database (server-side)
        let USERS = @json($penggunas->values());
        const CSRF = @json(csrf_token());
        const ROUTES = {
            store: @json(route('admin.pengguna.store')),
            update: (id) => @json(route('admin.pengguna.update', '__ID__')).replace('__ID__', id),
            destroy: (id) => @json(route('admin.pengguna.destroy', '__ID__')).replace('__ID__', id),
        };

        const ROLE = {
            admin:      { label: 'Admin', cls: 'bg-[#0B3A22] text-white' },
            petugas:    { label: 'Petugas', cls: 'bg-[#FBD9B0] text-[#6B4520]' },
            pengunjung: { label: 'Pengunjung', cls: 'bg-[#DDE6FB] text-slate-800' },
        };
        const AV = ['bg-[#DDE6FB]', 'bg-[#FBD9B0]', 'bg-[#CDEFD5]'];
        const MON = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const fmtDate = (iso) => {
            if (!iso) return '-';
            const d = new Date(iso);
            return d.getDate() + ' ' + MON[d.getMonth()] + ' ' + d.getFullYear();
        };

        // ---------- Toast ----------
        const toast = (msg) => {
            const t = document.createElement('div');
            t.className = 'relative flex items-center gap-2 overflow-hidden rounded-xl bg-[#0B3A22] px-4 py-3 text-[11px] font-semibold text-white shadow-lg opacity-0 translate-x-6 transition-all duration-300';
            t.innerHTML = '<span class="h-2 w-2 animate-pulse rounded-full bg-[#B5F0BE]"></span>' + esc(msg);
            $('#toasts').appendChild(t);
            requestAnimationFrame(() => t.classList.remove('opacity-0', 'translate-x-6'));
            setTimeout(() => { t.classList.add('opacity-0', 'translate-x-6'); setTimeout(() => t.remove(), 300); }, 3200);
        };

        // ---------- State ----------
        const state = { role: 'all', q: '', page: 1 };
        const PER_PAGE = 8;
        const matchU = (u) => {
            const q = state.q.trim().toLowerCase();
            return (!q || (u.nama_pengguna + ' ' + u.email + ' ' + (u.nohp || '')).toLowerCase().includes(q))
                && (state.role === 'all' || u.role === state.role);
        };

        // ---------- Render ----------
        const COLS = 'grid grid-cols-[minmax(210px,2.1fr)_120px_minmax(140px,1.2fr)_minmax(130px,1.1fr)_minmax(120px,1fr)]';
        const rowHTML = (u, i) => {
            const r = ROLE[u.role] || ROLE.pengunjung;
            const ini = (u.nama_pengguna || '?').split(' ').map((w) => w[0]).slice(0, 2).join('').toUpperCase();
            const bg = AV[(u.id + (u.nama_pengguna || '').length) % 3];
            const nohp = esc(u.nohp || '-').replace(/^(\d{4})(\d+)/, '$1-$2');
            return `<div data-row="${u.id}" class="${COLS} items-center gap-3 border-b border-slate-100 px-5 py-4 transition-colors last:border-0 hover:bg-slate-50" style="animation:jw-rise .4s cubic-bezier(.2,.8,.2,1) ${i * 40}ms backwards">
                <div class="flex min-w-0 items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full ${bg} text-[12px] font-bold text-slate-800">${ini}</span>
                    <div class="min-w-0">
                        <p class="truncate text-[14px] font-bold leading-tight text-slate-900">${esc(u.nama_pengguna)}</p>
                        <p class="mt-0.5 break-all text-[10px] text-slate-600">${esc(u.email)}</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold leading-snug text-slate-800">${nohp}</span>
                <span class="inline-flex w-fit items-center gap-1 rounded-[14px] ${r.cls} px-2.5 py-1 text-[9px] font-bold leading-tight">${r.label}</span>
                <span class="text-[10px] text-slate-600">${fmtDate(u.created_at)}</span>
                <div class="flex items-center justify-end gap-1.5">
                    <button type="button" data-act="edit" data-id="${u.id}" class="rounded-lg bg-[#DDE6FB] px-2.5 py-1.5 text-[9px] font-bold text-slate-800 transition hover:brightness-95">Edit</button>
                    <button type="button" data-act="del" data-id="${u.id}" class="rounded-lg bg-[#FBD5D5] px-2.5 py-1.5 text-[9px] font-bold text-red-700 transition hover:brightness-95">Hapus</button>
                </div>
            </div>`;
        };

        const pagerBtn = 'flex h-8 min-w-8 items-center justify-center rounded-lg px-2 text-[11px] font-bold transition';
        const renderPager = (pages) => {
            const pg = state.page;
            let list = [];
            if (pages <= 5) list = Array.from({ length: pages }, (_, i) => i + 1);
            else if (pg <= 3) list = [1, 2, 3, '…', pages];
            else if (pg >= pages - 2) list = [1, '…', pages - 2, pages - 1, pages];
            else list = [1, '…', pg - 1, pg, pg + 1, '…', pages];
            $('#pager').innerHTML =
                `<button type="button" data-pg="${pg - 1}" ${pg <= 1 ? 'disabled' : ''} class="${pagerBtn} bg-white text-slate-600 shadow-sm hover:bg-slate-100 disabled:opacity-40">${ic(P.left, 'h-3.5 w-3.5')}</button>` +
                list.map((x) => x === '…' ? '<span class="px-1 text-[11px] text-slate-500">...</span>'
                    : `<button type="button" data-pg="${x}" class="${pagerBtn} ${x === pg ? 'bg-[#0B3A22] text-white shadow-sm' : 'bg-white text-slate-700 shadow-sm hover:bg-slate-100'}">${x}</button>`).join('') +
                `<button type="button" data-pg="${pg + 1}" ${pg >= pages ? 'disabled' : ''} class="${pagerBtn} bg-white text-slate-600 shadow-sm hover:bg-slate-100 disabled:opacity-40">${ic(P.right, 'h-3.5 w-3.5')}</button>`;
        };

        const renderRows = () => {
            const list = USERS.filter(matchU);
            const pages = Math.max(1, Math.ceil(list.length / PER_PAGE));
            state.page = Math.min(state.page, pages);
            const rows = list.slice((state.page - 1) * PER_PAGE, state.page * PER_PAGE);
            $('#user-rows').innerHTML = rows.length
                ? rows.map((u, i) => rowHTML(u, i)).join('')
                : '<p class="p-10 text-center text-[12px] font-medium text-slate-500">Tidak ada pengguna yang cocok.</p>';
            $('[data-showing]').textContent = `Menampilkan ${rows.length} dari ${fmt(list.length)} pengguna`;
            renderPager(pages);
        };

        // ---------- Filter ----------
        $$('[data-role]').forEach((b) => b.addEventListener('click', () => {
            state.role = b.dataset.role; state.page = 1;
            $$('[data-role]').forEach((x) => {
                const on = x.dataset.role === state.role;
                x.className = 'rounded-full px-4 py-2 text-[11px] font-semibold transition ' + (on ? 'bg-[#0B3A22] text-white shadow-sm' : 'bg-[#DDE6FB] text-slate-800 hover:brightness-95');
            });
            renderRows();
        }));
        let tq;
        $('[data-q]').addEventListener('input', (e) => { clearTimeout(tq); tq = setTimeout(() => { state.q = e.target.value; state.page = 1; renderRows(); }, 150); });
        $('[data-reset]').addEventListener('click', () => {
            Object.assign(state, { role: 'all', q: '', page: 1 });
            $('[data-q]').value = '';
            $$('[data-role]').forEach((x, i) => {
                x.className = 'rounded-full px-4 py-2 text-[11px] font-semibold transition ' + (i === 0 ? 'bg-[#0B3A22] text-white shadow-sm' : 'bg-[#DDE6FB] text-slate-800 hover:brightness-95');
            });
            renderRows(); toast('Filter direset.');
        });
        $('#pager').addEventListener('click', (e) => {
            const b = e.target.closest('[data-pg]');
            if (!b || b.disabled) return;
            state.page = Number(b.dataset.pg); renderRows();
        });

        // ---------- Modal ----------
        const modal = $('#modal');
        let editingId = null, onOk = null;
        const val = (k) => ($(`[data-f="${k}"]`).value || '').trim();
        const setVal = (k, v) => { $(`[data-f="${k}"]`).value = v ?? ''; };
        const openModal = (title, sub, cb, hidePw) => {
            $('[data-m-title]').textContent = title;
            $('[data-m-sub]').textContent = sub;
            $('[data-f="password"]').closest('label').classList.toggle('hidden', !!hidePw);
            onOk = cb;
            modal.classList.remove('hidden'); modal.classList.add('flex');
            $('[data-f="nama_pengguna"]').focus();
        };
        const closeModal = () => { modal.classList.add('hidden'); modal.classList.remove('flex'); editingId = null; };
        $$('[data-m-close]').forEach((b) => b.addEventListener('click', closeModal));
        modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });

        // ---------- API helpers ----------
        const api = async (url, method, data) => {
            const res = await fetch(url, {
                method,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                body: JSON.stringify(data),
            });
            const body = await res.json().catch(() => ({}));
            if (!res.ok) {
                const msg = body.message || Object.values(body.errors || {}).flat().join(' ') || 'Terjadi kesalahan.';
                throw new Error(msg);
            }
            return body;
        };

        // ---------- Aksi ----------
        $('[data-add]').addEventListener('click', () => {
            editingId = null;
            ['nama_pengguna', 'email', 'nohp', 'password'].forEach((k) => setVal(k, ''));
            setVal('role', 'pengunjung');
            openModal('Tambah Pengguna', 'Akun baru tersimpan ke database', async () => {
                if (!val('nama_pengguna') || !val('email').includes('@') || !val('nohp') || !val('password')) {
                    toast('Semua kolom wajib diisi.'); return false;
                }
                try {
                    const r = await api(ROUTES.store, 'POST', {
                        nama_pengguna: val('nama_pengguna'), email: val('email'), nohp: val('nohp'),
                        role: val('role'), password: val('password'),
                    });
                    USERS.unshift(r.data);
                    state.page = 1; renderRows();
                    toast('Akun ' + r.data.nama_pengguna + ' dibuat.');
                    return true;
                } catch (err) { toast(err.message); return false; }
            });
        });

        const findUser = (id) => USERS.find((u) => String(u.id) === String(id));

        $('#user-rows').addEventListener('click', async (e) => {
            const b = e.target.closest('[data-act]');
            if (!b) return;
            const u = findUser(b.dataset.id);
            if (!u) return;

            if (b.dataset.act === 'edit') {
                editingId = u.id;
                setVal('nama_pengguna', u.nama_pengguna);
                setVal('email', u.email);
                setVal('nohp', u.nohp);
                setVal('role', u.role);
                setVal('password', '');
                openModal('Edit Pengguna', u.nama_pengguna, async () => {
                    if (!val('nama_pengguna') || !val('email').includes('@') || !val('nohp')) {
                        toast('Nama, email, dan no. HP wajib diisi.'); return false;
                    }
                    const payload = {
                        nama_pengguna: val('nama_pengguna'), email: val('email'), nohp: val('nohp'), role: val('role'),
                    };
                    if (val('password')) payload.password = val('password');
                    try {
                        const r = await api(ROUTES.update(editingId), 'PUT', payload);
                        const idx = USERS.findIndex((x) => String(x.id) === String(editingId));
                        if (idx > -1) USERS[idx] = r.data;
                        renderRows();
                        toast('Akun ' + r.data.nama_pengguna + ' diperbarui.');
                        return true;
                    } catch (err) { toast(err.message); return false; }
                }, true);
            } else if (b.dataset.act === 'del') {
                if (!confirm('Hapus akun "' + u.nama_pengguna + '"? Tindakan ini tidak bisa dibatalkan.')) return;
                try {
                    await api(ROUTES.destroy(u.id), 'DELETE', {});
                    USERS = USERS.filter((x) => String(x.id) !== String(u.id));
                    renderRows();
                    toast('Akun ' + u.nama_pengguna + ' dihapus.');
                } catch (err) { toast(err.message); }
            }
        });

        $('[data-m-ok]').addEventListener('click', async () => {
            const btn = $('[data-m-ok]');
            if (btn.disabled) return;
            btn.disabled = true;
            const ok = onOk ? await onOk() : true;
            btn.disabled = false;
            if (ok !== false) closeModal();
        });

        // ---------- Export CSV ----------
        $('[data-export]').addEventListener('click', () => {
            const rows = USERS.filter(matchU);
            const csv = ['Nama,Email,No HP,Role,Terdaftar'].concat(rows.map((u) =>
                [u.nama_pengguna, u.email, u.nohp, u.role, fmtDate(u.created_at)]
                    .map((x) => '"' + String(x ?? '').replace(/"/g, '""') + '"').join(','))).join('\n');
            const a = document.createElement('a');
            a.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8' }));
            a.download = 'pengguna-jiwanta.csv';
            a.click();
            setTimeout(() => URL.revokeObjectURL(a.href), 1000);
            toast(rows.length + ' pengguna diekspor ke CSV.');
        });

        // ---------- Reveal ----------
        const io = new IntersectionObserver((es) => es.forEach((en) => {
            if (!en.isIntersecting) return;
            en.target.classList.add('in');
            io.unobserve(en.target);
        }), { threshold: 0.06 });
        $$('[data-reveal]').forEach((el, i) => { el.style.transitionDelay = (i % 4) * 90 + 'ms'; io.observe(el); });

        // ---------- Init ----------
        renderRows();
    });
</script>
@endpush
