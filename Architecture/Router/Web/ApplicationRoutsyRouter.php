<?php


namespace Kamille\Architecture\Router\Web;


use Kamille\Architecture\Request\Web\HttpRequestInterface;
use Kamille\Architecture\Router\RouterInterface;
use Kamille\Utils\Routsy\Exception\RoutsyException;
use Kamille\Utils\Routsy\RoutsyConfig;
use Kamille\Utils\Routsy\RoutsyRouter;
use Kamille\Utils\Routsy\Util\ConstraintsChecker\AppleConstraintsChecker;
use Kamille\Utils\Routsy\Util\DynamicUriMatcher\CherryDynamicUriMatcher;
use Kamille\Utils\Routsy\Util\RequirementsChecker\KiwiRequirementsChecker;


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

    }

    public function setRoutes(array $routes)
    {
        $this->routes = $routes;
        return $this;
    }

    //--------------------------------------------
    //
    //--------------------------------------------

    private function getRouter(){
        if(null === $this->router){
$f = RoutsyConfig::getConfPath();
            $routes = [];
            include
            $this->router = RoutsyRouter::create()->setRoutes($routes);
        }
        return $this->router;
    }

}