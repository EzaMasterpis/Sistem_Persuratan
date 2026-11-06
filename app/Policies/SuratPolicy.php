<?php

namespace App\Policies;

use App\Models\Surat;
use App\Models\User;

class SuratPolicy
{
    /** Pengirim, atau pegawai di salah satu bagian penerima (hanya surat terkirim). */
    public function view(User $user, Surat $surat): bool
    {
        if ($surat->pengirim_id === $user->id) {
            return true;
        }

        return $surat->status === 'terkirim' && $surat->diterimaOleh($user->bagian);
    }

    /** Hanya draft milik sendiri yang boleh diedit. */
    public function update(User $user, Surat $surat): bool
    {
        return $surat->status === 'draft' && $surat->pengirim_id === $user->id;
    }

    /** Draft: pengirimnya. Surat terkirim: hanya admin (Sekretariat). */
    public function delete(User $user, Surat $surat): bool
    {
        if ($surat->status === 'draft') {
            return $surat->pengirim_id === $user->id;
        }

        return $user->isAdmin() && $this->view($user, $surat);
    }

    /** Pengarsipan dilakukan oleh bagian penerima. */
    public function archive(User $user, Surat $surat): bool
    {
        return $surat->status === 'terkirim' && $surat->diterimaOleh($user->bagian);
    }

    /** Konfirmasi penerimaan: hanya pegawai di bagian penerima (utama atau tembusan), bukan pengirim. */
    public function terima(User $user, Surat $surat): bool
    {
        return $surat->status === 'terkirim'
            && $surat->pengirim_id !== $user->id
            && $surat->diterimaOleh($user->bagian);
    }
}