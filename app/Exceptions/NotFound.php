<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Throwable;

class NotFound extends Exception
{
    public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
    public static function patientNotFound(Throwable $e): self
    {
        return new self(
            'Patient not found',
            3,
            $e
        );
    }
    public static function physicianNotFound(Throwable $e): self
    {
        return new self(
            'Physician not found',
            2,
            $e
        );
    }
}
