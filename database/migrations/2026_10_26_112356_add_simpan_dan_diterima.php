<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_penerima', function (Blueprint $table) {
            $table->timestamp('diterima_at')->nullable()->after('dibaca_at');
            $table->foreignId('diterima_oleh')->nullable()->constrained('users')->nullOnDelete();
        });

        Schema::create('surat_simpanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('surat_id')->constrained('surat')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'surat_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_simpanan');

        Schema::table('surat_penerima', function (Blueprint $table) {
            $table->dropConstrainedForeignId('diterima_oleh');
            $table->dropColumn('diterima_at');
        });
    }
};