<?php

namespace Pickles\Crypto;

/**
 * Class BCrypt
 *
 * Implements the Hasher interface using the BCrypt algorithm for secure password hashing.
 *
 * Provides methods to hash passwords and verify hashed passwords using the BCrypt algorithm.
 */
class BCrypt implements Hasher
{
    /**
     *
     * @inheritDoc
     */
    public function hash(string $data): string
    {
        return password_hash($data, PASSWORD_BCRYPT);
    }

    /**
     *
     * @inheritDoc
     */
    public function verify(string $data, string $hash): bool
    {
        return password_verify($data, $hash);
    }
}
