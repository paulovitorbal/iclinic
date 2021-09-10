<?php

/** @var Router $router */

use App\Api\Controllers\PresciptionsController;
use Laravel\Lumen\Routing\Router;

$router->post('/prescriptions', PresciptionsController::class);
