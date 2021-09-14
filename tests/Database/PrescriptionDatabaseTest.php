<?php

declare(strict_types=1);

namespace Tests\Database;

use App\Models\Prescription;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class PrescriptionDatabaseTest extends TestCase
{
    use DatabaseMigrations;

    public function testSaveNewPrescription(): void
    {
        $prescription = new Prescription();
        $prescription->clinicId = 1;
        $prescription->physicianId = 2;
        $prescription->patientId = 3;
        $prescription->text = 'test';

        $prescription->save();

        $prescriptions = Prescription::all();
        $this->assertCount(1, $prescriptions);
        $this->assertEquals(1, $prescriptions[0]->clinicId);
        $this->assertEquals(2, $prescriptions[0]->physicianId);
        $this->assertEquals(3, $prescriptions[0]->patientId);
        $this->assertEquals('test', $prescriptions[0]->text);
    }
}
