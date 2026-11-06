@props(['opsi', 'tujuan' => [], 'tembusan' => [], 'gaya' => 'edit'])

@php
    $compose = $gaya === 'compose';
    $baris   = $compose ? 'flex items-start border-b border-gray-100 px-4 py-2.5' : '';
    $label   = $compose ? 'w-20 shrink-0 whitespace-nowrap pt-1 text-sm text-gray-500' : 'mb-1 block text-sm font-medium';
    $lebar   = $compose ? 'width: 7rem' : null;
    $isi     = 'flex min-w-0 flex-1 flex-wrap items-center gap-1.5';
    $pilihan = 'rounded-full border border-dashed border-gray-300 bg-white py-1 pl-3 pr-7 text-xs text-gray-600 outline-none focus:border-brand-400 focus:ring-0';
@endphp

<div {{ $attributes }}
     x-data="{
        opsi: @js($opsi),
        tujuan: @js(array_values((array) $tujuan)),
        tembusan: @js(array_values((array) $tembusan)),
        label(k) { return (this.opsi.find(o => o.key === k) || {}).label || k },
        sisa() { return this.opsi.filter(o => !this.tujuan.includes(o.key) && !this.tembusan.includes(o.key)) },
        tambahTujuan(k) { if (k && !this.tujuan.includes(k) && !this.tembusan.includes(k)) this.tujuan.push(k) },
        tambahTembusan(k) { if (k && !this.tujuan.includes(k) && !this.tembusan.includes(k)) this.tembusan.push(k) },
        hapusTujuan(k) { this.tujuan = this.tujuan.filter(x => x !== k) },
        hapusTembusan(k) { this.tembusan = this.tembusan.filter(x => x !== k) },
        semua() { this.tujuan = this.opsi.map(o => o.key).filter(k => !this.tembusan.includes(k)) }
     }">

    {{-- KEPADA --}}
    <div class="{{ $baris }}">
        <span class="{{ $label }}" @if ($lebar) style="{{ $lebar }}" @endif>Kepada</span>
        <div class="{{ $isi }}">
            <template x-for="k in tujuan" :key="k">
                <span class="inline-flex items-center gap-1 rounded-full bg-brand-100 py-1 pl-3 pr-1.5 text-xs font-medium ring-1 ring-brand-300">
                    <span x-text="label(k)"></span>
                    <input type="hidden" name="bagian_tujuan[]" :value="k">
                    <button type="button" @click="hapusTujuan(k)" :aria-label="'Hapus ' + label(k)"
                            class="rounded-full p-0.5 transition hover:bg-brand-300">
                        <x-icon name="x" size="h-3.5 w-3.5" />
                    </button>
                </span>
            </template>

            <select x-show="sisa().length > 0" x-cloak
                    @change="tambahTujuan($event.target.value); $event.target.value = ''"
                    class="{{ $pilihan }}">
                <option value="" x-text="tujuan.length ? '+ Tambah bagian' : 'Pilih bagian tujuan'"></option>
                <template x-for="o in sisa()" :key="o.key">
                    <option :value="o.key" x-text="o.label"></option>
                </template>
            </select>

            <button type="button" x-show="sisa().length > 0" x-cloak @click="semua()"
                    class="rounded-full px-2 py-1 text-xs font-semibold text-brand-600 hover:underline">
                Semua bagian
            </button>
        </div>
    </div>

    {{-- TEMBUSAN (opsional) --}}
    <div class="{{ $baris }} {{ $compose ? '' : 'mt-4' }}">
        <span class="{{ $label }}" @if ($lebar) style="{{ $lebar }}" @endif>Tembusan</span>
        <div class="{{ $isi }}">
            <template x-for="k in tembusan" :key="k">
                <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 py-1 pl-3 pr-1.5 text-xs font-medium ring-1 ring-gray-300">
                    <span x-text="label(k)"></span>
                    <input type="hidden" name="bagian_tembusan[]" :value="k">
                    <button type="button" @click="hapusTembusan(k)" :aria-label="'Hapus ' + label(k)"
                            class="rounded-full p-0.5 transition hover:bg-gray-300">
                        <x-icon name="x" size="h-3.5 w-3.5" />
                    </button>
                </span>
            </template>

            <select x-show="sisa().length > 0" x-cloak
                    @change="tambahTembusan($event.target.value); $event.target.value = ''"
                    class="{{ $pilihan }}">
                <option value="" x-text="tembusan.length ? '+ Tambah bagian' : 'Opsional: pilih bagian tembusan'"></option>
                <template x-for="o in sisa()" :key="o.key">
                    <option :value="o.key" x-text="o.label"></option>
                </template>
            </select>

            <span x-show="sisa().length === 0 && tembusan.length === 0" x-cloak class="text-xs text-gray-400">
                Semua bagian sudah dipilih sebagai tujuan.
            </span>
        </div>
    </div>
</div>