<?php

declare(strict_types=1);

namespace App\Service\External;

use GuzzleHttp\Exception\TransferException;

class TooMuchAttemptsException extends \RuntimeException
{
    public static function withPathAttempts(string $path, int $try, ?TransferException $lastException): self
    {
        return new self(
            sprintf(
                'Too much attempts [%d] for [%s]. The last exception error was: [%s]',
                $try,
                $path,
                ($lastException) ? $lastException->getMessage() : ''
            ),
            ($lastException) ? (int)$lastException->getCode() : -1,
            $lastException
        );
    }
}
