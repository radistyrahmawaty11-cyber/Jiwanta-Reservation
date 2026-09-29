@props([
    'href' => null,
    'type' => 'submit',
    'color' => 'black',
    'arrow' => true,
])

@php
    $colorClasses = [
        'black' => 'bg-black text-white hover:bg-gray-800',
        'jiwanta' => 'bg-jiwanta text-white hover:bg-jiwanta-dark',
        'light' => 'bg-light-blue text-gray-700 hover:bg-[#cddde4]',
    ];
    $class = $colorClasses[$color] ?? $colorClasses['black'];
@endphp

@if($href)
    <a
        href="{{ $href }}"
        {{ $attributes->merge(['class' => "w-full {$class} font-semibold py-3 rounded-full flex items-center justify-center gap-2 transition duration-200 text-sm"]) }}
    >
        {{ $slot }}
        @if($arrow)
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
        @endif
    </a>
@else
    <button
        type="{{ $type }}"
        {{ $attributes->merge(['class' => "w-full {$class} font-semibold py-3 rounded-full flex items-center justify-center gap-2 transition duration-200 text-sm"]) }}
    >
        {{ $slot }}
        @if($arrow)
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
        @endif
    </button>
@endif
