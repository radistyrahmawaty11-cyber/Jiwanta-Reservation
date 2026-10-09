@extends('layouts.app')

@section('title', 'Masuk - Jiwanta')

@section('content')

<div class="bg-white rounded-3xl shadow-xl w-full max-w-md p-8">

    {{-- Badge Pengunjung --}}
    <div class="flex justify-center mb-6">
        <div class="inline-flex items-center gap-2 px-6 py-2 rounded-full border-2 border-gray-300 text-sm font-semibold tracking-wide"
             style="background-color: #2d5a4a; color: white;">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            LOGIN
        </div>
    </div>

    {{-- Divider --}}
    <hr class="border-gray-200 mb-6">

    {{-- Form Login --}}
    <form action="{{ route('login.process') }}" method="POST" class="space-y-4">
        @csrf

        {{-- ✅ TAMBAHAN: Menampilkan Pesan Error Jika Login Gagal --}}
        @if ($errors->any())
            <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-red-600 text-sm text-center font-medium">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Input WhatsApp/Email --}}
        {{-- ✅ PERBAIKAN: name diubah dari "whatsapp" menjadi "email" agar dibaca oleh AuthController --}}
        <x-input-field
            label="No. WhatsApp / Email"
            name="email"
            placeholder="Contoh: 082134 atau nama@email.com"
            :required="true"
            icon='<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" /></svg>'
        />

        {{-- Input Kata Sandi --}}
        <x-input-field
            label="Kata sandi"
            name="password"
            type="password"
            placeholder="Masukkan kata sandi"
            :required="true"
            :showToggle="true"
            icon='<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>'
        />

        {{-- Ingat Saya --}}
        <div class="flex items-center gap-2">
            <input
                type="checkbox"
                id="remember"
                name="remember"
                class="w-4 h-4 rounded border-gray-300 focus:ring-green-700"
                style="accent-color: #2d5a4a;"
            >
            <label for="remember" class="text-sm text-gray-600">Ingat saya di perangkat ini</label>
        </div>

        {{-- Tombol Masuk --}}
        <button type="submit" class="w-full bg-black text-white font-semibold py-3 rounded-full flex items-center justify-center gap-2 hover:bg-gray-800 transition duration-200 text-sm">
            Masuk ke Akun Jiwanta
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
        </button>

    </form>

    {{-- Divider "Atau Masuk Lebih Cepat" --}}
    <div class="flex items-center my-5">
        <hr class="flex-1 border-gray-200">
        <span class="px-4 text-xs text-gray-400 font-medium tracking-wide">ATAU MASUK LEBIH CEPAT</span>
        <hr class="flex-1 border-gray-200">
    </div>

    {{-- Tombol OTP WhatsApp --}}
    <button type="button" class="w-full text-gray-700 font-medium py-3 rounded-full flex items-center justify-center gap-2 hover:bg-gray-100 transition duration-200 text-sm mb-3"
            style="background-color: #dce8ed;">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600" viewBox="0 0 24 24" fill="currentColor">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
        Kirim kode OTP WhatsApp
    </button>

    {{-- Tombol Google --}}
    <button type="button" class="w-full text-gray-700 font-medium py-3 rounded-full flex items-center justify-center gap-2 hover:bg-gray-100 transition duration-200 text-sm mb-4"
            style="background-color: #dce8ed;">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 48 48">
            <path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303c-1.649 4.657-6.08 8-11.303 8-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/>
            <path fill="#FF3D00" d="M6.306 14.691l6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z"/>
            <path fill="#4CAF50" d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238A11.91 11.91 0 0124 36c-5.202 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z"/>
            <path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303a12.04 12.04 0 01-4.087 5.571l.003-.002 6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z"/>
        </svg>
        Lanjutkan dengan Google
    </button>

    {{-- Tombol Daftar --}}
    <a href="{{ route('register') }}" class="w-full text-white font-semibold py-3 rounded-full flex items-center justify-center gap-2 hover:opacity-90 transition duration-200 text-sm"
       style="background-color: #2d5a4a;">
        Belum memiliki akun? Daftar Sekarang!
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
        </svg>
    </a>

</div>

@endsection

@push('scripts')
<script>
    function togglePassword(inputId) {
        const passwordInput = document.getElementById(inputId);
        const eyeIcon = document.getElementById('eye-icon-' + inputId);

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />';
        } else {
            passwordInput.type = 'password';
            eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
        }
    }
</script>
@endpush
