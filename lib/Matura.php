<?php namespace Matura;

use Matura\Core\Builder;
use Matura\Core\ErrorHandler;
use Matura\Core\TestContext;

/**
 * This classes specific role is unclear at the moment. It mostly manages DSL
 * generation.
 */
class Matura
{
    /** @var Builder $builder The active builder object. */
    protected static $error_handler;

    /** @var string[] $method_names The method names we magically support in or
     *  DSL.
     */
    protected static $method_names = [
        'it',
        'xit',
        'before_all',
        'xbefore_all',
        'before',
        'xbefore',
        'after',
        'xafter',
        'after_all',
        'xafter_all',
        'describe',
        'xdescribe',
        'context',
        'xcontext',
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
