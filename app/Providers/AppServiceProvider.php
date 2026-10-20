<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
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
        Gate::define('admin-panel', fn (User $user): Response => in_array($user->role, ['admin', 'editor'], true)
            ? Response::allow()
            : Response::deny('Недостаточно прав для доступа к админ-панели'));

        Paginator::defaultView('pagination::default');
        Model::preventLazyLoading(! app()->isProduction());
        DB::enableQueryLog();
    }
}
