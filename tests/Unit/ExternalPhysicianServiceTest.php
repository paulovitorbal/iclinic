<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\StdClassFactory;
use App\Service\External\ExternalPhysicianService;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Config;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Tests\TestCase;

class ExternalPhysicianServiceTest extends TestCase
{
    public function testGetSuccessfulRequest(): void
    {
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

        $mock = new MockHandler(
            [
                new Response(200, [], $this->getAssetContents('success-get-physicians-by-id.json'))
            ]
        );
        $testHandler = new TestHandler();
        $logger = new Logger('test', [$testHandler]);

        /** @var Repository $cache */
        $cache = $this->app->make(Repository::class);

        $externalService = new ExternalPhysicianService(
            $cache,
            $logger,
            new StdClassFactory(),
            $mock
        );
        $cachedPatient = $cache->get('/physicians/1');
        $this->assertNull($cachedPatient);

        $physician = $externalService->getPhysician(1);

        $this->assertEquals('Wesley Marquardt', $physician->getName());
        $this->assertEquals('0bc31b08-04f2-4eb8-b1b4-fd52f21622f4', $physician->getCrm());
        $this->assertEquals('1', $physician->getId());

        $cachedPhysician = $externalService->getPhysician(1);

        $this->assertEquals('Wesley Marquardt', $cachedPhysician->getName());
        $this->assertEquals('0bc31b08-04f2-4eb8-b1b4-fd52f21622f4', $cachedPhysician->getCrm());
        $this->assertEquals('1', $cachedPhysician->getId());
    }

    private function getAssetContents(string $filename): string
    {
        return file_get_contents(__DIR__ . '/assets/ExternalConsumer/' . $filename);
    }
}
