<?php

namespace Pickles\Auth;

use Pickles\Auth\Authenticators\Authenticator;
use Pickles\Container\Exceptions\InvalidInstanceSavedException;

class Auth
{
    public static function user(): ?Authenticatable
    {
        $authenticatorInstance = app(Authenticator::class);
        if (!$authenticatorInstance instanceof Authenticator) {
            throw new InvalidInstanceSavedException();
        }

        return $authenticatorInstance->resolve();
    }

    public static function isGuest(): bool
    {
        return self::user() === null;
    }
}
