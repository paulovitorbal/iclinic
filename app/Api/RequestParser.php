<?php

namespace App\Api;

use Psr\Http\Message\ServerRequestInterface;

# This is an adapter interface, that will receive a generic request and will
# fit it into a more specific request
interface RequestParser
{
    public function parse(ServerRequestInterface $request): Request;
}
