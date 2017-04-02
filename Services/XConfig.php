<?php


namespace Kamille\Services;


use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Services\Exception\XConfigException;
use Kamille\Utils\ModuleInstallationRegister\ModuleInstallationRegister;

class XConfig
{

    /**
     * @var array of module names => configuration parameters
     *          configuration parameters: key => value
     *
     */
    private static $confs = [];


    /**
     * @param $key :
     *      key: <module> <.> <moduleKey>
     *
     * For instance: Core.paramOne
     *
     */
    public static function get($key, $default = null, $throwEx = false)
    {
        $p = explode('.', $key, 2);
        $error = null;
        if (2 === count($p)) {
            $module = $p[0];
            $parameter = $p[1];

            if (ModuleInstallationRegister::isInstalled($module)) {


                if (false === array_key_exists($module, self::$confs)) {
                    $appDir = ApplicationParameters::get('app_dir');
                    $modConfFile = $appDir . "/config/modules/$module.conf.php";
                    $conf = [];
                    if (file_exists($modConfFile)) {
                        include $modConfFile;

                    }
                    self::$confs[$module] = $conf;
                }

                if (array_key_exists($parameter, self::$confs[$module])) {
                    return self::$confs[$module][$parameter];
                } else {
                    $error = "Parameter not found: $key";
                }
            } else {
                $error = "Module $module is not installed";
            }
        } else {
            $error = "Invalid parameter syntax: $key";
        }
        if (true === $throwEx) {
            throw new XConfigException($error);
        }
        return $default;
    }
}