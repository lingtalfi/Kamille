<?php


namespace Kamille\Utils\Routsy\RouteCollection;


class PrefixedRouteCollection extends RouteCollection
{

    protected $urlPrefix;


    public function getRoutes()
    {
        $prefix = (string)$this->urlPrefix;

        $routes = array_map(function ($v) use ($prefix) {
            $v[0] = $prefix . $v[0];
            return $v;
        }, $this->routes);
        return $routes;
    }


    public function setUrlPrefix($prefix)
    {
        $this->urlPrefix = $prefix;
        return $this;
    }


}