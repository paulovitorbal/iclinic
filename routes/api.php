<?php

declare(strict_types=1);

/** @var Router $router */

use App\Api\Prescription\PrescriptionsController;
use Laravel\Lumen\Routing\Router;

$router->post('/prescriptions', PrescriptionsController::class);
