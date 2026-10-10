@extends('layouts.pengunjung')

@section('title', 'Beranda - Jiwanta')
@section('page-label', 'Beranda')

@section('page-content')

@php
    // ---------- Helper ----------
    $ic = fn (string $d, string $c = 'h-4 w-4', string $sw = '1.8') =>
        '<svg class="'.$c.'" fill="none" stroke="currentColor" stroke-width="'.$sw.'" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="'.$d.'"/></svg>';

    $p = [
        'cloud'     => 'M7 18a4 4 0 010-8 5 5 0 019.6-1A4.5 4.5 0 0117 18H7z',
        'sauna'     => 'M4 20c1.5 0 1.5-1 3-1s1.5 1 3 1 1.5-1 3-1 1.5 1 3 1 1.5-1 3-1M8 15V9M12 15V6M16 15V9M6 6c0-1 1-1 1-2M12 3c0-1 1-1 1-2',
        'home'      => 'M3 11l9-8 9 8M5 10v10h14V10M10 20v-5h4v5',
        'swim'      => 'M3 19c1.5 0 1.5-1 3-1s1.5 1 3 1 1.5-1 3-1 1.5 1 3 1 1.5-1 3-1 1.5 1 3 1M3 15c1.5 0 1.5-1 3-1s1.5 1 3 1 1.5-1 3-1 1.5 1 3 1 1.5-1 3-1 1.5 1 3 1M15 6a1.5 1.5 0 100-3 1.5 1.5 0 000 3zM6 11l5-3 4 3',
        'check'     => 'M5 13l4 4L19 7',
        'heart'     => 'M12 21s-7-4.5-9-9a5 5 0 019-3 5 5 0 019 3c-2 4.5-9 9-9 9z',
        'cup'       => 'M4 8h12v6a4 4 0 01-4 4H8a4 4 0 01-4-4V8zM16 10h2a2 2 0 010 4h-2M8 3v2M12 3v2',
        'bath'      => 'M4 12h16v3a4 4 0 01-4 4H8a4 4 0 01-4-4v-3zM6 12V7a2 2 0 014 0',
        'wifi'      => 'M2 9a15 15 0 0120 0M5 12.5a10 10 0 0114 0M8.5 16a5 5 0 017 0M12 19.5h.01',
        'fire'      => 'M12 3s5 4 5 9a5 5 0 01-10 0c0-2 1-3 2-4 0 2 1 3 2 3 0-3 1-5 1-8z',
        'balcony'   => 'M4 20V8l8-4 8 4v12M8 20v-6M12 20v-6M16 20v-6M4 14h16',
        'arrow'     => 'M5 12h14M13 6l6 6-6 6',
        'tree'      => 'M12 3l4 6h-2.5l3.5 5h-3l3 5H7l3-5H7l3.5-5H8l4-6zM11 19h2v3h-2z',
        'receipt'   => 'M6 3h12v18l-3-2-3 2-3-2-3 2V3zM9 8h6M9 12h6',
        'ticket'    => 'M4 7h16v3a2 2 0 000 4v3H4v-3a2 2 0 000-4V7zM14 7v10',
    ];

    // Kelas animasi masuk (dipakai oleh script)
    $reveal   = 'opacity-0 translate-y-8 transition duration-700 ease-out';
    $revealSm = 'opacity-0 translate-y-3 transition duration-500 ease-out';
    $delays   = ['delay-100', 'delay-200', 'delay-300'];

    $classic = ['Therapeutic Pool, The Cave', 'The Canyon, Swimming Pool', 'Hydrotherapy Pool, Kids Pool, dan Pool Deck'];
    $premier = ['Kolam Rendam & Kolam Sender', 'Barrel Pool & Onses', 'Termasuk Seluruh Fasilitas Area Classic'];

    $cabins = [
        [
            'name' => 'Shorts', 'image' => asset('images/cabin-shorts.jpg'), 'capacity' => null,
            'price' => 'IDR 340.000', 'amount' => 340000, 'unit' => '/3 hours',
            'features' => [['Free Breakfast 4 Pax', 'cup'], ['Private Warm Jacuzzi', 'bath'], ['High Speed Wi-Fi', 'wifi']],
        ],
        [
            'name' => 'Suite', 'image' => asset('images/cabin-suite.jpg'), 'capacity' => 'Kapasitas 4-6 orang',
            'price' => 'IDR 1.040.000', 'amount' => 1040000, 'unit' => '/malam',
            'features' => [['Outdoor Fire Pit', 'fire'], ['Balcony View Kabut', 'balcony'], ['Coffee Maker Set', 'cup']],
        ],
    ];

    $nav = [
        ['Beranda', route('dashboard'), true, 'tree'],
        ['Reservasi', '#', false, 'home'],
        ['Transaksi', '#', false, 'receipt'],
        ['Tiket Saya', '#', false, 'ticket'],
    ];

    $shadow = 'shadow-[0_4px_18px_rgba(15,69,39,0.08)]';
