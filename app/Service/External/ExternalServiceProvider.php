<?php

declare(strict_types=1);

namespace App\Service\External;

use App\DTO\StdClassFactory;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Psr\Log\LoggerInterface;

class ExternalServiceProvider extends ServiceProvider implements DeferrableProvider
{
    private const GET_CLASSES = [
        ExternalPatientService::class,
        ExternalPhysicianService::class,
        ExternalClinicService::class
    ];
    private const POST_CLASSES = [
        ExternalMetricService::class,
    ];
    private const CLASSES = [
        ...self::GET_CLASSES,
        ...self::POST_CLASSES,
    ];

    public function boot(
        Repository $cache,
        LoggerInterface $logger,
        StdClassFactory $factory
    ): void {
        foreach (self::GET_CLASSES as $class) {
            $this->app->bind(
                $class,
                static function () use ($logger, $factory, $cache, $class) {
                    return new $class(
                        $cache,
                        $logger,
                        $factory
                    );
                }
            );
        }
        foreach (self::POST_CLASSES as $class) {
            $this->app->bind(
                $class,
                static function () use ($logger, $factory, $class) {
                    return new $class(
                        $logger,
                        $factory
                    );
                }
            );
        }
    }

    public function provides(): array
    {
        return self::CLASSES;
    }
}
