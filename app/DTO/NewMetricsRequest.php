<?php

declare(strict_types=1);

namespace App\DTO;

class NewMetricsRequest implements \JsonSerializable, \Stringable
{
    public function __construct(
        private int $clinicId,
        private ?string $clinicName,
        private int $physicianId,
        private string $physicianName,
        private string $physicianCrm,
        private int $patientId,
        private string $patientName,
        private string $patientEmail,
        private string $patientPhone,
        private int $prescriptionId
    ) {
    }
    public function jsonSerialize(): array
    {
        return [
            "clinic_id" => $this->clinicId,
            "clinic_name" => $this->clinicName,
            "physician_id" => $this->physicianId,
            "physician_name" => $this->physicianName,
            "physician_crm" => $this->physicianCrm,
            "patient_id" => $this->patientId,
            "patient_name" => $this->patientName,
            "patient_email" => $this->patientEmail,
            "patient_phone" => $this->patientPhone,
            "prescription_id" => $this->prescriptionId
        ];
    }

    public function __toString(): string
    {
        return json_encode($this->jsonSerialize(), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
    }
}
