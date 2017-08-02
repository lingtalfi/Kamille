<?php


namespace Kamille\Utils\Claws\Renderer;


use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Mvc\Renderer\PhpLayoutRenderer;
use Kamille\Services\XLog;
use Kamille\Utils\Claws\Claws;
use Loader\FileLoader;
use Loader\LoaderInterface;

class ClawsRenderer
{
    /**
     * @var Claws
     */
    private $claws;
    private $widgetDefaultLoader;
    private $commonRenderer;


    public function render()
    {
        if (null !== $this->claws) {

            $appDir = ApplicationParameters::get("app_dir");
            $theme = ApplicationParameters::get("theme");
            $wloader = $this->getWidgetDefaultLoader();
            $commonRenderer = PhpLayoutRenderer::create();



        } else {
            XLog::error("[Kamille] - ClawsRenderer: claws instance not set");
        }
    }


    public function setWidgetDefaultLoader(LoaderInterface $widgetDefaultLoader)
    {
        $this->widgetDefaultLoader = $widgetDefaultLoader;
        return $this;
    }

    public function setCommonRenderer(PhpLayoutRenderer $renderer){

    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private function getWidgetDefaultLoader()
    {
        if (null === $this->widgetDefaultLoader) {
            $appDir = ApplicationParameters::get("app_dir");
            $theme = ApplicationParameters::get("theme");
            $this->widgetDefaultLoader = FileLoader::create()->addDir($appDir . "/theme/$theme/widgets");
        }
        return $this->widgetDefaultLoader;
    }


}