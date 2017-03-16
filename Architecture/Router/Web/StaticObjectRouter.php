<?php


namespace Kamille\Architecture\Router\Web;


use Kamille\Architecture\Request\Web\HttpRequestInterface;
use Kamille\Architecture\Router\RouterInterface;


/**
 * This router will instantiate a Controller from a controllerString
 * returned from the uri2Controller array.
 *
 * The controllerString is the full path to a controller, which must be an object with a render method.
 *
 * The constructor must not have any parameters, nor does the render method.
 *
 *
 */
class StaticObjectRouter implements RouterInterface
{

    protected $uri2Controller;

    public function __construct()
    {
        $this->uri2Controller = [];
    }

    public static function create()
    {
        return new static();
    }

    public function setUri2Controller(array $uri2Controller)
    {
        $this->uri2Controller = $uri2Controller;
        return $this;
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    public function match(HttpRequestInterface $request)
    {
        $uri = $request->uri(false);
        $uri2Controller = $this->uri2Controller;
        if (array_key_exists($uri, $uri2Controller)) {
            $controller = $uri2Controller[$uri];
            $o = new $controller;
            return [
                [$o, 'render'],
                [],
            ];
        }
    }


}