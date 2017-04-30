<?php


namespace Kamille\Utils\Laws;


use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Ling\Z;
use Kamille\Mvc\BodyEndSnippetsCollector\BodyEndSnippetsCollectorInterface;
use Kamille\Mvc\HtmlPageHelper\HtmlPageHelper;
use Kamille\Mvc\Layout\HtmlLayout;
use Kamille\Mvc\LayoutProxy\ConfigAwareLayoutProxyInterface;
use Kamille\Mvc\LayoutProxy\LawsLayoutProxy;
use Kamille\Mvc\LayoutProxy\LawsLayoutProxyInterface;
use Kamille\Mvc\LayoutProxy\LayoutProxyInterface;
use Kamille\Mvc\LayoutProxy\RendererAwareLayoutProxyInterface;
use Loader\FileLoader;
use Loader\PublicFileLoaderInterface;
use Kamille\Mvc\Renderer\PhpLayoutRenderer;
use Kamille\Mvc\Widget\Widget;
use Kamille\Services\XLog;
use Kamille\Utils\Laws\Exception\LawsUtilException;


class LawsUtil implements LawsUtilInterface
{

    private $_file; // passed as a debug info
    private $_viewId; // passed as a debug info
    /**
     * @var LayoutProxyInterface
     */
    private $layoutProxy;


    public static function create()
    {
        return new static();
    }

    /**
     * $config: callable|array
     *          is used to alter the configuration found in the laws configuration file (pointed by the viewId)
     *
     *          If it's an array, it will be merged with the laws config array.
     *          If it's a callable, the laws config array will be passed by reference as the argument of that callable.
     *
     * $options: array,
     *          to alter the behaviour of the method on a per call basis
     *
     */
    public function renderLawsViewById($viewId, $config = null, array $options = [])
    {

        $appDir = ApplicationParameters::get("app_dir");
        $file = $appDir . "/config/laws/$viewId.conf.php";
        if (file_exists($file)) {
            $conf = [];
            include $file;

            if (is_array($config)) {
                $conf = array_replace_recursive($conf, $config);
            } elseif (is_callable($config)) {
                call_user_func_array($config, [&$conf]);
            }
            $this->_file = $file;
            $this->_viewId = $viewId;
            return $this->renderLawsView($conf, $options);
        }
        throw new LawsUtilException("laws config file not found: $file");
    }

    public function setLawsLayoutProxy(LawsLayoutProxyInterface $layoutProxy)
    {
        $this->layoutProxy = $layoutProxy;
        return $this;
    }


