@extends('layouts.master')

@section('body-class', 'bg-gray-100 overflow-hidden')

@php
    $ic = fn (string $d, string $c = 'h-4 w-4') =>
        '<svg class="'.$c.'" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="'.$d.'"/></svg>';

    $p = [
        'grid'    => 'M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z',
        'scan'    => 'M4 8V5a1 1 0 011-1h3M16 4h3a1 1 0 011 1v3M20 16v3a1 1 0 01-1 1h-3M8 20H5a1 1 0 01-1-1v-3M4 12h16',
        'receipt' => 'M6 3h12v18l-3-2-3 2-3-2-3 2V3zM9 8h6M9 12h6',
        'bell'    => 'M6 8a6 6 0 0112 0c0 7 3 8 3 8H3s3-1 3-8M10 20a2 2 0 004 0',
        'logout'  => 'M15 4h4a1 1 0 011 1v14a1 1 0 01-1 1h-4M10 16l-4-4 4-4M6 12h10',
        'tree'    => 'M12 3l4 6h-2.5l3.5 5h-3l3 5H7l3-5H7l3.5-5H8l4-6z',
        'users'   => 'M17 20v-1a4 4 0 00-4-4H7a4 4 0 00-4 4v1M10 11a4 4 0 100-8 4 4 0 000 8zM21 20v-1a4 4 0 00-3-3.9M16 3.1a4 4 0 010 7.8',
        'checkc'  => 'M9 12l2 2 4-4M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'camera'  => 'M4 8h3l2-3h6l2 3h3v11H4zM12 17a3.5 3.5 0 100-7 3.5 3.5 0 000 7z',
        'volume'  => 'M11 5L6 9H3v6h3l5 4V5zM15.5 8.5a5 5 0 010 7',
        'search'  => 'M11 18a7 7 0 100-14 7 7 0 000 14zM21 21l-4.5-4.5',
        'x'       => 'M6 6l12 12M18 6L6 18',
        'cloud'   => 'M7 18a4 4 0 010-8 5 5 0 019.6-1A4.5 4.5 0 0117 18H7z',
    ];
@endphp

@push('styles')
<style>
    #page-anim {
        animation: admin-page-in .38s cubic-bezier(.22, .61, .36, 1);
        transition: opacity .16s ease-in, transform .16s ease-in;
    }
    #page-anim.page-leave {
        animation: none;
        opacity: 0;
        transform: translateY(-12px);
    }
    @keyframes admin-page-in {
        from { opacity: 0; transform: translateY(14px); }
        to { opacity: 1; transform: none; }
    }
    @media (prefers-reduced-motion: reduce) {
        #page-anim, #page-anim.page-leave { animation: none; transition: none; opacity: 1; transform: none; }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const shell = document.getElementById('page-anim');

        window.addEventListener('pageshow', (e) => {
            if (e.persisted && shell) shell.classList.remove('page-leave');
        });
        if (!shell || reduce) return;

        document.querySelectorAll('aside a[href]').forEach((a) => {
            a.addEventListener('click', (e) => {
                if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
                let url;
                try { url = new URL(a.href, location.href); } catch (err) { return; }
                if (url.origin !== location.origin) return;
                if (url.href === location.href) { e.preventDefault(); return; }
                e.preventDefault();
                shell.classList.add('page-leave');
                setTimeout(() => { location.href = a.href; }, 160);
            });
        });
    });
</script>
@endpush

@section('content')
<div class="flex h-screen w-full overflow-hidden bg-[#F8F7FF] text-[#0B3A22]">

    <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">@csrf</form>

    {{-- ================= SIDEBAR (full desktop, selalu tampil) ================= --}}
    <aside class="flex w-[250px] shrink-0 flex-col border-r border-slate-100 bg-white">
        <div class="px-6 pt-7">
            <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#0B3A22] text-[#B5F0BE]">{!! $ic($p['tree'], 'h-5 w-5') !!}</span>
                <div>
                    <p class="text-[17px] font-bold leading-tight">Jiwanta Gate<br>Portal</p>
                </div>
            </div>
            <p class="mt-3 text-[10px] font-bold uppercase tracking-wide text-slate-600">Petugas &amp; Akses Masuk</p>
        </div>

        <nav class="mt-8 flex-1 px-5">
            <p class="mb-2 px-1 text-[10px] font-bold uppercase tracking-wider text-slate-500">Menu</p>
            <a href="#" aria-current="page" class="flex items-center gap-3 rounded-xl bg-[#0B3A22] px-3.5 py-3 text-[13px] font-semibold text-white shadow-sm">
                {!! $ic($p['grid'], 'h-4 w-4 shrink-0') !!} Dashboard
            </a>
        </nav>

        <div class="m-5 flex items-center justify-between rounded-2xl bg-[#E8ECFB] px-4 py-4">
            <div>
                <p class="text-[12px] font-bold leading-tight">Shift Pagi Gate</p>
                <p class="mt-0.5 text-[11px] leading-tight text-slate-700">07:00 - 15:00 WIB</p>
            </div>
            <button type="submit" form="logout-form" class="transition hover:text-red-600" aria-label="Keluar">{!! $ic($p['logout'], 'h-5 w-5') !!}</button>
        </div>
    </aside>

    {{-- ================= MAIN (scroll di dalam, full desktop) ================= --}}
    <div class="flex min-w-0 flex-1 flex-col">

        {{-- Topbar --}}
        <header class="z-20 flex h-16 shrink-0 items-center justify-between border-b border-slate-100 bg-white/90 px-6 backdrop-blur">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#0B3A22] text-[#B5F0BE]">{!! $ic($p['tree'], 'h-4 w-4') !!}</span>
                <span class="flex items-center gap-2 rounded-full bg-[#E4E8FB] px-4 py-2 text-[12px]">
                    <span class="relative flex h-2 w-2"><span class="absolute h-full w-full animate-ping rounded-full bg-[#0B3A22] opacity-60"></span><span class="relative h-2 w-2 rounded-full bg-[#0B3A22]"></span></span>
                    <b class="font-semibold">Gate Online</b>
                    <span class="hidden font-mono text-[12px] font-bold sm:inline" data-clock>--:--:--</span>
                    <span class="hidden text-[10px] text-slate-600 sm:inline">WIB</span>
                </span>
            </div>
            <div class="flex items-center gap-4">
                <button type="button" id="bell" class="relative flex h-10 w-10 items-center justify-center rounded-full bg-[#E4E8FB] transition hover:bg-[#d6dcf7]" aria-label="Pembayaran menunggu verifikasi">
                    {!! $ic($p['bell'], 'h-5 w-5') !!}
                    <span data-bell-count class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-bold text-white">0</span>
                </button>
                <span class="hidden h-8 w-px bg-slate-200 sm:block"></span>
                <div class="hidden text-right sm:block">
                    <p class="text-[13px] font-bold leading-tight">Rian Hidayat</p>
                    <p class="text-[10px] font-semibold uppercase leading-tight tracking-wide text-[#8B5E34]">Petugas Gate STF-042</p>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto px-6 py-5">
            <div id="page-anim" class="mx-auto max-w-7xl space-y-5">
                @yield('page-content')
            </div>
        </main>
    </div>
</div>

@yield('overlays')
@endsection
