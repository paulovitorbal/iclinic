<?php

declare(strict_types=1);

namespace App\DTO;

class Clinic
{
    public function __construct(
        private int $id,
        private ?string $name = null
    ) {
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
