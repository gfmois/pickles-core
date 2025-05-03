<?php

namespace Pickles\Session;

use Constants;

// FIXME: This class is not yet implemented. It is a placeholder for future development.
class FileSessionStorage implements SessionStorage
{
    protected string $filesPath = "/pickles/sessions";
    protected array $cachedFiles = [];
    public function start()
    {
    }

    public function id(): string
    {
        $actualSessionId = $_COOKIE[Constants::FRAMEWORK_SESSION_ID_KEY] ?? null;
        if ($actualSessionId == null) {
            $actualSessionId = $this->generateSessionId();
            $_COOKIE[Constants::FRAMEWORK_SESSION_ID_KEY] = $actualSessionId;
        }

        return $actualSessionId;
    }

    public function get(string $key, $default = null): mixed
    {
        $session = $this->read();
        return $session[$key] ?? $default;
    }

    public function set(string $key, mixed $value)
    {
        $session = $this->read();
        $session[$key] = $value;
    }

    public function has(string $key): bool
    {
        $session = $this->read();
        return isset($session[$key]);
    }

    public function remove(string $key)
    {
        $session = $this->read();
        unset($session[$key]);
    }

    public function destroy(): bool
    {
        $filePath = $this->getFilePath();
        return false;
    }
    public function save(): bool
    {
        return false;
    }

    public function read(string $sessionId = $this->id()): string
    {
        $path = $this->getFilePath($sessionId);
        if (!file_exists($path)) {
            $this->write(serialize(""));
        }

        $fileContent = unserialize(file_get_contents($path));
        return $fileContent;
    }

    public function write(string $content)
    {
        file_put_contents($this->getFilePath(), serialize($content));
    }

    public function generateSessionId(int $num_bytes = 4): string
    {
        return bin2hex(openssl_random_pseudo_bytes($num_bytes));
    }

    public function getFilePath(string $sessionId = $this->id()): string
    {
        return $this->filesPath . DIRECTORY_SEPARATOR . $sessionId . ".psctx";
    }
}
