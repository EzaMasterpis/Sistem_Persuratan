<div x-show="compose" x-cloak
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="translate-y-8 opacity-0"
     x-transition:enter-end="translate-y-0 opacity-100"
     class="fixed bottom-0 right-0 z-50 w-full sm:right-6 sm:w-[40rem]">

    <div class="overflow-hidden rounded-t-xl border border-gray-200 bg-white shadow-2xl">

        <div class="flex items-center justify-between bg-brand-400 px-4 py-2.5">
            <button type="button" @click="minimized = !minimized" class="flex-1 text-left text-sm font-semibold">Surat Baru</button>
            <div class="flex items-center gap-1">
                <button type="button" @click="minimized = !minimized" aria-label="Kecilkan" class="rounded p-1 transition hover:bg-white/40">
                    <x-icon name="minus" size="h-4 w-4" />
                </button>
                <button type="button" @click="compose = false" aria-label="Tutup" class="rounded p-1 transition hover:bg-white/40">
                    <x-icon name="x" size="h-4 w-4" />
                </button>
            </div>
        </div>

        <form x-show="!minimized" method="POST" action="{{ route('surat.store') }}" enctype="multipart/form-data"
              class="flex max-h-[85vh] flex-col overflow-y-auto">
            @csrf
            <input type="hidden" name="_form" value="compose">

            @if ($errors->any() && old('_form') === 'compose')
                <div class="border-b border-red-100 bg-red-50 px-4 py-2 text-xs text-red-700">
                    @foreach (array_unique($errors->all()) as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                    @if ($errors->has('lampiran') || $errors->has('lampiran.*'))
                        <p class="mt-1 text-red-500">Pilih ulang file lampiran Anda.</p>
                    @endif
                </div>
            @endif

            {{-- Kepada + Tembusan --}}
            <x-pilih-penerima gaya="compose"
                              :opsi="auth()->user()->opsiTujuan()"
                              :tujuan="old('_form') === 'compose' ? old('bagian_tujuan', []) : []"
                              :tembusan="old('_form') === 'compose' ? old('bagian_tembusan', []) : []" />

            <div class="flex items-center border-b border-gray-100 px-4">
                <label for="c-perihal" class="shrink-0 whitespace-nowrap pr-2 text-sm text-gray-500" style="width: 7rem">Perihal</label>
                <input id="c-perihal" type="text" name="perihal" value="{{ old('perihal') }}" maxlength="255"
                       class="w-full border-0 bg-transparent py-2.5 text-sm outline-none focus:ring-0">
            </div>

            <div class="flex items-center border-b border-gray-100 px-4">
                <label for="c-sifat" class="shrink-0 whitespace-nowrap pr-2 text-sm text-gray-500" style="width: 7rem">Sifat</label>
                <select id="c-sifat" name="sifat"
                        class="w-full border-0 bg-transparent py-2.5 text-sm outline-none focus:ring-0">
                    <option value="">Tanpa sifat</option>
                    @foreach (config('bpkad.sifat') as $key => $label)
                        <option value="{{ $key }}" @selected(old('sifat') === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Isi surat: tinggi tetap, tidak ikut menyusut --}}
            <textarea name="isi" placeholder="Tulis isi surat di sini..."
                      class="block h-72 min-h-[18rem] w-full shrink-0 resize-y border-0 px-4 py-3 text-sm leading-relaxed outline-none focus:ring-0">{{ old('isi') }}</textarea>

            {{-- Lampiran --}}
            <x-pilih-lampiran :maks="config('bpkad.lampiran.maks_file')" class="border-t border-gray-100 px-4 py-3" />

            {{-- Detail surat & lembar disposisi: sekarang di bawah isi surat --}}
            @include('partials.detail-disposisi', ['gaya' => 'compose', 'surat' => null])

            {{-- Tombol selalu terlihat di bawah --}}
            <div class="sticky bottom-0 mt-auto flex items-center gap-3 border-t border-gray-100 bg-white px-4 py-3">
                <button type="submit" name="aksi" value="kirim"
                        class="rounded-lg bg-brand-400 px-6 py-2 text-sm font-semibold shadow transition hover:bg-brand-500">
                    Kirim
                </button>
                <button type="submit" name="aksi" value="draft"
                        class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold transition hover:border-brand-400 hover:bg-brand-50">
                    Simpan Draft
                </button>
                <button type="button" @click="compose = false" class="ml-auto text-sm text-gray-500 hover:text-ink">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>