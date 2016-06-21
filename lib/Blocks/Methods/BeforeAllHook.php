<?php namespace Matura\Blocks\Methods;

class BeforeAllHook extends HookMethod
{
    protected $name = 'before all';

    protected $result;
    protected $invoked;

    public function invoke()
    {
        if ($this->invoked) {
            return $this->result;
        } else {
            $this->result = $this->invokeWithin($this->fn, [$this->createContext()]);
            $this->invoked = true;
            return $this->result;
        }
    }

    public function createContext()
    {
        if ($this->context) {
            return $this->context;
        } else {
            return parent::createContext();
        }
    }
}
