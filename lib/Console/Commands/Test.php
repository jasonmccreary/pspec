<?php namespace PSpec\Console\Commands;

use PSpec\Console\Output\Printer;
use PSpec\Core\ErrorHandler;
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
            ->setDescription('Run specs')
            ->addArgument(
                'path',
                InputArgument::OPTIONAL,
                'The path to a spec file or directory containing specs.',
                'specs'
            )
            ->addOption(
                'filter',
                null,
                InputOption::VALUE_REQUIRED,
                'Filter individual spec files by name.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path');

        $options = [];

        $filter = $input->getOption('filter');
        if ($filter) {
            $options['filter'] = "/$filter/i";
        }

        $printer = new Printer($output);
        $test_runner = new TestRunner($path, $options);
        $test_runner->addListener($printer);

        $error_handler = new ErrorHandler();
        set_error_handler([$error_handler, 'handleError']);
        $exit_code = $test_runner->run()->isSuccess() ? 0 : 1;
        restore_error_handler();

        return $exit_code;
    }
}
