<header class="h-[60px] bg-white border-b border-slate-200 px-6 flex items-center justify-between shrink-0">
    <div class="flex items-center gap-2">
        <p class="text-[11px] text-slate-600">Sistem Jiwanta</p>
        <span class="text-slate-400">/</span>
        <b class="text-[11px] font-semibold text-[#0B3A22]">Panel Kendali Utama</b>
    </div>
    <div class="flex items-center gap-3">
        <span class="flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1.5 text-[10px] font-medium">{!! $ic($p['snow'], 'h-3.5 w-3.5') !!} Ciwidey 18&deg;C Kabut Sejuk</span>
        <span class="flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-[10px] font-medium text-emerald-700">
            <span class="relative flex h-2 w-2"><span class="absolute h-full w-full animate-ping rounded-full bg-emerald-600 opacity-60"></span><span class="relative h-2 w-2 rounded-full bg-emerald-600"></span></span>
            Gate Turnstile Online
        </span>
        <button type="button" data-bell class="relative h-10 w-10 flex items-center justify-center rounded-full bg-slate-100 transition hover:bg-slate-200" aria-label="Notifikasi">
            {!! $ic($p['bell'], 'h-5 w-5') !!}
            <span class="absolute right-2 top-2 h-2.5 w-2.5 rounded-full bg-red-600 border-2 border-white"></span>
        </button>
        <div class="flex items-center gap-3 pl-3 border-l border-slate-200">
            <img data-avatar-mini src="{{ asset('images/profil.jpg') }}" alt="Profil" class="h-10 w-10 rounded-full bg-[#C9B99A] object-cover border-2 border-white shadow-sm">
            <div>
                <p data-name-mini class="text-[11px] font-bold leading-tight">Bagas Dananjaya</p>
                <p class="text-[9px] font-medium uppercase leading-tight text-slate-600">Super Admin Resort</p>
            </div>
        </div>
    </div>
</header>
