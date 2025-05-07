<?php

namespace Pickles\Session;

/**
 * Class PhpNativeSessionStorage
 *
 * This class implements the SessionStorage interface and provides
 * session storage functionality using PHP's native session handling.
 *
 * @package PicklesFramework\Session
 */
class PhpNativeSessionStorage implements SessionStorage
{
    /**
     * @inheritDoc
     */
    public function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            if (!@session_start()) {
                throw new \RuntimeException("Could not start the session: " . error_get_last()['message'] ?? '');
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function id(): string
    {
        return session_id();
    }

    /**
     * @inheritDoc
     */
    public function get(string $key, $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * @inheritDoc
     */
    public function set(string $key, mixed $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * @inheritDoc
     */
    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * @inheritDoc
     */
    public function remove(string $key)
    {
        unset($_SESSION[$key]);
    }

    /**
     * @inheritDoc
     */
    public function destroy(): bool
    {
        return session_destroy();
    }

    /**
     * @inheritDoc
     */
    public function save(): bool
    {
        return session_write_close();
    }
}
