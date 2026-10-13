<div id="top-header" class="sticky top-0 z-40 bg-[#F9F8FF]/85 backdrop-blur transition-shadow duration-300">
    <header class="flex items-center justify-between px-5 py-3">
        <span class="text-[19px] font-bold tracking-tight text-[#0B3A22]">Jiwanta</span>
        <div class="flex items-center gap-3">
            <span class="text-[13px] font-medium text-slate-600">@yield('page-label', '')</span>
            <a href="{{ route('profil') }}" aria-label="Pengaturan profil" class="transition active:scale-95">
                <img src="{{ auth()->user()->fotoUrl() }}" alt="Profil" class="h-9 w-9 rounded-full bg-[#C9B99A] object-cover ring-2 {{ request()->routeIs('profil') ? 'ring-[#0B3A22]' : 'ring-transparent' }}">
            </a>
        </div>
    </header>
    @yield('subheader')
</div>
