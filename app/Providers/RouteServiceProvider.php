<?php

namespace App\Providers;

use Pickles\Kernel;
use Pickles\Providers\ServiceProvider;
use Pickles\Routing\Route;

class RouteServiceProvider implements ServiceProvider
{
    public function registerServices() {
        Route::load(Kernel::$root . "/routes");   
    }
}
