<?php

namespace App\Notifications;

use App\Models\Surat;
use Illuminate\Notifications\Notification;

class SuratDiterimaNotification extends Notification
{
    public function __construct(
        public Surat $surat,
        public string $namaBagian,
        public string $namaPenerima,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'tipe'     => 'diterima',
            'surat_id' => $this->surat->id,
            'judul'    => 'Surat diterima oleh ' . $this->namaBagian,
            'pesan'    => $this->surat->perihal . ' (diterima oleh ' . $this->namaPenerima . ')',
            'sifat'    => $this->surat->sifat,
        ];
    }
}