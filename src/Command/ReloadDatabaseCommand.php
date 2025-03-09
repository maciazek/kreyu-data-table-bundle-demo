<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:reload-database',
    description: 'Drops and creates schema, then seeds database',
)]
class ReloadDatabaseCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $commands = [
            [
                'command' => $this->getApplication()->find('doctrine:schema:drop'),
                'arguments' => [
                    '--full-database' => true,
                    '--force' => true,
                ],
            ],
            [
                'command' => $this->getApplication()->find('doctrine:schema:update'),
                'arguments' => [
                    '--force' => true,
                ],
            ],
            [
                'command' => $this->getApplication()->find('app:seed-database'),
                'arguments' => [
                    '--no-debug' => true,
                ],
            ],
        ];

        foreach ($commands as $command) {
            $arrayInput = new ArrayInput($command['arguments']);
            $arrayInput->setInteractive(false);
            $command['command']->run($arrayInput, $output);
        }

        $io->success('Database reloaded successfully!');

        return Command::SUCCESS;
    }
}
