<?php

namespace Pickles\Cli\Commands;

use Pickles\Kernel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Serve extends Command
{
    protected static $defaultName = "serve";

    protected static $defaultDescription = "Run Lune development application";

    public function __construct()
    {
        parent::__construct('serve');
    }

    protected function configure()
    {
        $this
            ->addOption("host", null, InputOption::VALUE_OPTIONAL, "Host address to run on", "127.0.0.1")
            ->addOption("port", null, InputOption::VALUE_OPTIONAL, "Port to run on", "8080");
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $host = $input->getOption("host");
        $port = $input->getOption("port");
        $dir = Kernel::$root . "/public";

        $output->writeln("<info>Starting development server on $host:$port</info>");
        shell_exec("php -S $host:$port $dir/index.php");

        return Command::SUCCESS;
    }
}
