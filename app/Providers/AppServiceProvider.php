<?php

namespace App\Providers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
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
        // Yetki/Gate işlemleri:

        // Sistem yöneticisine (admin) her zaman tam yetki ver.
        Gate::before(function (User $user, $ability) {
            if ($user->isAdmin()) {
                return true;
            }
        });

        // Diğer spesifik yetkileri mevcut role/permissions sistemine bağla (UserController json)
        $custom_permissions = [
            'manage_users',
            'view_aboneler',
            'manage_aboneler',
            'manage_bolgeler',
            'view_logs',
            'view_reports',
            'upload_faturalar',
            'view_import_logs',
            'view_staging_faturalar',
            'view_kesinlesen_faturalar',
            'view_anomali_faturalar',
            'view_reaktif_faturalar',
            'view_itirazlar',
            'onay_faturalar',
            'odeme_faturalar',
            'manage_support',
            'view_tesis_bilgi_sistemi',
        ];

        foreach ($custom_permissions as $permission) {
            Gate::define($permission, function (User $user) use ($permission) {
                return $user->hasPermission($permission);
            });
        }

        // Login & Logout Logları
        Event::listen(Login::class, function (Login $event) {
            ActivityLog::create([
                'user_id' => $event->user->id,
                'action' => 'login',
                'description' => 'Sisteme güvenli giriş yapıldı.',
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        Event::listen(Logout::class, function (Logout $event) {
            ActivityLog::create([
                'user_id' => $event->user ? $event->user->id : null,
                'action' => 'logout',
                'description' => 'Sistemden güvenli çıkış yapıldı.',
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });
    }
}
