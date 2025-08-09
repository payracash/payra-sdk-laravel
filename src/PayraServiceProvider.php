<?php

namespace Payra;

use Illuminate\Support\ServiceProvider;

class PayraServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/payra.php', 'payra');

        $this->app->singleton(Payra::class, function () {
            return new Payra();
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/payra.php' => config_path('payra.php'),
        ], 'payra-config');

        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
    }
}
