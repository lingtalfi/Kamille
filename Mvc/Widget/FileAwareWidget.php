<?php


namespace Kamille\Mvc\Widget;

use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Mvc\Loader\LoaderInterface;
use Kamille\Mvc\Loader\PublicFileLoaderInterface;
use Kamille\Mvc\Renderer\Exception\RendererException;
use Kamille\Mvc\Renderer\RendererInterface;
use Kamille\Mvc\Widget\Exception\WidgetException;
use Kamille\Services\XLog;


class FileAwareWidget extends Widget
{


    protected function prepareVariables(array &$variables)
    {
        if ($this->loader instanceof PublicFileLoaderInterface) {
            $variables['__FILE__'] = $this->loader->getFile();
        }
    }

}