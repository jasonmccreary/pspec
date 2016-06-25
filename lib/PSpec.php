<?php namespace PSpec;

use PSpec\Core\Builder;
use PSpec\Core\ErrorHandler;

/**
 * This classes specific role is unclear at the moment. It mostly manages DSL
 * generation.
 */
class PSpec
{
    /** @var Builder $builder The active builder object. */
    protected static $error_handler;

    /** @var string[] $method_names The method names we magically support in or
     *  DSL.
     */
    protected static $method_names = [
        'before',
        'xbefore',
        'after',
        'xafter',
        'describe',
        'xdescribe',
        'context',
        'xcontext',
        'it',
        'xit',
        'expect',
        'skip'
    ];

    public static function loadDSL()
    {
        require_once __DIR__ . '/functions.php';
    }

    public static function init()
    {
        $error_handler = new ErrorHandler();
        set_error_handler([$error_handler, 'handleError']);
        static::loadDSL();
    }

    public static function cleanup()
    {
        restore_error_handler();
    }
}
