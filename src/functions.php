<?php

if (!function_exists('it')) {
    function it()
    {
        return call_user_func_array(
            [\PSpec\Core\Builder::class, 'it'],
            func_get_args()
        );
    }
}

if (!function_exists('xit')) {
    function xit()
    {
        return call_user_func_array(
            [\PSpec\Core\Builder::class, 'xit'],
            func_get_args()
        );
    }
}
if (!function_exists('before')) {
    function before()
    {
        return call_user_func_array(
            [\PSpec\Core\Builder::class, 'before'],
            func_get_args()
        );
    }
}
if (!function_exists('xbefore')) {
    function xbefore()
    {
        return call_user_func_array(
            [\PSpec\Core\Builder::class, 'xbefore'],
            func_get_args()
        );
    }
}

if (!function_exists('after')) {
    function after()
    {
        return call_user_func_array(
            [\PSpec\Core\Builder::class, 'after'],
            func_get_args()
        );
    }
}

if (!function_exists('xafter')) {
    function xafter()
    {
        return call_user_func_array(
            [\PSpec\Core\Builder::class, 'xafter'],
            func_get_args()
        );
    }
}

if (!function_exists('describe')) {
    function describe()
    {
        return call_user_func_array(
            [\PSpec\Core\Builder::class, 'describe'],
            func_get_args()
        );
    }
}

if (!function_exists('xdescribe')) {
    function xdescribe()
    {
        return call_user_func_array(
            [\PSpec\Core\Builder::class, 'xdescribe'],
            func_get_args()
        );
    }
}

if (!function_exists('context')) {
    function context()
    {
        return call_user_func_array(
            [\PSpec\Core\Builder::class, 'describe'],
            func_get_args()
        );
    }
}

if (!function_exists('xcontext')) {
    function xcontext()
    {
        return call_user_func_array(
            [\PSpec\Core\Builder::class, 'xdescribe'],
            func_get_args()
        );
    }
}

