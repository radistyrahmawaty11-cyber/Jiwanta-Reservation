@extends('layouts.master')

@section('body-class', 'min-h-screen flex justify-center p-0 sm:items-center sm:p-4')

@section('content')
<div class="min-h-screen w-full @yield('outer-class', 'bg-slate-200') @yield('page-class')">
    <div class="@yield('frame-class', 'relative mx-auto min-h-screen w-full max-w-[390px] overflow-x-clip bg-[#F9F8FF] pb-28 shadow-2xl') !max-w-none sm:!max-w-[390px] !shadow-none sm:!shadow-2xl">

        @include('partials.pengunjung.header')

        @yield('page-content')

        @include('partials.pengunjung.bottom-nav')
    </div>
</div>

@yield('overlays')
@endsection
