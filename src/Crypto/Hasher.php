<?php

namespace Pickles\Crypto;

interface Hasher
{
    public function hash(string $data): string;
    public function verify(string $data, string $hash): bool;
}
