<?php

declare(strict_types=1);

namespace App\DTO;

interface ExternalDTOFactory
{
    /** @psalm-suppress MissingParamType
     * # As we could have different kinds of inputs here
     * # i.e.: a Factory that expects XML files, another one json strings, etc.
     */
    public function createPhysician($input): Physician;
}
