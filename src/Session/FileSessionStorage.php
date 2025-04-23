<?php

namespace Pickles\Session;

class FileSessionStorage implements SessionStorage {
    protected string $filesPath = "/pickles/sessions";

    public function start() {}

    public function id(): string {
        $actualSessionId = $_COOKIE[Constants::FRAMEWORK_SESSION_ID_KEY] ?? null;
        if ($actualSessionId == null) {
            $actualSessionId = $this->generateSessionId();
            $_COOKIE[Constants::FRAMEWORK_SESSION_ID_KEY] = $actualSessionId;
        }

        return $actualSessionId;
    }

    public function get(string $key, $default = null): mixed { return null; }
    public function set(string $key, mixed $value) { return null; }
    public function has(string $key): bool { return null; }
    public function remove(string $key) { return null; }
    public function destroy(): bool { return null; }
    public function save(): bool { return null; }

    public function read(string $sessionId = $this->id()): string {
        $path = $this->filesPath . DIRECTORY_SEPARATOR . $sessionId . ".psctx";
        if (!file_exists()) {
            $this->write("");
        }

        $fileContent = unserialize(file_get_content($path));
        return $fileContent;
    }

    public function write(string $content) {
        file_put_content("{$this->filesPath}/{$this->id()}.psctx", serialize($content));
    }

    public function generateSessionId(int $num_bytes = 4): string {
        return bin2hex(openssl_random_pseudo_bytes($num_bytes));
    }
}