<?php

namespace Pickles\Database\Drivers;

/**
 * Interface DatabaseDriver
 *
 * Defines the contract for database driver implementations.
 */
interface DatabaseDriver
{
    /**
     * Establishes a connection to the database.
     *
     * @param string $protocol The protocol to use for the connection (e.g., 'mysql', 'pgsql').
     * @param string $host The hostname or IP address of the database server.
     * @param int $port The port number to connect to on the database server.
     * @param string $user The username for authentication.
     * @param string $password The password for authentication.
     * @param string $database The name of the database to connect to.
     * @return void
     */
    public function connect(string $protocol, string $host, int $port, string $user, string $password, string $database);

    /**
     * Closes the connection to the database.
     *
     * @return void
     */
    public function close();

    /**
     * Prepares and executes a database statement.
     *
     * @param string $query The SQL query to execute.
     * @param array $bind An optional associative array of parameters to bind to the query.
     * @return mixed The result of the query execution, format depends on the implementation.
     */
    public function statement(string $query, array $bind = []): mixed;
}
