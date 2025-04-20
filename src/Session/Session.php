<?php

namespace Pickles\Session;

class Session
{
    protected SessionStorage $driver;

    public function __construct(SessionStorage $storage)
    {
        $this->driver = $storage;
        $this->driver->start();
    }

    public function flash(string $key, mixed $value)
    {
    }

    public function start()
    {
        $this->driver->start();
    }

    public function id(): string
    {
        return $this->driver->id();
    }

    public function get(string $key, $default = null): mixed
    {
        return $this->driver->get($key, $default);
    }

    public function set(string $key, mixed $value)
    {
        $this->driver->set($key, $value);
    }

    public function has(string $key): bool
    {
        return $this->driver->has($key);
    }

    public function remove(string $key)
    {
        return $this->driver->remove($key);
    }

    public function destroy(): bool
    {
        return $this->driver->destroy();
    }
}
