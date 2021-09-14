<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\Clinic;
use App\DTO\NewMetricsRequest;
use App\Models\Prescription;
use App\Service\External\ExternalClinicService;
use App\Service\External\ExternalPatientService;
use App\Service\External\ExternalPhysicianService;
use Illuminate\Database\DatabaseManager;
use Psr\Log\LoggerInterface;

class PrescriptionService
{

    public function __construct(
        private DatabaseManager $db,
        private ExternalClinicService $clinicService,
        private ExternalPhysicianService $physicianService,
        private ExternalPatientService $patientService,
        private LoggerInterface $logger
    ) {
    }

    /**
     * @throws \Throwable
     */
    public function createMetricsRequest(Prescription $prescription): NewMetricsRequest
    {
        try {
            $clinic = $this->clinicService->getClinic($prescription->clinicId);
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
            $clinic = new Clinic($prescription->clinicId);
        }

        $physician = $this->physicianService->getPhysician($prescription->physicianId);
        $patient = $this->patientService->getPatient($prescription->patientId);

        return new NewMetricsRequest(
            $clinic->getId(),
            $clinic->getName(),
            $physician->getId(),
            $physician->getName(),
            $physician->getCrm(),
            $patient->getId(),
            $patient->getName(),
            $patient->getEmail(),
            $patient->getPhone(),
            $prescription->id
        );
    }

    /**
     * @throws \Throwable
     */
    public function savePrescription(Prescription $prescription): Prescription
    {
        $this->db->transaction(
            static function () use ($prescription) {
                $prescription->save();
            }
        );

        return $prescription;
    }
}
