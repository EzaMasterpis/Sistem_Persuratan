@props(['sifat'])

@php
    $kelas = [
        'sangat_segera' => 'bg-red-600 text-white ring-red-600',
        'segera'        => 'bg-red-50 text-red-700 ring-red-200',
        'penting'       => 'bg-brand-100 text-brand-600 ring-brand-300',
        'rahasia'       => 'bg-gray-800 text-white ring-gray-800',
    ];
@endphp

@if ($sifat && isset($kelas[$sifat]))
    <span {{ $attributes->merge(['class' => 'rounded-full px-2 py-0.5 text-[11px] font-semibold ring-1 ' . $kelas[$sifat]]) }}>
        {{ config('bpkad.sifat.' . $sifat) }}
    </span>
@endif