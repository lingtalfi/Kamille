<?php


namespace Kamille\Utils\Routsy\RouteCollection;


use Kamille\Utils\Routsy\RoutsyUtil;

/**
 * This route collection assumes that the following structure exists:
 *
 *
 * The routsy dir by default is: **app/config/routsy**
 *
 * - $routsyDir/$prefix.php
 *
 *
 *
 */
class RoutsyRouteCollection extends PrefixedRouteCollection
{

    private $routsyDir;
    private $fileName;


    public function __construct()
    {
        parent::__construct();
        $this->routsyDir = RoutsyUtil::getRoutsyDir();
    }

    public function setRoutsyDir($routsyDir)
    {
        $this->routsyDir = $routsyDir;
        return $this;
    }

    public function getRoutes()
    {
        $f = $this->routsyDir . "/" . $this->fileName . ".php";
        if (file_exists($f)) {
            $routes = [];
            include $f;
            $this->routes = $routes;
        }
        return parent::getRoutes();
    }

    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
        return $this;
    }


}