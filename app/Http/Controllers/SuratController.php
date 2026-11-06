<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use App\Models\User;
use App\Notifications\SuratMasukNotification;
use App\Notifications\SuratTerkirimNotification;
use App\Notifications\SuratDiterimaNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SuratController extends Controller
{
    /** Daftar surat per folder + filter. */
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q'      => ['nullable', 'string', 'max:100'],
            'sifat'  => ['nullable', Rule::in(array_merge(array_keys(config('bpkad.sifat')), ['tanpa']))],
            'dari'   => ['nullable', 'date'],
            'sampai' => ['nullable', 'date', 'after_or_equal:dari'],
        ], [
            'dari.date'             => 'Tanggal awal tidak valid.',
            'sampai.date'           => 'Tanggal akhir tidak valid.',
            'sampai.after_or_equal' => 'Tanggal akhir harus sama atau setelah tanggal awal.',
        ]);

        $user   = $request->user();
        $folder = $request->query('folder', 'masuk');

        if (! array_key_exists($folder, config('bpkad.folders'))) {
            $folder = 'masuk';
        }

        $query = match ($folder) {
            'masuk'    => Surat::diterima($user->bagian),
            'arsip'    => Surat::diterima($user->bagian, true),
            'terkirim' => Surat::terkirim()->where('pengirim_id', $user->id),
            'draft'    => Surat::draft()->where('pengirim_id', $user->id),
            'disimpan' => Surat::terkirim()->whereHas('disimpanOleh', fn ($q) => $q->where('users.id', $user->id)),
        };

        $daftar = $query
            ->saring($filters)
            ->with(['pengirim:id,name,bagian', 'penerima'])
            ->withCount('lampiran')
            ->withExists(['disimpanOleh as disimpan' => fn ($q) => $q->where('users.id', $user->id)])
            ->orderByRaw('COALESCE(dikirim_at, updated_at) DESC')
            ->paginate(15)
            ->withQueryString();

        return view('dashboard', compact('daftar', 'folder'));
    }

    /** Simpan surat baru (dari jendela "Surat Baru"). */
    public function store(Request $request)
    {
        $data  = $request->validate($this->rules($request), $this->messages());
        $kirim = $data['aksi'] === 'kirim';

        $surat = DB::transaction(function () use ($request, $data, $kirim) {
            $surat = Surat::create([
                'pengirim_id' => $request->user()->id,
                'perihal'     => $data['perihal'],
                'isi'         => $data['isi'] ?? null,
                'sifat'       => $data['sifat'] ?? null,
                'status'      => $kirim ? 'terkirim' : 'draft',
                'dikirim_at'  => $kirim ? now() : null,
            ] + $this->dataLembar($request, $data));

            $this->simpanPenerima($surat, $data['bagian_tujuan'] ?? [], $data['bagian_tembusan'] ?? []);
            $this->simpanLampiran($surat, $request->file('lampiran', []));

            if ($kirim) {
                $this->beriNomorAgenda($surat);
            }

            return $surat;
        });

        if ($kirim) {
            $this->kirimNotifikasi($surat);
        }

        return redirect()
            ->route('dashboard', ['folder' => $kirim ? 'terkirim' : 'draft'])
            ->with('status', $kirim
                ? 'Surat berhasil dikirim. Tujuan: ' . $surat->tujuan_label . '.'
                : 'Draft berhasil disimpan.');
    }

    /** Baca surat (tampilan lembar disposisi). */
    public function show(Request $request, Surat $surat)
    {
        Gate::authorize('view', $surat);

        $user = $request->user();

        // Draft langsung diarahkan ke halaman edit
        if ($surat->status === 'draft') {
            return redirect()->route('surat.edit', $surat);
        }

        // Tandai terbaca untuk bagian penerima yang membuka
        if ($surat->pengirim_id !== $user->id) {
            $surat->penerima()
                ->where('bagian', $user->bagian)
                ->whereNull('dibaca_at')
                ->update(['dibaca_at' => now()]);
        }

        // Tandai notifikasi surat ini sebagai sudah dibaca
        $user->unreadNotifications
            ->where('data.surat_id', $surat->id)
            ->each->markAsRead();

        $surat->load('pengirim:id,name,bagian,email', 'penerima.yangMenerima:id,name', 'lampiran');

        $disimpan = $surat->disimpanOleh()->where('users.id', $user->id)->exists();

        return view('surat.show', compact('surat', 'disimpan'));
    }

    /** Form edit draft. */
    public function edit(Surat $surat)
    {
        Gate::authorize('update', $surat);

        $surat->load('penerima', 'lampiran');

        return view('surat.edit', compact('surat'));
    }

    /** Simpan perubahan draft (bisa langsung dikirim). */
    public function update(Request $request, Surat $surat)
    {
        Gate::authorize('update', $surat);

        $data  = $request->validate($this->rules($request, $surat), $this->messages());
        $kirim = $data['aksi'] === 'kirim';

        DB::transaction(function () use ($request, $surat, $data, $kirim) {
            $surat->fill([
                'perihal' => $data['perihal'],
                'isi'     => $data['isi'] ?? null,
                'sifat'   => $data['sifat'] ?? null,
            ] + $this->dataLembar($request, $data));

            if ($kirim) {
                $surat->status     = 'terkirim';
                $surat->dikirim_at = now();
            }

            $surat->save();

            $this->simpanPenerima($surat, $data['bagian_tujuan'] ?? [], $data['bagian_tembusan'] ?? []);
            $this->hapusLampiran($surat, $this->idHapus($request));
            $this->simpanLampiran($surat, $request->file('lampiran', []));

            if ($kirim) {
                $this->beriNomorAgenda($surat);
            }
        });

        if ($kirim) {
            $this->kirimNotifikasi($surat);
        }

        return redirect()
            ->route('dashboard', ['folder' => $kirim ? 'terkirim' : 'draft'])
            ->with('status', $kirim
                ? 'Surat berhasil dikirim. Tujuan: ' . $surat->tujuan_label . '.'
                : 'Draft berhasil diperbarui.');
    }

    /** Hapus surat (file lampiran ikut terhapus lewat event model). */
    public function destroy(Surat $surat)
    {
        Gate::authorize('delete', $surat);

        $folder = $surat->status === 'draft' ? 'draft' : 'masuk';
        $surat->delete();

        return redirect()->route('dashboard', ['folder' => $folder])->with('status', 'Surat berhasil dihapus.');
    }

    /** Arsipkan / keluarkan dari arsip (khusus bagian penerima yang bersangkutan). */
    public function arsip(Request $request, Surat $surat)
    {
        Gate::authorize('archive', $surat);

        $penerima = $surat->penerima()->where('bagian', $request->user()->bagian)->firstOrFail();

        $diarsipkan = is_null($penerima->diarsipkan_at);
        $penerima->update(['diarsipkan_at' => $diarsipkan ? now() : null]);

        return redirect()
            ->route('dashboard', ['folder' => $diarsipkan ? 'masuk' : 'arsip'])
            ->with('status', $diarsipkan ? 'Surat dipindahkan ke Arsip.' : 'Surat dikembalikan ke Kotak Masuk.');
    }

    /** Konfirmasi surat diterima oleh bagian pengguna, lalu beri tahu pengirim. */
    public function terima(Request $request, Surat $surat)
    {
        Gate::authorize('terima', $surat);

        $user     = $request->user();
        $penerima = $surat->penerima()->where('bagian', $user->bagian);

        // Menerima berarti sudah membaca
        (clone $penerima)->whereNull('dibaca_at')->update(['dibaca_at' => now()]);

        // Update bersyarat: hanya satu pegawai per bagian yang tercatat sebagai penerima
        $baru = (clone $penerima)->whereNull('diterima_at')->update([
            'diterima_at'   => now(),
            'diterima_oleh' => $user->id,
        ]);

        if (! $baru) {
            return back()->with('status', 'Surat ini sudah diterima oleh bagian Anda.');
        }

        $surat->loadMissing('pengirim');
        $surat->pengirim->notify(new SuratDiterimaNotification(
            $surat,
            config('bpkad.bagian.' . $user->bagian, $user->bagian),
            $user->name,
        ));

        return back()->with('status', 'Surat telah diterima. Pengirim sudah diberi tahu.');
    }

    /** Simpan / hapus dari Disimpan (bookmark pribadi). */
    public function simpan(Request $request, Surat $surat)
    {
        Gate::authorize('view', $surat);

        abort_if($surat->status !== 'terkirim', 404);

        $hasil = $request->user()->simpanan()->toggle($surat->id);

        return back()->with('status', count($hasil['attached']) > 0
            ? 'Surat disimpan.'
            : 'Surat dihapus dari Disimpan.');
    }

    /**
     * Nomor agenda: satu urutan untuk seluruh BPKAD, dimulai dari 1 setiap tahun.
     * Harus dipanggil di dalam transaksi.
     */
    private function beriNomorAgenda(Surat $surat): void
    {
        $tahun = now()->year;

        $terakhir = Surat::where('tahun_agenda', $tahun)->lockForUpdate()->max('nomor_agenda');

        $surat->forceFill([
            'tahun_agenda' => $tahun,
            'nomor_agenda' => ((int) $terakhir) + 1,
        ])->save();
    }

    /** Kolom lembar disposisi. Bagian disposisi hanya diisi oleh admin (Sekretariat). */
    private function dataLembar(Request $request, array $data): array
    {
        $hasil = [
            'nomor_surat'   => $data['nomor_surat'] ?? null,
            'tanggal_surat' => $data['tanggal_surat'] ?? null,
        ];

        if ($request->user()->isAdmin()) {
            $hasil += [
                'surat_dari'        => $data['surat_dari'] ?? null,
                'instruksi'         => array_values($data['instruksi'] ?? []) ?: null,
                'instruksi_lain'    => $data['instruksi_lain'] ?? null,
                'isi_disposisi'     => $data['isi_disposisi'] ?? null,
                'pemberi_disposisi' => $data['pemberi_disposisi'] ?? null,
                'keterangan'        => $data['keterangan'] ?? null,
                'diteruskan_ke'     => $data['diteruskan_ke'] ?? null,
            ];
        }

        return $hasil;
    }

    /** Ganti daftar penerima: tujuan utama + tembusan. */
    private function simpanPenerima(Surat $surat, array $tujuan, array $tembusan): void
    {
        $surat->penerima()->delete();

        $baris = collect($tujuan)->unique()
            ->map(fn ($b) => ['bagian' => $b, 'jenis' => 'utama'])
            ->concat(
                collect($tembusan)->unique()->map(fn ($b) => ['bagian' => $b, 'jenis' => 'tembusan'])
            );

        $surat->penerima()->createMany($baris->all());
    }

    /** Simpan file lampiran ke disk privat (storage/app/private/lampiran/{id}). */
    private function simpanLampiran(Surat $surat, array $files): void
    {
        foreach ($files as $file) {
            $nama   = Str::limit($file->getClientOriginalName(), 200, '');
            $ukuran = $file->getSize();
            $mime   = $file->getMimeType();

            $path = $file->store('lampiran/' . $surat->id, 'local');

            $surat->lampiran()->create([
                'nama_asli' => $nama,
                'path'      => $path,
                'ukuran'    => $ukuran,
                'mime'      => $mime,
            ]);
        }
    }

    private function hapusLampiran(Surat $surat, array $ids): void
    {
        if (! $ids) {
            return;
        }

        // Dibatasi pada lampiran milik surat ini saja
        $surat->lampiran()->whereIn('id', $ids)->get()->each(function ($l) {
            Storage::disk('local')->delete($l->path);
            $l->delete();
        });
    }

    private function idHapus(Request $request): array
    {
        return collect((array) $request->input('hapus_lampiran', []))
            ->filter(fn ($v) => is_numeric($v))
            ->map(fn ($v) => (int) $v)
            ->values()
            ->all();
    }

    /** Notifikasi: konfirmasi untuk pengirim + pemberitahuan untuk tiap bagian penerima. */
    private function kirimNotifikasi(Surat $surat): void
    {
        $surat->load(['pengirim', 'penerima']);

        $surat->pengirim->notify(new SuratTerkirimNotification($surat));

        foreach (['utama', 'tembusan'] as $jenis) {
            $bagian = $surat->penerima->where('jenis', $jenis)->pluck('bagian');

            if ($bagian->isEmpty()) {
                continue;
            }

            $pegawai = User::whereIn('bagian', $bagian)
                ->where('id', '!=', $surat->pengirim_id)
                ->get();

            Notification::send($pegawai, new SuratMasukNotification($surat, $jenis));
        }
    }

    private function rules(Request $request, ?Surat $surat = null): array
    {
        $kirim = $request->input('aksi') === 'kirim';
        $user  = $request->user();
        $opsi  = array_column($user->opsiTujuan(), 'key');
        $cfg   = config('bpkad.lampiran');

        // Sisa kuota lampiran (lampiran lama yang ditandai hapus tidak dihitung)
        $ada     = $surat ? $surat->lampiran()->count() : 0;
        $dihapus = $surat ? $surat->lampiran()->whereIn('id', $this->idHapus($request))->count() : 0;
        $sisa    = max(0, $cfg['maks_file'] - ($ada - $dihapus));

        // Isi surat boleh kosong jika surat dilampirkan sebagai file
        $baru        = $request->file('lampiran', []);
        $adaLampiran = ($ada - $dihapus + (is_array($baru) ? count($baru) : 0)) > 0;

        $tujuanDipilih = array_filter((array) $request->input('bagian_tujuan', []), 'is_scalar');

        $rules = [
            'aksi'              => ['required', 'in:kirim,draft'],
            'bagian_tujuan'     => [$kirim ? 'required' : 'nullable', 'array'],
            'bagian_tujuan.*'   => ['distinct', Rule::in($opsi)],
            'bagian_tembusan'   => ['nullable', 'array'],
            'bagian_tembusan.*' => ['distinct', Rule::in($opsi), Rule::notIn($tujuanDipilih)],
            'perihal'           => ['required', 'string', 'max:255'],
            'sifat'             => ['nullable', Rule::in(array_keys(config('bpkad.sifat')))],
            'isi'               => [$kirim && ! $adaLampiran ? 'required' : 'nullable', 'string'],
            'nomor_surat'       => ['nullable', 'string', 'max:100'],
            'tanggal_surat'     => ['nullable', 'date'],
            'lampiran'          => ['nullable', 'array', 'max:' . $sisa],
            'lampiran.*'        => ['file', 'mimes:' . implode(',', $cfg['ekstensi']), 'max:' . $cfg['maks_kb']],
            'hapus_lampiran'    => ['nullable', 'array'],
        ];

        // Kolom disposisi: hanya admin (Sekretariat)
        if ($user->isAdmin()) {
            $rules += [
                'surat_dari'        => ['nullable', 'string', 'max:150'],
                'instruksi'         => ['nullable', 'array'],
                'instruksi.*'       => [Rule::in(array_keys(config('bpkad.instruksi')))],
                'instruksi_lain'    => ['nullable', 'string', 'max:150'],
                'isi_disposisi'     => ['nullable', 'string', 'max:2000'],
                'pemberi_disposisi' => ['nullable', 'string', 'max:100'],
                'keterangan'        => ['nullable', 'string', 'max:1000'],
                'diteruskan_ke'     => ['nullable', 'string', 'max:255'],
            ];
        }

        return $rules;
    }

    private function messages(): array
    {
        $maks = config('bpkad.lampiran.maks_file');

        return [
            'bagian_tujuan.required'     => 'Pilih minimal satu bagian tujuan.',
            'bagian_tujuan.*.in'         => 'Ada bagian tujuan yang tidak valid.',
            'bagian_tujuan.*.distinct'   => 'Ada bagian tujuan yang dipilih dua kali.',
            'bagian_tembusan.*.in'       => 'Ada bagian tembusan yang tidak valid.',
            'bagian_tembusan.*.distinct' => 'Ada bagian tembusan yang dipilih dua kali.',
            'bagian_tembusan.*.not_in'   => 'Satu bagian tidak bisa menjadi tujuan sekaligus tembusan.',
            'perihal.required'           => 'Perihal wajib diisi.',
            'sifat.in'                   => 'Sifat surat tidak valid.',
            'isi.required'               => 'Isi surat wajib diisi, atau lampirkan file surat.',
            'tanggal_surat.date'         => 'Tanggal surat tidak valid.',
            'instruksi.*.in'             => 'Ada instruksi disposisi yang tidak valid.',
            'lampiran.max'               => "Lampiran maksimal {$maks} file per surat.",
            'lampiran.*.mimes'           => 'Format lampiran harus PDF, Word, Excel, JPG, atau PNG.',
            'lampiran.*.max'             => 'Ukuran tiap lampiran maksimal 5 MB.',
            'lampiran.*.uploaded'        => 'Lampiran gagal diunggah. Ukuran file melebihi batas server.',
            'lampiran.*.file'            => 'Lampiran harus berupa file.',
        ];
    }
}