@extends('layouts.admin')

@section('title', isset($cabin) ? 'Edit Cabin - Jiwanta' : 'Tambah Cabin Baru - Jiwanta')

@section('page-content')

<div class="max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('admin.cabin.index') }}" class="inline-flex items-center gap-2 text-[11px] font-semibold text-slate-600 hover:text-[#0B3A22] transition mb-3">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar Cabin
        </a>
        <h1 class="text-[28px] font-bold leading-tight text-[#0B3A22]">
            {{ isset($cabin) ? 'Edit Unit Cabin' : 'Tambah Unit Cabin Baru' }}
        </h1>
        <p class="mt-1 text-[13px] text-slate-600">
            {{ isset($cabin) ? 'Perbarui informasi, fasilitas, dan tarif untuk unit ini.' : 'Isi detail lengkap untuk menambahkan unit cabin baru ke dalam inventaris.' }}
        </p>
    </div>

    {{-- Form Card --}}
    <div class="rounded-2xl bg-white shadow-sm border border-slate-200 p-6">
        <form action="{{ isset($cabin) ? route('admin.cabin.update', $cabin->id) : route('admin.cabin.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if(isset($cabin))
                @method('PUT')
            @endif

            {{-- Grid Layout --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Nama Cabin --}}
                <div class="md:col-span-2">
                    <label for="nama_cabin" class="block text-[11px] font-bold uppercase tracking-wide text-slate-600 mb-1.5">Nama Cabin <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_cabin" id="nama_cabin" value="{{ old('nama_cabin', $cabin->nama_cabin ?? '') }}"
                           class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-[13px] text-slate-900 outline-none transition focus:border-[#0B3A22] focus:ring-2 focus:ring-[#0B3A22]/15"
                           placeholder="Contoh: Cabin Suite Pinus" required>
                    @error('nama_cabin') <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Kapasitas --}}
                <div>
                    <label for="kapasitas" class="block text-[11px] font-bold uppercase tracking-wide text-slate-600 mb-1.5">Kapasitas (Orang) <span class="text-red-500">*</span></label>
                    <input type="number" name="kapasitas" id="kapasitas" value="{{ old('kapasitas', $cabin->kapasitas ?? '') }}" min="1"
                           class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-[13px] text-slate-900 outline-none transition focus:border-[#0B3A22] focus:ring-2 focus:ring-[#0B3A22]/15"
                           placeholder="Contoh: 4" required>
                    @error('kapasitas') <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-[11px] font-bold uppercase tracking-wide text-slate-600 mb-1.5">Status Ketersediaan <span class="text-red-500">*</span></label>
                    <select name="status" id="status" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-[13px] text-slate-900 outline-none transition focus:border-[#0B3A22] focus:ring-2 focus:ring-[#0B3A22]/15" required>
                        <option value="tersedia" {{ (old('status', $cabin->status ?? '') == 'tersedia') ? 'selected' : '' }}>Tersedia</option>
                        <option value="terisi" {{ (old('status', $cabin->status ?? '') == 'terisi') ? 'selected' : '' }}>Terisi</option>
                    </select>
                    @error('status') <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Harga Per Malam --}}
                <div>
                    <label for="harga_per_malam" class="block text-[11px] font-bold uppercase tracking-wide text-slate-600 mb-1.5">Harga Per Malam (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="harga_per_malam" id="harga_per_malam" value="{{ old('harga_per_malam', $cabin->harga_per_malam ?? '') }}" min="0"
                           class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-[13px] text-slate-900 outline-none transition focus:border-[#0B3A22] focus:ring-2 focus:ring-[#0B3A22]/15"
                           placeholder="Contoh: 1250000" required>
                    @error('harga_per_malam') <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Upload Gambar --}}
                <div>
                    <label for="gambar" class="block text-[11px] font-bold uppercase tracking-wide text-slate-600 mb-1.5">Foto Cabin</label>
                    <input type="file" name="gambar" id="gambar" accept="image/jpeg,image/png,image/jpg"
                           class="block w-full text-[12px] text-slate-600 file:mr-4 file:rounded-lg file:border-0 file:bg-[#CDEFD5] file:px-4 file:py-2.5 file:text-[12px] file:font-bold file:text-[#0B3A22] hover:file:bg-[#b8e6c1] transition cursor-pointer">
                    <p class="mt-1 text-[10px] text-slate-500">Format: JPG, PNG. Maksimal 2MB.</p>
                    @if(isset($cabin) && $cabin->gambar)
                        <div class="mt-3">
                            <p class="text-[10px] font-semibold text-slate-600 mb-1">Gambar Saat Ini:</p>
                            <img src="{{ asset('storage/' . $cabin->gambar) }}" alt="Preview" class="h-24 w-auto rounded-lg border border-slate-200 object-cover">
                        </div>
                    @endif
                    @error('gambar') <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Deskripsi --}}
                <div class="md:col-span-2">
                    <label for="deskripsi" class="block text-[11px] font-bold uppercase tracking-wide text-slate-600 mb-1.5">Deskripsi Singkat</label>
                    <textarea name="deskripsi" id="deskripsi" rows="3"
                              class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-[13px] text-slate-900 outline-none transition focus:border-[#0B3A22] focus:ring-2 focus:ring-[#0B3A22]/15"
                              placeholder="Contoh: Cabin mewah dengan pemandangan langsung ke hutan pinus dan akses private hot spring.">{{ old('deskripsi', $cabin->deskripsi ?? '') }}</textarea>
                    @error('deskripsi') <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Fasilitas --}}
                <div class="md:col-span-2">
                    <label for="fasilitas" class="block text-[11px] font-bold uppercase tracking-wide text-slate-600 mb-1.5">Fasilitas (Pisahkan dengan koma)</label>
                    <input type="text" name="fasilitas" id="fasilitas" value="{{ old('fasilitas', $cabin->fasilitas ?? '') }}"
                           class="w-full rounded-lg border border-slate-200 bg-slate-50 px-4 py-2.5 text-[13px] text-slate-900 outline-none transition focus:border-[#0B3A22] focus:ring-2 focus:ring-[#0B3A22]/15"
                           placeholder="Contoh: King Bed, Smart TV, Private Hot Spring, High-Speed WiFi, Sarapan 2 Pax">
                    <p class="mt-1 text-[10px] text-slate-500">Tips: Tulis fasilitas dipisah koma agar otomatis muncul sebagai badge di halaman depan.</p>
                    @error('fasilitas') <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p> @enderror
                </div>

            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-6 mt-6">
                <a href="{{ route('admin.cabin.index') }}" class="rounded-lg bg-slate-100 px-6 py-2.5 text-[12px] font-bold text-slate-700 transition hover:bg-slate-200">
                    Batal
                </a>
                <button type="submit" class="rounded-lg bg-[#0B3A22] px-6 py-2.5 text-[12px] font-bold text-white shadow-sm transition hover:bg-[#124c2f] active:scale-95">
                    {{ isset($cabin) ? 'Simpan Perubahan' : 'Tambah Cabin' }}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
