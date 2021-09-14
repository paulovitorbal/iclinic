<?php

declare(strict_types=1);

namespace App\DTO;

class NewMetricsRequest implements \JsonSerializable
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
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
