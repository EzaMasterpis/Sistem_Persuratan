@php
    $compose = $gaya === 'compose';
    $lama    = old('_form') === $gaya;
    $admin   = auth()->user()->isAdmin();
    $pad     = $compose ? 'px-4' : '';

    $input = 'w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-200';
    $label = 'mb-1 block text-xs font-medium text-gray-500';

    // Di jendela Surat Baru semua error sudah tampil di bagian atas, jadi pesan per kolom hanya untuk halaman edit
    $pesan = fn ($k) => $compose ? null : $errors->first($k);

    $tanggal   = old('tanggal_surat', $surat?->tanggal_surat?->format('Y-m-d'));
    $dipilih   = $lama ? (array) old('instruksi', []) : (array) ($surat?->instruksi ?? []);
@endphp

<div x-data="{ buka: {{ $compose ? ($lama && $errors->any() ? 'true' : 'false') : 'true' }} }"
     class="{{ $compose ? 'border-b border-gray-100' : 'border-t border-gray-100 pt-3' }}">

    <button type="button" @click="buka = !buka"
            class="flex w-full items-center justify-between {{ $pad }} py-2.5 text-left text-sm font-medium transition hover:text-brand-600">
        <span>{{ $admin ? 'Detail surat & lembar disposisi' : 'Detail surat (nomor dan tanggal)' }}</span>
        <svg class="h-4 w-4 transition-transform" :class="buka ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div x-show="buka" x-cloak class="space-y-3 {{ $pad }} pb-3">

        <div class="grid gap-3 sm:grid-cols-2">
            <div>
                <label for="{{ $gaya }}-nomor" class="{{ $label }}">Nomor surat</label>
                <input id="{{ $gaya }}-nomor" type="text" name="nomor_surat" maxlength="100"
                       value="{{ old('nomor_surat', $surat?->nomor_surat) }}" placeholder="Nomor pada surat" class="{{ $input }}">
                @if ($pesan('nomor_surat')) <p class="mt-1 text-xs text-red-600">{{ $pesan('nomor_surat') }}</p> @endif
            </div>
            <div>
                <label for="{{ $gaya }}-tanggal" class="{{ $label }}">Tanggal surat</label>
                <input id="{{ $gaya }}-tanggal" type="date" name="tanggal_surat" value="{{ $tanggal }}" class="{{ $input }}">
                @if ($pesan('tanggal_surat')) <p class="mt-1 text-xs text-red-600">{{ $pesan('tanggal_surat') }}</p> @endif
            </div>
        </div>
        <p class="text-[11px] text-gray-400">Nomor agenda dan tanggal diterima terisi otomatis saat surat dikirim.</p>

        @if ($admin)
            <div class="border-t border-dashed border-gray-200 pt-3">
                <label for="{{ $gaya }}-dari" class="{{ $label }}">Surat dari <span class="font-normal">(instansi luar)</span></label>
                <input id="{{ $gaya }}-dari" type="text" name="surat_dari" maxlength="150"
                       value="{{ old('surat_dari', $surat?->surat_dari) }}"
                       placeholder="Kosongkan untuk surat antarbagian" class="{{ $input }}">
                @if ($pesan('surat_dari')) <p class="mt-1 text-xs text-red-600">{{ $pesan('surat_dari') }}</p> @endif
            </div>

            <fieldset>
                <legend class="{{ $label }}">Dengan hormat harap</legend>
                <div class="flex flex-wrap gap-x-5 gap-y-1.5">
                    @foreach (config('bpkad.instruksi') as $key => $text)
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="instruksi[]" value="{{ $key }}" @checked(in_array($key, $dipilih, true))
                                   class="h-4 w-4 accent-amber-500">
                            {{ $text }}
                        </label>
                    @endforeach
                </div>
                <input type="text" name="instruksi_lain" maxlength="150" value="{{ old('instruksi_lain', $surat?->instruksi_lain) }}"
                       placeholder="Instruksi lainnya (opsional)" class="mt-2 {{ $input }}">
                @if ($pesan('instruksi_lain')) <p class="mt-1 text-xs text-red-600">{{ $pesan('instruksi_lain') }}</p> @endif
            </fieldset>

            <div>
                <label for="{{ $gaya }}-disposisi" class="{{ $label }}">Isi disposisi</label>
                <textarea id="{{ $gaya }}-disposisi" name="isi_disposisi" rows="3" maxlength="2000"
                          class="{{ $input }}">{{ old('isi_disposisi', $surat?->isi_disposisi) }}</textarea>
                @if ($pesan('isi_disposisi')) <p class="mt-1 text-xs text-red-600">{{ $pesan('isi_disposisi') }}</p> @endif
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <div>
                    <label for="{{ $gaya }}-pemberi" class="{{ $label }}">Pemberi disposisi</label>
                    <input id="{{ $gaya }}-pemberi" type="text" name="pemberi_disposisi" maxlength="100"
                           value="{{ old('pemberi_disposisi', $surat?->pemberi_disposisi) }}"
                           placeholder="Mis. Kepala Badan" class="{{ $input }}">
                    @if ($pesan('pemberi_disposisi')) <p class="mt-1 text-xs text-red-600">{{ $pesan('pemberi_disposisi') }}</p> @endif
                </div>
                <div>
                    <label for="{{ $gaya }}-teruskan" class="{{ $label }}">Diteruskan ke</label>
                    <input id="{{ $gaya }}-teruskan" type="text" name="diteruskan_ke" maxlength="255"
                           value="{{ old('diteruskan_ke', $surat?->diteruskan_ke) }}" class="{{ $input }}">
                    @if ($pesan('diteruskan_ke')) <p class="mt-1 text-xs text-red-600">{{ $pesan('diteruskan_ke') }}</p> @endif
                </div>
            </div>

            <div>
                <label for="{{ $gaya }}-ket" class="{{ $label }}">Keterangan</label>
                <textarea id="{{ $gaya }}-ket" name="keterangan" rows="2" maxlength="1000"
                          class="{{ $input }}">{{ old('keterangan', $surat?->keterangan) }}</textarea>
                @if ($pesan('keterangan')) <p class="mt-1 text-xs text-red-600">{{ $pesan('keterangan') }}</p> @endif
            </div>
        @endif
    </div>
</div>