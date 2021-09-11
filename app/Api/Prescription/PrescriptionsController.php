<?php

namespace App\Api\Prescription;

use Illuminate\Http\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;

class PrescriptionsController
{

    public function __construct(private NewPrescriptionRequestParser $requestParser)
    {
    }

    public function __invoke(ServerRequestInterface $request): JsonResponse
    {
        return $this->create($request);
    }

    public function create(ServerRequestInterface $request): JsonResponse
    {
        $this->requestParser->parse($request);
        return new JsonResponse([], 201);
    }
}
