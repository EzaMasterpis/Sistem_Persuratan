<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lampiran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_id')->constrained('surat')->cascadeOnDelete();
            $table->string('nama_asli');
            $table->string('path');
            $table->unsignedBigInteger('ukuran')->default(0);
            $table->string('mime', 100)->nullable();
            $table->timestamps();
        });

        Schema::table('surat_penerima', function (Blueprint $table) {
            $table->string('jenis', 10)->default('utama')->after('bagian'); // utama | tembusan
        });
    }

    public function down(): void
    {
        Schema::table('surat_penerima', function (Blueprint $table) {
            $table->dropColumn('jenis');
        });

        Schema::dropIfExists('lampiran');
    }
};