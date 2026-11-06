<?php

namespace App\Notifications;

use App\Models\Surat;
use Illuminate\Notifications\Notification;

class SuratTerkirimNotification extends Notification
{
    public function __construct(public Surat $surat)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $this->surat->loadMissing('penerima');

        $pesan = $this->surat->perihal . ' (kepada ' . $this->surat->tujuan_label;

        if ($tembusan = $this->surat->tembusan_label) {
            $pesan .= '; tembusan ' . $tembusan;
        }

        return [
            'tipe'     => 'terkirim',
            'surat_id' => $this->surat->id,
            'judul'    => 'Surat berhasil terkirim',
            'pesan'    => $pesan . ')',
            'sifat'    => $this->surat->sifat,
        ];
    }
}