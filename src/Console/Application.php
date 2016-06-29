<?php
namespace PSpec\Console;

use PSpec\Console\Commands\PSpec;
use Symfony\Component\Console\Input\InputInterface;

class Application extends \Symfony\Component\Console\Application
{
    protected function getCommandName(InputInterface $input)
    {
        return 'pspec';
    }

    protected function getDefaultCommands()
    {
        $defaultCommands = parent::getDefaultCommands();
        $defaultCommands[] = new PSpec();

        return $defaultCommands;
    }

    public function getDefinition()
    {
        $inputDefinition = parent::getDefinition();
        $inputDefinition->setArguments();

        return $inputDefinition;
    }
}