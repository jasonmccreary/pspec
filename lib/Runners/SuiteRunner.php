<?php namespace PSpec\Runners;

use PSpec\Blocks\Block;
use PSpec\Blocks\Describe;
use PSpec\Blocks\Methods\Example;
use PSpec\Blocks\Suite;
use PSpec\Core\Result;
use PSpec\Core\ResultSet;
use PSpec\Exceptions\AssertionException;
use PSpec\Exceptions\Exception as PSpecException;
use PSpec\Exceptions\SkippedException;

/**
 * Responsible for running a Suite and it's nested Describes, TestMethods, Before\After
 * Hooks and so on.
 *
 * It's a fairly top-down approach - an alternative might be to have TestMethods
 * know how to run themselves. However, there's a lot of machinery around
 * executing a test such as:
 *
 * 1. Invoking before/after hooks for each method.
 * 2. Wrapping both the hooks and test method execution in our captureResult
 *    method.
 * 3. Printing results in a somewhat granular fashion (start / complete events).
 *
 * That would mix the responsibilities of our blocks.
 */
class SuiteRunner extends Runner
{
    protected $suite;

    public function __construct(Suite $suite, ResultSet $result_set)
    {
        $this->suite = $suite;
        $this->result_set = $result_set;
    }

    /**
     * Runs the Suite from start to finish.
     */
    public function run()
    {
        $this->emit(
            'suite.start',
            [
                'suite' => $this->suite,
                'result_set' => $this->result_set
            ]
        );

        $result = $this->captureAround([$this, 'runGroup'], $this->suite, $this->suite);

        $this->emit(
            'suite.complete',
            [
                'suite' => $this->suite,
                'result' => $result,
                'result_set' => $this->result_set
            ]
        );

        if ($result->isFailure()) {
            $this->result_set->addResult($result);
        }
    }

    // Nested Blocks and Tests
    // #######################

    protected function runDescribe(Describe $describe)
    {
        $this->emit(
            'describe.start',
            [
                'describe' => $describe,
                'result_set' => $this->result_set
            ]
        );

        $result = $this->captureAround([$this, 'runGroup'], $describe, $describe);

        $this->emit(
            'describe.complete',
            [
                'describe' => $describe,
                'result' => $result,
                'result_set' => $this->result_set
            ]
        );

        if ($result->isFailure()) {
            $this->result_set->addResult($result);
        }
    }

    protected function runGroup(Block $block)
    {
        foreach ($block->tests() as $test) {
            $this->runTest($test);
        }

        foreach ($block->describes() as $describe) {
            $this->runDescribe($describe);
        }
    }

    protected function runTest(Example $test)
    {
        $start_context = [
            'test' => $test,
            'result_set' => $this->result_set
        ];

        $this->emit('test.start', $start_context);


        $suite_runner = $this;
        $test_result_set = new ResultSet();
        $test->aroundEach(function ($block) use ($suite_runner, $test_result_set, $test) {
            if ($test_result_set->isFailure()) {
                $block->skip();
            }

            $result = $suite_runner->captureAround([$block, 'invoke'], $test, $block);

            $test_result_set->addResult($result);
        });

        $this->result_set->addResult($test_result_set);

        $complete_context = [
            'test' => $test,
            'result' => $test_result_set,
            'result_set' => $this->result_set
        ];

        $this->emit('test.complete', $complete_context);

        return $test_result_set;
    }

    /**
     * @param $owner The Block 'owns' the result of $fn(). E.g. a TestMethod owns
     * the results from all of it's before and after hooks.
     *
     * public because @bindshim
     *
     * @return Result
     */
    public function captureAround($fn, Block $owner, Block $invoked)
    {
        try {
            $return_value = call_user_func($fn, $owner, $invoked);
            $status = Result::SUCCESS;
        } catch (EsperanceError $e) {
            $status = Result::FAILURE;
            $return_value = new AssertionException($e->getMessage(), $e->getCode(), $e);
        } catch (SkippedException $e) {
            $status = Result::SKIPPED;
            $return_value = $e;
        } catch (\Exception $e) {
            $status = Result::FAILURE;
            $return_value = new PSpecException($e->getMessage(), $e->getCode(), $e);
        }

        return new Result($owner, $invoked, $status, $return_value);
    }
}
