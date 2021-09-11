<?php

namespace App\Middlewares;

use App\Exceptions\BadRequest;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

class BadRequestInterceptor implements MiddlewareInterface
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            $response = $handler->handle($request);
        } catch (BadRequest $e) {
            if ($e->getPrevious() !== null) {
                $this->logger->error(
                    $e->getPrevious()->getMessage(),
                    [
                        'code' => $e->getPrevious()->getCode(),
                        'file' => $e->getPrevious()->getFile(),
                        'line' => $e->getPrevious()->getLine(),
                        'trace' => $e->getPrevious()->getTraceAsString(),
                    ]
                );
            }
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
        return $response;
    }
}
