<?php


namespace Architecture\Request;


interface RequestInterface
{
    //--------------------------------------------
    // PARAMS
    //--------------------------------------------
    public function set($key, $value);

    public function get($key, $defaultValue = null);
}