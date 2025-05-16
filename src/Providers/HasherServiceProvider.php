<?php

namespace Pickles\Providers;

use Constants;
use Pickles\Crypto\BCrypt;
use Pickles\Crypto\Hasher;

class HasherServiceProvider implements ServiceProvider
{
    public function registerServices()
    {
        match (config(Constants::HASH_HASHER, Constants::DEFAULT_HASHER)) {
            Constants::DEFAULT_HASHER => singleton(Hasher::class, BCrypt::class)
        };
    }
}
