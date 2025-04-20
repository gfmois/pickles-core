<?php

namespace Pickles\Session;

interface SessionStorage
{
    public function start();
    public function id(): string;
    public function get(string $key, $default = null): mixed;
    public function set(string $key, mixed $value);
    public function has(string $key): bool;
    public function remove(string $key);
    public function destroy(): bool;
}
