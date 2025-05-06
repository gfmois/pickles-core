<?php

namespace Pickles\Tests\Database;

use PDOException;
use Pickles\Database\Drivers\DatabaseDriver;
use Pickles\Database\Drivers\PdoDriver;
use Pickles\Database\Model;

trait RefreshDatabase
{
    protected function setUp(): void
    {
        if ($this->databaseDriver === null) {
            $this->databaseDriver = singleton(DatabaseDriver::class, PdoDriver::class);
            Model::setDatabaseDriver($this->databaseDriver);
            try {
                $this->databaseDriver->connect("mysql", "127.0.0.1", 3306, "root", "1234", "test");
            } catch (PDOException $e) {
                $this->markTestSkipped("Database connection failed: {$e->getMessage()}");
            }
        }
    }

    protected function tearDown(): void
    {
        $this->databaseDriver->statement("DROP DATABASE IF EXISTS test");
        $this->databaseDriver->statement("CREATE DATABASE test");
    }
}
