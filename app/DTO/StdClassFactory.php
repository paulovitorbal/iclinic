<?php

declare(strict_types=1);

namespace App\DTO;

use Webmozart\Assert\Assert;

/** @psalm-suppress MissingParamType
 *  # First thing we do here is to assert that we have a \stdClass as the input
 */
class StdClassFactory implements ExternalDTOFactory
{
    public function createPhysician($input): Physician
    {
        Assert::isInstanceOf($input, \stdClass::class);
        Assert::stringNotEmpty($input->name);
        Assert::stringNotEmpty($input->crm);
        Assert::positiveInteger((int)$input->id);

        return new Physician($input->name, $input->crm, (int)$input->id);
    }

    public function createClinic($input): Clinic
    {
        Assert::isInstanceOf($input, \stdClass::class);
        Assert::stringNotEmpty($input->name);
        Assert::positiveInteger((int)$input->id);

        return new Clinic($input->name, (int)$input->id);
    }

    public function createPatient($input): Patient
    {
        Assert::isInstanceOf($input, \stdClass::class);
        Assert::stringNotEmpty($input->name);
        Assert::positiveInteger((int)$input->id);
        Assert::stringNotEmpty($input->email);
        Assert::email($input->email);
        Assert::stringNotEmpty($input->phone);

        return new Patient($input->name, (int)$input->id, $input->email, $input->phone);
    }
}
