<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;

class SubscriptionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function boot()
    {
        Cashier::useCurrency('eur', '€');
    }
}
