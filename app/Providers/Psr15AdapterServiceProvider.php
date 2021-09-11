<?php

namespace App\Providers;

use App\Middlewares\BadRequestInterceptor;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;
use Softonic\Laravel\Middleware\Psr15Bridge\Psr15MiddlewareAdapter;

class Psr15AdapterServiceProvider extends ServiceProvider
{
    public function boot(LoggerInterface $logger): void
    {
        $this->app->bind(
            BadRequestInterceptor::class,
            fn() => Psr15MiddlewareAdapter::adapt(new BadRequestInterceptor($logger))
        );
    }
}
