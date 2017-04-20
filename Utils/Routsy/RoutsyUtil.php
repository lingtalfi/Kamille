<?php


namespace Kamille\Utils\Routsy;


use Kamille\Architecture\ApplicationParameters\ApplicationParameters;

class RoutsyUtil
{

    private static $routes;


    public static function getConfPath()
    {
        return ApplicationParameters::get("app_dir") . "/config/routsy/routes.php";
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
}