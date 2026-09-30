@extends('layouts.dashboard')

@section('title', 'Beranda - Jiwanta')

@section('content')

@php
    $reveal   = 'opacity-0 translate-y-8 transition duration-700 ease-out';
    $revealSm = 'opacity-0 translate-y-3 transition duration-500 ease-out';
    $delays   = ['delay-100', 'delay-200', 'delay-300'];
    $classic = ['Therapeutic Pool, The Cave', 'The Canyon, Swimming Pool', 'Hydrotherapy Pool, Kids Pool, dan Pool Deck'];
    $premier = ['Kolam Rendam & Kolam Sender', 'Barrel Pool & Onses', 'Termasuk Seluruh Fasilitas Area Classic'];

    $cabins = [
        [
            'name'     => 'Shorts',
            'image'    => asset('images/cabin-shorts.jpg'),
            'capacity' => null,
            'price'    => 'IDR 340.000',
            'amount'   => 340000,
            'unit'     => '/3 hours',
            'features' => ['Free Breakfast 4 Pax', 'Private Warm Jacuzzi', 'High Speed Wi-Fi'],
        ],
        [
            'name'     => 'Suite',
            'image'    => asset('images/cabin-suite.jpg'),
            'capacity' => 'Kapasitas 4-6 orang',
            'price'    => 'IDR 1.040.000',
            'amount'   => 1040000,
            'unit'     => '/malam',
            'features' => ['Outdoor Fire Pit', 'Balcony View Kabut', 'Coffee Maker Set'],
        ],
    ];

    $nav = [
        ['label' => 'Beranda',   'href' => route('dashboard'), 'active' => true,  'icon' => 'M12 3l4 6h-2.5l3.5 5h-3l3 5H7l3-5H7l3.5-5H8l4-6zM11 19h2v3h-2z'],
        ['label' => 'Reservasi', 'href' => '#', 'active' => false, 'icon' => 'M3 11l9-8 9 8M5 10v10h5v-6h4v6h5V10'],
        ['label' => 'Transaksi', 'href' => '#', 'active' => false, 'icon' => 'M6 3h12v18l-3-2-3 2-3-2-3 2V3zM9 8h6M9 12h6'],
        ['label' => 'Tiket Saya', 'href' => '#', 'active' => false, 'icon' => 'M4 7h16v3a2 2 0 000 4v3H4v-3a2 2 0 000-4V7zM14 7v10'],
    ];
@endphp

