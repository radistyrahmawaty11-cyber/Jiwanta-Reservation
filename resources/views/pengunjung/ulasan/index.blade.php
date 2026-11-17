@extends('layouts.pengunjung')

@section('title', 'Ulasan - Jiwanta')
@section('page-label', 'Ulasan')

@push('styles')
<style>
    #ulasan-status.is-ok { background: #D5F5DC; color: #0B3A22; }
    #ulasan-status.is-error { background: #FEF2F2; color: #B91C1C; }
    #komentar.is-error { box-shadow: 0 0 0 2px #fca5a5; }
</style>
@endpush

@section('page-content')

@php
    $labelKategori = \App\Models\Ulasan::labelKategori($kategori);
    $labelKategoriDinilai = collect($kategoriBisaDinilai)
        ->map(fn ($item) => \App\Models\Ulasan::labelKategori($item))
        ->join(' dan ');
    $initialRating = max(1, min(5, (int) old('rating', $ulasanForm?->rating ?? 5)));
    $starPath = 'M12 2.5l2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 17.4 6.1 20.5l1.2-6.5L2.5 9.4l6.6-.9z';
    $tabs = [
        \App\Models\Ulasan::KATEGORI_TIKET => [
            'label' => 'Tiket Renang',
            'ringkasan' => $ringkasanKategori[\App\Models\Ulasan::KATEGORI_TIKET],
        ],
        \App\Models\Ulasan::KATEGORI_CABIN => [
            'label' => 'Cabin',
            'ringkasan' => $ringkasanKategori[\App\Models\Ulasan::KATEGORI_CABIN],
        ],
    ];
@endphp

<section class="px-5 pt-4">
    <div class="grid grid-cols-2 gap-2.5">
        @foreach ($tabs as $key => $tab)
            @php $active = $kategori === $key; @endphp
            <a href="{{ route('ulasan.index', ['kategori' => $key, 'sort' => $sort]) }}"
               @if($active) aria-current="page" @endif
               class="rounded-2xl px-3.5 py-3 text-left transition active:scale-[0.99] {{ $active ? 'bg-[#0B3A22] text-white shadow-[0_10px_24px_rgba(11,58,34,0.22)]' : 'bg-white text-[#0B3A22] shadow-[0_4px_18px_rgba(15,69,39,0.08)] hover:bg-[#f5f7ff]' }}">
                <p class="text-[10px] font-bold uppercase tracking-wider {{ $active ? 'text-white/60' : 'text-[#8B5E34]' }}">{{ $tab['label'] }}</p>
                <p class="mt-1 text-[22px] font-extrabold leading-none">
                    <span data-tab-average="{{ $key }}">{{ number_format($tab['ringkasan']['rata_rata'], 1, ',', '.') }}</span><span class="text-[12px] font-bold {{ $active ? 'text-white/50' : 'text-slate-400' }}">/5</span>
                </p>
                <p class="mt-1 text-[10px] {{ $active ? 'text-white/70' : 'text-slate-500' }}">
                    <span data-tab-count="{{ $key }}">{{ $tab['ringkasan']['jumlah_ulasan'] }}</span> ulasan
                </p>
            </a>
        @endforeach
    </div>
</section>

<section class="px-5 pt-3">
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#0B3A22] via-[#0F4527] to-[#17653c] p-5 text-white shadow-[0_18px_40px_rgba(11,58,34,0.25)]">
        <div class="pointer-events-none absolute -right-10 -top-10 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>

        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-white/60">Rating {{ $labelKategori }}</p>

        <div class="mt-3 flex items-end gap-3">
            <p class="text-5xl font-extrabold leading-none"><span data-rating-average>{{ number_format($ringkasan['rata_rata'], 1, ',', '.') }}</span><span class="text-xl font-bold text-white/50">/5</span></p>
            <div class="pb-1">
                <span data-rating-stars>@include('pengunjung.ulasan._stars', ['rating' => (int) round($ringkasan['rata_rata']), 'size' => 'h-4 w-4'])</span>
                <p class="mt-1 text-[11px] text-white/70">dari <span data-rating-count>{{ $ringkasan['jumlah_ulasan'] }}</span> ulasan {{ $labelKategori }}</p>
            </div>
        </div>

        <p class="mt-4 text-[12px] leading-relaxed text-white/75">Rating {{ $labelKategori }} terpisah dari kategori lainnya, sehingga penilaian sesuai dengan produk yang dipesan.</p>
    </div>
