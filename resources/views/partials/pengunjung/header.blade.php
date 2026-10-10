<div id="top-header" class="sticky top-0 z-40 bg-[#F9F8FF]/85 backdrop-blur transition-shadow duration-300">
    <header class="flex items-center justify-between px-5 py-3">
        <span class="text-[19px] font-bold tracking-tight text-[#0B3A22]">Jiwanta</span>
        <div class="flex items-center gap-3">
            <span class="text-[13px] font-medium text-slate-600">@yield('page-label', '')</span>
            <img src="{{ asset('images/profil.jpg') }}" alt="Profil" class="h-9 w-9 rounded-full bg-[#C9B99A] object-cover">
        </div>
    </header>
    @yield('subheader')
</div>
