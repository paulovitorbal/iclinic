<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\StdClassFactory;
use Tests\TestCase;

class PatientTest extends TestCase
{
    public function testGetSuccessfulRequest(): void
    {
        /** @var mixed $json */
        $json = json_decode(
            $this->getAssetContents('success-get-patients-by-id.json'),
            false,
            512,
            JSON_THROW_ON_ERROR
        );
        $factory = new StdClassFactory();
        $physician = $factory->createPatient($json);
        $this->assertEquals('Boyd Crooks', $physician->getName());
        $this->assertEquals('Danial.Kassulke59@hotmail.com', $physician->getEmail());
        $this->assertEquals('413-218-5913 x9333', $physician->getPhone());
        $this->assertEquals('1', $physician->getId());
    }

    private function getAssetContents(string $filename): string
    {
        return file_get_contents(__DIR__ . '/assets/ExternalConsumer/' . $filename);
    }
}
