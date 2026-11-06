<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class Surat extends Model
{
    protected $table = 'surat';

    protected $fillable = [
        'pengirim_id', 'perihal', 'isi', 'sifat', 'status', 'dikirim_at',
        'surat_dari', 'nomor_surat', 'tanggal_surat',
        'instruksi', 'instruksi_lain', 'isi_disposisi',
        'pemberi_disposisi', 'keterangan', 'diteruskan_ke',
    ];

    protected function casts(): array
    {
        return [
            'dikirim_at'    => 'datetime',
            'tanggal_surat' => 'date',
            'instruksi'     => 'array',
        ];
    }

    protected static function booted(): void
    {
        // Hapus folder lampiran dari disk saat surat dihapus
        static::deleting(function (Surat $surat) {
            Storage::disk('local')->deleteDirectory('lampiran/' . $surat->id);
        });
    }

    public function pengirim(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pengirim_id');
    }

    public function penerima(): HasMany
    {
        return $this->hasMany(SuratPenerima::class);
    }

    public function lampiran(): HasMany
    {
        return $this->hasMany(Lampiran::class);
    }

    public function disimpanOleh(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'surat_simpanan')->withTimestamps();
    }

    public function scopeTerkirim(Builder $query): Builder
    {
        return $query->where('status', 'terkirim');
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', 'draft');
    }

    /** Surat terkirim yang diterima suatu bagian (utama maupun tembusan). */
    public function scopeDiterima(Builder $query, ?string $bagian, bool $arsip = false): Builder
    {
        return $query->terkirim()->whereHas('penerima', function (Builder $p) use ($bagian, $arsip) {
            $p->where('bagian', $bagian);
            $arsip ? $p->whereNotNull('diarsipkan_at') : $p->whereNull('diarsipkan_at');
        });
    }

    /** Filter pencarian, sifat, dan rentang tanggal. */
    public function scopeSaring(Builder $query, array $f): Builder
    {
        $tanggal = 'DATE(COALESCE(dikirim_at, updated_at))';

        return $query
            ->when($f['q'] ?? null, fn ($q, $v) => $q->where(
                fn ($q) => $q->where('perihal', 'like', "%{$v}%")->orWhere('isi', 'like', "%{$v}%")
            ))
            ->when($f['sifat'] ?? null, fn ($q, $v) => $v === 'tanpa'
                ? $q->whereNull('sifat')
                : $q->where('sifat', $v))
            ->when($f['dari'] ?? null, fn ($q, $v) => $q->whereRaw("$tanggal >= ?", [$v]))
            ->when($f['sampai'] ?? null, fn ($q, $v) => $q->whereRaw("$tanggal <= ?", [$v]));
    }

    public function diterimaOleh(?string $bagian): bool
    {
        return $bagian !== null && $this->penerima->contains('bagian', $bagian);
    }

    /** Contoh: "Anggaran dan Perbendaharaan", atau "Semua bagian". */
    public function getTujuanLabelAttribute(): string
    {
        return $this->labelBagian($this->penerima->where('jenis', 'utama'), true);
    }

    public function getTembusanLabelAttribute(): string
    {
        return $this->labelBagian($this->penerima->where('jenis', 'tembusan'));
    }

    private function labelBagian(Collection $penerima, bool $ringkas = false): string
    {
        $bagian = $penerima->pluck('bagian');

        if ($bagian->isEmpty()) {
            return '';
        }

        // Semua bagian selain pengirim
        if ($ringkas && $bagian->count() >= count(config('bpkad.bagian')) - 1) {
            return 'Semua bagian';
        }

        return $bagian
            ->map(fn ($b) => config('bpkad.bagian.' . $b, $b))
            ->join(', ', ' dan ');
    }

    public function getTanggalAttribute()
    {
        return $this->dikirim_at ?? $this->updated_at;
    }

    /** Format ala Gmail: jam jika hari ini, tanggal singkat jika lainnya. */
    public function getTanggalSingkatAttribute(): string
    {
        $t = $this->tanggal;

        if ($t->isToday()) {
            return $t->format('H:i');
        }

        return $t->isCurrentYear() ? $t->translatedFormat('d M') : $t->translatedFormat('d/m/y');
    }
}