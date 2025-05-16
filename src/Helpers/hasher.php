<?php

use Pickles\Crypto\Hasher;

function hashString(string $data): string
{
    $hasher = app(Hasher::class);

    if ($hasher === null) {
        throw new Exception('Hasher is not registered');
    }

    if (!($hasher instanceof Hasher)) {
        throw new Exception('Hasher is not instance of Pickles\Crypto\Hasher');
    }

    return $hasher->hash($data);
}
