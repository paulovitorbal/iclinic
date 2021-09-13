<?php

declare(strict_types=1);

namespace App\DTO;

use App\Request;

class NewPrescriptionRequest implements Request
{
    public function __construct(
        private int    $clinicId,
        private int    $physicianId,
        private int    $patientId,
        private string $text
    )
    {
    }

    public function getClinicId(): int
    {
        return $this->clinicId;
    }

    public function getPhysicianId(): int
    {
        return $this->physicianId;
    }

    public function getPatientId(): int
    {
        return $this->patientId;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
