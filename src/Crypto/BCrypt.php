<?php

namespace Pickles\Crypto;

class BCrypt implements Hasher
{
    public function hash(string $data): string
    {
        return password_hash($data, PASSWORD_BCRYPT);
    }
    public function verify(string $data, string $hash): bool
    {
        return password_verify($data, $hash);
    }
}
