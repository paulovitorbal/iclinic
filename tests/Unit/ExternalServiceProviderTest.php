<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\StdClassFactory;
use App\Service\External\ExternalClinicService;
use App\Service\External\ExternalMetricService;
use App\Service\External\ExternalPatientService;
use App\Service\External\ExternalPhysicianService;
use App\Service\External\ExternalServiceProvider;
use Closure;
use Illuminate\Contracts\Cache\Repository;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

class ExternalServiceProviderTest extends TestCase
{

    public function testServiceProvider(): void
    {

        $serviceProvider = new ExternalServiceProvider($this->app);
        $serviceProvider->boot(
            new class () implements  Repository{
                public function get($key, $default = null)
                {
                }

                public function set($key, $value, $ttl = null)
                {
                }

                public function delete($key)
                {
                }

                public function clear()
                {
                }

                public function getMultiple($keys, $default = null)
                {
                }

                public function setMultiple($values, $ttl = null)
                {
                }

                public function deleteMultiple($keys)
                {
                }

                public function has($key)
                {
                }

                public function pull($key, $default = null)
                {
                }

                public function put($key, $value, $ttl = null)
                {
                }

                public function add($key, $value, $ttl = null)
                {
                }

                public function increment($key, $value = 1)
                {
                }

                public function decrement($key, $value = 1)
                {
                }

                public function forever($key, $value)
                {
                }

                public function remember($key, $ttl, Closure $callback)
                {
                }

                public function sear($key, Closure $callback)
                {
                }

                public function rememberForever($key, Closure $callback)
                {
                }

                public function forget($key)
                {
                }

                public function getStore()
                {
                }
            },
            $this->app->get(LoggerInterface::class),
            $this->app->get(StdClassFactory::class),
            false
        );
        $this->assertEquals(
            [
                ExternalPatientService::class,
                ExternalPhysicianService::class,
                ExternalClinicService::class,
                ExternalMetricService::class,
            ],
            $serviceProvider->provides()
        );
    }
}
