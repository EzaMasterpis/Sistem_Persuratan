<?php

namespace App\Http\Controllers;

use App\Models\Lampiran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class LampiranController extends Controller
{
    /** Unduh lampiran, atau tampilkan di tab baru (khusus PDF dan gambar) bila ?tampil=1. */
    public function unduh(Request $request, Lampiran $lampiran)
    {
        // Hak akses mengikuti surat induknya
        Gate::authorize('view', $lampiran->surat);

        $disk = Storage::disk('local');

        abort_unless($disk->exists($lampiran->path), 404);

        if ($request->boolean('tampil') && $lampiran->bisa_dilihat) {
            return $disk->response($lampiran->path, $lampiran->nama_asli, [
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        return $disk->download($lampiran->path, $lampiran->nama_asli);
    }
}