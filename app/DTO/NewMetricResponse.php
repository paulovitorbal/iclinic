<?php

declare(strict_types=1);

namespace App\DTO;

class NewMetricResponse
{
    public function __construct(
        private int $id
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }
}
