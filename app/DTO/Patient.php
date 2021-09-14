<?php

declare(strict_types=1);

namespace App\DTO;

class Patient
{
    public function __construct(
        private string $name,
        private int $id,
        private string $email,
        private string $phone
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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }
}
