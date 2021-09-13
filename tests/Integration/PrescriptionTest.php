<?php

declare(strict_types=1);

namespace Tests\Integration;

use Tests\TestCase;

class PrescriptionTest extends TestCase
{
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
        $response = $this->json(
            'POST',
            '/prescriptions',
            [
                'clinic' => [
                    'id' => 1,
                ],
                'physician' => [
                    'id' => 2,
                ],
                'patient' => [
                    'id' => 3,
                ],
                'text' => 'Dipirona 1x ao dia',
            ],
            [
                'Content-Type' => 'application/json'
            ]
        );

        $response->seeJson([]);

        $response->assertResponseStatus(201);
    }
}
