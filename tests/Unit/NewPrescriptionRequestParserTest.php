<?php

namespace Tests\Unit;

use App\Api\Prescription\NewPrescriptionRequestParser;
use App\Exceptions\BadRequest;
use Laminas\Diactoros\ServerRequestFactory;
use Monolog\Handler\TestHandler;
use Tests\TestCase;

class NewPrescriptionRequestParserTest extends TestCase
{
    private TestHandler $testHandler;
    private NewPrescriptionRequestParser $parser;

    public function setUp(): void
    {
        parent::setUp();
        $this->parser = new NewPrescriptionRequestParser();
    }

    /**
     * @dataProvider provideBadRequestParameters
     * @throws \JsonException
     */
    public function testParseBadRequests(array $params): void
    {
        $request = ServerRequestFactory::fromGlobals()->withParsedBody($params);

        $this->expectException(BadRequest::class);
        $this->parser->parse($request);
    }

    /**
     * @throws \JsonException
     */
    public function testParseGoodRequest(): void
    {
        $request = ServerRequestFactory::fromGlobals()->withParsedBody(
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
            ]
        );

        $request = $this->parser->parse($request);
        $this->assertEquals(1, $request->getClinicId());
        $this->assertEquals(2, $request->getPhysicianId());
        $this->assertEquals(3, $request->getPatientId());
        $this->assertEquals('Dipirona 1x ao dia', $request->getText());
    }

    public function provideBadRequestParameters(): \Generator
    {
        yield 'Missing clinic object' => [
            [
                'physician' => [
                    'id' => 1,
                ],
                'patient' => [
                    'id' => 1,
                ],
                'text' => 'Dipirona 1x ao dia',
            ]
        ];
        yield 'Missing clinic id' => [[
            'clinic' => [
            ],
            'physician' => [
                'id' => 1,
            ],
            'patient' => [
                'id' => 1,
            ],
            'text' => 'Dipirona 1x ao dia',
        ]];
        yield 'Clinic id as string' => [[
            'clinic' => [
                'id' => 'test'
            ],
            'physician' => [
                'id' => 1,
            ],
            'patient' => [
                'id' => 1,
            ],
            'text' => 'Dipirona 1x ao dia',
        ]];
        yield 'Clinic negative id' => [[
            'clinic' => [
                'id' => -1
            ],
            'physician' => [
                'id' => 1,
            ],
            'patient' => [
                'id' => 1,
            ],
            'text' => 'Dipirona 1x ao dia',
        ]];
        yield 'Missing physician object' => [
            [
                'clinic' => [
                    'id' => 1
                ],
                'patient' => [
                    'id' => 1,
                ],
                'text' => 'Dipirona 1x ao dia',
            ]
        ];
        yield 'Missing physician id' => [[
            'clinic' => [
                'id' => 1
            ],
            'physician' => [
            ],
            'patient' => [
                'id' => 1,
            ],
            'text' => 'Dipirona 1x ao dia',
        ]];
        yield 'Physician id as string' => [[
            'clinic' => [
                'id' => 1
            ],
            'physician' => [
                'id' => 'test',
            ],
            'patient' => [
                'id' => 1,
            ],
            'text' => 'Dipirona 1x ao dia',
        ]];
        yield 'Physician negative id' => [[
            'clinic' => [
                'id' => 1
            ],
            'physician' => [
                'id' => -1,
            ],
            'patient' => [
                'id' => 1,
            ],
            'text' => 'Dipirona 1x ao dia',
        ]];
        yield 'Missing patient object' => [
            [
                'clinic' => [
                    'id' => 1
                ],
                'physician' => [
                    'id' => 1,
                ],
                'text' => 'Dipirona 1x ao dia',
            ]
        ];
        yield 'Missing patient id' => [[
            'clinic' => [
                'id' => 1
            ],
            'physician' => [
                'id' => 1,
            ],
            'patient' => [
            ],
            'text' => 'Dipirona 1x ao dia',
        ]];
        yield 'Patient id as string' => [[
            'clinic' => [
                'id' => 1
            ],
            'physician' => [
                'id' => 1,
            ],
            'patient' => [
                'id' => 'test',
            ],
            'text' => 'Dipirona 1x ao dia',
        ]];
        yield 'Patient negative id' => [[
            'clinic' => [
                'id' => 1
            ],
            'physician' => [
                'id' => 1,
            ],
            'patient' => [
                'id' => -1,
            ],
            'text' => 'Dipirona 1x ao dia',
        ]];
        yield 'Text should be string' => [[
            'clinic' => [
                'id' => 1
            ],
            'physician' => [
                'id' => 1,
            ],
            'patient' => [
                'id' => 1,
            ],
            'text' => 1,
        ]];
        yield 'Text should not be empty' => [[
            'clinic' => [
                'id' => 1
            ],
            'physician' => [
                'id' => 1,
            ],
            'patient' => [
                'id' => 1,
            ],
            'text' => 1,
        ]];
    }
}
