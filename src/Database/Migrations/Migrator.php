<?php

namespace Pickles\Database\Migrations;

use Constants;
use Pickles\Database\Drivers\DatabaseDriver;
use Pickles\Utils\FileUtils;
use RuntimeException;

/**
 * The Migrator class is responsible for handling database migrations.
 * It provides functionality to run, rollback, and manage migrations
 * within the application.
 *
 * @package Pickles\Database\Migrations
 */
class Migrator
{
    public function __construct(
        private string $migrationsDir,
        private string $templateDir,
        private DatabaseDriver $databaseDriver,
        private bool $logProgress = true,
    ) {
        $this->migrationsDir = $migrationsDir;
        $this->templateDir = $templateDir;
        $this->databaseDriver = $databaseDriver;
        $this->logProgress = $logProgress;

        FileUtils::ensureDirectoryExists($this->migrationsDir);
    }

    /**
     * Executes the migration process by applying all pending migrations.
     *
     * This method performs the following steps:
     * 1. Ensures the migration table exists by calling `createMigrationTable()`.
     * 2. Retrieves the list of already migrated entries from the `migrations` table.
     * 3. Fetches all available migration files.
     * 4. Compares the migrated entries with the available migrations to determine
     *    which migrations are pending.
     * 5. Iterates over the pending migrations, requiring each migration file,
     *    validating its type, and executing its `up()` method.
     * 6. Records the successful migration in the `migrations` table.
     * 7. Logs the progress and results of the migration process.
     *
     * If there are no pending migrations, a log message is generated indicating
     * that there is nothing to migrate.
     *
     * @return void
     */
    public function migrate(): void
    {
        $this->createMigrationTable();
        $migrated = $this->databaseDriver->table(Constants::MIGRATIONS_TABLE_NAME)->get();
        $migrations = $this->getAllMigrations();

        if (count($migrated) >= count($migrations)) {
            $this->log("Nothing to migrate");
            return;
        }

        $cleanMigrations = array_slice($migrations, count($migrated));
        foreach ($cleanMigrations as $migrationFile) {
            $migration = require $migrationFile;
            if (!$migration instanceof Migration) {
                $this->log("Invalid migration file: $migrationFile");
                continue;
            }

            $migration->up();
            $migrationName = str_replace('.php', '', basename($migrationFile));
            $this->databaseDriver->table(Constants::MIGRATIONS_TABLE_NAME)->insert([
                'migration_name' => $migrationName
            ]);
            $this->log("Migrated: $migrationName successfully");
        }
    }

    /**
     * Rollback the last database migration(s).
     *
     * @param int|null $steps The number of migrations to rollback. If null, it will rollback the last batch.
     * @return void
     */
    public function rollback(?int $steps = null): void
    {
        $this->createMigrationTable();
        $migrated = $this->databaseDriver->table(Constants::MIGRATIONS_TABLE_NAME)->get();
        $pendingCount = count($migrated);

        if ($pendingCount === 0) {
            $this->log("Nothing to rollback");
            return;
        }

        if ($steps === null || $steps > $pendingCount) {
            $steps = $pendingCount;
        }

        $migrations = array_reverse($this->getAllMigrations());
        $migrations = array_slice($migrations, -$pendingCount, $steps);

        foreach ($migrations as $migrationFile) {
            $migration = require $migrationFile;
            if (!$migration instanceof Migration) {
                $this->log("Invalid migration file: $migrationFile");
                continue;
            }

            $migration->down();
            $migrationName = str_replace('.php', '', basename($migrationFile));
            $this->databaseDriver->table(Constants::MIGRATIONS_TABLE_NAME)->delete([
                'migration_name' => $migrationName
            ]);
            $this->log("Rolled back: $migrationName successfully");

            if (--$steps == 0) {
                break;
            }
        }
    }

