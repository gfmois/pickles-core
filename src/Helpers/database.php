<?php

use Pickles\Database\Drivers\DatabaseDriver;
use Pickles\Kernel;

/**
 * Retrieves the database driver instance from the application kernel.
 *
 * @return DatabaseDriver The database driver instance.
 * @throws \Exception If the application instance is not a Kernel instance.
 */
function db(): DatabaseDriver
{
    $appInstance = app();
    if (!$appInstance instanceof Kernel) {
        throw new \Exception("The application instance is not a Kernel instance.");
    }

    return $appInstance->database;
}
