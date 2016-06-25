<?php

if (!function_exists('it')) {
    function it()
    {
        return call_user_func_array(
            ['\PSpec\Core\Builder', 'it'],
            func_get_args()
        );
    }
}

if (!function_exists('xit')) {
    function xit()
    {
        return call_user_func_array(
            ['\PSpec\Core\Builder', 'xit'],
            func_get_args()
        );
    }
}
if (!function_exists('before')) {
    function before()
    {
        return call_user_func_array(
            ['\PSpec\Core\Builder', 'before'],
            func_get_args()
        );
    }
}
if (!function_exists('xbefore')) {
    function xbefore()
    {
        return call_user_func_array(
            ['\PSpec\Core\Builder', 'xbefore'],
            func_get_args()
        );
    }
}

if (!function_exists('after')) {
    function after()
    {
        return call_user_func_array(
            ['\PSpec\Core\Builder', 'after'],
            func_get_args()
        );
    }
}

if (!function_exists('xafter')) {
    function xafter()
    {
        return call_user_func_array(
            ['\PSpec\Core\Builder', 'xafter'],
            func_get_args()
        );
    }
}

if (!function_exists('describe')) {
    function describe()
    {
        return call_user_func_array(
            ['\PSpec\Core\Builder', 'describe'],
            func_get_args()
        );
    }
}

if (!function_exists('xdescribe')) {
    function xdescribe()
    {
        return call_user_func_array(
            ['\PSpec\Core\Builder', 'xdescribe'],
            func_get_args()
        );
    }
}

if (!function_exists('context')) {
    function context()
    {
        return call_user_func_array(
            ['\PSpec\Core\Builder', 'describe'],
            func_get_args()
        );
    }
}

if (!function_exists('xcontext')) {
    function xcontext()
    {
        return call_user_func_array(
            ['\PSpec\Core\Builder', 'xdescribe'],
            func_get_args()
        );
    }
}

if (!function_exists('expect')) {
    function expect()
    {
        return call_user_func_array(
            ['\PSpec\Core\Builder', 'expect'],
            func_get_args()
        );
    }
}
