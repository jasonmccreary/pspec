<?php namespace PSpec\Core;

use PSpec\Blocks\Block;
use PSpec\Blocks\Describe;
use PSpec\Blocks\Methods\AfterHook;
use PSpec\Blocks\Methods\BeforeHook;
use PSpec\Blocks\Methods\ExpectMethod;
use PSpec\Blocks\Methods\TestMethod;
use PSpec\Exceptions\SkippedException;

/**
 * Enables the callback based "sugar" api to work the way it does. It maintains
 * and actually executes the methods defined in the global DSL in functions.php.
 */
class Builder
{
    // The global functions defined in functions.php delegate to
    // corresponding methods in the builder object. The syntactic sugar leans
    // on some clever tricks driven by the interaction of the Builder and the
    // InvocationContext.

    /**
     * Begins a fluent expectation using esperance. Invoked when the test is run
     * (as compared to constructed e.g. describe, before).
     */
    public static function expect($obj)
    {
        $expect_method = new ExpectMethod(
            InvocationContext::getActive(),
            function ($ctx) use (&$obj) {
                // Should, perhaps be configurable.
                return new \Esperance\Assertion($obj);
            }
        );

        $expect_method->closestTest()->addAssertion();

        return $expect_method->invoke();
    }

    /**
     * Marks the test skipped and throws a SkippedException.
     */
    public static function skip($message = '')
    {
        throw new SkippedException($message);
    }

    /**
     * Begins a new 'describe' block. The callback $fn is invoked when the test
     * suite is run.
     */
    public static function describe($name, $fn)
    {
        $next = new Describe(InvocationContext::getActive(), $fn, $name);
        $next->addToParent();

        return $next;
    }

    /**
     * Begins a new test case within the active block.
     */
    public static function it($name, $fn)
    {
        $active_block = InvocationContext::getAndAssertActiveBlock('PSpec\Blocks\Describe');
        $test_method = new TestMethod($active_block, $fn, $name);
        $test_method->addToParent();

        return $test_method;
    }

    /**
     * Adds a before callback to the active block. The active block should be
     * a describe block.
     */
    public static function before($fn)
    {
        $test_method = new BeforeHook(InvocationContext::getActive(), $fn);
        $test_method->addToParent();

        return $test_method;
    }

    public static function after($fn)
    {
        $test_method = new AfterHook(InvocationContext::getActive(), $fn);
        $test_method->addToParent();

        return $test_method;
    }

    /**
     * Takes care of our 'x' flag to skip any of the above methods.
     *
     * @return Block
     */
    public static function __callStatic($name, $arguments)
    {
        list($name, $skip) = self::getNameAndSkipFlag($name);

        $block = call_user_func_array(['static', $name], $arguments);

        if ($skip) {
            $block->skip();
        }

        return $block;
    }

    /**
     * Used to detect skipped versions of methods.
     *
     * @example
     * >>$this->getNameAndSkipFlag('xit');
     * array('it', true);
     *
     * >>$this->getNameAndSkipFlag('before');
     * array('before', false);
     *
     * @return a 2-tuple of a method name and skip flag.
     */
    protected static function getNameAndSkipFlag($name)
    {
        if ($name[0] == 'x') {
            return [substr($name, 1), true];
        }

        return [self::$method_map[$name], false];
    }
}
