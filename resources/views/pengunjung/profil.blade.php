@extends('layouts.pengunjung')

@section('title', 'Pengaturan Profil - Jiwanta')
@section('page-label', 'Profil')
@section('frame-class') relative mx-auto min-h-screen w-full max-w-[390px] overflow-x-clip bg-[#F9F8FF] pb-32 shadow-2xl @endsection

@section('page-content')

@php
    $user = $user ?? auth()->user();

    $ic = fn (string $d, string $c = 'h-4 w-4', string $sw = '1.8') =>
        '<svg class="'.$c.'" fill="none" stroke="currentColor" stroke-width="'.$sw.'" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="'.$d.'"/></svg>';

    $p = [
        'user'   => 'M12 12a4 4 0 100-8 4 4 0 000 8zM5 20v-1a5 5 0 015-5h4a5 5 0 015 5v1',
        'mail'   => 'M3 6h18v12H3zM3 7l9 6 9-6',
        'phone'  => 'M5 4h3l2 5-2 1a11 11 0 005 5l1-2 5 2v3a2 2 0 01-2 2A16 16 0 013 6a2 2 0 012-2z',
        'lock'   => 'M6 11h12v9H6zM8 11V8a4 4 0 018 0v3',
        'save'   => 'M5 4h12l3 3v13H5zM8 4v5h8V4M8 20v-6h8v6',
        'logout' => 'M15 4h4a1 1 0 011 1v14a1 1 0 01-1 1h-4M10 16l-4-4 4-4M6 12h10',
        'camera' => 'M4 8h3l2-3h6l2 3h3v11H4zM12 17a3.5 3.5 0 100-7 3.5 3.5 0 000 7z',
        'check'  => 'M9 12l2 2 4-4M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'x'      => 'M9 12l2 2 4-4M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9 9l6 6',
    ];

    $card  = 'rounded-2xl bg-white p-4 shadow-[0_4px_18px_rgba(15,69,39,0.06)]';
    $field = 'mt-1.5 w-full rounded-xl border border-transparent bg-[#EEF2FC] px-4 py-3 text-[13px] font-medium text-slate-800 placeholder-slate-400 transition focus:border-[#0B3A22]/40 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#0B3A22]/15';
    $lbl   = 'block text-[10px] font-semibold uppercase tracking-wide text-slate-500';
@endphp

        <main class="space-y-4 px-4 pt-2">

            {{-- ========== KARTU IDENTITAS ========== --}}
            <section class="relative overflow-hidden rounded-2xl bg-gradient-to-b from-[#0B3A22] to-[#0F4527] px-5 pb-5 pt-6 text-white shadow-md">
                <div class="pointer-events-none absolute -right-10 -top-10 h-32 w-32 rounded-full bg-emerald-300/10 blur-2xl"></div>
                <div class="relative flex items-center gap-4">
                    <div class="relative shrink-0">
                        <img data-foto-preview src="{{ $user->fotoUrl() }}" alt="Foto Profil"
                             class="h-16 w-16 rounded-full border-4 border-white/20 bg-[#C9B99A] object-cover">
                    </div>
                    <div class="min-w-0">
                        <h1 class="truncate text-[18px] font-bold leading-tight">{{ $user->nama_pengguna }}</h1>
                        <p class="truncate text-[12px] text-white/80">{{ $user->email }}</p>
                        <span class="mt-1.5 inline-flex items-center gap-1.5 rounded-full bg-[#CDEFD5] px-2.5 py-0.5 text-[9px] font-bold text-[#0B3A22]">
                            {!! $ic($p['check'], 'h-3 w-3', '2.2') !!} Pengunjung Terverifikasi
                        </span>
                    </div>
                </div>
            </section>

            {{-- ========== NOTIFIKASI ========== --}}
            @if (session('status'))
                <div class="flex items-start gap-2.5 rounded-2xl bg-[#CDEFD5] px-4 py-3 text-[12px] font-semibold text-[#0B3A22]">
                    <span class="mt-0.5 shrink-0">{!! $ic($p['check'], 'h-4 w-4', '2.2') !!}</span>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-2xl bg-red-50 px-4 py-3 text-[12px] font-semibold text-red-700">
                    <ul class="list-inside list-disc space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('profil.update') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                {{-- ========== FOTO PROFIL ========== --}}
                <section class="{{ $card }}">
                    <h2 class="flex items-center gap-2 text-[15px] font-bold text-[#0B3A22]">
                        <span class="text-[#8B5E34]">{!! $ic($p['camera'], 'h-4 w-4', '2') !!}</span> Foto Profil
                    </h2>
                    <div class="mt-4 flex items-center gap-4">
                        <img data-foto-preview src="{{ $user->fotoUrl() }}" alt="Foto Profil"
                             class="h-16 w-16 shrink-0 rounded-full bg-[#C9B99A] object-cover ring-2 ring-[#DDE6FB]">
                        <div class="min-w-0 flex-1">
                            <button type="button" data-foto-trigger class="inline-flex items-center gap-2 rounded-full bg-[#DDE6FB] px-4 py-2.5 text-[12px] font-bold text-[#0B3A22] transition hover:brightness-95 active:scale-95">
                                {!! $ic($p['camera'], 'h-4 w-4', '2') !!} Ganti Foto
                            </button>
                            <p class="mt-1.5 text-[10px] leading-snug text-slate-500">Format JPG/PNG, maksimal 2 MB.</p>
                        </div>
                    </div>
                    <input data-foto-input name="foto" type="file" accept="image/png,image/jpeg" class="hidden">
                </section>

                {{-- ========== INFORMASI AKUN ========== --}}
                <section class="{{ $card }}">
                    <h2 class="flex items-center gap-2 text-[15px] font-bold text-[#0B3A22]">
                        <span class="text-[#8B5E34]">{!! $ic($p['user'], 'h-4 w-4', '2') !!}</span> Informasi Akun
                    </h2>
                    <div class="mt-4 space-y-4">
                        <label class="{{ $lbl }}">Nama Lengkap
                            <input name="nama_pengguna" type="text" value="{{ old('nama_pengguna', $user->nama_pengguna) }}" placeholder="Nama lengkap" class="{{ $field }}">
                        </label>
                        <label class="{{ $lbl }}">Email
                            <input name="email" type="email" value="{{ $user->email }}" readonly tabindex="-1"
                                   class="mt-1.5 w-full cursor-not-allowed rounded-xl border border-transparent bg-slate-100 px-4 py-3 text-[13px] font-medium text-slate-500 focus:outline-none">
                            <span class="mt-1 block text-[10px] font-normal normal-case tracking-normal text-slate-400">Email tidak dapat diubah.</span>
                        </label>
                        <label class="{{ $lbl }}">No. WhatsApp
                            <input name="nohp" type="text" value="{{ $user->nohp }}" readonly tabindex="-1"
                                   class="mt-1.5 w-full cursor-not-allowed rounded-xl border border-transparent bg-slate-100 px-4 py-3 text-[13px] font-medium text-slate-500 focus:outline-none">
                            <span class="mt-1 block text-[10px] font-normal normal-case tracking-normal text-slate-400">Nomor HP tidak dapat diubah.</span>
                        </label>
                    </div>
                </section>

                {{-- ========== UBAH KATA SANDI ========== --}}
                <section class="{{ $card }}">
                    <h2 class="flex items-center gap-2 text-[15px] font-bold text-[#0B3A22]">
                        <span class="text-[#8B5E34]">{!! $ic($p['lock'], 'h-4 w-4', '2') !!}</span> Ubah Kata Sandi
                    </h2>
                    <p class="mt-1 text-[11px] leading-snug text-slate-500">Biarkan kosong jika tidak ingin mengubah kata sandi.</p>
                    <div class="mt-4 space-y-4">
                        <label class="{{ $lbl }}">Kata Sandi Saat Ini
                            <input name="password_lama" type="password" placeholder="••••••••" autocomplete="current-password" class="{{ $field }}">
                        </label>
                        <label class="{{ $lbl }}">Kata Sandi Baru
                            <input name="password" type="password" placeholder="Minimal 6 karakter" autocomplete="new-password" class="{{ $field }}">
                        </label>
                        <label class="{{ $lbl }}">Konfirmasi Kata Sandi Baru
                            <input name="password_confirmation" type="password" placeholder="Ulangi kata sandi baru" autocomplete="new-password" class="{{ $field }}">
                        </label>
                    </div>
                </section>

                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-full bg-[#0B3A22] px-6 py-4 text-[14px] font-bold text-white shadow-lg transition hover:bg-[#124c2f] active:scale-[0.98]">
                    {!! $ic($p['save'], 'h-4 w-4', '2.2') !!} Simpan Perubahan
                </button>
            </form>

            {{-- ========== KELUAR ========== --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-full bg-red-50 px-6 py-4 text-[14px] font-bold text-red-700 transition hover:bg-red-100 active:scale-[0.98]">
                    {!! $ic($p['logout'], 'h-4 w-4', '2.2') !!} Keluar dari Akun
                </button>
            </form>
        </main>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const trigger = document.querySelector('[data-foto-trigger]');
        const input = document.querySelector('[data-foto-input]');
        const previews = document.querySelectorAll('[data-foto-preview]');
        if (!trigger || !input) return;

        trigger.addEventListener('click', () => input.click());

        input.addEventListener('change', () => {
            const file = input.files && input.files[0];
            if (!file) return;
            if (file.size > 2 * 1024 * 1024) {
                alert('Ukuran foto maksimal 2 MB.');
                input.value = '';
                return;
            }
            const url = URL.createObjectURL(file);
            previews.forEach((img) => { img.src = url; });
        });
    });
</script>
@endpush
