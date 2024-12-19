<?php

namespace App\Providers;

use App\Http\Middleware\RouteCheck;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();
        $this->mapApiv2Routes();
        $this->mapSharedRoutes();
        $this->mapAdminRoutes();
        $this->mapSellerRoutes();
        $this->mapProductManagerRoutes();
        $this->mapResellerRoutes();
        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapAdminRoutes()
    {
        Route::middleware(['web', RouteCheck::class])
            ->namespace($this->namespace)
            ->group(base_path('routes/admin.php'));
    }

    protected function mapSellerRoutes()
    {
        Route::middleware(['web', RouteCheck::class])
            ->namespace($this->namespace)
            ->group(base_path('routes/seller.php'));
    }

    protected function mapProductManagerRoutes()
    {
        Route::middleware(['web', RouteCheck::class])
            ->namespace($this->namespace)
            ->group(base_path('routes/product_manager.php'));
    }

    protected function mapResellerRoutes()
    {
        Route::middleware(['web', RouteCheck::class])
            ->namespace($this->namespace)
            ->group(base_path('routes/reseller.php'));
    }

    protected function mapWebRoutes()
    {
        Route::middleware(['web', RouteCheck::class])
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }


    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api/v1/api.php'));
    }

    protected function mapApiv2Routes()
    {
        Route::prefix('api/v2/seller')
            ->middleware('api')
            ->namespace("App\\Http\\Controllers\\api\\v2\\seller")
            ->group(base_path('routes/api/v2/seller/apis.php'));
    }

    protected function mapSharedRoutes()
    {
        Route::middleware(['web', RouteCheck::class])
            ->namespace($this->namespace)
            ->group(base_path('routes/shared.php'));
    }
}
