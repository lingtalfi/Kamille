<?php


namespace Kamille\Mvc\LayoutProxy;

use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Mvc\Position\PositionInterface;
use Kamille\Mvc\Renderer\RendererInterface;
use Kamille\Services\XLog;


/**
 *
 * Using laws conventions:
 *
 * widgetId (when widget is rendered via position): <positionName> <.> <className> (<-> <index>)
 * widgetId (when widget is called alone): <className> (<-> <index>)
 *
 *
 * See laws documentation for more info.
 */
class LawsLayoutProxy extends LayoutProxy implements LawsLayoutProxyInterface, VariablesAwareLayoutProxyInterface
{

    private $positions;
    private $includesDir;

    /**
     * @var RendererInterface
     */
    private $renderer;
    private $variables;


    public function __construct()
    {
        parent::__construct();
        $this->positions = [];
        $this->includesDir = ApplicationParameters::get("app_dir") . "/theme/" . ApplicationParameters::get("theme") . "/includes";
    }

    public function setRenderer(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
        return $this;
    }

    public function setVariables(array $variables)
    {
        $this->variables = $variables;
        return $this;
    }


    public function bindPosition($position, PositionInterface $p)
    {
        if (is_array($position)) {
            foreach ($position as $pos) {
                $this->positions[$pos] = $p;
            }
        } else {
            $this->positions[$position] = $p;
        }
        return $this;
    }

    public function position($positionName)
    {

        $position = null;
        if (array_key_exists($positionName, $this->positions)) {
            $position = $this->positions[$positionName];
        } elseif (array_key_exists('*', $this->positions)) {
            $position = $this->positions['*'];
        }


        $allWidgets = $this->layout->getWidgets();
        $widgets = [];

        foreach ($allWidgets as $widgetId => $widget) {
            if (0 === strpos($widgetId, $positionName . ".")) {
                $widgets[$widgetId] = $widget;
            }
        }


        if ($position instanceof PositionInterface) {
            echo $position->render(["widgets" => $widgets]);
        } else {
            foreach ($widgets as $widget) {
                echo $widget->render();
            }
        }



        $config = 0;
        $i = 0;
        foreach($widgets as $widgetId => $widget){
            $s = $widget->render();


//            foreach($decorators as $decorator){
//                $decorator->decorateWidget($s, $widgetId, $i, $widget, $config);
//            }

//            // let the user decorate it
//            if($position instanceof PositionInterface){
//                $position->decorate($s, $widgetId, $i, $widget, $config);
//            }
//
//            // now assembly the widget in a grid layout if any
//            $this->decorateWithGrid($s, $widgetId, $i, $widget, $config);


            echo $s;
        }
    }

    public function includes($includePath)
    {
        $f = $this->includesDir . "/$includePath";
        if (file_exists($f)) {
            echo $this->renderer->render(file_get_contents($f), $this->variables);
        } else {
            $msg = "Include not found: $includePath ($f)";
            if (true === ApplicationParameters::get("debug")) {
                echo "debug: " . $msg;
            }
            XLog::error($msg);
        }
    }
}


