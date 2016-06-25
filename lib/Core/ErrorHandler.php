<?php namespace PSpec\Core;

class ErrorHandler
{
    public function __construct($options = [])
    {
        $default_options = [
            'error_reporting' => error_reporting(),
            'error_class' => \PSpec\Exceptions\Error::class
        ];

        $final_options = array_merge($default_options, $options);

        $this->error_reporting = $final_options['error_reporting'];
        $this->error_class     = $final_options['error_class'];
    }

    public function handleError($errno, $errstr, $errfile, $errline)
    {
        if ($errno & $this->error_reporting === 0) {
            return false;
        }

        $error_class = $this->error_class;

        throw new $error_class($errno, $errstr, $errfile, $errline, debug_backtrace());
    }
}
