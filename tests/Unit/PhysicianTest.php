<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\StdClassFactory;
use Tests\TestCase;

class PhysicianTest extends TestCase
{
    public function testGetSuccessfulRequest(): void
    {
        /** @var mixed $json */
        $json = json_decode(
            $this->getAssetContents('success-get-physicians-by-id.json'),
            false,
            512,
            JSON_THROW_ON_ERROR
        );
        $factory = new StdClassFactory();
        $physician = $factory->createPhysician($json);
        $this->assertEquals('Wesley Marquardt', $physician->getName());
        $this->assertEquals('0bc31b08-04f2-4eb8-b1b4-fd52f21622f4', $physician->getCrm());
        $this->assertEquals('1', $physician->getId());
    }

    private function getAssetContents(string $filename): string
    {
        return file_get_contents(__DIR__ . '/assets/ExternalConsumer/' . $filename);
    }
}
