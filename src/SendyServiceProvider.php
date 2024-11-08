<?php

namespace Heterodoks\LaravelSendy;

use Illuminate\Support\ServiceProvider;

class SendyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/Config/sendy.php', 'sendy'
        );

        $this->app->singleton('sendy', function ($app) {
            return new Sendy($app['config']['sendy']);
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/Config/sendy.php' => config_path('sendy.php'),
        ], 'sendy-config');
    }
} 