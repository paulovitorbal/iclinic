<?php

declare(strict_types=1);

namespace App\Api\Prescription;

use App\DTO\NewPrescriptionRequest;
use App\Service\PrescriptionService;
use Illuminate\Http\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;

class PrescriptionsController
{

    public function __construct(
        private NewPrescriptionRequestParser $requestParser,
        private PrescriptionService $prescriptionService
    ) {
    }

    public function __invoke(ServerRequestInterface $request): JsonResponse
    {
        return $this->create($request);
    }

    public function create(ServerRequestInterface $request): JsonResponse
    {
        /** @var NewPrescriptionRequest $newPrescriptionRequest */
        $newPrescriptionRequest = $this->requestParser->parse($request);
        $model = $this->prescriptionService->mapRequestToModel($newPrescriptionRequest);
        $this->prescriptionService->savePrescription($model);
        return new JsonResponse([], 201);
    }
}
