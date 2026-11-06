@extends('layouts.app')

@section('title', config('bpkad.folders.' . $folder . '.label'))

@section('content')

@php
    $meta       = config('bpkad.folders.' . $folder);
    $masukLike  = in_array($folder, ['masuk', 'arsip']);
    $sifatAktif = request('sifat', '');
    $adaFilter  = request()->anyFilled(['q', 'sifat', 'dari', 'sampai']);
    $chips = [
        ''              => 'Semua',
        'sangat_segera' => 'Sangat Segera',
        'segera'        => 'Segera',
        'rahasia'       => 'Rahasia',
        'penting'       => 'Penting',
        'tanpa'         => 'Tanpa sifat',
    ];
@endphp

<div class="sticky top-0 z-10 border-b border-gray-200 bg-white">

    {{-- Judul --}}
    <div class="flex items-center gap-3 px-4 py-2.5">
        <h1 class="text-sm font-semibold">{{ $meta['label'] }}</h1>
        <span class="ml-auto text-xs text-gray-500">{{ $daftar->total() }} surat</span>
    </div>

    {{-- Filter --}}
    <div class="flex flex-wrap items-end gap-x-6 gap-y-3 border-t border-gray-100 bg-gray-50/70 px-4 py-3">

        {{-- Filter sifat --}}
        <div>
            <p class="mb-1.5 text-[11px] font-medium uppercase tracking-wide text-gray-400">Sifat surat</p>
            <div class="flex flex-wrap gap-1.5">
                @foreach ($chips as $val => $label)
                    <a href="{{ route('dashboard', array_filter(array_merge(request()->except(['sifat', 'page']), ['folder' => $folder, 'sifat' => $val]))) }}"
                       class="rounded-full border px-3 py-1 text-xs font-medium transition
                              {{ $sifatAktif === $val ? 'border-brand-400 bg-brand-400 text-ink' : 'border-gray-300 bg-white text-gray-600 hover:border-brand-400' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Filter tanggal --}}
        <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-end gap-2">
            <input type="hidden" name="folder" value="{{ $folder }}">
            @foreach (['q', 'sifat'] as $k)
                @if (request()->filled($k))
                    <input type="hidden" name="{{ $k }}" value="{{ request($k) }}">
                @endif
            @endforeach

            <div>
                <label for="dari" class="mb-1.5 block text-[11px] font-medium uppercase tracking-wide text-gray-400">Dari tanggal</label>
                <input id="dari" type="date" name="dari" value="{{ request('dari') }}"
                       class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
            </div>
            <div>
                <label for="sampai" class="mb-1.5 block text-[11px] font-medium uppercase tracking-wide text-gray-400">Sampai tanggal</label>
                <input id="sampai" type="date" name="sampai" value="{{ request('sampai') }}"
                       class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
            </div>
            <button type="submit"
                    class="rounded-lg bg-brand-400 px-4 py-1.5 text-xs font-semibold shadow-sm transition hover:bg-brand-500">
                Terapkan
            </button>
            @if ($adaFilter)
                <a href="{{ route('dashboard', ['folder' => $folder]) }}"
                   class="py-1.5 text-xs font-medium text-brand-600 hover:underline">Reset</a>
            @endif
        </form>
    </div>

    @if ($errors->has('dari') || $errors->has('sampai'))
        <p class="border-t border-red-100 bg-red-50 px-4 py-1.5 text-xs text-red-700">
            {{ $errors->first('sampai') ?: $errors->first('dari') }}
        </p>
    @endif
</div>

