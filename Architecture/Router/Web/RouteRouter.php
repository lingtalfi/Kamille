<?php


namespace Kamille\Architecture\Router\Web;


use Kamille\Architecture\Request\Web\HttpRequestInterface;
use Kamille\Architecture\Route\RouteInterface;
use Kamille\Architecture\Router\Helper\RouterHelper;
use Kamille\Architecture\Router\RouterInterface;
use Kamille\Architecture\Routes\RoutesInterface;


/**
 * This router uses the Routes system described in the repo's doc.
 */
class RouteRouter implements RouterInterface
{

    /**
     * @var RoutesInterface
     */
    protected $routes;


    public static function create()
    {
        return new static();
    }

    public function setRoutes(RoutesInterface $routes)
    {
        $this->routes = $routes;
        return $this;
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    public function match(HttpRequestInterface $request)
    {
        $routes = $this->routes->getRoutes();
        foreach ($routes as $route) {

            /**
             * @var RouteInterface $route
             */
            if (null !== ($res = $route->match($request))) {
                return RouterHelper::routerControllerToCallable($res);
            }
        }
    }
}