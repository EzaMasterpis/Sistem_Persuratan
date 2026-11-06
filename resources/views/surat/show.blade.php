@extends('layouts.app')

@section('title', $surat->perihal)

@section('content')
@php
    $punyaku        = $surat->penerima->firstWhere('bagian', auth()->user()->bagian);
    $adalahPengirim = $surat->pengirim_id === auth()->id();
@endphp

<div class="mx-auto max-w-4xl px-4 py-6">

    {{-- Bar aksi --}}
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('dashboard', ['folder' => request('folder', 'masuk')]) }}"
           class="text-sm font-medium text-brand-600 hover:underline">&larr; Kembali</a>

        <div class="flex flex-wrap gap-2">

            {{-- Terima surat (hanya penerima yang bagiannya belum mengonfirmasi) --}}
            @can('terima', $surat)
                @if ($punyaku && is_null($punyaku->diterima_at))
                    <form method="POST" action="{{ route('surat.terima', $surat) }}"
                          data-judul="Terima surat ini?"
                          data-konfirmasi="Bagian {{ auth()->user()->bagian_label }} akan tercatat telah menerima surat ini, dan pengirim akan diberi tahu."
                          data-tombol="Ya, terima">
                        @csrf @method('PATCH')
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-brand-400 px-4 py-2 text-sm font-semibold shadow transition hover:bg-brand-500">
                            <x-icon name="check" size="h-4 w-4" />
                            Terima Surat
                        </button>
                    </form>
                @endif
            @endcan

            {{-- Simpan / bookmark --}}
            <form method="POST" action="{{ route('surat.simpan', $surat) }}">
                @csrf
                <button type="submit"
                        class="inline-flex items-center gap-1.5 rounded-lg border px-4 py-2 text-sm font-semibold transition
                               {{ $disimpan ? 'border-brand-400 bg-brand-100' : 'border-gray-300 hover:border-brand-400 hover:bg-brand-50' }}">
                    <x-icon name="star" :size="'h-4 w-4 ' . ($disimpan ? 'text-brand-600' : '')" :solid="$disimpan" />
                    {{ $disimpan ? 'Disimpan' : 'Simpan' }}
                </button>
            </form>

            @can('archive', $surat)
                <form method="POST" action="{{ route('surat.arsip', $surat) }}">
                    @csrf @method('PATCH')
                    <button type="submit"
                            class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold transition hover:border-brand-400 hover:bg-brand-50">
                        {{ $punyaku?->diarsipkan_at ? 'Keluarkan dari Arsip' : 'Arsipkan' }}
                    </button>
                </form>
            @endcan

            @can('delete', $surat)
                <form method="POST" action="{{ route('surat.destroy', $surat) }}"
                      data-judul="Hapus surat ini?"
                      data-konfirmasi="Surat dan lampirannya akan dihapus permanen dari semua penerima. Tindakan ini tidak dapat dibatalkan."
                      data-tombol="Ya, hapus"
                      data-bahaya>
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="rounded-lg border border-red-200 px-4 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50">
                        Hapus
                    </button>
                </form>
            @endcan
        </div>
    </div>

    <p class="mb-3 text-xs text-gray-500">
        Dikirim oleh <span class="font-semibold">{{ $surat->pengirim->name }}</span>
        ({{ config('bpkad.bagian.' . $surat->pengirim->bagian) }})
        &middot; {{ $surat->tanggal->translatedFormat('d F Y, H:i') }}
    </p>

    {{-- Keterangan penerimaan (sisi penerima) --}}
    @if (! $adalahPengirim && $punyaku?->diterima_at)
        <div class="mb-4 flex items-center gap-2 rounded-lg border border-green-300 bg-green-50 px-4 py-2.5 text-sm text-green-800">
            <x-icon name="check" size="h-5 w-5 shrink-0" />
            <span>
                Surat diterima oleh <strong>{{ $punyaku->yangMenerima?->name ?? 'pegawai bagian ini' }}</strong>
                ({{ auth()->user()->bagian_label }}) pada {{ $punyaku->diterima_at->translatedFormat('d F Y, H:i') }}.
            </span>
        </div>
    @endif

    {{-- Status per bagian (sisi pengirim) --}}
    @if ($adalahPengirim)
        <div class="mb-4 flex flex-wrap gap-2 text-xs">
            @foreach ($surat->penerima as $p)
                @php
                    $namaBagian = config('bpkad.bagian.' . $p->bagian) . ($p->jenis === 'tembusan' ? ' (tembusan)' : '');
                @endphp

                @if ($p->diterima_at)
                    <span class="rounded-full border border-green-300 bg-green-50 px-3 py-1 text-green-800">
                        {{ $namaBagian }} &middot; Diterima {{ $p->diterima_at->translatedFormat('d M, H:i') }}{{ $p->yangMenerima ? ' oleh ' . $p->yangMenerima->name : '' }}
                    </span>
                @elseif ($p->dibaca_at)
                    <span class="rounded-full border border-brand-300 bg-brand-50 px-3 py-1">
                        {{ $namaBagian }} &middot; Dibaca {{ $p->dibaca_at->translatedFormat('d M, H:i') }}
                    </span>
                @else
                    <span class="rounded-full border border-gray-200 px-3 py-1 text-gray-500">
                        {{ $namaBagian }} &middot; Belum dibaca
                    </span>
                @endif
            @endforeach
        </div>
    @endif

    {{-- Lembar disposisi --}}
    @include('partials.lembar', ['surat' => $surat])

    {{-- Isi surat --}}
    @if (filled($surat->isi))
        <section class="mt-6 rounded-xl border border-gray-200 bg-white p-5">
            <h2 class="text-sm font-semibold">Isi Surat</h2>
            <div class="mt-3 whitespace-pre-line text-sm leading-relaxed">{{ $surat->isi }}</div>
        </section>
    @endif

    {{-- Lampiran --}}
    @if ($surat->lampiran->isNotEmpty())
        <section class="mt-6">
            <p class="mb-3 text-sm font-semibold">Lampiran ({{ $surat->lampiran->count() }})</p>

            <ul class="grid gap-2 sm:grid-cols-2">
                @foreach ($surat->lampiran as $l)
                    <li class="flex items-center gap-3 rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-brand-100 text-[10px] font-bold uppercase text-brand-600">
                            {{ $l->ekstensi }}
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate font-medium">{{ $l->nama_asli }}</span>
                            <span class="text-xs text-gray-400">{{ $l->ukuran_label }}</span>
                        </span>
                        @if ($l->bisa_dilihat)
                            <a href="{{ route('lampiran.unduh', [$l, 'tampil' => 1]) }}" target="_blank" rel="noopener"
                               class="text-xs font-semibold text-gray-600 hover:underline">Lihat</a>
                        @endif
                        <a href="{{ route('lampiran.unduh', $l) }}"
                           class="rounded-lg bg-brand-400 px-3 py-1.5 text-xs font-semibold transition hover:bg-brand-500">Unduh</a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</div>
@endsection