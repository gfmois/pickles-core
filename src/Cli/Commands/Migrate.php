<?php

namespace Pickles\Cli\Commands;

use Exception;
use Pickles\Database\Migrations\Migrator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Handles database migration commands for the CLI.
 *
 * This command provides functionality to manage and execute database migrations
 * within the Pickles Framework. It extends the base Command class to integrate
 * with the CLI command system.
 */
class Migrate extends Command
{
    protected static $defaultName = 'migrate';
    protected static $defaultDescription = 'Run all pending migrations';

    public function __construct()
    {
        parent::__construct('migrate');
    }

    protected function configure()
    {
        $this->setHelp("This command allows you to run all pending migrations in the database/migrations directory.");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            app(Migrator::class)->migrate();
            return self::SUCCESS;
        } catch (Exception $e) {
            $output->writeln("<error>Error: {$e->getMessage()}</error>");
            $output->writeln($e->getTraceAsString());
            return self::FAILURE;
        }
    }
}
