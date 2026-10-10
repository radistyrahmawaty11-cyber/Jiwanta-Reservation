@php
    $mint  = 'bg-[#CDEFD5]';
    $peach = 'bg-[#FBD9B0]';

    $sideNav = [
        'Navigasi Utama' => [
            ['Dashboard', 'dashboard', 'admin.dashboard', null],
            ['Verifikasi', 'shield', 'verifikasi', 14],
            ['Transaksi', 'receipt', 'admin.transaksi', null],
        ],
        'Manajemen Resort' => [
            ['Kelola Tiket', 'ticket', 'tiket-renang', null],
            ['Kelola Cabin Suite', 'cabin', 'cabin', null],
        ],
        'Administrasi & Sistem' => [
            ['Manajemen Pengguna', 'users', 'manajemen-pengguna', null],
            ['Laporan & Export', 'chart', 'laporan', null],
            ['Pengaturan Profil', 'gear', 'pengaturan', null],
        ],
    ];
@endphp
<aside class="w-[240px] shrink-0 bg-white border-r border-slate-200 flex flex-col">
    <div class="px-5 pt-6 pb-4">
        <div class="flex items-center gap-3">
            <span class="flex h-[26px] w-[26px] items-center justify-center rounded-lg bg-[#0B3A22] text-[#B5F0BE]">{!! $ic($p['tree'], 'h-4 w-4') !!}</span>
            <div>
                <p class="text-[13px] font-bold leading-tight tracking-wide">JIWANTA</p>
                <p class="text-[8px] font-semibold uppercase leading-tight tracking-wider text-slate-600">Ciwidey Resort</p>
            </div>
        </div>
        <span class="mt-4 inline-flex items-center gap-2 rounded-full {{ $mint }} px-3 py-1.5 text-[10px] font-bold">
            <span class="h-2 w-2 rounded-full bg-[#0B3A22]"></span> Sistem Operasional Aktif
        </span>
    </div>

    <nav class="flex-1 px-4 space-y-6 overflow-y-auto">
        @foreach ($sideNav as $label => $items)
            <div>
                <p class="mb-2 px-2 text-[9px] font-bold uppercase tracking-wider text-slate-500">{{ $label }}</p>
                <ul class="space-y-1">
                    @foreach ($items as [$text, $icon, $routeName, $badge])
                        @php $active = request()->routeIs($routeName); @endphp
                        <li>
                            <a href="{{ route($routeName) }}" class="flex h-[36px] items-center gap-3 rounded-lg px-3 text-[13px] font-medium transition {{ $active ? 'bg-[#0B3A22] text-white' : 'text-slate-700 hover:bg-slate-50' }}">
                                {!! $ic($p[$icon], 'h-4 w-4 shrink-0') !!}
                                <span class="flex-1">{{ $text }}</span>
                                @if ($badge)
                                    <span data-c="pending" data-pending-badge class="rounded-full {{ $peach }} px-2 py-0.5 text-[10px] font-bold text-[#6B4520]">{{ $badge }}</span>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </nav>

    <div class="border-t border-slate-200 p-4">
        <div class="mb-3 flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
            <span class="flex items-center gap-2 text-[10px] font-semibold text-slate-700">{!! $ic($p['clock'], 'h-3.5 w-3.5') !!} Shift Pagi 07:00 - 15:00</span>
            <span class="h-2 w-2 rounded-full bg-[#0B3A22]"></span>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-lg bg-red-50 py-2.5 text-[11px] font-bold text-red-700 transition hover:bg-red-100">
                {!! $ic($p['logout'], 'h-4 w-4') !!} Keluar Sistem
            </button>
        </form>
    </div>
</aside>