@endphp

        {{-- ========== HERO ========== --}}
        <section id="hero" class="relative overflow-hidden rounded-b-[40px] bg-gradient-to-b from-[#0B3A22] via-[#0F4527] to-[#0B3A22] px-5 pb-16 pt-5 text-white">
            <div class="pointer-events-none absolute -right-12 top-4 h-56 w-56 rounded-full bg-emerald-300/10 blur-3xl"></div>
            <div class="pointer-events-none absolute -left-16 bottom-0 h-48 w-48 rounded-full bg-emerald-200/5 blur-3xl"></div>

            <div data-hero-inner class="relative will-change-transform">
                <div data-reveal class="{{ $reveal }} delay-100 mb-5 inline-flex items-center gap-1.5 rounded-full border border-white/25 bg-white/10 px-3 py-1.5">
                    {!! $ic($p['cloud'], 'h-3.5 w-3.5') !!}
                    <span class="text-[10px] font-medium uppercase tracking-wide">Ciwidey, 18°C Sejuk &amp; Berkabut</span>
                </div>

                <h1 data-reveal class="{{ $reveal }} delay-200 text-[28px] font-bold leading-tight">
                    Selamat Datang di<br><span class="text-[#B5F0BE]">Jiwanta Ciwidey</span>
                </h1>
                <p data-reveal class="{{ $reveal }} delay-300 mt-3 text-[13px] leading-relaxed text-white/80">
                    Harmoni Alam Pegunungan &amp; Relaksasi<br>Sempurna di tengah keasrian hutan pinus.
                </p>

                <div class="mt-6 grid grid-cols-2 gap-3">
                    <div data-reveal class="{{ $reveal }} delay-500">
                        <a href="#tiket" data-tilt class="relative block rounded-2xl bg-white px-3 pb-3.5 pt-5 text-center text-[#0B3A22] transition-transform duration-150 ease-out will-change-transform">
                            <span class="absolute -top-2.5 right-3 rounded-b-md bg-[#8B5E34] px-2.5 py-0.5 text-[9px] font-semibold text-white">Populer</span>
                            <span class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-[#E4E8DD]">{!! $ic($p['sauna'], 'h-5 w-5') !!}</span>
                            <h3 class="text-[13px] font-bold">Tiket Renang</h3>
                            <p class="text-[11px] text-slate-500">Air Hangat Alami</p>
                        </a>
                    </div>
                    <div data-reveal class="{{ $reveal }} delay-700">
                        <a href="#cabin" data-tilt class="relative block rounded-2xl bg-white px-3 pb-3.5 pt-5 text-center text-[#0B3A22] transition-transform duration-150 ease-out will-change-transform">
                            <span class="absolute -top-2.5 right-3 rounded-b-md bg-[#E9C79F] px-2.5 py-0.5 text-[9px] font-semibold text-[#6B4520]">Best View</span>
                            <span class="mx-auto mb-2 flex h-10 w-10 items-center justify-center rounded-full bg-[#EADFCB] text-[#8B5E34]">{!! $ic($p['home'], 'h-5 w-5') !!}</span>
                            <h3 class="text-[13px] font-bold">Cabin Suite</h3>
                            <p class="text-[11px] text-slate-500">Vila Kayu Pinus</p>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- ========== INFO SUHU (menimpa hero) ========== --}}
        <div class="relative z-10 -mt-5 px-5">
            <div data-reveal class="{{ $reveal }} delay-300 flex items-center justify-between rounded-full bg-white px-4 py-2.5 shadow-md">
                <div class="flex items-center gap-2">
                    <span class="relative flex h-2 w-2"><span class="absolute h-full w-full animate-ping rounded-full bg-[#0B3A22] opacity-60"></span><span class="relative h-2 w-2 rounded-full bg-[#0B3A22]"></span></span>
                    <span class="text-[11px] font-medium text-[#0B3A22]">Suhu Kolam Vulkanik: 38°C – 40°C</span>
                </div>
                <span class="text-[10px] font-bold text-[#8B5E34]">Buka Hari Ini</span>
            </div>
        </div>

        <main class="px-5">

            {{-- ========== TIKET KOLAM HANGAT ========== --}}
            <section id="tiket" class="mt-8 scroll-mt-20">
                <div data-reveal class="{{ $reveal }} mb-4 flex items-end justify-between">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-[#8B5E34]">Relaksasi Tubuh</p>
                        <h2 class="text-2xl font-bold text-[#0B3A22]">Tiket Kolam Hangat</h2>
                    </div>
                    <span class="mb-1 text-[#0B3A22]">{!! $ic($p['swim'], 'h-6 w-6', '2') !!}</span>
                </div>

                {{-- Classic --}}
                <article data-reveal class="{{ $reveal }} mb-4 rounded-2xl bg-white p-4 {{ $shadow }}">
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
                                <span class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-[#0B3A22] text-white">{!! $ic($p['check'], 'h-2.5 w-2.5', '3.5') !!}</span>
                                {{ $item }}
                            </li>
                        @endforeach
                    </ul>
                </article>

                {{-- Premier --}}
                <article data-reveal class="{{ $reveal }} relative overflow-hidden rounded-2xl bg-white p-4 {{ $shadow }}">
                    <div class="absolute right-[-34px] top-[14px] w-[120px] rotate-45 bg-[#8B5E34] py-1 text-center text-[9px] font-bold tracking-wider text-white">BEST SELLER</div>

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
                                <span class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-[#8B5E34] text-white">{!! $ic($p['check'], 'h-2.5 w-2.5', '3.5') !!}</span>
                                {{ $item }}
                            </li>
                        @endforeach
                    </ul>
                </article>
            </section>

            {{-- ========== CABIN ========== --}}
            <section id="cabin" class="mt-10 scroll-mt-20">
                <div data-reveal class="{{ $reveal }} mb-4 flex items-end justify-between">
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-wider text-[#8B5E34]">Menginap &amp; Santai</p>
                        <h2 class="text-2xl font-bold text-[#0B3A22]">Cabin</h2>
                    </div>
                    <span class="mb-1 text-[#0B3A22]">{!! $ic($p['home'], 'h-6 w-6', '2') !!}</span>
                </div>

                <div class="space-y-4">
                    @foreach ($cabins as $cabin)
                        <article data-reveal class="{{ $reveal }} overflow-hidden rounded-2xl bg-white {{ $shadow }}">
                            <div class="relative h-[350px] overflow-hidden bg-slate-300">
                                <img src="{{ $cabin['image'] }}" alt="Cabin {{ $cabin['name'] }}" data-parallax-img style="transform: scale(1.15)" class="h-full w-full object-cover will-change-transform">

                                @if ($cabin['capacity'])
                                    <span class="absolute left-3 top-3 inline-flex items-center gap-1 rounded-full bg-white/95 px-2.5 py-1 text-[10px] font-semibold text-slate-800">
                                        {!! $ic($p['heart'], 'h-3 w-3', '2') !!} {{ $cabin['capacity'] }}
                                    </span>
                                @endif

                                <span class="absolute right-3 top-3 inline-flex items-center gap-1.5 rounded-full bg-[#D5F5DC] px-2.5 py-1 text-[10px] font-semibold text-[#0B3A22]">
                                    <span class="h-1.5 w-1.5 rounded-full bg-[#0B3A22]"></span> Tersedia
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
                                    @foreach ($cabin['features'] as [$label, $icon])
                                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-[#EEF0FB] px-2.5 py-1.5 text-[10px] font-semibold text-slate-700">
                                            <span class="text-[#8B5E34]">{!! $ic($p[$icon], 'h-3 w-3', '2') !!}</span> {{ $label }}
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
                <a href="{{ route('reservasi') }}" class="inline-flex items-center gap-2 rounded-full bg-[#FDD9B0] px-5 py-3 text-sm font-bold text-[#0B3A22] transition hover:bg-[#fbcb9a] active:scale-95">
                    Pesan Sekarang {!! $ic($p['arrow'], 'h-4 w-4', '2.2') !!}
                </a>
            </section>
        </main>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const hidden = ['opacity-0', 'translate-y-8', 'translate-y-3'];
        const $$ = (s) => document.querySelectorAll(s);

        // 1. Muncul bertahap saat masuk layar
        const io = new IntersectionObserver((entries) => {
            entries.forEach((e) => {
                if (!e.isIntersecting) return;
                e.target.classList.remove(...hidden);
                io.unobserve(e.target);
            });
        }, { threshold: 0.15 });
        $$('[data-reveal]').forEach((el) => (reduce ? el.classList.remove(...hidden) : io.observe(el)));

        // 2. Harga menghitung naik saat terlihat
        if (!reduce) {
            const countIO = new IntersectionObserver((entries) => {
                entries.forEach((e) => {
                    if (!e.isIntersecting) return;
                    const el = e.target, end = Number(el.dataset.count), pre = el.dataset.prefix || '', t0 = performance.now();
                    const tick = (now) => {
                        const p = Math.min((now - t0) / 1200, 1);
                        el.textContent = pre + Math.round(end * (1 - Math.pow(1 - p, 3))).toLocaleString('id-ID');
                        if (p < 1) requestAnimationFrame(tick);
                    };
                    requestAnimationFrame(tick);
                    countIO.unobserve(el);
                });
            }, { threshold: 0.6 });
            $$('[data-count]').forEach((el) => countIO.observe(el));
        }

        // 3. Tilt kartu hero mengikuti kursor / sentuhan
        if (!reduce) {
            $$('[data-tilt]').forEach((card) => {
                card.addEventListener('pointermove', (e) => {
                    const r = card.getBoundingClientRect();
                    const x = (e.clientX - r.left) / r.width - 0.5;
                    const y = (e.clientY - r.top) / r.height - 0.5;
                    card.style.transform = `perspective(600px) rotateX(${-y * 10}deg) rotateY(${x * 10}deg) scale(1.03)`;
                });
                ['pointerleave', 'pointerup', 'pointercancel'].forEach((ev) =>
                    card.addEventListener(ev, () => (card.style.transform = '')));
            });
        }

        // 4. Real-time saat scroll: parallax hero, gambar cabin, bayangan header
        const hero = document.getElementById('hero');
        const heroInner = document.querySelector('[data-hero-inner]');
        const header = document.getElementById('top-header');
        const imgs = $$('[data-parallax-img]');
        let ticking = false;

        const update = () => {
            const y = window.scrollY, vh = window.innerHeight;
            header.classList.toggle('shadow-[0_4px_16px_rgba(15,69,39,0.08)]', y > 10);

            if (!reduce) {
                const progress = Math.min(y / hero.offsetHeight, 1);
                heroInner.style.transform = `translate3d(0, ${y * 0.25}px, 0)`;
                heroInner.style.opacity = Math.max(0, 1 - progress * 1.2);

                imgs.forEach((img) => {
                    const r = img.parentElement.getBoundingClientRect();
                    if (r.bottom < 0 || r.top > vh) return;
                    const off = (r.top + r.height / 2 - vh / 2) / vh;
                    img.style.transform = `translate3d(0, ${off * -18}px, 0) scale(1.15)`;
                });
            }
            ticking = false;
        };

        window.addEventListener('scroll', () => {
            if (!ticking) { ticking = true; requestAnimationFrame(update); }
        }, { passive: true });
        update();

        // 5. Scroll halus untuk kartu hero
        document.documentElement.style.scrollBehavior = reduce ? 'auto' : 'smooth';
    });
</script>
@endpush
