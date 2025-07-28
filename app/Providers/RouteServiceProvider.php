<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    protected $namespace = 'App\Http\Controllers';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();
    }
   

    protected function mapWebRoutes() {
        Route::middleware('web')
                // ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));

    }    

    protected function mapAuthRoutes() {
        Route::middleware('web')
            ->prefix('auth')
            ->group(base_path('routes/auth.php'));   

    }    

    protected function mapAdminRoutes() {

        Route::middleware('admin')
            ->namespace($this->namespace)
            ->prefix('admin')
            ->group(base_path('routes/admin.php'));   
    }


    protected function mapSuperadminRoutes() {
    
        Route::middleware('superadmin')
            ->namespace($this->namespace)
            ->prefix('superadmin')
            ->group(base_path('routes/superadmin.php'));   
    }

    protected function mapDealerRoutes() {

        Route::middleware('dealer')
            ->namespace($this->namespace)
            ->prefix('dealer')
            ->group(base_path('routes/dealer.php'));   
    }

    protected function mapUserRoutes() {

        Route::middleware('staff')
            ->namespace($this->namespace)
            ->prefix('staff')
            ->group(base_path('routes/staff.php'));   
    }

    protected function mapAPIRoutes() {

        Route::namespace($this->namespace)
            ->prefix('api')
            ->group(base_path('routes/api.php'));   
    }


    public function map() {
        $this->mapWebRoutes();
        $this->mapAuthRoutes();
        $this->mapAdminRoutes();
        $this->mapSuperadminRoutes();
        $this->mapDealerRoutes();
        $this->mapUserRoutes();
        $this->mapAPIRoutes();
    }
  

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
