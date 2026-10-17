<header class="h-[60px] bg-white border-b border-slate-200 px-6 flex items-center justify-between shrink-0">
    <div class="flex items-center gap-2">
        <p class="text-[11px] text-slate-600">Sistem Jiwanta</p>
        <span class="text-slate-400">/</span>
        <b class="text-[11px] font-semibold text-[#0B3A22]">Panel Kendali Utama</b>
    </div>
    <div class="flex items-center gap-3">
        <span class="flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1.5 text-[10px] font-medium">{!! $ic($p['snow'], 'h-3.5 w-3.5') !!} Ciwidey 18&deg;C Kabut Sejuk</span>
        <span class="flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-[10px] font-medium text-emerald-700">
            <span class="relative flex h-2 w-2"><span class="absolute h-full w-full animate-ping rounded-full bg-emerald-600 opacity-60"></span><span data-ws-dot class="relative h-2 w-2 rounded-full bg-emerald-500"></span></span>
            <span data-ws-text>Menghubungkan&hellip;</span>
        </span>
        <div class="relative" data-notif-root>
            <button type="button" data-bell class="relative h-10 w-10 flex items-center justify-center rounded-full bg-slate-100 transition hover:bg-slate-200" aria-label="Notifikasi">
                {!! $ic($p['bell'], 'h-5 w-5') !!}
                <span data-notif-count class="absolute -right-1 -top-1 hidden min-w-[18px] h-[18px] items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-bold text-white border-2 border-white">0</span>
            </button>

            <div data-notif-panel class="hidden absolute right-0 mt-2 w-80 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl z-50">
                <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                    <p class="text-xs font-bold text-slate-800">Notifikasi Pesanan</p>
                    <span class="flex items-center gap-1.5 text-[10px] font-medium text-slate-400">
                        <span data-notif-status-dot class="h-1.5 w-1.5 rounded-full bg-slate-300"></span>
                        <span data-notif-status-text>Menghubungkan&hellip;</span>
                    </span>
                </div>
                <div data-notif-list class="max-h-80 divide-y divide-slate-100 overflow-y-auto">
                    <p data-notif-empty class="px-4 py-6 text-center text-xs text-slate-400">Belum ada pesanan baru</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-3 pl-3 border-l border-slate-200">
            @php
                $authUser = auth()->user();
                $avatarUrl = $authUser && $authUser->foto
                    ? \Illuminate\Support\Facades\Storage::url($authUser->foto)
                    : asset('images/profil.jpg');
            @endphp
            <img data-avatar-mini src="{{ $avatarUrl }}" alt="Profil" class="h-10 w-10 rounded-full bg-[#C9B99A] object-cover border-2 border-white shadow-sm">
            <div>
                <p data-name-mini class="text-[11px] font-bold leading-tight">{{ $authUser->nama_pengguna ?? 'Pengguna' }}</p>
                <p class="text-[9px] font-medium uppercase leading-tight text-slate-600">{{ ucfirst($authUser->role ?? 'admin') }} Resort</p>
            </div>
        </div>
    </div>
</header>
