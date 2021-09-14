<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\StdClassFactory;
use App\Exceptions\NotFound;
use App\Exceptions\TooMuchRetries;
use App\Service\External\ExternalPatientService;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Contracts\Cache\Repository;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Tests\TestCase;

class ExternalPatientServiceTest extends TestCase
{
    public function testGetSuccessfulRequest(): void
    {
        $mock = new MockHandler(
            [
                new Response(200, [], $this->getAssetContents('success-get-patients-by-id.json'))
            ]
        );
        $testHandler = new TestHandler();
        $logger = new Logger('test', [$testHandler]);

        /** @var Repository $cache */
        $cache = $this->app->make(Repository::class);

        $externalService = new ExternalPatientService(
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

        $cachedPatient = $externalService->getPatient(1);

        $this->assertEquals('Boyd Crooks', $cachedPatient->getName());
        $this->assertEquals('Danial.Kassulke59@hotmail.com', $cachedPatient->getEmail());
        $this->assertEquals('413-218-5913 x9333', $cachedPatient->getPhone());
        $this->assertEquals('1', $cachedPatient->getId());
    }
    public function testGetRequestNotFound(): void
    {
        $mock = new MockHandler(
            [
                RequestException::create(new Request('GET', '/patientsß/1'), new Response(404)),
            ]
        );
        $testHandler = new TestHandler();
        $logger = new Logger('test', [$testHandler]);

        /** @var Repository $cache */
        $cache = $this->app->make(Repository::class);

        $externalService = new ExternalPatientService(
            $cache,
            $logger,
            new StdClassFactory(),
            $mock
        );
        $this->expectException(NotFound::class);
        $this->expectExceptionMessage('Patient not found');
        $this->expectExceptionCode(3);
        $externalService->getPatient(1);
    }
    public function testGetRequestBadRequest(): void
    {
        $mock = new MockHandler(
            [
                RequestException::create(new Request('GET', '/patientsß/1'), new Response(400)),
            ]
        );
        $testHandler = new TestHandler();
        $logger = new Logger('test', [$testHandler]);

        /** @var Repository $cache */
        $cache = $this->app->make(Repository::class);

        $externalService = new ExternalPatientService(
            $cache,
            $logger,
            new StdClassFactory(),
            $mock
        );
        $this->expectException(ClientException::class);
        $externalService->getPatient(1);
    }
    public function testGetRequestTooMuchRetries(): void
    {
        $mock = new MockHandler(
            [
                RequestException::create(new Request('GET', '/patient/1'), new Response(500)),
                RequestException::create(new Request('GET', '/patient/1'), new Response(500)),
                RequestException::create(new Request('GET', '/patient/1'), new Response(500)),
                RequestException::create(new Request('GET', '/patient/1'), new Response(500)),
                RequestException::create(new Request('GET', '/patient/1'), new Response(500)),
            ]
        );
        $testHandler = new TestHandler();
        $logger = new Logger('test', [$testHandler]);

        /** @var Repository $cache */
        $cache = $this->app->make(Repository::class);

        $externalService = new ExternalPatientService(
            $cache,
            $logger,
            new StdClassFactory(),
            $mock
        );
        $this->expectException(TooMuchRetries::class);
        $this->expectExceptionMessage('Patient service not available');
        $this->expectExceptionCode(6);
        $externalService->getPatient(1);
    }
    private function getAssetContents(string $filename): string
    {
        return file_get_contents(__DIR__ . '/assets/ExternalConsumer/' . $filename);
    }
}
