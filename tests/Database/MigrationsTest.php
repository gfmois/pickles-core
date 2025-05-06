<?php

namespace Pickles\Tests\Database;

use PDOException;
use PHPUnit\Framework\TestCase;
use Pickles\Database\Drivers\DatabaseDriver;
use Pickles\Database\Migrations\Migrator;

class MigrationsTest extends TestCase
{
    use RefreshDatabase {
        setUp as refreshSetUp;
        tearDown as refreshTearDown;
    }

    protected ?DatabaseDriver $databaseDriver = null;
    protected string $templatesDir = __DIR__ . "/templates";
    protected string $migrationsDir = __DIR__ . "/migrations";
    protected string $expectedMigrationsDir = __DIR__ . "/expected";
    protected Migrator $migrator;

    protected function setUp(): void
    {
        if (!file_exists($this->migrationsDir)) {
            mkdir($this->migrationsDir, 0777, true);
        }

        $this->refreshSetUp();
        $this->migrator = new Migrator(
            $this->migrationsDir,
            $this->templatesDir,
            $this->databaseDriver,
            false
        );
    }

    protected function tearDown(): void
    {
        shell_exec("rm -rf {$this->migrationsDir}");
        $this->refreshTearDown();
    }

    public function getMigrationNames(): array
    {
        return [
            [
                "create_products_table",
                "$this->expectedMigrationsDir/create_products_table.php",
            ],
            [
                "modify_column_from_users_table",
                "$this->expectedMigrationsDir/modify_column_from_users_table.php",
            ],
        ];
    }

    /**
     * @dataProvider getMigrationNames
     */
    public function test_creates_migration_file($name, $expectedMigration): void
    {
        $expectedName = sprintf("%s_%06d_%s.php", date('Y_m_d_'), 0, $name);
        $this->migrator->make($name);

        $file = "$this->migrationsDir/$expectedName";

        $this->assertFileExists($file);
        $this->assertFileEquals($expectedMigration, $file);
    }

    /**
     * @depends test_creates_migration_file
     */
    public function test_migrate_files()
    {
        $tables = ["users", "products", "sellers"];
        $migrated = [];

        foreach ($tables as $table) {
            $migrationName = $this->migrator->make("create_{$table}_table");
            $migrationName = str_replace(".php", "", $migrationName);
            $migrated[] = $migrationName;
        }

        $this->migrator->migrate();

        $rows = $this->databaseDriver->statement("SELECT * FROM migrations");

        $this->assertEquals(3, count($rows));
        $this->assertEquals($migrated, array_column($rows, "migration_name"));

        foreach ($tables as $table) {
            try {
                $this->databaseDriver->table($table)->get();
            } catch (PDOException $e) {
                $this->fail("Failed accessing migrated table $table: {$e->getMessage()}");
            }
        }
    }
}