{{-- Daftar surat --}}
@forelse ($daftar as $s)
    @php
        $mine           = $s->penerima->firstWhere('bagian', auth()->user()->bagian);
        $belumDibaca    = $folder === 'masuk' && $mine && is_null($mine->dibaca_at);
        $keluar         = $s->pengirim_id === auth()->id();
        $tampilPengirim = $folder === 'disimpan' ? ! $keluar : $masukLike;
        $jmlPenerima    = $s->penerima->count();
        $jmlDiterima    = $s->penerima->whereNotNull('diterima_at')->count();
        $url = $s->status === 'draft'
            ? route('surat.edit', $s)
            : route('surat.show', ['surat' => $s, 'folder' => $folder]);
    @endphp

    <div class="flex items-center border-b border-gray-100 transition hover:shadow-md
                {{ $belumDibaca ? 'bg-white font-semibold text-ink' : 'bg-gray-50 text-gray-600' }}">

        {{-- Bintang simpan --}}
        @if ($s->status === 'terkirim')
            <form method="POST" action="{{ route('surat.simpan', $s) }}" class="shrink-0 pl-3">
                @csrf
                <button type="submit"
                        title="{{ $s->disimpan ? 'Hapus dari Disimpan' : 'Simpan surat' }}"
                        aria-label="{{ $s->disimpan ? 'Hapus dari Disimpan' : 'Simpan surat' }}"
                        class="rounded-full p-1.5 transition hover:bg-brand-100 {{ $s->disimpan ? 'text-brand-500' : 'text-gray-300 hover:text-brand-500' }}">
                    <x-icon name="star" size="h-5 w-5" :solid="(bool) $s->disimpan" />
                </button>
            </form>
        @else
            <span class="w-11 shrink-0"></span>
        @endif

        <a href="{{ $url }}" class="flex min-w-0 flex-1 items-center gap-3 py-3 pl-2 pr-4 text-sm">

            <div class="w-28 shrink-0 sm:w-52">
                @if ($tampilPengirim)
                    <p class="truncate">{{ $s->pengirim->name }}</p>
                    <p class="truncate text-[11px] font-normal text-gray-400">{{ config('bpkad.bagian.' . $s->pengirim->bagian) }}</p>
                @else
                    <p class="truncate">Kepada: {{ $s->tujuan_label ?: '(belum ada tujuan)' }}</p>
                    @if ($s->status === 'draft')
                        <p class="truncate text-[11px] font-normal text-red-500">Draft</p>
                    @elseif ($jmlPenerima > 0)
                        <p class="truncate text-[11px] font-normal {{ $jmlDiterima === $jmlPenerima ? 'text-green-600' : 'text-gray-400' }}">
                            Diterima {{ $jmlDiterima }} dari {{ $jmlPenerima }} bagian
                        </p>
                    @endif
                @endif
            </div>

            <div class="flex min-w-0 flex-1 items-center gap-2">
                <x-sifat-badge :sifat="$s->sifat" class="hidden shrink-0 sm:inline" />

                @if ($tampilPengirim && $mine && $mine->jenis === 'tembusan')
                    <span class="hidden shrink-0 rounded-full bg-gray-100 px-2 py-0.5 text-[11px] font-semibold text-gray-600 ring-1 ring-gray-200 sm:inline">
                        Tembusan
                    </span>
                @endif

                <p class="truncate">
                    {{ $s->perihal }}
                    <span class="hidden font-normal text-gray-400 md:inline">
                        &ndash; {{ \Illuminate\Support\Str::limit(strip_tags((string) $s->isi), 90) }}
                    </span>
                </p>
            </div>

            <div class="flex shrink-0 items-center gap-3">
                @if ($s->lampiran_count > 0)
                    <x-icon name="clip" size="h-4 w-4 text-gray-400" />
                @endif
                <span class="w-16 text-right text-xs {{ $belumDibaca ? '' : 'font-normal text-gray-400' }}">
                    {{ $s->tanggal_singkat }}
                </span>
            </div>
        </a>
    </div>
@empty
    <div class="flex flex-col items-center justify-center px-4 py-24 text-center">
        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-brand-100 text-brand-600">
            <x-icon :name="$meta['icon']" size="h-8 w-8" />
        </div>
        <p class="mt-4 font-semibold">{{ $adaFilter ? 'Tidak ada surat yang cocok' : 'Belum ada surat' }}</p>
        <p class="mt-1 text-sm text-gray-500">
            {{ $adaFilter ? 'Coba ubah atau reset filter.' : 'Folder ' . $meta['label'] . ' masih kosong.' }}
        </p>
    </div>
@endforelse

@if ($daftar->hasPages())
    <div class="px-4 py-4">{{ $daftar->links() }}</div>
@endif

@endsection