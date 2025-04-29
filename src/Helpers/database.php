<?php

use Pickles\Database\Drivers\DatabaseDriver;
use Pickles\Kernel;

function db(): DatabaseDriver
{
    $appInstance = app();
    if (!$appInstance instanceof Kernel) {
        throw new \Exception("The application instance is not a Kernel instance.");
    }

    return $appInstance->database;
}
