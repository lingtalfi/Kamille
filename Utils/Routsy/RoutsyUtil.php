<?php


namespace Kamille\Utils\Routsy;


use Kamille\Architecture\ApplicationParameters\ApplicationParameters;

class RoutsyUtil
{

    public static function getConfPath()
    {
        return ApplicationParameters::get("app_dir") . "/config/routsy/routes.php";
    }

    public static function getRoutes()
    {
        $routes = [];
        $f = self::getConfPath();
        if (file_exists($f)) {
            include $f;
        }
        return $routes;
    }
}