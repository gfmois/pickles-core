<?php

namespace Pickles\Providers;

use Pickles\Server\PhpNativeServer;
use Pickles\Server\Server;

class ServerServiceProvider implements ServiceProvider
{
    public function registerServices()
    {
        singleton(Server::class, PhpNativeServer::class);
    }
}
