<?php

namespace App\Providers;

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

    protected $routes = [
        'account',
        'auth',
        'users',
        'organizations',
        'teams',
        'teamInvitations',
        'projects',
        'projectInvitations',
        'subscriptionPlans',
        'subscriptionProducts',
        'subscriptionOptions',
        'options',
        'activeSubscriptions',
        'customers',
        'cards',
        'webhooks',
        'nodes',
        'translations',
        'memos',
        'invoices',
        'repositories',
    ];

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
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
        Route::prefix('api/v1')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(function () {
                foreach ($this->routes as $route) {
                    require base_path("routes/{$route}.php");
                }
            })
        ;
    }
}
