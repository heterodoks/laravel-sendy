<?php

namespace Heterodoks\LaravelSendy\Facades;

use Illuminate\Support\Facades\Facade;

class Sendy extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'sendy';
    }
} 