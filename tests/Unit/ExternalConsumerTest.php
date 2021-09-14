<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Service\External\ExternalConsumer;
use App\Service\External\TooMuchAttemptsException;
use GuzzleHttp\Exception\ClientException;
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

    public function testGetRequestRetry3TimesThenThrows(): void
    {
        $mock = new MockHandler(
            [
                RequestException::create(new Request('GET', 'test'), new Response(500)),
                RequestException::create(new Request('GET', 'test'), new Response(500)),
                RequestException::create(new Request('GET', 'test'), new Response(500)),
                RequestException::create(new Request('GET', 'test'), new Response(500)),
                RequestException::create(new Request('GET', 'test'), new Response(500)),
            ]
        );
        $nullHandler = new NullHandler();
        $logger = new Logger('null', [$nullHandler]);
        $consumer = new ExternalConsumer('https://www.example.com', 4, 3, $logger, $mock);

        $this->expectException(TooMuchAttemptsException::class);
        $this->expectExceptionMessage(
            'Too much attempts [4] for [/physicians/1]. The last exception error was: [Server error: `GET test` ' .
            'resulted in a `500 Internal Server Error` response]'
        );
        $consumer->get('/physicians/1');
    }

    public function testGetRequestNotFound(): void
    {
        $mock = new MockHandler(
            [
                RequestException::create(new Request('GET', 'test'), new Response(404)),
            ]
        );
        $nullHandler = new NullHandler();
        $logger = new Logger('null', [$nullHandler]);
        $consumer = new ExternalConsumer('https://www.example.com', 4, 3, $logger, $mock);

        $this->expectException(ClientException::class);
        $this->expectExceptionCode(404);
        $consumer->get('/physicians/1');
    }

    public function testPostSuccessfulRequestWithToken(): void
    {
        $mock = new MockHandler(
            [
                new Response(201, [], $this->getAssetContents('success-post-metrics.json'))
            ]
        );

        $jsonObject = $this->getAssetContents('new-metric-request.json');

        $consumer = new ExternalConsumer('http://www.example.com', 4, 2, null, $mock);
        $object = $consumer->post('/metric', $jsonObject, 'Bearer -----');
        $this->assertEquals(30, $object->id);
    }
    public function testPostRequestWithTooMuchErrors(): void
    {
        $mock = new MockHandler(
            [
                RequestException::create(new Request('GET', 'test'), new Response(500)),
                RequestException::create(new Request('GET', 'test'), new Response(500)),
                RequestException::create(new Request('GET', 'test'), new Response(500)),
                RequestException::create(new Request('GET', 'test'), new Response(500)),
            ]
        );

        $jsonObject = $this->getAssetContents('new-metric-request.json');
        $nullHandler = new NullHandler();
        $logger = new Logger('null', [$nullHandler]);
        $consumer = new ExternalConsumer('http://www.example.com', 4, 2, $logger, $mock);

        $this->expectException(TooMuchAttemptsException::class);
        $this->expectExceptionMessage(
            'Too much attempts [3] for [/metric]. The last exception error was: [Server error: `GET test` resulted ' .
            'in a `500 Internal Server Error` response]'
        );

        $consumer->post('/metric', $jsonObject, 'Bearer -----');
    }
    public function testPostSuccessfulRequestWithoutToken(): void
    {
        $mock = new MockHandler(
            [
                new Response(201, [], $this->getAssetContents('success-post-metrics.json'))
            ]
        );

        $jsonObject = $this->getAssetContents('new-metric-request.json');

        $consumer = new ExternalConsumer('http://www.example.com', 4, 2, null, $mock);
        $object = $consumer->post('/metric', $jsonObject);
        $this->assertEquals(30, $object->id);
    }

    private function getAssetContents(string $filename): string
    {
        return file_get_contents(__DIR__ . '/assets/ExternalConsumer/' . $filename);
    }
}
