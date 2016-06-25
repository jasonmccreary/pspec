<?php namespace PSpec\Core;

use PSpec\Blocks\Block;
use PSpec\Blocks\Methods\TestMethod;

class Result implements ResultComponent
{
    const INCOMPLETE = 'I';
    const SUCCESS    = 'S';
    const SKIPPED    = 'K';
    const FAILURE    = 'F';

    /**
     * @var Block $owning_block The block that created us.
     */
    protected $owning_block;

    /**
     * @var Block $owning_block The block that was invoked for the result.
     */
    protected $invoked_block;

    /**
     * @var int $status The status code for a test.
     *
     * 0 - failed
     * 1 - skipped
     * 2 - success
     */
    protected $status = null;

    /** @var mixed $result The return value or Exception raised by a test. */
    protected $result = null;

    public function __construct(Block $owning_block, Block $invoked_block, $status, $returned)
    {
        $this->owning_block  = $owning_block;
        $this->invoked_block = $invoked_block;
        $this->status        = $status;
        $this->returned      = $returned;
    }

    public function getBlock()
    {
        return $this->owning_block;
    }

    /**
     * E.g. a before method failure will be owned by it's triggering test. The
     * invoked block will still be the before method.
     */
    public function getInvokedBlock()
    {
        return $this->invoked_block;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getStatusString()
    {
        switch($this->status) {
            case Result::SUCCESS:
                return 'success';
            case Result::FAILURE:
                return 'failure';
            case Result::SKIPPED:
                return 'skipped';
            case Result::INCOMPLETE:
                return 'incomplete';
            default:
                return null;
        }
    }

    public function getReturned()
    {
        return $this->returned;
    }

    public function getException()
    {
        if ($this->returned instanceof \Exception) {
            return $this->returned;
        }

        return null;
    }

    public function isTestMethod()
    {
        return $this->invoked_block && ($this->invoked_block instanceof TestMethod);
    }

    public function totalTests()
    {
        return $this->isTestMethod() ? 1 : 0;
    }

    public function totalAssertions()
    {
        return $this->invoked_block->getAssertions();
    }

    public function totalFailures()
    {
        return $this->isFailure() ? 1 : 0;
    }

    public function totalIncomplete()
    {
        return $this->isIncomplete() ? 1 : 0;
    }

    public function totalSuccesses()
    {
        return $this->isSuccess() ? 1 : 0;
    }

    public function totalSkipped()
    {
        return $this->isSkipped() ? 1 : 0;
    }

    public function isSuccess()
    {
        return $this->status == static::SUCCESS;
    }

    public function isFailure()
    {
        return $this->status == static::FAILURE;
    }

    public function isSkipped()
    {
        return $this->status == static::SKIPPED;
    }

    public function isIncomplete()
    {
        return $this->invoked_block->getAssertions() == 0;
    }

    public function getFailures()
    {
        if ($this->isFailure()) {
            return [$this];
        }

        return [];
    }

    public function getWithFilter($fn)
    {
        if ($fn($this)) {
            return [$this];
        }

        return [];
    }
}
