<?php namespace PSpec\Blocks\Methods;

use PSpec\Core\InvocationContext;
use PSpec\Exceptions\SkippedException;

class Example extends Method
{
    public function __construct($name, \Closure $closure)
    {
        $invocationContext = InvocationContext::getAndAssertActiveBlock(\PSpec\Blocks\Describe::class);
        parent::__construct($invocationContext, $closure, $name);
    }

    public function collectOrderedBefores()
    {
        $befores = [];
        $this->traversePost(function ($block) use (&$befores) {
            $befores = array_merge($befores, $block->befores());
        });

        return $befores;
    }

    public function collectOrderedAfters()
    {
        $afters = [];
        $this->traversePre(function ($block) use (&$afters) {
            $afters = array_merge($afters, $block->afters());
        });

        return $afters;
    }

    public function aroundEach($fn)
    {
        foreach (array_merge(
            $this->collectOrderedBefores(),
            [$this],
            $this->collectOrderedAfters()
        ) as $block) {
            $fn($block);
        }
    }

    public function invoke()
    {
        if ($this->hasSkippedAncestors()) {
            return $this->invokeWithinContext(
                function () {
                    throw new SkippedException();
                },
                [$this->createContext()]
            );
        }

        return $this->invokeWithinContext($this->fn, [$this->createContext()]);
    }
}
