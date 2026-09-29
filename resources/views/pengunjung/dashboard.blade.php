@extends('layouts.dashboard')

@section('title', 'Beranda - Jiwanta')

@push('styles')
<style>
    html { scroll-behavior: smooth; }

    /* Animasi Fade Up saat Scroll */
    .scroll-anim {
        opacity: 0;
        transform: translateY(30px);
        transition: opacity 0.8s ease-out, transform 0.8s ease-out;
    }
    .scroll-anim.visible {
        opacity: 1;
        transform: translateY(0);
    }

    /* Delay bertahap */
    .delay-1 { transition-delay: 0.1s; }
    .delay-2 { transition-delay: 0.2s; }
    .delay-3 { transition-delay: 0.3s; }

    /* Transisi background halus */
    #main-wrapper {
        transition: background-color 0.1s linear;
    }

    /* Parallax ringan untuk hero */
    .hero-content {
        transition: transform 0.1s linear, opacity 0.1s linear;
    }
</style>
@endpush

@section('content')

{{-- Wrapper Utama (Background berubah warna saat scroll) --}}
<div id="main-wrapper" class="w-full min-h-screen flex justify-center" style="background-color: #2d5a4a;">

    {{-- Container (Ukuran pas di laptop, seperti tampilan aplikasi HP) --}}
    <div class="w-full max-w-2xl bg-[#c8dcc4] min-h-screen relative shadow-2xl">

        {{-- ========== HERO SECTION (Hijau Gelap) ========== --}}
        <div id="hero-section" class="bg-[#2d5a4a] text-white px-6 pt-8 pb-10 rounded-b-[2rem] relative overflow-hidden z-10">

            {{-- Topbar --}}
            <div class="flex justify-between items-center mb-8 hero-content">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-xs font-bold">J</div>
                    <div>
                        <h1 class="text-sm font-bold leading-tight">JIWANTA</h1>
                        <p class="text-[10px] opacity-80 leading-tight">Thermal Springs</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm font-medium">Beranda</span>
                    <div class="w-8 h-8 rounded-full bg-white/20"></div>
                </div>
            </div>

            {{-- Judul Hero --}}
            <div class="mb-6 hero-content">
                <p class="text-[10px] opacity-80 mb-1 tracking-wider">CIWIDEY, 18°C SEJUK & BERKABUT</p>
                <h2 class="text-2xl font-bold mb-2">Selamat Datang di<br>Jiwanta Ciwidey</h2>
                <p class="text-xs opacity-80 leading-relaxed">Harmoni Alam Pegunungan dan Relaksasi<br>Sempurna di tengah Kelestarian Hutan Pinus</p>
            </div>

            {{-- Card Kecil Tiket & Cabin --}}
            <div class="grid grid-cols-2 gap-3 hero-content">
                <div class="bg-white rounded-xl p-3 text-center relative text-gray-800">
                    <span class="absolute -top-2 right-2 bg-orange-400 text-white text-[8px] px-2 py-0.5 rounded-full font-bold">Populer</span>
                    <div class="text-xl mb-1">🏊</div>
                    <h3 class="text-xs font-bold">Tiket Renang</h3>
                    <p class="text-[10px] text-gray-500">Air Hangat Alami</p>
                </div>
                <div class="bg-white rounded-xl p-3 text-center relative text-gray-800">
                    <span class="absolute -top-2 right-2 bg-orange-400 text-white text-[8px] px-2 py-0.5 rounded-full font-bold">Best View</span>
                    <div class="text-xl mb-1">🏠</div>
                    <h3 class="text-xs font-bold">Cabin Suite</h3>
                    <p class="text-[10px] text-gray-500">Villa Kayu Pinus</p>
                </div>
            </div>
        </div>

        {{-- ========== KONTEN UTAMA (Hijau Muda) ========== --}}
        <div class="px-5 py-6 pb-24 -mt-4 relative z-20">

            {{-- Info Suhu --}}
            <div class="bg-white rounded-full px-4 py-2.5 flex items-center justify-between shadow-sm mb-8 scroll-anim">
                <div class="flex items-center gap-2">
                    <span class="text-base">🌡️</span>
                    <span class="text-xs text-gray-700 font-medium">Suhu Kolam Vulkanik: 38°C - 40°C</span>
                </div>
                <span class="text-[10px] text-gray-500">Buka Hari Ini</span>
            </div>

            {{-- Section Tiket Kolam Hangat --}}
            <div class="mb-6 scroll-anim">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-full bg-[#2d5a4a] flex items-center justify-center text-white">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/></svg>
                    </div>
                    <div>
                        <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold">RELAKSASI TUBUH</p>
                        <h2 class="text-lg font-bold text-gray-800">Tiket Kolam Hangat</h2>
                    </div>
                </div>
            </div>

            {{-- Tiket Classic Area --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm mb-4 scroll-anim delay-1">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-1 h-5 rounded-full bg-[#2d5a4a]"></div>
                    <h3 class="text-sm font-bold text-gray-800">Tiket Classic Area</h3>
                </div>

                {{-- Tabel Harga --}}
                <div class="grid grid-cols-4 gap-2 text-[10px] mb-2 pb-2 border-b border-gray-200 font-bold text-gray-500">
                    <div>KATEGORI</div>
                    <div class="text-center">WEEKDAY</div>
                    <div class="text-center">WEEKEND</div>
                    <div class="text-center">HOLIDAY</div>
                </div>
                <div class="space-y-2 text-xs mb-4">
                    <div class="grid grid-cols-4 gap-2 items-center">
                        <div class="text-gray-700">Anak-Anak</div>
                        <div class="text-center font-semibold">32K</div>
                        <div class="text-center font-semibold">45K</div>
                        <div class="text-center font-semibold">50K</div>
                    </div>
                    <div class="grid grid-cols-4 gap-2 items-center">
                        <div class="text-gray-700">Dewasa</div>
                        <div class="text-center font-semibold">45K</div>
                        <div class="text-center font-semibold">60K</div>
                        <div class="text-center font-semibold">65K</div>
                    </div>
                    <div class="grid grid-cols-4 gap-2 items-center">
                        <div class="text-gray-700">Lansia</div>
                        <div class="text-center font-semibold">40K</div>
                        <div class="text-center font-semibold">50K</div>
                        <div class="text-center font-semibold">55K</div>
                    </div>
                </div>

                <p class="text-[10px] text-gray-500 mb-4 leading-relaxed">
                    Fasilitas: Therapeutic Pool, The Cave, The Canyon, Swimming Pool, Hydrotherapy Pool, Kids Pool, Pool Deck
                </p>
                <button class="w-full flex items-center justify-between px-4 py-2.5 rounded-full text-white text-sm font-semibold bg-[#2d5a4a] hover:bg-[#234a3d] transition">
                    <span>Pesan Classic</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </button>
            </div>

            {{-- Tiket Premier Area --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm mb-8 scroll-anim delay-2">
                <div class="flex items-center gap-2 mb-4">
                    <div class="px-2 py-0.5 rounded bg-[#2d5a4a] text-white text-[9px] font-bold">BEST SELLER</div>
                    <h3 class="text-sm font-bold text-gray-800">Tiket Premier Area</h3>
                </div>

                <div class="grid grid-cols-4 gap-2 text-[10px] mb-2 pb-2 border-b border-gray-200 font-bold text-gray-500">
                    <div>KATEGORI</div>
                    <div class="text-center">WEEKDAY</div>
                    <div class="text-center">WEEKEND</div>
                    <div class="text-center">HOLIDAY</div>
                </div>
                <div class="space-y-2 text-xs mb-4">
                    <div class="grid grid-cols-4 gap-2 items-center">
                        <div class="text-gray-700">Anak-Anak</div>
                        <div class="text-center font-semibold">45K</div>
                        <div class="text-center font-semibold">60K</div>
                        <div class="text-center font-semibold">65K</div>
                    </div>
                    <div class="grid grid-cols-4 gap-2 items-center">
                        <div class="text-gray-700">Dewasa</div>
                        <div class="text-center font-semibold">65K</div>
                        <div class="text-center font-semibold">75K</div>
                        <div class="text-center font-semibold">80K</div>
                    </div>
                    <div class="grid grid-cols-4 gap-2 items-center">
                        <div class="text-gray-700">Lansia</div>
                        <div class="text-center font-semibold">55K</div>
                        <div class="text-center font-semibold">65K</div>
                        <div class="text-center font-semibold">70K</div>
                    </div>
                </div>

                <p class="text-[10px] text-gray-500 mb-4 leading-relaxed">
                    Fasilitas: Semua akses Classic Area, Kolam Renang, Kolam Sender, Barrel Pool, Onsen
                </p>
                <button class="w-full flex items-center justify-between px-4 py-2.5 rounded-full text-white text-sm font-semibold bg-[#2d5a4a] hover:bg-[#234a3d] transition">
                    <span>Pesan Premier</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                </button>
            </div>

            {{-- Section Cabin Suite --}}
            <div class="mb-4 scroll-anim">
                <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold">MENGINAP & SANTAI</p>
                <h2 class="text-lg font-bold text-gray-800">Cabin Suite Pilihan</h2>
            </div>

            {{-- Cabin & BBQ --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm mb-4 scroll-anim delay-1">
                <div class="text-center mb-4">
                    <p class="text-[10px] text-gray-500 uppercase tracking-wider font-bold">MENGINAP & SANTAI</p>
                    <h3 class="text-sm font-bold text-gray-800">CABIN & BBQ</h3>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-4 pb-4 border-b border-gray-200">
                    <div class="text-center">
                        <p class="text-[10px] text-gray-500 mb-1">Weekday</p>
                        <p class="text-lg font-bold text-[#2d5a4a]">2.115K</p>
                    </div>
                    <div class="text-center">
                        <p class="text-[10px] text-gray-500 mb-1">Weekend</p>
                        <p class="text-lg font-bold text-[#2d5a4a]">3.780K</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-[10px] text-gray-600 mb-4">
                    <div>• Paket Cabin 3 hari 2 malam</div>
                    <div>• Paket BBQ untuk 4 porsi (1 kali)</div>
                    <div>• Welcome Drink</div>
                    <div>• Akses Pool Classic</div>
                    <div>• Breakfast untuk 4 porsi (2 kali)</div>
                    <div>• Akses Pool Premier, PNBP</div>
                </div>

                <button class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-full text-white text-sm font-semibold bg-[#2d5a4a] hover:bg-[#234a3d] transition">
                    <span>Reservasi Cabin & BBQ</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </button>
            </div>

            {{-- Couple Stay Package --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm mb-4 scroll-anim delay-2">
                <div class="text-center mb-4">
                    <h3 class="text-sm font-bold text-gray-800">COUPLE STAY PACKAGE</h3>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-4 pb-4 border-b border-gray-200">
                    <div class="text-center">
                        <p class="text-[10px] text-gray-500 mb-1">Weekday</p>
                        <p class="text-lg font-bold text-[#2d5a4a]">1.320K</p>
                    </div>
                    <div class="text-center">
                        <p class="text-[10px] text-gray-500 mb-1">Weekend</p>
                        <p class="text-lg font-bold text-[#2d5a4a]">2.160K</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-[10px] text-gray-600 mb-4">
                    <div>• Paket Cabin Queen Bed (1 malam)</div>
                    <div>• Akses Classic Pool</div>
                    <div>• Breakfast untuk 2 orang</div>
                    <div>• Akses Pool Premier</div>
                    <div>• Paket Romantic Dinner untuk 2 orang</div>
                    <div>• 2 minuman herbal, PNBP</div>
                </div>

                <button class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-full text-white text-sm font-semibold bg-[#2d5a4a] hover:bg-[#234a3d] transition">
                    <span>Reservasi Couple Stay Package</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </button>
            </div>

        </div>

        {{-- ========== BOTTOM NAVIGATION (Sticky di bawah) ========== --}}
        <div class="fixed bottom-4 left-1/2 -translate-x-1/2 w-full max-w-2xl px-4 z-30">
            <div class="bg-white rounded-full shadow-lg border border-gray-100 px-2 py-2 flex justify-around items-center">
                <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 px-4 py-1 rounded-full bg-[#c8dcc4]">
                    <div class="w-8 h-8 rounded-full bg-[#2d5a4a] flex items-center justify-center text-white">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                    </div>
                    <span class="text-[10px] font-bold text-[#2d5a4a]">Beranda</span>
                </a>

                <a href="#" class="flex flex-col items-center gap-1 px-4 py-1">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="text-[10px] text-gray-500">Reservasi</span>
                </a>

                <a href="#" class="flex flex-col items-center gap-1 px-4 py-1">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="text-[10px] text-gray-500">Transaksi</span>
                </a>

                <a href="#" class="flex flex-col items-center gap-1 px-4 py-1">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                    <span class="text-[10px] text-gray-500">Tiket Saya</span>
                </a>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const mainWrapper = document.getElementById('main-wrapper');
        const heroSection = document.getElementById('hero-section');
        const heroContent = document.querySelectorAll('.hero-content');

        // Warna awal (Hijau Gelap) dan akhir (Hijau Muda)
        const colorStart = { r: 45, g: 90, b: 74 };   // #2d5a4a
        const colorEnd = { r: 200, g: 220, b: 196 };  // #c8dcc4

        // 1. SCROLL ANIMATION (Fade Up saat elemen masuk layar)
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.scroll-anim').forEach(el => observer.observe(el));

        // 2. TRANSISI WARNA BACKGROUND & PARALLAX HERO
        window.addEventListener('scroll', () => {
            const scrollY = window.scrollY;
            const heroHeight = heroSection.offsetHeight;

            // Hitung persentase scroll relatif terhadap tinggi hero (0 sampai 1)
            let progress = Math.min(scrollY / heroHeight, 1);

            // Interpolasi warna background wrapper
            const r = Math.round(colorStart.r + (colorEnd.r - colorStart.r) * progress);
            const g = Math.round(colorStart.g + (colorEnd.g - colorStart.g) * progress);
            const b = Math.round(colorStart.b + (colorEnd.b - colorStart.b) * progress);

            mainWrapper.style.backgroundColor = `rgb(${r}, ${g}, ${b})`;

            // Efek Parallax & Fade pada konten Hero
            heroContent.forEach(el => {
                el.style.transform = `translateY(${scrollY * 0.3}px)`;
                el.style.opacity = 1 - (progress * 1.5);
            });
        });
    });
</script>
@endpush
