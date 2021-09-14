<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\StdClassFactory;
use App\Service\ExternalService;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Config;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Tests\TestCase;

class ExternalServiceTest extends TestCase
{
    public function testGetSuccessfulRequest(): void
    {
        Config::set(
            'external-services.patients',
            [
                'host' => 'https://5f71da6964a3720016e60ff8.mockapi.io/v1',
                'route' => '/patients/%d',
                'authentication' => 'Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyLCJzZXJ2aWNlIjoicGF0aWVudHMifQ.Pr6Z58GzNRtjX8Y09hEBzl7dluxsGiaxGlfzdaphzVU',
                'timeout' => 3,
                'retry' => 2,
                'cacheTtl' => 12 * 60
            ]
        );
        $mock = new MockHandler(
            [
                new Response(200, [], $this->getAssetContents('success-get-patients-by-id.json'))
            ]
        );
        $testHandler = new TestHandler();
        $logger = new Logger('test', [$testHandler]);

        /** @var Repository $cache */
        $cache = $this->app->make(Repository::class);

        $externalService = new ExternalService(
            $cache,
            $logger,
            new StdClassFactory(),
            $mock
        );
        $cachedPatient = $cache->get('/patients/1');
        $this->assertNull($cachedPatient);

        $patient = $externalService->getPatient(1);

        $this->assertEquals('Boyd Crooks', $patient->getName());
        $this->assertEquals('Danial.Kassulke59@hotmail.com', $patient->getEmail());
        $this->assertEquals('413-218-5913 x9333', $patient->getPhone());
        $this->assertEquals('1', $patient->getId());

        $cachedPatient = $externalService->getPatient(1);;
        $this->assertEquals('Boyd Crooks', $cachedPatient->getName());
        $this->assertEquals('Danial.Kassulke59@hotmail.com', $cachedPatient->getEmail());
        $this->assertEquals('413-218-5913 x9333', $cachedPatient->getPhone());
        $this->assertEquals('1', $cachedPatient->getId());

    }

    private function getAssetContents(string $filename): string
    {
        return file_get_contents(__DIR__ . '/assets/ExternalConsumer/' . $filename);
    }
}
