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
    private string $tableToQuery;

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

    public function table(string $table): static
    {
        $this->tableToQuery = $table;
        return $this;
    }

    // TODO: Add support for basic operations like OR, LIKE, etc...
    /**
     * @inheritDoc
     */
    public function get(?array $data = null): array
    {
        $query = "SELECT * FROM {$this->tableToQuery}";

        if ($data !== null && !empty($data)) {
            $specialKeys = ['LIMIT', 'ORDER BY', 'LIKE', 'OR'];
            $specialValues = array_intersect_key($data, array_flip($specialKeys));
            $data = array_diff_key($data, array_flip($specialKeys));

            $hasLimit = isset($specialValues['LIMIT']);
            $hasOrderBy = isset($specialValues['ORDER BY']);
            $hasLike = isset($specialValues['LIKE']);
            $hasOr = isset($specialValues['OR']);

            $orderBy = $specialValues['ORDER BY'] ?? null;
            $limit = $specialValues['LIMIT'] ?? null;
            $like = $specialValues['LIKE'] ?? null;
            $or = $specialValues['OR'] ?? null;

            if ($hasLimit) {
                unset($data['LIMIT']);
            }

            if ($hasOrderBy) {
                unset($data['ORDER BY']);
            }

            if ($hasLike) {
                unset($data['LIKE']);
            }

            if ($hasOr) {
                unset($data['OR']);
            }

            $conditions = [];
            $hasWhere = false;
            foreach ($data as $key => $_) {
                $conditions[] = "$key = ?";
            }

            $likeConditions = [];
            if ($like !== null) {
                foreach ($like as $key => $_) {
                    if (is_array($_)) {
                        foreach ($_ as $value) {
                            $likeConditions[] = "$key LIKE ?";
                        }
                    } else {
                        $likeConditions[] = "$key LIKE ?";
                    }
                }
            }

            $orConditions = [];
            if ($or !== null) {
                foreach ($or as $key => $value) {
                    $isSpecialKey = array_key_exists($key, array_flip($specialKeys));
                    if (is_array($value)) {
                        foreach ($value as $subKey => $_) {
                            $isSpecialKey
                                ? $orConditions[] = "$subKey $key ?"
                                : $orConditions[] = "$key = ?";
                        }
                    } else {
                        echo "inside";
                        $orConditions[] = "$key = ?";
                    }
                }
            }

            if (!empty($conditions)) {
                $hasWhere = true;
                $query .= " WHERE " . implode(" AND ", $conditions);
            }

            if (!empty($or)) {
                $query .= ($hasWhere ? " OR " : " WHERE ") . implode(" OR ", $orConditions);
                $hasWhere = true;

                foreach ($or as $value) {
                    $data[] = $value;
                }
            }

            if (!empty($like)) {
                $query .= ($hasWhere ? " AND " : " WHERE ") . implode(" AND ", $likeConditions);

                foreach ($like as $value) {
                    $data[] = $value;
                }
            }

            if ($hasOrderBy) {
                $query .= " ORDER BY $orderBy";
            }

            if ($hasLimit) {
                $query .= " LIMIT $limit";
            }
        }

        return $this->statement($query, array_values($data ?? []));
    }

    /**
     * @inheritDoc
     */
    public function insert(array $data): void
    {
        $query = "INSERT INTO {$this->tableToQuery} (";
        $query .= implode(", ", array_keys($data)) . ") VALUES (";
        $query .= implode(", ", array_fill(0, count($data), "?")) . ")";

        $this->statement($query, array_values($data));
    }

    /**
     * @inheritDoc
     */
    public function delete(array $data): void
    {
        $query = "DELETE FROM {$this->tableToQuery}";
        if ($data !== null && !empty($data)) {
            $query .= " WHERE " . implode(" AND ", array_map(fn ($key) => "$key = ?", array_keys($data)));
        }

        $this->statement($query, array_values($data));
    }

    /**
     * @inheritDoc
     */
    public function lastInsertId(): int
    {
        return (int) $this->pdo->lastInsertId();
    }
}
