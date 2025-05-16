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

    /**
     * Sets the table name for the database query.
     *
     * @param string $table The name of the table to interact with.
     * @return static Returns the current instance for method chaining.
     */
    public function table(string $table): static;

    /**
     * Retrieves data from the database based on the provided criteria.
     *
     * @param array|null $data Optional associative array of criteria to filter the data.
     *                         If null, retrieves all data.
     * @return array An array of results matching the criteria.
     */
    public function get(?array $data = null): array;

    /**
     * Inserts a new record into the database.
     *
     * @param array $data An associative array containing the data to be inserted,
     *                    where the keys represent column names and the values
     *                    represent the corresponding values to be stored.
     *
     * @return void
     */
    public function insert(array $data): void;

    /**
     * Deletes a record from the database based on the provided data.
     *
     * @param array $data An associative array containing the criteria for deletion.
     *
     * @return void
     */
    public function delete(array $data): void;


    /**
     * Retrieves the ID of the last inserted row.
     *
     * @return int The ID of the last inserted row in the database.
     */
    public function lastInsertId(): int;
}
