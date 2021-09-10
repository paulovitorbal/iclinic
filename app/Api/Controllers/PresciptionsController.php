<?php

namespace App\Api\Controllers;

use Illuminate\Http\Response;

class PresciptionsController
{
    public function __invoke(): Response
    {
        return $this->create();
    }

    public function create(): Response
    {
        return new Response();
    }
}
