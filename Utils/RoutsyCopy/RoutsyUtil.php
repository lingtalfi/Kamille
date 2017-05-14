<?php


namespace Kamille\Utils\RoutsyCopy;


use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Utils\RoutsyCopy\LinkGenerator\ApplicationLinkGenerator;

class RoutsyUtil
{

    private static $routes;


    public static function getConfPath()
    {
        return ApplicationParameters::get("app_dir") . "/config/RoutsyCopy/routes.php";
    }

    public static function getRoutes($recreate = false)
    {
        if (null !== self::$routes && false === $recreate) {
            return self::$routes;
        }
        $routes = [];
        $f = self::getConfPath();
        if (file_exists($f)) {
            include $f;
        }
        self::$routes = $routes;
        return self::$routes;
    }


    public static function routeIdentifierToUri($routeIdentifier)
    {
        if (is_array($routeIdentifier)) {
            list($routeId, $params) = $routeIdentifier;
        } else {
            $routeId = $routeIdentifier;
            $params = [];
        }
        return ApplicationLinkGenerator::getUri($routeId, $params);
    }
}