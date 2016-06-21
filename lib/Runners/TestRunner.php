<?php namespace Matura\Runners;

use ArrayIterator;
use FilesystemIterator;
use Matura\Blocks\Suite;
use Matura\Core\InvocationContext;
use Matura\Core\ResultSet;
use Matura\Filters\Defaults;
use Matura\Filters\FilePathIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * Responsible for invoking files, Suites, and TestMethods.
 *
 * The test envirojnment is set up mostly in #run() where we register our
 * error handler and load our DSL.
 */
class TestRunner extends Runner
{
    protected $options = [
        'include' => Defaults::MATCH_TEST,
        'exclude' => Defaults::MATCH_NONE,
        'grep'    => Defaults::MATCH_ALL
    ];

    /** @var The directory or folder containing our test file(s). */
    protected $path;

    public function __construct($path, $options = [])
    {
        $this->path = $path;
        $this->options = array_merge($this->options, $options);
        $this->result_set = new ResultSet();
    }

    /**
     * Recursively obtains all test files under `$this->path` and returns
     * the filtered result after applying our filtering regex.
     *
     * @return Iterator
     */
    public function collectFiles()
    {
        if (is_dir($this->path)) {
            $directory = new RecursiveDirectoryIterator($this->path, FilesystemIterator::SKIP_DOTS);
            $iterator = new RecursiveIteratorIterator($directory);
            return new FilePathIterator($iterator, $this->options['include'], $this->options['exclude']);
        } else {
            return new ArrayIterator(array(new SplFileInfo($this->path)));
        }
    }

    /**
     * Bootstraps parts of our test enviornment and iteratively invokes each
     * file.
     *
     * @return ResultSet
     */
    public function run()
    {
        $tests = $this->collectFiles();

        $this->emit('test_run.start');

        foreach ($tests as $test_file) {
            $suite = new Suite(
                new InvocationContext(),
                function () use ($test_file) {
                    require $test_file;
                },
                $test_file->getPathName()
            );

            $suite->build();

            $suite_result = new ResultSet();
            $suite_runner = new SuiteRunner($suite, $suite_result, [
                'grep' => $this->options['grep']
            ]);
            $this->result_set->addResult($suite_result);
            
            foreach ($this->listeners as $listener) {
                $suite_runner->addListener($listener);
            }

            $suite_runner->run();
        }

        $this->emit('test_run.complete', ['result_set' => $this->result_set]);

        return $this->result_set;
    }
}
