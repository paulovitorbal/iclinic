<?php

declare(strict_types=1);

namespace App\DTO;

class Physician
{
    public function __construct(
        private string $name,
        private string $crm,
        private int $id
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCrm(): string
    {
        return $this->crm;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
