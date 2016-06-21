<?php namespace Matura\Blocks\Methods;

use Matura\Exceptions\SkippedException;

class TestMethod extends Method
{
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
            return $this->invokeWithin(
                function () {
                    throw new SkippedException();
                },
                [$this->createContext()]
            );
        } else {
            return $this->invokeWithin($this->fn, [$this->createContext()]);
        }
    }
}
