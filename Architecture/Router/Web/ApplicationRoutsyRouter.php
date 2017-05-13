<?php


namespace Kamille\Architecture\Router\Web;


use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Architecture\Request\Web\HttpRequestInterface;
use Kamille\Architecture\Router\Helper\RouterHelper;
use Kamille\Architecture\Router\RouterInterface;
use Kamille\Services\XLog;
use Kamille\Utils\Routsy\RoutsyRouter;
use Kamille\Utils\Routsy\RoutsyUtil;


class ApplicationRoutsyRouter implements RouterInterface
{

    /**
     * @var RoutsyRouter
     */
    private $router;

    public static function create()
    {
        return new static();
    }

    public function match(HttpRequestInterface $request)
    {
        $router = $this->getRouter();
        if (false !== ($res = $router->match($request))) {
            list($routeId, $controller, $urlParams) = $res;
            if (true === ApplicationParameters::get("debug")) {
                $sSuffix = "";
                if(is_string($controller)){
                    $sSuffix .= " and controller is $controller";
                }
                XLog::debug("ApplicationRoutsyRouter: routeId $routeId matched" . $sSuffix);
            }
            $request->set("route", $routeId);
            return RouterHelper::routerControllerToCallable($controller, $urlParams);
        }
    }

    public function setRoutes(array $routes)
    {
        $this->routes = $routes;
        return $this;
    }

    //--------------------------------------------
    //
    //--------------------------------------------

    private function getRouter()
    {
        if (null === $this->router) {
            $this->router = RoutsyRouter::create()->setRoutes(RoutsyUtil::getRoutes());
        }
        return $this->router;
    }

}