<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    protected function prepareJsonResponse($request, Throwable $e): JsonResponse
    {
        if ($e instanceof BadRequest) {
            return new JsonResponse(
                [
                    'error' => [
                        'message' => $e->getMessage(),
                        'code' => str_pad((string)$e->getCode(), 2, '0', STR_PAD_LEFT)
                    ]
                ],
                400
            );
        }

        return parent::prepareJsonResponse($request, $e);
    }
}