    public function renderLawsView(array $config, array $options = [])
    {
        $file = $this->_file;
        $viewId = $this->_viewId;

        $options = array_merge([
            'autoloadCss' => true,
            'widgetClass' => 'Kamille\Mvc\Widget\Widget',
            'bodyEndSnippetsCollector' => null, // a BodyEndSnippetsCollectorInterface instance
        ], $options);
        $autoloadCss = $options['autoloadCss'];
        $widgetClass = $options['widgetClass'];


        $layoutTemplate = $config['layout']['tpl'];
//        $positions = (array_key_exists('positions', $config)) ? $config['positions'] : [];
        $widgets = (array_key_exists('widgets', $config)) ? $config['widgets'] : [];
        $layoutConf = (array_key_exists('conf', $config['layout'])) ? $config['layout']['conf'] : [];

        $theme = ApplicationParameters::get("theme");
        $wloader = FileLoader::create()->addDir(Z::appDir() . "/theme/$theme/widgets");

//        $ploader = FileLoader::create()->addDir(Z::appDir() . "/theme/$theme/positions");


        $commonRenderer = PhpLayoutRenderer::create();
        $proxy = $this->getLayoutProxy();
        if ($proxy instanceof RendererAwareLayoutProxyInterface) {
            $proxy->setRenderer($commonRenderer);
        }
        if ($proxy instanceof ConfigAwareLayoutProxyInterface) {
            $proxy->setConfig($config);
        }


        if (true === ApplicationParameters::get('debug')) {

            $sWidgets = "";
            foreach ($widgets as $id => $widgetInfo) {
                $name = "unknown";
                if (true === array_key_exists('tpl', $widgetInfo)) {
                    $name = $widgetInfo["tpl"];
                }
                $sWidgets .= PHP_EOL . "----- id: $id; tplName: $name";
            }

            $viewIdFile = $file;
            if (null !== $viewIdFile) {
                $appDir = ApplicationParameters::get("app_dir");
                $viewIdFile = str_replace($appDir, '', $viewIdFile);
                $viewIdFile = ' (' . $viewIdFile . ')';
            }

//            $sPos = "";
//            $c = 0;
//            foreach ($positions as $name => $info) {
//                if (0 !== $c) {
//                    $sPos .= ", ";
//                }
//                $sPos .= "name: $name; tplName: " . $info['tpl'];
//                $c++;
//            }


            $trace = [];
            $theme = ApplicationParameters::get("theme", "no theme");
            $trace[] = "LawsUtil trace with theme: $theme, viewId: $viewId" . $viewIdFile . ":";
            $trace[] = "- layout: $layoutTemplate";
//            $trace[] = "- positions: " . $sPos;
            $trace[] = "- widgets: " . $sWidgets;


            XLog::trace(implode(PHP_EOL, $trace));
        }


        //--------------------------------------------
        // LAYOUT
        //--------------------------------------------
        $layoutLoader = FileLoader::create()->addDir(Z::appDir() . "/theme/$theme/layouts");
        $layout = HtmlLayout::create()
            ->setOnPrepareVariablesCallback(function (array &$variables) use ($layoutLoader) {
                if ($layoutLoader instanceof PublicFileLoaderInterface) {
                    $variables["__FILE__"] = $layoutLoader->getFile();
                    $variables["__DIR__"] = dirname($variables["__FILE__"]);
                }
            })
            ->setOnRenderedTemplateReadyCallback(function (&$content) use ($options) {

                $collector = $options['bodyEndSnippetsCollector'];
                if ($collector instanceof BodyEndSnippetsCollectorInterface) {
                    $snippets = $collector->getSnippets();
                    foreach ($snippets as $snippet) {
                        HtmlPageHelper::addBodyEndSnippet($snippet);
                    }
                }
            })
            ->setTemplate($layoutTemplate)
            ->setLoader($layoutLoader)
            ->setRenderer($commonRenderer);

        if (true === $autoloadCss) {
            $p = explode("/", $layoutTemplate);
            $css = "theme/$theme/layouts/" . $p[0] . "/" . $p[0] . '.' . $p[1] . ".css";
            if (file_exists(Z::appDir() . "/www/$css")) {
                HtmlPageHelper::css("/$css");
            }
        }

        //--------------------------------------------
        // POSITIONS
        //--------------------------------------------
//        foreach ($positions as $positionName => $pInfo) {
//            $tplName = $pInfo['tpl'];
//            $pVars = (array_key_exists('conf', $pInfo)) ? $pInfo['conf'] : [];
//
//            $proxy->bindPosition($positionName, Position::create()
//                ->setTemplate($tplName)
//                ->setLoader($ploader)
//                ->setVariables($pVars)
//                ->setRenderer($commonRenderer));
//
//
//            if (true === $autoloadCss) {
//                $p = explode("/", $tplName);
//                $css = "theme/$theme/positions/" . $p[0] . "/" . $p[0] . '.' . $p[1] . ".css";
//                if (file_exists(Z::appDir() . "/www/$css")) {
//                    HtmlPageHelper::css("/$css");
//                }
//            }
//        }
        $commonRenderer->setLayoutProxy($proxy);


        //--------------------------------------------
        // WIDGETS
        //--------------------------------------------
        foreach ($widgets as $id => $widgetInfo) {
            if (true === array_key_exists('tpl', $widgetInfo)) {

                $name = $widgetInfo['tpl'];
                $conf = (array_key_exists('conf', $widgetInfo)) ? $widgetInfo['conf'] : [];


                $widget = new $widgetClass;
                if ($widget instanceof Widget) {
                    $widget->setOnPrepareVariablesCallback(function (array &$variables) use ($wloader) {
                        if ($wloader instanceof PublicFileLoaderInterface) {
                            $variables["__FILE__"] = $wloader->getFile();
                            $variables["__DIR__"] = dirname($variables["__FILE__"]);
                        }
                    });

                    $layout
                        ->bindWidget($id, $widget
                            ->setTemplate($name)
                            ->setVariables($conf)
                            ->setLoader($wloader)
                            ->setRenderer($commonRenderer)
                        );


                    if (true === $autoloadCss) {
                        $p = explode("/", $name);
                        $css = "theme/$theme/widgets/" . $p[0] . "/" . $p[0] . '.' . $p[1] . ".css";
                        if (file_exists(Z::appDir() . "/www/$css")) {
                            HtmlPageHelper::css("/$css");
                        }
                    }
                } else {
                    /**
                     * We want the widget to be instance of Kamille\Mvc\Widget\Widget, so that we can
                     * provide the __FILE__ variable for all laws templates.
                     */
                    XLog::error('LawsUtil: widget with id must be an instance of the Kamille\Mvc\Widget\Widget class');
                }

            } else {
                $end = (null !== $viewId) ? " (viewId=$viewId)" : "";
                XLog::error("LawsUtil: name is not a valid key for widgetId $id" . $end);
            }
        }

        return $layout->render($layoutConf);

    }


    //--------------------------------------------
    //
    //--------------------------------------------
    private function getLayoutProxy()
    {
        if (null === $this->layoutProxy) {
            $this->layoutProxy = LawsLayoutProxy::create();
        }
        return $this->layoutProxy;
    }
}