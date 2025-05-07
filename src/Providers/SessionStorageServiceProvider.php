<?php

namespace Pickles\Providers;

use Constants;
use Pickles\Session\PhpNativeSessionStorage;
use Pickles\Session\SessionStorage;

class SessionStorageServiceProvider implements ServiceProvider
{
    public function registerServices()
    {
        match (config(Constants::SESSION_STORAGE, Constants::DEFAULT_SESSION_STORAGE)) {
            Constants::DEFAULT_SESSION_STORAGE => singleton(SessionStorage::class, PhpNativeSessionStorage::class)
        };
    }
}
