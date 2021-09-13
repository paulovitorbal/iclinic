<?php

declare(strict_types=1);

namespace App\Service;

use App\Api\Prescription\NewPrescriptionRequest;
use App\Models\Prescription;
use Illuminate\Database\DatabaseManager;

class PrescriptionService
{

    public function __construct(private DatabaseManager $db)
    {
    }

    /**
     * @throws \Throwable
     */
    public function createAndEnrichPrescription(NewPrescriptionRequest $request): void
    {
        $prescription = $this->mapRequestToModel($request);
        $this->db->transaction(
            static function () use ($prescription) {
                $prescription->save();
            }
        );
    }

    private function mapRequestToModel(NewPrescriptionRequest $request): Prescription
    {
        $prescription = new Prescription();
        $prescription->clinicId = $request->getClinicId();
        $prescription->physicianId = $request->getPhysicianId();
        $prescription->patientId = $request->getPatientId();
        $prescription->text = $request->getText();
        return $prescription;
    }
}
