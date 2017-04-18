<?php


namespace Kamille\Architecture\Route;




abstract class Route implements RouteInterface
{

    public static function create()
    {
        return new static();
    }
}