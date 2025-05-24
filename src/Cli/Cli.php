<?php

namespace Pickles\Cli;

use Constants;
use Dotenv\Dotenv;
use Pickles\Cli\Commands\MakeMigration;
use Pickles\Cli\Commands\Migrate;
use Pickles\Cli\Commands\MigrationRollback;
use Pickles\Config\Config;
use Pickles\Database\Drivers\DatabaseDriver;
use Pickles\Database\Migrations\Migrator;
use Pickles\Kernel;
use Symfony\Component\Console\Application;

class Cli
{
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
        ]);

        $cli->run();
    }
}
