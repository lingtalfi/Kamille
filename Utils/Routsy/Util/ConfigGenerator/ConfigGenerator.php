<?php


namespace Kamille\Utils\Routsy\Util\ConfigGenerator;


use ArrayExport\ArrayExport;
use Bat\FileSystemTool;
use Kamille\Utils\ModuleInstallationRegister\ModuleInstallationRegister;
use LinearFile\LineSetFinder\BiggestWrapLineSetFinder;

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
        $changed = false;
        $newRoutesDynamic = [];
        $newRoutesStatic = [];


        foreach ($installedModules as $mod) {
            $modConfFile = $this->modulesTargetDir . "/$mod/routsy/conf.php";
            if (file_exists($modConfFile)) {
                $routes = [];
                include $modConfFile;

                $lines = file($modConfFile);

                foreach ($routes as $id => $route) {
                    // we only override routes that don't exist (don't want to accidentally override the user's work)
                    if (!array_key_exists($id, $_routes)) {

                        // doesn't exist?
                        // ok, is it dynamic or static (make two groups)
                        $routeContent = $this->getRouteContent($id, $lines);

                        if (true === $this->isDynamic($route[0])) {
                            $newRoutesDynamic[$id] = $route;
                        } else {
                            $newRoutesStatic[$id] = $route;
                        }
                        $changed = true;
                    }
                }
            }
        }


        // append in static Section
        // append in dynamic Section


//        if (true === $changed) {
//            $routesArr = ArrayExport::export($_routes, 2);
//
//            $newContent = <<<EEE
//<?php
//use Kamille\Architecture\Request\Web\HttpRequestInterface;
//
//\$routes = $routesArr;
//EEE;
//
//
//            FileSystemTool::mkfile($confFile, $newContent);
//        }
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

    //--------------------------------------------
    //
    //--------------------------------------------
    private function isDynamic($uri)
    {
        return (false !== strpos($uri, '{'));
    }

    private function getRouteContent($routeId, array $lines)
    {
        $pat = '!^\$routes\[([^\]]+)\]\s*=!';
        $lineSets = BiggestWrapLineSetFinder::create()
            ->setPrepareNameCallback(function ($v) {
                return substr($v, 1, -1);
            })
            ->setNamePattern($pat)
            ->setStartPattern($pat)
            ->setPotentialEndPattern('!\];!')
            ->find($lines);
        return $lineSets;
    }

}