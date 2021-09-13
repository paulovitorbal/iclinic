<?php

declare(strict_types=1);

namespace Tests\Database;

use App\DTO\NewPrescriptionRequest;
use App\Models\Prescription;
use App\Service\PrescriptionService;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class PrescriptionDatabaseTest extends TestCase
{
    use DatabaseMigrations;

    public function testServiceWillSaveNewPrescription(): void
    {
        /** @var PrescriptionService $service */
        $service = $this->app->get(PrescriptionService::class);
        $service->createAndEnrichPrescription(
            new NewPrescriptionRequest(
                1,
                2,
                3,
                'test'
            )
        );
        $prescriptions = Prescription::all();
        $this->assertCount(1, $prescriptions);
        $this->assertEquals(1, $prescriptions[0]->clinicId);
        $this->assertEquals(2, $prescriptions[0]->physicianId);
        $this->assertEquals(3, $prescriptions[0]->patientId);
        $this->assertEquals('test', $prescriptions[0]->text);
    }
}
