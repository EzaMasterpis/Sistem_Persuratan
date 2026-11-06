@props(['maks' => 5])

<div {{ $attributes }}
     x-data="{
        files: [],
        maks: {{ (int) $maks }},
        tambah(e) {
            const dt = new DataTransfer();
            [...this.files, ...Array.from(e.target.files)].slice(0, this.maks).forEach(f => dt.items.add(f));
            this.$refs.berkas.files = dt.files;
            this.files = Array.from(dt.files);
        },
        hapus(i) {
            const dt = new DataTransfer();
            this.files.forEach((f, n) => { if (n !== i) dt.items.add(f); });
            this.$refs.berkas.files = dt.files;
            this.files = Array.from(dt.files);
        },
        ukuran(b) { return b >= 1048576 ? (b / 1048576).toFixed(1) + ' MB' : Math.max(1, Math.round(b / 1024)) + ' KB'; }
     }">

    <input type="file" name="lampiran[]" multiple x-ref="berkas" class="hidden"
           accept="{{ '.' . implode(',.', config('bpkad.lampiran.ekstensi')) }}"
           @change="tambah($event)">

    <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
        <button type="button" x-show="files.length < maks" @click="$refs.berkas.click()"
                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold transition hover:border-brand-400 hover:bg-brand-50">
            <x-icon name="clip" size="h-4 w-4" />
            Lampirkan file
        </button>
        <span x-show="maks === 0" x-cloak class="text-xs text-gray-500">Batas lampiran sudah tercapai.</span>
        <span class="text-[11px] text-gray-400">PDF, Word, Excel, JPG, PNG &middot; maks. 5 MB per file</span>
    </div>

    <ul x-show="files.length > 0" x-cloak class="mt-2 space-y-1">
        <template x-for="(f, i) in files" :key="f.name + f.size + i">
            <li class="flex items-center gap-2 rounded-lg border border-gray-200 bg-gray-50 px-3 py-1.5 text-xs">
                <x-icon name="clip" size="h-4 w-4 shrink-0 text-gray-400" />
                <span class="min-w-0 flex-1 truncate" x-text="f.name"></span>
                <span class="shrink-0 text-gray-400" x-text="ukuran(f.size)"></span>
                <button type="button" @click="hapus(i)" aria-label="Hapus lampiran"
                        class="rounded-full p-0.5 text-gray-500 transition hover:bg-gray-200">
                    <x-icon name="x" size="h-3.5 w-3.5" />
                </button>
            </li>
        </template>
    </ul>
</div>