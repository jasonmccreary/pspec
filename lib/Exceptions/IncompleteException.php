<?php namespace PSpec\Exceptions;

class IncompleteException extends Exception
{
    public function getCategory()
    {
        return 'Incomplete';
    }
}
