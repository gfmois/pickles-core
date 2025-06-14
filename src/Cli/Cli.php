<?php

namespace Pickles\Cli;

use Constants;
use Dotenv\Dotenv;
use Pickles\Cli\Commands\MakeMigration;
use Pickles\Cli\Commands\Migrate;
use Pickles\Cli\Commands\MigrationRollback;
use Pickles\Cli\Commands\Serve;
use Pickles\Config\Config;
use Pickles\Database\Drivers\DatabaseDriver;
use Pickles\Database\Migrations\Migrator;
use Pickles\Kernel;
use Symfony\Component\Console\Application;

/**
 * Class Cli
 *
 * Handles command-line interface (CLI) operations for the Pickles Framework.
 * Provides methods and properties to interact with and manage CLI commands,
 * arguments, and output.
 */
class Cli
{
    /**
     * Bootstraps the CLI application by initializing core components and services.
     *
     * This method performs the following actions:
     * - Sets the application root directory.
     * - Loads environment variables from a `.env` file in the root directory.
     * - Loads configuration files from the `config` directory.
     * - Registers CLI service providers defined in the configuration.
     * - Retrieves and validates the database driver instance.
     * - Establishes a database connection using configuration values.
     * - Registers a singleton instance of the `Migrator` class for database migrations.
     *
     * @param string $root The root directory of the application.
     * @return self Returns an instance of the CLI application.
     * @throws \Exception If the database driver instance is invalid.
     */
    public static function bootstrap(string $root): self
    {
        Kernel::$root = $root;
        Dotenv::createImmutable($root)->load();
        Config::load($root . '/config');

        foreach (config("providers." . Constants::CLI_PROVIDERS) as $provider) {
            (new $provider())->registerServices();
        }

        $dbInstance = app(DatabaseDriver::class);
        if (!$dbInstance instanceof DatabaseDriver || $dbInstance === null) {
            throw new \Exception("Database driver instance is not valid.");
        }

        $dbInstance->connect(
            config(Constants::DATABASE_PROTOCOL),
            config(Constants::DATABASE_HOST),
            config(Constants::DATABASE_PORT),
            config(Constants::DATABASE_USERNAME),
            config(Constants::DATABASE_PASSWORD),
            config(Constants::DATABASE_DATABASE),
        );

        singleton(
            Migrator::class,
            fn () => new Migrator(
                "$root/database/migrations",
                resourcesDirectory() . "/templates",
                $dbInstance
            )
        );

        return new self();
    }

    /**
     * Runs the CLI application.
     *
     * Initializes a new CLI application instance with the configured application name and version.
     * Registers the available CLI commands: MakeMigration, Migrate, and MigrationRollback.
     * Executes the CLI application, handling user input and command execution.
     *
     * @return void
     */
    public function run(): void
    {
        $cli = new Application(
            config(Constants::APP_NAME, "Pickles CLI"),
            config(Constants::APP_VERSION, "1.0.0")
        );

        $cli->addCommands([
            new MakeMigration(),
            new Migrate(),
            new MigrationRollback(),
            new Serve()
        ]);

        $cli->run();
    }
}
