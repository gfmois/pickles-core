<?php

namespace Pickles\Session;

/**
 * Interface SessionStorage
 *
 * Defines the contract for session storage implementations.
 * Provides methods to manage session lifecycle and data.
 */
interface SessionStorage
{
    /**
     * Starts the session storage mechanism.
     *
     * This method is responsible for initializing the session storage,
     * ensuring that the session is ready for use. It should be called
     * before attempting to read or write session data.
     *
     * @return void
     */
    public function start();

    /**
     * Retrieves the current session ID.
     *
     * @return string The session ID.
     */
    public function id(): string;

    /**
     * Retrieves a value from the session storage by its key.
     *
     * @param string $key The key associated with the value to retrieve.
     * @param mixed|null $default The default value to return if the key does not exist. Defaults to null.
     * @return mixed The value associated with the given key, or the default value if the key does not exist.
     */
    public function get(string $key, $default = null): mixed;

    /**
     * Stores a value in the session storage with the specified key.
     *
     * @param string $key The key under which the value will be stored.
     * @param mixed $value The value to store in the session storage.
     * @return void
     */
    public function set(string $key, mixed $value);

    /**
     * Checks if a specific key exists in the session storage.
     *
     * @param string $key The key to check for existence in the session storage.
     * @return bool Returns true if the key exists, false otherwise.
     */
    public function has(string $key): bool;

    /**
     * Removes a value from the session storage.
     *
     * @param string $key The key identifying the value to be removed.
     * @return void
     */
    public function remove(string $key);

    /**
     * Destroys the current session and clears all session data.
     *
     * @return bool Returns true on success or false on failure.
     */
    public function destroy(): bool;

    /**
     * Saves the current session data to the storage.
     *
     * @return bool Returns true on success or false on failure.
     */
    public function save(): bool;
}
