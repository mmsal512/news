<?php

namespace App\Providers;

use App\Actions\Jetstream\DeleteUser;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Laravel\Jetstream\Jetstream;

class JetstreamServiceProvider extends ServiceProvider
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
        $this->configurePermissions();

        Jetstream::deleteUsersUsing(DeleteUser::class);

        Vite::prefetch(concurrency: 3);
    }

    /**
     * Configure the permissions that are available within the application.
     */
    protected function configurePermissions(): void
    {
        Jetstream::defaultApiTokenPermissions(['read']);

        Jetstream::role('admin', 'Administrator', [
            'article:create',
            'article:read',
            'article:update',
            'article:delete',
            'user:manage',
            'source:manage',
        ])->description('مدير النظام لديه صلاحيات كاملة');

        Jetstream::role('editor', 'Editor', [
            'article:create',
            'article:read',
            'article:update',
            'article:approve',
        ])->description('المحرر يمكنه إدارة المقالات');
    }
}
