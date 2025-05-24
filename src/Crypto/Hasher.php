<?php

namespace Pickles\Crypto;

/**
 * Interface Hasher
 *
 * Defines methods for hashing data and verifying hashes.
 */
interface Hasher
{
    /**
     * Generates a hash value from the given data string.
     *
     * @param string $data The input data to be hashed.
     * @return string The resulting hash as a string.
     */
    public function hash(string $data): string;

    /**
     * Verifies that the given data matches the provided hash.
     *
     * @param string $data The original data to verify.
     * @param string $hash The hash to compare against.
     * @return bool Returns true if the data matches the hash, false otherwise.
     */
    public function verify(string $data, string $hash): bool;
}
