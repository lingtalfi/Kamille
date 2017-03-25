<?php


namespace Kamille\Services;

use Kamille\Architecture\ApplicationParameters\ApplicationParameters;

/**
 * This service only works in an application that has an app_dir parameter, like a WebApplication for instance.
 */
class XInstalledModules
{

    private static $installed;

    public static function isInstalled($moduleName)
    {
        $list = self::getInstalledModules();
        return in_array($moduleName, $list);
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private static function getInstalledModules()
    {
        if (null === self::$installed) {
            $ret = [];
            $f = ApplicationParameters::get('app_dir');
            if (null !== $f && is_dir($f)) {
                $moduleFile = $f . "/modules.txt"; // following convention started in the kamille-naive-importer
                if (file_exists($moduleFile)) {
                    $ret = file($moduleFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                    $ret = array_filter($ret);
                }
            }
            self::$installed = $ret;
        }
        return self::$installed;
    }
}