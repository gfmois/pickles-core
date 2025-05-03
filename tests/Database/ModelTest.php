<?php

namespace Pickles\Tests\Database;

use PDOException;
use PHPUnit\Framework\TestCase;
use Pickles\Database\Drivers\DatabaseDriver;
use Pickles\Database\Drivers\PdoDriver;
use Pickles\Database\Model;

class MockModel extends Model
{
    protected bool $insertTimestamps = true;
    protected array $fillable = ["test", "name"];
}

class ModelTest extends TestCase
{
    protected ?DatabaseDriver $databaseDriver = null;

    protected function setUp(): void
    {
        if ($this->databaseDriver === null) {
            $this->databaseDriver = new PdoDriver();
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

    private function createTestTable($name, $columns, $withTimestamps = false): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS $name (
            id INT AUTO_INCREMENT PRIMARY KEY,
            " . implode(", ", array_map(fn ($col) => "$col VARCHAR(255)", $columns)) . " 
        ";

        if ($withTimestamps) {
            $sql .= ", created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
        }

        $sql .= ")";

        $this->databaseDriver->statement($sql);
    }

    public function test_save_basic_model_with_attributes(): void
    {
        $this->createTestTable("mock_models", ["test", "name"], true);
        $model = new MockModel();
        $model->test = "test_value";
        $model->name = "name_value";

        $model->save();

        $rows = $this->databaseDriver->statement("SELECT * FROM mock_models");
        $expected = [
            "id" => 1,
            "test" => "test_value",
            "name" => "name_value",
            "created_at" => date("Y-m-d H:m:s"),
            "updated_at" => date("Y-m-d H:m:s"),
        ];

        $this->assertEquals($expected, $rows[0]);
        $this->assertEquals(1, count($rows));
    }
}
