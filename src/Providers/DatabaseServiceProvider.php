<?php

namespace Pickles\Providers;

use Constants;
use Pickles\Database\Drivers\DatabaseDriver;
use Pickles\Database\Drivers\PdoDriver;

class DatabaseServiceProvider implements ServiceProvider
{
    public function registerServices()
    {
        match (config(Constants::DATABASE_PROTOCOL, Constants::DEFAULT_DATABASE_PROTOCOL)) {
            Constants::DEFAULT_DATABASE_PROTOCOL, Constants::POSTGRES_DATABASE_PROTOCOL => singleton(DatabaseDriver::class, PdoDriver::class),
        };
    }
}
