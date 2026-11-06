<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengirim_id')->constrained('users')->cascadeOnDelete();
            $table->string('bagian_tujuan', 30)->nullable()->index();
            $table->string('perihal');
            $table->longText('isi')->nullable();
            $table->string('sifat', 20)->nullable();           // penting / segera / rahasia / null
            $table->string('status', 10)->default('draft')->index(); // draft / terkirim
            $table->timestamp('dikirim_at')->nullable();
            $table->timestamp('dibaca_at')->nullable();
            $table->timestamp('diarsipkan_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat');
    }
};