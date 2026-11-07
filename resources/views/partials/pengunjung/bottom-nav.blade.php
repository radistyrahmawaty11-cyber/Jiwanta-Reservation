@php
    $navItems = [
        [
            'route' => 'dashboard',
            'label' => 'Beranda',
            'icon' => '<svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M8 3l4.2 6.2h-2l3.3 4.6h-2.3L15 18.5H1l3.8-4.7H2.6L6 9.2H4z"/>
                <path d="M17 8l3.6 5.2h-1.8L22 17.5h-4.5v3H16v-3h-1.4z" opacity=".85"/>
                <path d="M7 18.5h2V21H7z"/>
            </svg>',
        ],
        [
            'route' => 'reservasi',
            'label' => 'Reservasi',
            'icon' => '<svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 11l9-7 9 7v9a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-9z"/>
            </svg>',
        ],
        [
            'route' => 'transaksi',
            'label' => 'Transaksi',
            'icon' => '<svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v17l2-1.2 2 1.2 2-1.2 2 1.2 2-1.2 2 1.2 2-1.2V4a2 2 0 00-2-2H6zm2 5h8v2H8V7zm0 4h8v2H8v-2zm0 4h5v2H8v-2z" clip-rule="evenodd"/>
            </svg>',
        ],
        [
            'route' => 'tiket',
            'label' => 'Tiket Saya',
            'icon' => '<svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
            </svg>',
        ],
    ];
@endphp

<nav class="fixed bottom-0 left-1/2 z-30 w-full max-w-none -translate-x-1/2 bg-white pb-[env(safe-area-inset-bottom,0px)] shadow-[0_-4px_16px_rgba(11,46,34,0.06)] sm:max-w-[390px]"
     style="view-transition-name: bottom-nav" aria-label="Navigasi utama">
    <div class="flex items-center justify-around px-2 pt-2.5 pb-3">
        @foreach ($navItems as $item)
            @php $isActive = request()->routeIs($item['route']); @endphp
            <a href="{{ route($item['route']) }}"
               @if($isActive) aria-current="page" @endif
               class="group relative flex flex-1 flex-col items-center gap-1 rounded-2xl px-1 py-1.5 transition-colors duration-300 ease-out {{ $isActive ? 'text-[#0B3A22]' : 'text-slate-400 hover:text-[#0B3A22]/70' }}">
                <span class="pointer-events-none absolute inset-x-1.5 inset-y-0 rounded-2xl bg-[rgba(11,58,34,0.07)] transition-all duration-300 ease-out {{ $isActive ? 'scale-100 opacity-100' : 'scale-75 opacity-0' }}"></span>
                <span class="relative flex h-6 w-6 items-center justify-center transition-transform duration-300 ease-out {{ $isActive ? '-translate-y-px scale-110' : 'group-hover:scale-105 group-active:scale-90' }}">
                    {!! $item['icon'] !!}
                </span>
                <span class="relative text-[10.5px] leading-none transition-all duration-300 {{ $isActive ? 'font-extrabold' : 'font-bold' }}">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </div>
</nav>
