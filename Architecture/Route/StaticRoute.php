<?php


namespace Kamille\Architecture\Route;


use Kamille\Architecture\Request\Web\HttpRequestInterface;

class StaticRoute extends Route
{

    private $uri;

    /**
     * @var string|mixed, the controller to return
     */
    private $controller;


    /**
     * @return string, the uri of the route
     */
    public function getUri(array $params = [])
    {
        return $this->uri;
    }

    public function match(HttpRequestInterface $request)
    {
        if ($this->uri === $request->uri(false)) {
            return $this->controller;
        }
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    public function setUri($uri)
    {
        $this->uri = $uri;
        return $this;
    }

    public function setController($controller)
    {
        $this->controller = $controller;
        return $this;
    }

}