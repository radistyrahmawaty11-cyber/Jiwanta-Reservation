@php
    $tanggal = fn ($iso) => $iso ? \Illuminate\Support\Carbon::parse($iso)->format('d M Y') : '';
@endphp

@forelse ($ulasans as $ulasan)
    <article class="rounded-2xl bg-white p-4 shadow-[0_4px_18px_rgba(15,69,39,0.06)]">
        <div class="flex items-start gap-3">
            <img src="{{ $ulasan['foto'] }}" alt="{{ $ulasan['nama_pengguna'] }}" loading="lazy" class="h-9 w-9 shrink-0 rounded-full bg-[#C9B99A] object-cover">
            <div class="min-w-0 flex-1">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="truncate text-[13px] font-bold text-[#0B3A22]">{{ $ulasan['nama_pengguna'] }}</p>
                        <div class="mt-0.5 flex flex-wrap items-center gap-2">
                            @include('pengunjung.ulasan._stars', ['rating' => $ulasan['rating'], 'size' => 'h-3.5 w-3.5'])
                            <span class="text-[10px] text-slate-400">{{ $tanggal($ulasan['created_at']) }}</span>
                        </div>
                    </div>
                    @if ((int) $ulasan['pengguna_id'] === (int) auth()->id())
                        <span class="shrink-0 rounded-full bg-[#E8ECFB] px-2 py-0.5 text-[9px] font-bold text-[#3B4A8C]">Ulasan Anda</span>
                    @endif
                </div>
                <p class="mt-2 text-[12.5px] leading-relaxed text-slate-700">{{ $ulasan['komentar'] }}</p>
            </div>
        </div>
    </article>
@empty
    <div class="rounded-2xl border border-dashed border-slate-300 bg-white/60 px-4 py-8 text-center">
        <p class="text-[13px] font-bold text-[#0B3A22]">Belum ada ulasan</p>
        <p class="mt-1 text-[11px] text-slate-500">Jadilah yang pertama membagikan pengalaman Anda.</p>
    </div>
@endforelse
