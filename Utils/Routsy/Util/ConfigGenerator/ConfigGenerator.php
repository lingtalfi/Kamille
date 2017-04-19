<?php


namespace Kamille\Utils\Routsy\Util\ConfigGenerator;


use ArrayExport\ArrayExport;
use Kamille\Utils\ModuleInstallationRegister\ModuleInstallationRegister;

class ConfigGenerator
{

    private $confFile;
    private $modulesTargetDir;


    public static function create()
    {
        return new static();
    }

    public function generate()
    {
        $confFile = $this->confFile;
        $routes = [];
        if (file_exists($confFile)) {
            include $confFile;
        }
        $_routes = $routes;


        $installedModules = ModuleInstallationRegister::getInstalled();
        a($installedModules);
        $changed = false;
        foreach ($installedModules as $mod) {
            $modConfFile = $this->modulesTargetDir . "/$mod/routsy/conf.php";
            if (file_exists($modConfFile)) {
                $routes = [];
                include $modConfFile;

                foreach ($routes as $id => $route) {
                    // we only override routes that don't exist (don't want to accidentally override the user's work)
                    if (!array_key_exists($id, $_routes)) {
                        $_routes[$id] = $route;
                        $changed = true;
                    }
                }
            }
        }

        if (true === $changed) {
            a($_routes);
            $newContent = ArrayExport::export($_routes, 2);
            a($newContent);
        }

    }

    public function setConfFile($confFile)
    {
        $this->confFile = $confFile;
        return $this;
    }

    public function setModulesTargetDir($modulesTargetDir)
    {
        $this->modulesTargetDir = $modulesTargetDir;
        return $this;
    }


}