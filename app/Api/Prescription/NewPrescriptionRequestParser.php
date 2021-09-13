<?php

declare(strict_types=1);

namespace App\Api\Prescription;

use App\Api\Request;
use App\Api\RequestParser;
use App\Exceptions\BadRequest;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Webmozart\Assert\Assert;

class NewPrescriptionRequestParser implements RequestParser
{

    /**
     * # I deliberately ignored the following psalm rules, due to the fact that
     * # on the body of the request anything may come in. And as I am asserting
     * # all the attributes that have meaning. I don't care about the rules
     * # on this context
     * @psalm-suppress MixedArrayAccess,MixedArgument
     */
    public function parse(ServerRequestInterface $request): Request
    {
        try {
            $params = $request->getParsedBody();
            Assert::isArray($params);

            Assert::keyExists($params, 'clinic');
            Assert::keyExists($params['clinic'], 'id');
            $clinicId = $params['clinic']['id'];
            Assert::positiveInteger($clinicId);

            Assert::keyExists($params, 'physician');
            Assert::keyExists($params['physician'], 'id');
            $physicianId = $params['physician']['id'];
            Assert::positiveInteger($physicianId);

            Assert::keyExists($params, 'patient');
            Assert::keyExists($params['patient'], 'id');
            $patientId = $params['patient']['id'];
            Assert::positiveInteger($patientId);

            Assert::keyExists($params, 'text');
            Assert::stringNotEmpty($params['text']);
            $text = $params['text'];
        } catch (InvalidArgumentException $e) {
            throw new BadRequest($e);
        }

        return new NewPrescriptionRequest($clinicId, $physicianId, $patientId, $text);
    }
}
