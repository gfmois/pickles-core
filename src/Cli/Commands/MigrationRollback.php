<?php

namespace Pickles\Cli\Commands;

use Exception;
use Pickles\Database\Migrations\Migrator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Handles the rollback of database migrations via the CLI.
 *
 * This command allows users to revert the most recent migration or a specified number of migrations.
 * It is typically used to undo changes made by previous migration commands.
 *
 * @package PicklesFramework\Cli\Commands
 */
class MigrationRollback extends Command
{
    protected static $defaultName = 'migration:rollback';
    protected static $defaultDescription = 'Rollback the migrations all migrations back (or n using --n option)';
    protected static $defaultHelp = 'This command allows you to rollback all migrations in the database/migrations directory.';

    public function __construct()
    {
        parent::__construct('migration:rollback');
    }

    protected function configure()
    {
        $this->addArgument('steps', InputArgument::OPTIONAL, 'Number of migrations to rollback', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $steps = $input->getArgument('steps');
            if ($steps != null) {
                if (!is_numeric($steps) || $steps < 1) {
                    throw new Exception("The steps argument must be a positive integer.");
                }
                $steps = (int)$steps;
            }

            app(Migrator::class)->rollback($steps);
            return self::SUCCESS;
        } catch (Exception $e) {
            $output->writeln("<error>Error: {$e->getMessage()}</error>");
            $output->writeln($e->getTraceAsString());
            return self::FAILURE;
        }
    }
}
