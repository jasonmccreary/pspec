<?php namespace PSpec\Blocks\Methods;

use PSpec\Blocks\Block;

abstract class Method extends Block
{
    /**
     * Allows Method Block to act as callables.
     */
    public function __invoke($arguments)
    {
        return $this->invoke();
    }
}
