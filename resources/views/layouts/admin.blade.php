@extends('layouts.master')

@section('body-class', 'bg-gray-100 overflow-hidden')

@php
    $ic = fn (string $d, string $c = 'h-4 w-4') =>
        '<svg class="'.$c.'" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="'.$d.'"/></svg>';

    $p = [
        'dashboard' => 'M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z',
        'receipt'   => 'M6 3h12v18l-3-2-3 2-3-2-3 2V3zM9 8h6M9 12h6',
        'ticket'    => 'M4 7h16v3a2 2 0 000 4v3H4v-3a2 2 0 000-4V7zM14 7v10',
        'cabin'     => 'M3 11l9-8 9 8M5 10v10h14V10M10 20v-5h4v5',
        'users'     => 'M17 20v-1a4 4 0 00-4-4H7a4 4 0 00-4 4v1M10 11a4 4 0 100-8 4 4 0 000 8zM21 20v-1a4 4 0 00-3-3.9M16 3.1a4 4 0 010 7.8',
        'chart'     => 'M5 20V10M12 20V4M19 20v-7',
        'gear'      => 'M12 15a3 3 0 100-6 3 3 0 000 6zM12 2v3M12 19v3M2 12h3M19 12h3M5 5l2 2M17 17l2 2M19 5l-2 2M7 17l-2 2',
        'bell'      => 'M6 8a6 6 0 0112 0c0 7 3 8 3 8H3s3-1 3-8M10 20a2 2 0 004 0',
        'clock'     => 'M12 8v4l2 2M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'logout'    => 'M15 4h4a1 1 0 011 1v14a1 1 0 01-1 1h-4M10 16l-4-4 4-4M6 12h10',
        'snow'      => 'M12 3v18M4.5 7.5l15 9M4.5 16.5l15-9M9 4l3 2 3-2M9 20l3-2 3 2',
        'tree'      => 'M12 3l4 6h-2.5l3.5 5h-3l3 5H7l3-5H7l3.5-5H8l4-6z',
        'shield'    => 'M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6l8-3zM9 12l2 2 4-4',
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
<div class="flex h-screen w-full overflow-hidden @yield('page-class', 'bg-slate-50')">

    @include('partials.admin.sidebar')

    <div class="flex-1 flex flex-col min-w-0">

        @include('partials.admin.topbar')

        <main id="scroller" class="flex-1 overflow-y-auto p-6">
            <div id="page-anim" class="mx-auto @yield('container-class', 'max-w-7xl space-y-6')">
                @yield('page-content')
            </div>
        </main>
    </div>
</div>

@yield('overlays')
@endsection
