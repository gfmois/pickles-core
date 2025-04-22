<?php

namespace Pickles\Session;

use Constants;

class Session
{
    protected SessionStorage $sessionDriver;

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

    public function __destruct()
    {
        foreach ($this->sessionDriver->get(Constants::FLASH_KEY)[Constants::FLASH_OLD_KEY] as $key) {
            $this->sessionDriver->remove($key);
        }
        $this->ageFlashData();
        $this->sessionDriver->save();
    }

    public function flash(string $key, mixed $value)
    {
        $this->sessionDriver->set($key, $value);
        $flash = $this->sessionDriver->get(Constants::FLASH_KEY);
        $flash[Constants::FLASH_NEW_KEY][] = $key;
        $this->sessionDriver->set(Constants::FLASH_KEY, $flash);
    }

    public function ageFlashData()
    {
        $flash = $this->sessionDriver->get(Constants::FLASH_KEY);
        $flash[Constants::FLASH_OLD_KEY] = $flash[Constants::FLASH_NEW_KEY];
        $flash[Constants::FLASH_NEW_KEY] = [];
        $this->sessionDriver->set(Constants::FLASH_KEY, $flash);
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
