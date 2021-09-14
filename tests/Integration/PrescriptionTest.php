<?php

declare(strict_types=1);

namespace Tests\Integration;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class PrescriptionTest extends TestCase
{
    use DatabaseMigrations;

    public function testBadRequestReturns400(): void
    {

        $response = $this->json(
            'POST',
            '/prescriptions',
            [
            ],
            [
                'Content-Type' => 'application/json'
            ]
        );

        $response->seeJson(
            [
                'error' => [
                    'message' => 'malformed request',
                    'code' => '01'
                ]
            ]
        );

        $response->assertResponseStatus(400);
    }

    public function testWellFormedRequestReturns201(): void
    {
        $mock = new MockHandler([
            new Response(200, [], $this->getAssetContents('success-get-clinics-by-id.json')),
            new Response(200, [], $this->getAssetContents('success-get-physicians-by-id.json')),
            new Response(200, [], $this->getAssetContents('success-get-patients-by-id.json')),
            new Response(200, [], $this->getAssetContents('success-post-metrics.json')),
        ]);

        $this->app->instance(MockHandler::class, $mock);

        $response = $this->json(
            'POST',
            '/prescriptions',
            [
                'clinic' => [
                    'id' => 1,
                ],
                'physician' => [
                    'id' => 1,
                ],
                'patient' => [
                    'id' => 1,
                ],
                'text' => 'Dipirona 1x ao dia',
            ],
            [
                'Content-Type' => 'application/json'
            ]
        );

        $response->seeJson(
            [
                'data' => [
                    'id' => 1,
                    'clinic' => [
                        'id' => 1,
                    ],
                    'physician' => [
                        'id' => 1,
                    ],
                    'patient' => [
                        'id' => 1,
                    ],
                    'text' => 'Dipirona 1x ao dia',
                    'metric' => [
                        'id' => 30,
                    ],
                ],
            ]
        );

        $response->assertResponseStatus(201);
    }
    public function testMetricsTooMuchRetries(): void
    {
        $mock = new MockHandler([
            new Response(200, [], $this->getAssetContents('success-get-clinics-by-id.json')),
            new Response(200, [], $this->getAssetContents('success-get-physicians-by-id.json')),
            new Response(200, [], $this->getAssetContents('success-get-patients-by-id.json')),
            RequestException::create(new Request('GET', 'test'), new Response(500)),
            RequestException::create(new Request('GET', 'test'), new Response(500)),
            RequestException::create(new Request('GET', 'test'), new Response(500)),
            RequestException::create(new Request('GET', 'test'), new Response(500)),
            RequestException::create(new Request('GET', 'test'), new Response(500)),
            RequestException::create(new Request('GET', 'test'), new Response(500)),
            RequestException::create(new Request('GET', 'test'), new Response(500)),
        ]);

        $this->app->instance(MockHandler::class, $mock);

        $response = $this->json(
            'POST',
            '/prescriptions',
            [
                'clinic' => [
                    'id' => 1,
                ],
                'physician' => [
                    'id' => 1,
                ],
                'patient' => [
                    'id' => 1,
                ],
                'text' => 'Dipirona 1x ao dia',
            ],
            [
                'Content-Type' => 'application/json'
            ]
        );

        $response->seeJson(
            [
                'error' => [
                'code' => '04',
                'message' => 'Metrics service not available'
                    ]
            ]
        );

        $response->assertResponseStatus(503);
    }
    public function testPhysicsNotFound(): void
    {
        $mock = new MockHandler([
            new Response(200, [], $this->getAssetContents('success-get-clinics-by-id.json')),
            RequestException::create(new Request('GET', 'test'), new Response(404)),
        ]);

        $this->app->instance(MockHandler::class, $mock);

        $response = $this->json(
            'POST',
            '/prescriptions',
            [
                'clinic' => [
                    'id' => 1,
                ],
                'physician' => [
                    'id' => 1,
                ],
                'patient' => [
                    'id' => 1,
                ],
                'text' => 'Dipirona 1x ao dia',
            ],
            [
                'Content-Type' => 'application/json'
            ]
        );

        $response->seeJson(
            [
                'error' => [
                    'code' => '02',
                    'message' => 'Physician not found'
                ]
            ]
        );

        $response->assertResponseStatus(404);
    }
    public function testUnexpectedApiError(): void
    {
        $mock = new MockHandler([]);

        $this->app->instance(MockHandler::class, $mock);

        $response = $this->json(
            'POST',
            '/prescriptions',
            [
                'clinic' => [
                    'id' => 1,
                ],
                'physician' => [
                    'id' => 1,
                ],
                'patient' => [
                    'id' => 1,
                ],
                'text' => 'Dipirona 1x ao dia',
            ],
            [
                'Content-Type' => 'application/json'
            ]
        );

        $response->assertResponseStatus(500);
    }

    private function getAssetContents(string $filename): string
    {
        return file_get_contents(__DIR__ . '/../Unit/assets/ExternalConsumer/' . $filename);
    }
}
