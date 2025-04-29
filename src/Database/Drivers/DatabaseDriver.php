<?php

namespace Pickles\Database\Drivers;

interface DatabaseDriver
{
    public function connect(string $protocol, string $host, int $port, string $user, string $password, string $database);
    public function close();
    public function statement(string $query, array $bind = []): mixed;
}
