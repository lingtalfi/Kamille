<?php


namespace Architecture\Application;


use Architecture\Request\HttpRequestInterface;
use Architecture\RequestListener\HttpRequestListenerInterface;


class WebApplication implements WebApplicationInterface
{
    private static $inst;

    /**
     * @var HttpRequestListenerInterface[]
     */
    private $listeners;
    private $params;


    private function __construct()
    {
        $this->listeners = [];
        $this->params = [];
    }


    /**
     * Note: singleton so that we can access parameters from anywhere
     */
    public static function inst()
    {
        if (null === self::$inst) {
            self::$inst = new static();
        }
        return self::$inst;
    }


    public function addListener(HttpRequestListenerInterface $listener)
    {
        $this->listeners[] = $listener;
        return $this;
    }

    //--------------------------------------------
    // PARAMS
    //--------------------------------------------
    public function set($key, $value)
    {
        $this->params[$key] = $value;
        return $this;
    }

    public function get($key, $defaultValue = null)
    {
        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }
        return $defaultValue;
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    public function handleRequest(HttpRequestInterface $request)
    {
        foreach ($this->listeners as $listener) {
            $listener->listen($request); // use request.params to "stop" the treatment of the request if necessary
        }
    }
}