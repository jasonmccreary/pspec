<?php namespace PSpec\Blocks;

use PSpec\Core\Context;
use PSpec\Core\InvocationContext;

abstract class Block
{
    /** @var Callable $fn The method we're wrapping. */
    protected $fn;

    /** @var InvocationContext tracks our Block invocations. */
    protected $invocationContext;

    /** @var Context Contains additional test-specific state. */
    protected $context;

    /**
     * @var string $name The block name. Used in output to identify tests and
     * for filtering by strings.
     */
    protected $name;

    /** @var Block $parent The block within which this method was defined. */
    protected $parent;

    /** @var bool $skipped Whether this block was skipped during execution. */
    protected $skipped;

    /** @var bool $invoked Whether this method has been invoked. */
    protected $invoked = false;

    /** @var int $assertions The number of assertions within this block's immediate $fn. */
    protected $assertions = 0;

    /** @var Block[] Child block elements. */
    protected $children = [];

    public function __construct(InvocationContext $invocationContext, $fn = null, $name = null)
    {
        $this->invocationContext = $invocationContext;
        $this->parent = $invocationContext->activeBlock();
        $this->fn = $fn;
        $this->name = $name;
    }

    /**
     * Unless the Block has been skipped elsewhere, this marks the block as
     * skipped with the given message.
     *
     * @param string $message An optional skip message.
     *
     * @return Block $this
     */
    public function skip()
    {
        if ($this->skipped !== true) {
            $this->skipped = true;
        }

        return $this;
    }

    /**
     * Whether this Block has been marked for skipping.
     *
     * @return bool
     */
    public function isSkipped()
    {
        return $this->skipped;
    }

    /**
     * Whether this Block or any of it's ancestors have been marked skipped.
     *
     * @return bool
     */
    public function hasSkippedAncestors()
    {
        foreach ($this->ancestors() as $ancestor) {
            if ($ancestor->isSkipped()) {
                return true;
            }
        }

        return false;
    }

    public function createContext()
    {
        return $this->context = new Context($this);
    }

    public function getContext()
    {
        return $this->context;
    }

    /**
     * Returns an array of related contexts, in their intended call order.
     *
     * @return Context[]
     */
    public function getContextChain()
    {
        $block_chain = [];

        $this->traversePost(function ($block) use (&$block_chain) {
            $block_chain = array_merge($block_chain, $block->befores());
        });

        return array_filter(
            array_map(
                function ($block) {
                    return $block->getContext();
                },
                $block_chain
            )
        );
    }

    /**
     * Default external invocation method - calls the block originally passed into
     * the constructor along with a new context.
     */
    public function invoke()
    {
        return $this->invokeWithinContext($this->fn, [$this->createContext()]);
    }

    /**
     * Invokes $fn with $args while managing our internal invocation context
     * in order to ensure call graph is accurate.
     */
    public function invokeWithinContext($fn, $args = [])
    {
        $this->invocationContext->activate();
        $this->invocationContext->push($this);

        try {
            $result = call_user_func_array($fn, $args);
            $this->invocationContext->pop();
            $this->invocationContext->deactivate();
            return $result;
        } catch (\Exception $e) {
            $this->invocationContext->pop();
            $this->invocationContext->deactivate();
            throw $e;
        }
    }

    public function addAssertion()
    {
        ++$this->assertions;
    }

    public function getAssertions()
    {
        return $this->assertions;
    }

    /**
     * With no arguments, returns the complete path to this block down from it's
     * root ancestor.
     *
     * @param int $offset Used to array_slice the intermediate array before implosion.
     * @param int $length Used to array_slice the intermediate array before implosion.
     */
    public function path($offset = null, $length = null)
    {
        $ancestors = array_map(
            function ($ancestor) {
                return $ancestor->getName();
            },
            $this->ancestors()
        );

        $ancestors = array_slice(array_reverse($ancestors), $offset, $length);

        return implode(":", $ancestors);
    }

    public function getName()
    {
        return $this->name;
    }

    public function depth()
    {
        $total = 0;
        $block = $this;

        while ($block->parentBlock()) {
            $block = $block->parentBlock();
            $total++;
        }

        return $total;
    }

    public function ancestors()
    {
        $ancestors = [];
        $block = $this;

        while ($block) {
            $ancestors[] = $block;
            $block = $block->parentBlock();
        }

        return $ancestors;
    }

    public function parentBlock($parent_block = null)
    {
        if (func_num_args()) {
            $this->parent = $parent_block;
            return $this;
        }

        return $this->parent;
    }

    public function addToParent()
    {
        if ($this->parent) {
            $this->parent->addChild($this);
        }
    }

    public function traversePost($fn)
    {
        if ($parent_block = $this->parentBlock()) {
            $parent_block->traversePost($fn);
        }

        $fn($this);
    }

    public function traversePre($fn)
    {
        $fn($this);

        if ($parent_block = $this->parentBlock()) {
            $parent_block->traversePre($fn);
        }
    }

    public function closest($class_name)
    {
        foreach ($this->ancestors() as $ancestor) {
            if (is_a($ancestor, $class_name)) {
                return $ancestor;
            }
        }

        return null;
    }

    public function closestTest()
    {
        return $this->closest(\PSpec\Blocks\Methods\Example::class);
    }

    public function closestSuite()
    {
        return $this->closest(\PSpec\Blocks\Suite::class);
    }

    public function addChild(Block $block)
    {
        $type = get_class($block);
        if (!isset($this->children[$type])) {
            $this->children[$type] = [];
        }

        $this->children[$type][] = $block;
    }

    public function children($of_type)
    {
        if (!isset($this->children[$of_type])) {
            $this->children[$of_type] = [];
        }

        return $this->children[$of_type];
    }

    public function tests()
    {
        return $this->children(\PSpec\Blocks\Methods\Example::class);
    }

    public function describes()
    {
        return $this->children(\PSpec\Blocks\Describe::class);
    }

    public function afters()
    {
        return $this->children(\PSpec\Blocks\Methods\AfterHook::class);
    }

    public function befores()
    {
        return $this->children(\PSpec\Blocks\Methods\BeforeHook::class);
    }
}
