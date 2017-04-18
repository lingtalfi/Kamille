<?php


namespace Kamille\Architecture\Route;


use Kamille\Architecture\Request\Web\HttpRequestInterface;

class StaticRoute extends Route
{

    private $uri;


    /**
     * @return string, the uri of the route
     */
    public function getUri(array $params = [])
    {
        return $this->uri;
    }

    public function match(HttpRequestInterface $request)
    {
        return ($this->uri === $request->uri(false));
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    public function setUri($uri)
    {
        $this->uri = $uri;
        return $this;
    }


}