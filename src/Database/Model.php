<?php

namespace Pickles\Database;

use Pickles\Database\Drivers\DatabaseDriver;

abstract class Model
{
    protected ?string $table = null;
    protected string $primaryKey = 'id';
    protected array $hidden = [];
    protected array $fillable = [];
    protected array $attributes = [];
    private static ?DatabaseDriver $driver = null;

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

    public function save(): mixed
    {
        $columns = implode(", ", array_keys($this->attributes));
        $values = array_values($this->attributes);
        $bind = implode(", ", array_fill(0, count($this->attributes), '?'));

        $query = "INSERT INTO {$this->table} ({$columns}) VALUES ({$bind})";
        return self::$driver->statement($query, $values);
    }
}
