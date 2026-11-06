<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_penerima', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_id')->constrained('surat')->cascadeOnDelete();
            $table->string('bagian', 30);
            $table->timestamp('dibaca_at')->nullable();
            $table->timestamp('diarsipkan_at')->nullable();
            $table->timestamps();

            $table->unique(['surat_id', 'bagian']);
            $table->index('bagian');
        });

        // Pindahkan data lama (1 penerima per surat) ke tabel baru
        DB::table('surat')->whereNotNull('bagian_tujuan')->orderBy('id')->each(function ($s) {
            DB::table('surat_penerima')->insert([
                'surat_id'      => $s->id,
                'bagian'        => $s->bagian_tujuan,
                'dibaca_at'     => $s->dibaca_at,
                'diarsipkan_at' => $s->diarsipkan_at,
                'created_at'    => $s->created_at,
                'updated_at'    => $s->updated_at,
            ]);
        });

        Schema::table('surat', function (Blueprint $table) {
            $table->dropIndex(['bagian_tujuan']);
            $table->dropColumn(['bagian_tujuan', 'dibaca_at', 'diarsipkan_at']);
        });
    }

    public function down(): void
    {
        // Catatan: jika satu surat punya banyak penerima, data tambahan hilang saat rollback.
        Schema::table('surat', function (Blueprint $table) {
            $table->string('bagian_tujuan', 30)->nullable()->index();
            $table->timestamp('dibaca_at')->nullable();
            $table->timestamp('diarsipkan_at')->nullable();
        });

        Schema::dropIfExists('surat_penerima');
    }
};