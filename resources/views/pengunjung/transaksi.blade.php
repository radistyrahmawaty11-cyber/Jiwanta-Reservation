@extends('layouts.dashboard')

@section('title', 'Transaksi - Jiwanta')

@push('styles')
<style>
    html { scroll-behavior: smooth; }

    /* === ANIMASI === */
    .scroll-anim {
        opacity: 0;
        transform: translateY(30px);
        transition: opacity 0.8s ease-out, transform 0.8s ease-out;
    }
    .scroll-anim.visible {
        opacity: 1;
        transform: translateY(0);
    }
    .delay-1 { transition-delay: 0.1s; }
    .delay-2 { transition-delay: 0.2s; }
    .delay-3 { transition-delay: 0.3s; }

    #main-wrapper {
        transition: background-color 0.1s linear;
    }

    /* === STYLE KHUSUS TRANSAKSI === */
    /* Stepper */
    .stepper-line {
        position: absolute;
        top: 50%;
        left: 8;
        right: 8;
        height: 2px;
        background: #d1d5db;
        transform: translateY(-50%);
        z-index: 0;
    }
    .stepper-line-active {
        background: #2d5a4a;
        transition: width 0.5s ease;
    }
    .stepper-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s ease;
        position: relative;
        z-index: 1;
    }
    .stepper-circle.active {
        background: #2d5a4a;
        color: white;
        box-shadow: 0 0 0 4px rgba(45, 90, 74, 0.2);
    }
    .stepper-circle.completed {
        background: #2d5a4a;
        color: white;
    }
    .stepper-circle.pending {
        background: white;
        color: #9ca3af;
        border: 2px solid #d1d5db;
    }

    /* Countdown */
    .countdown-box {
        background: linear-gradient(135deg, #2d5a4a 0%, #3d6a5a 100%);
        color: white;
        border-radius: 16px;
        padding: 16px 20px;
        font-family: 'Courier New', monospace;
        font-size: 28px;
        font-weight: bold;
        letter-spacing: 2px;
        text-align: center;
        box-shadow: 0 4px 12px rgba(45, 90, 74, 0.3);
    }

    /* Upload Area */
    .upload-area {
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        padding: 30px 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #f9fafb;
    }
    .upload-area:hover {
        border-color: #2d5a4a;
        background: #f0fdf4;
    }
    .upload-area.has-file {
        border-color: #2d5a4a;
        background: #f0fdf4;
        border-style: solid;
    }

    /* Copy Button */
    .copy-btn {
        background: #2d5a4a;
        color: white;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
    }
    .copy-btn:hover { background: #234a3d; transform: scale(1.05); }
    .copy-btn.copied { background: #10b981; }

    /* Status Badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-pending { background: #fef3c7; color: #92400e; }

    @keyframes pulse-dot {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    .pulse-dot { animation: pulse-dot 2s ease-in-out infinite; }
</style>
@endpush

@section('content')

{{-- Wrapper Utama --}}
<div id="main-wrapper" class="w-full min-h-screen flex justify-center" style="background-color: #2d5a4a;">

    {{-- Container (Sama persis dengan Dashboard) --}}
    <div class="w-full max-w-2xl bg-[#c8dcc4] min-h-screen relative shadow-2xl pb-24">

        {{-- ========== HEADER ========== --}}
        <div class="bg-white px-6 pt-6 pb-4 rounded-b-[2rem] shadow-sm relative z-10">
            <div class="flex justify-between items-center mb-2">
                <div class="flex items-center gap-3">
                    <button onclick="window.history.back()" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center hover:bg-gray-200 transition">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </button>
                    <div>
                        <h1 class="text-sm font-bold leading-tight text-gray-800">JIWANTA</h1>
                        <p class="text-[10px] text-gray-500 leading-tight">Thermal Springs</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm font-medium text-gray-700">Transaksi</span>
                    <div class="w-8 h-8 rounded-full bg-gray-200"></div>
                </div>
            </div>
        </div>

        {{-- ========== KONTEN UTAMA ========== --}}
        <div class="px-5 py-6 relative z-20">

            {{-- Stepper / Timeline --}}
            <div class="scroll-anim mb-8">
                <div class="relative flex items-center justify-between px-2">
                    <div class="stepper-line"></div>
                    <div class="stepper-line stepper-line-active" style="width: 50%;"></div>

                    <div class="stepper-circle completed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                        </svg9>
                    </div>
                    <div class="stepper-circle active">2</div>
                    <div class="stepper-circle pending">3</div>
                </div>
                <div class="flex justify-between px-1 mt-2">
                    <span class="text-[10px] font-semibold text-[#2d5a4a]">Reservasi</span>
                    <span class="text-[10px] font-semibold text-[#2d5a4a]">Checkout</span>
                    <span class="text-[10px] font-semibold text-gray-400">Selesai</span>
                </div
            </div>

            {{-- Countdown Timer --}}
            <div class="scroll-anim delay-1 mb-6 text-center">
                <div class="countbox inline-block min-w-[200px]">
                    <div class="text-[10px] font-normal opacity-80 mb-1 tracking-widest">WAKTU PEMBAYARAN</div>
                    <div id="countdown">00:05:00</div>
                </div>
            </div>

            {{-- Detail Reservasi --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm mb-4 scroll-anim delay-1">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-full bg-[#2d5a4a] flex items-center justify-center text-white">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                            <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-800">Detail Reservasi</h3>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-500">Tanggal</span>
                        <span class="font-semibold text-gray-800">18 September 2024</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-500">Paket</span>
                        <span class="font-semibold text-gray-800"> Tiket Renang Premier</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-500">Lokasi</span>
                        <span class="font-semibold text-gray-800">Jiwanta Ciwidey</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-500">Durasi</span>
                        <span class="font-semibold text-gray-800">-</span>
                    </div>
                    <div class="flex justify-between items-center py-2 pt-3">
                        <span class="text-gray-500 font-medium">Total Harga</span>
                        <span class="text-lg font-bold text-[#2d5a4a]">Rp 110.250</span>
                    </div>
                </div>
            </div>

            {{-- Data Pemesan --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm mb-4 scroll-anim delay-2">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-full bg-[#2d5a4a] flex items-center justify-center text-white">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-800">Data Pemesan</h3>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="py-2 border-b border-gray-100">
                        <span class="text-gray-500 block mb-1">Nama</span>
                        <span class="font-semibold text-gray-800">Raditya Rahmawaty</span>
                    </div>
                    <div class="py-2 border-b border-gray-100">
                        <span class="text-gray-500 block mb-1">Email</span>
                        <span class="font-semibold text-gray-800">raditya@email.com</span>
                    </div>
                    <div class="py-2">
                        <span class="text-gray-500 block mb-1">No. Telepon</span>
                        <span class="font-semibold text-gray-800">081234567890</span>
                    </div>
                </div>
            </div>

            {{-- Transfer Bank Manual --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm mb-4 scroll-anim delay-2">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-full bg-[#2d5a4a] flex items-center justify-center text-white">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                            <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM14 13a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-800">Transfer Bank Manual</h3>
                </div>

                <div class="bg-gray-50 rounded-xl p-4 mb-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-xs">BCA</div>
                            <div>
                                <p class="text-[10px] text-gray-500">Bank</p>
                                <p class="text-sm font-bold text-gray-800">BCA</p>
                            </div>
                        </div>
                        <button class="copy-btn" onclick="copyRekening('1234567890', this)">Salin</button>
                    </div>

                    <div class="space-y-2 text-xs">
                        <div>
                            <span class="text-gray-500 block mb-1">No. Rekening</span>
                            <div class="flex items-center justify-between bg-white rounded-lg px-3 py-2 border border-gray-200">
                                <span class="font-semibold text-gray-800 font-mono">1234567890</span>
                            </div>
                        </div>
                        <div>
                            <span class="text-gray-500 block mb-1">Atas Nama</span>
                            <span class="font-semibold text-gray-800">Jiwanta Thermal Springs</span>
                        </div>
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-[10px] text-yellow-800 leading-relaxed">
                            Transfer sesuai dengan total harga sebelum waktu habis. Simpan bukti transfer untuk upload di bawah.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Bukti Pembayaran --}}
            <div class="bg-white rounded-2xl p-5 shadow-sm mb-4 scroll-anim delay-3">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-full bg-[#2d5a4a] flex items-center justify-center text-white">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-gray-800">Bukti Pembayaran</h3>
                </div>

                <div class="upload-area" id="uploadArea" onclick="document.getElementById('fileInput').click()">
                    <input type="file" id="fileInput" class="hidden" accept="image/*" onchange="handleFileUpload(event)">

                    <div id="uploadPlaceholder">
                        <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-gray-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                        </div>
                        <p class="text-xs text-gray-600 font-medium mb-1">Upload Bukti Transfer</p>
                        <p class="text-[10px] text-gray-400 mb-3">Format: JPG, PNG (Max 2MB)</p>
                        <button type="button" class="bg-[#2d5a4a] text-white px-4 py-2 rounded-full text-xs font-semibold hover:bg-[#234a3d] transition">
                            Pilih File
                        </button>
                    </div>

                    <div id="filePreview" class="hidden">
                        <img id="previewImage" src="" alt="Preview" class="max-h-32 mx-auto rounded-lg mb-2 shadow-sm">
                        <p id="fileName" class="text-xs text-gray-600 font-medium"></p>
                        <button type="button" onclick="removeFile(event)" class="text-xs text-red-500 hover:text-red-700 mt-2 font-medium">
                            Hapus File
                        </button>
                    </div>
                </div>

                <div class="mt-4 flex items-center justify-center">
                    <span class="status-badge status-pending">
                        <span class="w-2 h-2 bg-yellow-500 rounded-full pulse-dot"></span>
                        Menunggu Konfirmasi
                    </span>
                </div>
            </div>

            {{-- Tombol Kirim --}}
            <div class="scroll-anim delay-3 mt-6 mb-4">
                <button onclick="submitTransaction()" class="w-full bg-[#2d5a4a] text-white py-3.5 rounded-full font-semibold flex items-center justify-center gap-2 hover:bg-[#234a3d] transition shadow-lg group">
                    <span>Kirim & Cek Status Tiket</span>
                    <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </button>
            </div>

        </div>

        {{-- ========== BOTTOM NAVIGATION ========== --}}
        <div class="fixed bottom-4 left-1/2 -translate-x-1/2 w-full max-w-2xl px-4 z-30">
            <div class="bg-white rounded-full shadow-lg border border-gray-100 px-2 py-2 flex justify-around items-center">
                <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 px-4 py-1">
                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
                    </div>
                    <span class="text-[10px] text-gray-500">Beranda</span>
                </a>

                <a href="#" class="flex flex-col items-center gap-1 px-4 py-1">
                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <span class="text-[10px] text-gray-500">Reservasi</span>
                </a>

                <a href="{{ route('transaksi') }}" class="flex flex-col items-center gap-1 px-4 py-1 rounded-full bg-[#c8dcc4]">
                    <div class="w-8 h-8 rounded-full bg-[#2d5a4a] flex items-center justify-center text-white">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="text-[10px] font-bold text-[#2d5a4a]">Transaksi</span>
                </a>

                <a href="#" class="flex flex-col items-center gap-1 px-4 py-1">
                    <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/></svg>
                    </div>
                    <span class="text-[10px] text-gray-500">Tiket Saya</span>
                </a>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    // === LOGIKA TRANSAKSI ===
    let timeLeft = 5 * 60;
    const countdownElement = document.getElementById('countdown');

    function updateCountdown() {
        const hours = Math.floor(timeLeft / 3600);
        const minutes = Math.floor((timeLeft % 3600) / 60);
        const seconds = timeLeft % 60;
        countdownElement.textContent = String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
        if (timeLeft > 0) {
            timeLeft--;
        } else {
            countdownElement.textContent = '00:00:00';
            countdownElement.parentElement.style.background = 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)';
        }
    }
    setInterval(updateCountdown, 1000);
    updateCountdown();

    function copyRekening(rekening, btn) {
        navigator.clipboard.writeText(rekening).then(() => {
            const originalText = btn.textContent;
            btn.textContent = 'Tersalin!';
            btn.classList.add('copied');
            setTimeout(() => {
                btn.textContent = originalText;
                btn.classList.remove('copied');
            }, 2000);
        });
    }

    function handleFileUpload(event) {
        const file = event.target.files[0];
        if (file) {
            if (file.size > 2 * 1024 * 1024) { alert('Ukuran file maksimal 2MB!'); return; }
            if (!file.type.startsWith('image/')) { alert('Hanya file gambar yang diperbolehkan!'); return; }

            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewImage').src = e.target.result;
                document.getElementById('fileName').textContent = file.name;
                document.getElementById('uploadPlaceholder').classList.add('hidden');
                document.getElementById('filePreview').classList.remove('hidden');
                document.getElementById('uploadArea').classList.add('has-file');
            };
            reader.readAsDataURL(file);
        }
    }

    function removeFile(event) {
        event.stopPropagation();
        document.getElementById('fileInput').value = '';
        document.getElementById('uploadPlaceholder').classList.remove('hidden');
        document.getElementById('filePreview').classList.add('hidden');
        document.getElementById('uploadArea').classList.remove('has-file');
    }

    function submitTransaction() {
        if (!document.getElementById('fileInput').files[0]) {
            alert('Silakan upload bukti pembayaran terlebih dahulu!');
            return;
        }
        const btn = event.target.closest('button');
        const originalContent = btn.innerHTML;
        btn.innerHTML = '<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span class="ml-2">Mengirim...</span>';
        btn.disabled = true;

        setTimeout(() => {
            btn.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg><span class="ml-2">Berhasil Dikirim!</span>';
            btn.style.background = '#10b981';
            setTimeout(() => {
                alert('Bukti pembayaran berhasil dikirim! Silakan tunggu konfirmasi dari admin.');
                btn.innerHTML = originalContent;
                btn.style.background = '';
                btn.disabled = false;
            }, 1500);
        }, 2000);
    }

    // === LOGIKA ANIMASI DASHBOARD (Sama persis) ===
    document.addEventListener('DOMContentLoaded', () => {
        const mainWrapper = document.getElementById('main-wrapper');
        const heroSection = document.querySelector('.bg-white.rounded-b-\\[2rem\\]'); // Menggunakan header putih sebagai acuan tinggi
        const heroContent = document.querySelectorAll('.bg-white.rounded-2xl'); // Animasi pada card

        const colorStart = { r: 45, g: 90, b: 74 };
        const colorEnd = { r: 200, g: 220, b: 196 };

        // 1. Scroll Animation
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) entry.target.classList.add('visible');
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.scroll-anim').forEach(el => observer.observe(el));

        // 2. Background Color Transition
        window.addEventListener('scroll', () => {
            const scrollY = window.scrollY;
            const heroHeight = heroSection ? heroSection.offsetHeight : 200;
            let progress = Math.min(scrollY / heroHeight, 1);

            const r = Math.round(colorStart.r + (colorEnd.r - colorStart.r) * progress);
            const g = Math.round(colorStart.g + (colorEnd.g - colorStart.g) * progress);
            const b = Math.round(colorStart.b + (colorEnd.b - colorStart.b) * progress);

            mainWrapper.style.backgroundColor = `rgb(${r}, ${g}, ${b})`;
        });
    });
</script>
@endpush
