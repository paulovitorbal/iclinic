<?php

namespace App\Exceptions;

use InvalidArgumentException;
use Throwable;

class BadRequest extends InvalidArgumentException
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct('malformed request', 1, $previous);
    }
}