    /**
     * Creates a new migration file based on the provided migration name.
     *
     * This method generates a migration file by transforming the given migration name
     * into a snake_case format and applying a template. Depending on the migration name,
     * it determines the type of migration (e.g., creating a table, altering a table, or
     * a custom migration) and modifies the template accordingly.
     *
     * @param string $migrationName The name of the migration to create.
     *
     * The method performs the following:
     * - Converts the migration name to snake_case.
     * - Loads a migration template from the specified template directory.
     * - If the migration name matches the pattern for creating a table (`create_*_table`),
     *   it generates SQL statements for creating and dropping the table.
     * - If the migration name matches the pattern for altering a table (`*_from_*_table` or `*_to_*_table`),
     *   it generates SQL statements for altering the table.
     * - For other cases, it comments out any `DB::statement` lines in the template.
     * - Generates the migration file and outputs its file path.
     *
     * @return string The name of the created migration file.
     * @throws RuntimeException If the template file cannot be loaded or the migration file cannot be created.
     */
    public function make(string $migrationName): string
    {
        $migrationName = snake_case($migrationName);
        $template = file_get_contents("$this->templateDir/migration.php");

        if (preg_match("/create_.*_table/", $migrationName)) {
            $table = $this->getTableName($migrationName, "/create_(.*)_table/");
            $template = str_replace('$UP', "CREATE TABLE $table (id INT AUTO_INCREMENT PRIMARY KEY);", $template);
            $template = str_replace('$DOWN', "DROP TABLE $table;", $template);
        } elseif (preg_match("/.*_(from|to)_(.*)_table/", $migrationName)) {
            $table = $this->getTableName($migrationName, "/.*_(from|to)_(.*)_table/", group: 2);
            $template = preg_replace('/\$UP|\$DOWN/', "ALTER TABLE $table", $template);
        } else {
            $template = preg_replace_callback("/DB::statement.*/", fn ($matches) => "// {$matches[0]}", $template);
        }

        [$fileName, $filePath] = $this->generateMigrationFile($migrationName, $template);
        $this->log("Migration file created: $fileName");

        return $fileName;
    }

    /**
     * Retrieves the table name from a migration name using a regular expression.
     *
     * @param string $migrationName The name of the migration.
     * @param string $regex The regular expression used to extract the table name.
     * @param int $group The regex capture group to extract the table name. Defaults to 1.
     * @return string The extracted table name.
     */
    private function getTableName(string $migrationName, string $regex, int $group = 1): string
    {
        return preg_replace_callback($regex, fn ($matches) => $matches[$group], $migrationName);
    }

    /**
     * Generates a new migration file based on the provided migration name and template.
     *
     * @param string $migrationName The name of the migration to be created.
     * @param string $template The template content to be used for the migration file.
     * @return array<string> Returns an array containing the file name and the full path of the created migration file.
     */
    private function generateMigrationFile(string $migrationName, string $template): array
    {
        $date = date('Y_m_d_');
        $id = $this->getIdForMigrationFile($date);

        $fileName = sprintf("%s_%06d_%s.php", $date, $id, $migrationName);
        $path = "$this->migrationsDir/$fileName";

        file_put_contents($path, $template);

        return [$fileName, $path];
    }

    /**
     * Retrieves the unique identifier for a migration file based on the provided date.
     *
     * @param string $date The date string used to identify the migration file.
     * @return int The unique identifier corresponding to the migration file.
     */
    private function getIdForMigrationFile(string $date): int
    {
        $id = 0;

        foreach (glob("$this->migrationsDir/*.php") as $file) {
            if (str_starts_with(basename($file), $date)) {
                $id++;
            }
        }

        return $id;
    }

    /**
     * Creates the migration table in the database if it does not already exist.
     * This table is used to track the migrations that have been executed.
     *
     * @return void
     */
    private function createMigrationTable(): void
    {
        $table = "migrations";
        $sql = "CREATE TABLE IF NOT EXISTS $table (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration_name VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        $this->databaseDriver->statement($sql);
    }

    /**
     * Retrieves all migration files available in the migrations directory.
     *
     * @return array An array of migration file names.
     */
    private function getAllMigrations(): array
    {
        return glob("$this->migrationsDir/*.php");
    }

    /**
     * Logs a message for debugging or informational purposes.
     *
     * @param string $message The message to be logged.
     * @return void
     */
    private function log(string $message): void
    {
        if ($this->logProgress) {
            printf("%s\n", $message);
        }
    }
}
