<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\StdClassFactory;
use Tests\TestCase;

class ClinicTest extends TestCase
{
    public function testGetSuccessfulRequest(): void
    {
        /** @var mixed $json */
        $json = json_decode(
            $this->getAssetContents('success-get-clinics-by-id.json'),
            false,
            512,
            JSON_THROW_ON_ERROR
        );
        $factory = new StdClassFactory();
        $physician = $factory->createClinic($json);
        $this->assertEquals('Kenneth Torp DDS', $physician->getName());
        $this->assertEquals('1', $physician->getId());
    }

    private function getAssetContents(string $filename): string
    {
        return file_get_contents(__DIR__ . '/assets/ExternalConsumer/' . $filename);
    }
}
