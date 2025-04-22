<?php

namespace Pickles\Session;

class Session
{
    protected SessionStorage $sessionDriver;
    public const FLASH_KEY = "__flash__";
    public const FLASH_OLD_KEY = "__flash_old__";
    public const FLASH_NEW_KEY = "__flash_new__";

    public function __construct(SessionStorage $storage)
    {
        $this->sessionDriver = $storage;
        $this->sessionDriver->start();

        if (!$this->sessionDriver->has(self::FLASH_KEY)) {
            $this->sessionDriver->set(self::FLASH_KEY, [
                self::FLASH_OLD_KEY => [],
                self::FLASH_NEW_KEY => [],
            ]);
        }
    }

    public function __destruct()
    {
        foreach ($this->sessionDriver->get(self::FLASH_KEY)[self::FLASH_OLD_KEY] as $key) {
            $this->sessionDriver->remove($key);
        }
        $this->ageFlashData();
        $this->sessionDriver->save();
    }

    public function flash(string $key, mixed $value)
    {
        $this->sessionDriver->set($key, $value);
        $flash = $this->sessionDriver->get(self::FLASH_KEY);
        $flash[self::FLASH_NEW_KEY][] = $key;
        $this->sessionDriver->set(self::FLASH_KEY, $flash);
    }

    public function ageFlashData()
    {
        $flash = $this->sessionDriver->get(self::FLASH_KEY);
        $flash[self::FLASH_OLD_KEY] = $flash[self::FLASH_NEW_KEY];
        $flash[self::FLASH_NEW_KEY] = [];
        $this->sessionDriver->set(self::FLASH_KEY, $flash);
    }

    public function start()
    {
        $this->sessionDriver->start();
    }

    public function id(): string
    {
        return $this->sessionDriver->id();
    }

    public function get(string $key, $default = null): mixed
    {
        return $this->sessionDriver->get($key, $default);
    }

    public function set(string $key, mixed $value)
    {
        $this->sessionDriver->set($key, $value);
    }

    public function has(string $key): bool
    {
        return $this->sessionDriver->has($key);
    }

    public function remove(string $key)
    {
        return $this->sessionDriver->remove($key);
    }

    public function destroy(): bool
    {
        return $this->sessionDriver->destroy();
    }
}
