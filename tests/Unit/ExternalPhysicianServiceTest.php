<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\StdClassFactory;
use App\Exceptions\NotFound;
use App\Exceptions\TooMuchRetries;
use App\Service\External\ExternalPhysicianService;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Contracts\Cache\Repository;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Tests\TestCase;

class ExternalPhysicianServiceTest extends TestCase
{
    public function testGetSuccessfulRequest(): void
    {
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
    public function testGetRequestNotFound(): void
    {
        $mock = new MockHandler(
            [
                RequestException::create(new Request('GET', '/physicians/1'), new Response(404)),
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
        $this->expectException(NotFound::class);
        $this->expectExceptionMessage('Physician not found');
        $this->expectExceptionCode(2);
        $externalService->getPhysician(1);
    }
    public function testGetRequestBadRequest(): void
    {
        $mock = new MockHandler(
            [
                RequestException::create(new Request('GET', '/physicians/1'), new Response(400)),
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
        $this->expectException(ClientException::class);
        $externalService->getPhysician(1);
    }
    public function testTooMuchRetries(): void
    {
        $mock = new MockHandler(
            [
                RequestException::create(new Request('GET', '/physicians/1'), new Response(500)),
                RequestException::create(new Request('GET', '/physicians/1'), new Response(500)),
                RequestException::create(new Request('GET', '/physicians/1'), new Response(500)),
                RequestException::create(new Request('GET', '/physicians/1'), new Response(500)),
                RequestException::create(new Request('GET', '/physicians/1'), new Response(500)),
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
        $this->expectException(TooMuchRetries::class);
        $this->expectExceptionMessage('Physician service not available');
        $this->expectExceptionCode(5);
        $externalService->getPhysician(1);
    }

    private function getAssetContents(string $filename): string
    {
        return file_get_contents(__DIR__ . '/assets/ExternalConsumer/' . $filename);
    }
}
