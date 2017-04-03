<?php


namespace Kamille\Mvc\LayoutProxy;

use Kamille\Mvc\Position\PositionInterface;


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
class LawsLayoutProxy extends LayoutProxy
{

    private $positions;


    public function __construct()
    {
        parent::__construct();
        $this->positions = [];
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
        }
        else{
            foreach($widgets as $widget){
                echo $widget->render();
            }
        }

    }
}


