<nav class="fixed bottom-0 left-1/2 z-30 w-full max-w-[390px] -translate-x-1/2 bg-white pb-[env(safe-area-inset-bottom,0px)] shadow-[0_-4px_16px_rgba(11,46,34,0.06)]" aria-label="Navigasi utama">
    <div class="flex items-center justify-around px-2 pt-3 pb-3.5">
        <a href="{{ route('dashboard') }}" @if(request()->routeIs('dashboard')) aria-current="page" @endif class="flex flex-1 flex-col items-center gap-1 text-[#0b2e22]">
            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M8 3l4.2 6.2h-2l3.3 4.6h-2.3L15 18.5H1l3.8-4.7H2.6L6 9.2H4z"/>
                <path d="M17 8l3.6 5.2h-1.8L22 17.5h-4.5v3H16v-3h-1.4z" opacity=".85"/>
                <path d="M7 18.5h2V21H7z"/>
            </svg>
            <span class="text-[10.5px] {{ request()->routeIs('dashboard') ? 'font-extrabold' : 'font-bold' }}">Beranda</span>
        </a>
        <a href="{{ route('reservasi') }}" @if(request()->routeIs('reservasi')) aria-current="page" @endif class="flex flex-1 flex-col items-center gap-1 text-[#0b2e22]">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 11l9-7 9 7v9a1 1 0 01-1 1h-5v-6H9v6H4a1 1 0 01-1-1v-9z"/>
            </svg>
            <span class="text-[10.5px] {{ request()->routeIs('reservasi') ? 'font-extrabold' : 'font-bold' }}">Reservasi</span>
        </a>
        <a href="{{ route('transaksi') }}" @if(request()->routeIs('transaksi')) aria-current="page" @endif class="flex flex-1 flex-col items-center gap-1 text-[#0b2e22]">
            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v17l2-1.2 2 1.2 2-1.2 2 1.2 2-1.2 2 1.2 2-1.2V4a2 2 0 00-2-2H6zm2 5h8v2H8V7zm0 4h8v2H8v-2zm0 4h5v2H8v-2z" clip-rule="evenodd"/>
            </svg>
            <span class="text-[10.5px] {{ request()->routeIs('transaksi') ? 'font-extrabold' : 'font-bold' }}">Transaksi</span>
        </a>
        <a href="{{ route('tiket') }}" @if(request()->routeIs('tiket')) aria-current="page" @endif class="flex flex-1 flex-col items-center gap-1 text-[#0b2e22]">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
            </svg>
            <span class="text-[10.5px] {{ request()->routeIs('tiket') ? 'font-extrabold' : 'font-bold' }}">Tiket Saya</span>
        </a>
    </div>
</nav>
