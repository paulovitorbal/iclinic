<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\StdClassFactory;
use App\Models\Prescription;
use App\Service\External\ExternalClinicService;
use App\Service\External\ExternalPatientService;
use App\Service\External\ExternalPhysicianService;
use App\Service\PrescriptionService;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Database\DatabaseManager;
use Monolog\Handler\TestHandler;
use Monolog\Logger;
use Tests\TestCase;

class PrescriptionServiceTest extends TestCase
{
    public function testCreateMetricRequest(): void
    {
        $externalClinicService = $this->getExternalClinicService();
        $externalPhysicianService = $this->getExternalPhysicianService();
        $externalPatientService = $this->getExternalPatientService();
        $prescriptionService = new PrescriptionService(
            $this->app->make(DatabaseManager::class),
            $externalClinicService,
            $externalPhysicianService,
            $externalPatientService,
            $this->getLogger()
        );

        $prescription = new Prescription();
        $prescription->id = 1;
        $prescription->patientId = 1;
        $prescription->clinicId = 1;
        $prescription->physicianId = 1;
        $prescription->text = 'text';

        $metricRequest = $prescriptionService->createMetricsRequest($prescription);
        $this->assertJsonStringEqualsJsonFile(
            __DIR__ . '/assets/new-metric-request.json',
            $metricRequest->__toString()
        );
    }

    private function getExternalClinicService(): ExternalClinicService
    {
        $mock = new MockHandler(
            [
                new Response(200, [], $this->getAssetContents('success-get-clinics-by-id.json'))
            ]
        );
        $logger = $this->getLogger();

        /** @var Repository $cache */
        $cache = $this->app->make(Repository::class);

        return new ExternalClinicService(
            $cache,
            $logger,
            new StdClassFactory(),
            $mock
        );
    }

    private function getAssetContents(string $filename): string
    {
        return file_get_contents(__DIR__ . '/assets/ExternalConsumer/' . $filename);
    }

    private function getLogger(): Logger
    {
        $testHandler = new TestHandler();
        return new Logger('test', [$testHandler]);
    }

    private function getExternalPhysicianService(): ExternalPhysicianService
    {
        $mock = new MockHandler(
            [
                new Response(200, [], $this->getAssetContents('success-get-physicians-by-id.json'))
            ]
        );
        $logger = $this->getLogger();

        /** @var Repository $cache */
        $cache = $this->app->make(Repository::class);

        return new ExternalPhysicianService(
            $cache,
            $logger,
            new StdClassFactory(),
            $mock
        );
    }

    private function getExternalPatientService(): ExternalPatientService
    {
        $mock = new MockHandler(
            [
                new Response(200, [], $this->getAssetContents('success-get-patients-by-id.json'))
            ]
        );
        $logger = $this->getLogger();

        /** @var Repository $cache */
        $cache = $this->app->make(Repository::class);

        return new ExternalPatientService(
            $cache,
            $logger,
            new StdClassFactory(),
            $mock
        );
    }

    public function testCreateMetricRequestWithClinicNotFound(): void
    {
        $externalClinicService = $this->getExternalClinicServiceWith404Return();
        $externalPhysicianService = $this->getExternalPhysicianService();
        $externalPatientService = $this->getExternalPatientService();
        $prescriptionService = new PrescriptionService(
            $this->app->make(DatabaseManager::class),
            $externalClinicService,
            $externalPhysicianService,
            $externalPatientService,
            $this->getLogger()
        );

        $prescription = new Prescription();
        $prescription->id = 1;
        $prescription->patientId = 1;
        $prescription->clinicId = 1;
        $prescription->physicianId = 1;
        $prescription->text = 'text';

        $metricRequest = $prescriptionService->createMetricsRequest($prescription);
        $this->assertJsonStringEqualsJsonFile(
            __DIR__ . '/assets/new-metric-request-without-clinic-name.json',
            $metricRequest->__toString()
        );
    }

    private function getExternalClinicServiceWith404Return(): ExternalClinicService
    {
        $mock = new MockHandler(
            [
                RequestException::create(new Request('GET', 'test'), new Response(404)),
            ]
        );
        $logger = $this->getLogger();

        /** @var Repository $cache */
        $cache = $this->app->make(Repository::class);

        return new ExternalClinicService(
            $cache,
            $logger,
            new StdClassFactory(),
            $mock
        );
    }
}
