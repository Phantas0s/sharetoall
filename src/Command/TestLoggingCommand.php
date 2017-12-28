<?php

namespace App\Command;

use App\Traits\LoggerTrait;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestLoggingCommand extends CommandAbstract
{
    use LoggerTrait;

    protected function configure()
    {
        $this->setName('test:logging')
            ->setDescription('Test logging provider')
            ->addArgument('log-message', InputArgument::REQUIRED, 'Log message');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $message = $input->getArgument('log-message');
        if (empty($message)) {
            $this->logAlert("No message to test logging with.");
        }

        $this->log(LogLevel::ALERT, $message);
        $this->log(LogLevel::CRITICAL, $message);
        $this->log(LogLevel::DEBUG, $message);
        $this->log(LogLevel::EMERGENCY, $message);
        $this->log(LogLevel::ERROR, $message);
        $this->log(LogLevel::INFO, $message);
        $this->log(LogLevel::NOTICE, $message);
        $this->log(LogLevel::WARNING, $message);

        $output->writeln("Wrote logline ... ");
    }
}
