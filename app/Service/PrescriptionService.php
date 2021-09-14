<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\Clinic;
use App\DTO\NewMetricResponse;
use App\DTO\NewMetricsRequest;
use App\DTO\NewPrescriptionRequest;
use App\Models\Prescription;
use App\Service\External\ExternalClinicService;
use App\Service\External\ExternalMetricService;
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
        private ExternalMetricService $metricService,
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
    public function savePrescription(Prescription $prescription): NewMetricResponse
    {
        /** @var NewMetricResponse $response */
        $response = $this->db->transaction(
            function () use ($prescription): NewMetricResponse {
                $prescription->save();
                return $this->metricService->post(
                    $this->createMetricsRequest($prescription)
                );
            }
        );
        return $response;
    }

    public function mapRequestToModel(NewPrescriptionRequest $newPrescritionRequest): Prescription
    {
        $prescription = new Prescription();
        $prescription->patientId = $newPrescritionRequest->getPatientId();
        $prescription->clinicId = $newPrescritionRequest->getClinicId();
        $prescription->physicianId = $newPrescritionRequest->getPhysicianId();
        $prescription->text = $newPrescritionRequest->getText();
        return $prescription;
    }
}
