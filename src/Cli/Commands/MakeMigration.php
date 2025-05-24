<?php

namespace Pickles\Cli\Commands;

use Pickles\Database\Migrations\Migrator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command class responsible for generating new migration files.
 *
 * This class provides the functionality to create migration files
 * via the CLI, allowing developers to manage database schema changes.
 *
 * @package PicklesFramework\Cli\Commands
 */
class MakeMigration extends Command
{
    protected static $defaultName = 'make:migration';
    protected static $defaultDescription = 'Create a new migration file in the database/migrations directory';

    public function __construct()
    {
        parent::__construct('make:migration');
    }

    protected function configure()
    {
        $this->addArgument("name", InputArgument::REQUIRED, "The name of the migration file")
            ->setHelp("This command allows you to create a new migration file in the database/migrations directory.");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        app(Migrator::class)->make($input->getArgument("name"));
        return self::SUCCESS;
    }
}
