<?php


namespace Kamille\Utils\Claws;


class Claws
{
    /**
     * @var ClawsLayout|string, the layout template
     */
    private $layout;

    /**
     * @var ClawsWidget[]
     */
    private $widgets;


    public function __construct()
    {
        $this->widgets = [];
    }

    /**
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @param ClawsLayout|string $layout ,
     *                      if it's a string, it's the template and
     *                      the default ClawsLayout instance will be used to hold it.
     * @return $this
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
        return $this;
    }

    /**
     * @return ClawsWidget[]
     */
    public function getWidgets()
    {
        return $this->widgets;
    }

    /**
     * @param $id , string, the widgetId:
     *
     *                          - widgetId: ( <positionName>. )? <widgetInternalName>
     *
     * See https://github.com/lingtalfi/laws for more info
     * @param $widget
     * @return $this
     */
    public function setWidget($id, ClawsWidget $widget)
    {
        $this->widgets[$id] = $widget;
        return $this;
    }

    public function removeWidget($id)
    {
        unset($this->widgets[$id]);
        return $this;
    }


}