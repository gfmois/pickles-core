<?php

namespace Pickles\Auth\Authenticators;

use Pickles\Auth\Authenticatable;

/**
 * Interface Authenticator
 *
 * This interface defines the contract for authentication mechanisms.
 * Implementing classes should provide the necessary methods to handle
 * authentication logic, such as verifying credentials and managing
 * user sessions.
 *
 * @package Auth\Authenticators
 */
interface Authenticator
{
    /**
     * Logs in the given user.
     *
     * @param Authenticatable $user The user instance to log in.
     * @return void
     */
    public function login(Authenticatable $user);

    /**
     * Logs out the given user.
     *
     * @param Authenticatable $user The user instance to be logged out.
     * @return void
     */
    public function logout(Authenticatable $user);

    /**
     * Determines if the given user is authenticated.
     *
     * @param Authenticatable $user The user instance to check authentication for.
     * @return bool True if the user is authenticated, false otherwise.
     */
    public function isAuthenticated(Authenticatable $user): bool;

    /**
     * Resolves and returns an instance of an object implementing the Authenticatable interface.
     *
     * @return Authenticatable|null Returns an instance of Authenticatable if resolution is successful,
     *                              or null if no authenticatable entity could be resolved.
     */
    public function resolve(): ?Authenticatable;
}
