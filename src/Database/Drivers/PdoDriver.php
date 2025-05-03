<?php

namespace Pickles\Database\Drivers;

use PDO;

/**
 * Class PdoDriver
 *
 * This class implements the DatabaseDriver interface and provides
 * functionality for interacting with a database using PHP's PDO (PHP Data Objects).
 *
 * @package PicklesFramework\Database\Drivers
 */
class PdoDriver implements DatabaseDriver
{
    protected ?PDO $pdo;

    /**
     * @inheritDoc
     */
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

    /**
     * @inheritDoc
     */
    public function close()
    {
        $this->pdo = null;
    }

    /**
     * @inheritDoc
     */
    public function statement(string $query, array $bind = []): mixed
    {
        $statement = $this->pdo->prepare($query);
        $statement->execute($bind);

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
