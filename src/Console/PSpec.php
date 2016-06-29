<?php
namespace PSpec\Console;

use PSpec\Console\Commands\Test;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;

class PSpec extends Application
{
    protected function getCommandName(InputInterface $input)
    {
        return 'test';
    }

    protected function getDefaultCommands()
    {
        $defaultCommands = parent::getDefaultCommands();
        $defaultCommands[] = new Test();

        return $defaultCommands;
    }

    public function getDefinition()
    {
        $inputDefinition = parent::getDefinition();
        $inputDefinition->setArguments();

        return $inputDefinition;
    }
}