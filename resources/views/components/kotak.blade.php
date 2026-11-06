@props(['on' => false, 'ukuran' => 'h-5 w-5'])

<span {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center justify-center border border-gray-700 bg-white ' . $ukuran]) }}>
    @if ($on)
        <svg class="h-4 w-4 text-gray-900" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-label="Dipilih">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
        </svg>
    @endif
</span>