<?php


namespace Kamille\Utils\RoutsyCopy\Util\ConfigGenerator;


use Bat\FileSystemTool;
use Bat\FileTool;
use Kamille\Utils\ModuleInstallationRegister\ModuleInstallationRegister;
use Kamille\Utils\RoutsyCopy\Util\ConfigGenerator\Exception\ConfigGeneratorException;
use LinearFile\LineSet\LineSetInterface;
use LinearFile\LineSetFinder\BiggestWrapLineSetFinder;

class ConfigGenerator
{

    private $confFile;
    private $modulesTargetDir;


    public static function create()
    {
        return new static();
    }

    public function refresh()
    {
        $confFile = $this->confFile;
        $installedModules = ModuleInstallationRegister::getInstalled();
        $uninstalledModules = ModuleInstallationRegister::getUninstalled();


        //--------------------------------------------
        // REGISTER INSTALLED MODULES
        //--------------------------------------------
        $this->registerModules($installedModules);


        //--------------------------------------------
        // UNREGISTER UNINSTALLED MODULES
        //--------------------------------------------
        $this->unregisterModules($uninstalledModules);
    }

    public function registerModule($module)
    {
        $confFile = $this->confFile;
        $routes = [];
        if (file_exists($confFile)) {
            include $confFile;
        } else {
            $this->createEmptyConfFile();
        }
        $_routes = $routes;

        $newRoutesDynamic = [];
        $newRoutesStatic = [];


        $modConfFile = $this->modulesTargetDir . "/$module/RoutsyCopy/conf.php";
        if (file_exists($modConfFile)) {
            $routes = [];
            include $modConfFile;

            $lines = file($modConfFile);
            $lineSets = $this->getLineSets($lines);


            foreach ($routes as $id => $route) {
                // we only override routes that don't exist (don't want to accidentally override the user's work)
                if (!array_key_exists($id, $_routes)) {
                    // doesn't exist?
                    // ok, is it dynamic or static (make two groups)?

                    /**
                     * @var LineSetInterface $lineSet
                     */
                    $lineSet = $lineSets[$id];
                    $routeContent = $lineSet->toString();
                    if (true === $this->isDynamic($route[0])) {
                        $newRoutesDynamic[$id] = $routeContent;
                    } else {
                        $newRoutesStatic[$id] = $routeContent;
                    }
                }
            }
        }

        // append in static Section
        if (count($newRoutesStatic) > 0) {
            foreach ($newRoutesStatic as $id => $routeContent) {
                $dynamicSectionLineNumber = $this->getSectionLineNumber("dynamic", $confFile);
                FileTool::insert($dynamicSectionLineNumber, PHP_EOL . $routeContent . PHP_EOL, $confFile);
            }
        }


        // append in dynamic Section
        if (count($newRoutesDynamic) > 0) {
            foreach ($newRoutesDynamic as $id => $routeContent) {
                $userAfterSectionLineNumber = $this->getSectionLineNumber("user - after", $confFile);
                FileTool::insert($userAfterSectionLineNumber, PHP_EOL . $routeContent . PHP_EOL, $confFile);
            }
        }

        FileTool::cleanVerticalSpaces($confFile, 2);
    }

    public function unregisterModule($module)
    {
        $confFile = $this->confFile;
        if (file_exists($confFile)) {
            $lines = file($confFile);
            $lineSets = $this->getLineSets($lines);


            $slices = [];
            foreach ($lineSets as $id => $lineSet) {
                if (0 === strpos($id, $module . "_")) {
                    /**
                     * @var LineSetInterface $lineSet
                     */
                    $slices[] = [$lineSet->getStartLine(), $lineSet->getEndLine()];
                }

            }
            FileTool::extract($confFile, $slices, true);
            FileTool::cleanVerticalSpaces($confFile, 2);
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

    //--------------------------------------------
    //
    //--------------------------------------------
    private function unregisterModules(array $uninstalledModules)
    {
        foreach ($uninstalledModules as $mod) {
            $this->unregisterModule($mod);
        }
    }


    private function registerModules(array $installedModules)
    {

        foreach ($installedModules as $mod) {
            $this->registerModule($mod);
        }
    }

    private function isDynamic($uri)
    {
        return (false !== strpos($uri, '{'));
    }

    private function getLineSets(array $lines)
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

    private function getSectionLineNumber($section, $file)
    {
        $lines = file($file);


        $patternLine = '!//--------------------------------------------!';
        $pattern2 = '!//\s*' . strtoupper($section) . '!';
        $n = 1;
        $match1 = false;
        $match2 = false;
        foreach ($lines as $line) {
            if (false === $match1 && preg_match($patternLine, $line)) {
                $match1 = true;
            } elseif (true === $match1 && false === $match2 && preg_match($pattern2, $line)) {
                $match2 = true;
            } elseif (true === $match1 && true === $match2 && preg_match($patternLine, $line)) {
                return $n - 2;
            }
            $n++;
        }
        throw new ConfigGeneratorException("section not found $section");
    }

    private function createEmptyConfFile()
    {
        $confFile = $this->confFile;
        $data = file_get_contents(__DIR__ . "/assets/RoutsyCopy.conf.tpl.php");
        FileSystemTool::mkfile($confFile, $data);
    }
}