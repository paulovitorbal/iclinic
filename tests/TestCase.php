<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Support\Facades\Config;
use Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__ . '/../bootstrap/app.php';
    }

    protected function setUp(): void
    {
        parent::setUp();
        Config::set(
            'external-services.clinics',
            new \App\DTO\Config(
                host: 'https://5f71da6964a3720016e60ff8.mockapi.io/v1',
                route: '/clinics/%d',
                authentication: 'Bearer -----',
                timeout: 5,
                retry: 3,
                cacheTtl: 72 * 60
            )
        );
        Config::set(
            'external-services.patients',
            new \App\DTO\Config(
                host: 'https://5f71da6964a3720016e60ff8.mockapi.io/v1',
                route: '/patients/%d',
                authentication: 'bearer ----',
                timeout: 3,
                retry: 2,
                cacheTtl: 12 * 60
            )
        );
        Config::set(
            'external-services.physicians',
            new \App\DTO\Config(
                host: 'https://5f71da6964a3720016e60ff8.mockapi.io/v1',
                route: '/physicians/%d',
                authentication: 'Bearer 00000',
                timeout: 4,
                retry: 2,
                cacheTtl: 48 * 60
            )
        );
    }
}