</section>

@if ($bisaMenilai)
    <section class="px-5 pt-4">
        <div class="rounded-2xl bg-white p-4 shadow-[0_4px_18px_rgba(15,69,39,0.08)]">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-[16px] font-bold text-[#0B3A22]">{{ $ulasanForm ? 'Perbarui Rating Anda' : 'Bagikan Pengalaman Anda' }}</h2>
                    <p class="mt-0.5 text-[11px] leading-snug text-slate-500">Rating otomatis masuk ke {{ $labelKategoriDinilai }}. Kirim ulang untuk memperbarui.</p>
                </div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#FFF4D6] text-[#F5A623]">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $starPath }}"/></svg>
                </span>
            </div>

            <div class="mt-2 flex flex-wrap gap-1.5">
                @foreach ($kategoriBisaDinilai as $itemKategori)
                    <span class="rounded-full bg-[#EEF0FB] px-2.5 py-1 text-[10px] font-bold text-[#3B4A8C]">{{ \App\Models\Ulasan::labelKategori($itemKategori) }}</span>
                @endforeach
            </div>

            <form id="ulasan-form" class="mt-4" novalidate>
                @csrf
                <div class="flex flex-wrap items-center gap-1" data-star-picker>
                    @for ($i = 1; $i <= 5; $i++)
                        <button type="button" data-star="{{ $i }}" aria-label="{{ $i }} bintang" aria-pressed="{{ $i <= $initialRating ? 'true' : 'false' }}" class="rounded-lg p-0.5 transition active:scale-90 {{ $i <= $initialRating ? 'text-[#F5A623]' : 'text-slate-300' }}">
                            <svg class="h-9 w-9" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $starPath }}"/></svg>
                        </button>
                    @endfor
                    <input type="hidden" name="rating" id="rating" value="{{ $initialRating }}">
                    <span id="rating-label" class="ml-2 text-[11px] font-semibold text-slate-500">{{ $initialRating }} bintang</span>
                </div>

                <div class="mt-3">
                    <label for="komentar" class="sr-only">Komentar</label>
                    <textarea id="komentar" name="komentar" rows="4" maxlength="1000" placeholder="Ceritakan pengalaman Anda di Jiwanta..." class="w-full resize-none rounded-xl border border-slate-200 bg-[#F9F8FF] px-3.5 py-3 text-[12.5px] leading-relaxed text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[#0B3A22]/30 focus:bg-white focus:ring-2 focus:ring-[#0B3A22]/10">{{ old('komentar', $ulasanForm?->komentar) }}</textarea>
                    <div class="mt-1 flex items-start justify-between gap-3">
                        <p id="komentar-error" class="hidden text-[10px] font-semibold text-red-600" role="alert"></p>
                        <p class="ml-auto shrink-0 text-[10px] text-slate-400"><span id="komentar-count">0</span>/1000</p>
                    </div>
                    <p class="mt-1 text-[10px] text-slate-400">Minimal 10 karakter.</p>
                </div>

                <button type="submit" id="ulasan-submit" class="mt-4 w-full rounded-full bg-[#0B3A22] px-5 py-3 text-[13px] font-bold text-white shadow-[0_8px_20px_rgba(11,58,34,0.2)] transition hover:bg-[#124c2f] active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-60">Beri Rating Sekarang</button>
                <p id="ulasan-status" class="mt-3 hidden rounded-xl px-3 py-2 text-[11px] font-semibold" aria-live="polite"></p>
            </form>
        </div>
    </section>
