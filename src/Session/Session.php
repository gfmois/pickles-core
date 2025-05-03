<?php

namespace Pickles\Session;

use Constants;

/**
 * Class Session
 *
 * This class is responsible for managing session-related functionality
 * within the application. It provides methods to handle session data
 * and ensure proper session management.
 *
 * @package PicklesFramework\Session
 */
class Session
{
    protected SessionStorage $sessionDriver;

    /**
     * Constructor for the Session class.
     *
     * @param SessionStorage $storage The session storage handler instance.
     */
    public function __construct(SessionStorage $storage)
    {
        $this->sessionDriver = $storage;
        $this->sessionDriver->start();

        if (!$this->sessionDriver->has(Constants::FLASH_KEY)) {
            $this->sessionDriver->set(Constants::FLASH_KEY, [
                Constants::FLASH_OLD_KEY => [],
                Constants::FLASH_NEW_KEY => [],
            ]);
        }
    }

    /**
     * Destructor method for the Session class.
     *
     * This method is automatically called when the object is destroyed.
     * It can be used to perform cleanup tasks, such as closing session
     * resources or saving session data before the object is removed from memory.
     */
    public function __destruct()
    {
        foreach ($this->sessionDriver->get(Constants::FLASH_KEY)[Constants::FLASH_OLD_KEY] as $key) {
            $this->sessionDriver->remove($key);
        }
        $this->ageFlashData();
        $this->sessionDriver->save();
    }

    /**
     * Stores a flash message in the session.
     *
     * Flash messages are temporary data that will only be available
     * for the next request. This method sets the given key-value pair
     * in the session and marks it as a "new" flash message.
     *
     * @param string $key The key under which the flash message will be stored.
     * @param mixed $value The value of the flash message.
     * @return void
     */
    public function flash(string $key, mixed $value)
    {
        $this->sessionDriver->set($key, $value);
        $flash = $this->sessionDriver->get(Constants::FLASH_KEY);
        $flash[Constants::FLASH_NEW_KEY][] = $key;
        $this->sessionDriver->set(Constants::FLASH_KEY, $flash);
    }

    /**
     * Ages the flash data stored in the session.
     *
     * This method moves the current "new" flash data to the "old" flash data
     * and clears the "new" flash data. Flash data is typically used to store
     * temporary session data that is only available for the next request.
     *
     * @return void
     */
    public function ageFlashData()
    {
        $flash = $this->sessionDriver->get(Constants::FLASH_KEY);
        $flash[Constants::FLASH_OLD_KEY] = $flash[Constants::FLASH_NEW_KEY];
        $flash[Constants::FLASH_NEW_KEY] = [];
        $this->sessionDriver->set(Constants::FLASH_KEY, $flash);
    }

    /**
         * Starts the session by invoking the session driver's start method.
         *
         * This method initializes the session handling process using the
         * configured session driver.
         *
         * @return void
         */
    public function start()
    {
        $this->sessionDriver->start();
    }

    /**
     * Retrieve the current session ID. Calls the session driver's `id` method.
     *
     * This method delegates the retrieval of the session ID
     * to the session driver being used.
     *
     * @return string The current session ID.
     */
    public function id(): string
    {
        return $this->sessionDriver->id();
    }

    /**
     * Retrieves a value from the session using the specified key. Calls the session driver's `get` method.
     *
     * @param string $key The key associated with the value to retrieve.
     * @param mixed|null $default The default value to return if the key does not exist in the session.
     * @return mixed The value associated with the key, or the default value if the key does not exist.
     */
    public function get(string $key, $default = null): mixed
    {
        return $this->sessionDriver->get($key, $default);
    }

    /**
     * Sets a value in the session storage. Calls the session driver's `set` method.
     *
     * @param string $key   The key under which the value will be stored.
     * @param mixed  $value The value to store in the session.
     *
     * @return void
     */
    public function set(string $key, mixed $value)
    {
        $this->sessionDriver->set($key, $value);
    }

    /**
     * Checks if a specific key exists in the session. Calls the session driver's `has` method.
     *
     * @param string $key The key to check for existence in the session.
     * @return bool Returns true if the key exists, false otherwise.
     */
    public function has(string $key): bool
    {
        return $this->sessionDriver->has($key);
    }

    /**
     * Removes a value from the session storage. Calls the session driver's `remove` method.
     *
     * @param string $key The key of the session variable to remove.
     * @return mixed The result of the removal operation, as defined by the session driver.
     */
    public function remove(string $key)
    {
        return $this->sessionDriver->remove($key);
    }

    /**
     * Destroys the current session.
     *
     * This method calls the session driver's `destroy` method to terminate
     * the session and remove any associated session data.
     *
     * @return bool Returns true on success or false on failure.
     */
    public function destroy(): bool
    {
        return $this->sessionDriver->destroy();
    }
}
