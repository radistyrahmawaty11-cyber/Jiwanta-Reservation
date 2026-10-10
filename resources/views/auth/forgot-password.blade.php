@extends('layouts.app')

@section('title', 'Lupa Sandi - Jiwanta')

@section('content')

<div class="bg-white rounded-3xl shadow-xl w-full max-w-md p-8">

    {{-- Badge --}}
    <div class="flex justify-center mb-6">
        <div class="inline-flex items-center gap-2 px-6 py-2 rounded-full border-2 border-gray-300 text-sm font-semibold tracking-wide text-white"
             style="background-color: #2d5a4a;">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            LUPA SANDI
        </div>
    </div>

    <hr class="border-gray-200 mb-6">

    <p class="text-sm text-gray-500 mb-5 text-center leading-relaxed">
        Masukkan email atau nomor WhatsApp akun Anda. Kami akan membuat tautan untuk mengatur ulang kata sandi.
    </p>

    @if (session('status'))
        <div class="mb-4 rounded-2xl bg-green-50 border border-green-200 px-4 py-3 text-sm text-green-800">
            {{ session('status') }}
        </div>
    @endif

    @if (session('reset_url'))
        <div class="mb-4 rounded-2xl bg-blue-50 border border-blue-200 px-4 py-3 text-xs text-blue-800 break-all">
            <p class="font-semibold mb-1">Mode demo &mdash; tautan reset Anda:</p>
            <a href="{{ session('reset_url') }}" class="underline font-medium">{{ session('reset_url') }}</a>
        </div>
    @endif

    <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label for="identifier" class="text-sm font-medium text-gray-700">Email / No. WhatsApp</label>
            <input
                type="text"
                id="identifier"
                name="identifier"
                value="{{ old('identifier') }}"
                placeholder="Contoh: admin@jiwanta.id atau 081200000001"
                required
                class="mt-1.5 w-full px-4 py-2.5 border border-gray-300 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-jiwanta focus:border-transparent"
            >
            @error('identifier')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
                class="w-full text-white font-semibold py-3 rounded-full hover:opacity-90 transition duration-200 text-sm"
                style="background-color: #2d5a4a;">
            Kirim Tautan Reset
        </button>
    </form>

    <div class="mt-4 text-center">
        <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-green-700 transition">
            <span class="font-semibold" style="color: #2d5a4a;">Kembali ke halaman masuk</span>
        </a>
    </div>

</div>

@endsection
