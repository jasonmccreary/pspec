<?php namespace PSpec\Console\Commands;

use PSpec\Console\Output\Printer;
use PSpec\PSpec;
use PSpec\Runners\TestRunner;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Test extends Command
{
    protected function configure()
    {
        $this
            ->setName('test')
            ->setDescription('Run tests')
            ->addArgument(
                'path',
                InputArgument::OPTIONAL,
                'The path to the file or directory to test.',
                'test'
            )
            ->addOption(
                'filter',
                null,
                InputOption::VALUE_REQUIRED,
                'Filter individual test cases by a description regexp.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $printer = new Printer($output);

        $path = $input->getArgument('path');

        $options = [];

        $filter = $input->getOption('filter');
        if ($filter) {
            $options['filter'] = "/$filter/i";
        }

        $test_runner = new TestRunner($path, $options);
        $test_runner->addListener($printer);

        PSpec::init();
        $code = $test_runner->run()->isSuccessful() ? 0 : 1;
        PSpec::cleanup();

        return $code;
    }
}
