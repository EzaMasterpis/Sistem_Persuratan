<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat', function (Blueprint $table) {
            $table->string('surat_dari', 150)->nullable();
            $table->string('nomor_surat', 100)->nullable();
            $table->date('tanggal_surat')->nullable();

            $table->unsignedInteger('nomor_agenda')->nullable();
            $table->unsignedSmallInteger('tahun_agenda')->nullable();

            $table->json('instruksi')->nullable();
            $table->string('instruksi_lain', 150)->nullable();
            $table->text('isi_disposisi')->nullable();
            $table->string('pemberi_disposisi', 100)->nullable();
            $table->text('keterangan')->nullable();
            $table->string('diteruskan_ke', 255)->nullable();
        });

        // Beri nomor agenda untuk surat terkirim yang sudah ada (urut per tahun)
        $urut = [];

        DB::table('surat')
            ->where('status', 'terkirim')
            ->orderBy('dikirim_at')
            ->orderBy('id')
            ->each(function ($s) use (&$urut) {
                $tahun = (int) date('Y', strtotime($s->dikirim_at ?? $s->created_at));
                $urut[$tahun] = ($urut[$tahun] ?? 0) + 1;

                DB::table('surat')->where('id', $s->id)->update([
                    'tahun_agenda' => $tahun,
                    'nomor_agenda' => $urut[$tahun],
                ]);
            });

        Schema::table('surat', function (Blueprint $table) {
            $table->unique(['tahun_agenda', 'nomor_agenda']);
        });
    }

    public function down(): void
    {
        Schema::table('surat', function (Blueprint $table) {
            $table->dropUnique(['tahun_agenda', 'nomor_agenda']);
            $table->dropColumn([
                'surat_dari', 'nomor_surat', 'tanggal_surat', 'nomor_agenda', 'tahun_agenda',
                'instruksi', 'instruksi_lain', 'isi_disposisi', 'pemberi_disposisi',
                'keterangan', 'diteruskan_ke',
            ]);
        });
    }
};