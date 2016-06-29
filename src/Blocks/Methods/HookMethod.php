<?php namespace PSpec\Blocks\Methods;

use PSpec\Core\InvocationContext;

class HookMethod extends Method
{
    public function __construct(\Closure $closure)
    {
        parent::__construct(InvocationContext::getActive(), $closure, $this->name);
    }
}