<div class="min-h-screen w-full bg-slate-200">
    <div class="relative mx-auto min-h-screen w-full max-w-md bg-[#F9F8FF] pb-28 font-sans shadow-2xl">

        {{-- ========== TOPBAR ========== --}}
        <header class="flex items-center justify-between bg-[#F9F8FF] px-5 py-3">
            <span class="text-lg font-bold text-[#0B3A22]">Jiwanta</span>
            <div class="flex items-center gap-3">
                <span class="text-xs font-medium text-slate-600">Beranda</span>
                <img src="{{ asset('images/profil.jpg') }}" alt="Profil"
                     class="h-9 w-9 rounded-full bg-[#C9B99A] object-cover">
            </div>
        </header>

        {{-- ========== HERO ========== --}}
        <section id="hero" class="relative overflow-hidden rounded-b-[40px] bg-gradient-to-b from-[#0B3A22] via-[#0F4527] to-[#0B3A22] px-5 pb-16 pt-5 text-white">
            <div data-hero-inner class="will-change-transform">
            <div data-reveal class="{{ $reveal }} delay-100 mb-5 inline-flex items-center gap-1.5 rounded-full border border-white/25 bg-white/10 px-3 py-1.5">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 18a4 4 0 010-8 5 5 0 019.6-1A4.5 4.5 0 0117 18H7z"/>
                </svg>
                <span class="text-[10px] font-medium uppercase tracking-wide">Ciwidey, 18°C Sejuk &amp; Berkabut</span>
            </div>

            <h1 data-reveal class="{{ $reveal }} delay-200 text-[28px] font-bold leading-tight">
                Selamat Datang di<br>
                <span class="text-[#B5F0BE]">Jiwanta Ciwidey</span>
            </h1>
            <p data-reveal class="{{ $reveal }} delay-300 mt-3 text-[13px] leading-relaxed text-white/80">
                Harmoni Alam Pegunungan &amp; Relaksasi<br>Sempurna di tengah keasrian hutan pinus.
            </p>

            <div class="mt-6 grid grid-cols-2 gap-3">
                {{-- Tiket Renang --}}
                <div data-reveal class="{{ $reveal }} delay-500 relative rounded-2xl bg-white px-3 pb-3.5 pt-5 text-center text-[#0B3A22]">
                    <span class="absolute -top-2.5 right-3 rounded-b-md bg-[#8B5E34] px-2.5 py-0.5 text-[9px] font-semibold text-white">Populer</span>
                    <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-[#E4E8DD]">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 20c1.5 0 1.5-1 3-1s1.5 1 3 1 1.5-1 3-1 1.5 1 3 1 1.5-1 3-1M9 8c0-2 1-3 3-3s3 1 3 3M8 12c1.3-1 2.7-1 4 0s2.7 1 4 0"/>
                        </svg>
                    </div>
                    <h3 class="text-[13px] font-bold">Tiket Renang</h3>
                    <p class="text-[11px] text-slate-500">Air Hangat Alami</p>
                </div>

                {{-- Cabin Suite --}}
                <div data-reveal class="{{ $reveal }} delay-700 relative rounded-2xl bg-white px-3 pb-3.5 pt-5 text-center text-[#0B3A22]">
                    <span class="absolute -top-2.5 right-3 rounded-b-md bg-[#E9C79F] px-2.5 py-0.5 text-[9px] font-semibold text-[#6B4520]">Best View</span>
                    <div class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-[#EADFCB]">
                        <svg class="h-5 w-5 text-[#8B5E34]" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 11l9-7 9 7M5 10v10h14V10M9 20v-6h6v6"/>
                        </svg>
                    </div>
                    <h3 class="text-[13px] font-bold">Cabin Suite</h3>
                    <p class="text-[11px] text-slate-500">Vila Kayu Pinus</p>
                </div>
            </div>
            </div>
        </section>

        {{-- ========== INFO SUHU (menimpa hero) ========== --}}
        <div class="relative z-10 -mt-5 px-5">
            <div data-reveal class="{{ $reveal }} delay-300 flex items-center justify-between rounded-full bg-white px-4 py-2.5 shadow-md">
                <div class="flex items-center gap-2">
                    <span class="relative flex h-2 w-2"><span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-[#0B3A22] opacity-60"></span><span class="relative inline-flex h-2 w-2 rounded-full bg-[#0B3A22]"></span></span>
                    <span class="text-[11px] font-medium text-[#0B3A22]">Suhu Kolam Vulkanik: 38°C – 40°C</span>
                </div>
                <span class="text-[10px] font-bold text-[#8B5E34]">Buka Hari Ini</span>
            </div>
        </div>

        <main class="px-5">

            {{-- ========== TIKET KOLAM HANGAT ========== --}}
            <section class="mt-8">
                <div data-reveal class="{{ $reveal }} mb-4 flex items-end justify-between">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-[#8B5E34]">Relaksasi Tubuh</p>
                        <h2 class="text-2xl font-bold text-[#0B3A22]">Tiket Kolam Hangat</h2>
                    </div>
                    <svg class="mb-1 h-6 w-6 text-[#0B3A22]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 19c1.5 0 1.5-1 3-1s1.5 1 3 1 1.5-1 3-1 1.5 1 3 1 1.5-1 3-1 1.5 1 3 1M3 15c1.5 0 1.5-1 3-1s1.5 1 3 1 1.5-1 3-1 1.5 1 3 1 1.5-1 3-1 1.5 1 3 1M15 6a1.5 1.5 0 100-3 1.5 1.5 0 000 3zM6 11l5-3 4 3"/>
                    </svg>
                </div>

                {{-- Classic --}}
                <article data-reveal class="{{ $reveal }} mb-4 rounded-2xl bg-white p-4 shadow-[0_4px_16px_rgba(15,69,39,0.08)]">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="inline-block rounded-md bg-[#E6E8FB] px-2 py-0.5 text-[10px] font-semibold text-[#3B4A8C]">Harian / Weekend</span>
                            <h3 class="mt-2 text-xl font-bold text-[#0B3A22]">Tiket Classic</h3>
                        </div>
                        <div class="text-right">
                            <p class="text-[11px] text-slate-600">Mulai</p>
                            <p data-count="60000" data-prefix="IDR " class="text-xl font-bold leading-tight text-[#0B3A22]">IDR 60.000</p>
                            <p class="text-[11px] text-slate-600">/ Tiket Dewasa</p>
                        </div>
                    </div>
                    <ul class="mt-4 space-y-2.5">
                        @foreach ($classic as $item)
                            <li data-reveal class="{{ $revealSm }} {{ $delays[$loop->index] }} flex items-center gap-2.5 text-[13px] text-slate-800">
                                <span class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-[#0B3A22]">
                                    <svg class="h-2.5 w-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                {{ $item }}
                            </li>
                        @endforeach
                    </ul>
                </article>

                {{-- Premier --}}
                <article data-reveal class="{{ $reveal }} relative overflow-hidden rounded-2xl bg-white p-4 shadow-[0_4px_16px_rgba(15,69,39,0.08)]">
                    {{-- Ribbon Best Seller --}}
                    <div class="absolute right-[-34px] top-[14px] w-[120px] rotate-45 bg-[#8B5E34] py-1 text-center text-[9px] font-bold tracking-wider text-white">
                        BEST SELLER
                    </div>

                    <div class="flex items-start justify-between pr-8">
                        <div>
                            <span class="inline-block rounded-md bg-[#FBE3C8] px-2 py-0.5 text-[10px] font-semibold text-[#8B5E34]">Akses Eksklusif</span>
                            <h3 class="mt-2 text-xl font-bold text-[#0B3A22]">Tiket Premier</h3>
                        </div>
                        <div class="text-right">
                            <p class="text-[11px] text-slate-600">Mulai</p>
                            <p data-count="75000" data-prefix="IDR " class="text-xl font-bold leading-tight text-[#8B5E34]">IDR 75.000</p>
                            <p class="text-[11px] text-slate-600">/ Tiket Dewasa</p>
                        </div>
                    </div>
                    <ul class="mt-4 space-y-2.5">
                        @foreach ($premier as $item)
                            <li data-reveal class="{{ $revealSm }} {{ $delays[$loop->index] }} flex items-center gap-2.5 text-[13px] text-slate-800 {{ $loop->last ? 'font-semibold' : '' }}">
                                <span class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-[#8B5E34]">
                                    <svg class="h-2.5 w-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </span>
                                {{ $item }}
                            </li>
                        @endforeach
                    </ul>
                </article>
            </section>

            {{-- ========== CABIN ========== --}}
            <section class="mt-10">
                <div data-reveal class="{{ $reveal }} mb-4 flex items-end justify-between">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-[#8B5E34]">Menginap &amp; Santai</p>
                        <h2 class="text-2xl font-bold text-[#0B3A22]">Cabin</h2>
                    </div>
                    <svg class="mb-1 h-6 w-6 text-[#0B3A22]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 11l9-8 9 8M5 10v10h14V10M10 20v-5h4v5"/>
                    </svg>
                </div>

                <div class="space-y-4">
                    @foreach ($cabins as $cabin)
                        <article data-reveal class="{{ $reveal }} overflow-hidden rounded-2xl bg-white shadow-[0_4px_16px_rgba(15,69,39,0.08)]">
                            <div class="relative h-[148px] bg-slate-300">
                                <img src="{{ asset('images/cabin-shorts.jpg') }}" alt="Cabin {{ $cabin['name'] }}" data-parallax-img style="transform: scale(1.15)" class="h-full w-full object-cover will-change-transform">

                                @if ($cabin['capacity'])
                                    <span class="absolute left-3 top-3 inline-flex items-center gap-1 rounded-full bg-white/95 px-2.5 py-1 text-[10px] font-semibold text-slate-800">
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21s-7-4.5-9-9a5 5 0 019-3 5 5 0 019 3c-2 4.5-9 9-9 9z"/></svg>
                                        {{ $cabin['capacity'] }}
                                    </span>
                                @endif

                                <span class="absolute right-3 top-3 inline-flex items-center gap-1.5 rounded-full bg-[#D5F5DC] px-2.5 py-1 text-[10px] font-semibold text-[#0B3A22]">
                                    <span class="h-1.5 w-1.5 rounded-full bg-[#0B3A22]"></span>
                                    Tersedia
                                </span>
                            </div>

                            <div class="p-4">
                                <div class="flex items-start justify-between">
                                    <h3 class="text-lg font-bold text-[#0B3A22]">{{ $cabin['name'] }}</h3>
                                    <div class="text-right">
                                        <p data-count="{{ $cabin['amount'] }}" data-prefix="IDR " class="text-lg font-bold leading-tight text-[#0B3A22]">{{ $cabin['price'] }}</p>
                                        <p class="text-[11px] text-slate-600">{{ $cabin['unit'] }}</p>
                                    </div>
                                </div>

                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach ($cabin['features'] as $feature)
                                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-[#EEF0FB] px-2.5 py-1.5 text-[10px] font-semibold text-slate-700">
                                            <span class="h-2.5 w-2.5 rounded-full bg-[#8B5E34]/70"></span>
                                            {{ $feature }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            {{-- ========== CTA ========== --}}
            <section data-reveal class="{{ $reveal }} mt-6 flex items-center justify-between rounded-2xl bg-[#0B3A22] px-4 py-3.5 text-white">
                <div>
                    <p class="text-[9px] font-semibold uppercase tracking-wider text-white/70">Rencanakan Liburan</p>
                    <p class="text-base font-bold">Siap Refreshing?</p>
                </div>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-full bg-[#FDD9B5] px-5 py-3 text-sm font-bold text-[#0B3A22] transition hover:bg-[#fbcb9a] active:scale-95">
                    Pesan Sekarang
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M13 6l6 6-6 6"/></svg>
                </a>
            </section>
        </main>

        {{-- ========== BOTTOM NAV ========== --}}
        <nav class="fixed bottom-0 left-1/2 z-30 w-full max-w-md -translate-x-1/2 border-t border-slate-100 bg-white px-4 pb-3 pt-2.5 shadow-[0_-4px_20px_rgba(0,0,0,0.05)]">
            <ul class="flex items-center justify-between">
                @foreach ($nav as $item)
                    <li class="flex-1">
                        <a href="{{ $item['href'] }}"
                           class="flex flex-col items-center gap-1 transition active:scale-90 text-[10px] font-bold {{ $item['active'] ? 'text-[#0B3A22]' : 'text-slate-600' }}">
                            <svg class="h-6 w-6" fill="{{ $item['active'] ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                            </svg>
                            {{ $item['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>

    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const hiddenClasses = ['opacity-0', 'translate-y-8', 'translate-y-3'];

        // 1. Fade-up saat elemen masuk layar
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                entry.target.classList.remove(...hiddenClasses);
                revealObserver.unobserve(entry.target);
            });
        }, { threshold: 0.15 });

        document.querySelectorAll('[data-reveal]').forEach((el) => {
            reduce ? el.classList.remove(...hiddenClasses) : revealObserver.observe(el);
        });

        // 2. Count-up harga saat terlihat
        const countUp = (el) => {
            const end = Number(el.dataset.count);
            const prefix = el.dataset.prefix || '';
            const duration = 1200;
            const start = performance.now();
            const tick = (now) => {
                const p = Math.min((now - start) / duration, 1);
                const eased = 1 - Math.pow(1 - p, 3);
                el.textContent = prefix + Math.round(end * eased).toLocaleString('id-ID');
                if (p < 1) requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
        };

        if (!reduce) {
            const countObserver = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) return;
                    countUp(entry.target);
                    countObserver.unobserve(entry.target);
                });
            }, { threshold: 0.6 });
            document.querySelectorAll('[data-count]').forEach((el) => countObserver.observe(el));
        }

        // 3. Parallax hero + gambar cabin (real-time saat scroll)
        if (reduce) return;

        const hero = document.getElementById('hero');
        const heroInner = document.querySelector('[data-hero-inner]');
        const images = document.querySelectorAll('[data-parallax-img]');
        let ticking = false;

        const update = () => {
            const y = window.scrollY;
            const vh = window.innerHeight;
            const progress = Math.min(y / hero.offsetHeight, 1);

            heroInner.style.transform = `translate3d(0, ${y * 0.25}px, 0)`;
            heroInner.style.opacity = Math.max(0, 1 - progress * 1.2);

            images.forEach((img) => {
                const r = img.parentElement.getBoundingClientRect();
                if (r.bottom < 0 || r.top > vh) return;
                const offset = (r.top + r.height / 2 - vh / 2) / vh;
                img.style.transform = `translate3d(0, ${offset * -18}px, 0) scale(1.15)`;
            });

            ticking = false;
        };

        window.addEventListener('scroll', () => {
            if (!ticking) {
                ticking = true;
                requestAnimationFrame(update);
            }
        }, { passive: true });

        update();
    });
</script>
@endpush
