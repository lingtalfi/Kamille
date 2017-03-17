<?php


namespace Kamille\Architecture\ApplicationParameters\Web;


use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Architecture\Environment\Web\Environment;


class WebApplicationParameters extends ApplicationParameters
{


    public static function boot()
    {

        // the commented code below does not work with symlinks, so I used the DOCUMENT_ROOT property, which is
        // there in apache and nginx environments.
        //
//        $paramsFile = __DIR__ . "/../../../../../config/application-parameters-" . Environment::getEnvironment() . ".php";
        $paramsFile = $_SERVER['DOCUMENT_ROOT'] . "/../config/application-parameters-" . Environment::getEnvironment() . ".php";
        $params = [];
        if (file_exists($paramsFile)) {
            require $paramsFile;

        }
        self::$params = $params;
        return $params;
    }
}