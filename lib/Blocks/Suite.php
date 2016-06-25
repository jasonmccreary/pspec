<?php namespace PSpec\Blocks;

use PSpec\Core\SuiteRunner;

class Suite extends Describe
{
    public function build()
    {
        $builder_for = function (Block $block) use (&$builder_for) {
            return function () use ($block, $builder_for) {
                $block->invoke();

                foreach ($block->describes() as $describe) {
                    $describe->invokeWithinContext($builder_for($describe));
                }
            };
        };

        return $this->invokeWithinContext($builder_for($this));
    }
}
