<?php

namespace App\Providers;

use App\Models\Surat;
use App\Models\SuratPenerima;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            $user = auth()->user();

            if (! $user) {
                return;
            }

            $badge = [
                'masuk' => SuratPenerima::where('bagian', $user->bagian)
                    ->whereNull('dibaca_at')
                    ->whereNull('diarsipkan_at')
                    ->whereHas('surat', fn ($q) => $q->where('status', 'terkirim'))
                    ->count(),
                'draft' => Surat::draft()->where('pengirim_id', $user->id)->count(),
            ];

            $folders = collect(config('bpkad.folders'))
                ->map(fn ($f, $key) => $f + ['badge' => $badge[$key] ?? 0])
                ->all();

            $view->with([
                'folders'     => $folders,
                'notifUnread' => $user->unreadNotifications()->count(),
                'notifLatest' => $user->notifications()->limit(8)->get(),
            ]);
        });
    }
}
