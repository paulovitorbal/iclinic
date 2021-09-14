<?php

declare(strict_types=1);

namespace App\DTO;

class Clinic
{
    public function __construct(
        private string $name,
        private int $id
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getId(): int
    {
        return $this->id;
    }
}
