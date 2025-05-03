<?php

namespace Pickles\Database;

/**
 * Class DB
 *
 * This class is responsible for handling database operations.
 * It provides methods to interact with the database, such as
 * querying, inserting, updating, and deleting records.
 *
 * @package PicklesFramework\Database
 */
class DB
{
    /**
     * Executes a raw SQL statement with optional bound parameters.
     *
     * @param string $query The raw SQL query to be executed.
     * @param array $bind An associative array of parameters to bind to the query.
     *                    The keys should match the placeholders in the query.
     * @return mixed The result of the executed statement.
     */
    public static function statement($query, array $bind =  [])
    {
        return db()->statement($query, $bind);
    }
}
