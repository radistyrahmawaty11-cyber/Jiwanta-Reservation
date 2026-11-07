@extends('layouts.master')

@section('body-class', 'min-h-screen flex justify-center p-0 sm:items-center sm:p-4')

@section('content')
<div class="min-h-screen w-full @yield('outer-class', 'bg-slate-200') @yield('page-class')">
    <div class="@yield('frame-class', 'relative mx-auto min-h-screen w-full max-w-[390px] overflow-x-clip bg-[#F9F8FF] pb-28 shadow-2xl') !max-w-none sm:!max-w-[390px] !shadow-none sm:!shadow-2xl">

        @include('partials.pengunjung.header')

        <div class="jw-page">
            @yield('page-content')
        </div>

        @include('partials.pengunjung.bottom-nav')
    </div>
</div>

@yield('overlays')
@endsection

@push('styles')
<style>
    /* Transisi antar halaman pengunjung (Chrome/Edge: cross-document view transition) */
    @view-transition {
        navigation: auto;
    }

    /* Fade-in lembut saat halaman pengunjung dibuka / berpindah menu */
    .jw-page {
        animation: jw-page-in .28s ease-out both;
    }

    @keyframes jw-page-in {
        from { opacity: 0; }
        to   { opacity: 1; }
    }

    /* Bottom nav tidak ikut bergeser saat transisi halaman */
    ::view-transition-old(bottom-nav),
    ::view-transition-new(bottom-nav) {
        animation: none;
    }

    ::view-transition-old(root) {
        animation: jw-page-out .16s ease-in both;
    }

    ::view-transition-new(root) {
        animation: jw-page-slide-in .28s ease-out both;
    }

    @keyframes jw-page-out {
        to { opacity: 0; }
    }

    @keyframes jw-page-slide-in {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    @media (prefers-reduced-motion: reduce) {
        .jw-page { animation: none; }
        ::view-transition-old(root),
        ::view-transition-new(root) { animation: none; }
    }
</style>
@endpush
