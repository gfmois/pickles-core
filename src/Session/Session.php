<?php

namespace Pickles\Session;

class Session
{
    protected SessionStorage $driver;
    public const FLASH_KEY = "__flash__";
    public const FLASH_OLD_KEY = "__flash_old__";
    public const FLASH_NEW_KEY = "__flash_new__";

    public function __construct(SessionStorage $storage)
    {
        $this->driver = $storage;
        $this->driver->start();

        if (!$this->driver->has(self::FLASH_KEY)) {
            $this->driver->set(self::FLASH_KEY, [
                self::FLASH_OLD_KEY => [],
                self::FLASH_NEW_KEY => [],
            ]);
        }
    }

    public function __destruct()
    {
        foreach ($this->driver->get(self::FLASH_KEY)[self::FLASH_OLD_KEY] as $key) {
            $this->driver->remove($key);
        }
        $this->ageFlashData();
        $this->driver->save();
    }

    public function flash(string $key, mixed $value)
    {
        $this->driver->set($key, $value);
        $flash = $this->driver->get(self::FLASH_KEY);
        $flash[self::FLASH_NEW_KEY][] = $key;
        $this->driver->set(self::FLASH_KEY, $flash);
    }

    public function ageFlashData()
    {
        $flash = $this->driver->get(self::FLASH_KEY);
        $flash[self::FLASH_OLD_KEY] = $flash[self::FLASH_NEW_KEY];
        $flash[self::FLASH_NEW_KEY] = [];
        $this->driver->set(self::FLASH_KEY, $flash);
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
