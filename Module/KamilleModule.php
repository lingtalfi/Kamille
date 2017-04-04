<?php


namespace Kamille\Module;


use Kamille\Architecture\ApplicationParameters\ApplicationParameters;

use Kamille\Module\Exception\KamilleModuleException;
use Kamille\Utils\ModuleUtils\ModuleInstallTool;
use Output\ProgramOutputAwareInterface;
use Output\ProgramOutputInterface;


/**
 * This class helps you implementing basic module install tasks, like:
 * - mapping the module files to the application
 *      (just create a files/app directory inside your module directory)
 *
 *
 * But there is a philosophy that comes with it (that's the price to pay).
 * So the philosophy is that a module install/uninstall is composed of steps.
 *
 * Each module uses a certain number of steps (depending on the module);
 * the idea is to be able to display the following to the user:
 *
 * - step 1/5: installing files
 * - step 2/5: installing database
 * - ...
 *
 * So the benefit of having steps is that we have some kind of map/synopsis,
 * and we know in advance HOW MANY steps are required, which is the useful information
 * this philosophy try to promote.
 *
 * Some steps are registered automatically by this class (for files/app for instance, and other
 * auto mechanisms); and you need to register your own steps with the registerSteps method.
 *
 *
 *
 */
abstract class KamilleModule implements ProgramOutputAwareInterface, ModuleInterface
{
    /**
     * @var ProgramOutputInterface $output
     */
    private $output;
    /**
     * @var array of id => label
     */
    private $steps;


    public function __construct()
    {
        $this->steps = [];
    }


    public function install()
    {
        $steps = [];
        $this->collectAutoSteps($steps, 'install');
        $this->registerSteps($steps, 'install');
        $this->steps = $steps;


        $this->installAuto();
        $this->installModule();
    }

