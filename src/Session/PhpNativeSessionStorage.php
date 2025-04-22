<?php

namespace Pickles\Session;

class PhpNativeSessionStorage implements SessionStorage
{
    public function start()
    {
        if (!session_start()) {
            throw new \RuntimeException("Failed to start session");
        }
    }

    public function id(): string
    {
        return session_id();
    }

    public function get(string $key, $default = null): mixed
    {
        return  $_SESSION[$key] ?? $default;
    }

    public function set(string $key, mixed $value)
    {
        $_SESSION[$key] = $value;
    }

    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public function remove(string $key)
    {
        unset($_SESSION[$key]);
    }

    public function destroy(): bool
    {
        return session_destroy();
    }

    public function save(): bool
    {
        return session_write_close();
    }
}
