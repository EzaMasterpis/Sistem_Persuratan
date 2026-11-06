@extends('layouts.app')

@section('title', 'Edit Draft')

@section('content')
@php
    $lama = old('_form') === 'edit';
@endphp

<div class="mx-auto max-w-3xl px-4 py-6">

    <a href="{{ route('dashboard', ['folder' => 'draft']) }}"
       class="text-sm font-medium text-brand-600 hover:underline">&larr; Kembali ke Draft</a>

    <h1 class="mt-4 text-xl font-bold">Edit Draft Surat</h1>
    <div class="mt-2 h-1 w-12 rounded bg-brand-400"></div>

    <form method="POST" action="{{ route('surat.update', $surat) }}" enctype="multipart/form-data"
          class="mt-6 space-y-4 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        @csrf
        @method('PUT')
        <input type="hidden" name="_form" value="edit">

        <div>
            <x-pilih-penerima :opsi="auth()->user()->opsiTujuan()"
                              :tujuan="$lama ? old('bagian_tujuan', []) : $surat->penerima->where('jenis', 'utama')->pluck('bagian')->all()"
                              :tembusan="$lama ? old('bagian_tembusan', []) : $surat->penerima->where('jenis', 'tembusan')->pluck('bagian')->all()" />

            @foreach (['bagian_tujuan', 'bagian_tembusan'] as $k)
                @if ($errors->has($k) || $errors->has($k . '.*'))
                    <p class="mt-1 text-xs text-red-600">{{ $errors->first($k) ?: $errors->first($k . '.*') }}</p>
                @endif
            @endforeach
        </div>

        <div>
            <label for="perihal" class="mb-1 block text-sm font-medium">Perihal</label>
            <input id="perihal" type="text" name="perihal" maxlength="255" value="{{ old('perihal', $surat->perihal) }}"
                   class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
            @error('perihal') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="sifat" class="mb-1 block text-sm font-medium">Sifat</label>
            <select id="sifat" name="sifat"
                    class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-200">
                <option value="">Tanpa sifat</option>
                @foreach (config('bpkad.sifat') as $key => $label)
                    <option value="{{ $key }}" @selected(old('sifat', $surat->sifat) === $key)>{{ $label }}</option>
                @endforeach
            </select>
            @error('sifat') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="isi" class="mb-1 block text-sm font-medium">Isi Surat</label>
            <textarea id="isi" name="isi" rows="12"
                      class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm leading-relaxed outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-200">{{ old('isi', $surat->isi) }}</textarea>
            @error('isi') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
        
        @include('partials.detail-disposisi', ['gaya' => 'edit', 'surat' => $surat])
        {{-- Lampiran --}}
        <div>
            <span class="mb-1 block text-sm font-medium">Lampiran</span>

            @if ($surat->lampiran->isNotEmpty())
                <ul class="mb-3 space-y-1.5">
                    @foreach ($surat->lampiran as $l)
                        <li x-data="{ hapus: false }" :class="hapus ? 'opacity-50' : ''"
                            class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm">
                            <x-icon name="clip" size="h-4 w-4 shrink-0 text-gray-400" />
                            <span class="min-w-0 flex-1 truncate" :class="hapus ? 'line-through' : ''">{{ $l->nama_asli }}</span>
                            <span class="shrink-0 text-xs text-gray-400">{{ $l->ukuran_label }}</span>
                            <label class="flex shrink-0 cursor-pointer items-center gap-1 text-xs font-medium text-red-600">
                                <input type="checkbox" name="hapus_lampiran[]" value="{{ $l->id }}" x-model="hapus"
                                       class="h-3.5 w-3.5 accent-red-500">
                                Hapus
                            </label>
                        </li>
                    @endforeach
                </ul>
            @endif

            <x-pilih-lampiran :maks="max(0, config('bpkad.lampiran.maks_file') - $surat->lampiran->count())" />

            @if ($errors->has('lampiran') || $errors->has('lampiran.*'))
                <p class="mt-1 text-xs text-red-600">
                    {{ $errors->first('lampiran') ?: $errors->first('lampiran.*') }} Pilih ulang file Anda.
                </p>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-3 border-t border-gray-100 pt-4">
            <button type="submit" name="aksi" value="kirim"
                    class="rounded-lg bg-brand-400 px-6 py-2.5 text-sm font-semibold shadow transition hover:bg-brand-500">
                Kirim Surat
            </button>
            <button type="submit" name="aksi" value="draft"
                    class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-semibold transition hover:border-brand-400 hover:bg-brand-50">
                Simpan Draft
            </button>
            <button type="submit" form="hapus-draft"
                    class="ml-auto rounded-lg border border-red-200 px-5 py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-50">
                Hapus Draft
            </button>
        </div>
    </form>

    {{-- Form terpisah (HTML tidak boleh menumpuk form) --}}
    <form id="hapus-draft" method="POST" action="{{ route('surat.destroy', $surat) }}"
          data-judul="Hapus draft ini?"
          data-konfirmasi="Draft beserta lampirannya akan dihapus permanen."
          data-tombol="Ya, hapus draft"
          data-bahaya>
        @csrf
        @method('DELETE')
    </form>
</div>
@endsection