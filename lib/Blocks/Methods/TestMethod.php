<?php namespace Matura\Blocks\Methods;

use Matura\Exceptions\Exception;
use Matura\Blocks\Describe;

class TestMethod extends Method
{
    public function invokeAll()
    {
        return $this->traverseMethods(function($block) {
            $block->invoke();
        });
    }

    /**
     * Traverses this TestMethod and it's hooks, passing along each TestMethod
     * in the order it is encountered to the provided $cb.
     *
     * There is a deliberate defined ordering here. Suppose our test case is
     * structured like so:
     *
     * describe('User', ...)
     *    describe('Model', ...)
     *      it('should save', ...)
     *
     * If each describe block has one of each hook, then sequence of functions
     * called will look like:
     *
     * 'User::before_all', 'User::before', 'Model::before_all', 'Model::before'
     * 'should save'
     * 'Model::after', 'Model::after_all', 'User::after', 'User::after_all'
     *
     * @param Callable $cb A function to invoke with each acquired TestMethod or
     *  HookMethod.
     * @return mixed The return value of invoking this Method's function.
     */
    public function traverseMethods($cb)
    {
        if (!$this->parent_block instanceof Describe) {
            throw new Exception($this->path().' was not created in a Describe block.');
        }

        $this->parent_block->traversePost(
            function ($block) use ($cb) {
                foreach ($block->beforeAlls() as $once_before) {
                    $cb($once_before);
                }

                foreach ($block->befores() as $before) {
                    $cb($before);
                }
            }
        );

        $return_value = $cb($this);

        $this->parent_block->traversePre(
            function ($block) use ($cb) {
                foreach ($block->afters() as $after) {
                    $cb($after);
                }

                foreach ($block->afterAlls() as $once_after) {
                    $cb($once_after);
                }
            }
        );

        return $return_value;
    }
}
