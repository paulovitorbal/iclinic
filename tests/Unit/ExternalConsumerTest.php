<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Service\External\ExternalConsumer;
use App\Service\External\TooMuchAttemptsException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use Tests\TestCase;

class ExternalConsumerTest extends TestCase
{
    public function testGetSuccessfulRequest(): void
    {
        $mock = new MockHandler(
            [
                new Response(200, [], $this->getAssetContents('success-get-physicians-by-id.json'))
            ]
        );

        $consumer = new ExternalConsumer('http://www.example.com', 4, 2, null, $mock);
        $object = $consumer->get('/physicians/1');
        $this->assertEquals('Wesley Marquardt', $object->name);
        $this->assertEquals('0bc31b08-04f2-4eb8-b1b4-fd52f21622f4', $object->crm);
        $this->assertEquals('1', $object->id);
    }

    public function testRetry3TimesThenThrows(): void
    {
        $mock = new MockHandler(
            [
                new RequestException('Error 500 1', new Request('GET', 'test')),
                new RequestException('Error 500 2', new Request('GET', 'test')),
                new RequestException('Error 500 3', new Request('GET', 'test')),
                new RequestException('Error 500 4', new Request('GET', 'test')),
                new RequestException('Error 500 5', new Request('GET', 'test')),
            ]
        );
        $nullHandler = new NullHandler();
        $logger = new Logger('null', [$nullHandler]);
        $consumer = new ExternalConsumer('https://www.example.com', 4, 3, $logger, $mock);

        $this->expectException(TooMuchAttemptsException::class);
        $this->expectExceptionMessage(
            'Too much attempts [4] for [/physicians/1]. The last exception error was: [Error 500 4]'
        );
        $consumer->get('/physicians/1');
    }

    private function getAssetContents(string $filename): string
    {
        return file_get_contents(__DIR__ . '/assets/ExternalConsumer/' . $filename);
    }
}
