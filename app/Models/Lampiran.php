<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lampiran extends Model
{
    protected $table = 'lampiran';

    protected $fillable = ['surat_id', 'nama_asli', 'path', 'ukuran', 'mime'];

    public function surat(): BelongsTo
    {
        return $this->belongsTo(Surat::class);
    }

    /** Ekstensi diambil dari nama file di server (bukan dari nama kiriman pengguna). */
    public function getEkstensiAttribute(): string
    {
        return strtolower(pathinfo($this->path, PATHINFO_EXTENSION));
    }

    public function getBisaDilihatAttribute(): bool
    {
        return in_array($this->ekstensi, ['pdf', 'jpg', 'jpeg', 'png'], true);
    }

    public function getUkuranLabelAttribute(): string
    {
        return $this->ukuran >= 1048576
            ? number_format($this->ukuran / 1048576, 1) . ' MB'
            : max(1, (int) round($this->ukuran / 1024)) . ' KB';
    }
}