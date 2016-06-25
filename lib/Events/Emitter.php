<?php namespace Matura\Events;

interface Emitter
{
    public function emit($name, $arguments = []);
    public function addListener($listener);
}
