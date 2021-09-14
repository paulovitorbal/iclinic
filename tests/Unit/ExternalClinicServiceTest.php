<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\StdClassFactory;
use App\Service\External\ExternalClinicService;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Config;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Tests\TestCase;

class ExternalClinicServiceTest extends TestCase
{
    public function testGetSuccessfulRequest(): void
    {
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

        $mock = new MockHandler(
            [
                new Response(200, [], $this->getAssetContents('success-get-clinics-by-id.json'))
            ]
        );
        $testHandler = new TestHandler();
        $logger = new Logger('test', [$testHandler]);

        /** @var Repository $cache */
        $cache = $this->app->make(Repository::class);

        $externalService = new ExternalClinicService(
            $cache,
            $logger,
            new StdClassFactory(),
            $mock
        );
        $cachedPatient = $cache->get('/clinics/1');
        $this->assertNull($cachedPatient);

        $physician = $externalService->getClinic(1);

        $this->assertEquals('Kenneth Torp DDS', $physician->getName());
        $this->assertEquals('1', $physician->getId());

        $cachedClinic = $externalService->getClinic(1);

        $this->assertEquals('Kenneth Torp DDS', $cachedClinic->getName());
        $this->assertEquals('1', $cachedClinic->getId());
    }

    private function getAssetContents(string $filename): string
    {
        return file_get_contents(__DIR__ . '/assets/ExternalConsumer/' . $filename);
    }
}
