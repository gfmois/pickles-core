<?php

namespace Pickles\Database;

use Pickles\Database\Drivers\DatabaseDriver;
use Pickles\Database\Exceptions\NoDriverSetException;
use Pickles\Database\Exceptions\NoFillableAttributesDefinedException;
use Pickles\Database\Exceptions\NoFillableAttributesProvidedException;

abstract class Model
{
    protected ?string $table = null;
    protected string $primaryKey = 'id';
    protected array $hidden = [];
    protected array $fillable = [];
    protected array $attributes = [];
    protected bool $insertTimestamps = false;
    private static ?DatabaseDriver $driver = null;

    /**
     * Sets the database driver instance to be used by the model.
     *
     * @param DatabaseDriver $driver The database driver instance to set.
     *
     * @return void
     */
    public static function setDatabaseDriver(DatabaseDriver $driver): void
    {
        self::$driver = $driver;
    }

    public function __construct()
    {
        if ($this->table === null) {
            $subclass = (new \ReflectionClass(static::class))->getShortName();
            $this->table = snake_case("{$subclass}s");
        }
    }

    public function __set($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    public function __get($name)
    {
        return $this->attributes[$name] ?? null;
    }

    public function __call($method, $args)
    {
        // Check if the method requires a database driver
        if (in_array($method, ['save', 'first', 'find', 'all', 'where'])) {
            $this->checkDriver();
        }

        // If the method exists in the parent class, call it
        if (is_callable([$this, $method])) {
            return $this->$method(...$args);
        }

        // Throw an exception for undefined methods
        throw new \BadMethodCallException(
            "Undefined method '{$method}' called on " . static::class . " with arguments: " . json_encode($args)
        );
    }

    /**
     * Checks if the database driver is set.
     *
     * This method verifies whether the static `$driver` property is initialized.
     * If the driver is not set, it throws a `NoDriverSetException`.
     *
     * @throws NoDriverSetException If the database driver is not set.
     * @return void
     */
    private function checkDriver(): void
    {
        if (self::$driver === null) {
            throw new NoDriverSetException("Database driver is not set.");
        }
    }

    private function checkTimestamps(): void
    {
        if ($this->insertTimestamps) {
            $created_at = date("Y-m-d H:m:s");
            $updated_at = date("Y-m-d H:m:s");

            $this->attributes["created_at"] = $created_at;
            $this->attributes["updated_at"] = $updated_at;
        }
    }

    /**
     * Sets the attributes for the model.
     *
     * @param array $attributes An associative array of attributes to set.
     * @return static Returns the current instance of the model with the attributes set.
     */
    protected function setAttributes(array $attributes): static
    {
        foreach ($attributes as $key => $value) {
            $this->__set($key, $value);
        }

        return $this;
    }

    /**
     * Mass assigns the given attributes to the model if they are fillable.
     *
     * @param array $attributes An associative array of attributes to assign to the model.
     *
     * @throws NoFillableAttributesDefinedException If no fillable attributes are defined for the model.
     * @throws NoFillableAttributesProvidedException If no attributes are provided for assignment.
     *
     * @return static Returns the current instance of the model after assigning the attributes.
     */
    protected function massAssign(array $attributes): static
    {
        if (empty($this->fillable)) {
            throw new NoFillableAttributesDefinedException("No fillable attributes defined for " . static::class .  ".");
        }

        if (count($attributes) === 0) {
            throw new NoFillableAttributesProvidedException("No fillable attributes provided for " . static::class .  ".");
        }

        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->fillable)) {
                $this->__set($key, $value);
            }
        }

        return $this;
    }

    /**
     * Converts the model's attributes to an array, excluding hidden attributes.
     *
     * This method returns an associative array of the model's attributes,
     * excluding any attributes specified in the `$hidden` property.
     *
     * @return array The filtered array of attributes.
     */
    protected function toArray(): array
    {
        return array_diff_key(
            $this->attributes,
            array_flip($this->hidden)
        );
    }

    /**
     * Creates a new instance of the model and assigns the given attributes to it.
     *
     * @param array $attributes An associative array of attributes to assign to the model.
     * @return static A new instance of the model with the provided attributes assigned.
     */
    public static function create(array $attributes): static
    {
        return (new static())->massAssign($attributes)->save();
    }

    /**
     * Saves the current model instance to the database.
     *
     * This method constructs an SQL `INSERT` query using the model's attributes
     * and executes it using the database driver. The attributes of the model
     * are used as column names and their corresponding values are inserted
     * into the database table.
     *
     * @return static Returns the current instance of the model.
     */
    public function save(): static
    {
        $this->checkTimestamps();
        $columns = implode(", ", array_keys($this->attributes));
        $values = array_values($this->attributes);
        $bind = implode(", ", array_fill(0, count($this->attributes), '?'));

        $query = "INSERT INTO {$this->table} ({$columns}) VALUES ({$bind})";
        self::$driver->statement($query, $values);

        return $this;
    }

    /**
     * Retrieves the first record from the database model.
     *
     * @return static|null Returns the first record as an instance of the model,
     *                     or null if no records are found.
     */
    public static function first(): ?static
    {
        $model = new static();
        $rows = self::$driver->statement("SELECT * FROM " . $model->table . " LIMIT 1");

        if (count($rows) === 0) {
            return null;
        }

        return $model->setAttributes($rows[0]);
    }

    /**
     * Finds a record in the database by its primary key.
     *
     * @param int|string $id The primary key of the record to find.
     * @return static|null The found record as an instance of the calling class, or null if not found.
     */
    public static function find(int|string $id): ?static
    {
        $model = new static();
        $rows = self::$driver->statement("SELECT * FROM $model->table WHERE $model->primaryKey = ?", [$id]);

        if (count($rows) === 0) {
            return null;
        }

        return $model->setAttributes($rows[0]);
    }

    /**
     * Retrieve all records from the database table associated with the model.
     *
     * @return array<static> An array of all records as associative arrays or model instances.
     */
    public static function all(): array
    {
        $model = new static();
        $rows = self::$driver->statement("SELECT * FROM $model->table");

        if (count($rows) === 0) {
            return [];
        }

        return $model->mapRowsToModels($model, $rows);
    }

    /**
     * Filters records based on the specified column and value.
     *
     * @param string $column The name of the column to filter by.
     * @param mixed $value The value to match against the specified column.
     * @return array<static> An array of records that match the specified condition.
     */
    public static function where(string $column, mixed $value, string $operator = "="): array
    {
        $model = new static();
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $column)) {
            throw new \InvalidArgumentException("Invalid column name: $column");
        }


        $rows = self::$driver->statement("SELECT * FROM $model->table WHERE $column $operator ?", [$value]);

        if (count($rows) === 0) {
            return [];
        }

        return $model->mapRowsToModels($model, $rows);
    }

    /**
     * Maps an array of database rows to an array of model instances.
     *
     * @param Model $model The model instance used as a template for mapping rows.
     * @param array $rows An array of associative arrays representing database rows.
     * @return array<static> An array of model instances populated with data from the rows.
     */
    private function mapRowsToModels(Model $model, array $rows): array
    {
        $models = [$model->setAttributes($rows[0])];
        for ($i = 1; $i < count($rows); $i++) {
            $models[] = (new static())->setAttributes($rows[$i]);
        }

        return array_map(fn ($model) => $model->toArray(), $models);
    }
}
