<?php

namespace Pickles\Database\Drivers;

use PDO;

class PdoDriver implements DatabaseDriver
{
    protected ?PDO $pdo;
    public function connect(string $protocol, string $host, int $port, string $user, string $password, string $database)
    {
        $dsn = sprintf(
            "%s:host=%s;port=%d;dbname=%s",
            $protocol,
            $host,
            $port,
            $database
        );

        $this->pdo = new PDO($dsn, $user, $password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function close()
    {
        $this->pdo = null;
    }

    public function statement(string $query, array $bind = []): mixed
    {
        $statement = $this->pdo->prepare($query);
        $statement->execute($bind);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
