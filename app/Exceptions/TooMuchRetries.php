<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Throwable;

class TooMuchRetries extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function patientNotAvailable(Throwable $e): self
    {
        return new self(
            'Patient service not available',
            6,
            $e
        );
    }

    public static function physicianNotAvailable(Throwable $e): self
    {
        return new self(
            'Physician service not available',
            5,
            $e
        );
    }
    public static function metricsNotAvailable(Throwable $e): self
    {
        return new self(
            'Metrics service not available',
            4,
            $e
        );
    }
}