@else
    <section class="px-5 pt-4">
        <div class="rounded-2xl bg-white p-4 shadow-[0_4px_18px_rgba(15,69,39,0.08)]">
            <div class="flex items-start gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#FFF4D6] text-[#8B5E34]">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <rect x="5" y="10" width="14" height="10" rx="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M8 10V7a4 4 0 118 0v3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <div class="min-w-0 flex-1">
                    <h2 class="text-[16px] font-bold text-[#0B3A22]">Belum Bisa Menilai {{ $labelKategori }}</h2>
                    <p class="mt-1 text-[11.5px] leading-relaxed text-slate-600">
                        Hanya pengunjung yang sudah memesan {{ strtolower($labelKategori) }} dan transaksinya diverifikasi petugas yang dapat memberi rating.
                        Anda tetap bisa membaca ulasan pengunjung lain di bawah.
                    </p>
                </div>
            </div>

            <div class="mt-3 grid grid-cols-2 gap-2">
                <a href="{{ route('reservasi', $kategori === \App\Models\Ulasan::KATEGORI_CABIN ? ['tab' => 'cabin'] : []) }}" class="rounded-full bg-[#0B3A22] px-4 py-2.5 text-center text-[11.5px] font-bold text-white transition hover:bg-[#0F4527] active:scale-[0.99]">Pesan Sekarang</a>
                <a href="{{ route('tiket') }}" class="rounded-full bg-[#EEF0FB] px-4 py-2.5 text-center text-[11.5px] font-bold text-[#0B3A22] transition hover:bg-[#e3e7fa] active:scale-[0.99]">Cek Status Tiket</a>
            </div>
        </div>
    </section>
@endif

<section class="px-5 pb-8 pt-5">
    <div class="mb-3 flex items-end justify-between gap-3">
        <div>
            <h2 class="text-[16px] font-bold text-[#0B3A22]">Ulasan {{ $labelKategori }}</h2>
            <p class="mt-0.5 text-[11px] text-slate-500">Komentar pengunjung untuk kategori {{ strtolower($labelKategori) }}.</p>
        </div>
        <label class="flex shrink-0 items-center gap-1.5">
            <span class="sr-only">Urutkan ulasan</span>
            <select id="ulasan-sort" class="cursor-pointer rounded-full border-0 bg-[#E8ECFB] py-1.5 pl-3 pr-8 text-[10px] font-bold text-[#0B3A22] outline-none transition hover:bg-[#dde3f8] focus:ring-2 focus:ring-[#0B3A22]/20">
                <option value="rating" @selected($sort === 'rating')>Rating tertinggi</option>
                <option value="terbaru" @selected($sort === 'terbaru')>Rating terbaru</option>
            </select>
        </label>
    </div>

    <div data-review-list class="space-y-3">
        @include('pengunjung.ulasan._list', ['ulasans' => $ulasans, 'kategori' => $kategori])
    </div>
