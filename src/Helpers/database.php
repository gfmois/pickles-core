<?php

use Pickles\Database\Drivers\DatabaseDriver;

/**
 * Retrieves the database driver instance from the application kernel.
 *
 * @return DatabaseDriver The database driver instance.
 * @throws \Exception If the application instance is not a Kernel instance.
 */
function db(): DatabaseDriver
{
    $databaseDriverInstance = app(DatabaseDriver::class);
    if (!$databaseDriverInstance instanceof DatabaseDriver) {
        throw new \Exception("The application instance is not a Kernel instance.");
    }

    return $databaseDriverInstance;
}
