<?php


namespace Mvc\Layout;


use Mvc\Widget\WidgetInterface;

interface LayoutInterface
{

    public function setTemplate($templateName);

    public function bindWidget($name, WidgetInterface $widget);

    public function render(array $variables);

}