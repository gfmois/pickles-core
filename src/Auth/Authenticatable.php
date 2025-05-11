<?php

namespace Pickles\Auth;

use Pickles\Auth\Authenticators\Authenticator;
use Pickles\Container\Exceptions\InvalidInstanceSavedException;
use Pickles\Database\Model;

class Authenticatable extends Model
{
    public function id(): int|string
    {
        return $this->{$this->primaryKey};
    }

    public function login()
    {
        $authenticatorInstance = app(Authenticator::class);
        if (!$authenticatorInstance instanceof Authenticator) {
            throw new InvalidInstanceSavedException();
        }

        $authenticatorInstance->login($this);
    }

    public function logout()
    {
        $authenticatorInstance = app(Authenticator::class);
        if (!$authenticatorInstance instanceof Authenticator) {
            throw new InvalidInstanceSavedException();
        }

        $authenticatorInstance->logout($this);
    }

    public function isAuthenticated(): bool
    {
        $authenticatorInstance = app(Authenticator::class);
        if (!$authenticatorInstance instanceof Authenticator) {
            throw new InvalidInstanceSavedException();
        }

        return $authenticatorInstance->isAuthenticated($this);
    }
}
