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

class MockModelFillable extends MockModel
{
    protected ?string $table = "mock_models";
    protected array $fillable = ["test", "name"];
}

class ModelTest extends TestCase
{
    use RefreshDatabase;
    protected ?DatabaseDriver $databaseDriver = null;

    private function createTestTable($name, $columns, $withTimestamps = true): void
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

    /**
      * @depends test_save_basic_model_with_attributes
      */
    public function test_find_model()
    {
        $this->createTestTable("mock_models", ["test", "name"], withTimestamps: true);

        $expected = [
            [
                "id" => 1,
                "test" => "Test",
                "name" => "Name",
                "created_at" => date("Y-m-d H:m:s"),
                "updated_at" => date("Y-m-d H:m:s"),
            ],
            [
                "id" => 2,
                "test" => "Foo",
                "name" => "Bar",
                "created_at" => date("Y-m-d H:m:s"),
                "updated_at" => date("Y-m-d H:m:s"),
            ],
        ];

        foreach ($expected as $columns) {
            $model = new MockModel();
            $model->test = $columns["test"];
            $model->name = $columns["name"];
            $model->save();
        }

        foreach ($expected as $columns) {
            $model = new MockModel();
            foreach ($columns as $column => $value) {
                $model->{$column} = $value;
            }
            $this->assertEquals($model, MockModel::find($columns["id"]));
        }

        $this->assertNull(MockModel::find(5));
    }

    /**
      * @depends test_save_basic_model_with_attributes
      */
    public function test_create_model_with_no_fillable_attributes_throws_error()
    {
        $this->expectException(PDOException::class);
        MockModel::create(["test" => "test"]);
    }

    /**
      * @depends test_create_model_with_no_fillable_attributes_throws_error
      */
    public function test_create_model()
    {
        $this->createTestTable("mock_models", ["test", "name"], true);

        $model = MockModelFillable::create(["test" => "Test", "name" => "Name"]);
        $this->assertEquals(1, count($this->databaseDriver->statement("SELECT * FROM mock_models")));
        $this->assertEquals("Name", $model->name);
        $this->assertEquals("Test", $model->test);
    }

    /**
     * @depends test_create_model
     */
    public function test_all()
    {
        $this->createTestTable("mock_models", ["test", "name"], true);

        MockModelFillable::create(["test" => "Test", "name" => "Name"]);
        MockModelFillable::create(["test" => "Test", "name" => "Name"]);
        MockModelFillable::create(["test" => "Test", "name" => "Name"]);

        $models = MockModelFillable::all();

        $this->assertEquals(3, count($models));

        foreach ($models as $model) {
            $this->assertEquals("Test", $model->test);
            $this->assertEquals("Name", $model->name);
        }
    }

    /**
      * @depends test_create_model
      */
    public function test_where_and_first_where()
    {
        $this->createTestTable("mock_models", ["test", "name"], true);

        MockModelFillable::create(["test" => "First", "name" => "Name"]);
        MockModelFillable::create(["test" => "Where", "name" => "Foo"]);
        MockModelFillable::create(["test" => "Where", "name" => "Foo"]);

        $where = MockModelFillable::where("test", "Where");
        $this->assertEquals(2, count($where));
        $this->assertEquals("Where", $where[0]->test);
        $this->assertEquals("Where", $where[1]->test);

        $firstWhere = MockModelFillable::firstWhere('test', 'First');

        $this->assertEquals("First", $firstWhere->test);
    }

    /**
      * @depends test_create_model
      * @depends test_find_model
      */
    public function test_update()
    {
        $this->createTestTable("mock_models", ["test", "name"]);

        MockModelFillable::create(["test" => "test", "name" => "name"]);

        $model = MockModelFillable::find(1);

        $model->test = "UPDATED test";
        $model->name = "UPDATED name";
        $model->update();

        $rows = $this->databaseDriver->statement("SELECT test, name FROM mock_models");
        $this->assertEquals(1, count($rows));
        $this->assertEquals(["test" => "UPDATED test", "name" => "UPDATED name"], $rows[0]);
    }

    /**
      * @depends test_create_model
      * @depends test_find_model
      */
    public function test_delete()
    {
        $this->createTestTable("mock_models", ["test", "name"]);

        MockModelFillable::create(["test" => "test", "name" => "name"]);

        $model = MockModelFillable::find(1);

        $model->delete();

        $rows = $this->databaseDriver->statement("SELECT test, name FROM mock_models");
        $this->assertEquals(0, count($rows));
    }
}
