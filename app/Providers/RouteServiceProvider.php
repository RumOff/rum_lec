<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected $adminNamespace = 'App\Http\Controllers\Admin';
    protected $userNamespace = 'App\Http\Controllers\User';

    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const USER_HOME = '/surveys';
    public const ADMIN_HOME = 'admin/companies';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        // $this->configureRateLimiting();

        Route::pattern('adminId', '[0-9]+');
        Route::pattern('companyId', '[0-9]+');
        Route::pattern('surveyId', '[0-9]+');
        Route::pattern('surveyAnswerId', '[0-9]+');
        Route::pattern('surveyDeliveryId', '[0-9]+');
        Route::pattern('surveyTargetId', '[0-9]+');
        Route::pattern('userId', '[0-9]+');

        $this->routes(function () {
            Route::middleware('web')
            ->group(base_path('routes/web.php'));

            Route::middleware('web')
                ->prefix('admin')
                ->as('admin.')
                ->namespace($this->adminNamespace)
                ->group(base_path('routes/admin.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
