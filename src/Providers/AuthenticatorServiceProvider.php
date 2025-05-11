<?php

namespace Pickles\Providers;

use Constants;
use Pickles\Auth\Authenticators\Authenticator;
use Pickles\Auth\Authenticators\SessionAuthenticator;

/**
 * Class AuthenticatorServiceProvider
 *
 * This class implements the ServiceProvider interface and is responsible
 * for registering and bootstrapping authentication-related services within
 * the application.
 *
 * @package PicklesFramework\Providers
 */
class AuthenticatorServiceProvider implements ServiceProvider
{
    /**
     * @inheritDoc
     */
    public function registerServices()
    {
        match (config(Constants::AUTH_METHOD, Constants::DEFAULT_AUTH_METHOD)) {
            Constants::DEFAULT_AUTH_METHOD => singleton(Authenticator::class, SessionAuthenticator::class)
        };
    }
}
