<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratPenerima extends Model
{
    protected $table = 'surat_penerima';

    protected $fillable = ['surat_id', 'bagian', 'jenis', 'dibaca_at', 'diterima_at', 'diterima_oleh', 'diarsipkan_at'];

    protected function casts(): array
    {
        return [
            'dibaca_at'     => 'datetime',
            'diterima_at'   => 'datetime',
            'diarsipkan_at' => 'datetime',
        ];
    }

    public function surat(): BelongsTo
    {
        return $this->belongsTo(Surat::class);
    }

    /** Pegawai yang mengonfirmasi penerimaan untuk bagian ini. */
    public function yangMenerima(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diterima_oleh');
    }
}