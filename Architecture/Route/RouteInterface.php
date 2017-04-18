<?php


namespace Kamille\Architecture\Route;


use Kamille\Architecture\Router\RouterInterface;


/**
 * Note to myself:
 * intent is to create the following Routes:
 *
 * - StaticRoute
 * - DynamicRoute
 * ----- RegexDynamicRoute (like in symfony)
 * ----- ComponentizedDynamicRoute (using slash separated components of the uri)
 *
 *
 * See the "routes" document in the repo's documentation for more details.
 *
 */
interface RouteInterface extends RouterInterface
{

    /**
     * @return string, the uri of the route
     */
    public function getUri(array $params = []);

}