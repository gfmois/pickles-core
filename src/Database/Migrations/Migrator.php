<?php

namespace Pickles\Database\Migrations;

class Migrator
{
    public function __construct(
        private string $migrationsDir,
        private string $templateDir,
    ) {
        $this->migrationsDir = $migrationsDir;
        $this->templateDir = $templateDir;

        $this->ensureDirectoryExists($this->migrationsDir);
    }

    public function make(string $migrationName): void
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

        $filePath = $this->generateMigrationFile($migrationName, $template);
        echo "Migration file created at $filePath\n";
    }

    private function getTableName(string $migrationName, string $regex, int $group = 1): string
    {
        return preg_replace_callback($regex, fn ($matches) => $matches[$group], $migrationName);
    }

    private function generateMigrationFile(string $migrationName, string $template): string
    {
        $date = date('Y_m_d_');
        $id = $this->getIdForMigrationFile($date);

        $fileName = sprintf("%s_%06d_%s.php", $date, $id, $migrationName);
        $path = "$this->migrationsDir/$fileName";

        file_put_contents($path, $template);

        return $path;
    }

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

    private function ensureDirectoryExists(string $dir): void
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}
