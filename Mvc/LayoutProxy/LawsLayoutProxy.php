<?php


namespace Kamille\Mvc\LayoutProxy;


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


    public function position($positionName)
    {
        $widgets = $this->layout->getWidgets();
        foreach ($widgets as $id => $widget) {




            $class = get_class($widget);
        }
    }
}


