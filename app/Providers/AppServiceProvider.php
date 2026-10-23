<?php

namespace App\Providers;

use App\Contracts\SmsSender;
use App\Models\User;
use App\Services\Sms\LogSmsSender;
use App\Services\Sms\NullSmsSender;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SmsSender::class, function (): SmsSender {
            return match (config('services.sms.driver')) {
                'log' => new LogSmsSender,
                default => new NullSmsSender,
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('admin-panel', fn (User $user): Response => in_array($user->role, ['admin', 'editor'], true)
            ? Response::allow()
            : Response::deny('Недостаточно прав для доступа к админ-панели'));

        Gate::define('manage-bookings', fn (User $user): Response => $user->role === 'admin'
            ? Response::allow()
            : Response::deny('Только администратор управляет заказами'));

        Paginator::defaultView('pagination::default');
        Model::preventLazyLoading(! app()->isProduction());
    }
}
