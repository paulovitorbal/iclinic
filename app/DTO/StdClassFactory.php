<?php

declare(strict_types=1);

namespace App\DTO;

use Webmozart\Assert\Assert;

class StdClassFactory implements ExternalDTOFactory
{
    /** @psalm-suppress MissingParamType
     *  # First thing we do here is to assert that we have a \stdClass as the input
     */
    public function createPhysician($input): Physician
    {
        Assert::isInstanceOf($input, \stdClass::class);
        Assert::stringNotEmpty($input->name);
        Assert::stringNotEmpty($input->crm);
        Assert::positiveInteger((int)$input->id);

        return new Physician($input->name, $input->crm, (int)$input->id);
    }
}