</section>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('ulasan-form');
        const list = document.querySelector('[data-review-list]');
        const sortSelect = document.getElementById('ulasan-sort');
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const storeUrl = @json(route('ulasan.store'));
        const indexUrl = @json(route('ulasan.index'));
        const kategori = @json($kategori);

        const loadReviews = async () => {
            if (!list) return;

            const sort = sortSelect ? sortSelect.value : 'terbaru';

            try {
                const params = new URLSearchParams({ kategori, sort, partial: '1' });
                const response = await fetch(indexUrl + '?' + params.toString(), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });

                if (response.ok) {
                    list.innerHTML = await response.text();
                }
            } catch (error) {
                // Biarkan daftar lama tetap tampil bila jaringan bermasalah.
            }
        };

        sortSelect?.addEventListener('change', () => {
            const url = new URL(window.location.href);
            url.searchParams.set('kategori', kategori);
            url.searchParams.set('sort', sortSelect.value);
            window.history.replaceState({}, '', url);
            loadReviews();
        });

        if (!form) return;

        const picker = form.querySelector('[data-star-picker]');
        const ratingInput = document.getElementById('rating');
        const ratingLabel = document.getElementById('rating-label');
        const textarea = document.getElementById('komentar');
        const counter = document.getElementById('komentar-count');
        const errorEl = document.getElementById('komentar-error');
        const statusEl = document.getElementById('ulasan-status');
        const submit = document.getElementById('ulasan-submit');

        const setStars = (value) => {
            picker.querySelectorAll('[data-star]').forEach((button) => {
                const active = Number(button.dataset.star) <= value;
                button.classList.toggle('text-[#F5A623]', active);
                button.classList.toggle('text-slate-300', !active);
                button.setAttribute('aria-pressed', active ? 'true' : 'false');
            });
        };

        const currentRating = () => Math.min(5, Math.max(1, Number(ratingInput.value) || 5));

        const paintRating = () => {
            const value = currentRating();
            setStars(value);
            ratingLabel.textContent = value + ' bintang';
        };

        picker.querySelectorAll('[data-star]').forEach((button) => {
            button.addEventListener('click', () => {
                ratingInput.value = button.dataset.star;
                paintRating();
            });
        });

        const updateCounter = () => {
            counter.textContent = textarea.value.length;
        };

        const hideError = () => {
            errorEl.textContent = '';
            errorEl.classList.add('hidden');
            textarea.classList.remove('is-error');
        };

        const showError = (message) => {
            errorEl.textContent = message;
            errorEl.classList.remove('hidden');
            textarea.classList.add('is-error');
        };

        const showStatus = (message, ok) => {
            statusEl.textContent = message;
            statusEl.classList.remove('hidden', 'is-ok', 'is-error');
            statusEl.classList.add(ok ? 'is-ok' : 'is-error');
        };

        const paintSummary = (ringkasan, ringkasanKategori) => {
            const activeSummary = (ringkasanKategori && ringkasanKategori[kategori]) || ringkasan;

            if (activeSummary) {
                const average = Number(activeSummary.rata_rata || 0).toLocaleString('id-ID', {
                    minimumFractionDigits: 1,
                    maximumFractionDigits: 1,
                });

                document.querySelectorAll('[data-rating-average]').forEach((el) => {
                    el.textContent = average;
                });

                document.querySelectorAll('[data-rating-count]').forEach((el) => {
                    el.textContent = activeSummary.jumlah_ulasan;
                });

                const rounded = Math.round(Number(activeSummary.rata_rata || 0));
                document.querySelectorAll('[data-rating-stars] svg').forEach((svg, index) => {
                    svg.classList.toggle('text-[#F5A623]', index < rounded);
                    svg.classList.toggle('text-slate-300', index >= rounded);
                });
            }

            if (ringkasanKategori) {
                Object.entries(ringkasanKategori).forEach(([key, value]) => {
                    const avgEl = document.querySelector(`[data-tab-average="${key}"]`);
                    const countEl = document.querySelector(`[data-tab-count="${key}"]`);

                    if (avgEl) {
                        avgEl.textContent = Number(value.rata_rata || 0).toLocaleString('id-ID', {
                            minimumFractionDigits: 1,
                            maximumFractionDigits: 1,
                        });
                    }

                    if (countEl) {
                        countEl.textContent = value.jumlah_ulasan;
                    }
                });
            }
        };

        textarea.addEventListener('input', () => {
            updateCounter();
            if (!errorEl.classList.contains('hidden')) hideError();
        });

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            hideError();
            statusEl.classList.add('hidden');
            submit.disabled = true;
            submit.textContent = 'Mengirim...';

            try {
                const response = await fetch(storeUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        rating: currentRating(),
                        komentar: textarea.value,
                    }),
                });

                const payload = await response.json().catch(() => ({}));

                if (response.status === 422) {
                    const message = payload?.errors?.komentar?.[0]
                        || payload?.errors?.rating?.[0]
                        || payload?.message
                        || 'Ulasan belum valid.';

                    showError(message);
                    showStatus('Ulasan ditolak. Periksa kembali isian Anda.', false);
                    return;
                }

                if (!response.ok) {
                    showStatus(payload?.message || 'Gagal mengirim ulasan. Coba lagi.', false);
                    return;
                }

                paintSummary(payload.ringkasan, payload.ringkasan_kategori);
                showStatus('Rating berhasil dikirim dan langsung diperbarui.', true);
                await loadReviews();
            } catch (error) {
                showStatus('Tidak dapat terhubung ke server. Periksa koneksi Anda.', false);
            } finally {
                submit.disabled = false;
                submit.textContent = 'Beri Rating Sekarang';
            }
        });

        paintRating();
        updateCounter();
    });
</script>
@endpush
