<?php


namespace Kamille\Utils\RoutsyCopy;


use Kamille\Architecture\Request\Web\HttpRequestInterface;
use Kamille\Utils\RoutsyCopy\Exception\RoutsyException;
use Kamille\Utils\RoutsyCopy\Util\ConstraintsChecker\AppleConstraintsChecker;
use Kamille\Utils\RoutsyCopy\Util\DynamicUriMatcher\CherryDynamicUriMatcher;
use Kamille\Utils\RoutsyCopy\Util\RequirementsChecker\KiwiRequirementsChecker;


class RoutsyRouter
{

    private $routes;

    public function __construct()
    {
        $this->routes = [];
    }

    public static function create()
    {
        return new static();
    }

    public function match(HttpRequestInterface $request)
    {
        foreach ($this->routes as $routeId => $route) {
            $urlParams = [];
            if (false !== ($controller = $this->matchRoute($request, $route, $urlParams))) {
                return [
                    $routeId,
                    $controller,
                    $urlParams,
                ];
            }
        }
        return false;
    }

    public function setRoutes(array $routes)
    {
        $this->routes = $routes;
        return $this;
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private function matchRoute(HttpRequestInterface $request, array $route, array &$urlParams)
    {
        list($url, $constraints, $requirements, $controller) = $route;

        //--------------------------------------------
        // CHECK REQUIREMENTS
        //--------------------------------------------
        /**
         * We do requirements first because they are generally the fastest to process
         */
        if (null !== $requirements) {
            if (false === $this->checkRequirements($request, $requirements)) {
                return false;
            }
        }

        //--------------------------------------------
        // URL MATCHING
        //--------------------------------------------
        $urlMatched = false;
        $_urlParams = null; // only for dynamic params
        $uri = $request->uri(false);
        if (is_string($url)) {
            // is it dynamic or static?
            if (false === strpos($url, '{')) {
                // static
                $urlMatched = ($uri === $url);
            } else {
                // dynamic
                if (false !== ($_urlParams = $this->matchDynamic($url, $uri))) {
                    $urlMatched = true;

                    /**
                     * Checking provided url params
                     */
                    if (is_array($constraints)) {
                        if (false === $this->checkConstraints($_urlParams, $constraints)) {
                            return false;
                        }
                    }

                }
            }
        } elseif (null === $url) {
            $urlMatched = true;
        }


        //--------------------------------------------
        // CONTROLLER/URL PARAMS
        //--------------------------------------------
        if (true === $urlMatched) {
            if (is_string($controller)) {
                if (null !== $_urlParams) {
                    $urlParams = $_urlParams;
                }
                return $controller;
            } else {
                // assuming array
                if (null !== $_urlParams) {
                    $controller = array_merge($_urlParams, $controller);
                }
                if (array_key_exists("controller", $controller)) {
                    $_contr = $controller['controller'];
                    unset($controller['controller']);
                    $urlParams = $controller;
                    return $_contr;
                } else {
                    throw new RoutsyException("Controller not found");
                }
            }
        }
        return false;
    }

    private function checkRequirements(HttpRequestInterface $request, $requirements)
    {
        return KiwiRequirementsChecker::checkRequirements($request, $requirements);
    }


    private function matchDynamic($pattern, $uri)
    {
        return CherryDynamicUriMatcher::matchDynamic($pattern, $uri);
    }

    private function checkConstraints(array $urlParams, array $constraints)
    {
        return AppleConstraintsChecker::checkConstraints($urlParams, $constraints);
    }
}