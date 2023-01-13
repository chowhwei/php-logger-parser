<?php

namespace Chowhwei\PhpLoggerParser\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/*
#[AsCommand(
    name: 'app:create-user',
    description: 'Creates a new user.',
    hidden: false,
    aliases: ['app:add-user']
)]
*/
class TailCommand extends Command
{
    protected static $defaultDescription = 'Creates a new user.';

    // ...
    protected function configure()
    {
        $this
            // the command help shown when running the command with the "--help" option
            ->setHelp('This command allows you to create a user...')
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('hello world');
        return Command::SUCCESS;
    }
}