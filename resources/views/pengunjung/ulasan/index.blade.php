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
    $initialRating = max(1, min(5, (int) old('rating', $ulasanSaya?->rating ?? 5)));
    $starPath = 'M12 2.5l2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 17.4 6.1 20.5l1.2-6.5L2.5 9.4l6.6-.9z';
@endphp

<section class="px-5 pt-4">
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#0B3A22] via-[#0F4527] to-[#17653c] p-5 text-white shadow-[0_18px_40px_rgba(11,58,34,0.25)]">
        <div class="pointer-events-none absolute -right-10 -top-10 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>

        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-white/60">Rating Layanan</p>

        <div class="mt-3 flex items-end gap-3">
            <p class="text-5xl font-extrabold leading-none"><span data-rating-average>{{ number_format($ringkasan['rata_rata'], 1, ',', '.') }}</span><span class="text-xl font-bold text-white/50">/5</span></p>
            <div class="pb-1">
                <span data-rating-stars>@include('pengunjung.ulasan._stars', ['rating' => (int) round($ringkasan['rata_rata']), 'size' => 'h-4 w-4'])</span>
                <p class="mt-1 text-[11px] text-white/70">dari <span data-rating-count>{{ $ringkasan['jumlah_ulasan'] }}</span> ulasan pengunjung</p>
            </div>
        </div>

        <p class="mt-4 text-[12px] leading-relaxed text-white/75">Setiap ulasan membantu pengunjung lain menemukan pengalaman terbaik di Jiwanta Ciwidey.</p>
    </div>
</section>

<section class="px-5 pt-4">
    <div class="rounded-2xl bg-white p-4 shadow-[0_4px_18px_rgba(15,69,39,0.08)]">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h2 class="text-[16px] font-bold text-[#0B3A22]">{{ $ulasanSaya ? 'Perbarui Ulasan Anda' : 'Beri Ulasan' }}</h2>
                <p class="mt-0.5 text-[11px] leading-snug text-slate-500">Satu ulasan per akun. Kirim ulang untuk memperbarui.</p>
            </div>
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-[#FFF4D6] text-[#F5A623]">
                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="{{ $starPath }}"/></svg>
            </span>
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
                <textarea id="komentar" name="komentar" rows="4" maxlength="1000" placeholder="Ceritakan pengalaman Anda di Jiwanta..." class="w-full resize-none rounded-xl border border-slate-200 bg-[#F9F8FF] px-3.5 py-3 text-[12.5px] leading-relaxed text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-[#0B3A22]/30 focus:bg-white focus:ring-2 focus:ring-[#0B3A22]/10">{{ old('komentar', $ulasanSaya?->komentar) }}</textarea>
                <div class="mt-1 flex items-start justify-between gap-3">
                    <p id="komentar-error" class="hidden text-[10px] font-semibold text-red-600" role="alert"></p>
                    <p class="ml-auto shrink-0 text-[10px] text-slate-400"><span id="komentar-count">0</span>/1000</p>
                </div>
                <p class="mt-1 text-[10px] text-slate-400">Minimal 10 karakter.</p>
            </div>

            <button type="submit" id="ulasan-submit" class="mt-4 w-full rounded-full bg-[#0B3A22] px-5 py-3 text-[13px] font-bold text-white shadow-[0_8px_20px_rgba(11,58,34,0.2)] transition hover:bg-[#124c2f] active:scale-[0.99] disabled:cursor-not-allowed disabled:opacity-60">Kirim Ulasan</button>
            <p id="ulasan-status" class="mt-3 hidden rounded-xl px-3 py-2 text-[11px] font-semibold" aria-live="polite"></p>
        </form>
    </div>
</section>

<section class="px-5 pb-8 pt-5">
    <div class="mb-3 flex items-end justify-between gap-3">
        <div>
            <h2 class="text-[16px] font-bold text-[#0B3A22]">Ulasan Pengunjung</h2>
            <p class="mt-0.5 text-[11px] text-slate-500">Rating layanan dari pengunjung Jiwanta.</p>
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
        @include('pengunjung.ulasan._list', ['ulasans' => $ulasans])
    </div>
</section>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('ulasan-form');
        if (!form) return;

        const picker = form.querySelector('[data-star-picker]');
        const ratingInput = document.getElementById('rating');
        const ratingLabel = document.getElementById('rating-label');
        const textarea = document.getElementById('komentar');
        const counter = document.getElementById('komentar-count');
        const errorEl = document.getElementById('komentar-error');
        const statusEl = document.getElementById('ulasan-status');
        const submit = document.getElementById('ulasan-submit');
        const list = document.querySelector('[data-review-list]');
        const sortSelect = document.getElementById('ulasan-sort');
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const storeUrl = @json(route('ulasan.store'));
        const indexUrl = @json(route('ulasan.index'));

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

        const paintSummary = (ringkasan) => {
            if (!ringkasan) return;

            const average = Number(ringkasan.rata_rata || 0).toLocaleString('id-ID', {
                minimumFractionDigits: 1,
                maximumFractionDigits: 1,
            });

            document.querySelectorAll('[data-rating-average]').forEach((el) => {
                el.textContent = average;
            });

            document.querySelectorAll('[data-rating-count]').forEach((el) => {
                el.textContent = ringkasan.jumlah_ulasan;
            });

            const rounded = Math.round(Number(ringkasan.rata_rata || 0));
            document.querySelectorAll('[data-rating-stars] svg').forEach((svg, index) => {
                svg.classList.toggle('text-[#F5A623]', index < rounded);
                svg.classList.toggle('text-slate-300', index >= rounded);
            });
        };

        const loadReviews = async () => {
            if (!list) return;

            const sort = sortSelect ? sortSelect.value : 'terbaru';

            try {
                const response = await fetch(indexUrl + '?sort=' + encodeURIComponent(sort) + '&partial=1', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                });

                if (response.ok) {
                    list.innerHTML = await response.text();
                }
            } catch (error) {
                // Biarkan daftar lama tetap tampil bila jaringan bermasalah.
            }
        };

        textarea.addEventListener('input', () => {
            updateCounter();
            if (!errorEl.classList.contains('hidden')) hideError();
        });

        sortSelect?.addEventListener('change', () => {
            const url = new URL(window.location.href);
            url.searchParams.set('sort', sortSelect.value);
            window.history.replaceState({}, '', url);
            loadReviews();
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
                    showStatus('Komentar ulasan minimal harus terdiri dari 10 karakter.', false);
                    return;
                }

                if (!response.ok) {
                    showStatus(payload?.message || 'Gagal mengirim ulasan. Coba lagi.', false);
                    return;
                }

                paintSummary(payload.ringkasan);
                showStatus('Ulasan berhasil dikirim. Rata-rata rating langsung diperbarui.', true);
                await loadReviews();
            } catch (error) {
                showStatus('Tidak dapat terhubung ke server. Periksa koneksi Anda.', false);
            } finally {
                submit.disabled = false;
                submit.textContent = 'Kirim Ulasan';
            }
        });

        paintRating();
        updateCounter();
    });
</script>
@endpush
