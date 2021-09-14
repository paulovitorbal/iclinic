<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\NewMetricsRequest;
use App\DTO\StdClassFactory;
use App\Service\External\ExternalMetricService;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Tests\TestCase;

class ExternalMetricServiceTest extends TestCase
{
    public function testGetSuccessfulRequest(): void
    {
        $mock = new MockHandler(
            [
                new Response(201, [], $this->getAssetContents('success-post-metrics.json'))
            ]
        );
        $testHandler = new TestHandler();
        $logger = new Logger('test', [$testHandler]);

        $externalService = new ExternalMetricService(
            $logger,
            new StdClassFactory(),
            $mock
        );

        $object = $externalService->post(
            new NewMetricsRequest(
                1,
                'Kenneth Torp DDS',
                1,
                'Wesley Marquardt',
                '0bc31b08-04f2-4eb8-b1b4-fd52f21622f4',
                1,
                'Boyd Crooks',
                'Danial.Kassulke59@hotmail.com',
                '413-218-5913 x9333',
                1
            )
        );
        $this->assertEquals(30, $object->getId());
    }

    private function getAssetContents(string $filename): string
    {
        return file_get_contents(__DIR__ . '/assets/ExternalConsumer/' . $filename);
    }
}
