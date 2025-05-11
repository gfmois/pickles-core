<?php

namespace Pickles\Auth\Authenticators;

use Constants;
use Pickles\Auth\Authenticatable;

/**
 * Class SessionAuthenticator
 *
 * This class implements the Authenticator interface and provides
 * authentication functionality using session-based mechanisms.
 *
 * @package Auth\Authenticators
 */
class SessionAuthenticator implements Authenticator
{
    /**
     * Logs in the given authenticatable user by storing their information
     * in the session under a predefined key.
     *
     * @param Authenticatable $authenticatable The user instance to be logged in.
     * @return void
     */
    public function login(Authenticatable $authenticatable)
    {
        session()->set(Constants::AUTH_SESSION, $authenticatable);
    }

    /**
     * Logs out the given authenticatable user by removing their authentication session.
     *
     * @param Authenticatable $authenticatable The user instance to log out.
     * @return void
     */
    public function logout(Authenticatable $authenticatable)
    {
        session()->remove(Constants::AUTH_SESSION);
    }

    /**
     * Checks if the given authenticatable entity is authenticated.
     *
     * This method compares the ID of the current session's authenticated user
     * with the ID of the provided authenticatable entity to determine if they match.
     *
     * @param Authenticatable $authenticatable The entity to check authentication for.
     * @return bool True if the entity is authenticated, false otherwise.
     */
    public function isAuthenticated(Authenticatable $authenticatable): bool
    {
        return session()->get(Constants::AUTH_SESSION)?->id() === $authenticatable->id();
    }

    /**
     * Resolves and retrieves the authenticated user from the session.
     *
     * @return Authenticatable|null Returns an instance of the authenticated user
     *                              if found in the session, or null if no user is authenticated.
     */
    public function resolve(): ?Authenticatable
    {
        return session()->get(Constants::AUTH_SESSION);
    }
}
