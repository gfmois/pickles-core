<?php

require_once './vendor/autoload.php';

use Pickles\Database\Migrations\Migrator;

// This file is part of the Pickles framework.
// This file will be used to generate migrations for the Pickles framework.

// If number of arguments is less than 2, show usage
// and exit with error code 1
if (count($argv) < 2) {
    echo "Usage: php pickles.php <command>\n";
    exit(1);
}

// Recover the command from the arguments
$command = $argv[1];

// Command to lowercase
$command = strtolower($command);

if ($command == '') {
    echo "Invalid command\n";
    exit(1);
}

$migrator = new Migrator(
    migrationsDir: __DIR__ . "/database/migrations",
    templateDir: __DIR__ . "/templates"
);

if ($command == "make:migration") {
    $migrationName = $argv[2] ?? null;
    if ($migrationName === null) {
        echo "Usage: php pickles.php make:migration <migration_name>\n";
        exit(1);
    }

    echo "Creating migration file for $migrationName...\n";
    $migrator->make($migrationName);
} else {
    echo "Unknown command: $command\n";
    exit(1);
}