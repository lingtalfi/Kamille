<?php


namespace Architecture\RequestListener\Web;


use Architecture\Request\Web\HttpRequestInterface;
use Architecture\Router\RouterInterface;


/**
 * This is a router for a web application.
 *
 * It sets the controller parameter in the request (if a route matches),
 * or do nothing special otherwise.
 *
 * The controller is a callable.
 *
 */
class RouterRequestListener implements HttpRequestListenerInterface
{

    /**
     * @var RouterInterface[]
     */
    private $routers;

    public function __construct()
    {
        $this->routers = [];
    }

    public static function create()
    {
        return new static();
    }

    public function listen(HttpRequestInterface $request)
    {
        $controller = null;
        $controllerParams = [];
        foreach ($this->routers as $router) {
            if (null !== ($res = $router->match($request))) {
                if (is_array($res)) {
                    $controller = $res[0];
                    $controllerParams = $res[1];
                    break;

                } elseif (is_string($res)) {
                    $controller = $res;
                    break;
                }
            }
        }
        if (null !== $controller) {
            $request->set("controller", $controller);
            $request->set("controllerParams", $controllerParams);
        }
    }

    public function addRouter(RouterInterface $router)
    {
        $this->routers[] = $router;
        return $this;
    }

}