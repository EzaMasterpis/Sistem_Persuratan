<?php

namespace App\Notifications;

use App\Models\Surat;
use Illuminate\Notifications\Notification;

class SuratMasukNotification extends Notification
{
    public function __construct(public Surat $surat, public string $jenis = 'utama')
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $this->surat->loadMissing('pengirim');
        $asal = config('bpkad.bagian.' . $this->surat->pengirim->bagian, 'bagian lain');

        return [
            'tipe'     => 'masuk',
            'jenis'    => $this->jenis,
            'surat_id' => $this->surat->id,
            'judul'    => ($this->jenis === 'tembusan' ? 'Tembusan surat dari ' : 'Surat baru dari ') . $asal,
            'pesan'    => $this->surat->perihal,
            'sifat'    => $this->surat->sifat,
        ];
    }
}