@php
    $kop          = config('bpkad.kop');
    $penerimaMap  = $surat->penerima->keyBy('bagian');
    $instruksi    = (array) ($surat->instruksi ?? []);
    $tanggalSurat = $surat->tanggal_surat ?? $surat->tanggal;
    $dariLuar     = filled($surat->surat_dari);
@endphp

<article class="border-t-4 border-brand-400 bg-white p-5 text-[13px] leading-snug text-gray-900 shadow-sm ring-1 ring-gray-300 sm:p-8">

    {{-- KOP --}}
    <header class="flex items-center gap-4 border-b-2 border-gray-800 pb-3">
        <img src="{{ asset('images/logo-madiun.png') }}" alt="Logo Pemerintah Kabupaten Madiun"
             class="h-16 w-auto shrink-0 sm:h-20" onerror="this.style.display='none'">
        <div class="min-w-0 flex-1 text-center font-serif">
            <p class="text-sm uppercase sm:text-base">{{ $kop['instansi'] }}</p>
            <p class="text-sm font-bold uppercase sm:text-lg">{{ $kop['badan'] }}</p>
            <p class="text-xs sm:text-sm">{{ $kop['alamat'] }}</p>
            <p class="text-xs sm:text-sm">Email : {{ $kop['email'] }}</p>
            <p class="text-xs uppercase sm:text-sm">{{ $kop['kota'] }}</p>
        </div>
    </header>

    <div class="mt-4 border border-gray-800">

        {{-- Data surat --}}
        <div class="grid sm:grid-cols-2">
            <dl class="space-y-2.5 p-3 sm:border-r sm:border-gray-800">
                <div class="flex">
                    <dt class="w-28 shrink-0">Surat dari</dt>
                    <dd class="min-w-0 flex-1 break-words">
                        : <span class="font-semibold uppercase">{{ $dariLuar ? $surat->surat_dari : config('bpkad.bagian.' . $surat->pengirim->bagian) }}</span>
                        @unless ($dariLuar)
                            <span class="block pl-2 text-[11px] text-gray-500">{{ $surat->pengirim->name }}</span>
                        @endunless
                    </dd>
                </div>
                <div class="flex">
                    <dt class="w-28 shrink-0">Tanggal Surat</dt>
                    <dd class="min-w-0 flex-1 font-semibold">: {{ $tanggalSurat->translatedFormat('d F Y') }}</dd>
                </div>
                <div class="flex">
                    <dt class="w-28 shrink-0">Nomor Surat</dt>
                    <dd class="min-w-0 flex-1 break-words font-semibold">: {{ $surat->nomor_surat ?: '-' }}</dd>
                </div>
            </dl>

            <dl class="space-y-2.5 border-t border-gray-800 p-3 sm:border-t-0">
                <div class="flex">
                    <dt class="w-28 shrink-0">Diterima tanggal</dt>
                    <dd class="min-w-0 flex-1 font-semibold">: {{ $surat->tanggal->translatedFormat('d F Y') }}</dd>
                </div>
                <div class="flex">
                    <dt class="w-28 shrink-0">Nomor Agenda</dt>
                    <dd class="min-w-0 flex-1 font-semibold">: {{ $surat->nomor_agenda ?? '-' }}</dd>
                </div>
                <div class="flex">
                    <dt class="w-28 shrink-0">Sifat :</dt>
                    <dd class="grid flex-1 grid-cols-2 gap-x-3 gap-y-2">
                        @foreach (config('bpkad.sifat') as $key => $label)
                            <span class="flex items-center gap-2">
                                <x-kotak :on="$surat->sifat === $key" />
                                {{ $label }}
                            </span>
                        @endforeach
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Perihal --}}
        <div class="flex border-t border-gray-800 p-3">
            <span class="w-16 shrink-0">Perihal</span>
            <p class="min-w-0 flex-1 break-words font-semibold">: {{ $surat->perihal }}</p>
        </div>

        {{-- Diteruskan kepada + instruksi --}}
        <div class="grid border-t border-gray-800 sm:grid-cols-2">
            <div class="p-3 sm:border-r sm:border-gray-800">
                <p class="font-semibold">Diteruskan kepada Yth. :</p>
                <ol class="mt-3 space-y-2.5">
                    @foreach (config('bpkad.jabatan') as $key => $jabatan)
                        @php $p = $penerimaMap->get($key); @endphp
                        <li class="flex items-center gap-2">
                            <span class="w-5 shrink-0 text-right">{{ $loop->iteration }}.</span>
                            <span class="min-w-0 flex-1">
                                {{ $jabatan }}
                                @if ($p && $p->jenis === 'tembusan')
                                    <span class="text-[11px] text-gray-500">(tembusan)</span>
                                @endif
                            </span>
                            <x-kotak :on="(bool) $p" ukuran="h-6 w-9" />
                        </li>
                    @endforeach
                </ol>
            </div>

            <div class="border-t border-gray-800 p-3 sm:border-t-0">
                <p class="font-semibold">Dengan hormat harap</p>
                <ul class="mt-3 space-y-2.5">
                    @foreach (config('bpkad.instruksi') as $key => $label)
                        <li class="flex items-center gap-2">
                            <x-kotak :on="in_array($key, $instruksi, true)" ukuran="h-6 w-6" />
                            {{ $label }}
                        </li>
                    @endforeach
                    <li class="flex items-center gap-2">
                        <x-kotak :on="filled($surat->instruksi_lain)" ukuran="h-6 w-6" />
                        @if (filled($surat->instruksi_lain))
                            <span class="min-w-0 flex-1 break-words">{{ $surat->instruksi_lain }}</span>
                        @else
                            <span class="flex-1 border-b border-dotted border-gray-500">&nbsp;</span>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Isi disposisi --}}
    <section class="mt-8 min-h-[9rem]">
        <h2 class="text-center text-[13px] font-bold uppercase">Isi Disposisi</h2>
        <p class="mt-4 whitespace-pre-line break-words px-2 text-sm leading-relaxed">{{ $surat->isi_disposisi }}</p>

        @if (filled($surat->pemberi_disposisi))
            <div class="mt-6 flex justify-end px-2">
                <div class="text-center">
                    <p class="text-[11px] text-gray-500">Pemberi disposisi</p>
                    <p class="mt-1 font-semibold">{{ $surat->pemberi_disposisi }}</p>
                </div>
            </div>
        @endif
    </section>

    {{-- Bagian bawah --}}
    <dl class="mt-8 space-y-3 text-sm">
        <div class="flex">
            <dt class="w-36 shrink-0">Keterangan</dt>
            <dd class="min-w-0 flex-1 whitespace-pre-line break-words">: {{ $surat->keterangan }}</dd>
        </div>
        <div class="flex">
            <dt class="w-36 shrink-0">Tanggal Surat</dt>
            <dd class="min-w-0 flex-1">: {{ $tanggalSurat->translatedFormat('d F Y') }}</dd>
        </div>
        <div class="flex">
            <dt class="w-36 shrink-0">Diterima Tanggal</dt>
            <dd class="min-w-0 flex-1">: {{ $surat->tanggal->translatedFormat('d F Y') }}</dd>
        </div>
        <div class="flex">
            <dt class="w-36 shrink-0">Diteruskan Ke</dt>
            <dd class="min-w-0 flex-1 break-words">: {{ $surat->diteruskan_ke }}</dd>
        </div>
    </dl>
</article>