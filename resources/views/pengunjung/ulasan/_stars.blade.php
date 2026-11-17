@php
    $value = max(0, min(5, (int) ($rating ?? 0)));
    $size = $size ?? 'h-3.5 w-3.5';
@endphp
<span class="inline-flex items-center gap-0.5" aria-label="{{ $value }} dari 5 bintang">
    @for ($i = 1; $i <= 5; $i++)
        <svg class="{{ $size }} {{ $i <= $value ? 'text-[#F5A623]' : 'text-slate-300' }}" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M12 2.5l2.9 6 6.6.9-4.8 4.6 1.2 6.5L12 17.4 6.1 20.5l1.2-6.5L2.5 9.4l6.6-.9z"/>
        </svg>
    @endfor
</span>
