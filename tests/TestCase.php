<?php

namespace Heterodoks\LaravelSendy\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Heterodoks\LaravelSendy\SendyServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            SendyServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('sendy.url', 'http://test-sendy-url.com');
        $app['config']->set('sendy.api_key', 'test-api-key');
        $app['config']->set('sendy.brand_id', 'test-brand-id');
        $app['config']->set('sendy.timeout', 5);
    }
} 