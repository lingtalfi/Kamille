<?php


namespace Kamille\Architecture\Routes;


use Kamille\Architecture\Route\RouteInterface;

interface RoutesInterface
{


    /**
     * @return array of id => RouteInterface
     */
    public function getRoutes();

    /**
     * @param $id
     * @return RouteInterface|false
     */
    public function getRoute($id);

    public function addRoute($id, RouteInterface $route);
}