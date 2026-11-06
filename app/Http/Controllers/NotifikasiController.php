<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    /** Buka notifikasi: tandai terbaca lalu arahkan ke surat terkait. */
    public function buka(Request $request, string $id)
    {
        $notif = $request->user()->notifications()->findOrFail($id);
        $notif->markAsRead();

        $suratId = $notif->data['surat_id'] ?? null;

        if ($suratId && Surat::whereKey($suratId)->exists()) {
            return redirect()->route('surat.show', [
                'surat'  => $suratId,
                'folder' => ($notif->data['tipe'] ?? '') === 'masuk' ? 'masuk' : 'terkirim',
            ]);
        }

        return redirect()->route('dashboard')->with('status', 'Surat pada notifikasi ini sudah tidak tersedia.');
    }

    public function bacaSemua(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }
}