@props([
    'label' => '',
    'name' => '',
    'type' => 'text',
    'placeholder' => '',
    'icon' => '',
    'required' => false,
    'showToggle' => false,
])

<div>
    <div class="flex justify-between items-center mb-1.5">
        <label for="{{ $name }}" class="text-sm font-medium text-gray-700">{{ $label }}</label>
        @if($showToggle)
            <a href="#" class="text-sm text-gray-500 hover:text-jiwanta transition">Lupa Sandi?</a>
        @endif
    </div>
    <div class="relative">
        @if($icon)
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                {!! $icon !!}
            </span>
        @endif

        <input
            type="{{ $type }}"
            id="{{ $name }}"
            name="{{ $name }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => 'w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-jiwanta focus:border-transparent']) }}
        >

        @if($showToggle)
            <button type="button" onclick="togglePassword('{{ $name }}')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition">
                <svg id="eye-icon-{{ $name }}" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </button>
        @endif
    </div>
</div>
