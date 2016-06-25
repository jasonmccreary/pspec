<?php namespace PSpec\Exceptions;

class Exception extends \Exception
{
    public function getCategory()
    {
        return 'Exception';
    }

    public function originalTrace()
    {
        if ($previous = $this->getPrevious()) {
            return $previous->getTrace();
        }

        return $this->getTrace();
    }
}