    public function uninstall()
    {

        a(ApplicationParameters::get("debug"));
        $steps = [];
        $this->collectAutoSteps($steps, 'uninstall');
        $this->registerSteps($steps, 'uninstall');
        $this->steps = $steps;

        $this->uninstallAuto();
        $this->uninstallModule();
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    public function setProgramOutput(ProgramOutputInterface $output)
    {
        $this->output = $output;
        return $this;
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function installModule()
    {

    }

    protected function uninstallModule()
    {

    }


    /**
     * @param $type , string (install|uninstall)
     */
    protected function registerSteps(array &$steps, $type)
    {

    }


    protected function startStep($stepId)
    {
        if (array_key_exists($stepId, $this->steps)) {
            $label = $this->getStepLabel($stepId);
            $this->getOutput()->notice($label, false);
        } else {
            throw new KamilleModuleException("step $stepId doesn't exist");
        }
    }


    protected function stopStep($stepId, $text = "done")
    {
        if (array_key_exists($stepId, $this->steps)) {
            if ("done" === $text) {
                $this->getOutput()->success($text);
            } else {
                $this->getOutput()->info($text);
            }
        } else {
            throw new KamilleModuleException("step $stepId doesn't exist");
        }
    }


    protected function collectAutoSteps(array &$steps, $type)
    {
        if (true === $this->useConfig()) {
            if ('install' === $type) {
                $steps['config'] = "Copying module config file";
            } else {
                $steps['config'] = "Removing module config file";
            }
        }
        if (true === $this->useAutoFiles()) {
            if ('install' === $type) {
                $steps['files'] = "Installing files";
            } else {
                $steps['files'] = "Uninstalling files";
            }
        }
        if (true === $this->useXServices()) {
            if ('install' === $type) {
                $steps['xservices'] = "Installing services";
            } else {
                $steps['xservices'] = "Uninstalling services";
            }
        }
        if (true === $this->useHooks()) {
            if ('install' === $type) {
                $steps['hooks'] = "Installing hooks";
            } else {
                $steps['hooks'] = "Uninstalling hooks";
            }
        }
        if (true === $this->useControllers()) {
            if ('install' === $type) {
                $steps['controllers'] = "Installing controllers";
            } else {
                $steps['controllers'] = "Uninstalling controllers";
            }
        }
    }

    protected function installAuto()
    {
        if (true === $this->useConfig()) {
            $this->startStep('config');
            ModuleInstallTool::installConfig($this);
            $this->stopStep('config', "done");
        }

        if (true === $this->useAutoFiles()) {
            $this->startStep('files');
            ModuleInstallTool::installFiles($this);
            $this->stopStep('files', "done");
        }

        if (true === $this->useXServices()) {
            $this->startStep('xservices');
            $n = $this->getModuleName();
            $moduleName = 'Module\\' . $n . '\\' . $n . "Services";
            ModuleInstallTool::bindModuleServices($moduleName);
            $this->stopStep('xservices', "done");
        }

        if (true === $this->useHooks()) {
            $this->startStep('hooks');
            $n = $this->getModuleName();
            $moduleName = 'Module\\' . $n . '\\' . $n . "Hooks";
            ModuleInstallTool::bindModuleHooks($moduleName);
            $this->stopStep('hooks', "done");
        }


        if (true === $this->useControllers()) {
            $this->startStep('controllers');
            $moduleName = $this->getModuleName();
            ModuleInstallTool::installControllers($moduleName);
            $this->stopStep('controllers', "done");
        }
    }

    protected function uninstallAuto()
    {
        if (true === $this->useConfig()) {
            $this->startStep('config');
            $this->handleStep(function () {
                ModuleInstallTool::uninstallConfig($this);
            });
            $this->stopStep('config', "done");
        }


        if (true === $this->useAutoFiles()) {
            $this->startStep('files');
            $this->handleStep(function () {
                ModuleInstallTool::uninstallFiles($this);
            });
            $this->stopStep('files', "done");
        }

        if (true === $this->useXServices()) {
            $this->startStep('xservices');
            $this->handleStep(function () {
                $n = $this->getModuleName();
                $moduleName = 'Module\\' . $n . '\\' . $n . "Services";
                ModuleInstallTool::unbindModuleServices($moduleName);
            });
            $this->stopStep('xservices', "done");
        }

        if (true === $this->useHooks()) {
            $this->startStep('hooks');
            $this->handleStep(function () {
                $n = $this->getModuleName();
                $moduleName = 'Module\\' . $n . '\\' . $n . "Hooks";
                ModuleInstallTool::unbindModuleHooks($moduleName);
            });
            $this->stopStep('hooks', "done");
        }


        if (true === $this->useControllers()) {
            $this->startStep('controllers');
            /**
             * you don't want to remove userland code, do you?
             */
//            $moduleName = $this->getModuleName();
//            ModuleInstallTool::uninstallControllers($moduleName);
            $this->stopStep('controllers', "skipped, don't want to remove userland code");
        }
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private function useAutoFiles()
    {
        $d = $this->getModuleDir();
        $f = $d . "/files/app";
        return (file_exists($f));
    }

    private function useConfig()
    {
        $d = $this->getModuleDir();
        $f = $d . "/conf.php";
        return (file_exists($f));
    }

    private function useControllers()
    {
        $d = $this->getModuleDir();
        $f = $d . "/Controller";
        return (file_exists($f));
    }


    private function useXServices()
    {
        $d = $this->getModuleDir();
        $n = $this->getModuleName();
        $f = $d . "/$n" . "Services.php";
        return (file_exists($f));
    }

    private function useHooks()
    {
        $d = $this->getModuleDir();
        $n = $this->getModuleName();
        $f = $d . "/$n" . "Hooks.php";
        return (file_exists($f));
    }


    private function getModuleName()
    {
        $className = get_called_class();
        $p = explode('\\', $className);
        array_shift($p); // drop the Module prefix
        return $p[0];
    }

    private function getModuleDir()
    {
        $moduleName = $this->getModuleName();
        $appDir = ApplicationParameters::get("app_dir");
        return $appDir . "/class-modules/$moduleName";
    }

    /**
     * @return ProgramOutputInterface
     */
    private function getOutput()
    {
        return $this->output;
    }

    private function getStepLabel($stepId)
    {
        $n = 0;
        $label = null;
        foreach ($this->steps as $id => $label) {
            $n++;
            if ($id === $stepId) {
                break;
            }
        }
        $count = count($this->steps);
        $msg = "----> Step $n/$count: $label ... ";
        return $msg;
    }


    private function handleStep($fn)
    {
        try {
            call_user_func($fn);
        } catch (\Exception $e) {
            $debug = ApplicationParameters::get("debug");
            if (true === $debug) {
                echo $e;
            } else {
                throw $e;
            }
        }
    }
}