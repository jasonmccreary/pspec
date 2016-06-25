<?php
namespace  {

    function it()
    {
        return call_user_func_array(
            ['\PSpec\Core\Builder','it'],
            func_get_args()
        );
    }

    function xit()
    {
        return call_user_func_array(
            ['\PSpec\Core\Builder','xit'],
            func_get_args()
        );
    }

    function before()
    {
        return call_user_func_array(
            ['\PSpec\Core\Builder','before'],
            func_get_args()
        );
    }

    function xbefore()
    {
        return call_user_func_array(
            ['\PSpec\Core\Builder','xbefore'],
            func_get_args()
        );
    }

    function after()
    {
        return call_user_func_array(
            ['\PSpec\Core\Builder','after'],
            func_get_args()
        );
    }

    function xafter()
    {
        return call_user_func_array(
            ['\PSpec\Core\Builder','xafter'],
            func_get_args()
        );
    }

    function describe()
    {
        return call_user_func_array(
            ['\PSpec\Core\Builder','describe'],
            func_get_args()
        );
    }

    function xdescribe()
    {
        return call_user_func_array(
            ['\PSpec\Core\Builder','xdescribe'],
            func_get_args()
        );
    }

    function context()
    {
        return call_user_func_array(
            ['\PSpec\Core\Builder','describe'],
            func_get_args()
        );
    }

    function xcontext()
    {
        return call_user_func_array(
            ['\PSpec\Core\Builder','xdescribe'],
            func_get_args()
        );
    }

    function expect()
    {
        return call_user_func_array(
            ['\PSpec\Core\Builder','expect'],
            func_get_args()
        );
    }

    function skip()
    {
        return call_user_func_array(
            ['\PSpec\Core\Builder','skip'],
            func_get_args()
        );
    }
}
