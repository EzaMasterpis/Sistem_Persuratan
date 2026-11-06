<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\SuratController;
use App\Http\Controllers\LampiranController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [SuratController::class, 'index'])->name('dashboard');

    Route::resource('surat', SuratController::class)->except(['index', 'create']);
    Route::patch('/surat/{surat}/arsip', [SuratController::class, 'arsip'])->name('surat.arsip');

    Route::post('/notifikasi/baca-semua', [NotifikasiController::class, 'bacaSemua'])->name('notifikasi.baca-semua');
    Route::get('/notifikasi/{id}', [NotifikasiController::class, 'buka'])->name('notifikasi.buka');

    Route::post('/surat/{surat}/simpan', [SuratController::class, 'simpan'])->name('surat.simpan');
    Route::patch('/surat/{surat}/terima', [SuratController::class, 'terima'])->name('surat.terima');

    Route::get('/lampiran/{lampiran}', [LampiranController::class, 'unduh'])->name('lampiran.unduh');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});