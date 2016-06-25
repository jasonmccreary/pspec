<?php namespace PSpec\Exceptions;

class SkippedException extends Exception
{
    public function getCategory()
    {
        return 'Skipped';
    }
}
