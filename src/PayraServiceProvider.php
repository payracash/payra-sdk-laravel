<?php
namespace Payra;

use Payra\Http\Middleware\PayraAuthMiddleware;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Payra\Support\PayraHelper;

class PayraServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/payra.php', 'payra');

        $this->app->singleton(Payra::class, function () {
            return new Payra();
        });
    }

    public function boot(Router $router)
    {
        $this->publishes([
            __DIR__ . '/../config/payra.php' => config_path('payra.php'),
        ], 'payra-config');

        $router->aliasMiddleware('payra.auth', PayraAuthMiddleware::class);

        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');

        $this->app->booted(function () {
            $config = config('payra');

            foreach (['polygon', 'ethereum'] as $network) {
                $config[$network]['rpc_urls'] = PayraHelper::rpcUrls($network);
            }

            config(['payra' => $config]);
        });
    }
}
