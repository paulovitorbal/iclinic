<?php

namespace App\Console;

use Laravel\Lumen\Application;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->artisan = $this->getArtisan();
    }
}
