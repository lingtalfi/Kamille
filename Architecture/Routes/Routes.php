<?php


namespace Kamille\Architecture\Routes;


use Kamille\Architecture\Route\RouteInterface;
use Kamille\Architecture\Router\RouterInterface;

class Routes implements RoutesInterface
{


    /**
     * @var RouteInterface[]
     */
    private $routes;


    public function __construct()
    {
        $this->routes = [];
    }

    public static function create()
    {
        return new static();
    }


    /**
     * @return array of id => RouteInterface
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param $id
     * @return RouterInterface|false
     */
    public function getRoute($id)
    {
        if (array_key_exists($id, $this->routes)) {
            return $this->routes[$id];
        }
        return false;
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    public function addRoute($id, RouteInterface $route)
    {
        $this->routes[$id] = $route;
        return $this;
    }
}